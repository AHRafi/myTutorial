<?php

namespace App\Http\Controllers;

use App\Course;
use App\MaProcess;
use App\TrainingYear;
use App\CiModerationMarking;
use App\TermToCourse;
//use App\Marking;
use Auth;
use DB;
use Common;
use Illuminate\Http\Request;
use Response;
use Validator;

class MaProcessController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.MA_PROCESS');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYearInfo->id)
//                        ->where('status', '1')->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.TERM_SCHEDULING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termArr = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->select('term.name', 'term.id')
                        ->where('term_to_course.course_id', $activeCourse->id)
                        ->orderBy('term.order', 'asc')->get();
        
        $processList = Common::getMaProcessList();

        $prevDataInfo = MaProcess::select('process', 'term_id')
                ->where('course_id', $activeCourse->id)
                ->get();

        $prevDataArr = [];
        if (!$prevDataInfo->isEmpty()) {
            foreach ($prevDataInfo as $inf) {
                $prevDataArr[$inf->term_id] = $inf->toArray();
            }
        }

        

        return view('maProcess.index')->with(compact('activeTrainingYearInfo', 'activeCourse'
                                , 'request', 'prevDataArr', 'termArr', 'processList'));
    }

    public function saveProcess(Request $request) {

        $process = $request->process;
        $rules = [
            'course_id' => 'required|not_in:0',
        ];
        $messages = [];

        
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }



        $target = [];
        $i = 0;

        if (!empty($process)) {
            foreach ($process as $termId => $inf) {
                $target[$i]['course_id'] = $request->course_id;
                $target[$i]['term_id'] = $termId;
                $target[$i]['process'] = $inf['type'];
                $target[$i]['updated_at'] = date('Y-m-d H:i:s');
                $target[$i]['updated_by'] = Auth::user()->id;
                $i++;
            }
        }

        MaProcess::where('course_id', $request->course_id)
                ->delete();

        if (MaProcess::insert($target)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.MA_PROCESS_COULD_NOT_SET')), 401);
        }
    }

}
