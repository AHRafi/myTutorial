<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Course;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\MarkingGroup;
use App\CmMarkingGroup;
use App\DsMarkingGroup;
use App\SynToSubSyn;
use App\CmToSyn;
use App\Term;
use App\MaMksExport;
use App\CmBasicProfile;
use App\User;
use App\SynToCourse;
use App\Rank;
use App\ServiceAppointment;
use App\CommissioningCourse;
use App\Religion;
use App\ArmsService;
use App\Appointment;
use App\CmOthers;
use App\CmCountryVisit;
use App\CmPermanentAddress;
use App\CmCivilEducation;
use App\CmServiceRecord;
use App\CmRelativeInDefence;
use App\CmChild;
use App\CmPresentAddress;
use App\CmPassport;
use App\MilCourse;
use App\CmMission;
use App\CmBank;
use App\Decoration;
use App\Hobby;
use App\Award;
use App\Wing;
use App\Unit;
use App\Country;
use App\Division;
use App\District;
use App\Thana;
use App\GradingSystem;
use App\EventAssessmentMarking;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use App\CriteriaWiseWt;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\DsObsnMarkingLimit;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\MutualAssessmentEvent;
use App\MutualAssessmentMarking;
use App\MutualAssessmentMarkingLock;
use App\MaProcess;
use App\CmGroupMemberTemplate;
use App\DsRemarks;
use Response;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use File;
use DB;
use Common;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class CmProfileReportCrntController extends Controller {

    public function index(Request $request) {
        $qpArr = $request->all();
        $spouseProfession = Common::getSpouseProfessionList();
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.INDIVIDUAL_PROFILE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.INDIVIDUAL_PROFILE');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $dsDeligationList = Common::getDsDeligationList();
        $deligatedDs = !empty($dsDeligationList[$courseList->id]) ? $dsDeligationList[$courseList->id] : 0;


        $dsCmArr = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('ds_marking_group', 'ds_marking_group.marking_group_id', 'marking_group.id')
                ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_marking_group.cm_id')
                ->where('marking_group.course_id', $courseList->id)
                ->where('ds_marking_group.ds_id', Auth::user()->id)
                ->where('ds_marking_group.ds_id', '<>', $deligatedDs)
                ->where('cm_basic_profile.status', '1')
                ->pluck('cm_basic_profile.id', 'cm_basic_profile.id')
                ->toArray();

        $cmList = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->where('cm_basic_profile.course_id', $courseList->id)
                ->where('cm_basic_profile.status', '1')
                ->pluck('cm_name', 'cm_basic_profile.id')
                ->toArray();

        $cmList = ['0' => __('label.ALL_CM_OPT')] + $cmList;

        $termList = Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                        ->where('term_to_course.course_id', $courseList->id)
                        ->where('term_to_course.status', '<>', '0')
                        ->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term_id')->toArray();
        $termIdList = [];
        if (!empty($termList)) {
            foreach ($termList as $termId => $termName) {
                $termIdList[$termId] = $termId;
            }
        }

        $factorList = MutualAssessmentEvent::where('status', '1')
                        ->orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        $closeTermList = Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                ->where('term_to_course.course_id', $courseList->id);
        if (!in_array(Auth::user()->group_id, [2, 3])) {
            $closeTermList = $closeTermList->where('term_to_course.status', '2');
        }
        $closeTermList = $closeTermList->orderBy('term.order', 'asc')
                        ->pluck('term.name', 'term_id')->toArray();
        $closeTermIdList = [];
        if (!empty($closeTermList)) {
            foreach ($closeTermList as $termId => $termName) {
                $closeTermIdList[$termId] = $termId;
            }
        }



        $eventList = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $courseList->id)
                ->whereIn('term_to_event.term_id', $termIdList)
                ->where('event.status', '1')
                ->orderBy('event.event_code', 'asc')
                ->pluck('event.event_code', 'event.id')
                ->toArray();


        // get grade system
        $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();
        // get assigned ci obsn wt
        $assignedObsnInfo = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')->where('course_id', $courseList->id)->first();
        $assignedDsObsnInfo = DsObsnMarkingLimit::select('term_id', 'mks_limit', 'obsn')
                        ->where('course_id', $request->course_id)->get();

        $assignedDsObsnArr = [];
        if (!$assignedDsObsnInfo->isEmpty()) {
            foreach ($assignedDsObsnInfo as $dsObsn) {
                $assignedDsObsnArr[$dsObsn->term_id]['mks_limit'] = $dsObsn->mks_limit;
                $assignedDsObsnArr[$dsObsn->term_id]['obsn'] = $dsObsn->obsn;
            }
        }

        $keyAppt = $milQualification = $cmInfoData = $civilEducationInfoData = $serviceRecordInfoData = $msnDataInfo = $passportInfoData = $countryVisitDataInfo = $bankInfoData = $childInfoData = $defenceRelativeInfoData = $othersInfoData = [];
        $milCourseList = $organizationList = $countriesVisitedList = $maritalStatusList = $unitList = $armsServiceList = $appointmentList = $religionList = $hobbyList = $awardList = $decorationList = $bloodGroupList = $commissionTypeList = [];
        $allAppointmentList = $thanaList = $districtList = $divisionList = $presentThanaList = $presentDistrictList = $presentAddressInfo = $addressInfo = [];
        $eventMksWtArr = $achievedMksWtArr = $SynArr = $eventWiseMksWtArr = $eventResultArr = $cmArr = [];
        if ($request->generate == 'true') {

            if (!empty($request->cm_id)) {
                $cmInfoData = CmBasicProfile::leftJoin('rank', 'rank.id', '=', 'cm_basic_profile.rank_id')
                        ->leftJoin('course', 'course.id', '=', 'cm_basic_profile.course_id')
                        ->leftJoin('wing', 'wing.id', '=', 'cm_basic_profile.wing_id')
                        ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                        ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                        ->leftJoin('religion', 'religion.id', '=', 'cm_basic_profile.religion_id')
                        ->select('cm_basic_profile.id as cm_basic_profile_id', 'cm_basic_profile.email'
                                , 'cm_basic_profile.photo', 'cm_basic_profile.number', 'cm_basic_profile.full_name'
                                , 'cm_basic_profile.official_name'
                                , DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.official_name, ')') as cm_name")
                                , 'course.name as course_name'
                                , 'arms_service.name as arms_service_name', 'commissioning_course.name as commissioning_course_name'
                                , 'religion.name as religion_name'
                                , 'cm_basic_profile.*', 'wing.code as wing_name')
                        ->where('cm_basic_profile.status', '1')
                        ->where('cm_basic_profile.id', $request->cm_id)
                        ->first();

                $civilEducationInfoData = CmCivilEducation::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'civil_education_info')
                        ->first();

                $serviceRecordInfoData = CmServiceRecord::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'service_record_info')
                        ->first();

                $msnDataInfo = CmMission::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'msn_info')
                        ->first();

                if (!empty($serviceRecordInfoData)) {
                    $serviceRecordInfo = json_decode($serviceRecordInfoData->service_record_info, TRUE);
                    if (!empty($serviceRecordInfo)) {
                        foreach ($serviceRecordInfo as $skey => $serviceRecord) {
                            $keyAppt[$serviceRecord['appointment']] = $serviceRecord['appointment'];
                        }
                    }
                }
                if (!empty($msnDataInfo)) {
                    $msnData = json_decode($msnDataInfo->msn_info, TRUE);
                    if (!empty($msnData)) {
                        foreach ($msnData as $mkey => $msn) {
                            $keyAppt[$msn['appointment']] = $msn['appointment'];
                        }
                    }
                }


                $countryVisitDataInfo = CmCountryVisit::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'visit_info')
                        ->first();

                $bankInfoData = CmBank::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'bank_info')
                        ->first();

                $childInfoData = CmChild::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'cm_child_info', 'no_of_child')
                        ->first();

                $defenceRelativeInfoData = CmRelativeInDefence::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'cm_relative_info')
                        ->first();

                if (!empty($defenceRelativeInfoData)) {
                    $milQualArr = !empty($defenceRelativeInfoData->cm_relative_info) ? json_decode($defenceRelativeInfoData->cm_relative_info, true) : [];
                    if (!empty($milQualArr)) {
                        foreach ($milQualArr as $mKey => $mInfo) {
                            $type = Common::getMilCourseType($mInfo['course']);
                            $milQualification[$mKey] = $mInfo;
                        }
                    }
                }

                $othersInfoData = CmOthers::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'decoration_id', 'hobby_id', 'extra_curriclar_expt', 'admin_resp_appt')
                        ->first();
                $passportInfoData = CmPassport::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                        ->select('id', 'cm_basic_profile_id', 'passport_no', 'place_of_issue', 'date_of_issue', 'date_of_expire')
                        ->first();


                $commissionTypeList = Common::getCommissionType();
                $bloodGroupList = Common::getBloodGroup();

                $decorationList = Decoration::where('status', '1')->orderBy('order', 'asc')
                                ->pluck('code', 'id')->toArray();
                $awardList = Award::where('status', '1')->orderBy('order', 'asc')
                                ->pluck('code', 'id')->toArray();
                $hobbyList = Hobby::where('status', '1')->orderBy('order', 'asc')
                                ->pluck('code', 'id')->toArray();

                $religionList = ['0' => __('label.SELECT_RELIGION_OPT')] + Religion::pluck('name', 'id')->toArray();
                $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                                ->where('status', '1')->pluck('code', 'id')->toArray();
                $allAppointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + ServiceAppointment::orderBy('order', 'asc')
                                ->where('status', '1')->pluck('code', 'id')->toArray();
                $armsServiceList = ['0' => __('label.SELECT_ARMS_SERVICE_OPT')] + ArmsService::pluck('code', 'id')->toArray();
                $unitList = ['0' => __('label.SELECT_UNIT_OPT')] + Unit::pluck('code', 'id')->toArray();
                $maritalStatusList = ['0' => __('label.SELECT_MARITAL_STATUS_OPT')] + Helper::getMaritalStatus();
                $countriesVisitedList = Country::pluck('name', 'id')->toArray();
                $organizationList = ['0' => __('label.SELECT_UNIT_FMN_INST_OPT')] + Unit::where('status', '1')->orderBy('order', 'asc')
                                ->pluck('code', 'id')->toArray();
                $milCourseList = ['0' => __('label.SELECT_COURSE_OPT')] + MilCourse::where('status', '1')
                                ->pluck('short_info', 'id')->toArray();


                //Division District Thana for cm permanent address
                $addressInfo = CmPermanentAddress::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : '0')
                        ->select('id', 'cm_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details', 'same_as_present')
                        ->first();
                $presentAddressInfo = CmPresentAddress::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : '0')
                        ->select('id', 'cm_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details')
                        ->first();

                $presentDistrictList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', !empty($presentAddressInfo->division_id) ? $presentAddressInfo->division_id : 0)
                                ->pluck('name', 'id')->toArray();
                $presentThanaList = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', !empty($presentAddressInfo->district_id) ? $presentAddressInfo->district_id : 0)
                                ->pluck('name', 'id')->toArray();

                $divisionList = ['0' => __('label.SELECT_DIVISION_OPT')] + Division::pluck('name', 'id')->toArray();
                $districtList = ['0' => __('label.SELECT_DISTRICT_OPT')] + District::where('division_id', !empty($addressInfo->division_id) ? $addressInfo->division_id : 0)
                                ->pluck('name', 'id')->toArray();
                $thanaList = ['0' => __('label.SELECT_THANA_OPT')] + Thana::where('district_id', !empty($addressInfo->district_id) ? $addressInfo->district_id : 0)
                                ->pluck('name', 'id')->toArray();


                // event wise mks & wt
                $eventWiseMksWtInfo = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                            $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                            $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                            $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                            $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                            $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                            $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
                            $join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                        })
                        ->where('event_assessment_marking.course_id', $request->course_id)
                        ->where('event_assessment_marking.cm_id', $request->cm_id)
                        ->whereIn('event_assessment_marking.term_id', $closeTermIdList)
                        ->whereNotNull('event_assessment_marking.mks')
                        ->select('event_assessment_marking.term_id', 'event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                                , 'event_assessment_marking.cm_id', DB::raw("AVG(event_assessment_marking.mks) as avg_mks"), DB::raw("AVG(event_assessment_marking.wt) as avg_wt")
                                , DB::raw("AVG(event_assessment_marking.percentage) as avg_percentage"))
                        ->groupBy('event_assessment_marking.term_id', 'event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id'
                                , 'event_assessment_marking.cm_id')
                        ->get();
                $cmEventCountArr = [];
                if (!$eventWiseMksWtInfo->isEmpty()) {
                    foreach ($eventWiseMksWtInfo as $eventMwInfo) {
                        if (!empty($eventMwInfo->avg_mks)) {
                            $cmEventCountArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] = !empty($cmEventCountArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id]) ? $cmEventCountArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] : 0;
                            $cmEventCountArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] += 1;
                        }
                        $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id]['avg_mks'] = $eventMwInfo->avg_mks;
                        $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id]['avg_wt'] = $eventMwInfo->avg_wt;
                        $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id]['avg_percentage'] = $eventMwInfo->avg_percentage;
                    }
                }

                //event info
                $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                        ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                        ->where('term_to_event.course_id', $request->course_id)
                        ->whereIn('term_to_event.term_id', $closeTermIdList)
                        ->where('event.status', '1')
                        ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit', 'event_mks_wt.highest_mks_limit'
                                , 'event_mks_wt.lowest_mks_limit', 'event_mks_wt.wt', 'event.has_sub_event'
                                , 'term_to_event.term_id')
                        ->get();

                if (!$eventInfo->isEmpty()) {
                    foreach ($eventInfo as $ev) {
                        if ($ev->has_sub_event == '0') {
                            $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['mks_limit'] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['highest_mks_limit'] = !empty($ev->highest_mks_limit) ? $ev->highest_mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['lowest_mks_limit'] = !empty($ev->lowest_mks_limit) ? $ev->lowest_mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;

                            if (!empty($eventWiseMksWtArr[$ev->term_id][$ev->event_id][0][0][0])) {
                                $eventMksWtArr['total_wt'][$ev->term_id] = !empty($eventMksWtArr['total_wt'][$ev->term_id]) ? $eventMksWtArr['total_wt'][$ev->term_id] : 0;
                                $eventMksWtArr['total_wt'][$ev->term_id] += !empty($ev->wt) ? $ev->wt : 0;
                                $eventMksWtArr['total_mks_limit'][$ev->term_id] = !empty($eventMksWtArr['total_mks_limit'][$ev->term_id]) ? $eventMksWtArr['total_mks_limit'][$ev->term_id] : 0;
                                $eventMksWtArr['total_mks_limit'][$ev->term_id] += !empty($ev->mks_limit) ? $ev->mks_limit : 0;

                                $eventMksWtArr['total_event_wt'][$ev->event_id] = !empty($ev->wt) ? $ev->wt : 0;

                                $eventMksWtArr['term_total_agg_mks'] = !empty($eventMksWtArr['term_total_agg_mks']) ? $eventMksWtArr['term_total_agg_mks'] : 0;
                                $eventMksWtArr['term_total_agg_mks'] += (!empty($ev->mks_limit) ? $ev->mks_limit : 0);
                                $eventMksWtArr['term_total_agg_wt'] = !empty($eventMksWtArr['term_total_agg_wt']) ? $eventMksWtArr['term_total_agg_wt'] : 0;
                                $eventMksWtArr['term_total_agg_wt'] += (!empty($ev->wt) ? $ev->wt : 0);
                            }
                        }
                    }
                }

