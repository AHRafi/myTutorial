<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\CmBasicProfile; //model class
use App\TrainingYear; //model class
use App\Rank; //model class
use App\ServiceAppointment; //model class
use App\Wing; //model class
use App\Unit; //model class
use App\Course; //model class
use App\CommissioningCourse; //model class
use App\Religion; //model class
use App\ArmsService; //model class
use App\CmOthers; //model class
use App\Country; //model class
use App\Division; //model class
use App\District; //model class
use App\CmCountryVisit; //model class
use App\Thana; //model class
use App\CmPermanentAddress; //model class
use App\CmCivilEducation; //model class
use App\CmServiceRecord; //model class
use App\CmRelativeInDefence; //model class
use App\CmChild; //model class
use App\CmPresentAddress; //model class
use App\CmPassport;
use App\MilCourse;
use App\CmMission;
use App\CmBank;
use App\Decoration;
use App\Hobby;
use App\Award;
use Validator;
use Session;
use Response;
use Redirect;
use Auth;
use File;
use PDF;
use URL;
use Hash;
use Common;
use DB;
use Helper;
use Illuminate\Http\Request;

class CmController extends Controller {

    public function updatePassword(Request $request) {

        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['message' => $authRes['message'], 'status' => $authRes['status']]);
        }
        $uType = !empty($request->data['u_type']) ? $request->data['u_type'] : '1';
        $modelNameSpace = $uType == '1' ? '\\App\\CmBasicProfile' : ($uType == '1' ? '\\App\\Staff' : '\\App\\CmBasicProfile');
        $update['password'] = $request->data['password'];
        if ($modelNameSpace::where('id', $request->data['id'])->update($update)) {
            $authRes['message'] = __('label.PASSWORD_UPDATED_SUCCESSFULLY');
            $authRes['status'] = 200;
        } else {
            $authRes['message'] = __('label.PASSWORD_COULD_NOT_BE_UPDATED');
            $authRes['status'] = 401;
        }
        return response()->json(['message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    //*********** Start :: CM Profile **********************//
    public function profile(Request $request) {
//        echo '<pre>';        print_r($id); exit;
        $keyAppt = [];
        $id = $request->data['id'];

        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }


        $cmInfo['personal'] = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->leftJoin('course', 'course.id', '=', 'cm_basic_profile.course_id')
                        ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                        ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                        ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                        ->leftJoin('religion', 'religion.id', '=', 'cm_basic_profile.religion_id')
                        ->select('cm_basic_profile.id as cm_basic_profile_id', 'cm_basic_profile.email'
                                , 'cm_basic_profile.photo', 'cm_basic_profile.number', 'cm_basic_profile.gender'
                                , 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                                , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.official_name, ')') as cm_name")
                                , 'course.name as course', 'arms_service.name as arms_service'
                                , 'commissioning_course.name as commissioning_course', 'rank.code as rank'
                                , 'religion.name as religion', 'cm_basic_profile.*', 'wing.code as wing')
                        ->where('cm_basic_profile.status', '1')
                        ->where('cm_basic_profile.id', $id)
                        ->first()->toArray();

        $cmInfo['civil_edu'] = CmCivilEducation::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'civil_education_info')
                ->first();
        if (!empty($cmInfo['civil_edu'])) {
            $cmInfo['civil_edu'] = $cmInfo['civil_edu']->toArray();
            $cmInfo['civil_edu']['info'] = !empty($cmInfo['civil_edu']['civil_education_info']) ? json_decode($cmInfo['civil_edu']['civil_education_info'], TRUE) : [];
        }

        $cmInfo['service_rec'] = CmServiceRecord::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'service_record_info')
                ->first();
        if (!empty($cmInfo['service_rec'])) {
            $cmInfo['service_rec'] = $cmInfo['service_rec']->toArray();
            $cmInfo['service_rec']['info'] = !empty($cmInfo['service_rec']['service_record_info']) ? json_decode($cmInfo['service_rec']['service_record_info'], TRUE) : [];
            if (!empty($cmInfo['service_rec']['info'])) {
                foreach ($cmInfo['service_rec']['info'] as $key => $info) {
                    $cmInfo['key_appt'][$info['appointment']] = $info['appointment'];
                }
            }
        }

        $cmInfo['resp_list'] = Common::getSvcResposibilityList();

        $cmInfo['msn'] = CmMission::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'msn_info')
                ->first();
        if (!empty($cmInfo['msn'])) {
            $cmInfo['msn'] = $cmInfo['msn']->toArray();
            $cmInfo['msn']['info'] = !empty($cmInfo['msn']['msn_info']) ? json_decode($cmInfo['msn']['msn_info'], TRUE) : [];
            if (!empty($cmInfo['msn']['info'])) {
                foreach ($cmInfo['msn']['info'] as $key => $info) {
                    $cmInfo['key_appt'][$info['appointment']] = $info['appointment'];
                }
            }
        }


        $cmInfo['country_visit'] = CmCountryVisit::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'visit_info')
                ->first();
        if (!empty($cmInfo['country_visit'])) {
            $cmInfo['country_visit'] = $cmInfo['country_visit']->toArray();
            $cmInfo['country_visit']['info'] = !empty($cmInfo['country_visit']['visit_info']) ? json_decode($cmInfo['country_visit']['visit_info'], TRUE) : [];
        }

        $cmInfo['bank'] = CmBank::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'bank_info')
                ->first();
        if (!empty($cmInfo['bank'])) {
            $cmInfo['bank'] = $cmInfo['bank']->toArray();
            $cmInfo['bank']['info'] = !empty($cmInfo['bank']['bank_info']) ? json_decode($cmInfo['bank']['bank_info'], TRUE) : [];
        }

        $cmInfo['child'] = CmChild::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'cm_child_info', 'no_of_child')
                ->first();
        if (!empty($cmInfo['child'])) {
            $cmInfo['child'] = $cmInfo['child']->toArray();
            $cmInfo['child']['info'] = !empty($cmInfo['child']['cm_child_info']) ? json_decode($cmInfo['child']['cm_child_info'], TRUE) : [];
        }

        $cmInfo['mil_qual'] = CmRelativeInDefence::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'cm_relative_info as mil_qual_info')
                ->first();
        if (!empty($cmInfo['mil_qual'])) {
            $cmInfo['mil_qual'] = $cmInfo['mil_qual']->toArray();
            $cmInfo['mil_qual']['info'] = !empty($cmInfo['mil_qual']['mil_qual_info']) ? json_decode($cmInfo['mil_qual']['mil_qual_info'], TRUE) : [];

            if (!empty($cmInfo['mil_qual']['info'])) {
                foreach ($cmInfo['mil_qual']['info'] as $mKey => $mInfo) {
                    $type = !empty($mInfo['course']) ? Common::getMilCourseType($mInfo['course']) : '0';
                    $cmInfo['mil_qual']['data'][$type][$mKey] = $mInfo;
                }
            }
        }


        $cmInfo['decoration_list'] = Decoration::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $cmInfo['award_list'] = Award::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $cmInfo['hobby_list'] = Hobby::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();

        $cmInfo['other'] = CmOthers::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'decoration_id', 'hobby_id', 'extra_curriclar_expt', 'admin_resp_appt')
                ->first();

        if (!empty($cmInfo['other'])) {
            $cmInfo['other'] = $cmInfo['other']->toArray();
            $decoration = $hobby = '';
            $cmInfo['other']['ext_curr_expt'] = !empty($cmInfo['other']['extra_curriclar_expt']) ? explode(",", $cmInfo['other']['extra_curriclar_expt']) : [];
            $cmInfo['other']['admin_responsibility_appt'] = !empty($cmInfo['other']['admin_resp_appt']) ? explode(",", $cmInfo['other']['admin_resp_appt']) : [];

            $decorationArr = !empty($cmInfo['other']['decoration_id']) ? explode(",", $cmInfo['other']['decoration_id']) : [];
            if (!empty($decorationArr)) {
                foreach ($decorationArr as $dKey => $dec) {
                    $comma = array_key_last($decorationArr) != $dKey ? ',' : '';
                    $decoration .= !empty($cmInfo['decoration_list'][$dec]) ? $cmInfo['decoration_list'][$dec] . $comma : (!empty($dec) ? $dec . $comma : '');
                    $cmInfo['other']['decoration'][$dKey] = !empty($cmInfo['decoration_list'][$dec]) ? $cmInfo['decoration_list'][$dec] : (!empty($dec) ? $dec : '');
                }
            }
            $cmInfo['other']['decoration_id'] = $decoration;

            $hobbyArr = !empty($cmInfo['other']['hobby_id']) ? explode(",", $cmInfo['other']['hobby_id']) : [];
            if (!empty($hobbyArr)) {
                foreach ($hobbyArr as $hKey => $hob) {
                    $comma = array_key_last($hobbyArr) != $hKey ? ',' : '';
                    $hobby .= !empty($cmInfo['hobby_list'][$hob]) ? $cmInfo['hobby_list'][$hob] . $comma : (!empty($hob) ? $hob . $comma : '');
                    $cmInfo['other']['hobby'][$hKey] = !empty($cmInfo['hobby_list'][$hob]) ? $cmInfo['hobby_list'][$hob] : (!empty($hob) ? $hob : '');
                }
            }
            $cmInfo['other']['hobby_id'] = $hobby;
        }

        $cmInfo['passport'] = CmPassport::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'passport_no', 'place_of_issue', 'date_of_issue', 'date_of_expire'
                        , 'pass_scan_copy', 'pass_scan_copy_name', 'photo_without_uniform')
                ->first();
        $cmInfo['passport'] = !empty($cmInfo['passport']) ? $cmInfo['passport']->toArray() : [];


        $cmInfo['commission_type_list'] = Common::getCommissionType();
        $cmInfo['gender_list'] = Common::getGenderList();
        $cmInfo['blood_group_list'] = Common::getBloodGroup();

        $cmInfo['rank_list'] = array('0' => __('label.SELECT_RANK_OPT')) + Rank::join('wing', 'wing.id', '=', 'rank.wing_id')
                        ->where('rank.wing_id', $cmInfo['personal']['wing_id'])
                        ->where('rank.status', '1')
                        ->where('rank.for_course_member', '1')
                        ->orderBy('rank.order', 'asc')
                        ->pluck('rank.code', 'rank.id')->toArray();

        $cmInfo['wing_list'] = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('code', 'id')->toArray();


        $cmInfo['religion_list'] = ['0' => __('label.SELECT_RELIGION_OPT')] + Religion::pluck('name', 'id')->toArray();
        $cmInfo['appt_list'] = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $cmInfo['all_appt_list'] = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $cmInfo['arms_svc_list'] = ['0' => __('label.SELECT_ARMS_SERVICE_OPT')] + ArmsService::pluck('code', 'id')->toArray();
        $cmInfo['unit_list'] = ['0' => __('label.SELECT_UNIT_OPT')] + Unit::pluck('code', 'id')->toArray();
        $cmInfo['marital_status_list'] = ['0' => __('label.SELECT_MARITAL_STATUS_OPT')] + Helper::getMaritalStatus();
        $cmInfo['country_list'] = Country::pluck('name', 'id')->toArray();
        $cmInfo['course_list'] = ['0' => __('label.SELECT_COURSE_OPT')] + Course::pluck('name', 'id')->toArray();
        $cmInfo['organization_list'] = ['0' => __('label.SELECT_UNIT_FMN_INST_OPT')] + Unit::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $cmInfo['mil_course_list'] = ['0' => __('label.SELECT_COURSE_OPT')] + MilCourse::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('short_info', 'id')->toArray();
        $cmInfo['result_list'] = Common::getResultList();

        $cmInfo['commissioning_course_list'] = array('0' => __('label.SELECT_COMMISSIONING_COURSE_OPT')) + CommissioningCourse::where('wing_id', $cmInfo['personal']['wing_id'])
                        ->where('status', '1')->orderBy('commissioning_date', 'asc')
                        ->pluck('name', 'id')->toArray();

        //Division District Thana for cm permanent address
        $cmInfo['permanent_address'] = CmPermanentAddress::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details', 'same_as_present')
                ->first();
        $cmInfo['permanent_address'] = !empty($cmInfo['permanent_address']) ? $cmInfo['permanent_address']->toArray() : [];

        $cmInfo['present_address'] = CmPresentAddress::where('cm_basic_profile_id', $id)
                ->select('id', 'cm_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details')
                ->first();
        $cmInfo['present_address'] = !empty($cmInfo['present_address']) ? $cmInfo['present_address']->toArray() : [];


        $cmInfo['present_district_list'] = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', !empty($cmInfo['present_address']['division_id']) ? $cmInfo['present_address']['division_id'] : 0)
                        ->pluck('name', 'id')->toArray();
        $cmInfo['present_thana_list'] = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', !empty($cmInfo['present_address']['district_id']) ? $cmInfo['present_address']['district_id'] : 0)
                        ->pluck('name', 'id')->toArray();

        $cmInfo['division_list'] = ['0' => __('label.SELECT_DIVISION_OPT')] + Division::pluck('name', 'id')->toArray();
        $cmInfo['district_list'] = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', !empty($cmInfo['permanent_address']['division_id']) ? $cmInfo['permanent_address']['division_id'] : 0)
                        ->pluck('name', 'id')->toArray();
        $cmInfo['thana_list'] = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', !empty($cmInfo['permanent_address']['district_id']) ? $cmInfo['permanent_address']['district_id'] : 0)
                        ->pluck('name', 'id')->toArray();
        $cmInfo['spouse_profession'] = Common::getSpouseProfessionList();


        return response()->json(['result' => $cmInfo, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function getRank(Request $request) {
        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }


        $cmInfo['rank_list'] = array('0' => __('label.SELECT_RANK_OPT')) + Rank::where('wing_id', $qpArr['wing_id'])
                        ->where('status', '1')->where('for_course_member', '1')
                        ->orderBy('order', 'asc')->pluck('code', 'id')->toArray();

        $cmInfo['arms_svc_list'] = ['0' => __('label.SELECT_ARMS_SERVICE_OPT')] + ArmsService::where('wing_id', $qpArr['wing_id'])
                        ->pluck('code', 'id')->toArray();

        $cmInfo['commissioning_course_list'] = array('0' => __('label.SELECT_COMMISSIONING_COURSE_OPT')) + CommissioningCourse::where('wing_id', $qpArr['wing_id'])
                        ->where('status', '1')->orderBy('commissioning_date', 'asc')
                        ->pluck('name', 'id')->toArray();

        return response()->json(['result' => $cmInfo, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function updatePersonalInfo(Request $request) {
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update

        $targetArr = [
            'wing_id' => $qpArr['wing_id'],
            'rank_id' => $qpArr['rank_id'],
            'arms_service_id' => $qpArr['arms_service_id'],
            'personal_no' => $qpArr['personal_no'],
            'full_name' => strip_tags($qpArr['full_name'], '<b>'),
            'bn_name' => $qpArr['bn_name'],
            'official_name' => $qpArr['official_name'],
            'father_name' => $qpArr['father_name'],
            'username' => $qpArr['username'],
            'commission_type' => $qpArr['commission_type'],
            'commissioning_course_id' => $qpArr['commissioning_course_id'],
            'ante_date_seniority' => $qpArr['ante_date_seniority'] ?? null,
            'commisioning_date' => Helper::dateFormatConvert($qpArr['commisioning_date']),
            'date_of_birth' => Helper::dateFormatConvert($qpArr['date_of_birth']),
            'birth_place' => $qpArr['birth_place'],
            'religion_id' => $qpArr['religion_id'],
            'email' => $qpArr['email'],
            'number' => $qpArr['number'],
            'blood_group' => $qpArr['blood_group'],
            'gender' => $qpArr['gender'],
        ];

        if (CmBasicProfile::where('id', $qpArr['cm_basic_profile_id'])->update($targetArr)) {
            return response()->json(['message' => 'success', 'status' => 200]);
        } else {
            return response()->json(['message' => 'error', 'status' => 401]);
        }
    }

    public function updatePhoto(Request $request) {
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $target = CmBasicProfile::where('id', $request->data['cm_basic_profile_id'])->first();
        $previousFileName = $target->photo;

        //begin back same page after update
        $qpArr = $request->data;



        if (!empty($qpArr['encoded_photo'])) {
            $prevfileName = 'public/uploads/cm/' . $target->photo;

            if (File::exists($prevfileName)) {
                File::delete($prevfileName);
            }
        }
        $fileName = '';
        $file = !empty($qpArr['encoded_photo']) ? base64_decode($qpArr['encoded_photo']) : '';
        if (!empty($file)) {
            $fileName = $qpArr['file_name'];
            file_put_contents('public/uploads/cm/' . $qpArr['file_name'], $file);
        }


        $targetArr = [
            'photo' => (!empty($fileName) ? $fileName : $previousFileName),
        ];

        if (CmBasicProfile::where('id', $qpArr['cm_basic_profile_id'])->update($targetArr)) {
            return response()->json(['message' => 'success', 'status' => 200]);
        } else {
            return response()->json(['message' => 'error', 'status' => 401]);
        }
    }

    public function updateMaritalStatus(Request $request) {
        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $cmBasicProfileArr['marital_status'] = $qpArr['marital_status'];
        $cmBasicProfileArr['date_of_marriage'] = !empty($qpArr['date_of_marriage']) ? Helper::dateFormatConvert($qpArr['date_of_marriage']) : null;
        $cmBasicProfileArr['spouse_dob'] = !empty($qpArr['spouse_date_of_birth']) ? Helper::dateFormatConvert($qpArr['spouse_date_of_birth']) : null;
        $cmBasicProfileArr['spouse_name'] = $qpArr['spouse_name'] ?? null;
        $cmBasicProfileArr['spouse_nick_name'] = $qpArr['spouse_nick_name'] ?? null;
        $cmBasicProfileArr['spouse_mobile'] = $qpArr['spouse_mobile'] ?? null;
        $cmBasicProfileArr['spouse_occupation'] = $qpArr['spouse_occupation'] ?? null;
        $cmBasicProfileArr['spouse_work_address'] = $qpArr['spouse_work_address'] ?? null;

        $cmChildInfo = CmChild::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $cmChildInfo = !empty($cmChildInfo->id) ? CmChild::find($cmChildInfo->id) : new CmChild;

        $childInfo = !empty($qpArr['child']) ? json_encode($qpArr['child']) : '';
        $cmChildInfo->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $cmChildInfo->no_of_child = !empty($qpArr['no_of_child']) ? $qpArr['no_of_child'] : 0;
        $cmChildInfo->cm_child_info = $childInfo;
        $cmChildInfo->updated_at = date('Y-m-d H:i:s');
        $cmChildInfo->updated_by = $qpArr['cm_basic_profile_id'];

        if (CmBasicProfile::where('id', $qpArr['cm_basic_profile_id'])->update($cmBasicProfileArr) && $cmChildInfo->save()) {
            return response()->json(['result' => [], 'message' => __('label.CM_MARITAL_STATUS_UPDATED_SUCCESSFULLY'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_CM_MARITAL_STATUS'), 'status' => 401]);
        }

        //End updateMaritialStatus function
    }

    //For Districts
    public function getDistrict(Request $request) {
        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $districtList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', $qpArr['division_id'])
                        ->pluck('name', 'id')->toArray();
        return response()->json(['result' => $districtList, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    //For Thana
    public function getThana(Request $request) {
        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $thanaList = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', $qpArr['district_id'])->pluck('name', 'id')->toArray();

        return response()->json(['result' => $thanaList, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function updateAddress(Request $request) {
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $cmPermanentAddress = CmPermanentAddress::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $cmPermanentAddressInfo = !empty($cmPermanentAddress->id) ? CmPermanentAddress::find($cmPermanentAddress->id) : new CmPermanentAddress;


        $cmPresentAddressInfo = CmPresentAddress::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $cmPresentAddressInfo = !empty($cmPresentAddressInfo->id) ? CmPresentAddress::find($cmPresentAddressInfo->id) : new CmPresentAddress;


        $cmPresentAddressInfo->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $cmPresentAddressInfo->division_id = $qpArr['present_division_id'];
        $cmPresentAddressInfo->district_id = $qpArr['present_district_id'];
        $cmPresentAddressInfo->thana_id = $qpArr['present_thana_id'];
        $cmPresentAddressInfo->address_details = $qpArr['present_address_details'];
        $cmPresentAddressInfo->updated_at = date('Y-m-d H:i:s');
        $cmPresentAddressInfo->updated_by = $qpArr['cm_basic_profile_id'];

        $cmPermanentAddressInfo->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];

        if (!empty($qpArr['for_addr'])) {
            $cmPermanentAddressInfo->division_id = $qpArr['present_division_id'];
            $cmPermanentAddressInfo->district_id = $qpArr['present_district_id'];
            $cmPermanentAddressInfo->thana_id = $qpArr['present_thana_id'];
            $cmPermanentAddressInfo->address_details = $qpArr['present_address_details'];
        } else {
            $cmPermanentAddressInfo->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
            $cmPermanentAddressInfo->division_id = $qpArr['permanent_division_id'];
            $cmPermanentAddressInfo->district_id = $qpArr['permanent_district_id'];
            $cmPermanentAddressInfo->thana_id = $qpArr['permanent_thana_id'];
            $cmPermanentAddressInfo->address_details = $qpArr['permanent_address_details'];
        }

        $cmPermanentAddressInfo->same_as_present = $qpArr['for_addr'] ?? '0';
        $cmPermanentAddressInfo->updated_at = date('Y-m-d H:i:s');
        $cmPermanentAddressInfo->updated_by = $qpArr['cm_basic_profile_id'];

        if ($cmPermanentAddressInfo->save() && $cmPresentAddressInfo->save()) {
            return response()->json(['result' => [], 'message' => __('label.CM_ADDRESS_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_ADDRESS'), 'status' => 401]);
        }
    }

    public function rowAddForCivilEducation() {
        $html = view('cm.details.civilEducationRowAdd')->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function updateCivilEducationInfo(Request $request) {
        $qpArr = $request->data;
        //end back same page after update

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $civilEducationInfo = CmCivilEducation::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $civilEducationProfile = !empty($civilEducationInfo->id) ? CmCivilEducation::find($civilEducationInfo->id) : new CmCivilEducation;



        $civilEducation = json_encode($qpArr['academic_qual']);
        $civilEducationProfile->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $civilEducationProfile->civil_education_info = $civilEducation;
        $civilEducationProfile->updated_at = date('Y-m-d H:i:s');
        $civilEducationProfile->updated_by = $qpArr['cm_basic_profile_id'];

        //Update cm civil education
        if ($civilEducationProfile->save()) {
            return response()->json(['result' => [], 'message' => __('label.ACADEMIC_QUALIFICATION_INFO_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_ACADEMIC_QUAL'), 'status' => 401]);
        }
    }

    public function rowAddForServiceRecord(Request $request) {

        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $respList = Common::getSvcResposibilityList();

        return response()->json(['result' => $respList, 'message' => $authRes['message'], 'status' => $authRes['status']]);

        ////End rowAdd function
    }

    public function updateServiceRecordInfo(Request $request) {
        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $serviceEducationInfo = CmServiceRecord::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $serviceEducationProfile = !empty($serviceEducationInfo->id) ? CmServiceRecord::find($serviceEducationInfo->id) : new CmServiceRecord;

        $serviceRecord = json_encode($qpArr['service_record']);
        $serviceEducationProfile->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $serviceEducationProfile->service_record_info = $serviceRecord;
        $serviceEducationProfile->updated_at = date('Y-m-d H:i:s');
        $serviceEducationProfile->updated_by = $qpArr['cm_basic_profile_id'];

        //Update cm service record
        if ($serviceEducationProfile->save()) {
            return response()->json(['result' => [], 'message' => __('label.SERVICE_RECORD_INFO_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_RECORD_OF_SERVICE'), 'status' => 401]);
        }
    }

    public function rowAddForUnMsn() {
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $html = view('cm.details.unMsnRowAdd', compact('appointmentList'))->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForCountry() {
        $html = view('cm.details.newVisitedCountryRow')->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForChild(Request $request) {
        $noOfChild = $request->no_of_child;
        $html = view('cm.details.newChildRow', compact('noOfChild'))->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function rowAddForBank() {
        $html = view('cm.details.newBankRow')->render();
        return response()->json(['html' => $html]);
        ////End rowAdd function
    }

    public function updateUnMsn(Request $request) {
        $qpArr = $request->data;
        //end back same page after update

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $unMsnInfo = CmMission::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $unMsnInfo = !empty($unMsnInfo->id) ? CmMission::find($unMsnInfo->id) : new CmMission;

        $msnRecord = json_encode($qpArr['un_msn']);
        $unMsnInfo->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $unMsnInfo->msn_info = $msnRecord;
        $unMsnInfo->updated_at = date('Y-m-d H:i:s');
        $unMsnInfo->updated_by = $qpArr['cm_basic_profile_id'];

        //Update cm UN Msn record
        if ($unMsnInfo->save()) {
            return response()->json(['result' => [], 'message' => __('label.UN_MSN_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_UN_MSN'), 'status' => 401]);
        }
    }

    public function updateCountryVisit(Request $request) {
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $visitInfo = CmCountryVisit::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $visitInfo = !empty($visitInfo->id) ? CmCountryVisit::find($visitInfo->id) : new CmCountryVisit;

        $visitRecord = json_encode($qpArr['country_visit']);
        $visitInfo->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $visitInfo->visit_info = $visitRecord;
        $visitInfo->updated_at = date('Y-m-d H:i:s');
        $visitInfo->updated_by = $qpArr['cm_basic_profile_id'];

        if ($visitInfo->save()) {
            return response()->json(['result' => [], 'message' => __('label.COUNTRY_VISITED_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_COUNTRY_VISITED'), 'status' => 401]);
        }
    }

    public function updateBank(Request $request) {
        $qpArr = $request->data;
        //end back same page after update

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $bankInfo = CmBank::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $bankInfo = !empty($bankInfo->id) ? CmBank::find($bankInfo->id) : new CmBank;

        $bankRecord = json_encode($qpArr['bank']);
        $bankInfo->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $bankInfo->bank_info = $bankRecord;
        $bankInfo->updated_at = date('Y-m-d H:i:s');
        $bankInfo->updated_by = $qpArr['cm_basic_profile_id'];

        //Update cm bank record
        if ($bankInfo->save()) {
            return response()->json(['result' => [], 'message' => __('label.BANK_ACCOUNT_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_BANK_ACCOUNT'), 'status' => 401]);
        }
    }

    public function rowAddForMilQual(Request $request) {
        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $milCourseList = ['0' => __('label.SELECT_COURSE_OPT')] + MilCourse::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('short_info', 'id')->toArray();

        $resultList = Common::getResultList();

        return response()->json(['result' => $milCourseList , 'result2' => $resultList, 'message' => $authRes['message'], 'status' => $authRes['status']]);
    }

    public function updateMilQualInfo(Request $request) {
        //begin back same page after update
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $defenceRecordInfo = CmRelativeInDefence::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $defenceRecordProfile = !empty($defenceRecordInfo->id) ? CmRelativeInDefence::find($defenceRecordInfo->id) : new CmRelativeInDefence;

        $defenceRecord = json_encode($qpArr['mil_qual']);
        $defenceRecordProfile->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $defenceRecordProfile->cm_relative_info = $defenceRecord;
        $defenceRecordProfile->updated_at = date('Y-m-d H:i:s');
        $defenceRecordProfile->updated_by = $qpArr['cm_basic_profile_id'];

        //Update cm mil qual record
        if ($defenceRecordProfile->save()) {
            return response()->json(['result' => [], 'message' => __('label.MIL_QUAL_INFO_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_MIL_QUAL'), 'status' => 401]);
        }
    }

    public function updatePassportDetails(Request $request) {

        $qpArr = $request->data;
        //end back same page after update

        $authRes = Common::getHeaderAuth($request->header);


        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $prevPassportInfo = CmPassport::select('id', 'pass_scan_copy', 'pass_scan_copy_name', 'photo_without_uniform')
                        ->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();



        if (!empty($qpArr['encoded_photo']) && !empty($prevPassportInfo->photo_without_uniform)) {
            $prevfilePName = 'public/uploads/cmPassPhoto/' . $prevPassportInfo->photo_without_uniform;

            if (File::exists($prevfilePName)) {
                File::delete($prevfilePName);
            }
        }
        $filePName = '';
        $fileP = !empty($qpArr['encoded_photo']) ? base64_decode($qpArr['encoded_photo']) : '';
        if (!empty($fileP)) {
            $filePName = $qpArr['ph_name'];
            file_put_contents('public/uploads/cmPassPhoto/' . $filePName, $fileP);
        }


        if (!empty($qpArr['encoded_copy']) && !empty($prevPassportInfo->pass_scan_copy)) {
            $prevfileName = 'public/uploads/cmPassport/' . $prevPassportInfo->pass_scan_copy;

            if (File::exists($prevfileName)) {
                File::delete($prevfileName);
            }
        }
        $fileName = '';
        $file = !empty($qpArr['encoded_copy']) ? base64_decode($qpArr['encoded_copy']) : '';
        if (!empty($file)) {
            $fileName = $qpArr['file_name'];
            file_put_contents('public/uploads/cmPassport/' . $qpArr['file_name'], $file);
        }

        $scanPhotoCopy = !empty($qpArr['ph_name']) ? $qpArr['ph_name'] : (!empty($prevPassportInfo->photo_without_uniform) ? $prevPassportInfo->photo_without_uniform : null);
        $scanCopy = !empty($qpArr['file_name']) ? $qpArr['file_name'] : (!empty($prevPassportInfo->pass_scan_copy) ? $prevPassportInfo->pass_scan_copy : null);
        $scanCopyName = !empty($qpArr['file_original_name']) ? $qpArr['file_original_name'] : (!empty($prevPassportInfo->pass_scan_copy_name) ? $prevPassportInfo->pass_scan_copy_name : null);

        $passportInfo = !empty($prevPassportInfo->id) ? CmPassport::find($prevPassportInfo->id) : new CmPassport;
//        return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_PASSPORT_DETAILS'), 'status' => 401]);
        $passportInfo->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $passportInfo->passport_no = $qpArr['passport_no'];
        $passportInfo->place_of_issue = $qpArr['place_of_issue'];
        $passportInfo->date_of_issue = Helper::dateFormatConvert($qpArr['date_of_issue']);
        $passportInfo->date_of_expire = Helper::dateFormatConvert($qpArr['date_of_expire']);
        $passportInfo->pass_scan_copy = $scanCopy;
        $passportInfo->pass_scan_copy_name = $scanCopyName;
        $passportInfo->photo_without_uniform = $scanPhotoCopy;
        $passportInfo->updated_at = date('Y-m-d H:i:s');
        $passportInfo->updated_by = $qpArr['cm_basic_profile_id'];

        if ($passportInfo->save()) {
            return response()->json(['result' => [], 'message' => __('label.PASSPORT_DETAILS_INFO_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_PASSPORT_DETAILS'), 'status' => 401]);
        }
    }

    public function updateCmOthersInfo(Request $request) {
        $qpArr = $request->data;
        //end back same page after update
        $authRes = Common::getHeaderAuth($request->header);

        if ($authRes['status'] == 419) {
            return response()->json(['result' => [], 'message' => $authRes['message'], 'status' => $authRes['status']]);
        }

        $cmOthersInfo = CmOthers::select('id')->where('cm_basic_profile_id', $qpArr['cm_basic_profile_id'])->first();
        $cmOthersProfile = !empty($cmOthersInfo->id) ? CmOthers::find($cmOthersInfo->id) : new CmOthers;

        $decorationInfo = !empty($qpArr['decoration_id']) ? $qpArr['decoration_id'] : '';
        $hobbyInfo = !empty($qpArr['hobby_id']) ? $qpArr['hobby_id'] : '';
        $extCurrInfo = !empty($qpArr['extra_curriclar_expt']) ? $qpArr['extra_curriclar_expt'] : '';
        $adminResAppt = !empty($qpArr['admin_resp_appt']) ? $qpArr['admin_resp_appt'] : '';
        $cmOthersProfile->cm_basic_profile_id = $qpArr['cm_basic_profile_id'];
        $cmOthersProfile->decoration_id = $decorationInfo;
        $cmOthersProfile->hobby_id = $hobbyInfo;
        $cmOthersProfile->extra_curriclar_expt = $extCurrInfo;
        $cmOthersProfile->admin_resp_appt = $adminResAppt;
        $cmOthersProfile->updated_at = date('Y-m-d H:i:s');
        $cmOthersProfile->updated_by = $qpArr['cm_basic_profile_id'];

        if ($cmOthersProfile->save()) {
            return response()->json(['result' => [], 'message' => __('label.OTHER_INFO_UPDATED'), 'status' => $authRes['status']]);
        } else {
            return response()->json(['result' => [], 'message' => __('label.FAILED_TO_UPDATED_OTHER_INFO'), 'status' => 401]);
        }
    }

    //*********** End :: CM Profile **********************//
}
