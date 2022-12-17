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
use App\Staff;
use App\MediaContentType;
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

class CourseWiseDocSummaryController extends Controller {

    public function index(Request $request) {
        $activeTrainingYearInfo = TrainingYear::select('name', 'id')->where('status', '1')->first();
        $activeCourseInfo = Course::where('training_year_id', $activeTrainingYearInfo->id ?? 0)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();

        $courseList = Course::orderBy('id', 'desc')->pluck('name', 'id')->toArray();
        $contentTypeList = MediaContentType::orderBy('order', 'asc')->pluck('name', 'id')->toArray();


        $targetArr = $courseIds = $selectedCourses = $courseInfo = [];
        if ($request->generate == 'true') {

            if (!empty($request->course_id)) {
                $courseIds = $request->course_id;
            } else {
                $courseIds = !empty($request->course) ? explode(",", $request->course) : [];
            }

            $selectedCourses = Course::whereIn('course.id', $courseIds)
                    ->pluck('course.name', 'course.id')
                    ->toArray();

            $contentArr = Content::join('content_details', 'content_details.content_id', 'content.id');

            if (!empty($courseIds)) {
                $contentArr = $contentArr->whereIn('course_id', $courseIds);
            }

            // start: ds will get only active courses contents check
            $courseInfo = Course::where('status', '1')
                    ->select('id')
                    ->first();
            if (Auth::user()->group_id == '4') {
                $contentArr = $contentArr->where('content.course_id', $courseInfo->id);
            }
            // end: ds will get only active courses contents check 


            $contentArr = $contentArr->select(DB::raw('count(content_details.content_type_id) as total_content'), 'content.course_id', 'content_details.content_type_id')
                            ->groupBy('content.course_id')
                            ->groupBy('content_details.content_type_id')->get();

            $targetArr = [];
            if (!$contentArr->isEmpty()) {
                foreach ($contentArr as $item) {
                    //$targetArr[$item->id] = $item->toArray();
                    $targetArr[$item->course_id][$item->content_type_id] = isset($targetArr[$item->course_id][$item->content_type_id]) ? $targetArr[$item->course_id][$item->content_type_id] : 0;
                    $targetArr[$item->course_id][$item->content_type_id] += $item->total_content;
                }
            }
        }


        return view('referenceArchive.courseWiseDocSummary.index', compact('request', 'activeCourseInfo', 'courseList', 'courseIds', 'targetArr', 'selectedCourses', 'contentTypeList'));
    }

    public function filter(Request $request) {
//        print_r($request->all());
        $rules = $messages = [];

        $rules = [
            'course' => 'required|not_in:0'
        ];

        $messages = [
            'course.not_in' => __('label.COURSE_FIELD_IS_REQUIRED'),
        ];

        if (!empty($request->course_id)) {
            $url = 'course_id=' . $request->course_id;
        } else {
            $courseIds = !empty($request->course) ? implode(",", $request->course) : '';
            $url = 'course=' . $courseIds;
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('courseWiseDocSummary?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }

        return redirect('courseWiseDocSummary?generate=true&' . $url);
    }

}
