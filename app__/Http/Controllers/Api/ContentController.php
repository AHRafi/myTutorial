<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use App\Event;
use App\EventToSubEvent;
use App\EventToSubSubEvent;
use App\EventToSubSubSubEvent;
use App\TermToEvent;
use App\TrainingYear;
use App\TermToSubEvent;
use App\Course;
use App\Module;
use App\Content;
use App\Staff;
use App\MediaContentType;
use App\ContentCategory;
use App\ContentDetails;
use App\ContentClassification;
use DB;
use App\CmBasicProfile;
use Session;
use Helper;
use PDF;
use Redirect;
use Common;
use Auth;
use File;
use Response;
use Illuminate\Http\Request;

class ContentController extends Controller {

    private $controller = 'Content';

    public function __construct() {
        
    }

    public function index(Request $request) {


        $qpArr = $request->data;

        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }
        $nameArr = Content::select('title')->orderBy('date_upload', 'desc')->get();
        $contentData['name_arr'] = $nameArr;

        $contentTypeList = MediaContentType::orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();
        $contentData['content_type_list'] = $contentTypeList;

        $categoryList = ContentCategory::where('status', '1')->orderBy('name', 'asc')
                        ->pluck('name', 'id')->toArray();
        $contentData['category_list'] = $categoryList;

        $compartmentList = Common::getArchiveCompartmentList();
        $contentData['output_access'] = $compartmentList;

        $contentClassificationList = ContentClassification::orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();
        $contentData['classification_list'] = $contentClassificationList;

//passing param for custom function


        $contentInfo = Content::join('content_classification', 'content_classification.id', 'content.content_classification_id')
                ->join('content_category', 'content_category.id', 'content.content_category_id')
                ->join('module', 'module.id', 'content.module_id');
        if ($qpArr['origin'] == '2') {
            $contentInfo = $contentInfo->leftJoin('cm_basic_profile', 'cm_basic_profile.id', '=', 'content.originator')
                    ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                    ->select('content.id', 'content.title', 'content.originator', 'content.date_upload'
                    , 'content.short_description', 'content.course_id', 'content.content_classification_id'
                    , 'content.content_category_id', 'content.status', 'content_category.related_compartment'
                    , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name) as originator_name")
                    , 'content_category.name as content_cat', 'module.name as module_name'
                    , 'content_classification.name as content_classification_name'
                    , 'content_classification.icon as content_classification_icon', 'content.output_access'
                    , 'content_classification.color as content_classification_color');
        } elseif ($qpArr['origin'] == '3') {
            $contentInfo = $contentInfo->leftJoin('staff', 'staff.id', '=', 'content.originator')
                    ->select('content.id', 'content.title', 'content.originator', 'content.date_upload'
                    , 'content.short_description', 'content.course_id', 'content.output_access', 'content.content_classification_id'
                    , 'content.content_category_id', 'content.status', 'content_category.related_compartment'
                    , 'staff.official_name as originator_name', 'content_category.name as content_cat'
                    , 'content_classification.name as content_classification_name', 'content.output_access'
                    , 'content_classification.icon as content_classification_icon'
                    , 'content_classification.color as content_classification_color', 'module.name as module_name');
        }
        $contentInfo = $contentInfo->where('content.origin', $qpArr['origin'])
                ->where('content.originator', $qpArr['originator'])
                ->orderBy('content.status', 'asc')
                ->orderBy('content.date_upload', 'desc');
//begin filtering
        $searchText = !empty($qpArr['fil_search']) ? $qpArr['fil_search'] : '';
        if (!empty($searchText)) {
            $contentInfo->where(function ($query) use ($searchText) {
                $query->where('content.title', 'LIKE', '%' . $searchText . '%');
            });
        }

//end filtering
        $targetIdArr = $contentInfo->pluck('content.id', 'content.id')->toArray();
        $contentInfo = $contentInfo->get();

        $contentData['content_info'] = $contentInfo;

