<?php

namespace App\Http\Controllers;

use App\User; //model class
use App\CmBasicProfile; //model class
use App\UserGroup; //model class
use App\Rank; //model class
use App\ServiceAppointment; //model class
use App\Wing; //model class
use App\Unit; //model class
use App\Course; //model class
use App\CommissioningCourse; //model class
use App\Religion; //model class
use App\ArmsService; //model class
use App\Appointment; //model class
use App\CmOthers; //model class
use App\Country; //model class
use App\Division; //model class
use App\District; //model class
use App\CmCountryVisit; //model class
use App\Thana; //model class
use App\CmPermanentAddress; //model class
use App\CmCivilEducation; //model class
use App\CmServiceRecord; //model class
use App\CmRelativeInDefence; //model class
use App\CmChild; //model class
use App\CmPresentAddress; //model class
use App\CmPassport;
use App\MilCourse;
use App\CmMission;
use App\CmBank;
use App\Decoration;
use App\Hobby;
use App\Award;
use Validator;
use Session;
use Response;
use Redirect;
use Auth;
use File;
use PDF;
use URL;
use Hash;
use Common;
use DB;
use Helper;
use Illuminate\Http\Request;

class CmController extends Controller {

    public function __construct() {
        
    }

    public function index(Request $request) {
        $nameArr = CmBasicProfile::select('full_name')->where('status', '1')->get();

        //passing param for custom function

        $qpArr = $request->all();
//        $userPermissionArr = ['1' => ['1'], //AHQ Observer
//            '3' => ['1', '2', '3', '4', '5', '6', '7', '8'], //SuperAdmin  
//            '5' => ['6', '7', '8'], //admin
//        ];


        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->where('rank.for_course_member', '1')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        $courseList = array('0' => __('label.SELECT_COURSE_OPT')) + Course::orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')->toArray();
        $commissioningCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE_OPT')) + CommissioningCourse::orderBy('commissioning_date', 'asc')->pluck('name', 'id')->toArray();
        $commissionTypeList = Common::getCommissionType();
        $bloodGroupList = array('0' => __('label.SELECT_BLOOD_GROUP_OPT')) + Common::getBloodGroup();
        $targetArr = CmBasicProfile::join('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->join('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->join('course', 'course.id', '=', 'cm_basic_profile.course_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                ->select('cm_basic_profile.id', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                        , 'cm_basic_profile.photo', 'cm_basic_profile.personal_no', 'cm_basic_profile.status'
                        , 'course.name as course', 'cm_basic_profile.wing_id', 'rank.code as rank'
                        , 'wing.code as wing', 'arms_service.code as arms_service'
                        , 'commissioning_course.name as commissioning_course', 'cm_basic_profile.email'
                        , 'cm_basic_profile.number')
                ->orderBy('course.training_year_id', 'desc')
                ->orderBy('cm_basic_profile.course_id', 'desc')
                ->orderBy('cm_basic_profile.commisioning_date', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc');

//        echo $targetArr->count();exit;
        //begin filtering
        $searchText = $request->fil_search;

        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('cm_basic_profile.full_name', 'LIKE', '%' . $searchText . '%');
            });
        }

        if (!empty($request->fil_wing_id)) {
            $targetArr = $targetArr->where('cm_basic_profile.wing_id', '=', $request->fil_wing_id);
        }

        if (!empty($request->fil_rank_id)) {
            $targetArr = $targetArr->where('cm_basic_profile.rank_id', '=', $request->fil_rank_id);
        }
//
//        if (!empty($request->fil_appointment_id)) {
//            $targetArr = $targetArr->where('users.appointment_id', '=', $request->fil_appointment_id);
//        }
        if (!empty($request->fil_course_id)) {
            $targetArr = $targetArr->where('cm_basic_profile.course_id', '=', $request->fil_course_id);
        }
        if (!empty($request->fil_commissioning_course_id)) {
            $targetArr = $targetArr->where('cm_basic_profile.commissioning_course_id', '=', $request->fil_commissioning_course_id);
        }
        //end filtering
        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/cm?page=' . $page);
        }


        return view('cm.index')->with(compact('qpArr', 'targetArr', 'rankList'
                                , 'nameArr', 'wingList', 'courseList', 'commissioningCourseList'
                                , 'commissionTypeList', 'bloodGroupList'));
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();

//        $userNameArr = User::select('full_name')->where('group_id', 7)->where('status', '1')->get();
        //passing param for custom function
