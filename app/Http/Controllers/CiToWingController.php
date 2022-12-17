<?php

namespace App\Http\Controllers;

use Validator;
use App\CiToWing;
use App\TrainingYear;
use App\Wing;
use App\User;
use Response;
use Auth;
use DB;
use Illuminate\Http\Request;

class CiToWingController extends Controller {

    public function index(Request $request) {
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $ciList = array('0' => __('label.SELECT_CI_OPT')) + User::join('rank', 'rank.id', 'users.rank_id')
                        ->select(DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (', users.official_name, ')') as ci_name"), 'users.id')
                        ->orderBy('rank.order', 'asc')->where('group_id', '4')->pluck('ci_name', 'users.id')
                        ->toArray();

        return view('ciToWing.index')->with(compact('wingList', 'ciList'));
    }

    public function getCi(Request $request) {

        $targetArr = Syndicate::select('id', 'name')
                        ->where('status', '1')
//                        ->where('center_id', Auth::user()->center_id)
                        ->orderBy('order', 'asc')->get();
        //checked
        $previousDataArr = SynToCourse::select('syn_id', 'id')
                        ->where('course_id', $request->course_id)
//                        ->where('center_id', Auth::user()->center_id)
                        ->get()->toArray();

        $courseData = Course::select('wing_id')->where('id', $request->course_id)->first();

        $previousDataList = [];
        if (!empty($previousDataArr)) {
            foreach ($previousDataArr as $previousData) {
                $previousDataList[$previousData['syn_id']] = $previousData['syn_id'];
            }
        }
        //checked
        //Dependency check Disable data
        $disableSyn = [];
//        $disableSyn = PlCmdrToSyn::join('syn', 'syn.id', '=', 'pl_cmdr_to_syn.syn_id')
//                        ->where('pl_cmdr_to_syn.course_id', $request->course_id)
//                        ->where('pl_cmdr_to_syn.center_id', Auth::user()->center_id)
//                        ->pluck('syn.name', 'syn.id')->toArray();
        //end
        $html = view('ciToWing.showSyn', compact('targetArr', 'courseData', 'previousDataList', 'disableSyn'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveCi(Request $request) {
        
        $rules = [
           'ci_id' => 'required|not_in:0', 
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()], 400);
        }

        $ciToWingInfo = CiToWing::select('id')->where('wing_id', $request->wing_id)->first();
        $target = !empty($ciToWingInfo) ? CiToWing::find($ciToWing->id) : new CiToWing;
        
        $target->wing_id = $request->wing_id;
        $target->ci_id = $request->ci_id;
        $target->updated_by = Auth::user()->id;
        $target->updated_at = date('Y-m-d H:i:s');

        if ($target->save()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_CI')), 401);
        }
    }

}
