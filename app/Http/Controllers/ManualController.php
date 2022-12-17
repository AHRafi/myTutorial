<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Common;
use Auth;
use DB;
use URL;
use Session;
use Redirect;
use Helper;
use Validator;
use Response;
use App\User;

class ManualController extends Controller {

    public function index() {
        $manualArr = array('1' => 'Admin', '2' => 'Comdt', '3' => 'CI', '4' => 'DS');
        
        $deligationDs = Common::getDsDeligationList();
        
        $userGroup = in_array(Auth::user()->id,$deligationDs) ? 'Delegated-DS' : $manualArr[Auth::user()->group_id];
        
        $file = 'public/manual/' . $userGroup . '-Manual-for-AFWC-Assessment-Software-Version-V.1.1.pdf';
        
        
        if (file_exists($file)) {
            $content = file_get_contents($file);
            return Response::make($content, 200, array('content-type' => 'application/pdf'));
        } else {
            return Response::view('error');
        }
    }

}
