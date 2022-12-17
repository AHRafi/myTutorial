<?php

namespace App\Http\Controllers;

use Validator;
use App\MarkingFactors;
use App\FactorClassification;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Illuminate\Http\Request;

class MarkingFactorsController extends Controller {

    private $controller = 'MarkingFactors';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = MarkingFactors::select('name')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = MarkingFactors::join('factor_classification', 'factor_classification.id', '=', 'marking_factors.factor_classification')
                ->select('marking_factors.id', 'marking_factors.marks_from', 'marking_factors.marks_to'
                        , 'marking_factors.name', 'marking_factors.order', 'marking_factors.status'
                        , 'factor_classification.name as factor_classification_name')
                ->orderBy('marking_factors.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('marking_factors.name', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/markingFactors?page=' . $page);
        } else {
            return view('markingFactors.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        $factorClassificationList = array('0' => __('label.SELECT_FACTOR_CLASSIFICATION')) + FactorClassification::orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        return view('markingFactors.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'factorClassificationList'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'factor_classification' => 'required',
                    'marks_from' => 'required',
                    'marks_to' => 'required',
                    'name' => 'required',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('markingFactors/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new MarkingFactors;
        $target->factor_classification = $request->factor_classification;
        $target->marks_from = $request->marks_from;
        $target->marks_to = $request->marks_to;
        $target->name = $request->name;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.MARKING_FACTORS_CREATED_SUCCESSFULLY'));
            return redirect('markingFactors');
        } else {
            Session::flash('error', __('label.MARKING_FACTORS_COULD_NOT_BE_CREATED'));
            return redirect('markingFactors/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = MarkingFactors::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('markingFactors');
        }

        $factorClassificationList = array('0' => __('label.SELECT_FACTOR_CLASSIFICATION')) + FactorClassification::orderBy('id', 'desc')->pluck('name', 'id')->toArray();


        //passing param for custom function
        $qpArr = $request->all();

        return view('markingFactors.edit')->with(compact('target', 'qpArr', 'orderList', 'factorClassificationList'));
    }

    public function update(Request $request, $id) {
        $target = MarkingFactors::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'factor_classification' => 'required',
                    'marks_from' => 'required',
                    'marks_to' => 'required',
                    'name' => 'required',
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('markingFactors/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->factor_classification = $request->factor_classification;
        $target->marks_from = $request->marks_from;
        $target->marks_to = $request->marks_to;
        $target->name = $request->name;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.MARKING_FACTORS_UPDATED_SUCCESSFULLY'));
            return redirect('/markingFactors' . $pageNumber);
        } else {
            Session::flash('error', trans('label.MARKING_FACTORS_CUOLD_NOT_BE_UPDATED'));
            return redirect('markingFactors/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = MarkingFactors::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }



        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.MARKING_FACTORS_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.MARKING_FACTORS_COULD_NOT_BE_DELETED'));
        }
        return redirect('markingFactors' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('markingFactors?' . $url);
    }

}
