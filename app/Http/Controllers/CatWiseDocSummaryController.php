<?php

namespace App\Http\Controllers;

use Validator;
use App\ContentCategory;
use App\Course;
use App\TrainingYear;
use App\Appointment;
use App\CmBasicProfile;
use App\User;
use App\Content;
use App\ContentDetails;
use App\DsMarkingGroup;
use App\ContentClassification;
use App\MediaContentType;
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

class CatWiseDocSummaryController extends Controller {

    public function index(Request $request) {
        $contentCategoryList = ContentCategory::orderBy('name', 'asc')->where('status', '1')
                ->pluck('name', 'id')->toArray();
        $contentTypeList = MediaContentType::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        
        $targetArr = $categoryIds = $selectedCategories = $courseInfo = [];
        if ($request->generate == 'true') {
            $categoryIds = !empty($request->category) ? explode(",", $request->category) : [];

            $selectedCategories = ContentCategory::whereIn('content_category.id', $categoryIds)
                    ->where('content_category.status', '1')
                    ->pluck('content_category.name', 'content_category.id')
                    ->toArray();
           
            $contentArr = Content::join('content_details', 'content.id', 'content_details.content_id')
                    ->join('content_category', 'content_category.id', 'content.content_category_id');

            if (!empty($categoryIds)) {
                $contentArr = $contentArr->whereIn('content_category_id', $categoryIds);
            }
            
            
            // start: ds will get only active courses contents check
            $courseInfo = Course::where('status','1')
                          ->select('id')
                          ->first();
            if(Auth::user()->group_id == '4'){
                $contentArr = $contentArr->where('content.course_id',$courseInfo->id);
            }
            // end: ds will get only active courses contents check 

            $contentArr = $contentArr->select(DB::raw('count(content_details.content_type_id) as total_content'), 'content.content_category_id', 'content_details.content_type_id')
                            ->groupBy('content.content_category_id')
                            ->groupBy('content_details.content_type_id')->get();


            $targetArr = [];
            if (!$contentArr->isEmpty()) {
                foreach ($contentArr as $item) {
                    //$targetArr[$item->id] = $item->toArray();
                    $targetArr[$item->content_category_id][$item->content_type_id] = isset($targetArr[$item->content_category_id][$item->content_type_id]) ? $targetArr[$item->content_category_id][$item->content_type_id] : 0;
                    $targetArr[$item->content_category_id][$item->content_type_id] += $item->total_content;
                }
            }

          
        }
        

        return view('referenceArchive.catWiseDocSummary.index', compact('request', 'contentCategoryList', 'targetArr'
                ,'categoryIds','selectedCategories','contentTypeList'));
    }

    public function filter(Request $request) {
//        print_r($request->all());
        $rules = $messages = [];
        
        $rules = [
            
            'category' => 'required|not_in:0'
        ];

        $messages = [
            'category.not_in' => __('label.CAT_FIELD_IS_REQUIRED'),
           
        ];
        
        $categoryIds = !empty($request->category) ? implode(",", $request->category) : '';
        $url = 'category=' . $categoryIds;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('catWiseDocSummary?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }

        return redirect('catWiseDocSummary?generate=true&' . $url);
    }

}
