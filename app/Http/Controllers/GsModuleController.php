<?php

namespace App\Http\Controllers;

use App\GsModule;
use Session;
use Redirect;
use Helper;
use Validator;
use Response;
use App;
use App\CoreCurriculum;
use View;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;

class GsModuleController extends Controller {

    private $controller = 'GsModule';

    public function index(Request $request) {
        $nameArr = GsModule::select('name')->orderBy('order', 'asc')->get();
        $coreCurriculumList = ['0' => __('label.SELECT_CORE_CUR_OPT')] + CoreCurriculum::pluck('title','id')->toArray();
        $qpArr = $request->all();
        $targetArr = GsModule::join('core_curriculum','core_curriculum.id','gs_module.core_curriculum_id')
                            ->select('gs_module.id','gs_module.name','gs_module.order', 'gs_module.status','core_curriculum.title as core_curriculum')
                            ->orderBy('gs_module.order', 'asc');
        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('gs_module.name', 'LIKE', '%' . $searchText . '%');
            });
        }
        if (!empty($request->fil_core_curriculum_id)) {
            $targetArr = $targetArr->where('gs_module.core_curriculum_id', $request->fil_core_curriculum_id);
        }


        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));



        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/gsmodule?page=' . $page);
        }


        return view('gsmodule.index')->with(compact('qpArr','targetArr','nameArr', 'coreCurriculumList'));

    }

    public function create(Request $request) {
        //passing param for custom function
        $coreCurriculumList = ['0' => __('label.SELECT_CORE_CUR_OPT')] + CoreCurriculum::pluck('title','id')->toArray();
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('gsmodule.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber','coreCurriculumList'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();


        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'core_curriculum_id' => 'required|not_in:0',
                    'order' => 'required|not_in:0',
        ]);


        if ($validator->fails()) {
            return redirect('gsmodule/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new GsModule;
        $target->name = $request->name;
        $target->core_curriculum_id = $request->core_curriculum_id;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.MODULE_CREATED_SUCCESSFULLY'));
            return redirect('gsmodule');
        } else {
            Session::flash('error', __('label.MODULE_COULD_NOT_BE_CREATED'));
            return redirect('gsmodule/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = GsModule::find($id);
        $coreCurriculumList = ['0' => __('label.SELECT_CORE_CUR_OPT')] + CoreCurriculum::pluck('title','id')->toArray();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('gsmodule');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('gsmodule.edit')->with(compact('target', 'qpArr', 'orderList','coreCurriculumList'));
    }

    public function update(Request $request, $id) {

        $target = GsModule::find($id);
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
            return redirect('gsmodule/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->core_curriculum_id = $request->core_curriculum_id;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.MODULE_UPDATED_SUCCESSFULLY'));
            return redirect('/gsmodule' . $pageNumber);
        } else {
            Session::flash('error', trans('label.MODULE_CUOLD_NOT_BE_UPDATED'));
            return redirect('gsmodule/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = GsModule::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }


        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.MODULE_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.MODULE_COULD_NOT_BE_DELETED'));
        }
        return redirect('gsmodule' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search)
                .'&fil_core_curriculum_id=' . $request->fil_core_curriculum_id;
        return Redirect::to('gsmodule?' . $url);
    }


}
