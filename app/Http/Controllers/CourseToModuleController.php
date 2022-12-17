<?php

namespace App\Http\Controllers;

use Validator;
use App\DsGroupToCourse;
use App\TrainingYear;
use App\Course;
use App\DsGroup;
use App\DsGroupMemberTemplate;
use App\GsModule;
use App\CourseToModule;
use Response;
use Auth;
use Illuminate\Http\Request;

class CourseToModuleController extends Controller
{
    public function index(Request $request) {

        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

         $gsModuleArr = GsModule::orderBy('order', 'asc')->pluck('name', 'id')->toArray();

         $prevGsModuleArr = CourseToModule::pluck('module_id','module_id')->toArray();

        return view('courseToModule.index')->with(compact('activeTrainingYear', 'activeCourse','gsModuleArr','prevGsModuleArr'));
    }

    public function saveModule(Request $request) {
//        echo "<pre>";
//        print_r($request->all());
//        exit;

        $gsModule = $request->gs_module;


        if (empty($gsModule)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_COURSE_TO_ATLEAST_ONE_MODULE')), 401);
        }
        $rules = [
            'gs_module' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        if (!empty($request->training_year_id) && !empty($request->course_id)) {
            if (!empty($gsModule)) {
                foreach ($gsModule as $gsModuleId => $gsModuleId) {

                    $data[$gsModuleId]['course_id'] = $request->course_id;
                    $data[$gsModuleId]['module_id'] = $gsModuleId;
                    $data[$gsModuleId]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$gsModuleId]['updated_by'] = Auth::user()->id;
                }
            }
        }

        if (CourseToModule::insert($data)) {
        return Response::json(array('success' => true, 'message' => __('label.MODULE_SET_TO_COURSE_SUCCESSFULLY')), 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_MODULE')), 401);
        }

    }

    public function cloneModal(Request $request){

        $activeTrainingYear = TrainingYear::where('status', '1')->first();
        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();


        $previousCourseList = ['0' => __('label.SELECT_PRE_COURSE_OPT')] + Course::where('status','2')
                     ->pluck('name', 'id')->toArray();


        $html = view('courseToModule.showCloneModal',compact('activeCourse','previousCourseList'))->render();
        return Response::json(['html' => $html]);

    }

    public function showPreModuleTable(Request $request){

        $preCourseId = $request->pre_course_id;
        $activeCourseId = $request->active_course_id;


//        $prevGsModuleArr = CourseToModule::where('course_id',$preCourseId)->pluck('module_id','module_id')->toArray();

        $preCourseModuleList = CourseToModule::join('gs_module','gs_module.id','course_to_module.module_id')
                                            ->where('course_id',$preCourseId)
                                            ->select('course_to_module.module_id', 'gs_module.name','gs_module.status')
                                            ->get();


        $html = view('courseToModule.showPreModuleTable',compact('preCourseModuleList','activeCourseId'))->render();
        return Response::json(['html' => $html]);

    }
    public function savePreModule(Request $request){

//        print_r($request->all());
//        exit;

       $activeCourseId = $request->active_course_id;
       $previousModule = $request->pre_module;


        if (empty($previousModule)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => __('label.PLEASE_RELATE_COURSE_TO_ATLEAST_ONE_MODULE')), 401);
        }
        $rules = [
            'pre_module' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $data = [];
        if (!empty($request->active_course_id)) {
            if (!empty($previousModule)) {
                foreach ($previousModule as $previousModuleId => $previousModuleId) {

                    $data[$previousModuleId]['course_id'] = $request->active_course_id;
                    $data[$previousModuleId]['module_id'] = $previousModuleId;
                    $data[$previousModuleId]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$previousModuleId]['updated_by'] = Auth::user()->id;
                }
            }
        }

        if (CourseToModule::insert($data)) {
        return Response::json(array('success' => true, 'message' => __('label.MODULE_SET_TO_COURSE_SUCCESSFULLY')), 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_MODULE')), 401);
        }

    }
}
