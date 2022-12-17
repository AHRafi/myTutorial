<?php

namespace App\Http\Controllers;

use Validator;
use App\FactorClassification;
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

class FactorClassificationController extends Controller {

    private $controller = 'FactorClassification';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = FactorClassification::select('name')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = FactorClassification::select('factor_classification.id', 'factor_classification.name'
                        , 'factor_classification.short_info', 'factor_classification.order', 'factor_classification.status')
                ->orderBy('factor_classification.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('factor_classification.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('factor_classification.short_info', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/factorClassification?page=' . $page);
        }


        return view('factorClassification.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('factorClassification.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $messages = array(
            
        );

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'order' => 'required|not_in:0'
                        ], $messages);


        if ($validator->fails()) {
            return redirect('factorClassification/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new FactorClassification;
        $target->name = $request->name;
        $target->short_info = $request->short_info;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.FACTOR_CLASSIFICATION_CREATED_SUCCESSFULLY'));
            return redirect('factorClassification');
        } else {
            Session::flash('error', __('label.FACTOR_CLASSIFICATION_COULD_NOT_BE_CREATED'));
            return redirect('factorClassification/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = FactorClassification::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('factorClassification');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('factorClassification.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = FactorClassification::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
        );

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'order' => 'required|not_in:0'
                        ], $messages);

        if ($validator->fails()) {
            return redirect('factorClassification/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->short_info = $request->short_info;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.FACTOR_CLASSIFICATION_UPDATED_SUCCESSFULLY'));
            return redirect('/factorClassification' . $pageNumber);
        } else {
            Session::flash('error', trans('label.FACTOR_CLASSIFICATION_COULD_NOT_BE_UPDATED'));
            return redirect('factorClassification/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = FactorClassification::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.FACTOR_CLASSIFICATION_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.FACTOR_CLASSIFICATION_COULD_NOT_BE_DELETED'));
        }
        return redirect('factorClassification' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('factorClassification?' . $url);
    }

}
