<?php

namespace App\Http\Controllers;

use App;
use App\MilCourse;
use App\CmRelativeInDefence;
use App\UserRelativeInDefence;
use App\TrainingYear;
use App\Wing;
use Illuminate\Http\Request;
use Redirect;
use Response;
use Session;
use Helper;
use Common;
use Validator;
use View;

class MilCourseController extends Controller {

    private $controller = 'MilCourse';

    public function index(Request $request) {

        //passing param for custom function
        $qpArr = $request->all();
        $nameArr = MilCourse::select('name')->orderBy('name', 'asc')->get();

        $targetArr = MilCourse::select('id', 'name', 'short_info', 'category_id', 'status', 'order')
                ->orderBy('order', 'asc');

        $categoryList = Common::getMilCourseCategory();

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('name', 'LIKE', '%' . $searchText . '%');
            });
        }
        if (!empty($request->fill_category_id)) {
            $targetArr = $targetArr->where('category_id', $request->fill_category_id);
        }
        //end filtering
        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/milCourse?page=' . $page);
        }

        return view('milCourse.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'categoryList'));
    }

    public function create(Request $request) { //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $categoryList = Common::getMilCourseCategory();
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        return view('milCourse.create')->with(compact('qpArr', 'categoryList', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update
        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:mil_course',
                    'category_id' => 'required|not_in:0',
                    'order' => 'required|not_in:0',
        ]);

        if ($validator->fails()) {
            return redirect('milCourse/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target = new MilCourse;
        $target->name = $request->name;
        $target->short_info = $request->short_info;
        $target->category_id = $request->category_id;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper::insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.MIL_COURSE_CREATED_SUCCESSFULLY'));
            return redirect('milCourse');
        } else {
            Session::flash('error', __('label.MIL_COURSE_COULD_NOT_BE_CREATED'));
            return redirect('milCourse/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = MilCourse::find($id);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('milCourse');
        }
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        //passing param for custom function
        $qpArr = $request->all();
        $categoryList = Common::getMilCourseCategory();

        return view('milCourse.edit')->with(compact('target', 'qpArr', 'categoryList', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = MilCourse::find($id);
        $presentOrder = $target->order;

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update
        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:mil_course,name,' . $id,
                    'category_id' => 'required|not_in:0',
                    'order' => 'required|not_in:0',
        ]);

        if ($validator->fails()) {
            return redirect('milCourse/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->short_info = $request->short_info;
        $target->category_id = $request->category_id;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', __('label.MIL_COURSE_UPDATED_SUCCESSFULLY'));
            return redirect('milCourse' . $pageNumber);
        } else {
            Session::flash('error', __('label.MIL_COURSE_COULD_NOT_BE_UPDATED'));
            return redirect('milCourse/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = MilCourse::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        
        $dependencyArr[5] = 5;

        //Check Dependency before deletion
        $cmMilCourseInfo = CmRelativeInDefence::select('cm_relative_info')->get();
        
        if(!$cmMilCourseInfo->isEmpty()){
            foreach($cmMilCourseInfo as $cmMil){
                $courseArr = !empty($cmMil->cm_relative_info) ? json_decode($cmMil->cm_relative_info, true) : [];
                if(!empty($courseArr)){
                    foreach($courseArr as $cKey => $cInfo){
                        $dependencyArr[$cInfo['course']] = $cInfo['course'];
                    }
                }
            }
        }
        $dsMilCourseInfo = UserRelativeInDefence::select('user_relative_info')->get();
        
        if(!$dsMilCourseInfo->isEmpty()){
            foreach($dsMilCourseInfo as $dsMil){
                $courseArr = !empty($dsMil->user_relative_info) ? json_decode($dsMil->user_relative_info, true) : [];
                if(!empty($courseArr)){
                    foreach($courseArr as $dKey => $dInfo){
                        $dependencyArr[$dInfo['course']] = $dInfo['course'];
                    }
                }
            }
        }
        
        if (!empty($dependencyArr)) {
            if (in_array($id, $dependencyArr)) {
                Session::flash('error', __('label.THIS_COURSE_HAS_BEEN_USED_IN_THE_PROFILE'));
                return redirect('milCourse' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.MIL_COURSE_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.MIL_COURSE_COULD_NOT_BE_DELETED'));
        }
        return redirect('milCourse' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fill_category_id=' . $request->fill_category_id;
        return Redirect::to('milCourse?' . $url);
    }

    public function close(Request $request) {

        $target = MilCourse::find($request->id);
        if (empty($target)) {
            return Response::json(array('success' => false, 'message' => __('label.INVALID_DATA_ID')), 401);
        }
        $target->status = '2';

        if ($target->save()) {
            return Response::json(['success' => true, 'message' => $target->name . ' ' . __('label.HAS_BEEN_CLOSED')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.BATCH_COULD_NOT_BE_CLOSED')), 401);
        }
    }

}
