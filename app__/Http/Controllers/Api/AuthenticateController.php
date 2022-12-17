<?php

namespace App\Http\Controllers\Api;

use DB;
use URL;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\CmBasicProfile;
use Helper;
use Hash;
use Common;

class AuthenticateController extends Controller {

    public function __construct() {
        //$this->middleware('auth');
    }

    public function authenticate(Request $request) {
        
        $userType = $request->data['user_type'];
        $username = $request->data['username'];
        $password = $request->data['password'];
        
        
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        } elseif ($authRes['status'] == 200) {
            
            if($userType == '1'){
               $authorizedTable = '\\App\\CmBasicProfile';
            }else if($userType == '2'){
                $authorizedTable = '\\App\\Staff';
            }
            $user = $authorizedTable::where('username', $username)->first();
            
            if (empty($user) || (!empty($user) && !Hash::check($password, $user->password))) {
                $authRes['status'] = 419;
                $authRes['message'] = __('label.THESE_CREDENTIALS_DO_NOT_MATCH_OUR_RECORDS');
                $user = [];
            } elseif (!empty($user) && $user->status == '2') {
                $authRes['status'] = 419;
                $authRes['message'] = __('label.FAILED_TO_LOGIN_USER_INACTIVE');
                $user = [];
            }
            
            
            return response()->json(['result' => $user, 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }
    }
}
