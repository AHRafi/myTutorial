<?php

namespace App\Http\Controllers;

use Validator;
use App\DsGroupToCourse;
use App\TrainingYear;
use App\Course;
use App\DsGroup;
use App\DsGroupMemberTemplate;
use Response;
use Auth;
use Illuminate\Http\Request;

class DsGroupToCourseController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.RELATE_DS_GROUP_TO_COURSE');
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
        
        $targetArr = DsGroup::select('id', 'name')
                        ->where('status', '1')
                        ->orderBy('order', 'asc')->get();
        //checked
        $previousDataArr = DsGroupToCourse::select('ds_group_id', 'id')
                        ->where('course_id', $activeCourse->id)
                        ->get()->toArray();
        

        $previousDataList = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $previousData) {
                $previousDataList[$previousData['ds_group_id']] = $previousData['ds_group_id'];
            }
        }
        //checked
        //Dependency check Disable data
        $dsGroupTemplateDataArr = DsGroupMemberTemplate::where('course_id', $activeCourse->id)
                        ->pluck('ds_group_id','ds_group_id')
                        ->toArray();
        
        $disableDsGroup = [];
        //end
        
        return view('dsGroupToCourse.index')->with(compact('activeTrainingYear', 'activeCourse'
                , 'targetArr', 'previousDataList', 'disableDsGroup', 'dsGroupTemplateDataArr'));
    }

//    public function getDsGroup(Request $request) {
//
//        $targetArr = DsGroup::select('id', 'name')
//                        ->where('status', '1')
//                        ->orderBy('order', 'asc')->get();
//        //checked
//        $previousDataArr = DsGroupToCourse::select('ds_group_id', 'id')
//                        ->where('course_id', $request->course_id)
//                        ->get()->toArray();
//        
//
//        $previousDataList = [];
//        if (!empty($previousDataArr)) {
//            foreach ($previousDataArr as $previousData) {
//                $previousDataList[$previousData['ds_group_id']] = $previousData['ds_group_id'];
//            }
//        }
//        //checked
//        //Dependency check Disable data
//        $disableDsGroup = [];
//        //end
//        $html = view('dsGroupToCourse.showDsGroup', compact('targetArr', 'previousDataList', 'disableDsGroup'))->render();
//        return response()->json(['html' => $html]);
//    }

    public function saveDsGroup(Request $request) {

        $dsGroupArr = $request->ds_group_id;

        if (empty($dsGroupArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_COURSE_TO_ATLEAST_ONE_DS_GROUP')), 401);
        }
        $rules = [
            'ds_group_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }
        
        $data = [];
        if (!empty($request->training_year_id) && !empty($request->course_id)) {
            if (!empty($dsGroupArr)) {
                foreach ($dsGroupArr as $key => $dsGroupId) {
                    $data[$key]['course_id'] = $request->course_id;
                    $data[$key]['ds_group_id'] = $dsGroupId;
                    $data[$key]['updated_by'] = Auth::user()->id;
                    $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
            }

            DsGroupToCourse::where('course_id', $request->course_id)
                    ->delete();
        }

        if (DsGroupToCourse::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_DS_GROUP')), 401);
        }
    }

}
