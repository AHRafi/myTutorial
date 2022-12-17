<?php

namespace App\Http\Controllers;

use Validator;
use App\User; //model class
use App\CmBasicProfile; //model class
use App\UserGroup; //model class
use App\Rank; //model class
use App\CmAppointment; //model class
use App\Wing; //model class
use App\Course; //model class
use App\CommissioningCourse; //model class
use App\Religion; //model class
use App\ArmsService; //model class
use App\Unit; //model class
use App\Formation; //model class
use App\CmBrotherSister; //model class
use App\CmOthers; //model class
use App\Country; //model class
use App\Division; //model class
use App\District; //model class
use App\Thana; //model class
use App\CmPermanentAddress; //model class
use App\CmCivilEducation; //model class
use App\CmServiceRecord; //model class
use App\CmAwardRecord; //model class
use App\CmPunishmentRecord; //model class
use App\CmRelativeInDefence; //model class
use App\CmNextKin; //model class
use App\CmMedicalDetails; //model class
use App\CmWinterCollectiveTraining; //model class
use Session;
use Response;
use Redirect;
use Auth;
use File;
use PDF;
use URL;
use Hash;
use DB;
use Helper;
use Illuminate\Http\Request;

class CmProfileController extends Controller {

    public function __construct() {
        Validator::extend('complexPassword', function($attribute, $value, $parameters) {
            $password = $parameters[1];

            if (preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[!@#$%^&*()])(?=\S*[\d])\S*$/', $password)) {
                return true;
            }
            return false;
        });
    }

    public function index(Request $request) {
        $cmInfoData = User::leftJoin('cm_basic_profile', 'cm_basic_profile.user_id', '=', 'users.id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('unit', 'unit.id', '=', 'cm_basic_profile.unit_id')
                ->leftJoin('religion', 'religion.id', '=', 'cm_basic_profile.religion_id')
                ->leftJoin('formation', 'formation.id', '=', 'cm_basic_profile.formation_id')
                ->leftJoin('cm_appointment', 'cm_appointment.id', '=', 'users.appointment_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                ->leftJoin('course', 'course.id', '=', 'cm_basic_profile.course_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                ->select('users.id as user_id', 'users.email', 'users.photo', 'users.phone', 'users.full_name', 'users.official_name'
                        , 'users.username', DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (', users.official_name, ')') as cm_name")
                        , 'users.appointment_id as cm_appointment_id', 'course.name as course_name', 'arms_service.name as arms_service_name'
                        , 'commissioning_course.name as commissioning_course_name', 'unit.name as unit_name', 'religion.name as religion_name'
                        , 'formation.name as formation_name', 'cm_basic_profile.*'
                )
                ->where('users.group_id', Auth::user()->group_id)
                ->where('users.status', '1')
                ->where('users.id', Auth::user()->id)
                ->first();
        $brotherSisterInfoData = CmBrotherSister::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : 0)
                ->select('id', 'user_id', 'brother_sister_info')
                ->first();
        $civilEducationInfoData = CmCivilEducation::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : 0)
                ->select('id', 'user_id', 'civil_education_info')
                ->first();
        $serviceRecordInfoData = CmServiceRecord::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : 0)
                ->select('id', 'user_id', 'service_record_info')
                ->first();
        $awardRecordInfoData = CmAwardRecord::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : 0)
                ->select('id', 'user_id', 'award_record_info')
                ->first();
        $punishmentRecordInfoData = CmPunishmentRecord::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : 0)
                ->select('id', 'user_id', 'punishment_record_info')
                ->first();
        $defenceRelativeInfoData = CmRelativeInDefence::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : 0)
                ->select('id', 'user_id', 'cm_relative_info')
                ->first();
//        echo '<pre>'; print_r($awardRecordInfoData); exit;
        $othersInfoData = CmOthers::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : 0)
                ->select('id', 'user_id', 'visited_countries_id', 'special_quality', 'professional_computer', 'swimming')
                ->first();