//        $userPermissionArr = ['1' => ['1'], //AHQ Observer
//            '3' => ['1', '2', '3', '4', '5', '6', '7', '8'], //SuperAdmin  
//            '5' => ['6', '7', '8'], //admin
//        ];

        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::join('wing', 'wing.id', '=', 'rank.wing_id')
                        ->where('rank.wing_id', old('wing_id'))
                        ->where('rank.status', '1')
                        ->where('rank.for_course_member', '1')
                        ->pluck('rank.code', 'rank.id')->toArray();


        $courseList = array('0' => __('label.SELECT_COURSE_OPT')) + Course::orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')->toArray();
        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::pluck('code', 'id')->toArray();
        $religionList = array('0' => __('label.SELECT_RELIGION_OPT')) + Religion::pluck('name', 'id')->toArray();
        $commissioningCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE_OPT')) + CommissioningCourse::orderBy('commissioning_date', 'asc')
                        ->pluck('name', 'id')->toArray();
        $commissionTypeList = Common::getCommissionType();

        $bloodGroupList = array('0' => __('label.SELECT_BLOOD_GROUP_OPT')) + Common::getBloodGroup();

        return view('cm.create')->with(compact('qpArr', 'rankList', 'wingList'
                                , 'courseList', 'commissioningCourseList'
                                , 'armsServiceList', 'religionList', 'commissionTypeList'
                                , 'bloodGroupList'));
    }

    public function store(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();


        $pageNumber = $qpArr['filter'];

        $rules = [
            'course_id' => 'required|not_in:0',
            'wing_id' => 'required|not_in:0',
            'rank_id' => 'required|not_in:0',
            'personal_no' => 'required|unique:cm_basic_profile',
            'full_name' => 'required',
            'official_name' => 'required',
            'father_name' => 'required',
            'arms_service_id' => 'required|not_in:0',
            'commissioning_course_id' => 'required|not_in:0',
            'commission_type' => 'required|not_in:0',
            'commisioning_date' => 'required',
            'date_of_birth' => 'required',
            'birth_place' => 'required',
            'religion_id' => 'required|not_in:0',
            'email' => 'required|email',
            'number' => 'required',
        ];

        if (!empty($request->photo)) {
            $rules['photo'] = 'max:1024|mimes:jpeg,png,jpg';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect('cm/create')
                            ->withInput($request->except('photo', 'password', 'conf_password'))
                            ->withErrors($validator);
        }

        //file upload
        $file = $request->file('photo');
        if (!empty($file)) {
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/cm', $fileName);
        }

        $target = new CmBasicProfile;

        $target->course_id = $request->course_id;
        $target->wing_id = $request->wing_id;
        $target->rank_id = $request->rank_id;
        $target->personal_no = $request->personal_no;
        $target->full_name = $request->full_name;
        $target->official_name = $request->official_name;
        $target->father_name = $request->father_name;
        $target->status = $request->status;
        $target->photo = !empty($fileName) ? $fileName : '';


        $target->arms_service_id = $request->arms_service_id;

        $target->commission_type = $request->commission_type;
        $target->commissioning_course_id = $request->commissioning_course_id;
        $target->ante_date_seniority = $request->ante_date_seniority ?? null;
        $target->commisioning_date = Helper::dateFormatConvert($request->commisioning_date);

        $target->date_of_birth = Helper::dateFormatConvert($request->date_of_birth);
        $target->birth_place = $request->birth_place;
        $target->religion_id = $request->religion_id;
        $target->email = $request->email;
        $target->number = $request->number;
        $target->blood_group = $request->blood_group;

        if ($target->save()) {
            Session::flash('success', __('label.CM_CREATED_SUCCESSFULLY'));
            return redirect('cm');
        } else {
            Session::flash('error', __('label.CM_COULD_NOT_BE_CREATED'));
            return redirect('cm/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {

        $qpArr = $request->all();
        $target = CmBasicProfile::find($id);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('cm');
        }

        $cm = CmBasicProfile::select('course_id', 'commissioning_course_id')->where('id', $id)->first();
        //passing param for custom function
        $wingId = !empty(old('wing_id')) ? old('wing_id') : $target->wing_id;
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::join('wing', 'wing.id', '=', 'rank.wing_id')
                        ->where('rank.wing_id', $wingId)
                        ->where('rank.status', '1')
                        ->where('rank.for_course_member', '1')
                        ->pluck('rank.code', 'rank.id')->toArray();

        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        $courseList = array('0' => __('label.SELECT_COURSE_OPT')) + Course::orderBy('training_year_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')->toArray();
        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::pluck('code', 'id')->toArray();
        $religionList = array('0' => __('label.SELECT_RELIGION_OPT')) + Religion::pluck('name', 'id')->toArray();
        $commissioningCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE_OPT')) + CommissioningCourse::orderBy('commissioning_date', 'asc')
                        ->pluck('name', 'id')->toArray();
        $commissionTypeList = Common::getCommissionType();

        $bloodGroupList = array('0' => __('label.SELECT_BLOOD_GROUP_OPT')) + Common::getBloodGroup();

        return view('cm.edit')->with(compact('target', 'qpArr', 'cm', 'rankList'
                                , 'wingList', 'courseList', 'commissioningCourseList'
                                , 'armsServiceList', 'religionList', 'commissionTypeList'
                                , 'bloodGroupList'));
    }

    public function update(Request $request, $id) {
        $target = CmBasicProfile::find($id);
        $previousFileName = $target->photo;

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $rules = [
            'course_id' => 'required|not_in:0',
            'wing_id' => 'required|not_in:0',
            'rank_id' => 'required|not_in:0',
            'personal_no' => 'required|unique:cm_basic_profile,personal_no,' . $id,
            'full_name' => 'required',
            'official_name' => 'required',
            'father_name' => 'required',
            'arms_service_id' => 'required|not_in:0',
            'commissioning_course_id' => 'required|not_in:0',
            'commission_type' => 'required|not_in:0',
            'commisioning_date' => 'required',
            'date_of_birth' => 'required',
            'birth_place' => 'required',
            'religion_id' => 'required|not_in:0',
            'email' => 'required|email',
            'number' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!empty($request->photo)) {
            $validator->photo = 'max:1024|mimes:jpeg,png,gif,jpg';
        }

        if ($validator->fails()) {
            return redirect('cm/' . $id . '/edit' . $pageNumber)
                            ->withInput($request->all)
                            ->withErrors($validator);
        }

        if (!empty($request->photo)) {
            $prevfileName = 'public/uploads/cm/' . $target->photo;

            if (File::exists($prevfileName)) {
                File::delete($prevfileName);
            }
        }

        $file = $request->file('photo');
        if (!empty($file)) {
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/cm', $fileName);
//            echo '<pre>';print_r($fileName);exit;
        }
        $target->course_id = $request->course_id;
        $target->wing_id = $request->wing_id;
        $target->rank_id = $request->rank_id;
        $target->personal_no = $request->personal_no;
        $target->full_name = $request->full_name;
        $target->official_name = $request->official_name;
        $target->father_name = $request->father_name;
        $target->status = $request->status;
        $target->photo = !empty($fileName) ? $fileName : $previousFileName;


        $target->arms_service_id = $request->arms_service_id;

        $target->commission_type = $request->commission_type;
        $target->commissioning_course_id = $request->commissioning_course_id;
        $target->ante_date_seniority = $request->ante_date_seniority ?? null;
        $target->commisioning_date = Helper::dateFormatConvert($request->commisioning_date);

        $target->date_of_birth = Helper::dateFormatConvert($request->date_of_birth);
        $target->birth_place = $request->birth_place;
        $target->religion_id = $request->religion_id;
        $target->email = $request->email;
        $target->number = $request->number;
        $target->blood_group = $request->blood_group;


        if ($target->save()) {
            CmBasicProfile::where('id', $id);
            Session::flash('success', __('label.CM_UPDATED_SUCCESSFULLY'));
            return redirect('cm' . $pageNumber);
        } else {
            Session::flash('error', __('label.CM_COULD_NOT_BE_UPDATED'));
            return redirect('cm/create' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {

        $target = CmBasicProfile::find($id);


        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '?page=';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        $dependencyArr = [
            //administrativs dependancyArr
            'Rank' => ['1' => 'created_by', '2' => 'updated_by'],
            'Appointment' => ['1' => 'created_by', '2' => 'updated_by'],
            'TrainingYear' => ['1' => 'created_by', '2' => 'updated_by'],
            'ArmsService' => ['1' => 'created_by', '2' => 'updated_by'],
            'Wing' => ['1' => 'created_by', '2' => 'updated_by'],
            'Term' => ['1' => 'created_by', '2' => 'updated_by'],
//            'Trade' => ['1' => 'created_by', '2' => 'updated_by'],
//            'Module' => ['1' => 'created_by', '2' => 'updated_by'],
//            'Subject' => ['1' => 'created_by', '2' => 'updated_by'],
//            'Event' => ['1' => 'created_by', '2' => 'updated_by'],
//            'MajorEvent' => ['1' => 'created_by', '2' => 'updated_by'],
//            'WingToBatch' => ['1' => 'updated_by'],
//            'CiObservationMarkingLock' => ['1' => 'locked_by'],
//            'CiToWing' => ['1' => 'ci_id', '2' => 'updated_by'],
//            'CourseReport' => ['1' => 'created_by'],
//            'DropCategory' => ['1' => 'created_by', '2' => 'updated_by'],
//            'EventMarkingLock' => ['1' => 'locked_by'],
//            'EventWtDistr' => ['1' => 'updated_by'],
//            'Marking' => ['1' => 'updated_by'],
//            'ModuleWtDistr' => ['1' => 'updated_by'],
//            'ObservationMarking' => ['1' => 'updated_by'],
//            'ObservationMarkingLock' => ['1' => 'oic_locked_by', '2' => 'ci_locked_by'],
//            'ObservationWtDistr' => ['1' => 'updated_by'],
//            'ObservationMarkingLock' => ['1' => 'oic_locked_by', '2' => 'ci_locked_by'],
//            'Particular' => ['1' => 'created_by', '2' => 'updated_by'],
            'Syndicate' => ['1' => 'created_by', '2' => 'updated_by'],
//            'ParticularMarkingLock' => ['1' => 'locked_by'],
//            'ParticularWtDistr' => ['1' => 'updated_by'],
            'SynToCourse' => ['1' => 'updated_by'],
//            'RctState' => ['1' => 'created_by', '2' => 'unlock_request_by'],
            'Course' => ['1' => 'created_by', '2' => 'updated_by'],
//            'RecruitToPlatoon' => ['1' => 'updated_by'],
//            'RecruitToTrade' => ['1' => 'updated_by'],
//            'SubjectWtDistr' => ['1' => 'updated_by'],
//            'TermToCourse' => ['1' => 'updated_by'],
            'TermToEvent' => ['1' => 'updated_by'],
//            'TermToParticular' => ['1' => 'updated_by'],
        ];

        $fileName = 'public/uploads/user/' . $target->photo;
        if (File::exists($fileName)) {
            File::delete($fileName);
        }

        if ($target->delete()) {
            Session::flash('success', __('label.CM_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.CM_COULD_NOT_BE_DELETED'));
        }
        return redirect('cm' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fil_wing_id=' . $request->fil_wing_id
                . '&fil_rank_id=' . $request->fil_rank_id . '&fil_course_id=' . $request->fil_course_id;
        return Redirect::to('cm?' . $url);
    }

    public function getRank(Request $request) {
        $rankList = Rank::orderBy('id', 'asc');
        if ((!empty($request->index_id) && !empty($request->wing_id)) || empty($request->index_id)) {
            $rankList = $rankList->where('wing_id', $request->wing_id)->where('rank.for_course_member', '1');
        }
        $rankList = $rankList->pluck('code', 'id')->toArray();
        $rankList = ['0' => __('label.SELECT_RANK_OPT')] + $rankList;

        $html = view('cm.showRank', compact('rankList'))->render();
        return response()->json(['html' => $html]);
    }

    //*********** Start :: CM Profile **********************//
    public function profile(Request $request, $id) {
//        echo '<pre>';        print_r($id); exit;
        $keyAppt = [];
        $qpArr = $request->all();
        $cmInfoData = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('course', 'course.id', '=', 'cm_basic_profile.course_id')
                ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                ->leftJoin('religion', 'religion.id', '=', 'cm_basic_profile.religion_id')
                ->select('cm_basic_profile.id as cm_basic_profile_id', 'cm_basic_profile.email'
                        , 'cm_basic_profile.photo', 'cm_basic_profile.number', 'cm_basic_profile.full_name'
                        , 'cm_basic_profile.official_name'
                        , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.official_name, ')') as cm_name")
                        , 'course.name as course_name'
                        , 'arms_service.name as arms_service_name', 'commissioning_course.name as commissioning_course_name'
                        , 'religion.name as religion_name'
                        , 'cm_basic_profile.*','wing.code as wing_name')
                ->where('cm_basic_profile.status', '1')
                ->where('cm_basic_profile.id', $id)
                ->first();
        
        $civilEducationInfoData = CmCivilEducation::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'civil_education_info')
                ->first();

        $serviceRecordInfoData = CmServiceRecord::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'service_record_info')
                ->first();

        $msnDataInfo = CmMission::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'msn_info')
                ->first();
        if(!empty($serviceRecordInfoData)){
            $serviceRecordInfo = json_decode($serviceRecordInfoData->service_record_info,TRUE);
            if(!empty($serviceRecordInfo)){
                foreach ($serviceRecordInfo as $skey => $serviceRecord){
                    $keyAppt[$serviceRecord['appointment']] = $serviceRecord['appointment'];
                }
            }
        }
        if(!empty($msnDataInfo)){
            $msnData = json_decode($msnDataInfo->msn_info,TRUE);
            if(!empty($msnData)){
                foreach ($msnData as $mkey => $msn){
                    $keyAppt[$msn['appointment']] = $msn['appointment'];
                }
            }
        }
        

        $countryVisitDataInfo = CmCountryVisit::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'visit_info')
                ->first();

        $bankInfoData = CmBank::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'bank_info')
                ->first();

        $childInfoData = CmChild::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'cm_child_info', 'no_of_child')
                ->first();

        $defenceRelativeInfoData = CmRelativeInDefence::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'cm_relative_info')
                ->first();

        $othersInfoData = CmOthers::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'decoration_id', 'hobby_id')
                ->first();
        $passportInfoData = CmPassport::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'passport_no', 'place_of_issue', 'date_of_issue', 'date_of_expire')
                ->first();
        
        $commissionTypeList = Common::getCommissionType();
        $bloodGroupList = Common::getBloodGroup();

        $decorationList = Decoration::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $awardList = Award::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $hobbyList = Hobby::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();

        $religionList = ['0' => __('label.SELECT_RELIGION_OPT')] + Religion::pluck('name', 'id')->toArray();
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $allAppointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $armsServiceList = ['0' => __('label.SELECT_ARMS_SERVICE_OPT')] + ArmsService::pluck('code', 'id')->toArray();
        $unitList = ['0' => __('label.SELECT_UNIT_OPT')] + Unit::pluck('code', 'id')->toArray();
        $maritalStatusList = ['0' => __('label.SELECT_MARITAL_STATUS_OPT')] + Helper::getMaritalStatus();
        $countriesVisitedList = Country::pluck('name', 'id')->toArray();
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::pluck('name', 'id')->toArray();
        $organizationList = ['0' => __('label.SELECT_UNIT_FMN_INST_OPT')] + Unit::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $milCourseList = ['0' => __('label.SELECT_COURSE_OPT')] + MilCourse::where('status', '1')
                        ->pluck('short_info', 'id')->toArray();

        //Division District Thana for cm permanent address
        $addressInfo = CmPermanentAddress::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : '0')
                ->select('id', 'cm_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details', 'same_as_present')
                ->first();
        $presentAddressInfo = CmPresentAddress::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : '0')
                ->select('id', 'cm_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details')
                ->first();

        $presentDistrictList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', !empty($presentAddressInfo->division_id) ? $presentAddressInfo->division_id : 0)
                        ->pluck('name', 'id')->toArray();
        $presentThanaList = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', !empty($presentAddressInfo->district_id) ? $presentAddressInfo->district_id : 0)
                        ->pluck('name', 'id')->toArray();

        $divisionList = ['0' => __('label.SELECT_DIVISION_OPT')] + Division::pluck('name', 'id')->toArray();
        $districtList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', !empty($addressInfo->division_id) ? $addressInfo->division_id : 0)
                        ->pluck('name', 'id')->toArray();
        $thanaList = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', !empty($addressInfo->district_id) ? $addressInfo->district_id : 0)
                        ->pluck('name', 'id')->toArray();


        return view('cm.details.index')->with(compact('cmInfoData', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                , 'hobbyList', 'childInfoData', 'qpArr','commissionTypeList','bloodGroupList','keyAppt')
        );
    }

    public function updateMaritalStatus(Request $request) {
        $rules = [
            'marital_status' => 'not_in:0',
        ];

        $messages = [];

        if ($request->marital_status == '1') {
            $rules['spouse_name'] = 'required';
            $rules['spouse_date_of_birth'] = 'required';
            $rules['spouse_nick_name'] = 'required';
            $rules['date_of_marriage'] = 'required';
        }
        if (!empty($request->no_of_child)) {
            $row = 1;
            if (!empty($request->child)) {
                foreach ($request->child as $key => $childInfo) {
                    $rules['child.' . $key . '.name'] = 'required';
                    $rules['child.' . $key . '.dob'] = 'required';

                    $messages['child.' . $key . '.name.required'] = __('label.CHILD_NAME_FIELD_IS_REQUIRED', ["counter" => $row]);
                    $messages['child.' . $key . '.dob.required'] = __('label.CHILD_DATE_OF_BIRTH_FIELD_IS_REQ', ["counter" => $row]);

                    $row++;
                }
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $cmBasicProfile = CmBasicProfile::find($request->cm_basic_profile_id);
        $cmBasicProfile->marital_status = $request->marital_status;
        $cmBasicProfile->date_of_marriage = !empty($request->date_of_marriage) ? Helper::dateFormatConvert($request->date_of_marriage) : null;
        $cmBasicProfile->spouse_dob = !empty($request->spouse_date_of_birth) ? Helper::dateFormatConvert($request->spouse_date_of_birth) : null;
        $cmBasicProfile->spouse_name = $request->spouse_name ?? null;
        $cmBasicProfile->spouse_nick_name = $request->spouse_nick_name ?? null;
        $cmBasicProfile->spouse_mobile = $request->spouse_mobile ?? null;
        $cmBasicProfile->spouse_occupation = $request->spouse_occupation ?? null;
        $cmBasicProfile->spouse_work_address = $request->spouse_work_address ?? null;

        $cmChildInfo = CmChild::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $cmChildInfo = !empty($cmChildInfo->id) ? CmChild::find($cmChildInfo->id) : new CmChild;

        $childInfo = !empty($request->child) ? json_encode($request->child) : '';
        $cmChildInfo->cm_basic_profile_id = $request->cm_basic_profile_id;
        $cmChildInfo->no_of_child = !empty($request->no_of_child) ? $request->no_of_child : 0;
        $cmChildInfo->cm_child_info = $childInfo;
        $cmChildInfo->updated_at = date('Y-m-d H:i:s');
        $cmChildInfo->updated_by = Auth::user()->id;

        if ($cmBasicProfile->save() && $cmChildInfo->save()) {
            return response()->json(['success' => true, 'message' => __('label.CM_MARITAL_STATUS_UPDATED_SUCCESSFULLY')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_CM_MARITAL_STATUS')], 401);
        }

        //End updateMaritialStatus function
    }

    //For Districts
    public function getDistrict(Request $request) {
        $districtList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', $request->division_id)
                        ->pluck('name', 'id')->toArray();
        $thanaList = ['0' => __('label.SELECT_THANA_OPT')];
        $htmldistrict = view('cm.details.districts')->with(compact('districtList'))->render();
        $htmlThana = view('cm.details.thana')->with(compact('thanaList'))->render();
        return response()->json(['html' => $htmldistrict, 'htmlThana' => $htmlThana]);
        //End getDistrict function
    }

    //For Thana
    public function getThana(Request $request) {
        $thanaList = ['0' => __('label.SELECT_THANA_OPT')] + THANA::where('district_id', $request->district_id)->pluck('name', 'id')->toArray();
        $htmlThana = view('cm.details.thana')->with(compact('thanaList'))->render();
        return response()->json(['html' => $htmlThana]);
        //End getThana function
    }

    public function updatePermanentAddress(Request $request) {
        $messages = [];
        $rules = [
            'present_division_id' => 'required|not_in:0',
        ];
        if (empty($request->for_addr)) {
            $rules = [
                'permanent_division_id' => 'required|not_in:0',
            ];
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cmPermanentAddress = CmPermanentAddress::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $cmPermanentAddressInfo = !empty($cmPermanentAddress->id) ? CmPermanentAddress::find($cmPermanentAddress->id) : new CmPermanentAddress;

        $cmPresentAddressInfo = CmPresentAddress::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $cmPresentAddressInfo = !empty($cmPresentAddressInfo->id) ? CmPresentAddress::find($cmPresentAddressInfo->id) : new CmPresentAddress;

        $cmPresentAddressInfo->cm_basic_profile_id = $request->cm_basic_profile_id;
        $cmPresentAddressInfo->division_id = $request->present_division_id;
        $cmPresentAddressInfo->district_id = $request->present_district_id;
        $cmPresentAddressInfo->thana_id = $request->present_thana_id;
        $cmPresentAddressInfo->address_details = $request->present_address_details;
        $cmPresentAddressInfo->updated_at = date('Y-m-d H:i:s');
        $cmPresentAddressInfo->updated_by = Auth::user()->id;

        if (!empty($request->for_addr)) {
            $cmPermanentAddressInfo->cm_basic_profile_id = $request->cm_basic_profile_id;
            $cmPermanentAddressInfo->division_id = $request->present_division_id;
            $cmPermanentAddressInfo->district_id = $request->present_district_id;
            $cmPermanentAddressInfo->thana_id = $request->present_thana_id;
            $cmPermanentAddressInfo->address_details = $request->present_address_details;
            $cmPermanentAddressInfo->same_as_present = $request->for_addr ?? '0';
            $cmPermanentAddressInfo->updated_at = date('Y-m-d H:i:s');
            $cmPermanentAddressInfo->updated_by = Auth::user()->id;
        } else {
            $cmPermanentAddressInfo->cm_basic_profile_id = $request->cm_basic_profile_id;
            $cmPermanentAddressInfo->division_id = $request->permanent_division_id;
            $cmPermanentAddressInfo->district_id = $request->permanent_district_id;
            $cmPermanentAddressInfo->thana_id = $request->permanent_thana_id;
            $cmPermanentAddressInfo->address_details = $request->permanent_address_details;
            $cmPermanentAddressInfo->same_as_present = $request->for_addr ?? '0';
            $cmPermanentAddressInfo->updated_at = date('Y-m-d H:i:s');
            $cmPermanentAddressInfo->updated_by = Auth::user()->id;
        }

        if ($cmPermanentAddressInfo->save() && $cmPresentAddressInfo->save()) {
            return response()->json(['success' => true, 'message' => __('label.CM_ADDRESS_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_ADDRESS')], 401);
        }
        //End updatePermanentAddress function
    }

    public function rowAddForCivilEducation() {
        $html = view('cm.details.civilEducationRowAdd')->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function updateCivilEducationInfo(Request $request) {
        //Check Validation for Civil Education Information
        $rules = $messages = [];
        if (!empty($request->academic_qual)) {
            $row = 1;
            foreach ($request->academic_qual as $key => $civilEducation) {
                $rules['academic_qual.' . $key . '.institute_name'] = 'required';
                $rules['academic_qual.' . $key . '.examination'] = 'required';
                $rules['academic_qual.' . $key . '.from'] = 'required';
                $rules['academic_qual.' . $key . '.to'] = 'required';
//                $rules['academic_qual.' . $key . '.qual_erode'] = 'required';

                $messages['academic_qual.' . $key . '.institute_name.required'] = __('label.INSTITUTE_NAME_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['academic_qual.' . $key . '.examination.required'] = __('label.EXAMINATION_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['academic_qual.' . $key . '.from.required'] = __('label.FROM_DATE_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['academic_qual.' . $key . '.to.required'] = __('label.TO_DATE_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
//                $messages['academic_qual.' . $key . '.qual_erode.required'] = __('label.QUALIFICATION_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $civilEducationInfo = CmCivilEducation::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $civilEducationProfile = !empty($civilEducationInfo->id) ? CmCivilEducation::find($civilEducationInfo->id) : new CmCivilEducation;



        $civilEducation = json_encode($request->academic_qual);
        $civilEducationProfile->cm_basic_profile_id = $request->cm_basic_profile_id;
        $civilEducationProfile->civil_education_info = $civilEducation;
        $civilEducationProfile->updated_at = date('Y-m-d H:i:s');
        $civilEducationProfile->updated_by = Auth::user()->id;

        //Update cm civil education
        if ($civilEducationProfile->save()) {
            return response()->json(['success' => true, 'message' => __('label.ACADEMIC_QUALIFICATION_INFO_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_ACADEMIC_QUAL')], 401);
        }
        //End updateCivilEducationInfo function
    }

    public function rowAddForServiceRecord() {
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $unitList = ['0' => __('label.SELECT_UNIT_OPT')] + Unit::pluck('code', 'id')->toArray();
        $organizationList = ['0' => __('label.SELECT_UNIT_FMN_INST_OPT')] + Unit::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();

        $html = view('cm.details.serviceRecordRowAdd')->with(compact('appointmentList', 'unitList'
                                , 'organizationList'))->render();
        return response()->json(['html' => $html]);

        ////End rowAdd function
    }

    public function updateServiceRecordInfo(Request $request) {
        //Check Validation for Service Record Information
        $rules = $messages = [];

        if (!empty($request->service_record)) {
            $row = 1;
            foreach ($request->service_record as $srKey => $serviceRecord) {
                $rules['service_record.' . $srKey . '.from'] = 'required';
                $rules['service_record.' . $srKey . '.to'] = 'required';
                $rules['service_record.' . $srKey . '.unit_fmn_inst'] = 'required|not_in:0';
                $rules['service_record.' . $srKey . '.appointment'] = 'required|not_in:0';

                $messages['service_record.' . $srKey . '.from.required'] = __('label.FROM_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['service_record.' . $srKey . '.to.required'] = __('label.TO_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['service_record.' . $srKey . '.unit_fmn_inst.not_in'] = __('label.UNIT_FMN_INST_FIELD_REQUIRED', ["counter" => $row]);
                $messages['service_record.' . $srKey . '.appointment.not_in'] = __('label.APPOINTMENT_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $serviceEducationInfo = CmServiceRecord::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $serviceEducationProfile = !empty($serviceEducationInfo->id) ? CmServiceRecord::find($serviceEducationInfo->id) : new CmServiceRecord;

        $serviceRecord = json_encode($request->service_record);
        $serviceEducationProfile->cm_basic_profile_id = $request->cm_basic_profile_id;
        $serviceEducationProfile->service_record_info = $serviceRecord;
        $serviceEducationProfile->updated_at = date('Y-m-d H:i:s');
        $serviceEducationProfile->updated_by = Auth::user()->id;

        //Update cm service record
        if ($serviceEducationProfile->save()) {
            return response()->json(['success' => true, 'message' => __('label.SERVICE_RECORD_INFO_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_RECORD_OF_SERVICE')], 401);
        }
        //End updateServiceRecordInfo function
    }

    public function rowAddForUnMsn() {
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $html = view('cm.details.unMsnRowAdd', compact('appointmentList'))->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForCountry() {
        $html = view('cm.details.newVisitedCountryRow')->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForChild(Request $request) {
        $noOfChild = $request->no_of_child;
        $html = view('cm.details.newChildRow', compact('noOfChild'))->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForBank() {
        $html = view('cm.details.newBankRow')->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function updateUnMsn(Request $request) {
        //Check Validation for Punishment Record Information
        $rules = $messages = [];
        if (!empty($request->un_msn)) {
            $row = 1;
            foreach ($request->un_msn as $key => $unMsn) {
                $rules['un_msn.' . $key . '.from'] = 'required';
                $rules['un_msn.' . $key . '.to'] = 'required';
                $rules['un_msn.' . $key . '.msn'] = 'required';
                $rules['un_msn.' . $key . '.appointment'] = 'required|not_in:0';

                $messages['un_msn.' . $key . '.from.required'] = __('label.FROM_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['un_msn.' . $key . '.to.required'] = __('label.TO_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['un_msn.' . $key . '.msn.required'] = __('label.MSN_FIELD_IS_REQUIRED', ["counter" => $row]);
                $messages['un_msn.' . $key . '.appointment.not_in'] = __('label.APPOINTMENT_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $unMsnInfo = CmMission::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $unMsnInfo = !empty($unMsnInfo->id) ? CmMission::find($unMsnInfo->id) : new CmMission;

        $msnRecord = json_encode($request->un_msn);
        $unMsnInfo->cm_basic_profile_id = $request->cm_basic_profile_id;
        $unMsnInfo->msn_info = $msnRecord;
        $unMsnInfo->updated_at = date('Y-m-d H:i:s');
        $unMsnInfo->updated_by = Auth::user()->id;

        //Update cm punishment record
        if ($unMsnInfo->save()) {
            return response()->json(['success' => true, 'message' => __('label.UN_MSN_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_UN_MSN')], 401);
        }
        //End updatePunishmentRecordInfo function
    }

    public function updateCountryVisit(Request $request) {
        //Check Validation for Punishment Record Information
        $rules = $messages = [];
        if (!empty($request->country_visit)) {
            $row = 1;
            foreach ($request->country_visit as $key => $info) {
                $rules['country_visit.' . $key . '.country_name'] = 'required';
                $rules['country_visit.' . $key . '.from'] = 'required';
                $rules['country_visit.' . $key . '.to'] = 'required';
                $rules['country_visit.' . $key . '.reason'] = 'required';

                $messages['country_visit.' . $key . '.country_name.required'] = __('label.COUNTRY_NAME_FIELD_IS_REQUIRED', ["counter" => $row]);
                $messages['country_visit.' . $key . '.from.required'] = __('label.FROM_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['country_visit.' . $key . '.to.required'] = __('label.TO_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['country_visit.' . $key . '.reason.required'] = __('label.REASON_FIELD_IS_REQUIRED', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $visitInfo = CmCountryVisit::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $visitInfo = !empty($visitInfo->id) ? CmCountryVisit::find($visitInfo->id) : new CmCountryVisit;

        $visitRecord = json_encode($request->country_visit);
        $visitInfo->cm_basic_profile_id = $request->cm_basic_profile_id;
        $visitInfo->visit_info = $visitRecord;
        $visitInfo->updated_at = date('Y-m-d H:i:s');
        $visitInfo->updated_by = Auth::user()->id;

        //Update cm punishment record
        if ($visitInfo->save()) {
            return response()->json(['success' => true, 'message' => __('label.COUNTRY_VISITED_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_COUNTRY_VISITED')], 401);
        }
        //End updatePunishmentRecordInfo function
    }

    public function updateBank(Request $request) {
        //Check Validation for Punishment Record Information
        $rules = $messages = [];
        if (!empty($request->bank)) {
            $row = 1;
            foreach ($request->bank as $key => $info) {
                $rules['bank.' . $key . '.name'] = 'required';
                $rules['bank.' . $key . '.branch'] = 'required';
                $rules['bank.' . $key . '.account'] = 'required';

                $messages['bank.' . $key . '.name.required'] = __('label.BANK_NAME_IS_REQUIRED_FOR_SER', ["counter" => $row]);
                $messages['bank.' . $key . '.branch.required'] = __('label.BRANCH_IS_REQUIRED_FOR_SER', ["counter" => $row]);
                $messages['bank.' . $key . '.account.required'] = __('label.ACCOUNT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $bankInfo = CmBank::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $bankInfo = !empty($bankInfo->id) ? CmBank::find($bankInfo->id) : new CmBank;

        $bankRecord = json_encode($request->bank);
        $bankInfo->cm_basic_profile_id = $request->cm_basic_profile_id;
        $bankInfo->bank_info = $bankRecord;
        $bankInfo->updated_at = date('Y-m-d H:i:s');
        $bankInfo->updated_by = Auth::user()->id;

        //Update cm punishment record
        if ($bankInfo->save()) {
            return response()->json(['success' => true, 'message' => __('label.BANK_ACCOUNT_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_BANK_ACCOUNT')], 401);
        }
        //End updatePunishmentRecordInfo function
    }

    public function rowAddForDefenceRelative() {
        $milCourseList = ['0' => __('label.SELECT_COURSE_OPT')] + MilCourse::where('status', '1')
                        ->pluck('short_info', 'id')->toArray();
        $html = view('cm.details.defenceRelativeRowAdd')->with(compact('milCourseList'))->render();
        return response()->json(['html' => $html]);
        ////End rowAddForDefenceRelative function
    }

    public function updateDefenceRelativeInfo(Request $request) {
        //Check Validation for Punishment Record Information
        $rules = $messages = [];
        if (!empty($request->mil_qual)) {
            $row = 1;
            foreach ($request->mil_qual as $key => $defenceRelative) {
                $rules['mil_qual.' . $key . '.institute_name'] = 'required';
                $rules['mil_qual.' . $key . '.course'] = 'not_in:0';
                $rules['mil_qual.' . $key . '.from'] = 'required';
                $rules['mil_qual.' . $key . '.to'] = 'required';
                $rules['mil_qual.' . $key . '.result'] = 'required';

                $messages['mil_qual.' . $key . '.course.not_in'] = __('label.COURSE_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['mil_qual.' . $key . '.institute_name.required'] = __('label.INSTITUTE_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['mil_qual.' . $key . '.from.required'] = __('label.FROM_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['mil_qual.' . $key . '.to.required'] = __('label.TO_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['mil_qual.' . $key . '.result.required'] = __('label.RESULT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $defenceRecordInfo = CmRelativeInDefence::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $defenceRecordProfile = !empty($defenceRecordInfo->id) ? CmRelativeInDefence::find($defenceRecordInfo->id) : new CmRelativeInDefence;

        $defenceRecord = json_encode($request->mil_qual);
        $defenceRecordProfile->cm_basic_profile_id = $request->cm_basic_profile_id;
        $defenceRecordProfile->cm_relative_info = $defenceRecord;
        $defenceRecordProfile->updated_at = date('Y-m-d H:i:s');
        $defenceRecordProfile->updated_by = Auth::user()->id;

        //Update cm punishment record
        if ($defenceRecordProfile->save()) {
            return response()->json(['success' => true, 'message' => __('label.MIL_QUAL_INFO_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_MIL_QUAL')], 401);
        }
        //End updatePunishmentRecordInfo function
    }

    public function updateMedicalDetails(Request $request) {
        $rules = [
            'passport_no' => 'required',
            'date_of_issue' => 'required',
            'place_of_issue' => 'required',
            'date_of_expire' => 'required',
        ];

        $messages = [];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $passportInfo = CmPassport::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $passportInfo = !empty($passportInfo->id) ? CmPassport::find($passportInfo->id) : new CmPassport;

        $passportInfo->cm_basic_profile_id = $request->cm_basic_profile_id;
        $passportInfo->passport_no = $request->passport_no;
        $passportInfo->place_of_issue = $request->place_of_issue;
        $passportInfo->date_of_issue = Helper::dateFormatConvert($request->date_of_issue);
        $passportInfo->date_of_expire = Helper::dateFormatConvert($request->date_of_expire);
        $passportInfo->updated_at = date('Y-m-d H:i:s');
        $passportInfo->updated_by = Auth::user()->id;

        if ($passportInfo->save()) {
            return response()->json(['success' => true, 'message' => __('label.PASSPORT_DETAILS_INFO_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_PASSPORT_DETAILS')], 401);
        }
        //End updateMedicalDetails function
    }

    public function updateCmOthersInfo(Request $request) {
        $cmOthersInfo = CmOthers::select('id')->where('cm_basic_profile_id', $request->cm_basic_profile_id)->first();
        $cmOthersProfile = !empty($cmOthersInfo->id) ? CmOthers::find($cmOthersInfo->id) : new CmOthers;

        $decorationInfo = !empty($request->decoration_id) ? implode(',', $request->decoration_id) : '';
        $hobbyInfo = !empty($request->hobby_id) ? implode(',', $request->hobby_id) : '';
        $cmOthersProfile->cm_basic_profile_id = $request->cm_basic_profile_id;
        $cmOthersProfile->decoration_id = $decorationInfo;
        $cmOthersProfile->hobby_id = $hobbyInfo;
        $cmOthersProfile->updated_at = date('Y-m-d H:i:s');
        $cmOthersProfile->updated_by = Auth::user()->id;

        if ($cmOthersProfile->save()) {
            return response()->json(['success' => true, 'message' => __('label.OTHER_INFO_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_OTHER_INFO')], 401);
        }
        //End updateCmOthersInfo function
    }

    //*********** End :: CM Profile **********************//
}
