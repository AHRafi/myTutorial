<?php

namespace App\Http\Controllers;

use Validator;
use App\CorpsRegtBr;
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

class CorpsRegtBrController extends Controller {

    private $controller = 'CorpsRegtBr';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = CorpsRegtBr::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = CorpsRegtBr::select('id', 'name', 'code', 'order', 'status', 'organization_id')
                ->orderBy('order', 'asc');
        $organizationList = Common::getOrganizationType(2);

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('code', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/corpsRegtBr?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $corpsRegtBrCode = CorpsRegtBr::select('code')->where('id', Auth::user()->corpsRegtBr_id)->first();
            $pdf = PDF::loadView('printCorpsRegtBr', compact('targetArr', 'corpsRegtBrCode'))->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('corpsRegtBrList.pdf');
        } else {
            return view('corpsRegtBr.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'organizationList'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        $organizationList = ['0' => __('label.SELECT_ORGANIZATION_OPT')] + Common::getOrganizationType(2);

        return view('corpsRegtBr.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'organizationList'));
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
                    'name' => 'required|unique:corps_regt_br',
                    'code' => 'required|unique:corps_regt_br',
                    'order' => 'required|not_in:0',
                    'organization_id' => 'required|not_in:0'
                        ], $messages);


        if ($validator->fails()) {
            return redirect('corpsRegtBr/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new CorpsRegtBr;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->organization_id = $request->organization_id;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.CORPS_REGT_BR_CREATED_SUCCESSFULLY'));
            return redirect('corpsRegtBr');
        } else {
            Session::flash('error', __('label.CORPS_REGT_BR_COULD_NOT_BE_CREATED'));
            return redirect('corpsRegtBr/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = CorpsRegtBr::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);
        $organizationList = ['0' => __('label.SELECT_ORGANIZATION_OPT')] + Common::getOrganizationType(2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('corpsRegtBr');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('corpsRegtBr.edit')->with(compact('target', 'qpArr', 'orderList', 'organizationList'));
    }

    public function update(Request $request, $id) {
        $target = CorpsRegtBr::find($id);
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
                    'name' => 'required|unique:corps_regt_br,name,'.$id,
                    'code' => 'required|unique:corps_regt_br,code,'.$id,
                    'order' => 'required|not_in:0',
                    'organization_id' => 'required|not_in:0'
                        ], $messages);

        if ($validator->fails()) {
            return redirect('corpsRegtBr/' . $id . '/edit' . $pageNumber)
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
            Session::flash('success', trans('label.CORPS_REGT_BR_UPDATED_SUCCESSFULLY'));
            return redirect('/corpsRegtBr' . $pageNumber);
        } else {
            Session::flash('error', trans('label.CORPS_REGT_BR_COULD_NOT_BE_UPDATED'));
            return redirect('corpsRegtBr/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = CorpsRegtBr::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
//        //Check Dependency before deletion
//        $dependencyArr = ['User' => 'corpsRegtBr_id'];
//
//        foreach ($dependencyArr as $model => $key) {
//            $namespacedModel = '\\App\\' . $model;
//            $dependentData = $namespacedModel::where($key, $id)->first();
//            if (!empty($dependentData)) {
//                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
//                return redirect('corpsRegtBr' . $pageNumber);
//            }
//        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.CORPS_REGT_BR_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.CORPS_REGT_BR_COULD_NOT_BE_DELETED'));
        }
        return redirect('corpsRegtBr' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('corpsRegtBr?' . $url);
    }

}
