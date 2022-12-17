<?php

namespace App\Http\Controllers;

use Validator;
use App\Wing;
use App\Rank;
use Redirect;
use Session;
use Helper;
use Auth;
use Illuminate\Http\Request;

class WingController extends Controller {

    private $controller = 'Wing';

    public function index(Request $request) {

        $nameArr = Wing::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Wing::select('wing.id', 'wing.name', 'wing.code', 'wing.order', 'wing.status')->orderBy('order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('wing.code', 'LIKE', '%' . $searchText . '%');
            });
        }
        //end filtering


        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/wing?page=' . $page);
        }

        return view('wing.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
    }

    public function create(Request $request) { //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        return view('wing.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Name field is required.',
        );
        
        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:wing',
                    'code' => 'required|unique:wing',
                    'order' => 'required|not_in:0'
        ], $messages);

        if ($validator->fails()) {
            return redirect('wing/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target = new Wing;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.WING_CREATED_SUCCESSFULLY'));
            return redirect('wing');
        } else {
            Session::flash('error', __('label.WING_COULD_NOT_BE_CREATED'));
            return redirect('wing/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Wing::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);
        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('wing');
        }

        //passing param for custom function
        $qpArr = $request->all();
        return view('wing.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = Wing::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Name field is required.',
        );
        
        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:wing,name,' . $id,
                    'code' => 'required|unique:wing,code,' . $id,
                    'order' => 'required|not_in:0'
        ], $messages);

        if ($validator->fails()) {
            return redirect('wing/' . $id . '/edit' . $pageNumber)
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
            Session::flash('success', __('label.WING_UPDATED_SUCCESSFULLY'));
            return redirect('wing' . $pageNumber);
        } else {
            Session::flash('error', __('label.WING_COULD_NOT_BE_UPDATED'));
            return redirect('wing/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Wing::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        //START:: Check Dependency before deletion
        $dependencyArr = [
            'User' => 'wing_id'
            , 'CmBasicProfile' => 'wing_id'
            , 'Rank' => 'wing_id'
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('wing' . $pageNumber);
            }
        }
        //END:: Check Dependency before deletion
        
        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.WING_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.WING_COULD_NOT_BE_DELETED'));
        }
        return redirect('wing' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('wing?' . $url);
    }

}
