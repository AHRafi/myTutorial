<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Content;
use App\Module;
use App\Course;
use App\MediaContentType;
use App\ContentCategory;
use App\ContentDetails;
use App\ContentClassification;
use App\User;
use App\CmBasicProfile;
use App\Staff;
use DB;
use Common;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use File;
use Response;
use Illuminate\Http\Request;

class ContentController extends Controller {

    private $controller = 'Content';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Content::select('title')->orderBy('title')->get();

        $contentTypeList = MediaContentType::orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();

        $categoryList = ContentCategory::where('status', '1')->orderBy('name', 'asc')
                        ->pluck('name', 'id')->toArray();

//        $targetArr = ContentCategory::select('content_category.*')->orderBy('order', 'asc');

        $compartmentList = Common::getArchiveCompartmentList();

        $contentClassificationList = ContentClassification::orderBy('order', 'asc')
                        ->pluck('name', 'id')->toArray();

//passing param for custom function
        $qpArr = $request->all();

        $contentInfo = Content::leftJoin('users', 'users.id', '=', 'content.originator')
                ->leftJoin('cm_basic_profile', 'cm_basic_profile.id', '=', 'content.originator')
                ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->leftJoin('staff', 'staff.id', '=', 'content.originator')
                ->leftJoin('module', 'module.id', 'content.module_id')
                ->join('course', 'course.id', 'content.course_id')
                ->join('content_classification', 'content_classification.id', 'content.content_classification_id')
                ->join('content_category', 'content_category.id', 'content.content_category_id')
                ->select('content.id', 'content.title', 'content.originator', 'content.date_upload', 'content.origin'
                        , 'content.short_description', 'content.course_id', 'content.content_classification_id'
                        , 'content.content_category_id', 'content.output_access', 'content.status', 'content_category.related_compartment', 'users.id as user_id'
                        , 'users.official_name as user_official_name', DB::raw("CONCAT(rank.code ,' ', cm_basic_profile.official_name ) as cm_official_name")
                        , 'staff.official_name as staff_official_name', 'content_category.name as content_cat'
                        , 'course.name as course_name', 'content_classification.name as content_classification_name'
                        , 'content_classification.icon as content_classification_icon'
                        , 'content_classification.color as content_classification_color', 'module.name as module_name')
                ->orderBy('content.title', 'asc');

        if (in_array(Auth::user()->group_id, [3, 4])) {
            $contentInfo = $contentInfo->where('content.originator', Auth::user()->id);
        }

//begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $contentInfo->where(function ($query) use ($searchText) {
                $query->where('content.title', 'LIKE', '%' . $searchText . '%');
            });
        }

//end filtering
        $targetIdArr = $contentInfo->pluck('content.id', 'content.id')->toArray();
        $contentInfo = $contentInfo->get();

//change page number after delete if no data has current page
        if ($contentInfo->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/content?page=' . $page);
        }


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

        $targetArr = [];
        if (!$contentInfo->isEmpty()) {
            foreach ($contentInfo as $item) {
                $targetArr[$item->id] = $item->toArray();
                $targetArr[$item->id]['content_details'] = !empty($contentDetailsInfoArr[$item->id]) ? $contentDetailsInfoArr[$item->id] : '';
            }
        }

        return view('content.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'contentClassificationList', 'contentTypeList', 'categoryList', 'compartmentList', 'contentDetailsInfoArr'));
    }

    public function create(Request $request) {
//passing param for custom function
        $qpArr = $request->all();
//get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CREATE_NEW_CONTENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id)
                ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')
                ->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CREATE_NEW_CONTENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }


        $contentClassificationArr = ContentClassification::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        $contentClassificationInfo = ContentClassification::orderBy('order', 'asc')
                ->select('name', 'id', 'icon', 'color')
                ->get();

        $contentClassArr = [];
        if (!$contentClassificationInfo->isEmpty()) {
            foreach ($contentClassificationInfo as $cntCls) {
                $contentClassArr[$cntCls->id] = $cntCls->toArray();
            }
        }


        $contentTypeInfo = MediaContentType::orderBy('order', 'asc')
                ->select('name', 'file_size', 'id', 'description');

        $contentTypeArr = $contentTypeInfo->pluck('name', 'id')->toArray();
        $contentTypeInfo = $contentTypeInfo->get();

