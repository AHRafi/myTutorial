<?php

namespace App\Http\Controllers;

use Validator;
use App\EventTree;
use App\Event;
use App\SubEvent;
use App\SubSubEvent;
use App\SubSubSubEvent;
use Response;
use Auth;
use Illuminate\Http\Request;

class EventTreeController extends Controller {

    public function index(Request $request) {

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + Event::orderBy('id', 'desc')
                        ->pluck('event_code', 'id')->toArray();

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + SubEvent::where('status', '1')
                        ->orderBy('order', 'desc')->pluck('event_code', 'id')->toArray();

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + SubSubEvent::where('status', '1')
                        ->orderBy('order', 'desc')->pluck('event_code', 'id')->toArray();

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + SubSubSubEvent::where('status', '1')
                        ->orderBy('order', 'desc')->pluck('event_code', 'id')->toArray();



//        $previousDataList = [];
//        if (!empty($previousDataArr)) {
//            foreach ($previousDataArr as $previousData) {
//                $previousDataList[$previousData['has_sub_event']] = $previousData['has_sub_event'];
//                $previousDataList[$previousData['sub_event_id']] = $previousData['sub_event_id'];
//                $previousDataList[$previousData['has_sub_ds_ass_group']] = $previousData['has_sub_ds_ass_group'];
//                $previousDataList[$previousData['has_sub_2_event']] = $previousData['has_sub_2_event'];
//                $previousDataList[$previousData['sub_2_event_id']] = $previousData['sub_2_event_id'];
//                $previousDataList[$previousData['has_sub_2_ds_ass_group']] = $previousData['has_sub_2_ds_ass_group'];
//                $previousDataList[$previousData['has_sub_3_event']] = $previousData['has_sub_3_event'];
//                $previousDataList[$previousData['sub_3_event_id']] = $previousData['sub_3_event_id'];
//                $previousDataList[$previousData['has_sub_3_ds_ass_group']] = $previousData['has_sub_3_ds_ass_group'];
//            }
//        }


        return view('eventTree.index')->with(compact('eventList', 'subEventList', 'subSubEventList', 'subSubSubEventList'));
    }

    public function getPrevEvent(Request $request) {
        $previousDataArr = EventTree::select('event_id', 'has_sub_event', 'sub_event_id', 'has_sub_ds_ass_group'
                                , 'has_sub_2_event', 'sub_2_event_id', 'has_sub_2_ds_ass_group', 'has_sub_3_event'
                                , 'sub_3_event_id', 'has_sub_3_ds_ass_group')
                        ->where('event_id', $request->event_id)->first();


        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + (!empty($previousDataArr->has_sub_event) ? SubEvent::where('status', '1')
                        ->orderBy('order', 'desc')->pluck('event_code', 'id')->toArray() : []);

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + (!empty($previousDataArr->has_sub_2_event) ? SubSubEvent::where('status', '1')
                        ->orderBy('order', 'desc')->pluck('event_code', 'id')->toArray() : []);

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + (!empty($previousDataArr->has_sub_3_event) ? SubSubSubEvent::where('status', '1')
                        ->orderBy('order', 'desc')->pluck('event_code', 'id')->toArray() : []);

//        echo '<pre>';        print_r($subEventList);exit;
//        echo '<pre>';        print_r($previousDataArr->toArray());exit;
        $html = view('eventTree.showPrevEvent', compact('previousDataArr', 'subEventList', 'subSubEventList', 'subSubSubEventList'))->render();
        return response()->json(['html' => $html]);
    }

    public function getSubEvent() {

        $subEventList = ['0' => __('label.SELECT_SUB_EVENT_OPT')] + SubEvent::orderBy('id', 'desc')
                        ->pluck('event_code', 'id')->toArray();

        $html = view('eventTree.showSubEvent', compact('subEventList'))->render();
        return response()->json(['html' => $html]);
    }

    public function getSubSubEvent() {

        $subSubEventList = ['0' => __('label.SELECT_SUB_SUB_EVENT_OPT')] + SubSubEvent::orderBy('id', 'desc')
                        ->pluck('event_code', 'id')->toArray();

        $html = view('eventTree.showSubSubEvent', compact('subSubEventList'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSubSubSubEvent() {

        $subSubSubEventList = ['0' => __('label.SELECT_SUB_SUB_SUB_EVENT_OPT')] + SubSubSubEvent::orderBy('id', 'desc')
                        ->pluck('event_code', 'id')->toArray();

        $html = view('eventTree.showSubSubSubEvent', compact('subSubSubEventList'))->render();

        return response()->json(['html' => $html]);
    }

    public function saveEventTree(Request $request) {
        
        $validator = Validator::make($request->all(), [
                    'event_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('eventTree/index')
                            ->withInput()
                            ->withErrors($validator);
        }

        $target = new EventTree;
        $target->event_id = $request->event_id;
        if (!empty($request->has_sub_event)) {
            $target->has_sub_event = $request->has_sub_event ?? '0';
            $target->sub_event_id = $request->sub_event_id;
            $target->has_sub_ds_ass_group = $request->has_sub_ds_ass_group ?? '0';
            if (!empty($request->has_sub_2_event)) {
                $target->has_sub_2_event = $request->has_sub_2_event ?? '0';
                $target->sub_2_event_id = $request->sub_2_event_id;
                $target->has_sub_2_ds_ass_group = $request->has_sub_2_ds_ass_group ?? '0';
                if (!empty($request->has_sub_3_event)) {
                    $target->has_sub_3_event = $request->has_sub_3_event ?? '0';
                    $target->sub_3_event_id = $request->sub_3_event_id;
                    $target->has_sub_3_ds_ass_group = $request->has_sub_3_ds_ass_group ?? '0';
                }
            }
        }

        $target->updated_by = Auth::user()->id;
        $target->updated_at = date('Y-m-d H:i:s');
//        
//        echo '<pre>';
//        print_r($target->toArray());
//        exit;

        EventTree::where('event_id', $request->event_id)->delete();

        if ($target->save()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_EVENT_TREE')), 401);
        }
    }

}