//sub event info
                $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                        ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                            $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                        })
                        ->join('sub_event_mks_wt', function($join) {
                            $join->on('sub_event_mks_wt.event_id', '=', 'term_to_sub_event.event_id');
                            $join->on('sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                        })
                        ->where('term_to_sub_event.course_id', $request->course_id)
                        ->whereIn('term_to_sub_event.term_id', $closeTermIdList)
                        ->where('sub_event.status', '1')
                        ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit', 'sub_event_mks_wt.highest_mks_limit'
                                , 'sub_event_mks_wt.lowest_mks_limit', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                                , 'event_to_sub_event.event_id', 'event.event_code', 'term_to_sub_event.term_id'
                                , 'event_to_sub_event.avg_marking')
                        ->get();

                if (!$subEventInfo->isEmpty()) {
                    foreach ($subEventInfo as $subEv) {
                        $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                        if ($subEv->has_sub_sub_event == '0') {
                            $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['highest_mks_limit'] = !empty($subEv->highest_mks_limit) ? $subEv->highest_mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['lowest_mks_limit'] = !empty($subEv->lowest_mks_limit) ? $subEv->lowest_mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                            if (!empty($eventWiseMksWtArr[$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0])) {
                                $eventMksWtArr['total_event_wt'][$subEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subEv->event_id] : 0;
                                $eventMksWtArr['total_event_wt'][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                                $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] : 0;
                                $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;


                                $eventMksWtArr['total_wt'][$subEv->term_id] = !empty($eventMksWtArr['total_wt'][$subEv->term_id]) ? $eventMksWtArr['total_wt'][$subEv->term_id] : 0;
                                $eventMksWtArr['total_wt'][$subEv->term_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                                $eventMksWtArr['total_mks_limit'][$subEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subEv->term_id] : 0;
                                $eventMksWtArr['total_mks_limit'][$subEv->term_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                                $eventMksWtArr['term_total_agg_mks'] = !empty($eventMksWtArr['term_total_agg_mks']) ? $eventMksWtArr['term_total_agg_mks'] : 0;
                                $eventMksWtArr['term_total_agg_mks'] += (!empty($subEv->mks_limit) ? $subEv->mks_limit : 0);
                                $eventMksWtArr['term_total_agg_wt'] = !empty($eventMksWtArr['term_total_agg_wt']) ? $eventMksWtArr['term_total_agg_wt'] : 0;
                                $eventMksWtArr['term_total_agg_wt'] += (!empty($subEv->wt) ? $subEv->wt : 0);
                            }
                        } else {
                            if ($subEv->avg_marking == '1') {
                                $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                                $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                                if (!empty($eventWiseMksWtArr[$subEv->term_id][$subEv->event_id][$subEv->sub_event_id])) {

                                    $eventMksWtArr['total_event_wt'][$subEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subEv->event_id] : 0;
                                    $eventMksWtArr['total_event_wt'][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                                    $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] : 0;
                                    $eventMksWtArr['total_event_mks_limit'][$subEv->event_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                                    $eventMksWtArr['total_wt'][$subEv->term_id] = !empty($eventMksWtArr['total_wt'][$subEv->term_id]) ? $eventMksWtArr['total_wt'][$subEv->term_id] : 0;
                                    $eventMksWtArr['total_wt'][$subEv->term_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                                    $eventMksWtArr['total_mks_limit'][$subEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subEv->term_id] : 0;
                                    $eventMksWtArr['total_mks_limit'][$subEv->term_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                                    $eventMksWtArr['term_total_agg_mks'] = !empty($eventMksWtArr['term_total_agg_mks']) ? $eventMksWtArr['term_total_agg_mks'] : 0;
                                    $eventMksWtArr['term_total_agg_mks'] += (!empty($subEv->mks_limit) ? $subEv->mks_limit : 0);
                                    $eventMksWtArr['term_total_agg_wt'] = !empty($eventMksWtArr['term_total_agg_wt']) ? $eventMksWtArr['term_total_agg_wt'] : 0;
                                    $eventMksWtArr['term_total_agg_wt'] += (!empty($subEv->wt) ? $subEv->wt : 0);
                                }
                            }
                        }
                    }
                }

//sub sub event info
                $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                        ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                        ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                        ->join('event_to_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                        })
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                            $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                        })
                        ->leftJoin('sub_sub_event_mks_wt', function($join) {
                            $join->on('sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_event.event_id');
                            $join->on('sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                            $join->on('sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_event.course_id', $request->course_id)
                        ->whereIn('term_to_sub_sub_event.term_id', $closeTermIdList)
                        ->where('sub_sub_event.status', '1')
                        ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.mks_limit', 'sub_sub_event_mks_wt.highest_mks_limit'
                                , 'sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                                , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                                , 'sub_event.event_code as sub_event_code', 'event.event_code', 'term_to_sub_sub_event.term_id'
                                , 'event_to_sub_event.avg_marking')
                        ->get();


                if (!$subSubEventInfo->isEmpty()) {
                    foreach ($subSubEventInfo as $subSubEv) {
                        if ($subSubEv->has_sub_sub_sub_event == '0') {
                            $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['highest_mks_limit'] = !empty($subSubEv->highest_mks_limit) ? $subSubEv->highest_mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['lowest_mks_limit'] = !empty($subSubEv->lowest_mks_limit) ? $subSubEv->lowest_mks_limit : 0;
                            $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;

                            if ($subSubEv->avg_marking == '0') {
                                if (!empty($eventWiseMksWtArr[$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0])) {
                                    $eventMksWtArr['total_event_wt'][$subSubEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subSubEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subSubEv->event_id] : 0;
                                    $eventMksWtArr['total_event_wt'][$subSubEv->event_id] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                                    $eventMksWtArr['total_event_mks_limit'][$subSubEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subSubEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subSubEv->event_id] : 0;
                                    $eventMksWtArr['total_event_mks_limit'][$subSubEv->event_id] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;

                                    $eventMksWtArr['total_wt'][$subSubEv->term_id] = !empty($eventMksWtArr['total_wt'][$subSubEv->term_id]) ? $eventMksWtArr['total_wt'][$subSubEv->term_id] : 0;
                                    $eventMksWtArr['total_wt'][$subSubEv->term_id] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                                    $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subSubEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] : 0;
                                    $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;

                                    $eventMksWtArr['term_total_agg_mks'] = !empty($eventMksWtArr['term_total_agg_mks']) ? $eventMksWtArr['term_total_agg_mks'] : 0;
                                    $eventMksWtArr['term_total_agg_mks'] += (!empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0);
                                    $eventMksWtArr['term_total_agg_wt'] = !empty($eventMksWtArr['term_total_agg_wt']) ? $eventMksWtArr['term_total_agg_wt'] : 0;
                                    $eventMksWtArr['term_total_agg_wt'] += (!empty($subSubEv->wt) ? $subSubEv->wt : 0);
                                }
                            }
                        }

                        if ($subSubEv->avg_marking == '1') {
                            $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                            $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                        }
                    }
                }

//sub sub sub event info
                $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                        ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                        ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                        ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                        ->join('event_to_sub_sub_sub_event', function($join) {
                            $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                            $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                        })
                        ->join('event_to_sub_event', function($join) {
                            $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                            $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                        })
                        ->leftJoin('sub_sub_sub_event_mks_wt', function($join) {
                            $join->on('sub_sub_sub_event_mks_wt.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                            $join->on('sub_sub_sub_event_mks_wt.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                            $join->on('sub_sub_sub_event_mks_wt.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                            $join->on('sub_sub_sub_event_mks_wt.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                        })
                        ->where('term_to_sub_sub_sub_event.course_id', $request->course_id)
                        ->whereIn('term_to_sub_sub_sub_event.term_id', $closeTermIdList)
                        ->where('sub_sub_sub_event.status', '1')
                        ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit', 'sub_sub_sub_event_mks_wt.highest_mks_limit'
                                , 'sub_sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                                , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                                , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code', 'event.event_code'
                                , 'term_to_sub_sub_sub_event.term_id', 'event_to_sub_event.avg_marking')
                        ->get();


                if (!$subSubSubEventInfo->isEmpty()) {
                    foreach ($subSubSubEventInfo as $subSubSubEv) {
                        $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['mks_limit'] = !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['highest_mks_limit'] = !empty($subSubSubEv->highest_mks_limit) ? $subSubSubEv->highest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['lowest_mks_limit'] = !empty($subSubSubEv->lowest_mks_limit) ? $subSubSubEv->lowest_mks_limit : 0;
                        $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                        if ($subSubSubEv->avg_marking == '0') {
                            if (!empty($eventWiseMksWtArr[$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id])) {
                                $eventMksWtArr['total_event_wt'][$subSubSubEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subSubSubEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subSubSubEv->event_id] : 0;
                                $eventMksWtArr['total_event_wt'][$subSubSubEv->event_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                                $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subSubSubEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->event_id] : 0;
                                $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->event_id] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                                $eventMksWtArr['total_wt'][$subSubSubEv->term_id] = !empty($eventMksWtArr['total_wt'][$subSubSubEv->term_id]) ? $eventMksWtArr['total_wt'][$subSubSubEv->term_id] : 0;
                                $eventMksWtArr['total_wt'][$subSubSubEv->term_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                                $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] : 0;
                                $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                                $eventMksWtArr['term_total_agg_mks'] = !empty($eventMksWtArr['term_total_agg_mks']) ? $eventMksWtArr['term_total_agg_mks'] : 0;
                                $eventMksWtArr['term_total_agg_mks'] += (!empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0);
                                $eventMksWtArr['term_total_agg_wt'] = !empty($eventMksWtArr['term_total_agg_wt']) ? $eventMksWtArr['term_total_agg_wt'] : 0;
                                $eventMksWtArr['term_total_agg_wt'] += (!empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0);
                            }
                        }
                    }
                }






// ci moderation wise mks & wt 
                $ciModWiseMksWtInfo = CiModerationMarking::join('ci_moderation_marking_lock', function($join) {
                            $join->on('ci_moderation_marking_lock.course_id', 'ci_moderation_marking.course_id');
                            $join->on('ci_moderation_marking_lock.term_id', 'ci_moderation_marking.term_id');
                            $join->on('ci_moderation_marking_lock.event_id', 'ci_moderation_marking.event_id');
                            $join->on('ci_moderation_marking_lock.sub_event_id', 'ci_moderation_marking.sub_event_id');
                            $join->on('ci_moderation_marking_lock.sub_sub_event_id', 'ci_moderation_marking.sub_sub_event_id');
                            $join->on('ci_moderation_marking_lock.sub_sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id');
                            $join->on('ci_moderation_marking_lock.locked_by', 'ci_moderation_marking.updated_by');
                        })
                        ->where('ci_moderation_marking.course_id', $request->course_id)
                        ->where('ci_moderation_marking.cm_id', $request->cm_id)
                        ->whereIn('ci_moderation_marking.term_id', $closeTermIdList)
                        ->select('ci_moderation_marking.term_id', 'ci_moderation_marking.event_id', 'ci_moderation_marking.sub_event_id', 'ci_moderation_marking.sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id'
                                , 'ci_moderation_marking.cm_id', 'ci_moderation_marking.mks', 'ci_moderation_marking.wt', 'ci_moderation_marking.percentage', 'ci_moderation_marking.grade_id')
                        ->get();

                if (!$ciModWiseMksWtInfo->isEmpty()) {
                    foreach ($ciModWiseMksWtInfo as $ciMwInfo) {
                        if (!empty($ciMwInfo->mks) && empty($cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id])) {
							$cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] = !empty($cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id]) ? $cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] : 0;
							$cmEventCountArr[$ciMwInfo->cm_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id] += 1;
						}
						$eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id]['ci_mks'] = $ciMwInfo->mks;
                        $eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id]['ci_wt'] = $ciMwInfo->wt;
                        $eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id]['ci_percentage'] = $ciMwInfo->percentage;
                    }
                }
