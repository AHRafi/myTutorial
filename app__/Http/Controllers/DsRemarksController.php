<?php

namespace App\Http\Controllers;

//use App\CenterToCourse;
use App\Course;
use App\Term;
use App\TrainingYear;
use App\CmBasicProfile;
use App\Event;
use App\DsRemarks;
use App\DsRemarksViewer;
use App\SynToSubSyn;
use App\TermToCourse;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;
use Helper;
use Redirect;
use Session;

class DsRemarksController extends Controller {

    public function index(Request $request) {
        $qpArr = $request->all();
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.ARMS_SERVICE_WISE_EVENT_TREND');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.ARMS_SERVICE_WISE_EVENT_TREND');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termList = ['0' => __('label.ALL_TERMS')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')
                        ->toArray();
        $eventList = Event::join('term_to_event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $courseList->id);
        if (!empty($request->term_id)) {
            $eventList = $eventList->where('term_to_event.term_id', $request->term_id);
        }
        $eventList = $eventList->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();
        $eventList = ['0' => __('label.ALL_EVENT')] + $eventList;

        $cmList = ['0' => __('label.ALL_CM')] + CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->where('cm_basic_profile.course_id', $courseList->id)
                        ->where('cm_basic_profile.status', '1')
                        ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' ') as cm_name"), 'cm_basic_profile.id')
                        ->orderBy('cm_basic_profile.commisioning_date', 'asc')
                        ->pluck('cm_name', 'cm_basic_profile.id')
                        ->toArray();

        $dsRemarksArr = DsRemarks::join('users', 'users.id', 'ds_remarks.remarked_by')
                ->where('ds_remarks.remarked_by',Auth::user()->id)
				->where('ds_remarks.course_id', $courseList->id)
                ->join('cm_basic_profile', 'cm_basic_profile.id', 'ds_remarks.cm_id')
                ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('term', 'term.id', 'ds_remarks.term_id')
                ->leftJoin('event', 'event.id', 'ds_remarks.event_id')
                ->select('ds_remarks.date', 'ds_remarks.id', 'ds_remarks.remarks', 'users.official_name'
                , DB::raw('CONCAT(rank.code, " ", cm_basic_profile.full_name) as cm')
                , 'event.event_code as event', 'term.name as term');

//begin filtering

        if (!empty($request->term_id)) {
            $dsRemarksArr = $dsRemarksArr->where('ds_remarks.term_id', $request->term_id);
        }
        if (!empty($request->cm_id)) {
            $dsRemarksArr = $dsRemarksArr->where('ds_remarks.cm_id', $request->cm_id);
        }
        if (!empty($request->event_id)) {
            $dsRemarksArr = $dsRemarksArr->where('ds_remarks.event_id', $request->event_id);
        }
        $targetArr = $dsRemarksArr->orderBy('ds_remarks.date', 'desc')->paginate(Session::get('paginatorCount'));

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/dsRemarks?page=' . $page);
        }
//            echo '<pre>';
//            print_r($dsRemarksArr->toArray());
//            exit;

