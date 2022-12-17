<?php

namespace App\Http\Controllers;

use Validator;
use App\CrTrait;
use App\CrPara;
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

class CrTraitController extends Controller {

    private $controller = 'CrTrait';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = CrTrait::select('title')->orderBy('order', 'asc')->get();
        $paraList = array('0' => __('label.SELECT_PARA_OPT')) + CrPara::pluck('title', 'id')->toArray();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = CrTrait::join('cr_para', 'cr_para.id', '=', 'cr_trait.para_id')
                ->select('cr_trait.id', 'cr_trait.title', 'cr_trait.order', 'cr_trait.status'
                        , 'cr_para.title as para', 'cr_trait.for_grading_sentence'
                        , 'cr_trait.for_recomnd_sentence')
                ->orderBy('cr_trait.order', 'asc');
        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('cr_trait.title', 'LIKE', '%' . $searchText . '%');
            });
        }
        if (!empty($request->fil_para_id)) {
            $targetArr = $targetArr->where('cr_trait.para_id', '=', $request->fil_para_id);
        }


        //end filtering

        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));


        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/crTrait?page=' . $page);
        }


        return view('crSetup.trait.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'paraList'));
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        $paraList = array('0' => __('label.SELECT_PARA_OPT')) + CrPara::pluck('title', 'id')->toArray();

        return view('crSetup.trait.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'paraList'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $messages = array();
        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'para_id' => 'required|not_in:0',
                    'order' => 'required|not_in:0'
                        ], $messages);


        if ($validator->fails()) {
            return redirect('crTrait/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new CrTrait;
        $target->title = $request->title;
        $target->para_id = $request->para_id;
        $target->for_grading_sentence = !empty($request->for_grading_sentence) ? $request->for_grading_sentence : '0';
        $target->for_recomnd_sentence = !empty($request->for_recomnd_sentence) ? $request->for_recomnd_sentence : '0';
        $target->order = 0;
        $target->status = $request->status;

        $updateMarkerArr = [];

        if (!empty($request->for_grading_sentence)) {
            $updateMarkerArr['for_grading_sentence'] = '0';
        }
        if (!empty($request->for_recomnd_sentence)) {
            $updateMarkerArr['for_recomnd_sentence'] = '0';
        }

        if ($target->save()) {
            if (!empty($updateMarkerArr)) {
                CrTrait::where('id', '<>', $target->id)->update($updateMarkerArr);
            }
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.TRAIT_CREATED_SUCCESSFULLY'));
            return redirect('crTrait');
        } else {
            Session::flash('error', __('label.TRAIT_COULD_NOT_BE_CREATED'));
            return redirect('crTrait/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = CrTrait::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('crTrait');
        }

        //passing param for custom function
        $qpArr = $request->all();
        $paraList = array('0' => __('label.SELECT_PARA_OPT')) + CrPara::pluck('title', 'id')->toArray();

        return view('crSetup.trait.edit')->with(compact('target', 'qpArr', 'orderList', 'paraList'));
    }

    public function update(Request $request, $id) {
        $target = CrTrait::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array();

        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'para_id' => 'required|not_in:0',
                    'order' => 'required|not_in:0'
                        ], $messages);

        if ($validator->fails()) {
            return redirect('crTrait/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->title = $request->title;
        $target->para_id = $request->para_id;
        $target->for_grading_sentence = !empty($request->for_grading_sentence) ? $request->for_grading_sentence : '0';
        $target->for_recomnd_sentence = !empty($request->for_recomnd_sentence) ? $request->for_recomnd_sentence : '0';
        $target->order = $request->order;
        $target->status = $request->status;

        $updateMarkerArr = [];

        if (!empty($request->for_grading_sentence)) {
            $updateMarkerArr['for_grading_sentence'] = '0';
        }
        if (!empty($request->for_recomnd_sentence)) {
            $updateMarkerArr['for_recomnd_sentence'] = '0';
        }

        if ($target->save()) {
            if (!empty($updateMarkerArr)) {
                CrTrait::where('id', '<>', $target->id)->update($updateMarkerArr);
            }
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.TRAIT_UPDATED_SUCCESSFULLY'));
            return redirect('/crTrait' . $pageNumber);
        } else {
            Session::flash('error', trans('label.TRAIT_CUOLD_NOT_BE_UPDATED'));
            return redirect('crTrait/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = CrTrait::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        //START:: Check Dependency before deletion
        $dependencyArr = [
            'CrSentenceToTrait' => 'trait_id',
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('crTrait' . $pageNumber);
            }
        }
        //END:: Check Dependency before deletion

        if ($target->delete()) {
            Helper::deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.TRAIT_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.TRAIT_COULD_NOT_BE_DELETED'));
        }
        return redirect('crTrait' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fil_para_id=' . $request->fil_para_id;
        return Redirect::to('crTrait?' . $url);
    }

}
