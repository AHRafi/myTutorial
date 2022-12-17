<?php

namespace App\Http\Controllers;

use Validator;
use App\DsGroup;
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
use Illuminate\Http\Request;

class DsGroupController extends Controller {

    private $controller = 'DsGroup';

    public function __construct() {

    }

    public function index(Request $request) {
        $nameArr = DsGroup::select('name')->orderBy('order', 'asc')->get();
        
        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = DsGroup::select('ds_group.id', 'ds_group.name', 'ds_group.order', 'ds_group.status')
                ->orderBy('ds_group.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
            $query->where('ds_group.name', 'LIKE', '%' . $searchText . '%');
            });
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
            return redirect('/dsGroup?page=' . $page);
        } else {
            return view('dsGroup.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('dsGroup.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }
    
    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('dsGroup/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new DsGroup;
        $target->name = $request->name;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.DS_GROUP_CREATED_SUCCESSFULLY'));
            return redirect('dsGroup');
        } else {
            Session::flash('error', __('label.DS_GROUP_COULD_NOT_BE_CREATED'));
            return redirect('dsGroup/create' . $pageNumber);
        }
    }
    
    public function edit(Request $request, $id) {
        $target = DsGroup::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('dsGroup');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('dsGroup.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = DsGroup::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('dsGroup/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.DS_GROUP_UPDATED_SUCCESSFULLY'));
            return redirect('/dsGroup' . $pageNumber);
        } else {
            Session::flash('error', trans('label.DS_GROUP_CUOLD_NOT_BE_UPDATED'));
            return redirect('dsGroup/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = DsGroup::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        
        //START:: Check Dependency before deletion
        $dependencyArr = ['DsGroupToCourse' => 'ds_group_id'];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('dsGroup' . $pageNumber);
            }
        }
        //END:: Check Dependency before deletion

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.DS_GROUP_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.DS_GROUP_COULD_NOT_BE_DELETED'));
        }
        return redirect('dsGroup' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('dsGroup?' . $url);
    }

}