//change page number after delete if no data has current page


        $contentDetailsArr = ContentDetails::whereIn('content_details.content_id', $targetIdArr)
                        ->orderBy('content_order', 'asc')
                        ->select('content_details.*')->get();

        $contentDetailsInfoArr = [];
        if (!$contentDetailsArr->isEmpty()) {
            foreach ($contentDetailsArr as $contentDetails) {
                $contentDetailsId = !empty($contentDetails->id) ? $contentDetails->id : 0;
                $contentDetailsInfoArr[$contentDetails->content_id][$contentDetailsId]['content_type'] = $contentDetails->content_type_id;
                $contentDetailsInfoArr[$contentDetails->content_id][$contentDetailsId]['content'] = $contentDetails->content;
                $contentDetailsInfoArr[$contentDetails->content_id][$contentDetailsId]['content_original'] = $contentDetails->content_original;
                $contentDetailsInfoArr[$contentDetails->content_id][$contentDetailsId]['content_order'] = $contentDetails->content_order;
            }
        }

        $contentData['target_arr'] = [];
        if (!$contentInfo->isEmpty()) {
            foreach ($contentInfo as $item) {
                $contentData['target_arr'][$item->id] = $item->toArray();
                $contentData['target_arr'][$item->id]['content_details'] = !empty($contentDetailsInfoArr[$item->id]) ? $contentDetailsInfoArr[$item->id] : '';
            }
        }

        return response()->json(['result' => $contentData, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function create(Request $request) {
//begin back same page after update
        $qpArr = $request->data;

//        $contentData['qp_Arr'] = $qpArr;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);
        if ($authRes['status'] == 419) {
            return response()->json(['message' => $authRes['message'], 'status' => $authRes['status']]);
        }


        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        $personalInfo = [];
        if ($qpArr['origin'] == '2') {
            $personalInfo = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                            ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name) as originator_name"))
                            ->where('cm_basic_profile.id', $qpArr['originator'])->first();
        } elseif ($qpArr['origin'] == '3') {
            $personalInfo = Staff::select('official_name as originator_name')
                            ->where('id', $qpArr['originator'])->first();
        }

        $contentData['personal_info'] = !empty($personalInfo) ? $personalInfo->toArray() : [];

        $contentData['active_trg_yr'] = $activeTrainingYearInfo;

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id ?? 0)
                ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')
                ->first();
        $contentData['active_course'] = $activeCourse;

        $contentData['content_classification_list'] = ContentClassification::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        $contentClassificationInfo = ContentClassification::orderBy('order', 'asc')
                ->select('name', 'id', 'icon', 'color')
                ->get();

        if (!$contentClassificationInfo->isEmpty()) {
            foreach ($contentClassificationInfo as $cntCls) {
                $contentData['content_class'][$cntCls->id] = $cntCls->toArray();
            }
        }

        $contentTypeInfo = MediaContentType::orderBy('order', 'asc')
                ->select('name', 'file_size', 'id', 'description');

        $moduleArr = array('0' => __('label.SELECT_MODULE_OPT')) + Module::orderBy('name', 'asc')
                        ->pluck('name', 'id')->toArray();

        $contentData['content_module'] = $moduleArr;

        $compartmentList = Common::getArchiveCompartmentList();

        $contentData['output_access'] = $compartmentList;

        $contentData['content_type_list'] = $contentTypeInfo->pluck('name', 'id')->toArray();
        $contentTypeInfo = $contentTypeInfo->get();

//
        //

        if (!$contentTypeInfo->isEmpty()) {
            foreach ($contentTypeInfo as $typeInfo) {
                $contentData['content_type'][$typeInfo->id]['name'] = $typeInfo->name;
                $contentData['content_type'][$typeInfo->id]['file_size'] = $typeInfo->file_size;
                $contentData['content_type'][$typeInfo->id]['description'] = $typeInfo->description;
                $contentData['content_type'][$typeInfo->id]['id'] = $typeInfo->id;
            }
        }

//        $contentClassArr = [];
//        if (!$contentClassificationInfo->isEmpty()) {
//            foreach ($contentClassificationInfo as $cntCls) {
//                $contentClassArr[$cntCls->id] = $cntCls->toArray();
//            }
//        }


        $contentData['content_class'] = [];
        if (!$contentClassificationInfo->isEmpty()) {
            foreach ($contentClassificationInfo as $cntCls) {
                $contentData['content_class'][$cntCls->id] = $cntCls->toArray();
            }
        }

        $categoryInfo = ContentCategory::orderBy('name', 'asc')->select('related_compartment as cmpt', 'id', 'name')->get();
        $contentData['content_cat_list'] = array('0' => __('label.SELECT_CATEGORY_OPT'));
        if (!$categoryInfo->isEmpty()) {
            foreach ($categoryInfo as $cat) {
                $cmptArr = !empty($cat->cmpt) ? explode(",", $cat->cmpt) : [];
                if (!empty($cmptArr) && in_array($qpArr['origin'], $cmptArr)) {
                    $contentData['content_cat_list'][$cat->id] = $cat->name;
                }
            }
        }



        return response()->json(['result' => $contentData, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function store(Request $request) {

        $qpArr = $request->data;

        $contentArr = $qpArr['content'];
        $authRes = Common::getHeaderAuth($request->header);
        if ($authRes['status'] == 419) {
            return response()->json(['message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $rules = [
            'title' => 'unique:content',
        ];

        $message['title.unique'] = __('label.CONTENT_FIELD_HAS_ALREADY_BEEN_TAKEN', ['field' => 'Title']);

        //numberToOrdinal

        $validator = Validator::make($qpArr, $rules, $message);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => 400]);
        }


        $target = new Content;
        $target->title = $qpArr['title'];
        $target->course_id = $qpArr['course_id'];
        $target->originator = $qpArr['originator'];
        $target->origin = $qpArr['origin'];
        $target->date_upload = !empty($qpArr['date_upload']) ? Helper::dateFormatConvert($qpArr['date_upload']) : null;
        $target->short_description = $qpArr['short_description'];
        $target->content_classification_id = !empty($qpArr['content_classification_id']) ? $qpArr['content_classification_id'] : 0;
        $target->module_id = !empty($qpArr['module_id']) ? $qpArr['module_id'] : 0;
        $target->content_category_id = !empty($qpArr['content_category_id']) ? $qpArr['content_category_id'] : 0;
        $target->output_access = !empty($qpArr['origin']) && $qpArr['origin'] == '3' && !empty($qpArr['output_access']) ? implode(',', $qpArr['output_access']) : NULL;
        $target->status = $qpArr['status'];

        $target->created_at = $qpArr['created_at'];
        $target->created_by = $qpArr['created_by'];
        $target->updated_at = $qpArr['updated_at'];
        $target->updated_by = $qpArr['updated_by'];

        $contentDetailsArr = $fileArr = [];

        DB::beginTransaction();
        try {
            if ($target->save()) {

                if (!empty($contentArr)) {
                    foreach ($contentArr as $cKey => $cInfo) {
                        $prevfile = $file = '';
                        $updateDetails = $insertDetails = 0;
                        $contentDetailsArr['content_id'] = $target->id;
                        $contentDetailsArr['content_type_id'] = $cInfo['content_type'];
                        $contentDetailsArr['content_key'] = $cKey;
                        $contentDetailsArr['content_order'] = $cInfo['content_order'];
                        $cType = $cInfo['content_type'];
                        if (!empty($cType)) {
                            if ($cType == 1) {
                                if (!empty($request->data['content'][$cKey]['doc']) && !empty($cInfo['prev_doc'])) {
                                    $prevfile = 'public/uploads/content/file/' . $cInfo['prev_doc'];
                                } elseif (!empty($cInfo['prev_doc_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_doc'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_doc_original'];
                                }
                                $file = !empty($cInfo['encoded_doc']) ? base64_decode($cInfo['encoded_doc']) : '';
                                if (!empty($file)) {
                                    $contentDetailsArr['content'] = $cInfo['file_name'];
                                    $contentDetailsArr['content_original'] = $cInfo['file_original_name'];

                                    $fileArr['path'] = 'public/uploads/content/file/' . $cInfo['file_name'];
                                    $fileArr['file'] = $file;
                                }
                            } elseif ($cType == 2) {
                                if (!empty($request->data['content'][$cKey]['photo']) && !empty($cInfo['prev_photo'])) {
                                    $prevfile = 'public/uploads/content/photo/' . $cInfo['prev_photo'];
                                } elseif (!empty($cInfo['prev_photo_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_photo'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_photo_original'];
                                }
                                $file = !empty($cInfo['encoded_photo']) ? base64_decode($cInfo['encoded_photo']) : '';
                                if (!empty($file)) {
                                    $contentDetailsArr['content'] = $cInfo['file_name'];
                                    $contentDetailsArr['content_original'] = $cInfo['file_original_name'];

                                    $fileArr['path'] = 'public/uploads/content/photo/' . $cInfo['file_name'];
                                    $fileArr['file'] = $file;
                                }
                            } elseif ($cType == 3) {
                                if (!empty($request->data['content'][$cKey]['video']) && !empty($cInfo['prev_video'])) {
                                    $prevfile = 'public/uploads/content/video/' . $cInfo['prev_video'];
                                } elseif (!empty($cInfo['prev_video_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_video'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_video_original'];
                                }
                                $file = !empty($cInfo['encoded_video']) ? base64_decode($cInfo['encoded_video']) : '';
                                if (!empty($file)) {
                                    $contentDetailsArr['content'] = $cInfo['file_name'];
                                    $contentDetailsArr['content_original'] = $cInfo['file_original_name'];

                                    $fileArr['path'] = 'public/uploads/content/video/' . $cInfo['file_name'];
                                    $fileArr['file'] = $file;
                                }
                            } elseif ($cType == 4) {
                                $contentDetailsArr['content'] = $cInfo['url'];
                                $contentDetailsArr['content_original'] = null;
                            }
                        }

                        if (!empty($cInfo['id']) && !empty($contentDetailIdArr) && in_array($cInfo['id'], $contentDetailIdArr)) {
                            $updateDetails = ContentDetails::where('id', $cInfo['id'])->update($contentDetailsArr);
                            if ($updateDetails) {
                                if (!empty($prevfile) && File::exists($prevfile)) {
                                    File::delete($prevfile);
                                }
                                if (!empty($fileArr)) {
                                    $file = $fileArr['file'];
                                    $fPath = $fileArr['path'];
                                    file_put_contents($fPath, $file);
                                }
                            }
                        } else {
                            $insertDetails = ContentDetails::insert($contentDetailsArr);
                            if ($insertDetails) {
                                if (!empty($fileArr)) {
                                    $file = $fileArr['file'];
                                    $fPath = $fileArr['path'];
                                    file_put_contents($fPath, $file);
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => $authRes['message'], 'status' => 200]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['message' => $authRes['message'], 'status' => 401]);
        }
    }

    public function edit(Request $request) {

        $qpArr = $request->data;

        $target = Content::find($qpArr['id']);
        $contentData['target'] = $target;
        $authRes = Common::getHeaderAuth($request->header);
        if ($authRes['status'] == 419) {
            return response()->json(['message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $contentData['qp_Arr'] = $qpArr;

        $contentDetailsInfo = ContentDetails::select('id', 'content', 'content_type_id'
                        , 'content_key', 'content_original', 'content_order')
                ->where('content_id', $target->id)
                ->orderBy('content_order', 'asc')
                ->get();

        if (!$contentDetailsInfo->isEmpty()) {
            foreach ($contentDetailsInfo as $details) {
                $contentData['content_details'][$details->content_key] = $details->toArray();
            }
        }
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        $personalInfo = [];
        if ($qpArr['origin'] == '2') {
            $personalInfo = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                            ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name) as originator_name"))
                            ->where('cm_basic_profile.id', $qpArr['orginator'])->first();
        } elseif ($qpArr['origin'] == '3') {
            $personalInfo = Staff::select('official_name as originator_name')
                            ->where('id', $qpArr['orginator'])->first();
        }

        $contentData['personal_info'] = !empty($personalInfo) ? $personalInfo->toArray() : [];

        $contentData['active_trg_yr'] = $activeTrainingYearInfo;

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id ?? 0)
                ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')
                ->first();
        $contentData['active_course'] = $activeCourse;

        $contentData['content_classification_list'] = ContentClassification::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        $contentClassificationInfo = ContentClassification::orderBy('order', 'asc')
                ->select('name', 'id', 'icon', 'color')
                ->get();

        if (!$contentClassificationInfo->isEmpty()) {
            foreach ($contentClassificationInfo as $cntCls) {
                $contentData['content_class'][$cntCls->id] = $cntCls->toArray();
            }
        }

        $contentTypeInfo = MediaContentType::orderBy('order', 'asc')
                ->select('name', 'file_size', 'id', 'description');

        $moduleArr = array('0' => __('label.SELECT_MODULE_OPT')) + Module::orderBy('name', 'asc')
                        ->pluck('name', 'id')->toArray();
        $contentData['content_module'] = $moduleArr;

        $compartmentList = Common::getArchiveCompartmentList();

        $contentData['output_access'] = $compartmentList;

        $contentData['content_type_list'] = $contentTypeInfo->pluck('name', 'id')->toArray();
        $contentTypeInfo = $contentTypeInfo->get();

        if (!$contentTypeInfo->isEmpty()) {
            foreach ($contentTypeInfo as $typeInfo) {
                $contentData['content_type'][$typeInfo->id]['name'] = $typeInfo->name;
                $contentData['content_type'][$typeInfo->id]['file_size'] = $typeInfo->file_size;
                $contentData['content_type'][$typeInfo->id]['description'] = $typeInfo->description;
                $contentData['content_type'][$typeInfo->id]['id'] = $typeInfo->id;
            }
        }

//        $contentClassArr = [];
//        if (!$contentClassificationInfo->isEmpty()) {
//            foreach ($contentClassificationInfo as $cntCls) {
//                $contentClassArr[$cntCls->id] = $cntCls->toArray();
//            }
//        }


        $contentData['content_class'] = [];
        if (!$contentClassificationInfo->isEmpty()) {
            foreach ($contentClassificationInfo as $cntCls) {
                $contentData['content_class'][$cntCls->id] = $cntCls->toArray();
            }
        }

        $categoryInfo = ContentCategory::orderBy('name', 'asc')->select('related_compartment as cmpt', 'id', 'name')->get();
        $contentData['content_cat_list'] = array('0' => __('label.SELECT_CATEGORY_OPT'));
        if (!$categoryInfo->isEmpty()) {
            foreach ($categoryInfo as $cat) {
                $cmptArr = !empty($cat->cmpt) ? explode(",", $cat->cmpt) : [];
                if (!empty($cmptArr) && in_array($qpArr['origin'], $cmptArr)) {
                    $contentData['content_cat_list'][$cat->id] = $cat->name;
                }
            }
        }


        return response()->json(['result' => $contentData, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function update(Request $request) {
        $qpArr = $request->data;

        $id = $qpArr['id'];
        $contentArr = $qpArr['content'];

        $contentDetailIdArr = ContentDetails::where('content_id', $id)
                        ->pluck('id', 'id')->toArray();

        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $rules = [
            'title' => 'unique:content,title,' . $id,
        ];

        $message['title.unique'] = __('label.CONTENT_FIELD_HAS_ALREADY_BEEN_TAKEN', ['field' => 'Title']);

        //numberToOrdinal

        $validator = Validator::make($qpArr, $rules, $message);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => 400]);
        }


        $target = Content::find($id);
        $target->title = $qpArr['title'];
        $target->course_id = $qpArr['course_id'];
        $target->originator = $qpArr['originator'];
        $target->origin = $qpArr['origin'];
        $target->date_upload = !empty($qpArr['date_upload']) ? Helper::dateFormatConvert($qpArr['date_upload']) : null;
        $target->short_description = $qpArr['short_description'];
        $target->content_classification_id = !empty($qpArr['content_classification_id']) ? $qpArr['content_classification_id'] : 0;
        $target->module_id = !empty($qpArr['module_id']) ? $qpArr['module_id'] : 0;
        $target->content_category_id = !empty($qpArr['content_category_id']) ? $qpArr['content_category_id'] : 0;
        $target->output_access = !empty($qpArr['origin']) && $qpArr['origin'] == '3' && !empty($qpArr['output_access']) ? implode(',', $qpArr['output_access']) : NULL;
        $target->status = $qpArr['status'];

        $target->updated_at = $qpArr['updated_at'];
        $target->updated_by = $qpArr['updated_by'];

        DB::beginTransaction();

        try {
            if ($target->save()) {
                $i = 0;
                $delUploadInfo = ContentDetails::where('content_id', $target->id)
                                ->select('id', 'content_type_id', 'content')->get();

                if (!$delUploadInfo->isEmpty()) {
                    foreach ($delUploadInfo as $dul) {
                        $delUploadList[$dul->id] = $dul->toArray();
                    }
                }
                if (!empty($contentArr)) {
                    foreach ($contentArr as $cKey => $cInfo) {
                        if (!empty($cInfo['id']) && !empty($delUploadList[$cInfo['id']])) {
                            unset($delUploadList[$cInfo['id']]);
                        }
                        $contentDetailsArr = $files = [];
                        $prevfile = '';
                        $updateDetails = $insertDetails = 0;
                        $contentDetailsArr['content_id'] = $target->id;
                        $contentDetailsArr['content_type_id'] = $cInfo['content_type'];
                        $contentDetailsArr['content_key'] = $cKey;
                        $contentDetailsArr['content_order'] = $cInfo['content_order'];

                        $cType = $cInfo['content_type'];
                        if (!empty($cType)) {
                            if ($cType == 1) {
                                if (!empty($request->data['content'][$cKey]['doc']) && !empty($cInfo['prev_doc'])) {
                                    $prevfile = 'public/uploads/content/file/' . $cInfo['prev_doc'];
                                } elseif (!empty($cInfo['prev_doc_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_doc'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_doc_original'];
                                }
                                $file = !empty($cInfo['encoded_doc']) ? base64_decode($cInfo['encoded_doc']) : '';
                                if (!empty($file)) {
                                    $contentDetailsArr['content'] = $cInfo['file_name'];
                                    $contentDetailsArr['content_original'] = $cInfo['file_original_name'];

                                    $fileArr['path'] = 'public/uploads/content/file/' . $cInfo['file_name'];
                                    $fileArr['file'] = $file;
                                }
                            } elseif ($cType == 2) {
                                if (!empty($request->data['content'][$cKey]['photo']) && !empty($cInfo['prev_photo'])) {
                                    $prevfile = 'public/uploads/content/photo/' . $cInfo['prev_photo'];
                                } elseif (!empty($cInfo['prev_photo_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_photo'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_photo_original'];
                                }
                                $file = !empty($cInfo['encoded_photo']) ? base64_decode($cInfo['encoded_photo']) : '';
                                if (!empty($file)) {
                                    $contentDetailsArr['content'] = $cInfo['file_name'];
                                    $contentDetailsArr['content_original'] = $cInfo['file_original_name'];

                                    $fileArr['path'] = 'public/uploads/content/photo/' . $cInfo['file_name'];
                                    $fileArr['file'] = $file;
                                }
                            } elseif ($cType == 3) {
                                if (!empty($request->data['content'][$cKey]['video']) && !empty($cInfo['prev_video'])) {
                                    $prevfile = 'public/uploads/content/video/' . $cInfo['prev_video'];
                                } elseif (!empty($cInfo['prev_video_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_video'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_video_original'];
                                }
                                $file = !empty($cInfo['encoded_video']) ? base64_decode($cInfo['encoded_video']) : '';
                                if (!empty($file)) {
                                    $contentDetailsArr['content'] = $cInfo['file_name'];
                                    $contentDetailsArr['content_original'] = $cInfo['file_original_name'];

                                    $fileArr['path'] = 'public/uploads/content/video/' . $cInfo['file_name'];
                                    $fileArr['file'] = $file;
                                }
                            } elseif ($cType == 4) {
                                $contentDetailsArr['content'] = $cInfo['url'];
                                $contentDetailsArr['content_original'] = null;
                            }
                        }

                        if (!empty($cInfo['id']) && !empty($contentDetailIdArr) && in_array($cInfo['id'], $contentDetailIdArr)) {
                            $updateDetails = ContentDetails::where('id', $cInfo['id'])->update($contentDetailsArr);
                            if ($updateDetails) {
                                if (!empty($prevfile) && File::exists($prevfile)) {
                                    File::delete($prevfile);
                                }
                                if (!empty($fileArr)) {
                                    $file = $fileArr['file'];
//                                    $fName = $fileArr['file_name'];
                                    $fPath = $fileArr['path'];
//                                    $uplyoadSuccess = $file->move($fPath, $fName);
                                    file_put_contents($fPath, $file);
                                }
                            }
                        } else {
                            $insertDetails = ContentDetails::insert($contentDetailsArr);
                            if ($insertDetails) {
                                if (!empty($fileArr)) {
                                    $file = $fileArr['file'];
//                                    $fName = $fileArr['file_name'];
                                    $fPath = $fileArr['path'];
//                                    $uploadSuccess = $file->move($fPath, $fName);
                                    file_put_contents($fPath, $file);
                                }
                            }
                        }
                    }
                    $delUploadIdList = [];
                    if (!empty($delUploadList)) {
                        foreach ($delUploadList as $detId => $detInfo) {
                            $delUploadIdList[$detId] = $detId;
                            if ($detInfo['content_type_id'] == 1) {
                                $contentType = 'file';
                            } elseif ($detInfo['content_type_id'] == 2) {
                                $contentType = 'photo';
                            } elseif ($detInfo['content_type_id'] == 3) {
                                $contentType = 'video';
                            }

                            if ($detInfo['content_type_id'] != 4) {
                                $delfile = 'public/uploads/content/' . $contentType . '/' . $detInfo['content'];

                                if (!empty($delfile) && File::exists($delfile)) {
                                    File::delete($delfile);
                                }
                            }
                        }
                        ContentDetails::whereIn('id', $delUploadIdList)->delete();
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => $authRes['message'], 'status' => 200]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['message' => $authRes['message'], 'status' => 401]);
        }
    }

    public function destroy(Request $request) {

        $qpArr = $request->data;

        $id = $qpArr['id'];
        $target = Content::find($id);

        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $contentDetailsInfo = ContentDetails::select('content', 'content_type_id as t_id'
                        , 'content_key', 'content_original')
                ->where('content_id', $id)
                ->whereIn('content_type_id', [1, 2, 3])
                ->get();

        $fileArr = [];
        if (!$contentDetailsInfo->isEmpty()) {
            foreach ($contentDetailsInfo as $details) {
                $details->t_id ?? 0;
                $typePath = empty($details->t_id) ? '' : ($details->t_id == 1 ? 'file/' : ($details->t_id == 2 ? 'photo/' : ($details->t_id == 3 ? 'video/' : '')));
                $path = 'public/uploads/content/' . $typePath . $details->content;
                $fileArr[$details->content_key] = $path;
            }
        }

//begin back same page after update
//        $qpArr = $request->all();
        //$pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
//end back same page after update
        $msg = '';
        if (empty($target)) {
            $msg = __('label.INVALID_DATA_ID');
            return response()->json(['message' => $msg, 'status' => 401]);
        }


        if ($target->delete()) {
            if (!empty($fileArr)) {
                foreach ($fileArr as $cKey => $cPath) {
                    if (File::exists($cPath)) {
                        File::delete($cPath);
                    }
                }
            }
            return response()->json(['message' => $authRes['message'], 'status' => 200]);
        } else {
            return response()->json(['message' => __('label.CONTENT_COULD_NOT_BE_DELETED'), 'status' => 401]);
        }
    }

    public function addContentRow(Request $request) {
//        echo '<pre>';
//        print_r('gg');
//        exit;

        $qpArr = $request->data;
        $fileArr = !empty($qpArr['files']) ? $qpArr['files'] : [];

        $authRes = Common::getHeaderAuth($request->header);
        if ($authRes['status'] == 419) {
            return response()->json(['message' => $authRes['message'], 'status' => $authRes['status']]);
        }


        if (!empty($fileArr)) {
            $file = !empty($fileArr['file']) ? base64_decode($fileArr['file']) : '';
            $fName = $fileArr['file_name'];
            $fPath = $fileArr['path'] . '/' . $fName;
            file_put_contents($fPath, $file);
        }


        $prevContentInfo = ContentDetails::orderBy('content_id', 'asc')->pluck('content', 'id')->toArray();

        $contentData['prev_content_info'] = $prevContentInfo;

        $contentTypeInfo = MediaContentType::orderBy('order', 'asc')
                ->select('name', 'file_size', 'id', 'description');

        $contentTypeArr = $contentTypeInfo->pluck('name', 'id')->toArray();
        $contentTypeInfo = $contentTypeInfo->get();

        $contentData['content_type_arr'] = $contentTypeArr;
//                echo '<pre>';
//        print_r($contentTypeArr);
//        exit;

        if (!$contentTypeInfo->isEmpty()) {
            foreach ($contentTypeInfo as $typeInfo) {
                $contentData['content_type'][$typeInfo->id]['name'] = $typeInfo->name;
                $contentData['content_type'][$typeInfo->id]['file_size'] = $typeInfo->file_size;
                $contentData['content_type'][$typeInfo->id]['description'] = $typeInfo->description;
                $contentData['content_type'][$typeInfo->id]['id'] = $typeInfo->id;
            }
        }

        return response()->json(['result' => $contentData, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('content?' . $url);
    }

    public function downloadFile(Request $request) {
        $basicPath = public_path() . '/uploads/content/';
        $filePath = '';
        if ($request->content_type == 1) {
            $filePath = "file/";
        } elseif ($request->content_type == 2) {
            $filePath = "photo/";
        } elseif ($request->content_type == 3) {
            $filePath = "video/";
        }
        $filePathInfo = $basicPath . $filePath;
        $fileName = $request->file_name;
        $file = $filePathInfo . $fileName;

        $headers = array(
            'Content-Type: application/pdf',
        );

        return Response::download($file, 'Content-' . $fileName, $headers);
    }

}
