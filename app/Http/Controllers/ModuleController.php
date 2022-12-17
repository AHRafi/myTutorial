<?php

namespace App\Http\Controllers;

use Validator;
use App\Module;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Response;
use Illuminate\Http\Request;

class ModuleController extends Controller {

    private $controller = 'Module';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Module::select('name')->orderBy('name', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Module::select('module.id', 'module.name', 'module.short_description', 'module.status'
                        , 'module.order')
                ->orderBy('module.name', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('module.name', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/module?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $pdf = PDF::loadView('sub_event.printSubEvent', compact('targetArr', 'qpArr', 'nameArr'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('subEventList.pdf');
        } else {
            return view('module.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('module.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();

        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:module',
                    'order' => 'required|not_in:0'
                        ], [
                    'name.required' => __('label.THE_MODULE_NAME_FIELD_IS_REQUIRED'),
                    'name.unique' => __('label.THE_MODULE_HAS_ALREADY_BEEN_TAKEN'),
        ]);

        if ($validator->fails()) {
            return redirect('module/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Module;
        $target->name = $request->name;
        $target->short_description = $request->short_description;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.MODULE_CREATED_SUCCESSFULLY'));
            return redirect('module');
        } else {
            Session::flash('error', __('label.MODULE_COULD_NOT_BE_CREATED'));
            return redirect('module/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Module::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('module');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('module.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = Module::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:module,name,' . $id,
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('module/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->short_description = $request->short_description;
        $target->order = $request->order;
        ;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }

            Session::flash('success', trans('label.MODULE_UPDATED_SUCCESSFULLY'));
            return redirect('/module' . $pageNumber);
        } else {
            Session::flash('error', trans('label.MODULE_CUOLD_NOT_BE_UPDATED'));
            return redirect('module/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Module::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

//Check Dependency before deletion
        $dependencyArr = [
            'Content' => 'module_id'

        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('module?page=' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.MODULE_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.MODULE_COULD_NOT_BE_DELETED'));
        }
        return redirect('module?page=' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&name=' . $request->name;
        return Redirect::to('module?' . $url);
    }

}
