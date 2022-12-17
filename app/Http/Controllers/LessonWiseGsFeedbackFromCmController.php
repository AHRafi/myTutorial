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
use App\GsEvalByCm;
use App\GsToLesson;
use App\DsMarkingGroup;
use App\Imports\ExcelImport;
use App\Lesson;
use App\UserBasicProfile;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LessonWiseGsFeedbackFromCmController extends Controller
{

    private $controller = 'LessonWiseGsFeedbackFromCm';

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
        $gradingArr = $cmArr = [];

        if ($request->generate == 'true') {
            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $gsName = $request->gs_id != '0' && !empty($courseList[$request->gs_id]) ? '_' . $courseList[$request->gs_id] : '';
            $lessonName = $request->lesson_id != '0' && !empty($termList[$request->lesson_id]) ? '_' . $termList[$request->lesson_id] : '';

            $fileName = 'Lesson_Wise_GS_Feedback_from_CM_' . $tyName . $courseName . $lessonName;
            $fileName = Common::getFileFormatedName($fileName);

            $cmInfo = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->where('cm_basic_profile.status', '1')
                    ->where('cm_basic_profile.course_id', $request->course_id)
                    ->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                            , 'rank.code as rank', 'wing.code as wing', 'cm_basic_profile.photo','cm_basic_profile.id as cm_id');

            if (!empty($request->sort)) {
                if ($request->sort == 'svc') {
                    $cmInfo = $cmInfo->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $cmInfo = $cmInfo->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'seniority') {
                    $cmInfo = $cmInfo->orderBy('rank.svc_order', 'asc')
                            ->orderBy('cm_basic_profile.commisioning_date', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'rank') {
                    $cmInfo = $cmInfo->orderBy('rank.order', 'asc');
                } elseif ($request->sort == 'personal_no') {
                    $cmInfo = $cmInfo->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $cmInfo = $cmInfo->orderBy('wing.order', 'asc')
                            ->orderBy('cm_basic_profile.official_name', 'asc');
                }
            } else {
                $cmInfo = $cmInfo->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            }

            $cmInfo = $cmInfo->get();

            $gradingArr = GsEvalByCm::where('course_id', $request->course_id)
                ->where('gs_id', $request->gs_id)
                ->where('lesson_id', $request->lesson_id)
                ->pluck('grading', 'updated_by')->toArray();

            // echo '<pre>'; print_r( $gradingArr); exit;

            if (!$cmInfo->isEmpty()) {
                foreach ($cmInfo as $cm) {
                    $cmArr[$cm->cm_id] = $cm->toArray();
                    $cmArr[$cm->cm_id]['grading'] = $gradingArr[$cm->cm_id] ?? 0;
                }
            }


            // get postion after term total
            if (empty($request->sort) || $request->sort == 'position') {
                if (!empty($cmArr)) {
                    usort($cmArr, function ($item1, $item2) {
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
            return view('lessonWiseGsFeedback.fromCm.print.index')->with(compact(
                'activeTrainingYearList',
                'courseList',
                'gsList',
                'lessonList',
                'gradingArr',
                'cmArr',
                'sortByList'
            ));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('lessonWiseGsFeedback.fromCm.print.index', compact(
                'activeTrainingYearList',
                'courseList',
                'gsList',
                'lessonList',
                'gradingArr',
                'cmArr',
                'sortByList'
            ))
                ->setPaper('a4', 'landscape')
                ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('lessonWiseGsFeedback.fromCm.print.index', compact(
                'activeTrainingYearList',
                'courseList',
                'gsList',
                'lessonList',
                'gradingArr',
                'cmArr',
                'sortByList'
            )), $fileName . '.xlsx');
        }

        return view('lessonWiseGsFeedback.fromCm.index', compact(
            'activeTrainingYearList',
            'courseList',
            'gsList',
            'lessonList',
            'gradingArr',
            'cmArr',
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
        $html = view('lessonWiseGsFeedback.fromCm.getCourse', compact('courseList'))->render();
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

        $html = view('lessonWiseGsFeedback.fromCm.getGs', compact('gsList'))->render();
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

        $html = view('lessonWiseGsFeedback.fromCm.getLesson', compact('lessonList'))->render();
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
            return redirect('lessonWiseGsFeedbackFromCm?generate=false&' . $url)
                ->withInput()
                ->withErrors($validator);
        }
        return redirect('lessonWiseGsFeedbackFromCm?generate=true&' . $url);
    }
}
