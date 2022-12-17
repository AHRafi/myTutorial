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

class ProcessManualController extends Controller
{
    public function index() {
        $file = 'public/processWiseManual/Process-Manual-for-AFWC-Assessment-Software-Version-V.1.1.pdf';
        
        
        if (file_exists($file)) {
            $content = file_get_contents($file);
            return Response::make($content, 200, array('content-type' => 'application/pdf'));
        } else {
            return Response::view('error');
        }
    }
}
