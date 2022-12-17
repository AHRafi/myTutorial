<?php

namespace App\Http\Controllers;

use Validator;
use App\ContentCategory;
use App\Course;
use App\TrainingYear;
use App\Appointment;
use App\CmBasicProfile;
use App\User;
use App\Module;
use App\Content;
use App\ContentDetails;
use App\DsMarkingGroup;
use App\ContentClassification;
use App\Staff;
use Response;
use PDF;
use Auth;
use File;
use DB;
use Helper;
use Common;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class OriginatorWiseDocReportController extends Controller {

    public function index(Request $request) {

        $explodeOriginatorId = !empty($request->originator_id) ? explode("_", $request->originator_id, 2) : $request->originator_id;

        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();
        $activeCourse = Course::where('training_year_id', $activeTrainingYearList->id ?? 0)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        
        $contentModuleList = ['0' => __('label.SELECT_MODULE_OPT')] + Module::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        $markingDsList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('users', 'users.id', 'ds_marking_group.ds_id')
                ->where('marking_group.course_id', $activeCourse->id ?? 0)
                ->where('users.status', '1')
                ->pluck('users.id', 'users.id')
                ->toArray();

        //ds list
        $dsList = User::leftJoin('rank', 'rank.id', 'users.rank_id')
                ->leftJoin('appointment', 'appointment.id', 'users.appointment_id')
                ->where('users.group_id', 4);
        if (Auth::user()->group_id == 4) {
            $dsList = $dsList->whereIn('users.id', $markingDsList);
        }
        $dsList = $dsList->select('users.official_name', DB::raw("CONCAT(1, '_', users.id) as ds_id"))
                ->orderBy('users.status', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->pluck('users.official_name', 'ds_id')
                ->toArray();

        //staff
        $cmList = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id');
        if (Auth::user()->group_id == 4) {
            $cmList = $cmList->where('cm_basic_profile.course_id', $activeCourse->id ?? 0);
        }
        $cmList = $cmList->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name) as cm_name")
                        , DB::raw("CONCAT(2, '_', cm_basic_profile.id) as cm_id"))
                ->orderBy('cm_basic_profile.course_id', 'desc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->pluck('cm_name', 'cm_id')
                ->toArray();

        //staff list
        $staffList = Staff::leftJoin('rank', 'rank.id', 'staff.rank_id');
        if (Auth::user()->group_id == 4) {
            $staffList = $staffList->where('staff.status', '1');
        }
        $staffList = $staffList->select('staff.official_name', DB::raw("CONCAT(3, '_', staff.id) as staff_id"))
                ->orderBy('staff.status', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('staff.personal_no', 'asc')
                ->pluck('staff.official_name', 'staff_id')
                ->toArray();

//echo '<pre>';

        $originatorList = ['0' => __('label.SELECT_ORIGINATOR')] + $dsList + $cmList + $staffList;



        $contentDetailsInfoArr = [];
        $targetArr = [];
        if ($request->generate == 'true') {
            $contentArr = Content::leftJoin('users', 'users.id', '=', 'content.originator')
                    ->leftJoin('cm_basic_profile', 'cm_basic_profile.id', '=', 'content.originator')
                    ->leftJoin('module', 'module.id', 'content.module_id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('staff', 'staff.id', '=', 'content.originator')
                    ->join('content_classification', 'content_classification.id', 'content.content_classification_id')
                    ->join('content_category', 'content_category.id', 'content.content_category_id')
                    ->orderBy('content.date_upload', 'desc');


            if (!empty($explodeOriginatorId)) {
                $contentArr = $contentArr->where('content.origin', $explodeOriginatorId[0]);
                $contentArr = $contentArr->where('content.originator', $explodeOriginatorId[1]);
            }

           $contentArr = $contentArr->select('content.id', 'content.title', 'content.originator', 'content.date_upload', 'content.origin'
                    , 'content.short_description', 'content.course_id', 'content.content_classification_id', 'content.output_access'
                    , 'content.content_category_id', 'content.status', 'content_category.related_compartment', 'users.id as user_id'
                    , 'users.official_name as user_official_name', DB::raw("CONCAT(rank.code ,' ', cm_basic_profile.official_name ) as cm_official_name")
                    , 'staff.official_name as staff_official_name', 'module.name as module_name'
                    , 'content_category.name as content_cat', 'content_classification.name as content_classification_name'
                    , 'content_classification.icon as content_classification_icon'
                    , 'content_classification.color as content_classification_color');
           // start: ds will get only active courses contents check
            $courseInfo = Course::where('status','1')
                          ->select('id')
                          ->first();
            if(Auth::user()->group_id == '4'){
                $contentArr = $contentArr->where('content.course_id',$courseInfo->id);
            }
            // end: ds will get only active courses contents check 

            $targetIdArr = $contentArr->pluck('content.id', 'content.id')->toArray();
            $contentArr = $contentArr->get();

            $contentDetailsArr = ContentDetails::whereIn('content_details.content_id', $targetIdArr)
                            ->select('content_details.*')->get();

            $contentDetailsInfoArr = [];
            if (!$contentDetailsArr->isEmpty()) {
                foreach ($contentDetailsArr as $contentDetails) {
                    $contentDetailsId = !empty($contentDetails->id) ? $contentDetails->id : 0;
                    $contentDetailsInfoArr[$contentDetails->content_id][$contentDetailsId]['content_type'] = $contentDetails->content_type_id;
                    $contentDetailsInfoArr[$contentDetails->content_id][$contentDetailsId]['content'] = $contentDetails->content;
                    $contentDetailsInfoArr[$contentDetails->content_id][$contentDetailsId]['content_original'] = $contentDetails->content_original;
                }
            }

            $targetArr = [];
            if (!$contentArr->isEmpty()) {
                foreach ($contentArr as $item) {
                    $outputAccess = 0;
                    if (Auth::user()->group_id == 4) {
                        $outputAccessArr = !empty($item->output_access) ? explode(',', $item->output_access) : [];
                        if (!empty($outputAccessArr)) {
                            if (in_array('1', $outputAccessArr)) {
                                $outputAccess = 1;
                            }
                        } else {
                            $outputAccess = 1;
                        }
                    } else {
                        $outputAccess = 1;
                    }
                    if ($outputAccess == 1) {
                        $targetArr[$item->id] = $item->toArray();
                        $targetArr[$item->id]['content_details'] = !empty($contentDetailsInfoArr[$item->id]) ? $contentDetailsInfoArr[$item->id] : '';
                    }
                }
            }
            $fileName = 'Originator_Wise_Report';
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('referenceArchive.originatorWiseDocReport.print.index')->with(compact('request','originatorList', 'contentModuleList', 'contentDetailsInfoArr', 'targetArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('referenceArchive.originatorWiseDocReport.print.index', compact('request','originatorList', 'contentDetailsInfoArr', 'contentModuleList', 'targetArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('referenceArchive.originatorWiseDocReport.print.index', compact('request','originatorList'
                                    , 'contentDetailsInfoArr', 'contentModuleList', 'targetArr'), 3), $fileName . '.xlsx');
        } else {

            return view('referenceArchive.originatorWiseDocReport.index', compact('request', 'originatorList', 'contentModuleList', 'contentDetailsInfoArr', 'targetArr'));
        }
    }

    public function filter(Request $request) {
//        print_r($request->all());
        $rules = $messages = [];
        $url = 'originator_id=' . $request->originator_id;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('originatorWiseDocReport?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }

        return redirect('originatorWiseDocReport?generate=true&' . $url);
    }
    
    public function downloadFile(Request $request) {
        return Common::downloadFile($request);
    }

}
