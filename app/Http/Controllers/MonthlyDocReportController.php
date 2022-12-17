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

class MonthlyDocReportController extends Controller {

    public function index(Request $request) {
        $monthList = Common::getMonthList();
        
        $contentModuleList = ['0' => __('label.SELECT_MODULE_OPT')] + Module::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        $contentDetailsInfoArr = [];
        $targetArr = [];
        if ($request->generate == 'true') {
            $contentArr = Content::leftJoin('users', 'users.id', '=', 'content.originator')
                    ->leftJoin('cm_basic_profile', 'cm_basic_profile.id', '=', 'content.originator')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('module', 'module.id', 'content.module_id')
                    ->leftJoin('staff', 'staff.id', '=', 'content.originator')
                    ->join('content_classification', 'content_classification.id', 'content.content_classification_id')
                    ->join('content_category', 'content_category.id', 'content.content_category_id')
                    ->orderBy('content.date_upload', 'desc');

            $month = !empty($request->month) ? $request->month : '';
            if (!empty($month)) {
                $contentArr = $contentArr->whereMonth('date_upload', '=', $month)
                        ->whereYear('date_upload', '=', date("Y"));
            }

            $contentArr = $contentArr->select('content.id', 'content.title', 'content.originator', 'content.date_upload', 'content.origin'
                    , 'content.short_description', 'content.course_id', 'content.content_classification_id', 'content.output_access'
                    , 'content.content_category_id', 'content.status', 'content_category.related_compartment', 'users.id as user_id'
                    , 'users.official_name as user_official_name', DB::raw("CONCAT(rank.code ,' ', cm_basic_profile.official_name ) as cm_official_name")
                    , 'staff.official_name as staff_official_name','module.name as module_name'
                    , 'content_category.name as content_cat', 'content_classification.name as content_classification_name'
                    , 'content_classification.icon as content_classification_icon'
                    , 'content_classification.color as content_classification_color');
            // start: ds will get only active courses contents check
            $courseInfo = Course::where('status', '1')
                    ->select('id')
                    ->first();
            if (Auth::user()->group_id == '4') {
                $contentArr = $contentArr->where('content.course_id', $courseInfo->id);
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
            $fileName = 'Monthly_Document_Upload_Report';
            $fileName = Common::getFileFormatedName($fileName);
        }

        if ($request->view == 'print') {
            return view('referenceArchive.monthlyDocReport.print.index')->with(compact('monthList', 'request', 'contentModuleList', 'contentDetailsInfoArr', 'targetArr'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('referenceArchive.monthlyDocReport.print.index', compact('monthList', 'request', 'contentDetailsInfoArr', 'contentModuleList', 'targetArr'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);

            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('referenceArchive.monthlyDocReport.print.index', compact('monthList', 'request'
                                    , 'contentDetailsInfoArr', 'contentModuleList', 'targetArr'), 3), $fileName . '.xlsx');
        } else {
            return view('referenceArchive.monthlyDocReport.index', compact('monthList', 'request', 'contentModuleList', 'contentDetailsInfoArr', 'targetArr'));
        }
    }

    public function filter(Request $request) {
//        print_r($request->all());
        $rules = $messages = [];
        $url = 'month=' . $request->month;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('monthlyDocReport?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }

        return redirect('monthlyDocReport?generate=true&' . $url);
    }
    
    public function downloadFile(Request $request) {
        return Common::downloadFile($request);
    }

}
