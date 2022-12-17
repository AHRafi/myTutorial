<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\SynToSubSyn;
use App\CmToSyn;
use App\CmBasicProfile;
use App\Term;
use App\SynToCourse;
use App\CrGeneration;
use Response;
use PDF;
use Common;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class CmCourseReportCrntController extends Controller {

    private $controller = 'CmCourseReportCrnt';

    public function index(Request $request) {
        $qpArr = $request->all();
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.NOMINAL_ROLL');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.NOMINAL_ROLL');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        $sortByList = [
            'svc' => __('label.WING'),
            'arms_service' => __('label.ARMS_SERVICE')
        ];
        $synList = $targetArr = $subSynList = $crReportList = [];
        if ($request->generate == 'true') {

            $cmArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                    ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                    ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                    ->where('cm_basic_profile.status', '1')
                    ->where('cm_basic_profile.course_id', $request->course_id)
                    ->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                    , 'cm_basic_profile.photo', 'rank.code as rank', 'course.training_year_id', 'cm_basic_profile.email', 'cm_basic_profile.id'
                    , 'cm_basic_profile.number', 'commissioning_course.name as comm_course_name', 'arms_service.code as arms_service_name');

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

            $crReportList = CrGeneration::where('course_id', $request->course_id)
                            ->pluck('report_file', 'cm_id')->toArray();
        }

        return view('reportCrnt.cmCourseReport.index', compact('request', 'activeTrainingYearList', 'courseList'
                        , 'targetArr', 'qpArr', 'sortByList', 'crReportList'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('reportCrnt.cmCourseReport.getCourse', compact('courseList'))->render();
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


        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id
                . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('cmCourseReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('cmCourseReportCrnt?generate=true&' . $url);
    }

}
