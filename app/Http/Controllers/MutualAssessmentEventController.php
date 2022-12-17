<?php

namespace App\Http\Controllers;

use Validator;
use App\MutualAssessmentEvent;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Illuminate\Http\Request;

class MutualAssessmentEventController extends Controller {

    private $controller = 'MutualAssessmentEvent';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = MutualAssessmentEvent::select('name')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = MutualAssessmentEvent::select('mutual_assessment_event.id', 'mutual_assessment_event.name'
                        , 'mutual_assessment_event.name', 'mutual_assessment_event.status')
                ->orderBy('mutual_assessment_event.name', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('mutual_assessment_event.name', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/mutualAssessmentFactor?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $pdf = PDF::loadView('mutualAssessmentEvent.printEvent', compact('targetArr', 'qpArr', 'nameArr'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('mutualAssessmentEventList.pdf');
        } else {
            return view('mutualAssessmentEvent.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('mutualAssessmentEvent.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:mutual_assessment_event',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('mutualAssessmentFactor/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new MutualAssessmentEvent;
        $target->name = $request->name;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.MUTUAL_ASSESSMENT_EVENT_CREATED_SUCCESSFULLY'));
            return redirect('mutualAssessmentFactor');
        } else {
            Session::flash('error', __('label.MUTUAL_ASSESSMENT_EVENT_COULD_NOT_BE_CREATED'));
            return redirect('mutualAssessmentFactor/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = MutualAssessmentEvent::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('mutualAssessmentFactor');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('mutualAssessmentEvent.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = MutualAssessmentEvent::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:mutual_assessment_event,name,' . $id,
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('mutualAssessmentFactor/' . $id . '/edit' . $pageNumber)
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
            Session::flash('success', trans('label.MUTUAL_ASSESSMENT_EVENT_UPDATED_SUCCESSFULLY'));
            return redirect('/mutualAssessmentFactor' . $pageNumber);
        } else {
            Session::flash('error', trans('label.MUTUAL_ASSESSMENT_EVENT_CUOLD_NOT_BE_UPDATED'));
            return redirect('mutualAssessmentFactor/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = MutualAssessmentEvent::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        //Check Dependency before deletion
        $dependencyArr = [
            'MaMksExport' => 'factor_id',
            'MutualAssessmentMarking' => 'factor_id',
//            , 'EventMarkingLock' => 'syndicate_id', 'Marking' => 'syndicate_id'
//            , 'ParticularMarkingLock' => 'syndicate_id', 'SyndicateToBatch' => 'syndicate_id', 'PlCmdrToSyndicate' => 'syndicate_id'
//            , 'RctState' => 'syndicate_id', 'RecruitToSyndicate' => 'syndicate_id'
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('mutualAssessmentFactor' . $pageNumber);
            }
        }


        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.MUTUAL_ASSESSMENT_EVENT_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.MUTUAL_ASSESSMENT_EVENT_COULD_NOT_BE_DELETED'));
        }
        return redirect('mutualAssessmentFactor' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('mutualAssessmentFactor?' . $url);
    }

}
