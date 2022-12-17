<?php

namespace App\Http\Controllers;

use Validator;
use App\CrMarkingSlab;
use Session;
use Redirect;
use Helper;
use PDF;
use Common;
use Auth;
use Illuminate\Http\Request;

class CrMarkingSlabController extends Controller {

    private $controller = 'CrMarkingSlab';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = CrMarkingSlab::select('title')->orderBy('order', 'asc')->get();
        $slabTypeList = Common::getMarkingSlabTypeList();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = CrMarkingSlab::select('id', 'start_range', 'end_range', 'type'
                        , 'title', 'order', 'status', 'b_plus_n_above')
                ->orderBy('status', 'asc')
                ->orderBy('order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('title', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/crMarkingSlab?page=' . $page);
        } else {
            return view('crSetup.markingSlab.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'slabTypeList'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        $slabTypeList = Common::getMarkingSlabTypeList();
        return view('crSetup.markingSlab.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'slabTypeList'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update
        $rules = [
            'start_range' => 'required',
            'end_range' => 'required|gt:start_range',
            'title' => 'required',
            'order' => 'required|not_in:0'
        ];
        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return redirect('crMarkingSlab/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new CrMarkingSlab;
        $target->type = $request->type;
        $target->start_range = $request->start_range;
        $target->end_range = $request->end_range;
        $target->b_plus_n_above = !empty($request->b_plus_n_above) ? $request->b_plus_n_above : '0';
        $target->title = $request->title;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.MARKING_SLAB_CREATED_SUCCESSFULLY'));
            return redirect('crMarkingSlab');
        } else {
            Session::flash('error', __('label.MARKING_SLAB_COULD_NOT_BE_CREATED'));
            return redirect('crMarkingSlab/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = CrMarkingSlab::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('crMarkingSlab');
        }

        //passing param for custom function
        $qpArr = $request->all();
        $slabTypeList = Common::getMarkingSlabTypeList();

        return view('crSetup.markingSlab.edit')->with(compact('target', 'qpArr', 'orderList', 'slabTypeList'));
    }

    public function update(Request $request, $id) {
        $target = CrMarkingSlab::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $rules = [
            'start_range' => 'required',
            'end_range' => 'required|gt:start_range',
            'title' => 'required',
            'order' => 'required|not_in:0'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect('crMarkingSlab/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->type = $request->type;
        $target->start_range = $request->start_range;
        $target->end_range = $request->end_range;
        $target->b_plus_n_above = !empty($request->b_plus_n_above) ? $request->b_plus_n_above : '0';
        $target->title = $request->title;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.MARKING_SLAB_UPDATED_SUCCESSFULLY'));
            return redirect('/crMarkingSlab' . $pageNumber);
        } else {
            Session::flash('error', trans('label.MARKING_SLAB_CUOLD_NOT_BE_UPDATED'));
            return redirect('crMarkingSlab/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = CrMarkingSlab::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        //Check Dependency before deletion
        $dependencyArr = [
            'CrSentenceToTrait' => 'marking_slab_id',
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('crMarkingSlab' . $pageNumber);
            }
        }


        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.MARKING_SLAB_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.MARKING_SLAB_COULD_NOT_BE_DELETED'));
        }
        return redirect('crMarkingSlab' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('crMarkingSlab?' . $url);
    }

}