//        echo '<pre>';        print_r($cmInfoData); exit;
        $religionList = ['0' => __('label.SELECT_RELIGION_OPT')] + Religion::pluck('name', 'id')->toArray();
        $appointmentList = ['0' => __('label.SELECT_APPT_OPT')] + CmAppointment::pluck('code', 'id')->toArray();
        $armsServiceList = ['0' => __('label.SELECT_ARMS_SERVICE_OPT')] + ArmsService::pluck('code', 'id')->toArray();
        $unitList = ['0' => __('label.SELECT_UNIT_OPT')] + Unit::pluck('code', 'id')->toArray();
        $formationList = ['0' => __('label.SELECT_FORMATION_OPT')] + Formation::pluck('code', 'id')->toArray();
        $maritalStatusList = ['0' => __('label.SELECT_MARITAL_STATUS_OPT')] + Helper::getMaritalStatus();
        $swimmingList = ['0' => __('label.SELECT_SWIMMER_OPT')] + Helper::getSwimming();
        $countriesVisitedList = Country::pluck('name', 'id')->toArray();
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::pluck('name', 'id')->toArray();

        //Division District Thana for cm permanent address
        $addressInfo = CmPermanentAddress::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : '0')
                        ->select('id', 'division_id', 'district_id', 'thana_id', 'address_details')->first();


        $divisionList = ['0' => __('label.SELECT_DIVISION_OPT')] + Division::pluck('name', 'id')->toArray();
        $districtList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', !empty($addressInfo->division_id) ? $addressInfo->division_id : 0)->pluck('name', 'id')->toArray();
        $thanaList = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', !empty($addressInfo->district_id) ? $addressInfo->district_id : 0)->pluck('name', 'id')->toArray();

        //Division District Thana for next kin
        $nextKinAddressInfo = CmNextKin::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : '0')
                        ->select('id', 'name', 'relation', 'division_id', 'district_id', 'thana_id', 'address_details')->first();
        $nextKinDistrictList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', !empty($nextKinAddressInfo->division_id) ? $nextKinAddressInfo->division_id : 0)->pluck('name', 'id')->toArray();
        $nextKinThanaList = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', !empty($nextKinAddressInfo->district_id) ? $nextKinAddressInfo->district_id : 0)->pluck('name', 'id')->toArray();

        $cmMedicalDetails = CmMedicalDetails::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : '0')
                        ->select('id', 'category', 'blood_group', 'date_of_birth', 'ht_ft', 'ht_inch', 'weight', 'over_under_weight', 'any_disease')->first();

        $cmWinterTrainingInfoData = CmWinterCollectiveTraining::where('user_id', !empty($cmInfoData->user_id) ? $cmInfoData->user_id : '0')
                        ->select('id', 'participated_no', 'training_info')->first();

        return view('cmProfile.index')->with(compact('cmInfoData', 'religionList', 'appointmentList', 'armsServiceList'
                                , 'unitList', 'formationList', 'maritalStatusList', 'brotherSisterInfoData', 'countriesVisitedList'
                                , 'swimmingList', 'othersInfoData', 'addressInfo', 'divisionList', 'districtList', 'thanaList'
                                , 'civilEducationInfoData', 'serviceRecordInfoData', 'awardRecordInfoData', 'punishmentRecordInfoData'
                                , 'defenceRelativeInfoData', 'courseList', 'nextKinAddressInfo', 'nextKinDistrictList', 'nextKinThanaList'
                                , 'cmMedicalDetails', 'cmWinterTrainingInfoData')
        );
    }

