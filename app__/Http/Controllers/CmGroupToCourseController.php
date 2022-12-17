<?php

namespace App\Http\Controllers;

use Validator;
use App\CmGroupToCourse;
use App\TrainingYear;
use App\Course;
use App\CmGroup;
use App\CmGroupMemberTemplate;
use Response;
use Auth;
use Illuminate\Http\Request;

class CmGroupToCourseController extends Controller {

    public function index(Request $request) {
        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.RELATE_CM_GROUP_TO_COURSE');
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
        
        $targetArr = CmGroup::select('id', 'name')
                        ->where('status', '1')
                        ->orderBy('order', 'asc')->get();
        //checked
        $previousDataArr = CmGroupToCourse::select('cm_group_id', 'id')
                        ->where('course_id', $activeCourse->id)
                        ->get()->toArray();
        

        $previousDataList = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $previousData) {
                $previousDataList[$previousData['cm_group_id']] = $previousData['cm_group_id'];
            }
        }
        //checked
        //Dependency check Disable data
        
        $cmGroupTemplateDataArr = CmGroupMemberTemplate::where('course_id', $activeCourse->id)
                        ->pluck('cm_group_id','cm_group_id')
                        ->toArray();
        
        $disableCmGroup = [];
        //end
        
        
        return view('cmGroupToCourse.index')->with(compact('activeTrainingYear', 'activeCourse'
                , 'targetArr', 'previousDataList', 'disableCmGroup', 'cmGroupTemplateDataArr'));
    }

//    public function getCmGroup(Request $request) {
//
//        $targetArr = CmGroup::select('id', 'name')
//                        ->where('status', '1')
//                        ->orderBy('order', 'asc')->get();
//        //checked
//        $previousDataArr = CmGroupToCourse::select('cm_group_id', 'id')
//                        ->where('course_id', $request->course_id)
//                        ->get()->toArray();
//        
//
//        $previousDataList = [];
//        if (!empty($previousDataArr)) {
//            foreach ($previousDataArr as $previousData) {
//                $previousDataList[$previousData['cm_group_id']] = $previousData['cm_group_id'];
//            }
//        }
//        //checked
//        //Dependency check Disable data
//        $disableCmGroup = [];
//        //end
//        $html = view('cmGroupToCourse.showCmGroup', compact('targetArr', 'previousDataList', 'disableCmGroup'))->render();
//        return response()->json(['html' => $html]);
//    }

    public function saveCmGroup(Request $request) {

        $cmGroupArr = $request->cm_group_id;

        if (empty($cmGroupArr)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_COURSE_TO_ATLEAST_ONE_CM_GROUP')), 401);
        }
        $rules = [
            'cm_group_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }
        
        $data = [];
        if (!empty($request->training_year_id) && !empty($request->course_id)) {
            if (!empty($cmGroupArr)) {
                foreach ($cmGroupArr as $key => $cmGroupId) {
                    $data[$key]['course_id'] = $request->course_id;
                    $data[$key]['cm_group_id'] = $cmGroupId;
                    $data[$key]['updated_by'] = Auth::user()->id;
                    $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
            }

            CmGroupToCourse::where('course_id', $request->course_id)
                    ->delete();
        }

        if (CmGroupToCourse::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_CM_GROUP')), 401);
        }
    }

}
