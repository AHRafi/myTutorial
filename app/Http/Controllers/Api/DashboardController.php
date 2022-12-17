<?php

namespace App\Http\Controllers\Api;

use DB;
use URL;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\CmBasicProfile; //model class
use App\CmOthers; //model class
use App\CmCountryVisit; //model class
use App\CmPermanentAddress; //model class
use App\CmCivilEducation; //model class
use App\CmServiceRecord; //model class
use App\CmRelativeInDefence; //model class
use App\CmChild; //model class
use App\CmPresentAddress; //model class
use App\CmPassport;
use App\CmMission;
use App\CmBank;
use App\TrainingYear;
use Helper;
use Common;

class DashboardController extends Controller {

    public function __construct() {
        //$this->middleware('auth');
    }

    public function index(Request $request) {
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        $course = CmBasicProfile::join('course', 'course.id', 'cm_basic_profile.course_id')
                ->select('course.name', 'course.id', DB::raw('COUNT(cm_basic_profile.id) as total_cm'))
                ->groupBy('course.name', 'course.id')
                ->where('course.training_year_id', $activeTrainingYearInfo->id ?? 0)
                ->where('course.status', '1')
                ->first();


        $courseId = !empty($course->id) ? $course->id : 0;
        //START::Content Summary
        $contentArr = $this->getContentSummary($courseId);
        //END::Content Summary
        if ($request->data['type'] == '1') {
            return $this->getCmDashboard($request, $contentArr);
        } elseif ($request->data['type'] == '2') {
            return $this->getStaffDashboard($request, $contentArr);
        }
    }

    public function getCmDashboard(Request $request, $contentArr) {
        $id = $request->data['cm_id'];
        $today = $request->data['today'];

        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $percent = 0;

        $cmInfo['personal'] = CmBasicProfile::join('course', 'course.id', 'cm_basic_profile.course_id')
                ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->where('cm_basic_profile.id', $id)
                ->select('rank.code', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name', 'cm_basic_profile.id'
                        , 'cm_basic_profile.father_name', 'cm_basic_profile.commissioning_course_id'
                        , 'cm_basic_profile.marital_status', 'cm_basic_profile.photo', 'cm_basic_profile.wing_id'
                        , 'cm_basic_profile.personal_no', 'course.name as course')
                ->first();
        $cmInfo['basic'] = CmBasicProfile::join('course', 'course.id', 'cm_basic_profile.course_id')
                ->leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                ->where('cm_basic_profile.id', $id)
                ->where('cm_basic_profile.gender', '<>', '0')
                ->where('cm_basic_profile.religion_id', '<>', 0)
                ->whereNotNull('cm_basic_profile.birth_place')
                ->whereNotNull('cm_basic_profile.date_of_birth')
                ->whereNotNull('cm_basic_profile.email')
                ->whereNotNull('cm_basic_profile.number')
                ->select('cm_basic_profile.gender', 'cm_basic_profile.date_of_birth'
                        , 'cm_basic_profile.birth_place', 'cm_basic_profile.religion_id'
                        , 'cm_basic_profile.email', 'cm_basic_profile.number')
                ->first();
        $cmInfo['basic'] = !empty($cmInfo['basic']) ? $cmInfo['basic']->toArray() : [];

        if (!empty($cmInfo['personal'])) {
            $cmInfo['personal'] = $cmInfo['personal']->toArray();
            $percent += 1;
            $percent += ($cmInfo['personal']['photo'] != '') ? 1 : 0;
            $percent += (!empty($cmInfo['personal']['commissioning_course_id'])) ? 1 : 0;
            $percent += ($cmInfo['personal']['marital_status'] != 0) ? 1 : 0;
        }

        $cmInfo['civil_edu'] = CmCivilEducation::where('cm_basic_profile_id', $id)->select('id')->first();

        $cmInfo['service_rec'] = CmServiceRecord::where('cm_basic_profile_id', $id)->select('id')->first();

        $cmInfo['msn'] = CmMission::where('cm_basic_profile_id', $id)->select('id')->first();

        $cmInfo['country_visit'] = CmCountryVisit::where('cm_basic_profile_id', $id)->select('id')->first();

        $cmInfo['bank'] = CmBank::where('cm_basic_profile_id', $id)->select('id')->first();

        $cmInfo['child'] = CmChild::where('cm_basic_profile_id', $id)->select('id')->first();

        $cmInfo['defence_relative'] = CmRelativeInDefence::where('cm_basic_profile_id', $id)->select('id')->first();

        $cmInfo['others'] = CmOthers::where('cm_basic_profile_id', $id)->select('id')->first();

        $cmInfo['passport'] = CmPassport::where('cm_basic_profile_id', $id)->select('id')->first();



        //Division District Thana for cm permanent address
        $cmInfo['permanent_address'] = CmPermanentAddress::where('cm_basic_profile_id', $id)->select('id')->first();
        $cmInfo['present_address'] = CmPresentAddress::where('cm_basic_profile_id', $id)->select('id')->first();

        $percent += !empty($cmInfo['basic']) ? 1 : 0;
        $percent += !empty($cmInfo['civil_edu']) ? 1 : 0;
        $percent += !empty($cmInfo['service_rec']) ? 1 : 0;
//        $percent += !empty($cmInfo['msn']) ? 1 : 0;
        $percent += !empty($cmInfo['country_visit']) ? 1 : 0;
        $percent += !empty($cmInfo['bank']) ? 1 : 0;
        $percent += !empty($cmInfo['defence_relative']) ? 1 : 0;
        $percent += !empty($cmInfo['others']) ? 1 : 0;
        $percent += !empty($cmInfo['passport']) ? 1 : 0;
        $percent += !empty($cmInfo['permanent_address']) ? 1 : 0;
        $percent += !empty($cmInfo['present_address']) ? 1 : 0;
        $percent = ($percent / 14 ) * 100;
        $cmInfo['percent'] = $percent;
        $cmInfo['content_arr'] = $contentArr;
        return response()->json(['result' => $cmInfo, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function getStaffDashboard(Request $request, $contentArr) {
        $id = $request->data['staff_id'];
        $today = $request->data['today'];

        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }



        $staffInfo = [];
        $staffInfo['content_arr'] = $contentArr;
        return response()->json(['result' => $staffInfo, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function getContentSummary($courseId) {
        return Common::getContentSummary($courseId);
    }

}