// comdt moderation wise mks & wt 
                $comdtModWiseMksWtInfo = ComdtModerationMarking::join('comdt_moderation_marking_lock', function($join) {
                            $join->on('comdt_moderation_marking_lock.course_id', 'comdt_moderation_marking.course_id');
                            $join->on('comdt_moderation_marking_lock.term_id', 'comdt_moderation_marking.term_id');
                            $join->on('comdt_moderation_marking_lock.event_id', 'comdt_moderation_marking.event_id');
                            $join->on('comdt_moderation_marking_lock.sub_event_id', 'comdt_moderation_marking.sub_event_id');
                            $join->on('comdt_moderation_marking_lock.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_event_id');
                            $join->on('comdt_moderation_marking_lock.sub_sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id');
                            $join->on('comdt_moderation_marking_lock.locked_by', 'comdt_moderation_marking.updated_by');
                        })
                        ->where('comdt_moderation_marking.course_id', $request->course_id)
                        ->where('comdt_moderation_marking.cm_id', $request->cm_id)
                        ->whereIn('comdt_moderation_marking.term_id', $closeTermIdList)
                        ->select('comdt_moderation_marking.term_id', 'comdt_moderation_marking.event_id', 'comdt_moderation_marking.sub_event_id', 'comdt_moderation_marking.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id'
                                , 'comdt_moderation_marking.cm_id', 'comdt_moderation_marking.mks', 'comdt_moderation_marking.wt', 'comdt_moderation_marking.percentage', 'comdt_moderation_marking.grade_id')
                        ->get();
                if (!$comdtModWiseMksWtInfo->isEmpty()) {
                    foreach ($comdtModWiseMksWtInfo as $comdtMwInfo) {
                        $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id]['comdt_mks'] = $comdtMwInfo->mks;
                        $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id]['comdt_wt'] = $comdtMwInfo->wt;
                        $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id]['comdt_percentage'] = $comdtMwInfo->percentage;
                    }
                }
                //ds obsn marking info
                $dsObsnMksWtInfo = DsObsnMarking::join('ds_obsn_marking_lock', function($join) {
                            $join->on('ds_obsn_marking_lock.course_id', 'ds_obsn_marking.course_id');
                            $join->on('ds_obsn_marking_lock.term_id', 'ds_obsn_marking.term_id');
                            $join->on('ds_obsn_marking_lock.locked_by', 'ds_obsn_marking.updated_by');
                        })
                        ->where('ds_obsn_marking.course_id', $request->course_id)
                        ->where('ds_obsn_marking.cm_id', $request->cm_id)
                        ->whereIn('ds_obsn_marking.term_id', $closeTermIdList)
                        ->select('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id', DB::raw('AVG(ds_obsn_marking.obsn_mks) as obsn_mks')
                                , DB::raw('AVG(ds_obsn_marking.obsn_wt) as obsn_wt'))
                        ->groupBy('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id')
                        ->get();
                $dsObsnMksWtArr = [];
                if (!$dsObsnMksWtInfo->isEmpty()) {
                    foreach ($dsObsnMksWtInfo as $dsObsnInfo) {
                        $dsObsnMksWtArr[$dsObsnInfo->term_id]['ds_obsn_mks'] = $dsObsnInfo->obsn_mks;
                        $dsObsnMksWtArr[$dsObsnInfo->term_id]['ds_obsn_wt'] = $dsObsnInfo->obsn_wt;
                    }
                }

                $gradeArr = [];
                if (!$gradeInfo->isEmpty()) {
                    foreach ($gradeInfo as $grade) {
                        $gradeArr[$grade->grade_name]['id'] = $grade->id;
                        $gradeArr[$grade->grade_name]['start'] = $grade->marks_from;
                        $gradeArr[$grade->grade_name]['end'] = $grade->marks_to;
                    }
                }

                if (!empty($eventMksWtArr['mks_wt'])) {
                    foreach ($eventMksWtArr['mks_wt'] as $termId => $evInfo) {
                        foreach ($evInfo as $eventId => $subEvInfo) {
                            foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                                foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                    foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                        $comdtMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['comdt_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['comdt_mks'] : 0;
                                        $comdtWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['comdt_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['comdt_wt'] : 0;
                                        $comdtPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['comdt_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['comdt_percentage'] : 0;

                                        $ciMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['ci_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['ci_mks'] : 0;
                                        $ciWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['ci_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['ci_wt'] : 0;
                                        $ciPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['ci_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['ci_percentage'] : 0;

                                        $eventAvgMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['avg_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['avg_mks'] : 0;
                                        $eventAvgWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['avg_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['avg_wt'] : 0;
                                        $eventAvgPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['avg_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['avg_percentage'] : 0;

                                        $TotalTermMks = !empty($comdtMks) ? $comdtMks : (!empty($ciMks) ? $ciMks : $eventAvgMks);
                                        $TotalTermWt = !empty($comdtWt) ? $comdtWt : (!empty($ciWt) ? $ciWt : $eventAvgWt);
                                        $TotalTermPercentage = !empty($comdtPercentage) ? $comdtPercentage : (!empty($ciPercentage) ? $ciPercentage : $eventAvgPercentage);

                                        $assignedWt = !empty($info['wt']) ? $info['wt'] : 0;
                                        $totalCount = 0;
                                        //count average where avg marking is enabled
                                        if (!empty($cmEventCountArr[$termId][$eventId][$subEventId])) {
                                            if (!empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking'] == '1') {
                                                if (array_key_exists($termId, $cmEventCountArr)) {
                                                    $totalCount = $cmEventCountArr[$termId][$eventId][$subEventId];
                                                }

                                                $subEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit'] : 0;
                                                $subEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt'] : 0;
                                                $subSubEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['mks_limit'] : 0;
                                                $subSubEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId][$subSubEventId]['wt'] : 0;

                                                $mksLimit = !empty($subSubSubEventId) ? $subSubEventMksLimit : $subEventMksLimit;
                                                $wtLimit = !empty($subSubSubEventId) ? $subSubEventWtLimit : $subEventWtLimit;

                                                $unitMksLimit = !empty($eventMksWtArr['mks_wt'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit']) ? $eventMksWtArr['mks_wt'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit'] : 0;
                                                $unitWtLimit = !empty($eventMksWtArr['mks_wt'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? $eventMksWtArr['mks_wt'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] : 0;

                                                if ($totalCount != 0 && $unitMksLimit != 0 && $unitWtLimit != 0) {
                                                    $assignedWt = $subEventWtLimit / $totalCount;
                                                    $TotalTermMks = ($TotalTermMks * $subEventMksLimit) / ($totalCount * $unitMksLimit);
                                                    $TotalTermWt = ($TotalTermWt * $subEventWtLimit) / ($totalCount * $unitWtLimit);
                                                }
                                            }
                                        }
                                        //term wise total
                                        $achievedMksWtArr['term_total'][$termId]['total_mks'] = !empty($achievedMksWtArr['term_total'][$termId]['total_mks']) ? $achievedMksWtArr['term_total'][$termId]['total_mks'] : 0;
                                        $achievedMksWtArr['term_total'][$termId]['total_mks'] += $TotalTermMks;
                                        $achievedMksWtArr['term_total'][$termId]['total_wt'] = !empty($achievedMksWtArr['term_total'][$termId]['total_wt']) ? $achievedMksWtArr['term_total'][$termId]['total_wt'] : 0;
                                        $achievedMksWtArr['term_total'][$termId]['total_wt'] += $TotalTermWt;

                                        $achievedMksWtArr['term_total'][$termId]['assigned'] = !empty($achievedMksWtArr['term_total'][$termId]['assigned']) ? $achievedMksWtArr['term_total'][$termId]['assigned'] : 0;
                                        if (!empty($TotalTermWt)) {
                                            $achievedMksWtArr['term_total'][$termId]['assigned'] += (!empty($assignedWt) ? $assignedWt : 0);
                                        }

                                        $eventMksWtArr['total_wt'][$termId] = $achievedMksWtArr['term_total'][$termId]['assigned'];

                                        $achievedMksWtArr['term_total'][$termId]['total_percentage'] = 0;
                                        if (!empty($achievedMksWtArr['term_total'][$termId]['assigned'])) {
                                            $achievedMksWtArr['term_total'][$termId]['total_percentage'] = ($achievedMksWtArr['term_total'][$termId]['total_wt'] * 100) / $achievedMksWtArr['term_total'][$termId]['assigned'];
                                        }

                                        $achievedMksWtArr['term_total'][$termId]['percentage'] = $achievedMksWtArr['term_total'][$termId]['total_percentage'];
                                        $termMarkingArr[$termId]['percentage'] = $achievedMksWtArr['term_total'][$termId]['percentage'];
                                        $totalPercentage = Helper::numberFormatDigit2($achievedMksWtArr['term_total'][$termId]['percentage']);
                                        // grade
                                        if (!empty($totalPercentage)) {
                                            foreach ($gradeArr as $letter => $gradeRange) {
                                                if ($totalPercentage == 100) {
                                                    $achievedMksWtArr['term_total'][$termId]['total_grade'] = "A+";
                                                    $achievedMksWtArr['term_total'][$termId]['total_grade_id'] = $gradeRange['id'];
                                                }
                                                if ($gradeRange['start'] <= $totalPercentage && $totalPercentage < $gradeRange['end']) {
                                                    $achievedMksWtArr['term_total'][$termId]['total_grade'] = !empty($achievedMksWtArr['term_total'][$termId]['percentage']) ? $letter : '';
                                                    $achievedMksWtArr['term_total'][$termId]['total_grade_id'] = $gradeRange['id'];
                                                }
                                            }
                                        }


                                        // aggregated term total
                                        $achievedMksWtArr['term_agg_total_mks'] = !empty($achievedMksWtArr['term_agg_total_mks']) ? $achievedMksWtArr['term_agg_total_mks'] : 0;
                                        $achievedMksWtArr['term_agg_total_mks'] += $TotalTermMks;
                                        $achievedMksWtArr['term_agg_total_wt'] = !empty($achievedMksWtArr['term_agg_total_wt']) ? $achievedMksWtArr['term_agg_total_wt'] : 0;
                                        $achievedMksWtArr['term_agg_total_wt'] += $TotalTermWt;
                                        $achievedMksWtArr['term_agg_total_assigned_wt'] = !empty($achievedMksWtArr['term_agg_total_assigned_wt']) ? $achievedMksWtArr['term_agg_total_assigned_wt'] : 0;
                                        if (!empty($TotalTermWt)) {
                                            $achievedMksWtArr['term_agg_total_assigned_wt'] += (!empty($assignedWt) ? $assignedWt : 0);
                                        }
                                        $eventMksWtArr['term_total_agg_wt'] = $achievedMksWtArr['term_agg_total_assigned_wt'];

                                        $achievedMksWtArr['term_agg_total_percentage'] = !empty($achievedMksWtArr['term_agg_total_percentage']) ? $achievedMksWtArr['term_agg_total_percentage'] : 0;
                                        $achievedMksWtArr['term_agg_total_percentage'] += $TotalTermPercentage;
                                        $achievedMksWtArr['term_agg_percentage'] = ($achievedMksWtArr['term_agg_total_wt'] * 100) / (!empty($achievedMksWtArr['term_agg_total_percentage']) ? $achievedMksWtArr['term_agg_total_percentage'] : 1);


                                        //total assigned wt event wise
                                        $eventResultArr['assigned'][$eventId] = !empty($eventResultArr['assigned'][$eventId]) ? $eventResultArr['assigned'][$eventId] : 0;
                                        if (!empty($TotalTermWt)) {
                                            $eventResultArr['assigned'][$eventId] += (!empty($assignedWt) ? $assignedWt : 0);
                                        }

                                        //total achieved mks cm wise
                                        $eventResultArr['achieved'][$eventId] = !empty($eventResultArr['achieved'][$eventId]) ? $eventResultArr['achieved'][$eventId] : 0;
                                        $eventResultArr['achieved'][$eventId] += $TotalTermWt;

                                        //Event Wise Individual Result
                                        $eventResultArr['event_percentage'][$eventId] = 0;
                                        if (!empty($eventResultArr['assigned'][$eventId])) {
                                            $eventResultArr['event_percentage'][$eventId] = ($eventResultArr['achieved'][$eventId] * 100) / $eventResultArr['assigned'][$eventId];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($dsObsnMksWtArr)) {
                    foreach ($dsObsnMksWtArr as $termId => $info) {
                        $eventMksWtArr['total_wt'][$termId] = !empty($eventMksWtArr['total_wt'][$termId]) ? $eventMksWtArr['total_wt'][$termId] : 0;
                        $eventMksWtArr['total_wt'][$termId] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);
                        $eventMksWtArr['total_mks_limit'][$termId] = !empty($eventMksWtArr['total_mks_limit'][$termId]) ? $eventMksWtArr['total_mks_limit'][$termId] : 0;
                        $eventMksWtArr['total_mks_limit'][$termId] += (!empty($assignedDsObsnArr[$termId]['mks_limit']) ? $assignedDsObsnArr[$termId]['mks_limit'] : 0);


                        $eventMksWtArr['term_total_agg_mks'] = !empty($eventMksWtArr['term_total_agg_mks']) ? $eventMksWtArr['term_total_agg_mks'] : 0;
                        $eventMksWtArr['term_total_agg_mks'] += (!empty($assignedDsObsnArr[$termId]['mks_limit']) ? $assignedDsObsnArr[$termId]['mks_limit'] : 0);
                        $eventMksWtArr['term_total_agg_wt'] = !empty($eventMksWtArr['term_total_agg_wt']) ? $eventMksWtArr['term_total_agg_wt'] : 0;
                        $eventMksWtArr['term_total_agg_wt'] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);

                        $dsObsnWt = 0;
                        if (!empty($assignedDsObsnArr[$termId]['mks_limit'])) {
                            $dsObsnWt = (($info['ds_obsn_mks'] * $assignedDsObsnArr[$termId]['obsn']) / $assignedDsObsnArr[$termId]['mks_limit']);
                        }
                        //term wise total
                        $achievedMksWtArr['term_total'][$termId]['total_mks'] = !empty($achievedMksWtArr['term_total'][$termId]['total_mks']) ? $achievedMksWtArr['term_total'][$termId]['total_mks'] : 0;
                        $achievedMksWtArr['term_total'][$termId]['total_mks'] += $info['ds_obsn_mks'] ?? 0;
                        $achievedMksWtArr['term_total'][$termId]['total_wt'] = !empty($achievedMksWtArr['term_total'][$termId]['total_wt']) ? $achievedMksWtArr['term_total'][$termId]['total_wt'] : 0;
                        $achievedMksWtArr['term_total'][$termId]['total_wt'] += $dsObsnWt ?? 0;

                        $achievedMksWtArr['term_total'][$termId]['assigned'] = !empty($achievedMksWtArr['term_total'][$termId]['assigned']) ? $achievedMksWtArr['term_total'][$termId]['assigned'] : 0;
                        if (!empty($dsObsnWt)) {
                            $achievedMksWtArr['term_total'][$termId]['assigned'] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);
                        }
                        $eventMksWtArr['total_wt'][$termId] = $achievedMksWtArr['term_total'][$termId]['assigned'];


                        $achievedMksWtArr['term_total'][$termId]['total_percentage'] = 0;
                        if (!empty($achievedMksWtArr['term_total'][$termId]['assigned'])) {
                            $achievedMksWtArr['term_total'][$termId]['total_percentage'] = ($achievedMksWtArr['term_total'][$termId]['total_wt'] * 100) / $achievedMksWtArr['term_total'][$termId]['assigned'];
                        }

                        $achievedMksWtArr['term_total'][$termId]['percentage'] = $achievedMksWtArr['term_total'][$termId]['total_percentage'];
                        $termMarkingArr[$termId]['percentage'] = $achievedMksWtArr['term_total'][$termId]['percentage'];
                        $totalPercentage = Helper::numberFormatDigit2($achievedMksWtArr['term_total'][$termId]['percentage']);
                        // grade
                        if (!empty($totalPercentage)) {
                            foreach ($gradeArr as $letter => $gradeRange) {
                                if ($totalPercentage == 100) {
                                    $achievedMksWtArr['term_total'][$termId]['total_grade'] = "A+";
                                    $achievedMksWtArr['term_total'][$termId]['total_grade_id'] = $gradeRange['id'];
                                }
                                if ($gradeRange['start'] <= $totalPercentage && $totalPercentage < $gradeRange['end']) {
                                    $achievedMksWtArr['term_total'][$termId]['total_grade'] = !empty($achievedMksWtArr['term_total'][$termId]['percentage']) ? $letter : '';
                                    $achievedMksWtArr['term_total'][$termId]['total_grade_id'] = $gradeRange['id'];
                                }
                            }
                        }
                        // aggregated term total
                        $achievedMksWtArr['term_agg_total_mks'] = !empty($achievedMksWtArr['term_agg_total_mks']) ? $achievedMksWtArr['term_agg_total_mks'] : 0;
                        $achievedMksWtArr['term_agg_total_mks'] += $info['ds_obsn_mks'] ?? 0;
                        $achievedMksWtArr['term_agg_total_wt'] = !empty($achievedMksWtArr['term_agg_total_wt']) ? $achievedMksWtArr['term_agg_total_wt'] : 0;
                        $achievedMksWtArr['term_agg_total_wt'] += $dsObsnWt ?? 0;
                        $achievedMksWtArr['term_agg_total_assigned_wt'] = !empty($achievedMksWtArr['term_agg_total_assigned_wt']) ? $achievedMksWtArr['term_agg_total_assigned_wt'] : 0;
                        if (!empty($dsObsnWt)) {
                            $achievedMksWtArr['term_agg_total_assigned_wt'] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);
                        }
                        $eventMksWtArr['term_total_agg_wt'] = $achievedMksWtArr['term_agg_total_assigned_wt'];

                        $achievedMksWtArr['term_agg_percentage'] = ($achievedMksWtArr['term_agg_total_wt'] * 100) / (!empty($achievedMksWtArr['term_agg_total_assigned_wt']) ? $achievedMksWtArr['term_agg_total_assigned_wt'] : 1);
                    }
                }

                //Start:: Get Position
                $cmId = $cmInfoData->cm_basic_profile_id;
                $courseId = $request->course_id;
                $cmArr = Common::getIndividualPosition($courseId, $cmId);
                //End:: Get Position
//                echo '<pre>';                print_r($cmArr);exit;

                if (!empty($assignedObsnInfo)) {
                    $eventMksWtArr['ci_obsn_wt'] = !empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0;
                    $eventMksWtArr['comdt_obsn_wt'] = !empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0;
                }

                $eventMksWtArr['term_total_agg_final_wt'] = (!empty($eventMksWtArr['comdt_obsn_wt']) ? $eventMksWtArr['comdt_obsn_wt'] : 0) + (!empty($eventMksWtArr['ci_obsn_wt']) ? $eventMksWtArr['ci_obsn_wt'] : 0) + (!empty($achievedMksWtArr['term_agg_total_assigned_wt']) ? $achievedMksWtArr['term_agg_total_assigned_wt'] : 0);


                //echo '<pre>';print_r($eventResultArr);exit;

                $totalWtPercent = !empty($achievedMksWtArr['term_agg_percentage']) ? Helper::numberFormatDigit2($achievedMksWtArr['term_agg_percentage']) : 0;
                if (!empty($gradeArr)) {
                    foreach ($gradeArr as $letterGrade => $gradeRange) {
                        if ($totalWtPercent == 100) {
                            $achievedMksWtArr['term_agg_grade'] = "A+";
                        }
                        if ($gradeRange['start'] <= $totalWtPercent && $totalWtPercent < $gradeRange['end']) {
                            $achievedMksWtArr['term_agg_grade'] = !empty($totalWtPercent) ? $letterGrade : '';
                        }
                    }
                }

                $synDataArr = CmGroupMemberTemplate::leftJoin('term_to_course', 'term_to_course.term_id', '=', 'cm_group_member_template.term_id')
                ->leftJoin('cm_group', 'cm_group.id', '=', 'cm_group_member_template.cm_group_id')
                ->select('cm_group.name as cm_group_name', 'cm_group_member_template.cm_basic_profile_id as cm_id')
                ->where('term_to_course.status', '<>', '0')
                        ->where('cm_group_member_template.course_id', $request->course_id)
                        ->where('cm_group_member_template.cm_basic_profile_id', $request->cm_id)
                ->get();

                if (!$synDataArr->isEmpty()) {
                    foreach ($synDataArr as $synInfo) {
                        $SynArr[$synInfo->term_id]['syn_name'] = $synInfo->cm_group_name;
                    }
                }

                $ciObsnInfo = CiObsnMarking::select('ci_obsn', 'percentage')->where('course_id', $request->course_id)
                        ->where('cm_id', $request->cm_id)
                        ->first();
                $comdtObsnInfo = ComdtObsnMarking::select('comdt_obsn', 'percentage')->where('course_id', $request->course_id)
                        ->where('cm_id', $request->cm_id)
                        ->first();
                if (!empty($ciObsnInfo)) {
                    $achievedMksWtArr['ci_obsn'] = !empty($ciObsnInfo->ci_obsn) ? $ciObsnInfo->ci_obsn : 0;
                }
                if (!empty($comdtObsnInfo)) {
                    $achievedMksWtArr['comdt_obsn'] = !empty($comdtObsnInfo->comdt_obsn) ? $comdtObsnInfo->comdt_obsn : 0;
                }
                $achievedMksWtArr['final_wt'] = (!empty($achievedMksWtArr['term_agg_total_wt']) ? $achievedMksWtArr['term_agg_total_wt'] : 0) + (!empty($achievedMksWtArr['ci_obsn']) ? $achievedMksWtArr['ci_obsn'] : 0) + (!empty($achievedMksWtArr['comdt_obsn']) ? $achievedMksWtArr['comdt_obsn'] : 0);
                $achievedMksWtArr['final_percent'] = ($achievedMksWtArr['final_wt'] * 100) / (!empty($eventMksWtArr['term_total_agg_final_wt']) ? $eventMksWtArr['term_total_agg_final_wt'] : 1);

                $finalWtPercent = !empty($achievedMksWtArr['final_percent']) ? Helper::numberFormatDigit2($achievedMksWtArr['final_percent']) : 0;
                if (!empty($gradeArr)) {
                    foreach ($gradeArr as $letterGrade => $gradeRange) {
                        if ($finalWtPercent == 100) {
                            $achievedMksWtArr['final_grade'] = "A+";
                        }
                        if ($gradeRange['start'] <= $finalWtPercent && $finalWtPercent < $gradeRange['end']) {
                            $achievedMksWtArr['final_grade'] = !empty($finalWtPercent) ? $letterGrade : '';
                        }
                    }
                }

                $muaPosnArr = $this->getMutualAssessmentPosition($request, $closeTermIdList);
                $dsRemarksInfo = $this->getDsRmksOnCm($request);


                $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
                $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
                $cmName = $request->cm_id != '0' && !empty($cmList[$request->cm_id]) ? '_' . $cmList[$request->cm_id] : '';
                $fileName = 'Individual_Profile' . $tyName . $courseName . $cmName;
                $fileName = Common::getFileFormatedName($fileName);
                if ($request->view == 'print') {
                    return view('reportCrnt.cmIndividualProfile.print.profile')->with(compact('cmInfoData', 'cmArr', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                            , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                            , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                            , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                            , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                            , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                            , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                            , 'request', 'activeTrainingYearList', 'courseList', 'qpArr', 'cmList', 'achievedMksWtArr'
                                            , 'eventMksWtArr', 'termList', 'SynArr', 'eventList', 'eventResultArr', 'milQualification'
                                            , 'spouseProfession', 'factorList', 'muaPosnArr', 'dsRemarksInfo')
                    );
                } elseif ($request->view == 'pdf') {
                    $pdf = PDF::loadView('reportCrnt.cmIndividualProfile.print.profile', compact('cmInfoData', 'cmArr', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                            , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                            , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                            , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                            , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                            , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                            , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                            , 'request', 'activeTrainingYearList', 'courseList', 'qpArr', 'cmList', 'achievedMksWtArr'
                                            , 'eventMksWtArr', 'termList', 'SynArr', 'eventList', 'eventResultArr', 'milQualification'
                                            , 'factorList', 'muaPosnArr', 'dsRemarksInfo'))
                            ->setPaper('a4', 'landscape')
                            ->setOptions(['defaultFont' => 'sans-serif']);

                    return $pdf->download($fileName . '.pdf');
                }

                return view('reportCrnt.cmIndividualProfile.profile')->with(compact('cmInfoData', 'cmArr', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                        , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                        , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                        , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                        , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                        , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                        , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                        , 'request', 'activeTrainingYearList', 'courseList', 'qpArr', 'cmList', 'achievedMksWtArr'
                                        , 'eventMksWtArr', 'termList', 'SynArr', 'eventList', 'eventResultArr', 'milQualification', 'spouseProfession'
                                        , 'factorList', 'muaPosnArr', 'dsRemarksInfo')
                );
            } else {

                $targetArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                        ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'cm_basic_profile.commissioning_course_id')
                        ->leftJoin('arms_service', 'arms_service.id', '=', 'cm_basic_profile.arms_service_id')
                        ->select('cm_basic_profile.personal_no', 'cm_basic_profile.full_name', 'cm_basic_profile.official_name'
                                , 'cm_basic_profile.photo', 'rank.code as rank', 'cm_basic_profile.email', 'cm_basic_profile.id'
                                , 'cm_basic_profile.number', 'commissioning_course.name as comm_course_name', 'arms_service.code as arms_service_name')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->where('cm_basic_profile.course_id', $request->course_id)
                        ->where('cm_basic_profile.status', '1')
                        ->get();

                $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
                $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
                $fileName = 'CM_List' . $tyName . $courseName;

                if ($request->view == 'print') {
                    return view('reportCrnt.cmIndividualProfile.print.index')->with(compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                            , 'targetArr', 'qpArr', 'cmList', 'spouseProfession'));
                } elseif ($request->view == 'pdf') {
                    $pdf = PDF::loadView('reportCrnt.cmIndividualProfile.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                            , 'targetArr', 'qpArr', 'cmList'))
                            ->setPaper('a4', 'landscape')
                            ->setOptions(['defaultFont' => 'sans-serif']);

                    return $pdf->download($fileName . '.pdf');
                } elseif ($request->view == 'excel') {
                    return Excel::download(new ExcelExport('reportCrnt.cmIndividualProfile.print.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                            , 'targetArr', 'qpArr', 'cmList')), $fileName . '.xlsx');
                }


                return view('reportCrnt.cmIndividualProfile.index', compact('request', 'activeTrainingYearList', 'courseList', 'termList'
                                , 'targetArr', 'qpArr', 'cmList', 'spouseProfession'));
            }
        }


        return view('reportCrnt.cmIndividualProfile.index')->with(compact('request', 'activeTrainingYearList', 'courseList', 'qpArr', 'cmList', 'spouseProfession')
        );
    }

    public function getCourse(Request $request) {
        $courseStatusArr = TermToCourse::join('course', 'course.id', 'term_to_course.course_id')
                        ->where('course.training_year_id', $request->training_year_id)
                        ->where('term_to_course.status', '2')
                        ->pluck('course.id', 'course.id')->toArray();

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->where('status', '<>', '0');
        if (in_array(Auth::user()->group_id, [2])) {
            $courseList = $courseList->whereIn('id', $courseStatusArr);
        }
        $courseList = $courseList->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;
        $html = view('reportCrnt.cmIndividualProfile.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getCm(Request $request) {

        $cmList = ['0' => __('label.ALL_CM_OPT')] + CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                        ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->where('cm_basic_profile.course_id', $request->course_id)
                        ->where('cm_basic_profile.status', '1')
                        ->pluck('cm_name', 'cm_basic_profile.id')
                        ->toArray();

        $html = view('reportCrnt.cmIndividualProfile.getCm', compact('cmList'))->render();
        return Response::json(['html' => $html]);
    }

    public function filter(Request $request) {
        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
        ];


        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&cm_id=' . $request->cm_id;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('cmProfileReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('cmProfileReportCrnt?generate=true&' . $url);
    }

    public function profile(Request $request, $id) {
        $url = 'generate=' . $request->generate . '&training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&cm_id=' . $id;
        return redirect('cmProfileReportCrnt?' . $url);
    }

    public function getMutualAssessmentPosition(Request $request, $closeTermIdList) {
        $maProcessList = MaProcess::where('course_id', $request->course_id)
                ->pluck('process', 'term_id')
                ->toArray();

        $mutualAssessmentInfo = MutualAssessmentMarking::where('mutual_assessment_marking.course_id', $request->course_id)
                ->whereIn('mutual_assessment_marking.term_id', $closeTermIdList)
                ->select('mutual_assessment_marking.cm_id', 'mutual_assessment_marking.term_id'
                        , 'mutual_assessment_marking.event_id', 'mutual_assessment_marking.sub_event_id'
                        , 'mutual_assessment_marking.sub_sub_event_id', 'mutual_assessment_marking.sub_sub_sub_event_id'
                        , 'mutual_assessment_marking.syndicate_id', 'mutual_assessment_marking.sub_syndicate_id'
                        , 'mutual_assessment_marking.event_group_id', 'mutual_assessment_marking.factor_id'
                        , DB::raw("AVG(mutual_assessment_marking.position) as avg_pos")
                        , DB::raw("COUNT(DISTINCT mutual_assessment_marking.marking_cm_id) as total_gp_cm"))
                ->groupBy('mutual_assessment_marking.cm_id', 'mutual_assessment_marking.term_id'
                        , 'mutual_assessment_marking.event_id', 'mutual_assessment_marking.sub_event_id'
                        , 'mutual_assessment_marking.sub_sub_event_id', 'mutual_assessment_marking.sub_sub_sub_event_id'
                        , 'mutual_assessment_marking.syndicate_id', 'mutual_assessment_marking.sub_syndicate_id'
                        , 'mutual_assessment_marking.event_group_id', 'mutual_assessment_marking.factor_id')
                ->get();
        $mutualAssessmentArr = $posnArr = $totalCmArr = [];
        if (!$mutualAssessmentInfo->isEmpty()) {
            foreach ($mutualAssessmentInfo as $mua) {
                $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['total_pos'] = !empty($mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['total_pos']) ? $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['total_pos'] : 0;
                $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['total_pos'] += $mua->avg_pos;
                $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['count'] = !empty($mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['count']) ? $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['count'] : 0;
                $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['count'] += 1;
                $totalCmArr[$mua->term_id][$mua->factor_id]['total_cm_count'] = !empty($totalCmArr[$mua->term_id][$mua->factor_id]['total_cm_count']) ? $totalCmArr[$mua->term_id][$mua->factor_id]['total_cm_count'] : 0;
                $totalCmArr[$mua->term_id][$mua->factor_id]['total_cm_count'] += 1;

                $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['pos'] = 0;
                $totalCmArr[$mua->term_id][$mua->factor_id]['total_cm'] = 0;

                if (!empty($mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['count'])) {
                    $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['pos'] = $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['total_pos'] / $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['count'];
                    $totalCmArr[$mua->term_id][$mua->factor_id]['total_cm'] = $totalCmArr[$mua->term_id][$mua->factor_id]['total_cm_count'] / $mutualAssessmentArr[$mua->term_id][$mua->factor_id][$mua->cm_id]['count'];
                }
            }
        }

        if (!empty($mutualAssessmentArr)) {
            foreach ($mutualAssessmentArr as $termId => $factor) {
                foreach ($factor as $factorId => $info) {
                    $mutualAssessmentArr[$termId][$factorId] = Common::getPosition($info, 'pos', 'final_pos', 1);
                }
            }
        }
        if (!empty($mutualAssessmentArr)) {
            foreach ($mutualAssessmentArr as $termId => $factor) {
                foreach ($factor as $factorId => $cm) {
                    foreach ($cm as $cmId => $info) {
                        if ($cmId == $request->cm_id) {
                            $posnArr[$termId][$factorId] = $info;
                            $posnArr[$termId][$factorId]['total_cm'] = (!empty($totalCmArr[$termId][$factorId]['total_cm']) ? $totalCmArr[$termId][$factorId]['total_cm'] : 0);
                        }
                    }
                }
            }
        }


        return $posnArr;
    }

    public function getDsRmksOnCm(Request $request) {
        $dsRemarksInfo = DsRemarks::join('users', 'users.id', 'ds_remarks.remarked_by')
                ->join('cm_basic_profile', 'cm_basic_profile.id', 'ds_remarks.cm_id')
                ->join('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('term', 'term.id', 'ds_remarks.term_id')
                ->leftJoin('event', 'event.id', 'ds_remarks.event_id')
                ->select('ds_remarks.date', 'ds_remarks.remarks', 'users.official_name'
                        , DB::raw('CONCAT(rank.code, " ", cm_basic_profile.full_name) as cm')
                        , 'event.event_code as event', 'term.name as term')
                ->where('ds_remarks.course_id', $request->course_id)
                ->where('ds_remarks.cm_id', $request->cm_id)
                ->orderBy('ds_remarks.date', 'desc')
                ->get();

        return $dsRemarksInfo;
    }

}
