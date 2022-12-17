<?php

namespace App\Http\Controllers;

use Validator;
use App\Event;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\EventToSubSubSubEvent;
use App\TermToEvent;
use App\TrainingYear;
use App\TermToSubEvent;
use App\Course;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Illuminate\Http\Request;

class EventController extends Controller {

    private $controller = 'Event';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Event::select('event_code')->orderBy('event_code', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $courseList = Course::orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')->toArray();

        $activeCourseInfo = Course::where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        if (empty($request->fil_course_id)) {
            $request->fil_course_id = !empty($activeCourseInfo->id) ? $activeCourseInfo->id : 0;
        }

        $targetArr = Event::join('course', 'course.id', '=', 'event.course_id')
                ->select('event.id', 'event.event_code', 'event.event_detail', 'event.has_sub_event'
                        , 'event.has_ds_assesment', 'event.has_group_cloning', 'event.order', 'event.status'
                        , 'event.for_ma_grouping', 'course.name as course_name')
                ->where('event.course_id', '=', $request->fil_course_id)
                ->orderBy('course.id', 'desc')
                ->orderBy('event.status', 'asc')
                ->orderBy('event.event_code', 'asc');



        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('event.event_code', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/event?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $pdf = PDF::loadView('event.printEvent', compact('targetArr', 'qpArr', 'nameArr'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('eventList.pdf');
        } else {
            return view('event.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'courseList'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        $activeTrainingYear = TrainingYear::select('id', 'name')->where('status', '1')->first();
        if (empty($activeTrainingYear)) {
            $void['header'] = __('label.CREATE_CM');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYear->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.CREATE_CM');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        return view('event.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'activeCourse'));
    }

    public function store(Request $request) {
//        echo '<pre>';        print_r($request->all());exit();
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'event_code' => 'required|unique:event',
                    'event_detail' => 'required',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('event/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Event;
        $target->course_id = $request->course_id;
        $target->event_code = $request->event_code;
        $target->event_detail = $request->event_detail;
        if (!empty($request->has_sub_event)) {
            $target->has_sub_event = $request->has_sub_event ?? '0';
        }
        if (!empty($request->has_ds_assesment)) {
            $target->has_ds_assesment = $request->has_ds_assesment ?? '0';
        }
        if (!empty($request->has_group_cloning)) {
            $target->has_group_cloning = $request->has_group_cloning ?? '0';
        }
        $target->for_ma_grouping = !empty($request->for_ma_grouping) ? $request->for_ma_grouping : '0';
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper::insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.EVENT_CREATED_SUCCESSFULLY'));
            return redirect('event');
        } else {
            Session::flash('error', __('label.EVENT_COULD_NOT_BE_CREATED'));
            return redirect('event/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Event::find($id);


        $eventActiveCourse = Event::join('course', 'course.id', 'event.course_id')
                        ->select('course.name as course', 'event.course_id')
                        ->where('event.id', $id)->first();

//        echo '<pre>';        print_r($target->toArray()); exit;
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('event');
        }

        //passing param for custom function
        $qpArr = $request->all();

        $eventToSubEvent = EventToSubEvent::where('event_id', $id)->where('has_ds_assesment', '1')->first();
        $eventToSubSubEvent = EventToSubSubEvent::where('event_id', $id)->where('has_ds_assesment', '1')->first();

        $termToSubEvent = TermToSubEvent::where('event_id', $id)->first();

        $eventChild = !empty($eventToSubEvent) || !empty($eventToSubSubEvent) ? 1 : 0;
        $eventTerm = !empty($termToSubEvent) ? 1 : 0;

        return view('event.edit')->with(compact('target', 'qpArr', 'orderList', 'eventChild', 'eventTerm', 'eventActiveCourse'));
    }

    public function update(Request $request, $id) {

//        echo '<pre>';        print_r($request->all()); exit;
        $target = Event::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $courseId = $request->course_id;
        $validator = Validator::make($request->all(), [
                    'event_code' => 'required|unique:event,event_code,' . $id . ',id,course_id,' . $courseId,
                    'event_detail' => 'required',
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('event/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->course_id = $request->course_id;
        $target->event_code = $request->event_code;
        $target->event_detail = $request->event_detail;
        if (!empty($request->has_sub_event)) {
            $target->has_sub_event = $request->has_sub_event;
        } else {
            $target->has_sub_event = '0';
        }

        if (!empty($request->has_ds_assesment)) {
            $target->has_ds_assesment = $request->has_ds_assesment;
        } else {
            $target->has_ds_assesment = '0';
        }

        if (!empty($request->has_group_cloning)) {
            $target->has_group_cloning = $request->has_group_cloning;
        } else {
            $target->has_group_cloning = '0';
        }

        $target->for_ma_grouping = !empty($request->for_ma_grouping) ? $request->for_ma_grouping : '0';

        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            if (!empty($request->has_ds_assesment)) {
                EventToSubEvent::where('event_id', $id)
                        ->update(['has_ds_assesment' => '0']);

                EventToSubSubEvent::where('event_id', $id)
                        ->update(['has_ds_assesment' => '0']);

                EventToSubSubSubEvent::where('event_id', $id)
                        ->update(['has_ds_assesment' => '0']);
            }
            Session::flash('success', trans('label.EVENT_UPDATED_SUCCESSFULLY'));
            return redirect('/event' . $pageNumber);
        } else {
            Session::flash('error', trans('label.EVENT_CUOLD_NOT_BE_UPDATED'));
            return redirect('event/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Event::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

//Check Dependency before deletion
        $dependencyArr = [
            'EventToSubEvent' => 'event_id',
            'TermToEvent' => 'event_id',
//            , 'EventMarkingLock' => 'syndicate_id', 'Marking' => 'syndicate_id'
//            , 'ParticularMarkingLock' => 'syndicate_id', 'SyndicateToBatch' => 'syndicate_id', 'PlCmdrToSyndicate' => 'syndicate_id'
//            , 'RctState' => 'syndicate_id', 'RecruitToSyndicate' => 'syndicate_id'
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('event?page=' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.EVENT_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.EVENT_COULD_NOT_BE_DELETED'));
        }
        return redirect('event?page=' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fil_course_id=' . $request->fil_course_id;
        return Redirect::to('event?' . $url);
    }

}