        return view('dsRemarks.index', compact('qpArr', 'activeTrainingYearList', 'courseList', 'eventList', 'termList'
                        , 'cmList', 'targetArr'));
    }

    public function create(Request $request) {
        $qpArr = $request->all();
//get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.EVENT_ASSESSMENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + Event::join('term_to_event', 'term_to_event.event_id', 'event.id')
                        ->where('term_to_event.course_id', $courseList->id)
                        ->where('event.status', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $cmList = ['0' => __('label.SELECT_CM_OPT')] + CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->where('cm_basic_profile.course_id', $courseList->id)
                        ->where('cm_basic_profile.status', '1')
                        ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' ') as cm_name"), 'cm_basic_profile.id')
                        ->orderBy('cm_basic_profile.commisioning_date', 'asc')
                        ->pluck('cm_name', 'cm_basic_profile.id')
                        ->toArray();

//print_r($dsRemarksCount); exit;

        return view('dsRemarks.create')->with(compact('qpArr', 'activeTrainingYearInfo', 'termList'
                                , 'courseList', 'eventList', 'cmList'));
    }

    public function edit(Request $request, $id) {
        $target = DsRemarks::find($id);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('dsRemarks');
        }
        //passing param for custom function
        $qpArr = $request->all();

        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.EVENT_ASSESSMENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.OBSN_MARKING');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $termList = ['0' => __('label.SELECT_TERM_OPT')] + TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term.id')->toArray();

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + Event::join('term_to_event', 'term_to_event.event_id', 'event.id')
                        ->where('term_to_event.course_id', $courseList->id)
                        ->where('event.status', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $cmList = ['0' => __('label.SELECT_CM_OPT')] + CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->where('cm_basic_profile.course_id', $courseList->id)
                        ->where('cm_basic_profile.status', '1')
                        ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' ') as cm_name"), 'cm_basic_profile.id')
                        ->orderBy('cm_basic_profile.commisioning_date', 'asc')
                        ->pluck('cm_name', 'cm_basic_profile.id')
                        ->toArray();

        return view('dsRemarks.edit')->with(compact('target', 'qpArr', 'activeTrainingYearInfo', 'termList'
                                , 'courseList', 'eventList', 'cmList'));
    }

    public function update(Request $request) {

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '?page=';

        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'cm_id' => 'required|not_in:0',
            'date' => 'required',
            'remarks' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        
        $target = DsRemarks::find($request->id);
        $target->course_id = $request->course_id;
        $target->term_id = $request->term_id;
        $target->cm_id = $request->cm_id;
        $target->event_id = $request->event_id;
        $target->date = Helper::dateFormatConvert($request->date);
        $target->remarks = $request->remarks;
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;

        if ($target->save()) {
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.REMARKS_UPDATED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.REMARKS_COULD_NOT_BE_UPDATED')], 401);
        }
    }

    public function getEventCmDateRmks(Request $request) {

        $eventList = ['0' => __('label.SELECT_EVENT_OPT')] + Event::join('term_to_event', 'term_to_event.event_id', 'event.id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->where('term_to_event.term_id', $request->term_id)
                        ->where('event.status', '1')
                        ->orderBy('event.event_code', 'asc')
                        ->pluck('event.event_code', 'event.id')
                        ->toArray();

        $html = view('dsRemarks.showEvent', compact('eventList'))->render();
        return response()->json(['html' => $html]);
    }

    public function preview(Request $request) {
        //begin back same page after update
        //end back same page after update

        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'cm_id' => 'required|not_in:0',
            'date' => 'required',
            'remarks' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $trainingYear = TrainingYear::where('id', $request->training_year_id)->select('name')->first();
        $course = Course::where('id', $request->course_id)->select('name')->first();
        $term = Term::where('id', $request->term_id)->select('name')->first();
        $event = Event::where('id', $request->event_id)->select('event_code')->first();
        $cm = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->where('cm_basic_profile.id', $request->cm_id)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name, ' ') as cm_name"))
                ->first();

        $previewData = [
            'trainingYear' => !empty($trainingYear->name) ? $trainingYear->name : '',
            'course' => !empty($course->name) ? $course->name : '',
            'term' => !empty($term->name) ? $term->name : '',
            'event' => !empty($event->event_code) ? $event->event_code : '',
            'cm' => !empty($cm->cm_name) ? $cm->cm_name : '',
            'date' => !empty($request->date) ? $request->date : '',
            'remarks' => !empty($request->remarks) ? $request->remarks : '',
        ];

        $html = view('dsRemarks.showPreviewModal', compact('request', 'previewData'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveRmks(Request $request) {
        //begin back same page after update
        //end back same page after update

        $rules = [
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
            'cm_id' => 'required|not_in:0',
            'date' => 'required',
            'remarks' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }



//        echo '<pre>';
//        print_r($userList);
//        exit();



        $target = new DsRemarks;
        $target->course_id = $request->course_id;
        $target->term_id = $request->term_id;
        $target->cm_id = $request->cm_id;
        $target->event_id = $request->event_id;
        $target->date = Helper::dateFormatConvert($request->date);
        $target->remarks = $request->remarks;
        $target->remarked_at = date('Y-m-d H:i:s');
        $target->remarked_by = Auth::user()->id;
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;

        DB::beginTransaction();
        try {
            if ($target->save()) {
                $allowedUsers = User::whereIn('group_id', [3, 4])->where('status', '1')
                                ->pluck('id', 'id')->toArray();

                $rmksForUserArr = [];
                $mI = 0;
                if (!empty($allowedUsers)) {
                    foreach ($allowedUsers as $userId => $userId) {
                        $status = (Auth::user()->id == $userId) ? '1' : '0';
                        $rmksForUserArr[$mI]['user_id'] = $userId;
                        $rmksForUserArr[$mI]['remarks_id'] = $target->id;
                        $rmksForUserArr[$mI]['cm_id'] = $request->cm_id;
                        $rmksForUserArr[$mI]['event_id'] = $request->event_id;
                        $rmksForUserArr[$mI]['status'] = $status;
                        $rmksForUserArr[$mI]['updated_at'] = date('Y-m-d H:i:s');
                        $rmksForUserArr[$mI]['updated_by'] = Auth::user()->id;
                        $mI++;
                    }
                }

                DsRemarksViewer::insert($rmksForUserArr);
            }

            DB::commit();
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.REMARKS_CREATED_SUCCESSFULLY')], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.REMARKS_COULD_NOT_BE_CREATED')], 401);
        }
    }

    public function destroy(Request $request, $id) {
        $target = DsRemarks::find($id);
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '?page=';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

//        $dependencyArr = [
//            'ProductCheckInDetails' => ['1' => 'product_id'],
//            'ProductReturnDetails' => ['1' => 'product_id'],
//        ];
//        foreach ($dependencyArr as $model => $val) {
//            foreach ($val as $index => $key) {
//                $namespacedModel = '\\App\\' . $model;
//                $dependentData = $namespacedModel::where($key, $id)->first();
//                if (!empty($dependentData)) {
//                    Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL', ['model' => $model]));
//                    return redirect('admin/product' . $pageNumber);
//                }
//            }
//        }
        //end :: dependency check


        if ($target->delete()) { 
            DsRemarksViewer::where('remarks_id', $id)->delete();
            Session::flash('error', __('label.DS_REMARKS_HAS_BEEN_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.DS_REMARKS_COULD_NOT_BE_DELETED'));
        }
        return redirect('dsRemarks' . $pageNumber);
    }

    public function filter(Request $request) {
        
        $url = '&term_id=' . $request->term_id
                . '&cm_id=' . $request->cm_id . '&event_id=' . $request->event_id;
        return Redirect::to('/dsRemarks?' . $url);
    }

}
