<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\Term;
use Response;
use PDF;
use Auth;
use File;
use DB;
use Common;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class NominalRollReportController extends Controller {

    private $controller = 'NominalRollReport';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')] + TrainingYear::where('status', '2')
                        ->orderBy('start_date', 'desc')
                        ->pluck('name', 'id')->toArray();
        $qpArr = $request->all();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $termList = ['0' => __('label.SELECT_TERM_OPT')] + Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term_id')->toArray();
        $sortByList = [
            'svc' => __('label.WING'),
            'arms_service' => __('label.ARMS_SERVICE')
        ];
        $synList = $targetArr = $subSynList = [];
        if ($request->generate == 'true') {

            $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                    ->join('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', 'cm_basic_profile.id')
                    ->join('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id')
                    ->where('cm_group.order', '<=', '2')
                    ->where('cm_basic_profile.status', '1')
                    ->where('cm_group_member_template.course_id', $request->course_id);

            if (!empty($request->term_id)) {
                $cmArr = $cmArr->where('cm_group_member_template.term_id', $request->term_id);
            }

            $cmArr = $cmArr->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                    , 'cm_basic_profile.photo', 'rank.code as rank', 'course.training_year_id', 'cm_basic_profile.email', 'cm_basic_profile.id'
                    , 'cm_basic_profile.number', 'commissioning_course.name as comm_course_name', 'cm_group_member_template.course_id'
                    , 'cm_group_member_template.term_id', 'arms_service.code as arms_service_name', 'cm_group.name as cm_group_name', 'cm_group.id as cm_group_id');

            if (!empty($request->term_id)) {
                $cmArr = $cmArr->orderBy('cm_group.order', 'asc');
            }
            if (!empty($request->sort) && $request->sort == 'arms_service') {
                $cmArr = $cmArr->orderBy('arms_service.order', 'asc');
            }
            $cmArr = $cmArr->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();

            if (!$cmArr->isEmpty()) {
                foreach ($cmArr as $cmInfo) {
                    $targetArr[$cmInfo->id] = $cmInfo->toArray();
                }
            }
//    echo '<pre>';    print_r($targetArr); exit;

            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $termName = $request->term_id != '0' && !empty($termList[$request->term_id]) ? '_' . $termList[$request->term_id] : '';
            $fileName = 'CM_Profile' . $tyName . $courseName . $termName;
            $fileName = Common::getFileFormatedName($fileName);
        }


        if ($request->view == 'print') {
            return view('report.nominalRoll.print.index')->with(compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('report.nominalRoll.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('report.nominalRoll.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                    , 'targetArr', 'qpArr', 'sortByList')), $fileName . '.xlsx');
        } else {

            return view('report.nominalRoll.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                            , 'targetArr', 'qpArr', 'subSynList', 'sortByList'));
        }
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('report.nominalRoll.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getTerm(Request $request) {

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term_id')->toArray();

        $html = view('report.nominalRoll.getTerm', compact('termList'))->render();
        return Response::json(['html' => $html]);
    }

    public function filter(Request $request) {
        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
        ];


        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&term_id=' . $request->term_id
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('nominalRollReport?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('nominalRollReport?generate=true&' . $url);
    }

    public function profile(Request $request, $id) {
        $loadView = 'report.nominalRoll.profile';
        $prinLloadView = 'report.nominalRoll.print.profile';
        return Common::getProfile($request, $id, $loadView, $prinLloadView);
    }

}