//
//    public function editProfile(Request $request) {
//        $cmInfo = User::leftJoin('cm_basic_profile', 'cm_basic_profile.user_id', '=', 'users.id')
//                ->leftjoin('rank', 'rank.id', '=', 'users.rank_id')
//                ->leftjoin('unit', 'unit.id', '=', 'cm_basic_profile.unit_id')
//                ->leftjoin('religion', 'religion.id', '=', 'cm_basic_profile.religion_id')
//                ->leftjoin('formation', 'formation.id', '=', 'cm_basic_profile.formation_id')
//                ->leftjoin('appointment', 'appointment.id', '=', 'users.appointment_id')
//                ->leftjoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
//                ->leftJoin('course', 'course.id', '=', 'cm_basic_profile.course_id')
//                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
//                ->select('users.id as user_id', 'users.email', 'users.photo', 'users.phone'
//                        , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (', users.official_name, ')') as cm_name")
//                        , 'appointment.name', 'course.name as course_name', 'arms_service.name as arms_service_name'
//                        , 'commissioning_course.name as commissioning_course_name', 'unit.name as unit_name', 'religion.name as religion_name'
//                        , 'formation.name as formation_name', 'cm_basic_profile.*'
//                )
//                ->where('cm_basic_profile.user_id', $request->user_id)
//                ->first();
//        return response()->json(['cmInfo' => $cmInfo]);
//    }

    public function updateProfile(Request $request) {

        $rules = [
            'full_name' => 'required',
            'official_name' => 'required',
            'username' => 'required',
            'appointment_id' => 'not_in:0',
            'email' => 'email',
        ];

        $messages = [
            'not_in' => __('label.APPOINMENT_ERROR'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        //Start:: User table update
        $userCm = User::find($request->user_id);
        $userCm->full_name = $request->full_name;
        $userCm->official_name = $request->official_name;
        $userCm->username = $request->username;
        $userCm->email = $request->email;
        $userCm->phone = $request->phone;
        $userCm->appointment_id = $request->appointment_id;

        $height = (($request->ht_ft * 12) + $request->ht_inch) * 0.0254;
        $bmi = ($request->weight / ($height * $height));
        if ($bmi > 18.5 && $bmi < 25) {
            $cmMedical = '2';
        } elseif ($bmi < 18.5) {
            $cmMedical = '1';
        } elseif ($bmi >= 25) {
            $cmMedical = '3';
        }
        //End:: User table update
        //Start:: Cm Basic Profile update
        DB::beginTransaction();
        try {
            if ($userCm->save()) {
                $cmArr = [
                    'commanding_officer_name' => $request->commanding_officer_name,
                    'commanding_officer_contact_no' => $request->commanding_officer_contact_no,
                    'commisioning_date' => !empty($request->commisioning_date) ? Helper::dateFormatConvert($request->commisioning_date) : null,
                    'anti_date_seniority' => $request->anti_date_seniority,
                    'course_position' => $request->course_position,
                    'position_out' => $request->position_out,
                    'nationality' => $request->nationality,
                    'birth_place' => $request->birth_place,
                    'religion_id' => $request->religion_id,
                    'arms_service_id' => $request->arms_service_id,
                    'unit_id' => $request->unit_id,
                    'formation_id' => $request->formation_id,
                    'ht_ft' => $request->ht_ft,
                    'ht_inch' => $request->ht_inch,
                    'weight' => $request->weight,
                    'medical_categorize' => $request->medical_categorize,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                CmBasicProfile::where('user_id', $request->user_id)->update($cmArr);
                CmMedicalDetails::where('user_id', $request->user_id)->update(['over_under_weight' => $cmMedical]);
            }
            DB::commit();
            //Session::flash('success', __('label.CM_PROFILE_UPDATED_SUCCESSFULLY'));
            return response()->json(['success' => __('label.CM_PROFILE_UPDATED_SUCCESSFULLY')]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['message' => __('label.CM_PROFILE_COULD_NOT_BE_UPDATED')]);
        }

        //End:: Cm Basic Profile update
        //End updateProfile function
    }

    public function updateProfilePhoto(Request $request) {
        $userCm = User::find($request->user_id);

        $rules = [
            'photo' => 'required|image|mimes:jpeg,png,jfif,jpg,gif,webp|max:1024',
        ];

        $messages = [
            'image.max' => 'The :attribute should not exeed from 1MB.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        //Update with Folder
        if (!empty($request->file('photo'))) {
            $filePath = public_path("uploads/user/" . $userCm->photo);
            if (File::exists($filePath))
                File::delete($filePath);
        }

        //Photo update and upload
        $file = $request->file('photo');
        if (!empty($file)) {
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/user', $fileName);
        }

        $userCm->photo = !empty($fileName) ? $fileName : '';
        if ($userCm->save()) {
            return response()->json(['success' => __('label.CM_PROFILE_PHOTO_UPDATED_SUCCESSFULLY')]);
        }

        //End:: updateProfilePhoto function
    }

    public function updatePassword(Request $request) {
        $userCm = User::find($request->user_id);
        $rules = [
            'password' => 'required|same:conf_password',
        ];

        $messages = [
            'password.complex_password' => __('label.WEAK_PASSWORD_FOLLOW_PASSWORD_INSTRUCTION'),
        ];
        if (!empty($request->password)) {
            $rules['password'] = 'complex_password:,' . $request->password;
            $rules['conf_password'] = 'same:password';
        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        if (!empty($request->password)) {
            $userCm->password = Hash::make($request->password);
        }
        if ($userCm->save()) {
            return response()->json(['success' => __('label.CM_PROFILE_PASSWORD_PHOTO_UPDATED_SUCCESSFULLY')]);
        }

        //End:: updatePassword function
    }

    public function updateFamilyInfo(Request $request) {
        $rules = [
            'father_name' => 'required',
            'father_occupation' => 'required',
            'father_address' => 'required',
            'mother_name' => 'required',
            'mother_occupation' => 'required',
            'mother_address' => 'required',
        ];

        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cmPrevBasicInfo = CmBasicProfile::select('id')->where('user_id', $request->user_id)->first();
        $cmBasicProfile = !empty($cmPrevBasicInfo->id) ? CmBasicProfile::find($cmPrevBasicInfo->id) : new CmBasicProfile;

        $cmBasicProfile->user_id = $request->user_id;
        $cmBasicProfile->father_name = $request->father_name;
        $cmBasicProfile->father_occupation = $request->father_occupation;
        $cmBasicProfile->father_address = $request->father_address;
        $cmBasicProfile->mother_name = $request->mother_name;
        $cmBasicProfile->mother_occupation = $request->mother_occupation;
        $cmBasicProfile->mother_address = $request->mother_address;

        if ($cmBasicProfile->save()) {
            return response()->json(['success' => __('label.CM_FAMILY_INFO_UPDATED_SUCCESSFULLY')]);
        }
        //END:: updateFamilyInfo function
    }

    public function updateMaritalStatus(Request $request) {
        $rules = [
            'marital_status' => 'not_in:0',
        ];

        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cmPrevBasicInfo = CmBasicProfile::select('id')->where('user_id', $request->user_id)->first();
        $cmBasicProfile = !empty($cmPrevBasicInfo->id) ? CmBasicProfile::find($cmPrevBasicInfo->id) : new CmBasicProfile;
//        echo '<pre>';        print_r($cmBasicProfile); exit;

        $cmBasicProfile->marital_status = $request->marital_status;
        if ($request->marital_status == '1') {
            $rules = [
                //'marital_status' => 'not_in:0',
                'date_of_marriage' => 'required',
                'spouse_name' => 'required',
            ];

            $messages = [
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
            }

            $cmBasicProfile->date_of_marriage = Helper::dateFormatConvert($request->date_of_marriage);
            $cmBasicProfile->spouse_name = $request->spouse_name;
            $cmBasicProfile->spouse_occupation = $request->spouse_occupation;
            $cmBasicProfile->spouse_work_address = $request->spouse_work_address;
        } else {
            $cmBasicProfile->date_of_marriage = null;
            $cmBasicProfile->spouse_name = null;
            $cmBasicProfile->spouse_occupation = null;
            $cmBasicProfile->spouse_work_address = null;
        }
        if ($cmBasicProfile->save()) {
            return response()->json(['success' => __('label.CM_MARITAL_STATUS_UPDATED_SUCCESSFULLY')]);
        }
        //End updateMaritialStatus function
    }

    public function rowAddForBrotherSister() {
        $html = view('cmProfile.brotherSisterRowAdd')
                ->render();
        return response()->json(['html' => $html]);

        ////End rowAdd function
    }

    public function updateBrotherSisterInfo(Request $request) {
        //echo '<pre>';print_r($request->all());exit;
        $cmBrotherSisterInfo = CmBrotherSister::select('id')->where('user_id', $request->user_id)->first();
        $cmBrotherSisterProfile = !empty($cmBrotherSisterInfo->id) ? CmBrotherSister::find($cmBrotherSisterInfo->id) : new CmBrotherSister;

        //Check Validation for Brother/Sister Information
        $rules = $messages = [];
        if (!empty($request->brother_sister)) {
            $row = 1;

            foreach ($request->brother_sister as $key => $brotherSister) {
                $rules['brother_sister.' . $key . '.name'] = 'required';
                $rules['brother_sister.' . $key . '.relation'] = 'required';

                $messages['brother_sister.' . $key . '.name.required'] = __('label.NAME_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['brother_sister.' . $key . '.relation.required'] = __('label.RELATION_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }


        $brotherSisterInfo = json_encode($request->brother_sister);
        $cmBrotherSisterProfile->user_id = $request->user_id;
        $cmBrotherSisterProfile->brother_sister_info = $brotherSisterInfo;
        $cmBrotherSisterProfile->updated_at = date('Y-m-d H:i:s');
        $cmBrotherSisterProfile->updated_by = Auth::user()->id;

        //Update Brother/Sister Info in cm_brother_sister_profile
        if ($cmBrotherSisterProfile->save()) {
            return response()->json(['success' => __('label.CM_BROTHER_SISTER_INFO_UPDATED')]);
        } else {
            return response()->json(['failed' => __('label.CM_BROTHER_SISTER_INFO_COULD_NOT_BE_UPDATED')]);
        }
    }

    public function updateCmOthersInfo(Request $request) {
        $rules = [
            'swimming' => 'not_in:0',
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $cmOthersInfo = CmOthers::select('id')->where('user_id', $request->user_id)->first();
        $cmOthersProfile = !empty($cmOthersInfo->id) ? CmOthers::find($cmOthersInfo->id) : new CmOthers;

        $othersInfo = json_encode($request->visited_countries_id);
        $cmOthersProfile->user_id = $request->user_id;
        $cmOthersProfile->visited_countries_id = $othersInfo;
        $cmOthersProfile->special_quality = $request->special_quality;
        $cmOthersProfile->swimming = $request->swimming;
        $cmOthersProfile->professional_computer = $request->professional_computer;
        $cmOthersProfile->updated_at = date('Y-m-d H:i:s');
        $cmOthersProfile->updated_by = Auth::user()->id;

        if ($cmOthersProfile->save()) {
            return response()->json(['success' => __('label.CM_BROTHER_SISTER_INFO_UPDATED')]);
        }
        //End updateCmOthersInfo function
    }

    //For Districts
    public function getDistrict(Request $request) {
        $districtList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', $request->division_id)
                        ->pluck('name', 'id')->toArray();
        $thanaList = ['0' => __('label.SELECT_THANA_OPT')];
        $htmldistrict = view('cmProfile.districts')->with(compact('districtList'))->render();
        $htmlThana = view('cmProfile.thana')->with(compact('thanaList'))->render();
        return response()->json(['html' => $htmldistrict, 'htmlThana' => $htmlThana]);
        //End getDistrict function
    }

    //For Thana
    public function getThana(Request $request) {
        $thanaList = ['0' => __('label.SELECT_THANA_OPT')] + THANA::where('district_id', $request->district_id)->pluck('name', 'id')->toArray();
        $htmlThana = view('cmProfile.thana')->with(compact('thanaList'))->render();
        return response()->json(['html' => $htmlThana]);
        //End getThana function
    }

    public function updatePermanentAddress(Request $request) {
        //echo '<pre>';        print_r($request->all()); exit;
        $rules = [
            'division_id' => 'required|not_in:0',
            'district_id' => 'required|not_in:0',
            'thana_id' => 'required|not_in:0',
        ];

        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cmPermanentAddress = CmPermanentAddress::select('id')->where('user_id', $request->user_id)->first();
        $cmPermanentAddressInfo = !empty($cmPermanentAddress->id) ? CmPermanentAddress::find($cmPermanentAddress->id) : new CmPermanentAddress;
        $cmPermanentAddressInfo->user_id = $request->user_id;
        $cmPermanentAddressInfo->division_id = $request->division_id;
        $cmPermanentAddressInfo->district_id = $request->district_id;
        $cmPermanentAddressInfo->thana_id = $request->thana_id;
        $cmPermanentAddressInfo->address_details = $request->address_details;
        $cmPermanentAddressInfo->updated_at = date('Y-m-d H:i:s');
        $cmPermanentAddressInfo->updated_by = Auth::user()->id;

        if ($cmPermanentAddressInfo->save()) {
            return response()->json(['success' => __('label.CM_PERMANENT_ADDRESS_UPDATED')]);
        }
        //End updatePermanentAddress function
    }


    public function rowAddForCivilEducation() {
        $html = view('cmProfile.civilEducationRowAdd')
                ->render();
        return response()->json(['html' => $html]);

        ////End rowAdd function
    }

    public function updateCivilEducationInfo(Request $request) {
        //Check Validation for Civil Education Information
        $rules = $messages = [];
        if (!empty($request->civil_education)) {
            $row = 1;

            foreach ($request->civil_education as $key => $civilEducation) {
                $rules['civil_education.' . $key . '.institute_name'] = 'required';
                $rules['civil_education.' . $key . '.examination'] = 'required';
                $rules['civil_education.' . $key . '.result'] = 'required';
                $rules['civil_education.' . $key . '.year'] = 'required';

                $messages['civil_education.' . $key . '.institute_name.required'] = __('label.INSTITUTE_NAME_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['civil_education.' . $key . '.examination.required'] = __('label.EXAMINATION_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['civil_education.' . $key . '.result.required'] = __('label.RESULT_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['civil_education.' . $key . '.year.required'] = __('label.RELATION_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $civilEducationInfo = CmCivilEducation::select('id')->where('user_id', $request->user_id)->first();
        $civilEducationProfile = !empty($civilEducationInfo->id) ? CmCivilEducation::find($civilEducationInfo->id) : new CmCivilEducation;



        $civilEducation = json_encode($request->civil_education);
        $civilEducationProfile->user_id = $request->user_id;
        $civilEducationProfile->civil_education_info = $civilEducation;
        $civilEducationProfile->updated_at = date('Y-m-d H:i:s');
        $civilEducationProfile->updated_by = Auth::user()->id;

        //Update cm civil education
        if ($civilEducationProfile->save()) {
            return response()->json(['success' => __('label.CIVIL_EDUCATION_INFO_UPDATED')]);
        }
        //End updateCivilEducationInfo function
    }

    public function rowAddForServiceRecord() {
        $appointmentList = ['0' => __('label.SELECT_APPT_OPT')] + CmAppointment::pluck('code', 'id')->toArray();
        $unitList = ['0' => __('label.SELECT_UNIT_OPT')] + Unit::pluck('code', 'id')->toArray();

        $html = view('cmProfile.serviceRecordRowAdd')->with(compact('appointmentList', 'unitList'))
                ->render();
        return response()->json(['html' => $html]);

        ////End rowAdd function
    }

    public function updateServiceRecordInfo(Request $request) {
        //Check Validation for Service Record Information
        $rules = $messages = [];
        if (!empty($request->service_record)) {
            $row = 1;

            foreach ($request->service_record as $srKey => $serviceRecord) {
                $rules['service_record.' . $srKey . '.unit'] = 'not_in:0';
                $rules['service_record.' . $srKey . '.appointment'] = 'not_in:0';
                $rules['service_record.' . $srKey . '.year'] = 'required';

                $messages['service_record.' . $srKey . '.unit.not_in'] = __('label.UNIT_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['service_record.' . $srKey . '.appointment.not_in'] = __('label.APPOINTMENT_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['service_record.' . $srKey . '.year.required'] = __('label.YEAR_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }

        $serviceEducationInfo = CmServiceRecord::select('id')->where('user_id', $request->user_id)->first();
        $serviceEducationProfile = !empty($serviceEducationInfo->id) ? CmServiceRecord::find($serviceEducationInfo->id) : new CmServiceRecord;

        $serviceRecord = json_encode($request->service_record);
        $serviceEducationProfile->user_id = $request->user_id;
        $serviceEducationProfile->service_record_info = $serviceRecord;
        $serviceEducationProfile->updated_at = date('Y-m-d H:i:s');
        $serviceEducationProfile->updated_by = Auth::user()->id;

        //Update cm service record
        if ($serviceEducationProfile->save()) {
            return response()->json(['success' => __('label.SERVICE_RECORD_INFO_UPDATED')]);
        }
        //End updateServiceRecordInfo function
    }

    public function rowAddForAwardRecord() {
        $html = view('cmProfile.awardRecordRowAdd')
                ->render();
        return response()->json(['html' => $html]);

        ////End rowAdd function
    }

    public function updateAwardRecordInfo(Request $request) {
        //Check Validation for Award Record Information
        $rules = $messages = [];
        if (!empty($request->award_record)) {
            $row = 1;

            foreach ($request->award_record as $key => $awardRecord) {
                $rules['award_record.' . $key . '.award'] = 'required';
                $rules['award_record.' . $key . '.reason'] = 'required';
                $rules['award_record.' . $key . '.year'] = 'required';

                $messages['award_record.' . $key . '.award.required'] = __('label.AWARD_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['award_record.' . $key . '.reason.required'] = __('label.REASON_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['award_record.' . $key . '.year.required'] = __('label.YEAR_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $awardRecordInfo = CmAwardRecord::select('id')->where('user_id', $request->user_id)->first();
        $awardRecordProfile = !empty($awardRecordInfo->id) ? CmAwardRecord::find($awardRecordInfo->id) : new CmAwardRecord;



        $awardRecord = json_encode($request->award_record);
        $awardRecordProfile->user_id = $request->user_id;
        $awardRecordProfile->award_record_info = $awardRecord;
        $awardRecordProfile->updated_at = date('Y-m-d H:i:s');
        $awardRecordProfile->updated_by = Auth::user()->id;

        //Update cm award record
        if ($awardRecordProfile->save()) {
            return response()->json(['success' => __('label.AWARD_RECORD_INFO_UPDATED')]);
        }
        //End updateAwardRecordInfo function
    }

    public function rowAddForPunishmentRecord() {
        $html = view('cmProfile.punishmentRecordRowAdd')
                ->render();
        return response()->json(['html' => $html]);

        ////End rowAdd function
    }

    public function updatePunishmentRecordInfo(Request $request) {
        //Check Validation for Punishment Record Information
        $rules = $messages = [];
        if (!empty($request->punishment_record)) {
            $row = 1;

            foreach ($request->punishment_record as $key => $punishmentRecord) {
                $rules['punishment_record.' . $key . '.punishment'] = 'required';
                $rules['punishment_record.' . $key . '.reason'] = 'required';
                $rules['punishment_record.' . $key . '.year'] = 'required';

                $messages['punishment_record.' . $key . '.punishment.required'] = __('label.PUNISHMENT_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['punishment_record.' . $key . '.reason.required'] = __('label.REASON_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['punishment_record.' . $key . '.year.required'] = __('label.YEAR_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $punishmentRecordInfo = CmPunishmentRecord::select('id')->where('user_id', $request->user_id)->first();
        $punishmentRecordProfile = !empty($punishmentRecordInfo->id) ? CmPunishmentRecord::find($punishmentRecordInfo->id) : new CmPunishmentRecord;



        $punishmentRecord = json_encode($request->punishment_record);
        $punishmentRecordProfile->user_id = $request->user_id;
        $punishmentRecordProfile->punishment_record_info = $punishmentRecord;
        $punishmentRecordProfile->updated_at = date('Y-m-d H:i:s');
        $punishmentRecordProfile->updated_by = Auth::user()->id;

        //Update cm punishment record
        if ($punishmentRecordProfile->save()) {
            return response()->json(['success' => __('label.PUNISHMENT_RECORD_INFO_UPDATED')]);
        }
        //End updatePunishmentRecordInfo function
    }

    public function rowAddForDefenceRelative() {
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::pluck('name', 'id')->toArray();
        $html = view('cmProfile.defenceRelativeRowAdd')->with(compact('courseList'))
                ->render();
        return response()->json(['html' => $html]);

        ////End rowAddForDefenceRelative function
    }

    public function updateDefenceRelativeInfo(Request $request) {
        //Check Validation for Punishment Record Information
        $rules = $messages = [];
        if (!empty($request->defence_relative)) {
            $row = 1;

            foreach ($request->defence_relative as $key => $defenceRelative) {
                $rules['defence_relative.' . $key . '.course'] = 'not_in:0';
                $rules['defence_relative.' . $key . '.institute'] = 'required';
                $rules['defence_relative.' . $key . '.grading'] = 'required';
                $rules['defence_relative.' . $key . '.year'] = 'required';

                $messages['defence_relative.' . $key . '.course.not_in'] = __('label.COURSE_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['defence_relative.' . $key . '.institute.required'] = __('label.INSTITUTE_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['defence_relative.' . $key . '.grading.required'] = __('label.GRADING_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['defence_relative.' . $key . '.year.required'] = __('label.YEAR_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $defenceRecordInfo = CmRelativeInDefence::select('id')->where('user_id', $request->user_id)->first();
        $defenceRecordProfile = !empty($defenceRecordInfo->id) ? CmRelativeInDefence::find($defenceRecordInfo->id) : new CmRelativeInDefence;



        $defenceRecord = json_encode($request->defence_relative);
        $defenceRecordProfile->user_id = $request->user_id;
        $defenceRecordProfile->cm_relative_info = $defenceRecord;
        $defenceRecordProfile->updated_at = date('Y-m-d H:i:s');
        $defenceRecordProfile->updated_by = Auth::user()->id;

        //Update cm punishment record
        if ($defenceRecordProfile->save()) {
            return response()->json(['success' => __('label.PUNISHMENT_RECORD_INFO_UPDATED')]);
        }
        //End updatePunishmentRecordInfo function
    }

    public function updateNextKin(Request $request) {
        //echo '<pre>';        print_r($request->all()); exit;
        $rules = [
            'kin_name' => 'required',
            'kin_relation' => 'required',
            'kin_division_id' => 'not_in:0',
            'kin_district_id' => 'not_in:0',
            'kin_thana_id' => 'not_in:0',
        ];

        $messages = [
            'kin_name.required' => 'The name field is required',
            'kin_relation.required' => 'The relation field is required',
            'kin_division_id.not_in' => 'The division field is required',
            'kin_district_id.not_in' => 'The district field is required',
            'kin_thana_id.not_in' => 'The thana field is required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cmNextKin = CmNextKin::select('id')->where('user_id', $request->user_id)->first();
        $cmNextKinInfo = !empty($cmNextKin->id) ? CmNextKin::find($cmNextKin->id) : new CmNextKin;
        $cmNextKinInfo->user_id = $request->user_id;
        $cmNextKinInfo->name = $request->kin_name;
        $cmNextKinInfo->relation = $request->kin_relation;
        $cmNextKinInfo->division_id = $request->kin_division_id;
        $cmNextKinInfo->district_id = $request->kin_district_id;
        $cmNextKinInfo->thana_id = $request->kin_thana_id;
        $cmNextKinInfo->address_details = $request->kin_address_details;
        $cmNextKinInfo->updated_at = date('Y-m-d H:i:s');
        $cmNextKinInfo->updated_by = Auth::user()->id;

        if ($cmNextKinInfo->save()) {
            return response()->json(['success' => __('label.CM_NEXT_KIN_INFO_UPDATED')]);
        }
        //End updatePermanentAddress function
    }

    public function updateMedicalDetails(Request $request) {
        //echo '<pre>';        print_r($request->all()); exit;
        $rules = [
            'category' => 'required',
            'blood_group' => 'required',
            'date_of_birth' => 'required',
        ];

        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cmMedicalDetails = CmMedicalDetails::select('id')->where('user_id', $request->user_id)->first();
        $cmBasicInfo = CmBasicProfile::select('id', 'ht_ft', 'ht_inch', 'weight')->where('user_id', $request->user_id)->first();
//        echo '<pre>';        print_r($cmBasicInfo); exit;
        $cmMedicalDetailsInfo = !empty($cmMedicalDetails->id) ? CmMedicalDetails::find($cmMedicalDetails->id) : new CmMedicalDetails;
        $cmMedicalDetailsInfo->user_id = $request->user_id;
        $cmMedicalDetailsInfo->category = $request->category;
        $cmMedicalDetailsInfo->blood_group = $request->blood_group;
        $cmMedicalDetailsInfo->date_of_birth = !empty($request->date_of_birth) ? Helper::dateFormatConvert($request->date_of_birth) : null;
        $cmBasicInfo->ht_ft = $request->ht_ft;
        $cmBasicInfo->ht_inch = $request->ht_inch;
        $cmBasicInfo->weight = $request->weight;

//        $height = (($request->ht_ft * 12) + $request->ht_inch)*0.0254;
//        $bmi =($request->weight/($height*$height));
//        if($bmi >18.5 && $bmi < 25 ){
//            $cmMedicalDetailsInfo->over_under_weight = 2;
//        }elseif ($bmi < 18.5) {
//            $cmMedicalDetailsInfo->over_under_weight = 1;
//        }elseif ($bmi >= 25) {
//            $cmMedicalDetailsInfo->over_under_weight = 3;
//        }
        $cmMedicalDetailsInfo->over_under_weight = $request->over_under_weight;
        $cmMedicalDetailsInfo->any_disease = $request->any_disease;
        $cmMedicalDetailsInfo->updated_at = date('Y-m-d H:i:s');
        $cmMedicalDetailsInfo->updated_by = Auth::user()->id;

        if ($cmMedicalDetailsInfo->save() && $cmBasicInfo->save()) {
            return response()->json(['success' => __('label.CM_MEDICAL_DETAILS_INFO_UPDATED')]);
        }
        //End updateMedicalDetails function
    }

    public function updateWinterTraining(Request $request) {
        //echo '<pre>';        print_r($request->all()); exit;
        //Check Validation for Punishment Record Information

        $rules = $messages = [];
        $rules['participated_no'] = 'required';
        if (!empty($request->winter_training)) {
            $row = 1;

            foreach ($request->winter_training as $key => $winterTraining) {
                $rules['winter_training.' . $key . '.exercise'] = 'required';
                $rules['winter_training.' . $key . '.year'] = 'required';

                $messages['winter_training.' . $key . '.exercise.required'] = __('label.EXERCISE_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);
                $messages['winter_training.' . $key . '.year.required'] = __('label.YEAR_INPUT_FIELD_EMPTY_MESSAGE', ["counter" => $row]);

                $row++;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        $cmWinterTraining = CmWinterCollectiveTraining::select('id')->where('user_id', $request->user_id)->first();
        $cmWinterTrainingInfo = !empty($cmWinterTraining->id) ? CmWinterCollectiveTraining::find($cmWinterTraining->id) : new CmWinterCollectiveTraining;

        $winterTraining = json_encode($request->winter_training);
        $cmWinterTrainingInfo->user_id = $request->user_id;
        $cmWinterTrainingInfo->participated_no = $request->participated_no;
        $cmWinterTrainingInfo->training_info = $winterTraining;
        $cmWinterTrainingInfo->updated_at = date('Y-m-d H:i:s');
        $cmWinterTrainingInfo->updated_by = Auth::user()->id;

        if ($cmWinterTrainingInfo->save()) {
            return response()->json(['success' => __('label.CM_WINTER_TRAINING_INFO_UPDATED')]);
        }
        //End updatePermanentAddress function
    }

//End class
}
