<?php

namespace App\Http\Controllers;

use App\Course;
use App\ModerationMarkingLimit;
use App\TrainingYear;
use App\TermToCourse;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class ModerationMarkingLimitController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.MODERATION_MARKING_LIMIT_MENU');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        return view('moderationMarkingLimit.index')->with(compact('activeTrainingYearInfo', 'courseList'));
    }

    public function getMarkingLimit(Request $request) {
        $termArr = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->select('term.name', 'term.id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->orderBy('term.order', 'asc')->get();

        $prevDataInfo = ModerationMarkingLimit::select('ci_mod', 'comdt_mod', 'term_id')
                ->where('course_id', $request->course_id)
                ->get();

        $prevDataArr = [];
        if (!$prevDataInfo->isEmpty()) {
            foreach ($prevDataInfo as $inf) {
                $prevDataArr[$inf->term_id] = $inf->toArray();
            }
        }


        $html = view('moderationMarkingLimit.showMarkingLimit', compact('request', 'prevDataArr', 'termArr'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveMarkingLimit(Request $request) {

        $mod = $request->mod;
        $rules = [
            'course_id' => 'required|not_in:0',
        ];
        $messages = [];

        if (!empty($mod)) {
            foreach ($mod as $termId => $inf) {
                $rules['mod.' . $termId . '.ci_mod'] = 'required';
                $messages['mod.' . $termId . '.ci_mod.required'] = __('label.CI_MODERATION_MARKING_LIMIT_IS_REQUIRED_FOR_TERM', ['term' => $inf['term_name'] ?? '']);
//                $rules['mod.' . $termId . '.comdt_mod'] = 'required';
//                $messages['mod.' . $termId . '.comdt_mod.required'] = __('label.COMDT_MODERATION_MARKING_LIMIT_IS_REQUIRED_FOR_TERM', ['term' => $inf['term_name'] ?? '']);
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }



        $target = [];
        $i = 0;

        if (!empty($mod)) {
            foreach ($mod as $termId => $inf) {
                $target[$i]['course_id'] = $request->course_id;
                $target[$i]['term_id'] = $termId;
                $target[$i]['ci_mod'] = $inf['ci_mod'];
                $target[$i]['comdt_mod'] = 0;
                $target[$i]['updated_at'] = date('Y-m-d H:i:s');
                $target[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }

        ModerationMarkingLimit::where('course_id', $request->course_id)
                ->delete();

        if (ModerationMarkingLimit::insert($target)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.MODERATION_MARKING_LIMIT_COULD_NOT_ASSIGNED')), 401);
        }
    }
}
