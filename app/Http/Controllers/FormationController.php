<?php

namespace App\Http\Controllers;

use Validator;
use App\Formation;
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

class FormationController extends Controller {

    private $controller = 'Formation';

    public function __construct() {

    }

    public function index(Request $request) {

        $nameArr = Formation::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Formation::select('formation.id', 'formation.name', 'formation.code', 'formation.order', 'formation.status')
                ->orderBy('formation.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('formation.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('formation.code', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/formation?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $formationCode = Formation::select('code')->where('id', Auth::user()->formation_id)->first();
            $pdf = PDF::loadView('formation.printFormation', compact('targetArr', 'formationCode'))->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('formationList.pdf');
        } else {
            return view('formation.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('formation.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'code' => 'required',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('formation/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Formation;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.APPOINTMENT_CREATED_SUCCESSFULLY'));
            return redirect('formation');
        } else {
            Session::flash('error', __('label.APPOINTMENT_COULD_NOT_BE_CREATED'));
            return redirect('formation/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Formation::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('formation');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('formation.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = Formation::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'code' => 'required',
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('formation/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.APPOINTMENT_UPDATED_SUCCESSFULLY'));
            return redirect('/formation' . $pageNumber);
        } else {
            Session::flash('error', trans('label.APPOINTMENT_CUOLD_NOT_BE_UPDATED'));
            return redirect('formation/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Formation::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '?page=';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
//        //Check Dependency before deletion
//        $dependencyArr = ['User' => 'formation_id'];
//
//        foreach ($dependencyArr as $model => $key) {
//            $namespacedModel = '\\App\\' . $model;
//            $dependentData = $namespacedModel::where($key, $id)->first();
//            if (!empty($dependentData)) {
//                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
//                return redirect('formation' . $pageNumber);
//            }
//        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.FORMATION_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.FORMATION_COULD_NOT_BE_DELETED'));
        }
        return redirect('formation' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('formation?' . $url);
    }

}
