<?php

namespace App\Http\Controllers\Admin;

use DB;
use URL;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\DashboardSetup;
use Helper;
use Response;

class DashboardSetupController extends Controller {

    public function __construct() {
        //$this->middleware('auth');
    }

    public function index(Request $request) {
        $userId = Auth::user()->id;
        $groupId = Auth::user()->group_id;
        $userDashboard = DashboardSetup::select('*')->where('user_id', $userId)->where('group_id', $groupId)->first();
        
        $dashboardLayout = [];
        if(!empty($userDashboard)){
            $dashboardLayout = json_decode($userDashboard->layout, true);
        }
        
        return view('dashboardSetup.index')->with(compact('userDashboard', 'dashboardLayout'));
    }
    
    public function setDashboardLayout(Request $request){
        $userId = Auth::user()->id;
        $groupId = Auth::user()->group_id;
        $userDashboard = DashboardSetup::select('id')->where('user_id', $userId)->where('group_id', $groupId)->first();
        
        if(!empty($userDashboard)){
            $target = DashboardSetup::find($userDashboard->id);
        }else{
            $target = new DashboardSetup;
        }
        
        $target->user_id = $userId;
        $target->group_id = $groupId;
        $target->layout = $request->content_list;
        $target->updated_at = date("Y-m-d H:i:s");
        
        if ($target->save()) {
            return Response::json(array('heading' => 'Success', 'message' => __('label.DASHBOARD_LAYOUT_SET_SUCCESSFULLY')), 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FAILED_TO_SET_DASHBOARD_LAYOUT')), 401);
        }
    }

}
