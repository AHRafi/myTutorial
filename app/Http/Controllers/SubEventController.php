<?php

namespace App\Http\Controllers;

use Validator;
use App\SubEvent;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Response;
use Illuminate\Http\Request;

class SubEventController extends Controller {

    private $controller = 'SubEvent';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $hiddenOptionList = ['1' => __('label.HIDE'), '2' => __('label.SHOW')];

        $nameArr = SubEvent::select('event_code')->orderBy('event_code', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = SubEvent::select('sub_event.id', 'sub_event.event_code', 'sub_event.event_detail', 'sub_event.status'
                , 'sub_event.order', 'sub_event.hidden')
                ->orderBy('sub_event.status', 'asc')
                ->orderBy('sub_event.event_code', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('sub_event.event_code', 'LIKE', '%' . $searchText . '%');
            });
        }
        //end filtering
        
        if(empty($request->hidden) || (!empty($request->hidden) && $request->hidden == '1')){
            $targetArr = $targetArr->where('sub_event.hidden',  '0');   
        }
        

        if ($request->download == 'pdf') {
            $targetArr = $targetArr->get();
        } else {
            $targetArr = $targetArr->paginate(Session::get('paginatorCount'));
        }

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/subEvent?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $pdf = PDF::loadView('sub_event.printSubEvent', compact('targetArr', 'qpArr', 'nameArr'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('subEventList.pdf');
        } else {
            return view('subEvent.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'hiddenOptionList'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('subEvent.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'event_code' => 'required|unique:sub_event',
                    'event_detail' => 'required',
                    'order' => 'required|not_in:0'
                        ], [
                    'event_code.required' => __('label.THE_SUB_EVENT_CODE_FIELD_IS_REQUIRED'),
                    'event_code.unique' => __('label.THE_SUB_EVENT_CODE_HAS_ALREADY_BEEN_TAKEN'),
                    'event_detail.required' => __('label.THE_SUB_EVENT_DETAIL_FIELD_IS_REQUIRED'),
        ]);


        if ($validator->fails()) {
            return redirect('subEvent/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new SubEvent;
        $target->event_code = $request->event_code;
        $target->event_detail = $request->event_detail;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.SUB_EVENT_CREATED_SUCCESSFULLY'));
            return redirect('subEvent');
        } else {
            Session::flash('error', __('label.SUB_EVENT_COULD_NOT_BE_CREATED'));
            return redirect('subEvent/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = SubEvent::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('subEvent');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('subEvent.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = SubEvent::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'event_code' => 'required|unique:sub_event,event_code,' . $id,
                    'event_detail' => 'required',
                    'order' => 'required|not_in:0'
                        ], [
                    'event_code.required' => __('label.THE_SUB_EVENT_CODE_FIELD_IS_REQUIRED'),
                    'event_code.unique' => __('label.THE_SUB_EVENT_CODE_HAS_ALREADY_BEEN_TAKEN'),
                    'event_detail.required' => __('label.THE_SUB_EVENT_DETAIL_FIELD_IS_REQUIRED'),
        ]);

        if ($validator->fails()) {
            return redirect('subEvent/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        if (!empty($request->for_entrance)) {
            $forEntrance = '1';
            $previousEntrance = SubEvent::where('for_entrance', '1')->first();
        } else {
            $forEntrance = '0';
        }

        $target->event_code = $request->event_code;
        $target->event_detail = $request->event_detail;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }

            Session::flash('success', trans('label.SUB_EVENT_UPDATED_SUCCESSFULLY'));
            return redirect('/subEvent' . $pageNumber);
        } else {
            Session::flash('error', trans('label.SUB_EVENT_CUOLD_NOT_BE_UPDATED'));
            return redirect('subEvent/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = SubEvent::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

//Check Dependency before deletion
        $dependencyArr = [
            'EventToSubEvent' => 'sub_event_id'
//            , 'SubEventMarkingLock' => 'syndicate_id', 'Marking' => 'syndicate_id'
//            , 'ParticularMarkingLock' => 'syndicate_id', 'SyndicateToBatch' => 'syndicate_id', 'PlCmdrToSyndicate' => 'syndicate_id'
//            , 'RctState' => 'syndicate_id', 'RecruitToSyndicate' => 'syndicate_id'
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('subEvent?page=' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.SUB_EVENT_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.SUB_EVENT_COULD_NOT_BE_DELETED'));
        }
        return redirect('subEvent?page=' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&hidden='.$request->hidden; 
        return Redirect::to('subEvent?' . $url);
    }

    public function hideShow(Request $request) {

        $target = SubEvent::find($request->id);
        if (empty($target)) {
            return Response::json(array('success' => false, 'message' => __('label.INVALID_DATA_ID')), 401);
        }
        $target->hidden = !empty($request->status) ? $request->status : '0';
        
        $hideShow = !empty($request->status) ? 'hidden' : 'visible';
        $sucessMsg = __('label.RECORD_IS_SET_AS_HIDDEN_VISIBLE', ['name' => $target->event_code, 'hide_show' => $hideShow]);
        $errorMsg = __('label.FAILED_TO_SET_RECORD_AS_HIDDEN_VISIBLE', ['name' => $target->event_code, 'hide_show' => $hideShow]);

        if ($target->save()) {
            return Response::json(['success' => true, 'message' => $sucessMsg], 200);
        } else {
            return Response::json(array('success' => false, 'message' => $errorMsg), 401);
        }
    }

}
