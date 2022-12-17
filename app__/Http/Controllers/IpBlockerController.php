<?php

namespace App\Http\Controllers;

use App\User;
use App\IpBlocker;
use App\Configurable;
use DB;
use Auth;
use Session;
use Response;
use Validator;
use Common;
use Illuminate\Http\Request;

class IpBlockerController extends Controller {

    private $controller = 'IpBlocker';

    public function __construct() {
        
    }

    public function index() {
        $userList = User::leftJoin('ip_blocker', 'ip_blocker.user_id', 'users.id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('appointment', 'appointment.id', '=', 'users.appointment_id')
				->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->where('users.status', '1')
                ->select(DB::raw("CONCAT(rank.code, ' ', users.full_name, ' ') as user_name"), 'users.official_name'
                        , 'users.id', 'ip_blocker.ip')
                ->orderBy('users.group_id', 'asc')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();
        $target = Configurable::find(1);

        return view('ipBlocker.index')->with(compact('userList', 'target'));
    }

    public function saveIP(Request $request) {
        
//        echo '<pre>';
//        print_r($request->all());
//        exit;

        $ipArr = $request->ip;
        $userArr = $request->user;

        if (!empty($ipArr)) {
            foreach ($ipArr as $userId => $ip) {
                $rules['ip.' . $userId] = 'required|ip';
                $message['ip.' . $userId . '.required'] = __('label.IP_IS_REQUIRED_FOR', ['user' => $userArr[$userId]]);

                if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                    $message['ip.' . $userId . '.ip'] = __('label.IP_GIVEN_FOR_USER_IS_NOT_VALID', ['user' => $userArr[$userId]]);
                }
            }
        }
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $data = [];
        $i = 0;
        if (!empty($ipArr)) {
            foreach ($ipArr as $userId => $ip) {
                $data[$i]['user_id'] = $userId;
                $data[$i]['ip'] = $ip;
                $data[$i]['updated_by'] = Auth::user()->id;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $i++;
            }
        }
//            echo '<pre>';
//            print_r($data);
//            exit;
        IpBlocker::truncate();
        if (IpBlocker::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_SET_USER_VALID_LOGIN_IP')), 401);
        }
    }

    public function configure(Request $request) {
        
        $target = Configurable::find(1);
        $target->configurable = !empty($request->configurable) ? $request->configurable : '0';
        $target->updated_by = Auth::user()->id;
        $target->updated_at = date('Y-m-d H:i:s');
        if ($target->save()) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_CONFIGUR_SUCCESSFULLY')), 401);
        }
    }

}
