<?php

namespace App\Http\Controllers;

use Validator;
use App\GradingSystem;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Illuminate\Http\Request;

class GradingSystemController extends Controller {

    private $controller = 'GradingSystem';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = GradingSystem::select('grade_name')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = GradingSystem::select('grading_system.id', 'grading_system.marks_from', 'grading_system.marks_to'
                , 'grading_system.grade_name' , 'grading_system.order', 'grading_system.status')
                ->orderBy('grading_system.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('grading_system.grade_name', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/gradingSystem?page=' . $page);
        } else {
            return view('gradingSystem.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('gradingSystem.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'start_range' => 'required',
                    'end_range' => 'required|gt:start_range',
                    'grade_name' => 'required',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('gradingSystem/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new GradingSystem;
        $target->marks_from = $request->start_range;
        $target->marks_to = $request->end_range;
        $target->grade_name = $request->grade_name;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.GRADE_CREATED_SUCCESSFULLY'));
            return redirect('gradingSystem');
        } else {
            Session::flash('error', __('label.GRADE_COULD_NOT_BE_CREATED'));
            return redirect('gradingSystem/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = GradingSystem::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('gradingSystem');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('gradingSystem.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = GradingSystem::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'start_range' => 'required',
                    'end_range' => 'required|gt:start_range',
                    'grade_name' => 'required',
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('gradingSystem/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->marks_from = $request->start_range;
        $target->marks_to = $request->end_range;
        $target->grade_name = $request->grade_name;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.GRADE_UPDATED_SUCCESSFULLY'));
            return redirect('/gradingSystem' . $pageNumber);
        } else {
            Session::flash('error', trans('label.GRADE_CUOLD_NOT_BE_UPDATED'));
            return redirect('gradingSystem/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = GradingSystem::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        //Check Dependency before deletion
        $dependencyArr = [
            'EventAssessmentMarking' => 'grade_id',
//            , 'EventMarkingLock' => 'syndicate_id', 'Marking' => 'syndicate_id'
//            , 'ParticularMarkingLock' => 'syndicate_id', 'SyndicateToBatch' => 'syndicate_id', 'PlCmdrToSyndicate' => 'syndicate_id'
//            , 'RctState' => 'syndicate_id', 'RecruitToSyndicate' => 'syndicate_id'
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('gradingSystem' . $pageNumber);
            }
        }


        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.GRADE_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.GRADE_COULD_NOT_BE_DELETED'));
        }
        return redirect('gradingSystem' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('gradingSystem?' . $url);
    }

}