//        $contentTypeArr = array('0' => __('label.SELECT_CLASSIFICATION_OPT')) + $contentTypeArr;

        $contentTypeDataArr = [];

        if (!$contentTypeInfo->isEmpty()) {
            foreach ($contentTypeInfo as $typeInfo) {
                $contentTypeDataArr[$typeInfo->id]['name'] = $typeInfo->name;
                $contentTypeDataArr[$typeInfo->id]['file_size'] = $typeInfo->file_size;
                $contentTypeDataArr[$typeInfo->id]['description'] = $typeInfo->description;
                $contentTypeDataArr[$typeInfo->id]['id'] = $typeInfo->id;
            }
        }

        $categoryInfo = ContentCategory::orderBy('name', 'asc')->select('related_compartment as cmpt', 'id', 'name')->get();
        $categoryArr = array('0' => __('label.SELECT_CATEGORY_OPT'));
        if (!$categoryInfo->isEmpty()) {
            foreach ($categoryInfo as $cat) {
                $cmptArr = !empty($cat->cmpt) ? explode(",", $cat->cmpt) : [];
                if (!empty($cmptArr) && in_array('1', $cmptArr)) {
                    $categoryArr[$cat->id] = $cat->name;
                }
            }
        }

        $moduleArr = array('0' => __('label.SELECT_MODULE_OPT')) + Module::orderBy('name', 'asc')
                        ->pluck('name', 'id')->toArray();

