<?php

namespace App\Http\Controllers;

use App\GsGrading;
use Session;
use Redirect;
use Helper;
use Validator;
use Response;
use App;
use View;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;

class GsGradingController extends Controller {

    private $controller = 'GsGrading';

    public function index(Request $request) {
        $nameArr = GsGrading::select('title')->orderBy('order', 'asc')->get();
        $qpArr = $request->all();
        $targetArr = GsGrading::select('id', 'title','description', 'order', 'wt', 'status')
                ->orderBy('gs_grading.order', 'asc');
        //begin filtering

        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('title', 'LIKE', '%' . $searchText . '%');
            });
        }

        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/gsgrading?page=' . $page);
        }


        return view('gsgrading.index')->with(compact('qpArr', 'targetArr', 'nameArr'));
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('gsgrading.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();

        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'order' => 'required|not_in:0',
                    'wt' => 'required',
                    'description' => 'required',
        ]);


        if ($validator->fails()) {
            return redirect('gsgrading/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new GsGrading;
        $target->title = $request->title;
        $target->order = 0;
        $target->wt = $request->wt;
        $target->description = $request->description;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.GRADE_CREATED_SUCCESSFULLY'));
            return redirect('gsgrading');
        } else {
            Session::flash('error', __('label.GRADE_COULD_NOT_BE_CREATED'));
            return redirect('gsgrading/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = GsGrading::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('gsgrading');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('gsgrading.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = GsGrading::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'order' => 'required|not_in:0',
                    'wt' => 'required',
        ]);


        if ($validator->fails()) {
            return redirect('gsgrading/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->title = $request->title;
        $target->order = $request->order;
        $target->wt = $request->wt;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.GRADE_UPDATED_SUCCESSFULLY'));
            return redirect('/gsgrading' . $pageNumber);
        } else {
            Session::flash('error', trans('label.GRADE_CUOLD_NOT_BE_UPDATED'));
            return redirect('gsgrading/' . $id . '/edit' . $pageNumber);
        }
    }

     public function destroy(Request $request, $id) {
        $target = GsGrading::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }


        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.GRADE_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.  '));
        }
        return redirect('gsgrading' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('gsgrading?' . $url);
    }

}
