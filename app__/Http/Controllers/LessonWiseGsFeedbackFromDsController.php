<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use Response;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use File;
use DB;
use Common;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Gs;
use App\CmBasicProfile;
use App\Course;
use App\TrainingYear;
use App\GsEvalByDs;
use App\GsToLesson;
use App\DsMarkingGroup;
use App\Imports\ExcelImport;
use App\Lesson;
use App\UserBasicProfile;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LessonWiseGsFeedbackFromDsController extends Controller
{

    private $controller = 'LessonWiseGsFeedbackFromDs';

    public function index(Request $request)
    {

        $activeTrainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')] + TrainingYear::whereIn('status', ['1', '2'])
            ->orderBy('start_date', 'desc')
            ->pluck('name', 'id')->toArray();

        $courseList = Course::where('training_year_id', $request->training_year_id)
            ->whereIn('status', ['1', '2']);

        $courseList = $courseList->orderBy('training_year_id', 'desc')
            ->orderBy('id', 'desc')->pluck('name', 'id')
            ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;

        $gsList = GsToLesson::leftJoin('gs', 'gs.id', 'gs_to_lesson.gs_id')
            ->where('gs_to_lesson.course_id', $request->course_id)
            ->where('gs.status', '1')
            ->orderBy('gs.name', 'asc')
            ->pluck('gs.name as gs_name', 'gs.id')
            ->toArray();

        $gsList = ['0' => __('label.SELECT_GS_OPT')] + $gsList;


        $lessonList = GsToLesson::leftJoin('lesson', 'lesson.id', 'gs_to_lesson.lesson_id')
            ->orderBy('lesson.order', 'asc')
            ->where('gs_to_lesson.gs_id', $request->gs_id)
            ->where('lesson.status', '1')
            ->pluck('lesson.title as lesson_name', 'lesson.id')
            ->toArray();

        $lessonList = ['0' => __('label.SELECT_LESSON_OPT')] + $lessonList;

        $sortByList = [
            'position' => __('label.POSITION'),
            'svc' => __('label.WING'),
            'alphabatically' => __('label.ALPHABATICALLY'),
            'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'),
            'seniority' => __('label.SENIORITY'),
        ];
        $gradingArr = $dsDataList = [];

        if ($request->generate == 'true') {
            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $gsName = $request->gs_id != '0' && !empty($courseList[$request->gs_id]) ? '_' . $courseList[$request->gs_id] : '';
            $lessonName = $request->lesson_id != '0' && !empty($termList[$request->lesson_id]) ? '_' . $termList[$request->lesson_id] : '';

            $fileName = 'Lesson_Wise_GS_Feedback_from_DS_' . $tyName . $courseName . $lessonName;
            $fileName = Common::getFileFormatedName($fileName);

            $dsDataInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('users', 'users.id', 'ds_marking_group.ds_id')
                ->join('rank', 'rank.id', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', 'users.wing_id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', 'ds_marking_group.ds_appt_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('users.status', '1')
                ->select(
                    'appointment.code as appt',
                    'users.id as ds_id',
                    'users.photo',
                    'rank.code as rank',
                    'users.full_name as ds_name',
                    'users.official_name',
                    'wing.code as wing',
                    'users.personal_no'
                );

            //
            if (!empty($request->sort)) {
                if ($request->sort == 'svc') {
                    $dsDataInfo = $dsDataInfo->orderBy('appointment.order', 'asc')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('users.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $dsDataInfo = $dsDataInfo->orderBy('users.official_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $dsDataInfo = $dsDataInfo->orderBy('wing.order', 'asc')
                        ->orderBy('users.official_name', 'asc');
                } elseif ($request->sort == 'seniority') {
                    $dsDataInfo = $dsDataInfo->orderBy('rank.svc_order', 'asc')
                        ->orderBy('user_basic_profile.commisioning_date', 'asc')
                        ->orderBy('users.personal_no', 'asc');
                }
            } else {
                $dsDataInfo = $dsDataInfo->orderBy('appointment.order', 'asc')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('users.personal_no', 'asc');
            }

            $dsDataInfo = $dsDataInfo->get();

            // return $dsDataInfo;


            // echo '<pre>'; print_r($dsDataList); exit;

            //grading info

            $gradingArr = GsEvalByDs::where('course_id', $request->course_id)
                ->where('gs_id', $request->gs_id)
                ->where('lesson_id', $request->lesson_id)
                ->pluck('grading', 'updated_by')->toArray();

            // echo '<pre>'; print_r( $gradingArr); exit;

            if (!$dsDataInfo->isEmpty()) {
                foreach ($dsDataInfo as $ds) {
                    $dsDataList[$ds->ds_id] = $ds->toArray();
                    $dsDataList[$ds->ds_id]['grading'] = $gradingArr[$ds->ds_id] ?? 0;
                }
            }


            // get postion after term total
            if (empty($request->sort) || $request->sort == 'position') {
                if (!empty($dsDataList)) {
                    usort($dsDataList, function ($item1, $item2) {
                        if (!isset($item1['grading'])) {
                            $item1['grading'] = '';
                        }

                        if (!isset($item2['grading'])) {
                            $item2['grading'] = '';
                        }
                        return $item2['grading'] <=> $item1['grading'];
                    });
                }
            }
        }

        if ($request->view == 'print') {
            return view('lessonWiseGsFeedback.fromDs.print.index')->with(compact(
                'activeTrainingYearList',
                'courseList',
                'gsList',
                'lessonList',
                'gradingArr',
                'dsDataList',
                'sortByList'
            ));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('lessonWiseGsFeedback.fromDs.print.index', compact(
                'activeTrainingYearList',
                'courseList',
                'gsList',
                'lessonList',
                'gradingArr',
                'dsDataList',
                'sortByList'
            ))
                ->setPaper('a4', 'landscape')
                ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('lessonWiseGsFeedback.fromDs.print.index', compact(
                'activeTrainingYearList',
                'courseList',
                'gsList',
                'lessonList',
                'gradingArr',
                'dsDataList',
                'sortByList'
            )), $fileName . '.xlsx');
        }

        return view('lessonWiseGsFeedback.fromDs.index', compact(
            'activeTrainingYearList',
            'courseList',
            'gsList',
            'lessonList',
            'gradingArr',
            'dsDataList',
            'sortByList'
        ));
    }

    public function getCourse(Request $request)
    {

        $courseList = Course::where('training_year_id', $request->training_year_id)
            ->whereIn('status', ['1', '2']);

        $courseList = $courseList->orderBy('training_year_id', 'desc')
            ->orderBy('id', 'desc')->pluck('name', 'id')
            ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;
        $html = view('lessonWiseGsFeedback.fromDs.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getGs(Request $request)
    {

        $gsList = GsToLesson::leftJoin('gs', 'gs.id', 'gs_to_lesson.gs_id')
            ->where('gs_to_lesson.course_id', $request->course_id)
            ->where('gs.status', '1')
            ->orderBy('gs.name', 'asc')
            ->pluck('gs.name as gs_name', 'gs.id')
            ->toArray();

        $gsList = ['0' => __('label.SELECT_GS_OPT')] + $gsList;

        $html = view('lessonWiseGsFeedback.fromDs.getGs', compact('gsList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getLesson(Request $request)
    {

        $lessonList = GsToLesson::leftJoin('lesson', 'lesson.id', 'gs_to_lesson.lesson_id')
            ->orderBy('lesson.order', 'asc')
            ->where('gs_to_lesson.gs_id', $request->gs_id)
            ->where('lesson.status', '1')
            ->pluck('lesson.title as lesson_name', 'lesson.id')
            ->toArray();

        $lessonList = ['0' => __('label.SELECT_LESSON_OPT')] + $lessonList;

        $html = view('lessonWiseGsFeedback.fromDs.getLesson', compact('lessonList'))->render();
        return Response::json(['html' => $html]);
    }


    public function filter(Request $request)
    {

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'gs_id' => 'required|not_in:0',
            'lesson_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
            'gs_id.not_in' => __('label.THE_GS_FIELD_IS_REQUIRED'),
            'lesson_id.not_in' => __('label.THE_LESSON_FIELD_IS_REQUIRED'),
        ];
        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&gs_id=' . $request->gs_id
            . '&lesson_id=' . $request->lesson_id . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('lessonWiseGsFeedbackFromDs?generate=false&' . $url)
                ->withInput()
                ->withErrors($validator);
        }
        return redirect('lessonWiseGsFeedbackFromDs?generate=true&' . $url);
    }
}
