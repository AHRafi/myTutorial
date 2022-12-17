<?php

namespace App\Http\Controllers;

use Validator;
use App\CmGroup;
use App\Service;
use Session;
use Redirect;
use Helper;
use Response;
use App;
use View;
use PDF;
use Auth;
use Input;
use Common;
use Illuminate\Http\Request;

class CmGroupController extends Controller {

    private $controller = 'CmGroup';

    public function __construct() {

    }

    public function index(Request $request) {
        $nameArr = CmGroup::select('name')->orderBy('order', 'asc')->get();
        
        $groupTypeList = Common::getCmGroupTypeList();
        
        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = CmGroup::select('cm_group.id', 'cm_group.type', 'cm_group.name', 'cm_group.order', 'cm_group.status')
                ->orderBy('cm_group.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
            $query->where('cm_group.name', 'LIKE', '%' . $searchText . '%');
            });
        }
        if (!empty($request->fil_type)) {
            $targetArr = $targetArr->where('cm_group.type', $request->fil_type);
        }


        //end filtering

        if ($request->download == 'pdf') {
            $targetArr = $targetArr->get();
        } else {
            $targetArr = $targetArr->paginate(Session::get('paginatorCount'));
        }


        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/cmGroup?page=' . $page);
        } else {
            return view('cmGroup.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'groupTypeList'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        $groupTypeList = Common::getCmGroupTypeList();
        
        return view('cmGroup.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'groupTypeList'));
    }
    
    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'type' => 'required|not_in:0',
                    'order' => 'required|not_in:0',
        ]);


        if ($validator->fails()) {
            return redirect('cmGroup/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new CmGroup;
        $target->name = $request->name;
        $target->type = $request->type;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.CM_GROUP_CREATED_SUCCESSFULLY'));
            return redirect('cmGroup');
        } else {
            Session::flash('error', __('label.CM_GROUP_COULD_NOT_BE_CREATED'));
            return redirect('cmGroup/create' . $pageNumber);
        }
    }
    
    public function edit(Request $request, $id) {
        $target = CmGroup::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('cmGroup');
        }

        //passing param for custom function
        $qpArr = $request->all();
        $groupTypeList = Common::getCmGroupTypeList();
        

        return view('cmGroup.edit')->with(compact('target', 'qpArr', 'orderList', 'groupTypeList'));
    }

    public function update(Request $request, $id) {
        $target = CmGroup::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'type' => 'required|not_in:0',
                    'order' => 'required|not_in:0',
        ]);

        if ($validator->fails()) {
            return redirect('cmGroup/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->type = $request->type;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.CM_GROUP_UPDATED_SUCCESSFULLY'));
            return redirect('/cmGroup' . $pageNumber);
        } else {
            Session::flash('error', trans('label.CM_GROUP_CUOLD_NOT_BE_UPDATED'));
            return redirect('cmGroup/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = CmGroup::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        
        //START:: Check Dependency before deletion
        $dependencyArr = ['CmGroupToCourse' => 'cm_group_id'];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('cmGroup' . $pageNumber);
            }
        }
        //END:: Check Dependency before deletion

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.CM_GROUP_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.CM_GROUP_COULD_NOT_BE_DELETED'));
        }
        return redirect('cmGroup' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fil_type=' . $request->fil_type;
        return Redirect::to('cmGroup?' . $url);
    }

}
