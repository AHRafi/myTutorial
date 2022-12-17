<?php

namespace App\Http\Controllers;

use Validator;
use App\User; //model class
use App\UserGroup; //model class
use App\Rank; //model class
use App\Appointment; //model class
use App\Wing; //model class
use Session;
use Response;
use Redirect;
use Auth;
use File;
use PDF;
use URL;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller {

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
        $nameArr = User::select('full_name')->where('status', '1')->orderBy('group_id', 'asc')->get();

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
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $targetArr = User::with('rank', 'appointment')
                ->join('user_group', 'user_group.id', '=', 'users.group_id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
                ->join('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->select('user_group.name as group_name', 'wing.code as wing_name'
                        , 'users.group_id', 'users.id', 'users.wing_id'
                        , 'users.full_name', 'users.official_name', 'users.username'
                        , 'users.personal_no', 'users.rank_id', 'users.appointment_id'
                        , 'users.photo', 'users.status', 'users.id', 'users.email'
                        , 'users.phone')
                ->orderBy('users.status', 'asc')
                ->orderBy('user_group.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc');

        //begin filtering
        $searchText = $request->fil_search;

//        if (!empty($searchText)) {
        $targetArr->where(function ($query) use ($searchText) {
            $query->where('users.full_name', 'LIKE', '%' . $searchText . '%');
        });
//        }

        if (!empty($request->fil_group_id)) {
            $targetArr = $targetArr->where('users.group_id', '=', $request->fil_group_id);
        }

        if (!empty($request->fil_rank_id)) {
            $targetArr = $targetArr->where('users.rank_id', '=', $request->fil_rank_id);
        }
        if (!empty($request->fil_wing_id)) {
            $targetArr = $targetArr->where('users.wing_id', '=', $request->fil_wing_id);
        }

        if (!empty($request->fil_appointment_id)) {
            $targetArr = $targetArr->where('users.appointment_id', '=', $request->fil_appointment_id);
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


        return view('user.index')->with(compact('qpArr', 'targetArr', 'groupList'
                                , 'rankList', 'appointmentList', 'nameArr', 'wingList'));
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();

        $userNameArr = User::select('users.full_name')->where('status', '1')->orderBy('id', 'desc')->get();

        $userGroupArr = UserGroup::orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();
        $groupList = array('0' => __('label.SELECT_USER_GROUP_OPT')) + $userGroupArr;
        $rankList = array('0' => __('label.SELECT_RANK_OPT'));
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();


        return view('user.create')->with(compact('qpArr', 'groupList', 'userNameArr'
                                , 'rankList', 'appointmentList', 'wingList'));
    }

    public function store(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();

        $pageNumber = $qpArr['filter'];

        $rules = [
            'group_id' => 'required|not_in:0',
            'wing_id' => 'required|not_in:0',
            'rank_id' => 'required|not_in:0',
            'appointment_id' => 'required|not_in:0',
            'personal_no' => [
                'required', Rule::unique('users')
                        ->where('group_id', $request->group_id)
            ],
            'full_name' => 'required',
            'official_name' => 'required',
            'username' => 'required|alpha_dash|unique:users',
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

            return redirect('user/create' . $pageNumber)
                            ->withInput($request->except('photo', 'password', 'conf_password'))
                            ->withErrors($validator);
        }



        //file upload
        $file = $request->file('photo');
        if (!empty($file)) {
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/user', $fileName);
        }

        $target = new User;
        $target->group_id = $request->group_id;
        $target->wing_id = $request->wing_id;
        $target->rank_id = $request->rank_id;
        $target->appointment_id = $request->appointment_id;
        $target->personal_no = $request->personal_no;
        $target->full_name = $request->full_name;
        $target->official_name = $request->official_name;
        $target->username = $request->username;
        $target->password = Hash::make($request->password);
        $target->extension_no = $request->extension_no;
        $target->email = $request->email;
        $target->phone = $request->phone;
        $target->photo = !empty($fileName) ? $fileName : '';
        $target->status = $request->status;

        if ($target->save()) {
            Session::flash('success', __('label.USER_CREATED_SUCCESSFULLY'));
            return redirect('user');
        } else {
            Session::flash('error', __('label.USER_COULD_NOT_BE_CREATED'));
            return redirect('user/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {

        $qpArr = $request->all();
        $target = User::find($id);
        $userNameArr = User::select('users.full_name')->where('status', '1')->orderBy('id', 'desc')->get();

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('user');
        }

        //passing param for custom function
        $userGroupArr = UserGroup::orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();
        $groupList = array('0' => __('label.SELECT_USER_GROUP_OPT')) + $userGroupArr;
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')->where('wing_id', $target->wing_id)
                        ->pluck('code', 'id')->toArray();
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();


        return view('user.edit')->with(compact('target', 'qpArr', 'groupList', 'rankList', 'appointmentList'
                                , 'userNameArr', 'wingList'));
    }

    public function update(Request $request, $id) {

        $target = User::find($id);
        $previousFileName = $target->photo;

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
            'password.complex_password' => __('label.WEAK_PASSWORD_FOLLOW_PASSWORD_INSTRUCTION'),
        );

        $rules = [
            'group_id' => 'required|not_in:0',
            'wing_id' => 'required|not_in:0',
            'rank_id' => 'required|not_in:0',
            'appointment_id' => 'required|not_in:0',
            'personal_no' => 'required|unique:users,personal_no,' . $id . ',id,group_id,' . $request->group_id,
            'full_name' => 'required',
            'official_name' => 'required',
            'username' => 'required|alpha_dash|unique:users,username,' . $id
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
            return redirect('user/' . $id . '/edit' . $pageNumber)
                            ->withInput($request->all)
                            ->withErrors($validator);
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

        $target->group_id = $request->group_id;
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
        $target->extension_no = $request->extension_no;
        $target->email = $request->email;
        $target->phone = $request->phone;
        $target->photo = !empty($fileName) ? $fileName : $previousFileName;
        $target->status = $request->status;



        if ($target->save()) {
            Session::flash('success', __('label.USER_UPDATED_SUCCESSFULLY'));
            return redirect('user' . $pageNumber);
        } else {
            Session::flash('error', __('label.USER_COULD_NOT_BE_UPDATED'));
            return redirect('user/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {

        $target = User::find($id);

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
            'User' => ['1' => 'created_by', '2' => 'updated_by'],
            'TrainingYear' => ['1' => 'created_by', '2' => 'updated_by'],
            'Wing' => ['1' => 'created_by', '2' => 'updated_by'],
            'Term' => ['1' => 'created_by', '2' => 'updated_by'],
            'Event' => ['1' => 'created_by', '2' => 'updated_by'],
//            'OicToCourse' => ['1' => 'oic_id', '2' => 'updated_by'],
            'Syndicate' => ['1' => 'created_by', '2' => 'updated_by'],
            'SynToCourse' => ['1' => 'updated_by'],
            'Course' => ['1' => 'created_by', '2' => 'updated_by'],
            'TermToCourse' => ['1' => 'updated_by'],
            'TermToEvent' => ['1' => 'updated_by'],
        ];
        foreach ($dependencyArr as $model => $val) {
            foreach ($val as $index => $key) {
                $namespacedModel = '\\App\\' . $model;
                $dependentData = $namespacedModel::where($key, $id)->first();
                if (!empty($dependentData)) {
                    Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                    return redirect('user' . $pageNumber);
                }
            }
        }

        $fileName = 'public/uploads/user/' . $target->photo;
        if (File::exists($fileName)) {
            File::delete($fileName);
        }

        if ($target->delete()) {
            Session::flash('error', __('label.USER_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.USER_COULD_NOT_BE_DELETED'));
        }
        return redirect('user' . $pageNumber);
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
        $rankList = array('0' => __('label.SELECT_RANK_OPT')) + Rank::orderBy('order', 'asc')
                        ->where('status', '1')
                        ->where('wing_id', $request->wing_id)
                        ->pluck('code', 'id')->toArray();
        $html = view('user.getRank', compact('rankList'))->render();

        return Response::json(['success' => true, 'html' => $html]);
    }

    public function changePassword() {
        return view('user.changePassword');
    }

    public function updatePassword(Request $request) {
        $target = User::find(Auth::user()->id);

        $rules = [
            //'current_password' => 'required',
            'password' => 'required|complex_password:,' . $request->password,
            'conf_password' => 'required',
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
            return redirect('changePassword');
        } else {
            Session::flash('error', __('label.PASSWORD_COULD_NOT_BE_UPDATED'));
            return view('user.changePassword');
        }
    }

    public function myProfile(Request $request) {

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

        return view('user.myProfile')->with(compact('target'));
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
                        ->where('status', '1')
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

}
