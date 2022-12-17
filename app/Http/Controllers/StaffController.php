<?php

namespace App\Http\Controllers;

use Validator;
use App\User; //model class
use App\UserGroup; //model class
use App\Rank; //model class
use App\Appointment; //model class
use App\Wing; //model class
use App\ArmsService; //model class
use App\Religion;
use App\CommissioningCourse; //model class
use App\UserOthers; //model class
use App\Country; //model class
use App\Division; //model class
use App\District; //model class
use App\UserCountryVisit; //model class
use App\Thana; //model class
use App\UserPermanentAddress; //model class
use App\UserBasicProfile;
use App\UserCivilEducation; //model class
use App\UserServiceRecord; //model class
use App\UserRelativeInDefence; //model class
use App\UserChild; //model class
use App\UserPresentAddress; //model class
use App\UserPassport;
use App\Course; //model class
use App\MilCourse;
use App\UserMission;
use App\UserBank;
use App\Decoration;
use App\Hobby;
use App\Staff;
use App\Award;
use App\ServiceAppointment; //model class
use App\Unit; //model class
use Session;
use Response;
use Redirect;
use Auth;
use File;
use PDF;
use URL;
use Hash;
use DB;
use Common;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller {

    private $controller = 'Staff';

    public function __construct() {
        Validator::extend('complexPassword', function($attribute, $value, $parameters) {
            $password = $parameters[1];

            if (preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[!@#$%^&*()])(?=\S*[\d])\S*$/', $password)) {
                return true;
            }
            return false;
        });
    }

    public function index(Request $request) {
        $nameArr = Staff::select('official_name')->where('status', '1')->get();
//        if (Auth::user()->id != 125) {
//            $nameArr = $nameArr->where('staff.id', '<>', 125);
//        }
//        $nameArr = $nameArr->orderBy('group_id', 'asc')->get();
        //passing param for custom function

        $qpArr = $request->all();
//        $userPermissionArr = ['1' => ['1'], //AHQ Observer
//            '3' => ['1', '2', '3', '4', '5', '6', '7', '8'], //SuperAdmin  
//            '5' => ['6', '7', '8'], //admin
//        ];

        $userGroupArr = UserGroup::orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();
        $groupList = array('0' => __('label.SELECT_USER_GROUP_OPT')) + $userGroupArr;
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('status', '1')->where('responsibility', '<>', '2')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();

        $targetArr = Staff::with('rank', 'appointment')
                ->join('appointment', 'appointment.id', '=', 'staff.appointment_id')
                ->join('rank', 'rank.id', '=', 'staff.rank_id')
                ->join('wing', 'wing.id', '=', 'staff.wing_id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'staff.id')
                ->select('wing.code as wing_name'
                        , 'staff.id', 'staff.wing_id'
                        , 'staff.full_name', 'staff.official_name', 'staff.username'
                        , 'staff.personal_no', 'staff.rank_id', 'staff.appointment_id'
                        , 'staff.photo', 'staff.status', 'staff.id', 'staff.email'
                        , 'staff.phone')
                ->orderBy('staff.status', 'asc')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('staff.personal_no', 'asc');

        //begin filtering
//        if (Auth::user()->id != 125) {
//            $targetArr = $targetArr->where('staff.id', '<>', 125);
//        }

        $searchText = $request->fil_search;

//        if (!empty($searchText)) {
        $targetArr->where(function ($query) use ($searchText) {
            $query->where('staff.official_name', 'LIKE', '%' . $searchText . '%')
                    ->orWhere('staff.personal_no', 'LIKE', '%' . $searchText . '%');
        });
//        }

        if (!empty($request->fil_rank_id)) {
            $targetArr = $targetArr->where('staff.rank_id', '=', $request->fil_rank_id);
        }
        if (!empty($request->fil_wing_id)) {
            $targetArr = $targetArr->where('staff.wing_id', '=', $request->fil_wing_id);
        }

        if (!empty($request->fil_appointment_id)) {
            $targetArr = $targetArr->where('staff.appointment_id', '=', $request->fil_appointment_id);
        }
//        if (Auth::user()->group_id == 3) {
//            if (!empty($request->fil_wing_id)) {
//                $targetArr = $targetArr->where('users.wing_id', '=', $request->fil_wing_id);
//            }
//        }
        //end filtering


        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/user?page=' . $page);
        }


        return view('staff.index')->with(compact('qpArr', 'targetArr', 'groupList'
                                , 'rankList', 'appointmentList', 'nameArr', 'wingList'));
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();

        $staffNameArr = Staff::select('staff.full_name')->where('status', '1')->orderBy('id', 'desc')->get();

        $userGroupArr = UserGroup::orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();
        $groupList = array('0' => __('label.SELECT_USER_GROUP_OPT')) + $userGroupArr;
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')->where('wing_id', old('wing_id'))
                        ->pluck('code', 'id')->toArray();
        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT'));
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('status', '1')->where('responsibility', '2')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();

        $religionList = array('0' => __('label.SELECT_RELIGION_OPT')) + Religion::pluck('name', 'id')->toArray();
        $commissioningCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE_OPT'));
        $commissionTypeList = Common::getCommissionType();

        $bloodGroupList = array('0' => __('label.SELECT_BLOOD_GROUP_OPT')) + Common::getBloodGroup();
        $genderList = Common::getGenderList();

        return view('staff.create')->with(compact('qpArr', 'groupList', 'staffNameArr'
                                , 'rankList', 'appointmentList', 'wingList', 'commissioningCourseList'
                                , 'armsServiceList', 'religionList', 'commissionTypeList'
                                , 'bloodGroupList', 'genderList'));
    }

    public function store(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        
        
        

        $pageNumber = $qpArr['filter'];
        $rules1 = [];

        $rules = [
            'wing_id' => 'required|not_in:0',
            'rank_id' => 'required|not_in:0',
            'appointment_id' => 'required|not_in:0',
            'personal_no' => [
                'required', Rule::unique('staff')
                        
            ],
            'full_name' => 'required',
            'official_name' => 'required',
            'username' => 'required|alpha_dash|unique:staff',
            'password' => 'required|complex_password:,' . $request->password,
            'conf_password' => 'required|same:password',
        ];


        if (!empty($request->photo)) {
            $rules['photo'] = 'max:1024|mimes:jpeg,png,jpg';
        }

        $messages = array(
            'password.complex_password' => __('label.WEAK_PASSWORD_FOLLOW_PASSWORD_INSTRUCTION'),
        );
        
        

        $validator = Validator::make($request->all(), $rules, $messages);
        
       

        if ($validator->fails()) {
            return redirect('staff/create' . $pageNumber)
                            ->withInput($request->except('photo', 'password', 'conf_password'))
                            ->withErrors($validator);
        }
        
        

//        echo '<pre>';
//        print_r($request);
//        exit;
        //file upload
        $file = $request->file('photo');
        if (!empty($file)) {
            $imagedata = file_get_contents($file);
            // alternatively specify an URL, if PHP settings allow
            $request['encoded_photo'] = base64_encode($imagedata);
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/staff', $fileName);
        }

        $target = new Staff;
        $target->wing_id = $request->wing_id;
        $target->rank_id = $request->rank_id;
        $target->appointment_id = $request->appointment_id;
        $target->personal_no = $request->personal_no;
        $target->full_name = $request->full_name;
        $target->official_name = $request->official_name;
        $target->username = $request->username;
        $target->password = Hash::make($request->password);
        $target->phone_official = $request->phone_official;
        $target->join_date = !empty($request->join_date) ? Helper::dateFormatConvert($request->join_date) : null;
        $target->email = $request->email;
        $target->phone = $request->phone;
        $target->photo = !empty($fileName) ? $fileName : '';
        $target->status = $request->status;

        $request['u_type'] = '2';
		$request['gender'] = '1';
        $request['password'] = $target->password;
        $request['file_name'] = !empty($fileName) ? $fileName : '';
        $request['created_at'] = date('Y-m-d H:i:s');
        $request['created_by'] = Auth::user()->id;
        $request['updated_at'] = date('Y-m-d H:i:s');
        $request['updated_by'] = Auth::user()->id;

        DB::beginTransaction();
        try {
            if ($target->save()) {
                $request['basic_id'] = $target->id;
                $response = Common::sendHttpPost($request, 'cm/store');
				
                
                Staff::where('id', $target->id)->update(['portal_id' => ($response['portal_id'] ?? 0)]);
            }
            DB::commit();
            Session::flash('success', __('label.STAFF_CREATED_SUCCESSFULLY'));
            return redirect('staff');
        } catch (\Throwable $e) {
            DB::rollback();
            Session::flash('error', __('label.STAFF_COULD_NOT_BE_CREATED'));
            return redirect('staff/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {

        $qpArr = $request->all();


        $target = Staff::leftJoin('user_basic_profile', 'user_basic_profile.user_id', '=', 'staff.id')
                        ->select('staff.id', 'staff.wing_id', 'staff.rank_id'
                                , 'staff.appointment_id', 'staff.personal_no', 'staff.full_name'
                                , 'staff.official_name', 'staff.username', 'staff.password', 'staff.join_date'
                                , 'staff.phone_official', 'staff.email', 'staff.phone', 'staff.photo'
                                , 'staff.status', 'user_basic_profile.father_name'
                                , 'user_basic_profile.arms_service_id'
                                , 'user_basic_profile.commissioning_course_id'
                                , 'user_basic_profile.commission_type', 'user_basic_profile.commisioning_date'
                                , 'user_basic_profile.date_of_birth', 'user_basic_profile.birth_place', 'user_basic_profile.gender'
                                , 'user_basic_profile.religion_id', 'user_basic_profile.blood_group'
                                , 'user_basic_profile.ante_date_seniority')
                        ->where('staff.id', $id)->first();
        $staffNameArr = Staff::select('staff.full_name')->where('status', '1')->orderBy('id', 'desc')->get();

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('staff');
        }

        //passing param for custom function
        $userGroupArr = UserGroup::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        $groupList = array('0' => __('label.SELECT_USER_GROUP_OPT')) + $userGroupArr;
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')->where('wing_id', $target->wing_id)
                        ->pluck('code', 'id')->toArray();
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('status', '1')->where('responsibility', '2')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();

        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->where('wing_id', $target->wing_id)
                        ->pluck('code', 'id')->toArray();

        $commissioningCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE_OPT')) + CommissioningCourse::orderBy('commissioning_date', 'asc')
                        ->where('wing_id', $target->wing_id)
                        ->pluck('name', 'id')->toArray();

        $religionList = array('0' => __('label.SELECT_RELIGION_OPT')) + Religion::pluck('name', 'id')->toArray();
        $commissionTypeList = Common::getCommissionType();
        $genderList = Common::getGenderList();
        $bloodGroupList = array('0' => __('label.SELECT_BLOOD_GROUP_OPT')) + Common::getBloodGroup();

        return view('staff.edit')->with(compact('target', 'qpArr', 'groupList', 'rankList', 'appointmentList', 'genderList'
                                , 'staffNameArr', 'wingList', 'armsServiceList', 'religionList', 'commissioningCourseList', 'commissionTypeList', 'bloodGroupList'));
    }

    public function update(Request $request, $id) {

        $target = Staff::find($id);
        $previousFileName = $target->photo;

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
            'password.complex_password' => __('label.WEAK_PASSWORD_FOLLOW_PASSWORD_INSTRUCTION'),
        );

        $rules = [
            'wing_id' => 'required|not_in:0',
            'rank_id' => 'required|not_in:0',
            'appointment_id' => 'required|not_in:0',
            'personal_no' => 'required|unique:staff,personal_no,' . $id,
            'full_name' => 'required',
            'official_name' => 'required',
            'username' => 'required|alpha_dash|unique:staff,username,' . $id
        ];

        if (!empty($request->password)) {
            $rules['password'] = 'complex_password:,' . $request->password;
            $rules['conf_password'] = 'same:password';
        }


        $validator = Validator::make($request->all(), $rules, $messages);


        if (!empty($request->photo)) {
            $validator->photo = 'max:1024|mimes:jpeg,png,gif,jpg';
        }

        if ($validator->fails()) {
            return redirect('staff/' . $id . '/edit' . $pageNumber)
                            ->withInput($request->all)
                            ->withErrors($validator);
        }

        if (!empty($request->photo)) {
            $prevfileName = 'public/uploads/staff/' . $target->photo;

            if (File::exists($prevfileName)) {
                File::delete($prevfileName);
            }
        }

        $file = $request->file('photo');
        if (!empty($file)) {
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/staff', $fileName);
//            echo '<pre>';print_r($fileName);exit;
        }

        $target->wing_id = $request->wing_id;
        $target->rank_id = $request->rank_id;
        $target->appointment_id = $request->appointment_id;
        $target->personal_no = $request->personal_no;
        $target->full_name = $request->full_name;
        $target->official_name = $request->official_name;
        $target->username = $request->username;
        if (!empty($request->password)) {
            $target->password = Hash::make($request->password);
        }
        $target->phone_official = $request->phone_official;
        $target->join_date = !empty($request->join_date) ? Helper::dateFormatConvert($request->join_date) : null;
        $target->email = $request->email;
        $target->phone = $request->phone;
        $target->photo = !empty($fileName) ? $fileName : $previousFileName;
        $target->status = $request->status;

        $request['u_type'] = '2';
        $request['portal_id'] = $target->portal_id;
        $request['basic_id'] = $target->id;
        $request['file_name'] = !empty($fileName) ? $fileName : $previousFileName;
        $request['updated_at'] = date('Y-m-d H:i:s');
        $request['updated_by'] = Auth::user()->id;


        DB::beginTransaction();
        try {
            if ($target->save()) {
                $response = Common::sendHttpPost($request, 'cm/update');
            }
            DB::commit();
            Session::flash('success', __('label.STAFF_UPDATED_SUCCESSFULLY'));
            return redirect('staff');
        } catch (\Throwable $e) {
            DB::rollback();
            Session::flash('error', __('label.STAFF_COULD_NOT_BE_UPDATED'));
            return redirect('staff/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {

        $target = Staff::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }



        $fileName = 'public/uploads/staff/' . $target->photo;
        if (File::exists($fileName)) {
            File::delete($fileName);
        }



        DB::beginTransaction();
        try {
            if ($target->delete()) {
                $request['id'] = $target->portal_id;
                $request['u_type'] = '2';
                $request['photo'] = $target->photo;
                $response = Common::sendHttpPost($request, 'cm/destroy');
            }
            DB::commit();
            Session::flash('error', __('label.STAFF_HAS_BEEN_DELETED_SUCCESSFULLY'));
            return redirect('staff');
        } catch (\Throwable $e) {
            DB::rollback();
            Session::flash('error', __('label.STAFF_COULD_NOT_BE_DELETED'));
            return redirect('staff' . $pageNumber);
        }
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fil_group_id=' . $request->fil_group_id . '&fil_rank_id=' . $request->fil_rank_id .
                '&fil_appointment_id=' . $request->fil_appointment_id . '&fil_wing_id=' . $request->fil_wing_id;
        return Redirect::to('user?' . $url);
    }

    public function getWing(Request $request) {
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('name', 'id')->toArray();
        $html = view('user.getWing', compact('wingList'))->render();

        return Response::json(['success' => true, 'html' => $html]);
    }

    public function getRank(Request $request) {
        $loadView = 'user.getRank';
        $loadView1 = 'user.getArmsSvc';
        $loadView2 = 'user.getCommissioningCourse';
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->where('wing_id', $request->wing_id)
                        ->pluck('code', 'id')->toArray();

        $armsServiceList = array('0' => __('label.SELECT_ARMS_SERVICE_OPT')) + ArmsService::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->where('wing_id', $request->wing_id)
                        ->pluck('code', 'id')->toArray();

        $commissioningCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE_OPT')) + CommissioningCourse::orderBy('commissioning_date', 'asc')
                        ->where('wing_id', $request->wing_id)
                        ->pluck('name', 'id')->toArray();

        $view = view($loadView, compact('rankList'))->render();
        $view1 = view($loadView1, compact('armsServiceList'))->render();
        $view2 = view($loadView2, compact('commissioningCourseList'))->render();
        return response()->json(['html' => $view, 'html1' => $view1, 'html2' => $view2]);
    }

    public function getCommisioningDate(Request $request) {
        return Common::getCommisioningDate($request);
    }

    public function changePassword() {
        return view('user.changePassword');
    }

    public function updatePassword(Request $request) {
        $target = User::find(Auth::user()->id);

        $rules = [
            'password' => 'required|complex_password:,' . $request->password,
            'conf_password' => 'required|same:password',
        ];
        $messages = array(
            'password.complex_password' => __('label.WEAK_PASSWORD_FOLLOW_PASSWORD_INSTRUCTION'),
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('changePassword')
                            ->withInput($request->except('current_password', 'password', 'conf_password'))
                            ->withErrors($validator);
        }

        $target->password = Hash::make($request->password);
        if ($target->save()) {
            Session::flash('success', __('label.PASSWORD_UPDATED_SUCCESSFULLY'));
            return redirect('dashboard');
        } else {
            Session::flash('error', __('label.PASSWORD_COULD_NOT_BE_UPDATED'));
            return view('user.changePassword');
        }
    }

    public function myProfile(Request $request) {

        $id = Auth::user()->id;

        $user = User::select('group_id')->where('id', $id)->first();
        if (($user->group_id == '1') || ($user->group_id == '2')) {
            Session::flash('error', __('label.NOT_AUTHORIZED'));
            return redirect('user');
        }

        $keyAppt = [];
        $qpArr = $request->all();
        $userInfoData = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', '=', 'users.id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                ->leftJoin('religion', 'religion.id', '=', 'user_basic_profile.religion_id')
                ->select('users.id as users_id', 'users.email', 'users.join_Date', 'users.username'
                        , 'users.photo', 'users.phone', 'users.full_name', 'users.personal_no'
                        , 'users.official_name', 'users.phone_official', 'users.join_date', 'users.rank_id as rank_id'
                        , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (', users.official_name, ')') as username")
                        , 'arms_service.name as arms_service_name', 'commissioning_course.name as commissioning_course_name'
                        , 'religion.name as religion_name', 'user_basic_profile.id as user_basic_profile_id'
                        , 'user_basic_profile.*', 'users.phone_official', 'wing.id as wing_id', 'wing.code as wing_name')
//                ->where('users.status', '1')
                ->where('users.id', $id)
                ->first();
//        $userInfoData = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', '=', 'users.id')
//                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
//                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
//                ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
//                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
//                ->leftJoin('religion', 'religion.id', '=', 'user_basic_profile.religion_id')
//                ->select('users.id as users_id', 'users.email'
//                        , 'users.photo', 'users.phone', 'users.full_name', 'users.personal_no'
//                        , 'users.official_name'
//                        , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (', users.official_name, ')') as username"), 'user_basic_profile.commissioning_course_id as commissioning_course_id', 'user_basic_profile.commission_type as commissioning_type'
//                        , 'arms_service.*', 'users.rank_id as rank_id', 'user_basic_profile.arms_Service_id as arms_Service_id', 'religion.name as religion_name', 'user_basic_profile.id as user_basic_profile_id', 'user_basic_profile.commisioning_date', 'user_basic_profile.ante_date_seniority'
//                        , 'user_basic_profile.*', 'users.*', 'wing.id as wing_id', 'wing.code as wing_name')
//                ->where('users.status', '1')
//                ->where('users.id', $id)
//                ->first();
//        echo '<pre>';        print_r($userInfoData->toArray()); exit;
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $civilEducationInfoData = UserCivilEducation::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'civil_education_info')
                ->first();

        $serviceRecordInfoData = UserServiceRecord::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'service_record_info')
                ->first();

        $msnDataInfo = UserMission::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'msn_info')
                ->first();
        if (!empty($serviceRecordInfoData)) {
            $serviceRecordInfo = json_decode($serviceRecordInfoData->service_record_info, TRUE);
            if (!empty($serviceRecordInfo)) {
                foreach ($serviceRecordInfo as $skey => $serviceRecord) {
                    $keyAppt[$serviceRecord['appointment']] = $serviceRecord['appointment'];
                }
            }
        }
        if (!empty($msnDataInfo)) {
            $msnData = json_decode($msnDataInfo->msn_info, TRUE);
            if (!empty($msnData)) {
                foreach ($msnData as $mkey => $msn) {
                    $keyAppt[$msn['appointment']] = $msn['appointment'];
                }
            }
        }


        $countryVisitDataInfo = UserCountryVisit::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'visit_info')
                ->first();

        $bankInfoData = UserBank::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'bank_info')
                ->first();

        $childInfoData = UserChild::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'user_child_info', 'no_of_child')
                ->first();

        $defenceRelativeInfoData = UserRelativeInDefence::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'user_relative_info')
                ->first();

        $milQualification = [];
        if (!empty($defenceRelativeInfoData)) {
            $milQualArr = !empty($defenceRelativeInfoData->user_relative_info) ? json_decode($defenceRelativeInfoData->user_relative_info, true) : [];
            if (!empty($milQualArr)) {
                foreach ($milQualArr as $mKey => $mInfo) {
                    $type = Common::getMilCourseType($mInfo['course']);
                    $milQualification[$type][$mKey] = $mInfo;
                }
            }
        }

        $othersInfoData = UserOthers::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'decoration_id', 'hobby_id', 'extra_curriclar_expt')
                ->first();
        $passportInfoData = UserPassport::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'passport_no', 'place_of_issue', 'date_of_issue', 'date_of_expire', 'pass_scan_copy', 'pass_scan_copy_name')
                ->first();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();

        $commissionTypeList = Common::getCommissionType();
        $bloodGroupList = Common::getBloodGroup();

        $decorationList = Decoration::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $awardList = Award::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $hobbyList = Hobby::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $religionList = ['0' => __('label.SELECT_RELIGION_OPT')] + Religion::pluck('name', 'id')->toArray();

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
        $comCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE')) + CommissioningCourse::where('status', '1')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $bloodGroupList = ['0' => __('label.SELECT_BLOOD_GROUP')] + Common::getBloodGroup();


        //Division District Thana for user permanent address
        $addressInfo = UserPermanentAddress::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : '0')
                ->select('id', 'user_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details', 'same_as_present')
                ->first();
        $presentAddressInfo = UserPresentAddress::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : '0')
                ->select('id', 'user_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details')
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

        $spouseProfession = Common::getSpouseProfessionList();
        $genderList = Common::getGenderList();

        $respList = Common::getSvcResposibilityList();
        $resultList = Common::getResultList();

        return view('user.details.index')->with(compact('userInfoData', 'comCourseList', 'commissionTypeList', 'armsServiceList', 'rankList', 'wingList', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo', 'genderList'
                                , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData', 'religionList'
                                , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList', 'bloodGroupList'
                                , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                , 'spouseProfession', 'respList', 'milQualification', 'resultList'));
    }

    public function accountSetting(Request $request) {

        $userId = Auth::user()->id;

        $target = User::with('rank', 'appointment')
                        ->join('user_group', 'user_group.id', '=', 'users.group_id')
                        ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                        ->select('user_group.category_id', 'user_group.name as group_name', 'wing.code as wing_name'
                                , 'users.group_id', 'users.id', 'users.wing_id'
                                , 'users.full_name', 'users.official_name', 'users.username'
                                , 'users.personal_no', 'users.rank_id', 'users.appointment_id'
                                , 'users.photo', 'users.phone', 'users.email')
                        ->orderBy('user_group.order', 'asc')
                        ->where('users.id', $userId)->first();

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
            return redirect('dashboard');
        }

        $userPermissionArr = ['1' => ['1'], //AHQ Observer
            '3' => ['1', '2', '3', '4', '5', '6', '7', '8'], //SuperAdmin  
            '5' => ['6', '7', '8'], //admin
        ];

        $userGroupArr = UserGroup::join('user_category', 'user_category.id', '=', 'user_group.category_id')
                ->select('user_category.name as user_category_name', 'user_group.id'
                        , 'user_group.category_id', 'user_group.name as user_group_name')
                ->whereIn('user_group.id', $userPermissionArr[Auth::user()->group_id])
                ->orderBy('user_group.order', 'asc')
                ->get();

        $groupListArr = array();
        foreach ($userGroupArr as $userGroup) {
            $groupListArr[$userGroup->user_category_name][$userGroup->id] = $userGroup->user_group_name;
        }
        $groupList = array('0' => __('label.SELECT_USER_GROUP_OPT')) + $groupListArr;
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('code', 'id')->toArray();
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('status', '1')->where('responsibility', '3')
                        ->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();


        return view('user.accountSetting')->with(compact('target', 'groupList', 'rankList', 'appointmentList', 'wingList'));
    }

    public function updateProfile(Request $request) {
        $id = Auth::user()->id;
        $target = User::find($id);
        $rules = $messages = [];
        if ($request->update_id == 1) {
            //update personal info

            $rules = [
                'group_id' => 'required|not_in:0',
                'rank_id' => 'required|not_in:0',
                'appointment_id' => 'required|not_in:0',
                'personal_no' => 'required',
                'full_name' => 'required',
                'official_name' => 'required',
                'username' => 'required|alpha_dash|unique:users,username,' . $id
            ];

            if ($request->group_id >= 4) {
                $rules['wing_id'] = 'required|not_in:0';
            }


            $target->group_id = $request->group_id;

            if ($request->group_id >= 4) {
                $target->wing_id = $request->wing_id;
            } else {
                $target->wing_id = null;
            }
            $target->rank_id = $request->rank_id;
            $target->appointment_id = $request->appointment_id;
            $target->personal_no = $request->personal_no;
            $target->full_name = $request->full_name;
            $target->official_name = $request->official_name;
            $target->username = $request->username;
            if (!empty($request->password)) {
                $target->password = Hash::make($request->password);
            }
            $target->email = $request->email;
            $target->phone = $request->phone;
        } elseif ($request->update_id == 2) {
            //update photo
            $validator = Validator::make($request->all(), $rules);
            if (!empty($request->photo)) {
                $validator->photo = 'max:1024|mimes:jpeg,png,gif,jpg';
            }

            if ($validator->fails()) {
                return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
            }

            //delete previous photo from image folder if upload new photo
            if (!empty($request->photo)) {
                $prevfileName = 'public/uploads/user/' . $target->photo;

                if (File::exists($prevfileName)) {
                    File::delete($prevfileName);
                }
            }

            $file = $request->file('photo');
            if (!empty($file)) {
                $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
                $uploadSuccess = $file->move('public/uploads/user', $fileName);
            }
            $target->photo = !empty($fileName) ? $fileName : $previousFileName;
        } elseif ($request->update_id == 3) {
            //update password
            $rules['password'] = 'required|complex_password:,' . $request->password;
            $rules['conf_password'] = 'same:password';

            $messages = array(
                'password.complex_password' => __('label.WEAK_PASSWORD_FOLLOW_PASSWORD_INSTRUCTION'),
            );
            if (!empty($request->password)) {
                $target->password = Hash::make($request->password);
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        if ($target->save()) {
            return Response::json(['success' => true, 'message' => __('label.USER_UPDATED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.USER_COULD_NOT_BE_UPDATED')), 401);
        }
    }

    public function getProfileWing(Request $request) {
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->pluck('name', 'id')->toArray();
        $html = view('user.getProfileWing', compact('wingList'))->render();

        return Response::json(['success' => true, 'html' => $html]);
    }

    public function setRecordPerPage(Request $request) {

        $referrerArr = explode('?', URL::previous());
        $queryStr = '';
        if (!empty($referrerArr[1])) {
            $queryParam = explode('&', $referrerArr[1]);
            foreach ($queryParam as $item) {
                $valArr = explode('=', $item);
                if ($valArr[0] != 'page') {
                    $queryStr .= $item . '&';
                }
            }
        }

        $url = $referrerArr[0] . '?' . trim($queryStr, '&');

        if ($request->record_per_page > 999) {
            Session::flash('error', __('label.NO_OF_RECORD_MUST_BE_LESS_THAN_999'));
            return redirect($url);
        }

        if ($request->record_per_page < 1) {
            Session::flash('error', __('label.NO_OF_RECORD_MUST_BE_GREATER_THAN_1'));
            return redirect($url);
        }

        $request->session()->put('paginatorCount', $request->record_per_page);
        return redirect($url);
    }

    //*********** Start :: User Profile **********************//
    public function profile(Request $request, $id) {

        $user = User::select('group_id')->where('id', $id)->first();
        if (($user->group_id == '1') || ($user->group_id == '2')) {
            Session::flash('error', __('label.NOT_AUTHORIZED'));
            return redirect('user');
        }

        $keyAppt = [];
        $qpArr = $request->all();

        $userInfoData = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', '=', 'users.id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                ->leftJoin('religion', 'religion.id', '=', 'user_basic_profile.religion_id')
                ->select('users.id as users_id', 'users.email', 'users.join_Date', 'users.username'
                        , 'users.photo', 'users.phone', 'users.full_name', 'users.personal_no'
                        , 'users.official_name', 'users.phone_official', 'users.join_date', 'users.rank_id as rank_id'
                        , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (', users.official_name, ')') as username")
                        , 'arms_service.name as arms_service_name', 'commissioning_course.name as commissioning_course_name'
                        , 'religion.name as religion_name', 'user_basic_profile.id as user_basic_profile_id'
                        , 'user_basic_profile.*', 'users.phone_official', 'wing.id as wing_id', 'wing.code as wing_name')
//                ->where('users.status', '1')
                ->where('users.id', $id)
                ->first();

//        echo '<pre>';        print_r($userInfoData->toArray()); exit;
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();

        $civilEducationInfoData = UserCivilEducation::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'civil_education_info')
                ->first();

        $serviceRecordInfoData = UserServiceRecord::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'service_record_info')
                ->first();

        $msnDataInfo = UserMission::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'msn_info')
                ->first();
        if (!empty($serviceRecordInfoData)) {
            $serviceRecordInfo = json_decode($serviceRecordInfoData->service_record_info, TRUE);
            if (!empty($serviceRecordInfo)) {
                foreach ($serviceRecordInfo as $skey => $serviceRecord) {
                    $keyAppt[$serviceRecord['appointment']] = $serviceRecord['appointment'];
                }
            }
        }
        if (!empty($msnDataInfo)) {
            $msnData = json_decode($msnDataInfo->msn_info, TRUE);
            if (!empty($msnData)) {
                foreach ($msnData as $mkey => $msn) {
                    $keyAppt[$msn['appointment']] = $msn['appointment'];
                }
            }
        }


        $countryVisitDataInfo = UserCountryVisit::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'visit_info')
                ->first();

        $bankInfoData = UserBank::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'bank_info')
                ->first();

        $childInfoData = UserChild::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'user_child_info', 'no_of_child')
                ->first();

        $defenceRelativeInfoData = UserRelativeInDefence::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'user_relative_info')
                ->first();

        $milQualification = [];
        if (!empty($defenceRelativeInfoData)) {
            $milQualArr = !empty($defenceRelativeInfoData->user_relative_info) ? json_decode($defenceRelativeInfoData->user_relative_info, true) : [];
            if (!empty($milQualArr)) {
                foreach ($milQualArr as $mKey => $mInfo) {
                    $type = Common::getMilCourseType($mInfo['course']);
                    $milQualification[$type][$mKey] = $mInfo;
                }
            }
        }

        $othersInfoData = UserOthers::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'decoration_id', 'hobby_id', 'extra_curriclar_expt')
                ->first();
        $passportInfoData = UserPassport::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : 0)
                ->select('id', 'user_basic_profile_id', 'passport_no', 'place_of_issue', 'date_of_issue', 'date_of_expire', 'pass_scan_copy', 'pass_scan_copy_name')
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

        $comCourseList = array('0' => __('label.SELECT_COMMISSIONING_COURSE')) + CommissioningCourse::where('status', '1')
                        ->orderBy('id', 'desc')
                        ->pluck('name', 'id')
                        ->toArray();
        $bloodGroupList = ['0' => __('label.SELECT_BLOOD_GROUP')] + Common::getBloodGroup();

        //Division District Thana for user permanent address
        $addressInfo = UserPermanentAddress::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : '0')
                ->select('id', 'user_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details', 'same_as_present')
                ->first();
        $presentAddressInfo = UserPresentAddress::where('user_basic_profile_id', !empty($userInfoData->id) ? $userInfoData->id : '0')
                ->select('id', 'user_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details')
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

        $spouseProfession = Common::getSpouseProfessionList();
        $genderList = Common::getGenderList();

        $respList = Common::getSvcResposibilityList();
        $resultList = Common::getResultList();


        return view('user.details.index')->with(compact('userInfoData', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                , 'spouseProfession', 'respList', 'milQualification', 'wingList', 'genderList'
                                , 'comCourseList', 'bloodGroupList', 'rankList', 'resultList'));
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

        $userBasicProfile = UserBasicProfile::find($request->user_basic_profile_id);
        $userBasicProfile->marital_status = $request->marital_status;
        $userBasicProfile->date_of_marriage = !empty($request->date_of_marriage) ? Helper::dateFormatConvert($request->date_of_marriage) : null;
        $userBasicProfile->spouse_dob = !empty($request->spouse_date_of_birth) ? Helper::dateFormatConvert($request->spouse_date_of_birth) : null;
        $userBasicProfile->spouse_name = $request->spouse_name ?? null;
        $userBasicProfile->spouse_nick_name = $request->spouse_nick_name ?? null;
        $userBasicProfile->spouse_mobile = $request->spouse_mobile ?? null;
        $userBasicProfile->spouse_occupation = $request->spouse_occupation ?? null;
        $userBasicProfile->spouse_work_address = $request->spouse_work_address ?? null;

        $userChildInfo = UserChild::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $userChildInfo = !empty($userChildInfo->id) ? UserChild::find($userChildInfo->id) : new UserChild;

        $childInfo = !empty($request->child) ? json_encode($request->child) : '';
        $userChildInfo->user_basic_profile_id = $request->user_basic_profile_id;
        $userChildInfo->no_of_child = !empty($request->no_of_child) ? $request->no_of_child : 0;
        $userChildInfo->user_child_info = $childInfo;
        $userChildInfo->updated_at = date('Y-m-d H:i:s');
        $userChildInfo->updated_by = Auth::user()->id;

        if ($userBasicProfile->save() && $userChildInfo->save()) {
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
        $htmldistrict = view('user.details.districts')->with(compact('districtList'))->render();
        $htmlThana = view('user.details.thana')->with(compact('thanaList'))->render();
        return response()->json(['html' => $htmldistrict, 'htmlThana' => $htmlThana]);
        //End getDistrict function
    }

    //For Thana
    public function getThana(Request $request) {
        $thanaList = ['0' => __('label.SELECT_THANA_OPT')] + THANA::where('district_id', $request->district_id)->pluck('name', 'id')->toArray();
        $htmlThana = view('user.details.thana')->with(compact('thanaList'))->render();
        return response()->json(['html' => $htmlThana]);
        //End getThana function
    }

    public function updatePhoto(Request $request) {
        $target = User::find($request->user_basic_profile_id);
        $previousFileName = $target->photo;

        $rules = $messages = [];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!empty($request->photo)) {
            $validator->photo = 'max:1024|mimes:jpeg,png,gif,jpg';
        }

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        if (!empty($request->photo)) {
            $prevfileName = 'public/uploads/user/' . $target->photo;

            if (File::exists($prevfileName)) {
                File::delete($prevfileName);
            }
        }

        $file = $request->file('photo');
        if (!empty($file)) {
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/user', $fileName);
//            echo '<pre>';print_r($fileName);exit;
        }

        $target->photo = !empty($fileName) ? $fileName : $previousFileName;

        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;

        if ($target->save()) {
            return response()->json(['success' => true, 'message' => __('label.PHOTO_UPDATED_SUCCESSFULLY')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_PHOTO')], 401);
        }
        //Update user civil education
        if ($civilEducationProfile->save()) {
            return response()->json(['success' => true, 'message' => __('label.ACADEMIC_QUALIFICATION_INFO_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_ACADEMIC_QUAL')], 401);
        }
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
        $userPermanentAddress = UserPermanentAddress::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $userPermanentAddressInfo = !empty($userPermanentAddress->id) ? UserPermanentAddress::find($userPermanentAddress->id) : new UserPermanentAddress;

        $userPresentAddressInfo = UserPresentAddress::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $userPresentAddressInfo = !empty($userPresentAddressInfo->id) ? UserPresentAddress::find($userPresentAddressInfo->id) : new UserPresentAddress;

        $userPresentAddressInfo->user_basic_profile_id = $request->user_basic_profile_id;
        $userPresentAddressInfo->division_id = $request->present_division_id;
        $userPresentAddressInfo->district_id = $request->present_district_id;
        $userPresentAddressInfo->thana_id = $request->present_thana_id;
        $userPresentAddressInfo->address_details = $request->present_address_details;
        $userPresentAddressInfo->updated_at = date('Y-m-d H:i:s');
        $userPresentAddressInfo->updated_by = Auth::user()->id;

        if (!empty($request->for_addr)) {
            $userPermanentAddressInfo->user_basic_profile_id = $request->user_basic_profile_id;
            $userPermanentAddressInfo->division_id = $request->present_division_id;
            $userPermanentAddressInfo->district_id = $request->present_district_id;
            $userPermanentAddressInfo->thana_id = $request->present_thana_id;
            $userPermanentAddressInfo->address_details = $request->present_address_details;
            $userPermanentAddressInfo->same_as_present = $request->for_addr ?? '0';
            $userPermanentAddressInfo->updated_at = date('Y-m-d H:i:s');
            $userPermanentAddressInfo->updated_by = Auth::user()->id;
        } else {
            $userPermanentAddressInfo->user_basic_profile_id = $request->user_basic_profile_id;
            $userPermanentAddressInfo->division_id = $request->permanent_division_id;
            $userPermanentAddressInfo->district_id = $request->permanent_district_id;
            $userPermanentAddressInfo->thana_id = $request->permanent_thana_id;
            $userPermanentAddressInfo->address_details = $request->permanent_address_details;
            $userPermanentAddressInfo->same_as_present = $request->for_addr ?? '0';
            $userPermanentAddressInfo->updated_at = date('Y-m-d H:i:s');
            $userPermanentAddressInfo->updated_by = Auth::user()->id;
        }

        if ($userPermanentAddressInfo->save() && $userPresentAddressInfo->save()) {
            return response()->json(['success' => true, 'message' => __('label.CM_ADDRESS_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_ADDRESS')], 401);
        }
        //End updatePermanentAddress function
    }

    public function rowAddForCivilEducation() {
        $html = view('user.details.civilEducationRowAdd')->render();
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
                $rules['academic_qual.' . $key . '.year'] = 'required';
//                $rules['academic_qual.' . $key . '.qual_erode'] = 'required';

                $messages['academic_qual.' . $key . '.institute_name.required'] = __('label.INSTITUTE_NAME_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['academic_qual.' . $key . '.examination.required'] = __('label.EXAMINATION_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['academic_qual.' . $key . '.year.required'] = __('label.YEAR_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
//                $messages['academic_qual.' . $key . '.qual_erode.required'] = __('label.QUALIFICATION_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $civilEducationInfo = UserCivilEducation::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $civilEducationProfile = !empty($civilEducationInfo->id) ? UserCivilEducation::find($civilEducationInfo->id) : new UserCivilEducation;



        $civilEducation = json_encode($request->academic_qual);
        $civilEducationProfile->user_basic_profile_id = $request->user_basic_profile_id;
        $civilEducationProfile->civil_education_info = $civilEducation;
        $civilEducationProfile->updated_at = date('Y-m-d H:i:s');
        $civilEducationProfile->updated_by = Auth::user()->id;

        //Update user civil education
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

        $respList = Common::getSvcResposibilityList();

        $html = view('user.details.serviceRecordRowAdd')->with(compact('appointmentList', 'unitList'
                                , 'organizationList', 'respList'))->render();
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

        $serviceEducationInfo = UserServiceRecord::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $serviceEducationProfile = !empty($serviceEducationInfo->id) ? UserServiceRecord::find($serviceEducationInfo->id) : new UserServiceRecord;

        $serviceRecord = json_encode($request->service_record);
        $serviceEducationProfile->user_basic_profile_id = $request->user_basic_profile_id;
        $serviceEducationProfile->service_record_info = $serviceRecord;
        $serviceEducationProfile->updated_at = date('Y-m-d H:i:s');
        $serviceEducationProfile->updated_by = Auth::user()->id;

        //Update user service record
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
        $html = view('user.details.unMsnRowAdd', compact('appointmentList'))->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForCountry() {
        $html = view('user.details.newVisitedCountryRow')->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForChild(Request $request) {
        $noOfChild = $request->no_of_child;
        $html = view('user.details.newChildRow', compact('noOfChild'))->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForBank() {
        $html = view('user.details.newBankRow')->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function updateUnMsn(Request $request) {
        //Check Validation for Punishment Record Information
        $rules = $messages = [];
        if (!empty($request->un_msn)) {
            $row = 1;
            foreach ($request->un_msn as $key => $unMsn) {

                $rules['un_msn.' . $key . '.msn'] = 'required';
                $rules['un_msn.' . $key . '.appointment'] = 'required|not_in:0';


                $messages['un_msn.' . $key . '.msn.required'] = __('label.MSN_FIELD_IS_REQUIRED', ["counter" => $row]);
                $messages['un_msn.' . $key . '.appointment.not_in'] = __('label.APPOINTMENT_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $unMsnInfo = UserMission::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $unMsnInfo = !empty($unMsnInfo->id) ? UserMission::find($unMsnInfo->id) : new UserMission;

        $msnRecord = json_encode($request->un_msn);
        $unMsnInfo->user_basic_profile_id = $request->user_basic_profile_id;
        $unMsnInfo->msn_info = $msnRecord;
        $unMsnInfo->updated_at = date('Y-m-d H:i:s');
        $unMsnInfo->updated_by = Auth::user()->id;

        //Update user punishment record
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
        $visitInfo = UserCountryVisit::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $visitInfo = !empty($visitInfo->id) ? UserCountryVisit::find($visitInfo->id) : new UserCountryVisit;

        $visitRecord = json_encode($request->country_visit);
        $visitInfo->user_basic_profile_id = $request->user_basic_profile_id;
        $visitInfo->visit_info = $visitRecord;
        $visitInfo->updated_at = date('Y-m-d H:i:s');
        $visitInfo->updated_by = Auth::user()->id;

        //Update user punishment record
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
        $bankInfo = UserBank::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $bankInfo = !empty($bankInfo->id) ? UserBank::find($bankInfo->id) : new UserBank;

        $bankRecord = json_encode($request->bank);
        $bankInfo->user_basic_profile_id = $request->user_basic_profile_id;
        $bankInfo->bank_info = $bankRecord;
        $bankInfo->updated_at = date('Y-m-d H:i:s');
        $bankInfo->updated_by = Auth::user()->id;

        //Update user punishment record
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
        $resultList = Common::getResultList();
        $html = view('user.details.defenceRelativeRowAdd')->with(compact('milCourseList', 'resultList'))->render();
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
        $defenceRecordInfo = UserRelativeInDefence::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $defenceRecordProfile = !empty($defenceRecordInfo->id) ? UserRelativeInDefence::find($defenceRecordInfo->id) : new UserRelativeInDefence;

        $defenceRecord = json_encode($request->mil_qual);
        $defenceRecordProfile->user_basic_profile_id = $request->user_basic_profile_id;
        $defenceRecordProfile->user_relative_info = $defenceRecord;
        $defenceRecordProfile->updated_at = date('Y-m-d H:i:s');
        $defenceRecordProfile->updated_by = Auth::user()->id;

        //Update user punishment record
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

        if (!empty($request->pass_scan_copy)) {
            $rules['pass_scan_copy'] = 'max:2048|mimes:pdf';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $passportInfo = UserPassport::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        if (!empty($request->pass_scan_copy)) {
            $prevfileName = 'public/uploads/userPassport/' . $passportInfo->pass_scan_copy;

            if (File::exists($prevfileName)) {
                File::delete($prevfileName);
            }
        }

        $user = UserBasicProfile::join('users', 'users.id', 'user_basic_profile.user_id')
                ->where('user_basic_profile.id', $request->user_basic_profile_id)
                ->select('users.personal_no')
                ->first();


        $file = $request->file('pass_scan_copy');
        $fileName = $fileOriginalName = '';
        if (!empty($file)) {
            // alternatively specify an URL, if PHP settings allow
            $fileName = Common::getFileFormatedName($user->personal_no . "_" . $request->passport_no);
            $fileName = $fileName . "." . $file->getClientOriginalExtension();
            $fileOriginalName = $file->getClientOriginalName();
            $uploadSuccess = $file->move('public/uploads/userPassport', $fileName);
        }


        $scanCopy = !empty($fileName) ? $fileName : (!empty($passportInfo->pass_scan_copy) ? $passportInfo->pass_scan_copy : null);
        $scanCopyName = !empty($fileOriginalName) ? $fileOriginalName : (!empty($passportInfo->pass_scan_copy_name) ? $passportInfo->pass_scan_copy_name : null);

        $passportInfo = !empty($passportInfo->id) ? UserPassport::find($passportInfo->id) : new UserPassport;

        $passportInfo->user_basic_profile_id = $request->user_basic_profile_id;
        $passportInfo->passport_no = $request->passport_no;
        $passportInfo->place_of_issue = $request->place_of_issue;
        $passportInfo->date_of_issue = Helper::dateFormatConvert($request->date_of_issue);
        $passportInfo->date_of_expire = Helper::dateFormatConvert($request->date_of_expire);
        $passportInfo->pass_scan_copy = $scanCopy;
        $passportInfo->pass_scan_copy_name = $scanCopyName;
        $passportInfo->updated_at = date('Y-m-d H:i:s');
        $passportInfo->updated_by = Auth::user()->id;

        if ($passportInfo->save()) {
            return response()->json(['success' => true, 'message' => __('label.PASSPORT_DETAILS_INFO_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_PASSPORT_DETAILS')], 401);
        }
        //End updateMedicalDetails function
    }

    public function updateUserOthersInfo(Request $request) {
        $userOthersInfo = UserOthers::select('id')->where('user_basic_profile_id', $request->user_basic_profile_id)->first();
        $userOthersProfile = !empty($userOthersInfo->id) ? UserOthers::find($userOthersInfo->id) : new UserOthers;

        $decorationInfo = !empty($request->decoration) ? $request->decoration : '';
        $exCurInfo = !empty($request->extra_curriclar_expt) ? $request->extra_curriclar_expt : '';
        $userOthersProfile->user_basic_profile_id = $request->user_basic_profile_id;
        $userOthersProfile->decoration_id = $decorationInfo;
        $userOthersProfile->extra_curriclar_expt = $exCurInfo;
        $userOthersProfile->updated_at = date('Y-m-d H:i:s');
        $userOthersProfile->updated_by = Auth::user()->id;

        if ($userOthersProfile->save()) {
            return response()->json(['success' => true, 'message' => __('label.OTHER_INFO_UPDATED')], 200);
        } else {
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATED_OTHER_INFO')], 401);
        }
        //End updateUserOthersInfo function
    }

    public function updatePersonalInfo(Request $request) {
        $basicProId = $request->user_basic_profile_id;
        $newTarget = UserBasicProfile::find($basicProId);
        $userId = $newTarget->user_id;
        $target = User::find($userId);
//        $previousFileName = $target->photo;
        //begin back same page after update
        $qpArr = $request->all();
        //end back same page after update
        $messages = array();
        $rules = [
            'wing_id' => 'required|not_in:0',
            'rank_id' => 'required|not_in:0',
            'personal_no' => 'required|unique:users,personal_no,' . $userId,
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
            'username' => 'required|alpha_dash|unique:users,username,' . $userId
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $target->personal_no = $request->personal_no;
        $target->full_name = $request->full_name;
        $target->official_name = $request->official_name;
        $target->username = $request->username;
        $target->phone = $request->phone;
        $target->phone_official = $request->phone_official;
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;

        DB::beginTransaction();
        try {
            if ($target->save()) {

                $newTarget->user_id = $target->id;
                $newTarget->arms_service_id = $request->arms_service_id;
                $newTarget->father_name = $request->father_name;
                $newTarget->commission_type = $request->commission_type;
                $newTarget->commissioning_course_id = $request->commissioning_course_id;
                $newTarget->ante_date_seniority = $request->ante_date_seniority ?? null;
                $newTarget->commisioning_date = Helper::dateFormatConvert($request->commisioning_date);
                $newTarget->date_of_birth = Helper::dateFormatConvert($request->date_of_birth);
                $newTarget->birth_place = $request->birth_place;
                $newTarget->religion_id = $request->religion_id;
                $newTarget->blood_group = $request->blood_group;
                $newTarget->gender = $request->gender;
                $newTarget->save();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => __('label.PERSONAL_INFO_UPDATED_SUCCESSFULLY')], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => __('label.FAILED_TO_UPDATE_PERSONAL_INFO')], 401);
        }
    }

    //*********** End :: CM Profile **********************//
}
