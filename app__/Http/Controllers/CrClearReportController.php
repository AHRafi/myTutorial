<?php

namespace App\Http\Controllers;

use App\Course;
use App\TrainingYear;
use App\Term;
use App\CrGeneration;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class CrClearReportController extends Controller {

    public function index(Request $request) {

        $trainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')];
        $closedTrainingYear = TrainingYear::where('status', '2')->orderBy('start_date', 'desc')
                        ->select('name', 'id')->first();

        $activeTrainingYear = TrainingYear::where('status', '1')->select('name', 'id')->first();

        if (!empty($activeTrainingYear)) {
            $trainingYearList[$activeTrainingYear->id] = $activeTrainingYear->name;
        }
        if (!empty($closedTrainingYear)) {
            $trainingYearList[$closedTrainingYear->id] = $closedTrainingYear->name;
        }

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;

        $pervReportInfo = [];
        if ($request->generate == 'true') {
            $pervReportInfo = CrGeneration::join('cm_basic_profile', 'cm_basic_profile.id', 'cr_generation.cm_id')
                            ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                            ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                            ->join('users', 'users.id', 'cr_generation.updated_by')
                            ->select('cm_basic_profile.personal_no', 'cm_basic_profile.photo'
                                    , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.official_name, ')') as cm_name")
                                    , 'wing.code as wing_name', 'users.official_name as generated_by'
                                    , 'cr_generation.cm_id', 'cr_generation.updated_at', 'cr_generation.report_file')
                            ->where('cr_generation.course_id', $request->course_id)
                            ->orderBy('cr_generation.updated_at', 'desc')
                            ->get()->toArray();
        }

        return view('crSetup.clearReports.index')->with(compact('trainingYearList', 'courseList'
                                , 'pervReportInfo', 'request'));
    }

    public function getCourse(Request $request) {
        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;
        $html = view('crSetup.clearReports.getCourse', compact('courseList'))->render();
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


        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('crClearReport?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('crClearReport?generate=true&' . $url);
    }

    public function clear(Request $request) {

        $rules = [
            'course_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }
        
        $deleteReport = CrGeneration::where('course_id', $request->course_id);
        if ($deleteReport->delete()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FAILED_TO_CLEAR_GENERATED_COURSE_REPORTS_OF_THIS_COURSE')), 401);
        }
    }

}
