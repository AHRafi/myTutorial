<?php

namespace App\Http\Controllers;

use Validator;
use App\SynToCourse;
use App\TrainingYear;
use App\Course;
use App\SubSyndicate;
use App\SynToSubSyn;
use App\CmToSyn;
use Response;
use Auth;
use Illuminate\Http\Request;

class SynToSubSynController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.RELATE_SYN_TO_COURSE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

//        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::where('training_year_id', $activeTrainingYear->id)
//                        ->orderBy('id', 'desc')->pluck('name', 'id')->toArray();
        
        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.TERM_SCHEDULING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }
        
        $synList = ['0' => __('label.SELECT_SYN_OPT')] + SynToCourse::join('syndicate', 'syndicate.id', '=', 'syn_to_course.syn_id')
                        ->where('syn_to_course.course_id', $activeCourse->id)
                        ->pluck('syndicate.name', 'syndicate.id')->toArray();
        
//        echo '<pre>';        print_r($synList); exit;
        
        return view('synToSubSyn.index')->with(compact('activeTrainingYear', 'activeCourse', 'synList'));
    }

//    public function getSyn(Request $request){
//        $synList = ['0' => __('label.SELECT_SYN_OPT')] + SynToCourse::join('syndicate', 'syndicate.id', '=', 'syn_to_course.syn_id')
//                        ->where('syn_to_course.course_id', $request->course_id)
//                        ->pluck('syndicate.name', 'syndicate.id')->toArray();
//        $html = view('synToSubSyn.showSyn', compact('synList'))->render();
//        return response()->json(['html' => $html]);
//    }

    public function getSubSyn(Request $request) {

        $targetArr = SubSyndicate::select('id', 'name')
                        ->where('status', '1')
                        ->orderBy('order', 'asc')->get();
        //checked
        $previousDataArr = SynToSubSyn::select('sub_syn_id', 'id')
                        ->where('course_id', $request->course_id)
                        ->where('syn_id', $request->syn_id)
                        ->get()->toArray();
        

        $previousDataList = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $previousData) {
                $previousDataList[$previousData['sub_syn_id']] = $previousData['sub_syn_id'];
            }
        }
        //checked
        //Dependency check Disable data
        $cmToSynDataArr = CmToSyn::where('course_id', $request->course_id)
                        ->where('syn_id', $request->syn_id)
                        ->pluck('sub_syn_id','sub_syn_id')
                        ->toArray();
//        echo '<pre>';        print_r($cmToSynDataArr); exit;
        
        $disableSubSyn = [];
        //end
        $html = view('synToSubSyn.showSubSyn', compact('targetArr', 'previousDataList', 'cmToSynDataArr', 'disableSubSyn'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveSubSyn(Request $request) {

        $subSynArr = $request->sub_syn_id;
        
        if (empty($subSynArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_SYN_TO_ATLEAST_ONE_SUB_SYN')), 401);
        }
        $rules = [
            'sub_syn_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }
        
        
        $data = [];
        if (!empty($request->training_year_id) && !empty($request->course_id)) {
            if (!empty($subSynArr)) {
                foreach ($subSynArr as $key => $subSynId) {
                    $data[$key]['course_id'] = $request->course_id;
                    $data[$key]['syn_id'] = $request->syn_id;
                    $data[$key]['sub_syn_id'] = $subSynId;
                    $data[$key]['updated_by'] = Auth::user()->id;
                    $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
            }

            SynToSubSyn::where('course_id', $request->course_id)
                    ->where('syn_id', $request->syn_id)
                    ->delete();
        }

        if (SynToSubSyn::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_SUB_SYN')), 401);
        }
    }

}