//        $moduleInfo = Module::select('name as module_name', 'id')->get();

        $categoryArr = array('0' => __('label.SELECT_CATEGORY_OPT'));
        if (!$categoryInfo->isEmpty()) {
            foreach ($categoryInfo as $cat) {
                $cmptArr = !empty($cat->cmpt) ? explode(",", $cat->cmpt) : [];
                if (!empty($cmptArr) && in_array('1', $cmptArr)) {
                    $categoryArr[$cat->id] = $cat->name;
                }
            }
        }

        $compartmentList = Common::getArchiveCompartmentList();

        return view('content.create')->with(compact('qpArr', 'activeCourse', 'contentTypeArr'
                                , 'contentClassificationArr', 'contentTypeDataArr', 'categoryArr'
                                , 'contentClassArr', 'compartmentList', 'moduleArr'));
    }

    public function store(Request $request) {

//begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
//end back same page after update

        $message = [];
        $contentArr = $request->content;

//        echo '<pre>';
//        print_r($request->all());
//        exit;
        $rules = [
            'originator' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'title' => 'required|unique:content',
            'date_upload' => 'required',
            'content_classification_id' => 'required|not_in:0',
            'module_id' => 'required|not_in:0',
            'content_category_id' => 'required|not_in:0',
            'output_access' => 'required',
        ];

        $message['content_category_id.not_in'] = __('label.CONTENT_CATEGORY_IS_REQUIRED');
        //numberToOrdinal
        $in = 0;
        if (!empty($contentArr)) {
            foreach ($contentArr as $cKey => $cInfo) {
                $rules['content.' . $cKey . '.content_type'] = 'required|not_in:0';
                $message['content.' . $cKey . '.content_type.not_in'] = __('label.CONTENT_TYPE_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);

                $cType = $cInfo['content_type'];
                if (!empty($cType)) {
                    if ($cType == 1) {
                        if ($request->hasFile('content.' . $cKey . '.doc')) {
                            $rules['content.' . $cKey . '.doc'] = 'required|max:512000|mimes:pdf,doc,docx,xlsx,csv,ppt,pptx';
                            $message['content.' . $cKey . '.doc.required'] = __('label.DOCUMENT_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                            $message['content.' . $cKey . '.doc.max'] = __('label.DOCUMENT_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '10']);
                            $message['content.' . $cKey . '.doc.mimes'] = __('label.DOCUMENT_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                        }
                    } elseif ($cType == 2) {
                        if ($request->hasFile('content.' . $cKey . '.photo')) {
                            $rules['content.' . $cKey . '.photo'] = 'required|max:10240|mimes:jpg,jpeg,png,gif,tif,tiff';
                            $message['content.' . $cKey . '.photo.required'] = __('label.IMAGE_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                            $message['content.' . $cKey . '.photo.max'] = __('label.IMAGE_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '10']);
                            $message['content.' . $cKey . '.photo.mimes'] = __('label.IMAGE_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                        }
                    } elseif ($cType == 3) {
                        if ($request->hasFile('content.' . $cKey . '.video')) {
                            $rules['content.' . $cKey . '.video'] = 'required|max:1048576|mimes:avi,flv,mov,mp4,mkv,wmv';
                            $message['content.' . $cKey . '.video.required'] = __('label.VIDEO_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                            $message['content.' . $cKey . '.video.max'] = __('label.VIDEO_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '250']);
                            $message['content.' . $cKey . '.video.mimes'] = __('label.VIDEO_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                        }
                    } elseif ($cType == 4) {
                        $rules['content.' . $cKey . '.url'] = 'required';
                        $message['content.' . $cKey . '.url.required'] = __('label.LINK_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                    }
                }

                $in++;
            }
        } else {
            $msg = __('label.PLEASE_UPLOAD_SOMETHING_WITH_THIS_CONTENT');
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $msg), 401);
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        $target = new Content;
        $target->title = $request->title;
        $target->course_id = $request->course_id;
        $target->originator = $request->originator;
        $target->origin = $request->origin;
        $target->date_upload = !empty($request->date_upload) ? Helper::dateFormatConvert($request->date_upload) : null;
        $target->short_description = $request->short_description;
        $target->content_classification_id = !empty($request->content_classification_id) ? $request->content_classification_id : 0;
        $target->module_id = !empty($request->module_id) ? $request->module_id : 0;
        $target->content_category_id = !empty($request->content_category_id) ? $request->content_category_id : 0;
        $target->output_access = !empty($request->output_access) ? implode(',', $request->output_access) : NULL;
        $target->status = $request->status;

        $target->created_at = date("Y-m-d H:i:s");
        $target->created_by = Auth::user()->id;
        $target->updated_at = date("Y-m-d H:i:s");
        $target->updated_by = Auth::user()->id;

        $contentDetailsArr = $fileArr = [];
        $file = $fileName = $fileOriginalName = $uploadSuccess = '';

        DB::beginTransaction();

        try {
            if ($target->save()) {
                $i = 0;
                if (!empty($contentArr)) {
                    foreach ($contentArr as $cKey => $cInfo) {
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
                                if (!empty($request->content[$cKey]['doc']) && !empty($cInfo['prev_doc'])) {
                                    $prevfile = 'public/uploads/content/file/' . $cInfo['prev_doc'];
                                } elseif (!empty($cInfo['prev_doc_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_doc'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_doc_original'];
                                }

                                if ($request->hasFile('content.' . $cKey . '.doc')) {
                                    $file = $request->file('content.' . $cKey . '.doc');
                                    $fileName = $target->originator . '_' . $target->origin . '_' . uniqid() . "." . $file->getClientOriginalExtension();
                                    $fileOriginalName = $file->getClientOriginalName();

                                    $contentDetailsArr['content'] = $fileName;
                                    $contentDetailsArr['content_original'] = $fileOriginalName;

                                    $files['path'] = 'public/uploads/content/file';
                                    $files['file'] = $file;
                                    $files['file_name'] = $fileName;
                                }
                            } elseif ($cType == 2) {
                                if (!empty($request->content[$cKey]['photo']) && !empty($cInfo['prev_photo'])) {
                                    $prevfile = 'public/uploads/content/photo/' . $cInfo['prev_photo'];
                                } elseif (!empty($cInfo['prev_photo_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_photo'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_photo_original'];
                                }
                                if ($request->hasFile('content.' . $cKey . '.photo')) {
                                    $file = $request->file('content.' . $cKey . '.photo');
                                    $fileName = $target->originator . '_' . $target->origin . '_' . uniqid() . "." . $file->getClientOriginalExtension();
                                    $fileOriginalName = $file->getClientOriginalName();

                                    $contentDetailsArr['content'] = $fileName;
                                    $contentDetailsArr['content_original'] = $fileOriginalName;

                                    $files['path'] = 'public/uploads/content/photo';
                                    $files['file'] = $file;
                                    $files['file_name'] = $fileName;
                                }
                            } elseif ($cType == 3) {
                                if (!empty($request->content[$cKey]['video']) && !empty($cInfo['prev_video'])) {
                                    $prevfile = 'public/uploads/content/video/' . $cInfo['prev_video'];
                                } elseif (!empty($cInfo['prev_video_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_video'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_video_original'];
                                }
                                if ($request->hasFile('content.' . $cKey . '.video')) {
                                    $file = $request->file('content.' . $cKey . '.video');
                                    $fileName = $target->originator . '_' . $target->origin . '_' . uniqid() . "." . $file->getClientOriginalExtension();
                                    $fileOriginalName = $file->getClientOriginalName();

                                    $contentDetailsArr['content'] = $fileName;
                                    $contentDetailsArr['content_original'] = $fileOriginalName;

                                    $files['path'] = 'public/uploads/content/video';
                                    $files['file'] = $file;
                                    $files['file_name'] = $fileName;
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
                                if (!empty($files)) {
                                    $file = $files['file'];
                                    $fName = $files['file_name'];
                                    $fPath = $files['path'];
                                    $uploadSuccess = $file->move($fPath, $fName);
                                }
                            }
                        } else {
                            $insertDetails = ContentDetails::insert($contentDetailsArr);
                            if ($insertDetails) {
                                if (!empty($files)) {
                                    $file = $files['file'];
                                    $fName = $files['file_name'];
                                    $fPath = $files['path'];
                                    $uploadSuccess = $file->move($fPath, $fName);
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return Response::json(array('heading' => 'Success', 'message' => __('label.CONTENT_CREATED_SUCCESSFULLY')), 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(array('success' => false, 'message' => __('label.CONTENT_COULD_NOT_BE_CREATED')), 401);
        }
    }

    public function edit(Request $request, $id) {
        $target = Content::find($id);
        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('content');
        }

        $contentDetailsInfo = ContentDetails::select('id', 'content', 'content_type_id'
                        , 'content_key', 'content_original', 'content_order')
                ->where('content_id', $id)
                ->orderBy('content_order', 'asc')
                ->get();

        $contentDetailsArr = [];
        if (!$contentDetailsInfo->isEmpty()) {
            foreach ($contentDetailsInfo as $details) {
                $contentDetailsArr[$details->content_key] = $details->toArray();
            }
        }

//passing param for custom function
        $qpArr = $request->all();

//get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CREATE_NEW_CONTENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.CREATE_NEW_CONTENT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }



        $contentClassificationArr = ContentClassification::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        $contentClassificationInfo = ContentClassification::orderBy('order', 'asc')
                ->select('name', 'id', 'icon', 'color')
                ->get();

        $contentClassArr = [];
        if (!$contentClassificationInfo->isEmpty()) {
            foreach ($contentClassificationInfo as $cntCls) {
                $contentClassArr[$cntCls->id] = $cntCls->toArray();
            }
        }

        $moduleArr = array('0' => __('label.SELECT_MODULE_OPT')) + Module::orderBy('name', 'asc')
                        ->pluck('name', 'id')->toArray();

        $compartmentList = Common::getArchiveCompartmentList();

        $contentTypeInfo = MediaContentType::orderBy('order', 'asc')
                ->select('name', 'file_size', 'id', 'description');

        $contentTypeArr = $contentTypeInfo->pluck('name', 'id')->toArray();
        $contentTypeInfo = $contentTypeInfo->get();

//        $contentTypeArr = array('0' => __('label.SELECT_CLASSIFICATION_OPT')) + $contentTypeArr;

        $contentTypeDataArr = [];

        if (!$contentTypeInfo->isEmpty()) {
            foreach ($contentTypeInfo as $typeInfo) {
                $contentTypeDataArr[$typeInfo->id]['name'] = $typeInfo->name;
                $contentTypeDataArr[$typeInfo->id]['file_size'] = $typeInfo->file_size;
                $contentTypeDataArr[$typeInfo->id]['description'] = $typeInfo->description;
                $contentTypeDataArr[$typeInfo->id]['id'] = $typeInfo->id;
            }
        }

        $origin = !empty($target->origin) ? $target->origin : '1';
        $categoryInfo = ContentCategory::orderBy('name', 'asc')->select('related_compartment as cmpt', 'id', 'name')->get();
        $categoryArr = array('0' => __('label.SELECT_CATEGORY_OPT'));
        if (!$categoryInfo->isEmpty()) {
            foreach ($categoryInfo as $cat) {
                $cmptArr = !empty($cat->cmpt) ? explode(",", $cat->cmpt) : [];

                if (!empty($cmptArr) && in_array($origin, $cmptArr)) {
                    $categoryArr[$cat->id] = $cat->name;
                }
            }
        }

        $originatorInfo = [];
        if ($origin == '1') {
            $originatorInfo = User::where('id', $target->originator ?? 0)->select('official_name as name')->first();
        } elseif ($origin == '2') {
            $originatorInfo = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->where('cm_basic_profile.id', $target->originator ?? 0)
                    ->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.official_name) as name"))
                    ->first();
        } elseif ($origin == '3') {
            $originatorInfo = Staff::where('id', $target->originator ?? 0)->select('official_name as name')->first();
        }

        return view('content.edit')->with(compact('target', 'activeCourse', 'qpArr'
                                , 'contentClassificationArr', 'categoryArr', 'contentTypeArr'
                                , 'contentTypeDataArr', 'contentClassArr', 'contentDetailsArr'
                                , 'originatorInfo', 'moduleArr', 'compartmentList'));
    }

    public function update(Request $request) {
        $message = [];
        $contentArr = $request->content;
        $id = $request->id;

        $target = Content::find($request->id);
        $contentDetailIdArr = ContentDetails::where('content_id', $request->id)
                        ->pluck('id', 'id')->toArray();

        $rules = [
            'originator' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'title' => 'required|unique:content,title,' . $id,
            'date_upload' => 'required',
            'content_classification_id' => 'required|not_in:0',
            'module_id' => 'required|not_in:0',
            'content_category_id' => 'required|not_in:0',
            'output_access' => 'required',
        ];

        $message['content_category_id.not_in'] = __('label.CONTENT_CATEGORY_IS_REQUIRED');
        //numberToOrdinal
        $in = 0;
        if (!empty($contentArr)) {
            foreach ($contentArr as $cKey => $cInfo) {
                $rules['content.' . $cKey . '.content_type'] = 'required|not_in:0';
                $message['content.' . $cKey . '.content_type.not_in'] = __('label.CONTENT_TYPE_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);

                $cType = $cInfo['content_type'];
                if (!empty($cType)) {
                    if ($cType == 1) {
                        if ($request->hasFile('content.' . $cKey . '.doc')) {
                            $rules['content.' . $cKey . '.doc'] = 'required|max:512000|mimes:pdf,doc,docx,xlsx,csv,ppt,pptx';
                            $message['content.' . $cKey . '.doc.required'] = __('label.DOCUMENT_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                            $message['content.' . $cKey . '.doc.max'] = __('label.DOCUMENT_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '10']);
                            $message['content.' . $cKey . '.doc.mimes'] = __('label.DOCUMENT_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                        }
                    } elseif ($cType == 2) {
                        if ($request->hasFile('content.' . $cKey . '.photo')) {
                            $rules['content.' . $cKey . '.photo'] = 'required|max:10240|mimes:jpg,jpeg,png,gif,tif,tiff';
                            $message['content.' . $cKey . '.photo.required'] = __('label.IMAGE_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                            $message['content.' . $cKey . '.photo.max'] = __('label.IMAGE_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '10']);
                            $message['content.' . $cKey . '.photo.mimes'] = __('label.IMAGE_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                        }
                    } elseif ($cType == 3) {
                        if ($request->hasFile('content.' . $cKey . '.video')) {
                            $rules['content.' . $cKey . '.video'] = 'required|max:1048576|mimes:avi,flv,mov,mp4,mkv,wmv';
                            $message['content.' . $cKey . '.video.required'] = __('label.VIDEO_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                            $message['content.' . $cKey . '.video.max'] = __('label.VIDEO_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '250']);
                            $message['content.' . $cKey . '.video.mimes'] = __('label.VIDEO_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                        }
                    } elseif ($cType == 4) {
                        $rules['content.' . $cKey . '.url'] = 'required';
                        $message['content.' . $cKey . '.url.required'] = __('label.LINK_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                    }
                }

                $in++;
            }
        } else {
            $msg = __('label.PLEASE_UPLOAD_SOMETHING_WITH_THIS_CONTENT');
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $msg), 401);
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        $target->title = $request->title;
        $target->course_id = $request->course_id;
        $target->originator = $request->originator;
        $target->origin = $request->origin;
        $target->date_upload = !empty($request->date_upload) ? Helper::dateFormatConvert($request->date_upload) : null;
        $target->short_description = $request->short_description;
        $target->content_classification_id = !empty($request->content_classification_id) ? $request->content_classification_id : 0;
        $target->module_id = !empty($request->module_id) ? $request->module_id : 0;
        $target->content_category_id = !empty($request->content_category_id) ? $request->content_category_id : 0;
        $target->output_access = !empty($request->output_access) ? implode(',', $request->output_access) : NULL;
        $target->status = $request->status;

        $target->updated_at = date("Y-m-d H:i:s");
        $target->updated_by = Auth::user()->id;

        $contentDetailsArr = $fileArr = [];
        $file = $fileName = $fileOriginalName = $uploadSuccess = '';

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
                                if (!empty($request->content[$cKey]['doc']) && !empty($cInfo['prev_doc'])) {
                                    $prevfile = 'public/uploads/content/file/' . $cInfo['prev_doc'];
                                } elseif (!empty($cInfo['prev_doc_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_doc'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_doc_original'];
                                }
                                if ($request->hasFile('content.' . $cKey . '.doc')) {
                                    $file = $request->file('content.' . $cKey . '.doc');
                                    $fileName = $target->originator . '_' . $target->origin . '_' . uniqid() . "." . $file->getClientOriginalExtension();
                                    $fileOriginalName = $file->getClientOriginalName();

                                    $contentDetailsArr['content'] = $fileName;
                                    $contentDetailsArr['content_original'] = $fileOriginalName;

                                    $files['path'] = 'public/uploads/content/file';
                                    $files['file'] = $file;
                                    $files['file_name'] = $fileName;
                                }
                            } elseif ($cType == 2) {
                                if (!empty($request->content[$cKey]['photo']) && !empty($cInfo['prev_photo'])) {
                                    $prevfile = 'public/uploads/content/photo/' . $cInfo['prev_photo'];
                                } elseif (!empty($cInfo['prev_photo_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_photo'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_photo_original'];
                                }
                                if ($request->hasFile('content.' . $cKey . '.photo')) {
                                    $file = $request->file('content.' . $cKey . '.photo');
                                    $fileName = $target->originator . '_' . $target->origin . '_' . uniqid() . "." . $file->getClientOriginalExtension();
                                    $fileOriginalName = $file->getClientOriginalName();

                                    $contentDetailsArr['content'] = $fileName;
                                    $contentDetailsArr['content_original'] = $fileOriginalName;

                                    $files['path'] = 'public/uploads/content/photo';
                                    $files['file'] = $file;
                                    $files['file_name'] = $fileName;
                                }
                            } elseif ($cType == 3) {
                                if (!empty($request->content[$cKey]['video']) && !empty($cInfo['prev_video'])) {
                                    $prevfile = 'public/uploads/content/video/' . $cInfo['prev_video'];
                                } elseif (!empty($cInfo['prev_video_original'])) {
                                    $contentDetailsArr['content'] = $cInfo['prev_video'];
                                    $contentDetailsArr['content_original'] = $cInfo['prev_video_original'];
                                }
                                if ($request->hasFile('content.' . $cKey . '.video')) {
                                    $file = $request->file('content.' . $cKey . '.video');
                                    $fileName = $target->originator . '_' . $target->origin . '_' . uniqid() . "." . $file->getClientOriginalExtension();
                                    $fileOriginalName = $file->getClientOriginalName();

                                    $contentDetailsArr['content'] = $fileName;
                                    $contentDetailsArr['content_original'] = $fileOriginalName;

                                    $files['path'] = 'public/uploads/content/video';
                                    $files['file'] = $file;
                                    $files['file_name'] = $fileName;
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
                                if (!empty($files)) {
                                    $file = $files['file'];
                                    $fName = $files['file_name'];
                                    $fPath = $files['path'];
                                    $uploadSuccess = $file->move($fPath, $fName);
                                }
                            }
                        } else {
                            $insertDetails = ContentDetails::insert($contentDetailsArr);
                            if ($insertDetails) {
                                if (!empty($files)) {
                                    $file = $files['file'];
                                    $fName = $files['file_name'];
                                    $fPath = $files['path'];
                                    $uploadSuccess = $file->move($fPath, $fName);
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
            return Response::json(array('heading' => 'Success', 'message' => __('label.CONTENT_UPDATED_SUCCESSFULLY')), 200);
        } catch (Exception $ex) {
            DB::rollback();
            return Response::json(array('success' => false, 'message' => __('label.CONTENT_COULD_NOT_BE_UPDATED')), 401);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Content::find($id);

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
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
//end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        DB::beginTransaction();

        try {
            if ($target->delete()) {
                ContentDetails::where('content_id', $id)->delete();
                if (!empty($fileArr)) {
                    foreach ($fileArr as $cKey => $cPath) {
                        if (File::exists($cPath)) {
                            File::delete($cPath);
                        }
                    }
                }
            }
            DB::commit();
            Session::flash('error', __('label.CONTENT_DELETED_SUCCESSFULLY'));
        } catch (Exception $ex) {
            DB::rollback();
            Session::flash('error', __('label.CONTENT_COULD_NOT_BE_DELETED'));
        }
        return redirect('content?page=' . $pageNumber);
    }

    public function addContentRow(Request $request) {
        $fileArr = [];
        if (!empty($request->file('file'))) {
            $message = [];

//        echo '<pre>';
//        print_r($request->all());
//        exit;
            $rules = [];
            //numberToOrdinal
            $cType = $request->content_type;
            $in = $request->sl;
            if (!empty($cType)) {
                if ($cType == 1) {
                    $rules['file'] = 'required|max:512000|mimes:pdf,doc,docx,xlsx,csv,ppt,pptx';
                    $message['file.required'] = __('label.DOCUMENT_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                    $message['file.max'] = __('label.DOCUMENT_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '10']);
                    $message['file.mimes'] = __('label.DOCUMENT_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                } elseif ($cType == 2) {
                    $rules['file'] = 'required|max:10240|mimes:jpg,jpeg,png,gif,tif,tiff';
                    $message['file.required'] = __('label.IMAGE_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                    $message['file.max'] = __('label.IMAGE_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '10']);
                    $message['file.mimes'] = __('label.IMAGE_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                } elseif ($cType == 3) {
                    $rules['file'] = 'required|max:1048576|mimes:avi,flv,mov,mp4,mkv,wmv';
                    $message['file.required'] = __('label.VIDEO_IS_REQUIRED_FOR_NTH_UPLOAD', ['nth' => Helper::numberToOrdinal($in + 1)]);
                    $message['file.max'] = __('label.VIDEO_SIZE_OF_NTH_UPLOAD_MAY_NOT_BE_LARGER_THAN_10_MB', ['nth' => Helper::numberToOrdinal($in + 1), 'size' => '250']);
                    $message['file.mimes'] = __('label.VIDEO_FORMAT_OF_NTH_UPLOAD_IS_INAVLID', ['nth' => Helper::numberToOrdinal($in + 1)]);
                }
            }

            $validator = Validator::make($request->all(), $rules, $message);
            if ($validator->fails()) {
                return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
            }
            if (!empty($cType)) {
                if ($cType == 1) {
                    if ($request->hasFile('file')) {
                        $file = $request->file('file');
                        $fileName = Auth::user()->id . '_1_' . uniqid() . "." . $file->getClientOriginalExtension();
                        $fileOriginalName = $file->getClientOriginalName();

                        $fileArr['path'] = 'public/uploads/content/file';
                        $fileArr['file'] = $file;
                        $fileArr['file_name'] = $fileName;
                        $fileArr['file_original_name'] = $fileOriginalName;
                    }
                } elseif ($cType == 2) {
                    if ($request->hasFile('file')) {
                        $file = $request->file('file');
                        $fileName = Auth::user()->id . '_1_' . uniqid() . "." . $file->getClientOriginalExtension();
                        $fileOriginalName = $file->getClientOriginalName();

                        $fileArr['path'] = 'public/uploads/content/photo';
                        $fileArr['file'] = $file;
                        $fileArr['file_name'] = $fileName;
                        $fileArr['file_original_name'] = $fileOriginalName;
                    }
                } elseif ($cType == 3) {
                    if ($request->hasFile('file')) {
                        $file = $request->file('file');
                        $fileName = Auth::user()->id . '_1_' . uniqid() . "." . $file->getClientOriginalExtension();
                        $fileOriginalName = $file->getClientOriginalName();

                        $fileArr['path'] = 'public/uploads/content/video';
                        $fileArr['file'] = $file;
                        $fileArr['file_name'] = $fileName;
                        $fileArr['file_original_name'] = $fileOriginalName;
                    }
                }
            }

            if (!empty($fileArr)) {
                $file = $fileArr['file'];
                $fName = $fileArr['file_name'];
                $fPath = $fileArr['path'];
                $uploadSuccess = $file->move($fPath, $fName);
                if ($uploadSuccess) {
                    $fileArr['up'] = 1;
                }
            }
        }


        $prevContentInfo = ContentDetails::orderBy('content_id', 'asc')->pluck('content', 'id')->toArray();
        $contentTypeArr = MediaContentType::orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $contentTypeInfo = MediaContentType::orderBy('order', 'asc')
                ->select('name', 'file_size', 'id', 'description');
        $contentTypeArr = $contentTypeInfo->pluck('name', 'id')->toArray();
        $contentTypeInfo = $contentTypeInfo->get();

        $contentTypeDataArr = [];

        if (!$contentTypeInfo->isEmpty()) {
            foreach ($contentTypeInfo as $typeInfo) {
                $contentTypeDataArr[$typeInfo->id]['id'] = $typeInfo->id;
                $contentTypeDataArr[$typeInfo->id]['name'] = $typeInfo->name;
                $contentTypeDataArr[$typeInfo->id]['file_size'] = $typeInfo->file_size;
                $contentTypeDataArr[$typeInfo->id]['description'] = $typeInfo->description;
            }
        }

        $html = view('content.addContentRow')->with(compact('prevContentInfo', 'contentTypeDataArr'
                                , 'contentTypeArr', 'request', 'fileArr'))->render();
        return response()->json(['html' => $html]);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('content?' . $url);
    }

    public function downloadFile(Request $request) {
        return Common::downloadFile($request);
    }

}
