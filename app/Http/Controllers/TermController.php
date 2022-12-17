<?php

namespace App\Http\Controllers;

use Validator;
use App\Term;
use Redirect;
use Session;
use Helper;
use Illuminate\Http\Request;

class TermController extends Controller {

    private $controller = 'Term';

    public function index(Request $request) {

        $nameArr = Term::select('name')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Term::select('term.id', 'term.name', 'term.order', 'term.status')->orderBy('order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('term.name', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/term?page=' . $page);
        }


        return view('term.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
    }

    public function create(Request $request) { //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        return view('term.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:term',
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('term/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target = new Term;
        $target->name = $request->name;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.TERM_CREATED_SUCCESSFULLY'));
            return redirect('term');
        } else {
            Session::flash('error', __('label.TERM_COULD_NOT_BE_CREATED'));
            return redirect('term/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Term::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);
        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('term');
        }

        //passing param for custom function
        $qpArr = $request->all();
        return view('term.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = Term::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:term,name,' . $id,
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('term/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', __('label.TERM_UPDATED_SUCCESSFULLY'));
            return redirect('term' . $pageNumber);
        } else {
            Session::flash('error', __('label.TERM_COULD_NOT_BE_UPDATED'));
            return redirect('term/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Term::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        $dependencyArr = [
            'TermToCourse' => 'term_id',
//            'TermToEvent' => 'term_id', 'TermToParticular' => 'term_id'
//            , 'EventMarkingLock' => 'term_id', 'ParticularMarkingLock' => 'term_id'
//            , 'Marking' => 'term_id', 'PlCmdrToPlatoon' => 'term_id', 'RecruitToPlatoon' => 'term_id'
        ];
        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('term' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.TERM_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.TERM_COULD_NOT_BE_DELETED'));
        }
        return redirect('term' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('term?' . $url);
    }

}
