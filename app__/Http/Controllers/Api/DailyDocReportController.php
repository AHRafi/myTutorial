<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

class DailyDocReportController extends Controller {

    public function index(Request $request) {

        $qpArr = $request->data;
        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $origin = !empty($qpArr['origin']) ? $qpArr['origin'] : '2';

        $archiveData['contentModuleList'] = ['0' => __('label.SELECT_MODULE_OPT')] + Module::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $archiveData['targetArr'] = [];



        if (!empty($qpArr['generate']) && $qpArr['generate'] == 'true') {

            $contentArr = Content::leftJoin('users', 'users.id', '=', 'content.originator')
                    ->leftJoin('cm_basic_profile', 'cm_basic_profile.id', '=', 'content.originator')
                    ->leftJoin('module', 'module.id', 'content.module_id')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->leftJoin('staff', 'staff.id', '=', 'content.originator')
                    ->join('content_classification', 'content_classification.id', 'content.content_classification_id')
                    ->join('content_category', 'content_category.id', 'content.content_category_id')
                    ->orderBy('content.date_upload', 'desc');

            $date = !empty($qpArr['date_of_upload']) ? date("Y-m-d", strtotime($qpArr['date_of_upload'])) : '';
            if (!empty($date)) {
                $contentArr = $contentArr->where('date_upload', '=', $date);
            }
            $contentArr = $contentArr->select('content.id', 'content.title', 'content.originator', 'content.date_upload', 'content.origin'
                    , 'content.short_description', 'content.course_id', 'content.content_classification_id'
                    , 'content.content_category_id', 'content.status', 'content_category.related_compartment', 'users.id as user_id'
                    , 'users.official_name as user_official_name', DB::raw("CONCAT(rank.code ,' ', cm_basic_profile.official_name ) as cm_official_name")
                    , 'staff.official_name as staff_official_name', 'module.name as module_name', 'content.output_access'
                    , 'content_category.name as content_cat', 'content_classification.name as content_classification_name'
                    , 'content_classification.icon as content_classification_icon'
                    , 'content_classification.color as content_classification_color');


            $courseInfo = Course::where('status', '1')
                    ->select('id')
                    ->first();

            $archiveData['course_info'] = $courseInfo;
            $contentArr = $contentArr->where('content.course_id', $courseInfo->id);

            $targetIdArr = $contentArr->pluck('content.id', 'content.id')->toArray();
            $archiveData['targetIdArr'] = $targetIdArr;
            $contentArr = $contentArr->get();
            $archiveData['contentArr'] = $contentArr;


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
            $archiveData['contentDetailsArr'] = $contentDetailsArr;


            $targetArr = [];
            if (!$contentArr->isEmpty()) {
                foreach ($contentArr as $item) {
                    $outputAccess = 0;
                    $outputAccessArr = !empty($item->output_access) ? explode(',', $item->output_access) : [];
                    if (!empty($outputAccessArr)) {
                        if (in_array($origin, $outputAccessArr)) {
                            $outputAccess = 1;
                        }
                    } else {
                        $outputAccess = 1;
                    }
                    if ($outputAccess == 1) {
                        $archiveData['targetArr'][$item->id] = $item->toArray();
                        $archiveData['targetArr'][$item->id]['content_details'] = !empty($contentDetailsInfoArr[$item->id]) ? $contentDetailsInfoArr[$item->id] : '';
                    }
                }
            }
        }

        return response()->json(['result' => $archiveData, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

//    public function filter(Request $request) {
////        print_r($request->all());
//        $rules = $messages = [];
//        $url = 'date_of_upload=' . $request->date_of_upload;
//
//        $validator = Validator::make($request->all(), $rules, $messages);
//        if ($validator->fails()) {
//            return redirect('dailyDocReport?generate=false&' . $url)
//                            ->withInput()
//                            ->withErrors($validator);
//        }
//
//        return redirect('dailyDocReport?generate=true&' . $url);
//    }
}
