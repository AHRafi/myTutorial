<?php

namespace App\Http\Controllers;

use Validator;
use App\Unit;
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

class UnitController extends Controller {

    private $controller = 'Unit';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Unit::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Unit::select('unit.id', 'unit.name', 'unit.code', 'unit.order', 'unit.status', 'organization_id')
                ->orderBy('unit.order', 'asc');
        $organizationList = Common::getOrganizationType(1);

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('unit.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('unit.code', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/unit?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $unitCode = Unit::select('code')->where('id', Auth::user()->unit_id)->first();
            $pdf = PDF::loadView('unit.printUnit', compact('targetArr', 'unitCode'))->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('unitList.pdf');
        } else {
            return view('unit.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'organizationList'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        $organizationList = ['0' => __('label.SELECT_ORGANIZATION_OPT')] + Common::getOrganizationType(1);

        return view('unit.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'organizationList'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Name field is required.',
            'code.unique' => 'The Short name has already been taken.',
        );

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:unit',
                    'code' => 'required|unique:unit',
                    'order' => 'required|not_in:0',
                    'organization_id' => 'required|not_in:0'
                        ], $messages);


        if ($validator->fails()) {
            return redirect('unit/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Unit;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->organization_id = $request->organization_id;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.UNIT_FMN_INST_CREATED_SUCCESSFULLY'));
            return redirect('unit');
        } else {
            Session::flash('error', __('label.UNIT_FMN_INST_COULD_NOT_BE_CREATED'));
            return redirect('unit/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Unit::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);
        $organizationList = ['0' => __('label.SELECT_ORGANIZATION_OPT')] + Common::getOrganizationType(1);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('unit');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('unit.edit')->with(compact('target', 'qpArr', 'orderList', 'organizationList'));
    }

    public function update(Request $request, $id) {
        $target = Unit::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Name field is required.',
            'code.unique' => 'The Short name has already been taken.',
        );

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:unit,name,'.$id,
                    'code' => 'required|unique:unit,code,'.$id,
                    'order' => 'required|not_in:0',
                    'organization_id' => 'required|not_in:0'
                        ], $messages);

        if ($validator->fails()) {
            return redirect('unit/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->code = $request->code;
        $target->organization_id = $request->organization_id;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.UNIT_FMN_INST_UPDATED_SUCCESSFULLY'));
            return redirect('/unit' . $pageNumber);
        } else {
            Session::flash('error', trans('label.UNIT_FMN_INST_COULD_NOT_BE_UPDATED'));
            return redirect('unit/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Unit::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
//        //Check Dependency before deletion
//        $dependencyArr = ['User' => 'unit_id'];
//
//        foreach ($dependencyArr as $model => $key) {
//            $namespacedModel = '\\App\\' . $model;
//            $dependentData = $namespacedModel::where($key, $id)->first();
//            if (!empty($dependentData)) {
//                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
//                return redirect('unit' . $pageNumber);
//            }
//        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.UNIT_FMN_INST_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.UNIT_FMN_INST_COULD_NOT_BE_DELETED'));
        }
        return redirect('unit' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('unit?' . $url);
    }

}
