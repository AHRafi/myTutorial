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

class DocumentSearchReportController extends Controller {

    public function index(Request $request) {

        $qpArr = $request->data;

        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $origin = !empty($qpArr['origin']) ? $qpArr['origin'] : '2';

        $explodeOriginatorId = !empty($qpArr['originator_id']) ? explode("_", $qpArr['originator_id']) : [];

        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        $activeCourse = Course::where('training_year_id', $activeTrainingYearList->id ?? 0)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        $archiveData['contentModuleList'] = ['0' => __('label.SELECT_MODULE_OPT')] + Module::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        $archiveData['courseList'] = ['0' => __('label.SELECT_COURSE_OPT')] + Course::orderBy('training_year_id', 'desc')->pluck('name', 'id')->toArray();
        $archiveData['contentCategoryList'] = ['0' => __('label.SELECT_CONTENT_CATEGORY')] + ContentCategory::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $archiveData['contentClassificationList'] = ['0' => __('label.SELECT_CONTENT_CLASSIFICATION')] + ContentClassification::orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $markingDsList = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('users', 'users.id', 'ds_marking_group.ds_id')
                ->where('marking_group.course_id', $activeCourse->id ?? 0)
                ->where('users.status', '1')
                ->pluck('users.id', 'users.id')
                ->toArray();

        //ds list
        $dsList = User::leftJoin('rank', 'rank.id', 'users.rank_id')
                ->leftJoin('appointment', 'appointment.id', 'users.appointment_id')
                ->where('users.group_id', 4)->whereIn('users.id', $markingDsList)
                ->select('users.official_name', DB::raw("CONCAT(1, '_', users.id) as ds_id"))
                ->orderBy('users.status', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->pluck('users.official_name', 'ds_id')
                ->toArray();

        //staff
        $cmList = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->where('cm_basic_profile.course_id', $activeCourse->id ?? 0)
                ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name) as cm_name")
                        , DB::raw("CONCAT(2, '_', cm_basic_profile.id) as cm_id"))
                ->orderBy('cm_basic_profile.course_id', 'desc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->pluck('cm_name', 'cm_id')
                ->toArray();

        //staff list
        $staffList = Staff::leftJoin('rank', 'rank.id', 'staff.rank_id')
                ->where('staff.status', '1')
                ->select('staff.official_name', DB::raw("CONCAT(3, '_', staff.id) as staff_id"))
                ->orderBy('staff.status', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('staff.personal_no', 'asc')
                ->pluck('staff.official_name', 'staff_id')
                ->toArray();


        $archiveData['originatorList'] = ['0' => __('label.SELECT_ORIGINATOR')] + $dsList + $cmList + $staffList;
        $archiveData['titleArr'] = Content::where('status', 1)->select('title')->get();



//return response()->json(['result' => $archiveData, 'message' => $authRes['message'], 'status' => $authRes['status']]);

        $archiveData['sortByList'] = [
            'title' => __('label.ALPHABETICALLY'),
            'module' => __('label.MODULE_SUBJECT'),
            'classification' => __('label.CLASSIFICATION'),
            'category' => __('label.CATEGORY'),
            'dou_asc' => __('label.DATE_OF_UPLOAD') . ' (' . __('label.ASCENDING_ORDER') . ')',
            'dou_desc' => __('label.DATE_OF_UPLOAD') . ' (' . __('label.DESCENDING_ORDER') . ')',
        ];


        $archiveData['targetArr'] = [];
        if (!empty($qpArr['generate']) && $qpArr['generate'] == 'true') {
            $contentArr = Content::leftJoin('users', 'users.id', '=', 'content.originator')
                    ->leftJoin('cm_basic_profile', 'cm_basic_profile.id', '=', 'content.originator')
                    ->leftJoin('staff', 'staff.id', '=', 'content.originator')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->join('module', 'module.id', 'content.module_id')
                    ->join('content_classification', 'content_classification.id', 'content.content_classification_id')
                    ->join('content_category', 'content_category.id', 'content.content_category_id');


            if (!empty($explodeOriginatorId)) {
                $contentArr = $contentArr->where('content.origin', $explodeOriginatorId[0]);
                $contentArr = $contentArr->where('content.originator', $explodeOriginatorId[1]);
            }
            if (!empty($qpArr['title'])) {
                $contentArr = $contentArr->where('content.title', $qpArr['title']);
            }
            if (!empty($qpArr['short_des'])) {
                $contentArr = $contentArr->where('content.short_description', 'LIKE', '%' . $qpArr['short_des'] . '%');
            }

            if (!empty($qpArr['con_category'])) {
                $contentArr = $contentArr->where('content.content_category_id', $qpArr['con_category']);
            }
            if (!empty($qpArr['con_classification'])) {
                $contentArr = $contentArr->where('content.content_classification_id', $qpArr['con_classification']);
            }
            if (!empty($qpArr['con_module'])) {
                $contentArr = $contentArr->where('content.module_id', $qpArr['con_module']);
            }

            $uploadDateFrom = !empty($qpArr['date_of_upload_from']) ? date("Y-m-d", strtotime($qpArr['date_of_upload_from'])) : '';
            $uploadDateTo = !empty($qpArr['date_of_upload_to']) ? date("Y-m-d", strtotime($qpArr['date_of_upload_to'])) : '';

            if (!empty($uploadDateFrom) && !empty($uploadDateTo)) {
                $contentArr = $contentArr->whereBetween('content.created_at', [$uploadDateFrom, $uploadDateTo]);
            } else {

                if (!empty($uploadDateFrom)) {
                    $contentArr = $contentArr->where('content.created_at', '>=', $uploadDateFrom);
                }
                if (!empty($uploadDateTo)) {
                    $contentArr = $contentArr->where('content.created_at', '<=', $uploadDateTo);
                }
            }

            $contentArr = $contentArr->where('content.course_id', $activeCourse->id ?? 0)
                    ->select('content.id', 'content.title', 'content.originator', 'content.date_upload', 'content.origin'
                    , 'content.short_description', 'content.course_id', 'content.content_classification_id'
                    , 'content.content_category_id', 'content.status', 'content_category.related_compartment', 'users.id as user_id'
                    , 'users.official_name as user_official_name', DB::raw("CONCAT(rank.code ,' ', cm_basic_profile.official_name ) as cm_official_name")
                    , 'staff.official_name as staff_official_name', 'module.name as module_name', 'content.output_access'
                    , 'content_category.name as content_cat', 'content_classification.name as content_classification_name'
                    , 'content_classification.icon as content_classification_icon'
                    , 'content_classification.color as content_classification_color');



            if (!empty($qpArr['sort'])) {
                if ($qpArr['sort'] == 'dou_asc') {
                    $contentArr = $contentArr->orderBy('content.date_upload', 'asc');
                } elseif ($qpArr['sort'] == 'dou_desc') {
                    $contentArr = $contentArr->orderBy('content.date_upload', 'desc');
                } elseif ($qpArr['sort'] == 'title') {
                    $contentArr = $contentArr->orderBy('content.title', 'asc');
                } elseif ($qpArr['sort'] == 'module') {
                    $contentArr = $contentArr->orderBy('module.order', 'asc');
                } elseif ($qpArr['sort'] == 'classification') {
                    $contentArr = $contentArr->orderBy('content_classification.order', 'asc');
                } elseif ($qpArr['sort'] == 'category') {
                    $contentArr = $contentArr->orderBy('content_category.order', 'asc');
                }
            } else {
                $contentArr = $contentArr->orderBy('content.date_upload', 'desc')
                        ->orderBy('content.title', 'asc')
                        ->orderBy('content_classification.order', 'asc')
                        ->orderBy('module.order', 'asc');
            }

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

}
