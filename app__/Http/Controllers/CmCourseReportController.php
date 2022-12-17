<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\CmBasicProfile;
use App\CrGeneration;
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

class CmCourseReportController extends Controller {

    private $controller = 'CmCourseReport';

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

        return view('report.cmCourseReport.index', compact('request', 'activeTrainingYearList', 'courseList'
                        , 'targetArr', 'qpArr', 'sortByList', 'crReportList'));
    }

    public function getCourse(Request $request) {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $request->training_year_id)
                        ->where('status', '<>', '0')
                        ->orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $html = view('report.cmCourseReport.getCourse', compact('courseList'))->render();
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
            return redirect('cmCourseReport?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('cmCourseReport?generate=true&' . $url);
    }

}
