<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Country;
use App\Division;
use App\District;
use App\Thana;
use App\GradingSystem;
use App\Course;
use App\EventAssessmentMarking;
use App\DsMarkingGroup;
use App\MarkingGroup;
use App\TrainingYear;
use App\Term;
use App\Event;
use App\SubEvent;
use App\SubSubEvent;
use App\SubSubSubEvent;
use App\EventAssessmentMarkingLock;
use App\EventToSubEvent;
use App\CmBasicProfile;
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
use App\CiModerationMarkingLock;
use App\ComdtModerationMarkingLock;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\CiObsnMarking;
use App\CiObsnMarkingLock;
use App\ComdtObsnMarking;
use App\ComdtObsnMarkingLock;
use App\DeligateCiAcctToDs;
use App\UserOthers;
use App\UserCountryVisit;
use App\UserPermanentAddress;
use App\UserPresentAddress;
use App\UserServiceRecord;
use App\UserPassport;
use App\UserBank;
use App\UserChild;
use App\UserMission;
use App\UserRelativeInDefence;
use App\UserCivilEducation;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\TermToCourse;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\DeligateReportsToDs;
use App\AssessmentActDeact;
use App\CmMarkingGroup;
use App\Occupation;
use App\MutualAssessmentMarkingLock;
use App\MutualAssessmentMarking;
use App\CmGroupMemberTemplate;
use App\Content;
use App\ContentDetails;
use Illuminate\Http\Request;

class Common {

    public static function sendHttpPost(Request $request, $url) {
        $clientHeader = Self::getApiHeader();
        $clientUrl = !empty($clientHeader['client_url']) ? $clientHeader['client_url'] : '';

        $response = Http::post($clientUrl . '/api/' . $url, [
                    'header' => $clientHeader,
                    'data' => $request->toArray(),
        ]);


//        return $response->body();
        return json_decode($response->body(), true);
    }

    public static function getHeaderAuth($clientHeader) {
        $ownHeader = Self::getApiOwnHeader();
        $status = 200;
        $message = '';

        if ($clientHeader['redirect_url'] != $ownHeader['redirect_url']) {
            $status = 419;
            $message = __('label.THIS_URL_IS_NOT_REGISTERED');
        } elseif ($clientHeader['client_secret'] != $ownHeader['client_secret']) {
            $status = 419;
            $message = __('label.THIS_URL_IS_NOT_AUTHORIZED');
        }
        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    public static function getApiHeader() {
        $header = [
            'type' => 'Application/Json',
            'client_id' => '2',
            'client_url' => __('api.API_CLIENT_URL'),
            'redirect_url' => __('api.API_SELF_URL'),
            'client_secret' => '354452317783-s7qg1bhhubpqjon8h6dhaloqbr5hga',
        ];
        return $header;
    }

    public static function getApiOwnHeader() {
        $header = [
            'type' => 'Application/Json',
            'client_id' => '1',
            'client_url' => __('api.API_SELF_URL'),
            'redirect_url' => __('api.API_CLIENT_URL'),
            'client_secret' => '453352416684-s7qg1bhhubpqjon8h6dhaloqbr5hga',
        ];
        return $header;
    }

    public static function getFullNameWithoutDecoration($nameWithDecoration) {
        $name = !empty($nameWithDecoration) ? explode(',', $nameWithDecoration) : [];

        return !empty($name[0]) ? $name[0] : '';
    }

    public static function getAvgMarkingList($courseId) {
        $avgMarkingInfo = EventToSubEvent::where('course_id', $courseId)
                ->where('avg_marking', '1')
                ->select('event_id', 'sub_event_id', 'avg_marking')
                ->get();
        $avgMarkingArr = [];
        if (!$avgMarkingInfo->isEmpty()) {
            foreach ($avgMarkingInfo as $info) {
                $avgMarkingArr[$info->event_id][$info->sub_event_id] = $info->avg_marking;
            }
        }
        return $avgMarkingArr;
    }

    public static function getCmGroupTypeList() {
        $groupTypeList = [
            '0' => __('label.SELECT_GROUP_TYPE_OPT'),
            '1' => __('label.SYNDICATE'),
            '2' => __('label.SUB_SYNDICATE'),
            '3' => __('label.SUB_SUB_SYNDICATE'),
        ];

        return $groupTypeList;
    }

    public static function getMaProcessList() {
        $processList = [
            '1' => __('label.SYNDICATE_WISE'),
            '2' => __('label.SUB_SYNDICATE_WISE'),
            '3' => __('label.EVENT_WISE'),
        ];

        return $processList;
    }

    public static function getMarkingSlabTypeList() {
        $slabTypeList = [
            '1' => __('label.WT_BAESD'),
            '2' => __('label.POSITION_BAESD'),
        ];

        return $slabTypeList;
    }

    public static function getPosition($cmArr, $totalWtKey, $positionKey, $mutualAssessment = 0) {
        $positionArr = [];
        if (!empty($cmArr)) {
            foreach ($cmArr as $cmId => $cm) {
                if (!isset($cm[$totalWtKey])) {
                    $cm[$totalWtKey] = 0;
                }
                if (!empty($positionArr)) {
                    if (!in_array($cm[$totalWtKey], $positionArr)) {
                        $positionArr[] = $cm[$totalWtKey];
                    }
                } else {
                    $positionArr[] = $cm[$totalWtKey];
                }
            }
        }
        if (!empty($mutualAssessment)) {
            sort($positionArr);
        } else {
            rsort($positionArr);
        }
        $ptn2 = 0;
        $positionArr2 = [];
        if (!empty($positionArr)) {
            foreach ($positionArr as $ptn => $value) {
                if (!empty($value)) {
                    if (!array_key_exists(strval($value), $positionArr2)) {
                        ++$ptn2;
                    }
                    $positionArr2[strval($value)] = $ptn2;
                }
            }
        }

        $positionArr = $positionArr2;
        if (!empty($cmArr)) {
            foreach ($cmArr as $cmId => $cm) {
                if (!isset($cm[$totalWtKey])) {
                    $cm[$totalWtKey] = 0;
                }

                if (!empty($positionArr)) {
                    $cmArr[$cmId][$positionKey] = isset($positionArr[strval($cm[$totalWtKey])]) ? $positionArr[strval($cm[$totalWtKey])] : null;
                } else {
                    $cmArr[$cmId][$positionKey] = 0;
                }
            }
        }
        return $cmArr;
    }

    public static function getIndividualPosition($courseId, $cmIndivId, $ontheFlyReport = 0) {



        $termDataArr = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                ->where('term_to_course.course_id', $courseId);
        $termIdList = $termDataArr->orderBy('term.order', 'asc')->pluck('term.id', 'term.id')
                ->toArray();
        $termDataArr = $termDataArr->orderBy('term.order', 'asc')->pluck('term.name', 'term.id')
                ->toArray();

        $closeTermList = Term::join('term_to_course', 'term_to_course.term_id', '=', 'term.id')
                ->where('term_to_course.course_id', $courseId);
        if (!in_array(Auth::user()->group_id, [2, 3]) && empty($ontheFlyReport)) {
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



//event info
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $courseId)
                ->whereIn('term_to_event.term_id', $closeTermIdList)
                ->where('event.status', '1')
                ->select('event.event_code', 'event.id as event_id', 'event_mks_wt.mks_limit', 'event_mks_wt.highest_mks_limit'
                        , 'event_mks_wt.lowest_mks_limit', 'event_mks_wt.wt', 'event.has_sub_event'
                        , 'term_to_event.term_id')
                ->get();

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                if (empty($ev->has_sub_event)) {
                    $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['mks_limit'] = !empty($ev->mks_limit) ? $ev->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['highest_mks_limit'] = !empty($ev->highest_mks_limit) ? $ev->highest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['lowest_mks_limit'] = !empty($ev->lowest_mks_limit) ? $ev->lowest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$ev->term_id][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;

                    $eventMksWtArr['total_wt'][$ev->term_id] = !empty($eventMksWtArr['total_wt'][$ev->term_id]) ? $eventMksWtArr['total_wt'][$ev->term_id] : 0;
                    $eventMksWtArr['total_wt'][$ev->term_id] += !empty($ev->wt) ? $ev->wt : 0;
                    $eventMksWtArr['total_mks_limit'][$ev->term_id] = !empty($eventMksWtArr['total_mks_limit'][$ev->term_id]) ? $eventMksWtArr['total_mks_limit'][$ev->term_id] : 0;
                    $eventMksWtArr['total_mks_limit'][$ev->term_id] += !empty($ev->mks_limit) ? $ev->mks_limit : 0;

                    $eventMksWtArr['total_wt_after_ci'][$ev->term_id] = $eventMksWtArr['total_wt'][$ev->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                    $eventMksWtArr['total_wt_after_comdt'][$ev->term_id] = $eventMksWtArr['total_wt_after_ci'][$ev->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
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
                ->where('term_to_sub_event.course_id', $courseId)
                ->whereIn('term_to_sub_event.term_id', $closeTermIdList)
                ->where('sub_event.status', '1')
                ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.mks_limit', 'sub_event_mks_wt.highest_mks_limit'
                        , 'sub_event_mks_wt.lowest_mks_limit', 'sub_event_mks_wt.wt', 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.event_id', 'event.event_code', 'term_to_sub_event.term_id', 'event_to_sub_event.avg_marking')
                ->get();

        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                $eventMksWtArr['event'][$subEv->term_id][$subEv->event_id]['name'] = $subEv->event_code ?? '';
                $eventMksWtArr['event'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['name'] = $subEv->sub_event_code ?? '';
                $eventMksWtArr['avg_marking'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                if ($subEv->has_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['highest_mks_limit'] = !empty($subEv->highest_mks_limit) ? $subEv->highest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['lowest_mks_limit'] = !empty($subEv->lowest_mks_limit) ? $subEv->lowest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                    $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] : 0;
                    $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                    $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] : 0;
                    $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;


                    $eventMksWtArr['total_wt'][$subEv->term_id] = !empty($eventMksWtArr['total_wt'][$subEv->term_id]) ? $eventMksWtArr['total_wt'][$subEv->term_id] : 0;
                    $eventMksWtArr['total_wt'][$subEv->term_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                    $eventMksWtArr['total_mks_limit'][$subEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subEv->term_id] : 0;
                    $eventMksWtArr['total_mks_limit'][$subEv->term_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                    $eventMksWtArr['total_wt_after_ci'][$subEv->term_id] = $eventMksWtArr['total_wt'][$subEv->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                    $eventMksWtArr['total_wt_after_comdt'][$subEv->term_id] = $eventMksWtArr['total_wt_after_ci'][$subEv->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                } else {
                    if ($subEv->avg_marking == '1') {
                        $eventMksWtArr['avg_marking'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                        $eventMksWtArr['avg_marking'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;

                        $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] : 0;
                        $eventMksWtArr['total_event_wt'][$subEv->term_id][$subEv->event_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subEv->term_id][$subEv->event_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;


                        $eventMksWtArr['total_wt'][$subEv->term_id] = !empty($eventMksWtArr['total_wt'][$subEv->term_id]) ? $eventMksWtArr['total_wt'][$subEv->term_id] : 0;
                        $eventMksWtArr['total_wt'][$subEv->term_id] += !empty($subEv->wt) ? $subEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'][$subEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subEv->term_id] : 0;
                        $eventMksWtArr['total_mks_limit'][$subEv->term_id] += !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;

                        $eventMksWtArr['total_wt_after_ci'][$subEv->term_id] = $eventMksWtArr['total_wt'][$subEv->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                        $eventMksWtArr['total_wt_after_comdt'][$subEv->term_id] = $eventMksWtArr['total_wt_after_ci'][$subEv->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
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
                ->where('term_to_sub_sub_event.course_id', $courseId)
                ->whereIn('term_to_sub_sub_event.term_id', $closeTermIdList)
                ->where('sub_sub_event.status', '1')
                ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.mks_limit', 'sub_sub_event_mks_wt.highest_mks_limit'
                        , 'sub_sub_event_mks_wt.lowest_mks_limit', 'sub_sub_event_mks_wt.wt', 'event_to_sub_sub_event.has_sub_sub_sub_event'
                        , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                        , 'sub_event.event_code as sub_event_code', 'event.event_code', 'term_to_sub_sub_event.term_id', 'event_to_sub_event.avg_marking')
                ->get();


        if (!$subSubEventInfo->isEmpty()) {
            foreach ($subSubEventInfo as $subSubEv) {
                $eventMksWtArr['event'][$subSubEv->term_id][$subSubEv->event_id]['name'] = $subSubEv->event_code ?? '';
                $eventMksWtArr['event'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id]['name'] = $subSubEv->sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['name'] = $subSubEv->sub_sub_event_code ?? '';
                if ($subSubEv->has_sub_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['mks_limit'] = !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['highest_mks_limit'] = !empty($subSubEv->highest_mks_limit) ? $subSubEv->highest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['lowest_mks_limit'] = !empty($subSubEv->lowest_mks_limit) ? $subSubEv->lowest_mks_limit : 0;
                    $eventMksWtArr['mks_wt'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;

                    if ($subSubEv->avg_marking == '0') {

                        $eventMksWtArr['total_event_wt'][$subSubEv->term_id][$subSubEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subSubEv->term_id][$subSubEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subSubEv->term_id][$subSubEv->event_id] : 0;
                        $eventMksWtArr['total_event_wt'][$subSubEv->term_id][$subSubEv->event_id] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subSubEv->term_id][$subSubEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subSubEv->term_id][$subSubEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subSubEv->term_id][$subSubEv->event_id] : 0;
                        $eventMksWtArr['total_event_mks_limit'][$subSubEv->term_id][$subSubEv->event_id] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;

                        $eventMksWtArr['total_wt'][$subSubEv->term_id] = !empty($eventMksWtArr['total_wt'][$subSubEv->term_id]) ? $eventMksWtArr['total_wt'][$subSubEv->term_id] : 0;
                        $eventMksWtArr['total_wt'][$subSubEv->term_id] += !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                        $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subSubEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] : 0;
                        $eventMksWtArr['total_mks_limit'][$subSubEv->term_id] += !empty($subSubEv->mks_limit) ? $subSubEv->mks_limit : 0;

                        $eventMksWtArr['total_wt_after_ci'][$subSubEv->term_id] = $eventMksWtArr['total_wt'][$subSubEv->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                        $eventMksWtArr['total_wt_after_comdt'][$subSubEv->term_id] = $eventMksWtArr['total_wt_after_ci'][$subSubEv->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                    }
                }

                if ($subSubEv->avg_marking == '1') {
                    $eventMksWtArr['avg_marking'][$subSubEv->term_id][$subEv->event_id][$subEv->sub_event_id][$subSubEv->sub_sub_event_id]['mks_limit'] = !empty($subEv->mks_limit) ? $subEv->mks_limit : 0;
                    $eventMksWtArr['avg_marking'][$subSubEv->term_id][$subEv->event_id][$subEv->sub_event_id][$subSubEv->sub_sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;
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
                ->where('term_to_sub_sub_sub_event.course_id', $courseId)
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
                $eventMksWtArr['event'][$subSubSubEv->term_id][$subSubSubEv->event_id]['name'] = $subSubSubEv->event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id]['name'] = $subSubSubEv->sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';

                $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['mks_limit'] = !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;
                $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['highest_mks_limit'] = !empty($subSubSubEv->highest_mks_limit) ? $subSubSubEv->highest_mks_limit : 0;
                $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['lowest_mks_limit'] = !empty($subSubSubEv->lowest_mks_limit) ? $subSubSubEv->lowest_mks_limit : 0;
                $eventMksWtArr['mks_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;

                if ($subSubSubEv->avg_marking == '0') {

                    $eventMksWtArr['total_event_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id] = !empty($eventMksWtArr['total_event_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id]) ? $eventMksWtArr['total_event_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id] : 0;
                    $eventMksWtArr['total_event_wt'][$subSubSubEv->term_id][$subSubSubEv->event_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                    $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->term_id][$subSubSubEv->event_id] = !empty($eventMksWtArr['total_event_mks_limit'][$subSubSubEv->term_id][$subSubSubEv->event_id]) ? $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->term_id][$subSubSubEv->event_id] : 0;
                    $eventMksWtArr['total_event_mks_limit'][$subSubSubEv->term_id][$subSubSubEv->event_id] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                    $eventMksWtArr['total_wt'][$subSubSubEv->term_id] = !empty($eventMksWtArr['total_wt'][$subSubSubEv->term_id]) ? $eventMksWtArr['total_wt'][$subSubSubEv->term_id] : 0;
                    $eventMksWtArr['total_wt'][$subSubSubEv->term_id] += !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
                    $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] = !empty($eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id]) ? $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] : 0;
                    $eventMksWtArr['total_mks_limit'][$subSubSubEv->term_id] += !empty($subSubSubEv->mks_limit) ? $subSubSubEv->mks_limit : 0;

                    $eventMksWtArr['total_wt_after_ci'][$subSubSubEv->term_id] = $eventMksWtArr['total_wt'][$subSubSubEv->term_id] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);

                    $eventMksWtArr['total_wt_after_comdt'][$subSubSubEv->term_id] = $eventMksWtArr['total_wt_after_ci'][$subSubSubEv->term_id] + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                }
            }
        }
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
                ->where('event_assessment_marking.course_id', $courseId)
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
                    $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] = !empty($cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id]) ? $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] : 0;
                    $cmEventCountArr[$eventMwInfo->cm_id][$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id] += 1;
                }
                $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_mks'] = $eventMwInfo->avg_mks;
                $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_wt'] = $eventMwInfo->avg_wt;
                $eventWiseMksWtArr[$eventMwInfo->term_id][$eventMwInfo->event_id][$eventMwInfo->sub_event_id][$eventMwInfo->sub_sub_event_id][$eventMwInfo->sub_sub_sub_event_id][$eventMwInfo->cm_id]['avg_percentage'] = $eventMwInfo->avg_percentage;
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
                ->where('ci_moderation_marking.course_id', $courseId)
                ->select('ci_moderation_marking.term_id', 'ci_moderation_marking.event_id', 'ci_moderation_marking.sub_event_id', 'ci_moderation_marking.sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id'
                        , 'ci_moderation_marking.cm_id', 'ci_moderation_marking.mks', 'ci_moderation_marking.wt', 'ci_moderation_marking.percentage', 'ci_moderation_marking.grade_id')
                ->get();

        if (!$ciModWiseMksWtInfo->isEmpty()) {
            foreach ($ciModWiseMksWtInfo as $ciMwInfo) {
                $eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_mks'] = $ciMwInfo->mks;
                $eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_wt'] = $ciMwInfo->wt;
                $eventWiseMksWtArr[$ciMwInfo->term_id][$ciMwInfo->event_id][$ciMwInfo->sub_event_id][$ciMwInfo->sub_sub_event_id][$ciMwInfo->sub_sub_sub_event_id][$ciMwInfo->cm_id]['ci_percentage'] = $ciMwInfo->percentage;
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
                ->where('comdt_moderation_marking.course_id', $courseId)
                ->select('comdt_moderation_marking.term_id', 'comdt_moderation_marking.event_id', 'comdt_moderation_marking.sub_event_id', 'comdt_moderation_marking.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id'
                        , 'comdt_moderation_marking.cm_id', 'comdt_moderation_marking.mks', 'comdt_moderation_marking.wt', 'comdt_moderation_marking.percentage', 'comdt_moderation_marking.grade_id')
                ->get();
        if (!$comdtModWiseMksWtInfo->isEmpty()) {
            foreach ($comdtModWiseMksWtInfo as $comdtMwInfo) {
                $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_mks'] = $comdtMwInfo->mks;
                $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_wt'] = $comdtMwInfo->wt;
                $eventWiseMksWtArr[$comdtMwInfo->term_id][$comdtMwInfo->event_id][$comdtMwInfo->sub_event_id][$comdtMwInfo->sub_sub_event_id][$comdtMwInfo->sub_sub_sub_event_id][$comdtMwInfo->cm_id]['comdt_percentage'] = $comdtMwInfo->percentage;
            }
        }

        $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->where('cm_basic_profile.course_id', $courseId)
                ->where('cm_basic_profile.status', '1')
                ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                        , 'cm_basic_profile.full_name', 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc');

        $cmDataArr = $cmDataArr->get();

        if (!$cmDataArr->isEmpty()) {
            foreach ($cmDataArr as $cmData) {
                $cmArr[$cmData->id] = $cmData->toArray();
            }
        }




        //ds obsn marking info
        $dsObsnMksWtInfo = DsObsnMarking::join('ds_obsn_marking_lock', function($join) {
                    $join->on('ds_obsn_marking_lock.course_id', 'ds_obsn_marking.course_id');
                    $join->on('ds_obsn_marking_lock.term_id', 'ds_obsn_marking.term_id');
                    $join->on('ds_obsn_marking_lock.locked_by', 'ds_obsn_marking.updated_by');
                })
                ->where('ds_obsn_marking.course_id', $courseId)
                ->whereIn('ds_obsn_marking.term_id', $closeTermIdList)
                ->select('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id', DB::raw('AVG(ds_obsn_marking.obsn_mks) as obsn_mks')
                        , DB::raw('AVG(ds_obsn_marking.obsn_wt) as obsn_wt'))
                ->groupBy('ds_obsn_marking.term_id', 'ds_obsn_marking.cm_id')
                ->get();
        $dsObsnMksWtArr = [];
        if (!$dsObsnMksWtInfo->isEmpty()) {
            foreach ($dsObsnMksWtInfo as $dsObsnInfo) {
                $dsObsnMksWtArr[$dsObsnInfo->term_id][$dsObsnInfo->cm_id]['ds_obsn_mks'] = $dsObsnInfo->obsn_mks;
                $dsObsnMksWtArr[$dsObsnInfo->term_id][$dsObsnInfo->cm_id]['ds_obsn_wt'] = $dsObsnInfo->obsn_wt;
            }
        }

        if (!empty($cmArr)) {
            foreach ($cmArr as $cmId => $cmInfo) {
                if (!empty($eventMksWtArr['mks_wt'])) {
                    foreach ($eventMksWtArr['mks_wt'] as $termId => $evInfo) {
                        foreach ($evInfo as $eventId => $subEvInfo) {
                            foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                                foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                                    foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                                        $comdtMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_mks'] : 0;
                                        $comdtWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_wt'] : 0;
                                        $comdtPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['comdt_percentage'] : 0;

                                        $ciMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_mks'] : 0;
                                        $ciWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_wt'] : 0;
                                        $ciPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['ci_percentage'] : 0;

                                        $eventAvgMks = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_mks']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_mks'] : 0;
                                        $eventAvgWt = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_wt'] : 0;
                                        $eventAvgPercentage = !empty($eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_percentage']) ? $eventWiseMksWtArr[$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId][$cmId]['avg_percentage'] : 0;

                                        $TotalTermMks = !empty($comdtMks) ? $comdtMks : (!empty($ciMks) ? $ciMks : $eventAvgMks);
                                        $TotalTermWt = !empty($comdtWt) ? $comdtWt : (!empty($ciWt) ? $ciWt : $eventAvgWt);
                                        $TotalTermPercentage = !empty($comdtPercentage) ? $comdtPercentage : (!empty($ciPercentage) ? $ciPercentage : $eventAvgPercentage);

                                        $assignedWt = !empty($info['wt']) ? $info['wt'] : 0;
                                        $totalCount = 0;
                                        //count average where avg marking is enabled
                                        if (!empty($cmEventCountArr[$cmId][$termId][$eventId][$subEventId])) {
                                            if (!empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['avg_marking'] == '1') {
                                                if (array_key_exists($cmId, $cmEventCountArr)) {
                                                    $totalCount = $cmEventCountArr[$cmId][$termId][$eventId][$subEventId];
                                                }

                                                $subEventMksLimit = !empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['mks_limit'] : 0;
                                                $subEventWtLimit = !empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['wt']) ? $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId]['wt'] : 0;
                                                $subSubEventMksLimit = !empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId][$subSubEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId][$subSubEventId]['mks_limit'] : 0;
                                                $subSubEventWtLimit = !empty($eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId][$subSubEventId]['wt']) ? $eventMksWtArr['avg_marking'][$termId][$eventId][$subEventId][$subSubEventId]['wt'] : 0;

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
                                        $cmArr[$cmId]['term_total'][$termId]['total_mks'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_mks']) ? $cmArr[$cmId]['term_total'][$termId]['total_mks'] : 0;
                                        $cmArr[$cmId]['term_total'][$termId]['total_mks'] += $TotalTermMks;
                                        $cmArr[$cmId]['term_total'][$termId]['total_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_wt'] : 0;
                                        $cmArr[$cmId]['term_total'][$termId]['total_wt'] += $TotalTermWt;
                                        $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 0;
                                        $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 0;
                                        if (!empty($TotalTermWt)) {
                                            $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] += (!empty($assignedWt) ? $assignedWt : 0);
                                        }
                                        $cmArr[$cmId]['term_total'][$termId]['percentage'] = 0;
                                        if (!empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'])) {
                                            $cmArr[$cmId]['term_total'][$termId]['percentage'] = ($cmArr[$cmId]['term_total'][$termId]['total_wt'] / $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) * 100;
                                        }

                                        $termMarkingArr[$termId][$cmId]['percentage'] = $cmArr[$cmId]['term_total'][$termId]['percentage'];


                                        // aggregated term total
//                                            $cmArr[$cmId]['term_agg_total_mks'] = !empty($cmArr[$cmId]['term_agg_total_mks']) ? $cmArr[$cmId]['term_agg_total_mks'] : 0;
//                                            $cmArr[$cmId]['term_agg_total_mks'] += $TotalTermMks;
//                                            $cmArr[$cmId]['term_agg_total_wt'] = !empty($cmArr[$cmId]['term_agg_total_wt']) ? $cmArr[$cmId]['term_agg_total_wt'] : 0;
//                                            $cmArr[$cmId]['term_agg_total_wt'] += $TotalTermWt;
//                                            $cmArr[$cmId]['term_agg_total_percentage'] = !empty($cmArr[$cmId]['term_agg_total_percentage']) ? $cmArr[$cmId]['term_agg_total_percentage'] : 0;
//                                            $cmArr[$cmId]['term_agg_total_percentage'] += $TotalTermPercentage;
//                                            $cmArr[$cmId]['term_agg_percentage'] = $cmArr[$cmId]['term_agg_total_percentage'] / sizeof($subSubSubEvInfo);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //ds obsn marking count
        if (!empty($dsObsnMksWtArr)) {
            foreach ($dsObsnMksWtArr as $termId => $termInfo) {
                $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 0;
                $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);

                foreach ($termInfo as $cmId => $info) {
                    $dsObsnWt = 0;
                    if (!empty($assignedDsObsnArr[$termId]['mks_limit'])) {
                        $dsObsnWt = (($info['ds_obsn_mks'] * $assignedDsObsnArr[$termId]['obsn']) / $assignedDsObsnArr[$termId]['mks_limit']);
                    }
//term wise total
                    $cmArr[$cmId]['term_total'][$termId]['total_mks'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_mks']) ? $cmArr[$cmId]['term_total'][$termId]['total_mks'] : 0;
                    $cmArr[$cmId]['term_total'][$termId]['total_mks'] += $info['ds_obsn_mks'] ?? 0;
                    $cmArr[$cmId]['term_total'][$termId]['total_wt'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_wt'] : 0;
                    $cmArr[$cmId]['term_total'][$termId]['total_wt'] += $dsObsnWt ?? 0;
//                            $cmArr[$cmId]['term_total'][$termId]['total_percentage'] = !empty($cmArr[$cmId]['term_total'][$termId]['total_percentage']) ? $cmArr[$cmId]['term_total'][$termId]['total_percentage'] : 0;
//                            $cmArr[$cmId]['term_total'][$termId]['total_percentage'] += $TotalTermPercentage;
                    $cmArr[$cmId]['term_total'][$termId]['percentage'] = 0;
                    if (!empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'])) {
                        $cmArr[$cmId]['term_total'][$termId]['percentage'] = ($cmArr[$cmId]['term_total'][$termId]['total_wt'] / $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) * 100;
                    }
                    $termMarkingArr[$termId][$cmId]['percentage'] = $cmArr[$cmId]['term_total'][$termId]['percentage'];
                }
            }
        }

        if (!empty($termMarkingArr)) {
            foreach ($termMarkingArr as $termId => $info) {
                $termMarkingArr[$termId] = self::getPosition($termMarkingArr[$termId], 'percentage', 'position');
                foreach ($info as $cmId => $cminf) {
                    $cmArr[$cmId]['term_total'][$termId]['position'] = $termMarkingArr[$termId][$cmId]['position'];
                }
            }
        }
//            echo '<pre>';
//            print_r($cmArr);exit;
//            Start:: Term Aggregate
        if (!empty($closeTermIdList)) {
            foreach ($closeTermIdList as $termId => $termName) {
                $eventMksWtArr['total_wt'][$termId] = !empty($eventMksWtArr['total_wt'][$termId]) ? $eventMksWtArr['total_wt'][$termId] : 0;
                $eventMksWtArr['total_wt'][$termId] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);
                $eventMksWtArr['total_mks_limit'][$termId] = !empty($eventMksWtArr['total_mks_limit'][$termId]) ? $eventMksWtArr['total_mks_limit'][$termId] : 0;
                $eventMksWtArr['total_mks_limit'][$termId] += (!empty($assignedDsObsnArr[$termId]['mks_limit']) ? $assignedDsObsnArr[$termId]['mks_limit'] : 0);

                $eventMksWtArr['agg_total_mks_limit'] = !empty($eventMksWtArr['agg_total_mks_limit']) ? $eventMksWtArr['agg_total_mks_limit'] : 0;
                $eventMksWtArr['agg_total_mks_limit'] += $eventMksWtArr['total_mks_limit'][$termId];
                $eventMksWtArr['agg_total_wt_limit'] = !empty($eventMksWtArr['agg_total_wt_limit']) ? $eventMksWtArr['agg_total_wt_limit'] : 0;
                $eventMksWtArr['agg_total_wt_limit'] += $eventMksWtArr['total_wt'][$termId];
            }
        }

        if (!empty($closeTermIdList)) {
            foreach ($cmArr as $cmId => $cmInfo) {
                if (!empty($closeTermIdList)) {
                    foreach ($closeTermIdList as $termId => $termName) {
                        $cmArr[$cmId]['agg_total_wt_limit'] = !empty($cmArr[$cmId]['agg_total_wt_limit']) ? $cmArr[$cmId]['agg_total_wt_limit'] : 0;
                        $cmArr[$cmId]['agg_total_wt_limit'] += !empty($cmArr[$cmId]['term_total'][$termId]['total_assigned_wt']) ? $cmArr[$cmId]['term_total'][$termId]['total_assigned_wt'] : 0;
                        $cmArr[$cmId]['term_agg_total_mks'] = !empty($cmArr[$cmId]['term_agg_total_mks']) ? $cmArr[$cmId]['term_agg_total_mks'] : 0;
                        $cmArr[$cmId]['term_agg_total_mks'] += $cmArr[$cmId]['term_total'][$termId]['total_mks'];
                        $cmArr[$cmId]['term_agg_total_wt'] = !empty($cmArr[$cmId]['term_agg_total_wt']) ? $cmArr[$cmId]['term_agg_total_wt'] : 0;
                        $cmArr[$cmId]['term_agg_total_wt'] += $cmArr[$cmId]['term_total'][$termId]['total_wt'];
                        $cmArr[$cmId]['term_agg_percentage'] = ($cmArr[$cmId]['term_agg_total_wt'] * 100) / (!empty($cmArr[$cmId]['agg_total_wt_limit']) ? $cmArr[$cmId]['agg_total_wt_limit'] : 1);
                    }
                }
            }
        }


        // get postion after term total
        $cmArr = self::getPosition($cmArr, 'term_agg_percentage', 'total_term_agg_position');


//            End:: Term Aggregate
//            get ci observation 
        $ciObsnDataArr = CiObsnMarking::join('ci_obsn_marking_lock', 'ci_obsn_marking_lock.course_id', 'ci_obsn_marking.course_id')
                ->leftJoin('grading_system', 'grading_system.id', 'ci_obsn_marking.grade_id')
                ->select('ci_obsn_marking.cm_id', 'ci_obsn_marking.ci_obsn', 'ci_obsn_marking.wt'
                        , 'ci_obsn_marking.percentage', 'grading_system.grade_name as after_ci_grade')
                ->where('ci_obsn_marking.course_id', $courseId)
                ->get();
        if (!$ciObsnDataArr->isEmpty()) {
            foreach ($ciObsnDataArr as $ciObsnData) {
                $cmArr[$ciObsnData->cm_id]['ci_obsn'] = $ciObsnData->ci_obsn ?? 0;
                $cmArr[$ciObsnData->cm_id]['total_wt_after_ci'] = $ciObsnData->wt ?? 0;
                $cmArr[$ciObsnData->cm_id]['percent_after_ci'] = $ciObsnData->percentage ?? 0;
                $cmArr[$ciObsnData->cm_id]['grade_after_ci'] = $ciObsnData->after_ci_grade ?? 0;
            }
        }

//            get comdt observation
        $comdtObsnDataArr = ComdtObsnMarking::join('comdt_obsn_marking_lock', 'comdt_obsn_marking_lock.course_id', 'comdt_obsn_marking.course_id')
                ->leftJoin('grading_system', 'grading_system.id', 'comdt_obsn_marking.grade_id')
                ->select('comdt_obsn_marking.cm_id', 'comdt_obsn_marking.comdt_obsn', 'comdt_obsn_marking.wt'
                        , 'comdt_obsn_marking.percentage', 'grading_system.grade_name as after_comdt_grade'
                        , 'grading_system.id as grade_id')
                ->where('comdt_obsn_marking.course_id', $courseId)
                ->get();

        if (!$comdtObsnDataArr->isEmpty()) {
            foreach ($comdtObsnDataArr as $comdtObsnData) {
                $cmArr[$comdtObsnData->cm_id]['comdt_obsn'] = $comdtObsnData->comdt_obsn ?? '';
                $cmArr[$comdtObsnData->cm_id]['total_wt_after_comdt'] = $comdtObsnData->wt ?? '';
                $cmArr[$comdtObsnData->cm_id]['percent_after_comdt'] = $comdtObsnData->percentage ?? '';
                $cmArr[$comdtObsnData->cm_id]['grade_after_comdt'] = $comdtObsnData->after_comdt_grade ?? '';
                $cmArr[$comdtObsnData->cm_id]['grade_id_after_comdt'] = $comdtObsnData->grade_id ?? 0;
            }
        }

        if (!empty($cmArr)) {
            foreach ($cmArr as $cmId => $cmInfo) {
                $comdtWt = !empty($cmInfo['comdt_obsn']) ? $cmInfo['comdt_obsn'] : 0;
                $ciWt = !empty($cmInfo['ci_obsn']) ? $cmInfo['ci_obsn'] : 0;
                $termTotalWt = !empty($cmInfo['term_agg_total_wt']) ? $cmInfo['term_agg_total_wt'] : 0;
                $cmArr[$cmId]['final_wt'] = $termTotalWt + $ciWt + $comdtWt;

                $assignedCiObsnWt = !empty($ciWt) && !empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0;
                $assignedComdtObsnWt = !empty($comdtWt) && !empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0;

                $cmArr[$cmId]['agg_total_wt_limit'] = !empty($cmArr[$cmId]['agg_total_wt_limit']) ? $cmArr[$cmId]['agg_total_wt_limit'] : 0;
                $cmArr[$cmId]['final_assigned_wt'] = $cmArr[$cmId]['agg_total_wt_limit'] + $assignedCiObsnWt + $assignedComdtObsnWt;

                $cmArr[$cmId]['final_percentage'] = ($cmArr[$cmId]['final_wt'] * 100) / (!empty($cmArr[$cmId]['final_assigned_wt']) ? $cmArr[$cmId]['final_assigned_wt'] : 1);
            }
        }

        //$eventMksWtArr['final_wt'] = $eventMksWtArr['agg_total_wt_limit'] + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0) + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);

        $cmArr = self::getPosition($cmArr, 'final_percentage', 'final_position');





//        echo '<pre>';
//        print_r($cmArr['19']);
//        exit;
        return $cmArr;
    }

    public static function getFileFormatedName($fileName) {
        $paterns = [
            '/', '|', '\\', '//', '?', ',', '-', ' '
            , '!', '@', '#', '$', '%', '^', '*', ';'
            , ':', '(', ')', '[', ']', '{', '}'
        ];
        return str_replace($paterns, '_', $fileName);
    }

    public static function getGradeName($cmArr, $gradeInfo, $wtPercent, $gradeKey) {
        $gradeArr = [];
        if (!$gradeInfo->isEmpty()) {
            foreach ($gradeInfo as $grade) {
                $gradeArr[$grade->grade_name]['start'] = $grade->marks_from;
                $gradeArr[$grade->grade_name]['end'] = $grade->marks_to;
            }
        }
        if (!empty($cmArr)) {
            foreach ($cmArr as $cmId => $cmInfo) {
                $totalWtPercent = !empty($cmInfo[$wtPercent]) ? Helper::numberFormatDigit2($cmInfo[$wtPercent]) : 0;
                if (!empty($gradeArr)) {
                    foreach ($gradeArr as $letterGrade => $gradeRange) {
                        if ($totalWtPercent == 100) {
                            $cmArr[$cmId][$gradeKey] = "A+";
                        }
                        if ($gradeRange['start'] <= $totalWtPercent && $totalWtPercent < $gradeRange['end']) {
                            if (is_int($cmId)) {
                                $cmArr[$cmId][$gradeKey] = !empty($totalWtPercent) ? $letterGrade : '';
                            }
                        }
                    }
                }
            }
        }

        return $cmArr;
    }
    
    public static function getEventList(Request $request, $courseId, $termId = 0, $maEvent = 0) {
        $eventMksWtArr = [];
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $courseId);
        if (!empty($termId)) {
            $eventInfo = $eventInfo->where('term_to_event.term_id', $termId);
        }
        if (!empty($maEvent)) {
            $eventInfo = $eventInfo->where('event.for_ma_grouping', '1');
        }
        $eventInfo = $eventInfo->where('event.status', '1')
                ->select('event.event_code', 'event.id as event_id', 'event.has_ds_assesment', 'term_to_event.term_id')
                ->orderBy('event.event_code', 'asc')
                ->get();

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                $eventMksWtArr['event'][$ev->event_id]['name'] = $ev->event_code ?? '';
                
                $eventMksWtArr['term_event'][$ev->term_id][$ev->event_id]['name'] = $ev->event_code ?? '';

                if ($ev->has_ds_assesment == '1') {
                    $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['name'] = $ev->event_code ?? '';
                }
            }
        }

        //sub event info
        $subEventInfo = TermToSubEvent::join('sub_event', 'sub_event.id', '=', 'term_to_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_event.course_id');
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_event.sub_event_id');
                })
                ->where('term_to_sub_event.course_id', $courseId);
        if (!empty($termId)) {
            $subEventInfo = $subEventInfo->where('term_to_sub_event.term_id', $termId);
        }
        if (!empty($maEvent)) {
            $subEventInfo = $subEventInfo->where('event.for_ma_grouping', '1');
        }
        $subEventInfo = $subEventInfo->where('sub_event.status', '1')
                ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'event_to_sub_event.has_ds_assesment'
                        , 'term_to_sub_event.term_id', 'event_to_sub_event.event_id', 'event.event_code', 'event.has_ds_assesment as event_has_ds_assessment')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->get();


        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                $eventMksWtArr['event'][$subEv->event_id]['name'] = $subEv->event_code ?? '';
                $eventMksWtArr['event'][$subEv->event_id][$subEv->sub_event_id]['name'] = $subEv->sub_event_code ?? '';
                
                $eventMksWtArr['term_event'][$subEv->term_id][$subEv->event_id]['name'] = $subEv->event_code ?? '';
                $eventMksWtArr['term_event'][$subEv->term_id][$subEv->event_id][$subEv->sub_event_id]['name'] = $subEv->sub_event_code ?? '';

                if ($subEv->has_ds_assesment == '1' && $subEv->event_has_ds_assessment == '0') {
                    $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['name'] = $subEv->sub_event_code ?? '';
                }
            }
        }

        //sub sub event info
        $subSubEventInfo = TermToSubSubEvent::join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                })
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.course_id', '=', 'term_to_sub_sub_event.course_id');
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_event.sub_sub_event_id');
                })
                ->where('term_to_sub_sub_event.course_id', $courseId);
        if (!empty($termId)) {
            $subSubEventInfo = $subSubEventInfo->where('term_to_sub_sub_event.term_id', $termId);
        }
        if (!empty($maEvent)) {
            $subSubEventInfo = $subSubEventInfo->where('event.for_ma_grouping', '1');
        }
        $subSubEventInfo = $subSubEventInfo->where('sub_sub_event.status', '1')
                ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'event_to_sub_sub_event.has_ds_assesment'
                        , 'term_to_sub_sub_event.term_id', 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                        , 'sub_event.event_code as sub_event_code', 'event.event_code', 'event.has_ds_assesment as event_has_ds_assessment'
                        , 'event_to_sub_event.has_ds_assesment as sub_event_has_ds_assessment')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubEventInfo->isEmpty()) {
            foreach ($subSubEventInfo as $subSubEv) {
                $eventMksWtArr['event'][$subSubEv->event_id]['name'] = $subSubEv->event_code ?? '';
                $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id]['name'] = $subSubEv->sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['name'] = $subSubEv->sub_sub_event_code ?? '';
                
                $eventMksWtArr['term_event'][$subSubEv->term_id][$subSubEv->event_id]['name'] = $subSubEv->event_code ?? '';
                $eventMksWtArr['term_event'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id]['name'] = $subSubEv->sub_event_code ?? '';
                $eventMksWtArr['term_event'][$subSubEv->term_id][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['name'] = $subSubEv->sub_sub_event_code ?? '';

                if ($subSubEv->has_ds_assesment == '1' && $subSubEv->event_has_ds_assessment == '0' && $subSubEv->sub_event_has_ds_assessment == '0') {
                    $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['name'] = $subSubEv->sub_sub_event_code ?? '';
                }
            }
        }

        //sub sub sub event info
        $subSubSubEventInfo = TermToSubSubSubEvent::join('sub_sub_sub_event', 'sub_sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id')
                ->join('sub_sub_event', 'sub_sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id')
                ->join('sub_event', 'sub_event.id', '=', 'term_to_sub_sub_sub_event.sub_event_id')
                ->join('event', 'event.id', '=', 'term_to_sub_sub_sub_event.event_id')
                ->join('event_to_sub_event', function($join) {
                    $join->on('event_to_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                    $join->on('event_to_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                })
                ->join('event_to_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                    $join->on('event_to_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                })
                ->join('event_to_sub_sub_sub_event', function($join) {
                    $join->on('event_to_sub_sub_sub_event.course_id', '=', 'term_to_sub_sub_sub_event.course_id');
                    $join->on('event_to_sub_sub_sub_event.event_id', '=', 'term_to_sub_sub_sub_event.event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_event_id');
                    $join->on('event_to_sub_sub_sub_event.sub_sub_sub_event_id', '=', 'term_to_sub_sub_sub_event.sub_sub_sub_event_id');
                })
                ->where('term_to_sub_sub_sub_event.course_id', $courseId);
        if (!empty($termId)) {
            $subSubSubEventInfo = $subSubSubEventInfo->where('term_to_sub_sub_sub_event.term_id', $termId);
        }
        if (!empty($maEvent)) {
            $subSubESubventInfo = $subSubSubEventInfo->where('event.for_ma_grouping', '1');
        }
        $subSubSubEventInfo = $subSubSubEventInfo->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'event_to_sub_sub_sub_event.event_id'
                        , 'term_to_sub_sub_sub_event.term_id', 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                        , 'event.event_code', 'event.has_ds_assesment as event_has_ds_assessment'
                        , 'event_to_sub_event.has_ds_assesment as sub_event_has_ds_assessment'
                        , 'event_to_sub_sub_event.has_ds_assesment as sub_sub_event_has_ds_assessment')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();


        if (!$subSubSubEventInfo->isEmpty()) {
            foreach ($subSubSubEventInfo as $subSubSubEv) {
                $eventMksWtArr['event'][$subSubSubEv->event_id]['name'] = $subSubSubEv->event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id]['name'] = $subSubSubEv->sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_event_code ?? '';
                $eventMksWtArr['event'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';
                
                $eventMksWtArr['term_event'][$subSubSubEv->term_id][$subSubSubEv->event_id]['name'] = $subSubSubEv->event_code ?? '';
                $eventMksWtArr['term_event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id]['name'] = $subSubSubEv->sub_event_code ?? '';
                $eventMksWtArr['term_event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_event_code ?? '';
                $eventMksWtArr['term_event'][$subSubSubEv->term_id][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';

                if ($subSubSubEv->event_has_ds_assessment == '0' && $subSubSubEv->sub_event_has_ds_assessment == '0' && $subSubSubEv->sub_sub_event_has_ds_assessment == '0') {
                    $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['name'] = $subSubSubEv->sub_sub_sub_event_code ?? '';
                }
            }
        }

        return $eventMksWtArr;
    }


    public static function requestCourseSatatusSummary($request, $loadView) {

        $courseName = Course::select('name')->where('id', $request->course_id)->first();

        $courseTermArr = TermToCourse::join('term', 'term.id', 'term_to_course.term_id')
                        ->where('term_to_course.course_id', $request->course_id)
                        ->where('term_to_course.status', '<>', '0')
                        ->pluck('term.name', 'term.id')->toArray();

        $termName = !empty($request->term_id) ? Term::select('name')->where('id', $request->term_id)->first() : [];
        $eventMksWtArr = [];
        //event info
        $eventInfo = MarkingGroup::join('event', 'event.id', '=', 'marking_group.event_id')
                ->join('term', 'term.id', 'marking_group.term_id')
                ->leftJoin('sub_event', 'sub_event.id', 'marking_group.sub_event_id')
                ->leftJoin('sub_sub_event', 'sub_sub_event.id', 'marking_group.sub_sub_event_id')
                ->leftJoin('sub_sub_sub_event', 'sub_sub_sub_event.id', 'marking_group.sub_sub_sub_event_id')
                ->where('marking_group.course_id', $request->course_id);

        if (!empty($request->term_id)) {
            $eventInfo = $eventInfo->where('marking_group.term_id', $request->term_id);
        }

        $eventInfo = $eventInfo->select('event.event_code as event_name', 'event.id as event_id', 'marking_group.term_id'
                        , 'sub_event.event_code as sub_event_name', 'marking_group.sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_name', 'marking_group.sub_sub_event_id'
                        , 'sub_sub_sub_event.event_code as sub_sub_sub_event_name', 'marking_group.sub_sub_sub_event_id'
                        , 'term.name as term_name')
                ->orderBy('term.order', 'asc')
                ->orderBy('event.event_code', 'asc')
                ->orderBy('sub_event.event_code', 'asc')
                ->orderBy('sub_sub_event.event_code', 'asc')
                ->orderBy('sub_sub_sub_event.event_code', 'asc')
                ->get();

        $termEventArr = Common::getEventList($request, $request->course_id, 0, 0);

        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                $eventMksWtArr['event'][$ev->term_id]['name'] = $ev->term_name ?? '';
//                $termEventArr['mks_wt'][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id][$ev->sub_sub_sub_event_id]
                if (!empty($termEventArr['term_event'][$ev->term_id][$ev->event_id])) {
                    $eventMksWtArr['event'][$ev->term_id][$ev->event_id]['name'] = $ev->event_name ?? '';
                }
                if (!empty($termEventArr['term_event'][$ev->term_id][$ev->event_id][$ev->sub_event_id])) {
                    $eventMksWtArr['event'][$ev->term_id][$ev->event_id][$ev->sub_event_id]['name'] = $ev->sub_event_name ?? '';
                }
                if (!empty($termEventArr['term_event'][$ev->term_id][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id])) {
                    $eventMksWtArr['event'][$ev->term_id][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id]['name'] = $ev->sub_sub_event_name ?? '';
                }
                if (!empty($termEventArr['term_event'][$ev->term_id][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id][$ev->sub_sub_sub_event_id])) {
                    $eventMksWtArr['event'][$ev->term_id][$ev->event_id][$ev->sub_event_id][$ev->sub_sub_event_id][$ev->sub_sub_sub_event_id]['name'] = $ev->sub_sub_sub_event_name ?? '';
                }
            }
        }

        $totalMarkingDsInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->select('marking_group.term_id', 'marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                        , 'marking_group.sub_sub_sub_event_id', DB::raw("COUNT(DISTINCT ds_marking_group.ds_id) as ds_id"))
                ->where('course_id', $request->course_id);

        if (!empty($request->term_id)) {
            $totalMarkingDsInfo = $totalMarkingDsInfo->where('marking_group.term_id', $request->term_id);
        }

        $totalMarkingDsInfo = $totalMarkingDsInfo->groupBy('marking_group.term_id', 'marking_group.event_id', 'marking_group.sub_event_id', 'marking_group.sub_sub_event_id'
                        , 'marking_group.sub_sub_sub_event_id')
                ->get();


        $totalDsArr = $totalLockedDsArr = $rowSpanArr = [];


        $totalLockedDsInfo = EventAssessmentMarkingLock::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id', DB::raw("COUNT(locked_by) as locked_ds"))
                ->where('course_id', $request->course_id);

        if (!empty($request->term_id)) {
            $totalLockedDsInfo = $totalLockedDsInfo->where('term_id', $request->term_id);
        }

        $totalLockedDsInfo = $totalLockedDsInfo->groupBy('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->get();

        if (!$totalLockedDsInfo->isEmpty()) {
            foreach ($totalLockedDsInfo as $lockInfo) {
                $eventMksWtArr['mks_wt'][$lockInfo->term_id][$lockInfo->event_id][$lockInfo->sub_event_id][$lockInfo->sub_sub_event_id][$lockInfo->sub_sub_sub_event_id]['forwarded'] = $lockInfo->locked_ds;
            }
        }
        if (!$totalMarkingDsInfo->isEmpty()) {
            foreach ($totalMarkingDsInfo as $dsInfo) {
                $forwarded = !empty($eventMksWtArr['mks_wt'][$dsInfo->term_id][$dsInfo->event_id][$dsInfo->sub_event_id][$dsInfo->sub_sub_event_id][$dsInfo->sub_sub_sub_event_id]['forwarded']) ? $eventMksWtArr['mks_wt'][$dsInfo->term_id][$dsInfo->event_id][$dsInfo->sub_event_id][$dsInfo->sub_sub_event_id][$dsInfo->sub_sub_sub_event_id]['forwarded'] : 0;

                $eventMksWtArr['mks_wt'][$dsInfo->term_id][$dsInfo->event_id][$dsInfo->sub_event_id][$dsInfo->sub_sub_event_id][$dsInfo->sub_sub_sub_event_id]['total'] = $dsInfo->ds_id;
                $eventMksWtArr['mks_wt'][$dsInfo->term_id][$dsInfo->event_id][$dsInfo->sub_event_id][$dsInfo->sub_sub_event_id][$dsInfo->sub_sub_sub_event_id]['not_forwarded'] = $dsInfo->ds_id - $forwarded;
            }
        }



        // ci mod check
        $ciModInfo = CiModerationMarking::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->where('course_id', $request->course_id);

        if (!empty($request->term_id)) {
            $ciModInfo = $ciModInfo->where('term_id', $request->term_id);
        }

        $ciModInfo = $ciModInfo->get();
        $ciModLockInfo = CiModerationMarkingLock::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->where('course_id', $request->course_id);

        if (!empty($request->term_id)) {
            $ciModLockInfo = $ciModLockInfo->where('term_id', $request->term_id);
        }

        $ciModLockInfo = $ciModLockInfo->get();

        if (!$ciModInfo->isEmpty()) {
            foreach ($ciModInfo as $ciInfo) {
                $eventMksWtArr['mks_wt'][$ciInfo->term_id][$ciInfo->event_id][$ciInfo->sub_event_id][$ciInfo->sub_sub_event_id][$ciInfo->sub_sub_sub_event_id]['ci_mod'] = 1;
            }
        }
        if (!$ciModLockInfo->isEmpty()) {
            foreach ($ciModLockInfo as $ciLockInfo) {
                $eventMksWtArr['mks_wt'][$ciLockInfo->term_id][$ciLockInfo->event_id][$ciLockInfo->sub_event_id][$ciLockInfo->sub_sub_event_id][$ciLockInfo->sub_sub_sub_event_id]['ci_mod_lock'] = 1;
            }
        }

        //comdt mod check
        $comdtModInfo = ComdtModerationMarking::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->where('course_id', $request->course_id);

        if (!empty($request->term_id)) {
            $comdtModInfo = $comdtModInfo->where('term_id', $request->term_id);
        }

        $comdtModInfo = $comdtModInfo->get();
        $comdtModLockInfo = ComdtModerationMarkingLock::select('term_id', 'event_id', 'sub_event_id', 'sub_sub_event_id'
                        , 'sub_sub_sub_event_id')
                ->where('course_id', $request->course_id);

        if (!empty($request->term_id)) {
            $comdtModLockInfo = $comdtModLockInfo->where('term_id', $request->term_id);
        }

        $comdtModLockInfo = $comdtModLockInfo->get();

        if (!$comdtModInfo->isEmpty()) {
            foreach ($comdtModInfo as $comdtInfo) {
                $eventMksWtArr['mks_wt'][$comdtInfo->term_id][$comdtInfo->event_id][$comdtInfo->sub_event_id][$comdtInfo->sub_sub_event_id][$comdtInfo->sub_sub_sub_event_id]['comdt_mod'] = 1;
            }
        }
        if (!$comdtModLockInfo->isEmpty()) {
            foreach ($comdtModLockInfo as $comdtLockInfo) {
                $eventMksWtArr['mks_wt'][$comdtLockInfo->term_id][$comdtLockInfo->event_id][$comdtLockInfo->sub_event_id][$comdtLockInfo->sub_sub_event_id][$comdtLockInfo->sub_sub_sub_event_id]['comdt_mod_lock'] = 1;
            }
        }

        $eventMksWtArr2 = [];
        if (!empty($eventMksWtArr['event'])) {
            foreach ($eventMksWtArr['event'] as $eventId => $evInfo) {
                if (sizeof($evInfo) == 1) {
                    $subEventId = $subSubEventId = $subSubSubEventId = 0;
                    $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                    $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                }

                foreach ($evInfo as $subEventId => $subEvInfo) {
                    if (is_int($subEventId)) {
                        if (sizeof($subEvInfo) == 1) {
                            $subSubEventId = $subSubSubEventId = 0;
                            $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                            $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                        }
                        foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                            if (is_int($subSubEventId)) {
                                if (sizeof($subSubEvInfo) == 1) {
                                    $subSubSubEventId = 0;
                                    $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                    $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                                }
                                foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {
                                    if (is_int($subSubSubEventId)) {
                                        $mksWtArr = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : [];
                                        $eventMksWtArr2[$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = $mksWtArr;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $eventMksWtArr['mks_wt'] = $eventMksWtArr2;

        if (!empty($eventMksWtArr['mks_wt'])) {
            foreach ($eventMksWtArr['mks_wt'] as $termId => $evMksWtInfo) {
                foreach ($evMksWtInfo as $eventId => $evInfo) {
                    foreach ($evInfo as $subEventId => $subEvInfo) {
                        foreach ($subEvInfo as $subSubEventId => $subSubEvInfo) {
                            foreach ($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo) {

                                $rowSpanArr['event'][$termId][$eventId] = !empty($rowSpanArr['event'][$termId][$eventId]) ? $rowSpanArr['event'][$termId][$eventId] : 0;
                                $rowSpanArr['event'][$termId][$eventId] += 1;

                                $rowSpanArr['sub_event'][$termId][$eventId][$subEventId] = !empty($rowSpanArr['sub_event'][$termId][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$termId][$eventId][$subEventId] : 0;
                                $rowSpanArr['sub_event'][$termId][$eventId][$subEventId] += 1;

                                $rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] = !empty($rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] : 0;
                                $rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] += 1;

                                $rowSpanArr['sub_sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] = !empty($rowSpanArr['sub_sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? $rowSpanArr['sub_sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] : 0;
                                $rowSpanArr['sub_sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId] += 1;
                            }
                        }
                    }
                }
            }
        }

        $ciObsnMarking = CiObsnMarking::select('id')->where('course_id', $request->course_id)->get();
        $ciObsnMarkingLock = CiObsnMarkingLock::select('id')->where('course_id', $request->course_id)->first();
        $comdtObsnMarking = ComdtObsnMarking::select('id')->where('course_id', $request->course_id)->get();
        $comdtObsnMarkingLock = ComdtObsnMarkingLock::select('id')->where('course_id', $request->course_id)->first();


        $dsDataInfo = User::join('user_group', 'user_group.id', 'users.group_id')
                ->join('appointment', 'appointment.id', 'users.appointment_id')
                ->join('rank', 'rank.id', 'users.rank_id')
                ->join('wing', 'wing.id', 'users.wing_id')
                ->where('users.group_id', 4)
                ->where('users.status', '1')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->select('users.official_name', 'users.id as ds_id', 'users.photo'
                        , DB::raw("CONCAT(rank.code, ' ', users.full_name) as ds_name")
                        , 'users.personal_no')
                ->get();
        $dsDataList = [];
        if (!$dsDataInfo->isEmpty()) {
            foreach ($dsDataInfo as $ds) {
                $dsDataList[$ds->ds_id] = $ds->toArray();
            }
        }

        $dsObservationMarkingInfo = DsObsnMarking::where('course_id', $request->course_id);
        if (!empty($request->termId)) {
            $dsObservationMarkingInfo = $dsObservationMarkingInfo->where('term_id', $request->term_id);
        }
        $dsObservationMarkingInfo = $dsObservationMarkingInfo->select('term_id', 'updated_by')->get();
        $dsObservationMarkingLockInfo = DsObsnMarkingLock::where('course_id', $request->course_id);
        if (!empty($request->termId)) {
            $dsObservationMarkingLockInfo = $dsObservationMarkingLockInfo->where('term_id', $request->term_id);
        }
        $dsObservationMarkingLockInfo = $dsObservationMarkingLockInfo->select('term_id', 'locked_by')->get();

        $dsObservationMarkingArr = $dsObservationMarkingLockArr = [];
        if (!$dsObservationMarkingInfo->isEmpty()) {
            foreach ($dsObservationMarkingInfo as $mInfo) {
                $dsObservationMarkingArr[$mInfo->term_id][$mInfo->updated_by] = $mInfo->updated_by;
            }
        }
        if (!$dsObservationMarkingLockInfo->isEmpty()) {
            foreach ($dsObservationMarkingLockInfo as $mLockInfo) {
                $dsObservationMarkingLockArr[$mLockInfo->term_id][$mLockInfo->locked_by] = $mLockInfo->locked_by;
            }
        }

        $assessmentActDeactInfo = AssessmentActDeact::where('course_id', $request->course_id);
        if (!empty($request->termId)) {
            $assessmentActDeactInfo = $assessmentActDeactInfo->where('term_id', $request->term_id);
        }
        $assessmentActDeactInfo = $assessmentActDeactInfo->where('status', '1')
                ->select('criteria', 'event_id', 'sub_event_id', 'sub_sub_event_id', 'sub_sub_sub_event_id', 'status')
                ->get();
        $assessmentActDeactArr = [];
        if (!$assessmentActDeactInfo->isEmpty()) {
            foreach ($assessmentActDeactInfo as $info) {
                $assessmentActDeactArr[$info->criteria][$info->event_id][$info->sub_event_id][$info->sub_sub_event_id][$info->sub_sub_sub_event_id] = $info->status;
            }
        }


        $html = view($loadView, compact('courseName', 'request', 'eventMksWtArr', 'rowSpanArr', 'termName', 'ciObsnMarking'
                        , 'ciObsnMarkingLock', 'comdtObsnMarking', 'comdtObsnMarkingLock', 'dsDataList'
                        , 'dsObservationMarkingArr', 'dsObservationMarkingLockArr', 'courseTermArr'
                        , 'assessmentActDeactArr'))->render();
        return response()->json(['html' => $html]);
    }

    public static function getDsMarkingSummary($request, $loadView) {

        //echo '<pre>';print_r($request->all()); exit;
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        $course = Course::where('id', $request->course_id)->first();
        $term = Term::where('id', $request->term_id)->first();
        $event = Event::where('id', $request->event_id)->first();
        $subEvent = SubEvent::where('id', $request->sub_event_id)->first();
        $subSubEvent = SubSubEvent::where('id', $request->sub_sub_event_id)->first();
        $subSubSubEvent = SubSubSubEvent::where('id', $request->sub_sub_sub_event_id)->first();

        //Lock Table
        $eventAssessmentMarkingLockInfo = EventAssessmentMarkingLock::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $eventAssessmentMarkingLockInfo = $eventAssessmentMarkingLockInfo->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $eventAssessmentMarkingLockInfo = $eventAssessmentMarkingLockInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $eventAssessmentMarkingLockInfo = $eventAssessmentMarkingLockInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $forceSubmitLockInfo = $eventAssessmentMarkingLockInfo->pluck('force_submitted', 'locked_by')->toArray();
        $eventAssessmentMarkingLockInfo = $eventAssessmentMarkingLockInfo->pluck('locked_by', 'locked_by')->toArray();


        $dsDataInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                ->join('users', 'users.id', 'ds_marking_group.ds_id')
                ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                ->join('rank', 'rank.id', 'users.rank_id')
                ->join('wing', 'wing.id', 'users.wing_id')
                ->leftJoin('appointment', 'appointment.id', 'ds_marking_group.ds_appt_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $dsDataInfo = $dsDataInfo->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $dsDataInfo = $dsDataInfo->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $dsDataInfo = $dsDataInfo->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }

        if (!empty($request->data_id)) {
            if ($request->data_id == '1') {
                $dsDataInfo = $dsDataInfo->whereIn('ds_marking_group.ds_id', $eventAssessmentMarkingLockInfo);
            } elseif ($request->data_id == '2') {
                $dsDataInfo = $dsDataInfo->whereNotIn('ds_marking_group.ds_id', $eventAssessmentMarkingLockInfo);
            }
        }

        $dsDataInfo = $dsDataInfo->select('users.official_name as appt', 'users.id as ds_id', 'users.photo'
                        , DB::raw("CONCAT(rank.code, ' ', users.full_name) as ds_name"), 'users.personal_no')
                ->orderBy('wing.order', 'asc')
                ->orderBy('appointment.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('users.personal_no', 'asc')
                ->get();

        $dsDataList = [];
        if (!$dsDataInfo->isEmpty()) {
            foreach ($dsDataInfo as $ds) {
                $dsDataList[$ds->ds_id] = $ds->toArray();
            }
        }

        // assessment marking data
        $eventAssessmentMarkingInfo = EventAssessmentMarking::join('grading_system', 'grading_system.id', 'event_assessment_marking.grade_id')
                ->where('event_assessment_marking.course_id', $request->course_id)
                ->where('event_assessment_marking.term_id', $request->term_id)
                ->where('event_assessment_marking.event_id', $request->event_id);

        if (!empty($request->sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->pluck('event_assessment_marking.updated_by', 'event_assessment_marking.updated_by')
                ->toArray();



//        echo '<pre>';
//        print_r($dsDataList);
//        exit;

        $view = view($loadView, compact('request', 'activeTrainingYearInfo', 'course', 'term', 'event', 'subEvent', 'subSubEvent'
                        , 'subSubSubEvent', 'dsDataList', 'eventAssessmentMarkingLockInfo', 'eventAssessmentMarkingInfo'
                        , 'forceSubmitLockInfo'))->render();
        return response()->json(['html' => $view]);
    }

    public static function getOrganizationType($flagType = 0) {

        if ($flagType == 1) {
            $organizationList = [
                '1' => 'Unit',
                '2' => 'Formation',
                '3' => 'Institute',
            ];
        } else {
            $organizationList = [
                '1' => 'Corps',
                '2' => 'Regt',
                '3' => 'Br',
            ];
        }

        return $organizationList;
    }

    public static function getCiDsProfile(Request $request, $id, $loadView, $prinLloadView) {
        $keyAppt = [];
        $qpArr = $request->all();
        $userInfoData = User::leftJoin('user_basic_profile', 'user_basic_profile.user_id', '=', 'users.id')
                ->leftJoin('rank', 'rank.id', '=', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', '=', 'users.wing_id')
                ->leftJoin('arms_service', 'arms_service.id', '=', 'user_basic_profile.arms_service_id')
                ->leftJoin('commissioning_course', 'commissioning_course.id', '=', 'user_basic_profile.commissioning_course_id')
                ->leftJoin('religion', 'religion.id', '=', 'user_basic_profile.religion_id')
                ->select('users.id as user_id', 'rank.code as rank'
                        , DB::raw("CONCAT(rank.code, ' ', users.full_name, ' (', users.official_name, ')') as user_name")
                        , 'arms_service.name as arms_service_name', 'commissioning_course.name as commissioning_course_name'
                        , 'religion.name as religion_name'
                        , 'users.*', 'wing.code as wing_name', 'user_basic_profile.*', 'user_basic_profile.id as user_basic_profile_id')
                ->where('users.status', '1')
                ->where('users.id', $id)
                ->first();

//        echo '<pre>';
//        print_r($userInfoData->toArray());
//        exit;

        $civilEducationInfoData = UserCivilEducation::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'civil_education_info')
                ->first();

        $serviceRecordInfoData = UserServiceRecord::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'service_record_info')
                ->first();

        $msnDataInfo = UserMission::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'msn_info')
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


        $countryVisitDataInfo = UserCountryVisit::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'visit_info')
                ->first();

        $bankInfoData = UserBank::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'bank_info')
                ->first();

        $childInfoData = UserChild::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'user_child_info', 'no_of_child')
                ->first();

        $defenceRelativeInfoData = UserRelativeInDefence::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'user_relative_info')
                ->first();

        $milQualification = [];
        if (!empty($defenceRelativeInfoData)) {
            $milQualArr = !empty($defenceRelativeInfoData->user_relative_info) ? json_decode($defenceRelativeInfoData->user_relative_info, true) : [];
            if (!empty($milQualArr)) {
                foreach ($milQualArr as $mKey => $mInfo) {
                    $type = Common::getMilCourseType($mInfo['course']);
                    $milQualification[$mKey] = $mInfo;
                }
            }
        }

        $othersInfoData = UserOthers::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'decoration_id', 'hobby_id', 'extra_curriclar_expt')
                ->first();
        $passportInfoData = UserPassport::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : 0)
                ->select('id', 'user_basic_profile_id', 'passport_no', 'place_of_issue', 'date_of_issue', 'date_of_expire')
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
        $appointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $allAppointmentList = array('0' => __('label.SELECT_APPOINTMENT_OPT')) + Appointment::orderBy('order', 'asc')
                        ->where('status', '1')->pluck('code', 'id')->toArray();
        $armsServiceList = ['0' => __('label.SELECT_ARMS_SERVICE_OPT')] + ArmsService::pluck('code', 'id')->toArray();
        $unitList = ['0' => __('label.SELECT_UNIT_OPT')] + Unit::pluck('code', 'id')->toArray();
        $maritalStatusList = ['0' => __('label.SELECT_MARITAL_STATUS_OPT')] + Helper::getMaritalStatus();
        $countriesVisitedList = Country::pluck('name', 'id')->toArray();
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::pluck('name', 'id')->toArray();
        $organizationList = ['0' => __('label.SELECT_UNIT_FMN_INST_OPT')] + Unit::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $milCourseList = ['0' => __('label.SELECT_COURSE_OPT')] + MilCourse::where('status', '1')
                        ->orderBy('name', 'asc')
                        ->pluck('short_info', 'id')->toArray();

        //Division District Thana for user permanent address
        $addressInfo = UserPermanentAddress::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : '0')
                ->select('id', 'user_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details', 'same_as_present')
                ->first();
        $presentAddressInfo = UserPresentAddress::where('user_basic_profile_id', !empty($userInfoData->user_basic_profile_id) ? $userInfoData->user_basic_profile_id : '0')
                ->select('id', 'user_basic_profile_id', 'division_id', 'district_id', 'thana_id', 'address_details')
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
        $spouseProfession = Common::getSpouseProfessionList();

        $respList = Common::getSvcResposibilityList();

        if ($request->view == 'print') {
            return view($prinLloadView)->with(compact('userInfoData', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                    , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                    , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                    , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                    , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                    , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                    , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                    , 'spouseProfession', 'respList', 'milQualification'));
        }


        return view($loadView)->with(compact('userInfoData', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                , 'spouseProfession', 'respList', 'milQualification')
        );
    }

    public static function getDsDeligationList() {

        $dsDeligationList = DeligateCiAcctToDs::join('course', 'course.id', 'deligate_ci_acct_to_ds.course_id')
                        ->join('training_year', 'training_year.id', 'course.training_year_id')
                        ->where('training_year.status', '1')->where('course.status', '1')
                        ->pluck('ds_id', 'course_id')->toArray();

        return $dsDeligationList;
    }

    public static function getReportDelegationList() {

        $reportDeligationInfo = DeligateReportsToDs::join('course', 'course.id', 'deligate_reports_to_ds.course_id')
                        ->join('training_year', 'training_year.id', 'course.training_year_id')
                        ->where('training_year.status', '1')->where('course.status', '1')
                        ->select('report', 'course_id')->get();
        $reportDeligationList = [];
        if (!$reportDeligationInfo->isEmpty()) {
            foreach ($reportDeligationInfo as $info) {
                $reportArr = !empty($info->report) ? explode(',', $info->report) : [];

                if (!empty($reportArr)) {
                    foreach ($reportArr as $key => $val) {
                        $reportDeligationList[$val] = $val;
                    }
                }
            }
        }

        return $reportDeligationList;
    }

    public static function getReportDelegationDsList() {

        $reportDeligationDs = DeligateReportsToDs::join('course', 'course.id', 'deligate_reports_to_ds.course_id')
                        ->join('training_year', 'training_year.id', 'course.training_year_id')
                        ->where('training_year.status', '1')->where('course.status', '1')
                        ->select('ds_id')->first();

        $dsList = User::where('users.group_id', 4)->where('users.status', '1')
                        ->pluck('users.id', 'users.id')->toArray();
        $reportDeligationDsList = [];

        if (!empty($reportDeligationDs)) {
			$dsIds = !empty($reportDeligationDs->ds_id) ? explode(',', $reportDeligationDs->ds_id) : ($reportDeligationDs->ds_id == '0' ? $dsList : []); 
				
			if(!empty($dsIds)){
				foreach($dsIds as $dsK => $dsId){
					$reportDeligationDsList[$dsId] = $dsId;
				}
			}
        }

        return $reportDeligationDsList;
    }

    public static function getCMFullName($cmFullName) {
        $cmFullName = !empty($cmFullName) ? explode('<', $cmFullName) : [];

        $cmFullName[1] = !empty($cmFullName[1]) ? str_replace('b>', '', $cmFullName[1]) : '';
        $cmFullName[2] = !empty($cmFullName[2]) ? str_replace('/b>', '', $cmFullName[2]) : '';

        return $cmFullName;
    }

    public static function getCommissionType() {
        $commissionTypeList = [
            '1' => __('label.REGULAR'),
            '2' => __('label.PERMANENT'),
            '3' => __('label.SHORT_SERVICE'),
            '4' => __('label.SHORT_COURSE'),
        ];

        return $commissionTypeList;
    }

    public static function getMilCourseCategory() {
        $categoryList = [
            '0' => __('label.SELECT_CATEGORY_OPT'),
            '1' => __('label.HOME_COURSE'),
            '2' => __('label.FOREIGN_COURSE'),
            '3' => __('label.BOTH'),
        ];

        return $categoryList;
    }

    public static function getBloodGroup() {
        $bloodGroupList = [
            '1' => __('label.A_POS'),
            '2' => __('label.A_NEG'),
            '3' => __('label.B_POS'),
            '4' => __('label.B_NEG'),
            '5' => __('label.AB_POS'),
            '6' => __('label.AB_NEG'),
            '7' => __('label.O_POS'),
            '8' => __('label.O_NEG'),
        ];

        return $bloodGroupList;
    }

    public static function getProfile(Request $request, $id, $loadView, $prinLloadView) {
        $keyAppt = $milQualification = [];
        $qpArr = $request->all();
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
                ->where('cm_basic_profile.id', $id)
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
                    $milQualification[$type][$mKey] = $mInfo;
                }
            }
        }

        $othersInfoData = CmOthers::where('cm_basic_profile_id', !empty($cmInfoData->cm_basic_profile_id) ? $cmInfoData->cm_basic_profile_id : 0)
                ->select('id', 'cm_basic_profile_id', 'decoration_id', 'hobby_id', 'extra_curriclar_expt')
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
        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + Course::pluck('name', 'id')->toArray();
        $organizationList = ['0' => __('label.SELECT_UNIT_FMN_INST_OPT')] + Unit::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('code', 'id')->toArray();
        $milCourseList = ['0' => __('label.SELECT_COURSE_OPT')] + MilCourse::where('status', '1')
                        ->orderBy('name', 'asc')
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


        $respList = Common::getSvcResposibilityList();

        if ($request->view == 'print') {
            return view($prinLloadView)->with(compact('cmInfoData', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                    , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                    , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                    , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                    , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                    , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                    , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                    , 'respList', 'milQualification'));
        }


        return view($loadView)->with(compact('cmInfoData', 'religionList', 'appointmentList', 'allAppointmentList', 'armsServiceList'
                                , 'unitList', 'maritalStatusList', 'countriesVisitedList', 'othersInfoData', 'addressInfo'
                                , 'divisionList', 'districtList', 'thanaList', 'civilEducationInfoData', 'serviceRecordInfoData'
                                , 'defenceRelativeInfoData', 'courseList', 'organizationList', 'passportInfoData'
                                , 'presentAddressInfo', 'presentDistrictList', 'presentThanaList', 'milCourseList'
                                , 'msnDataInfo', 'countryVisitDataInfo', 'bankInfoData', 'decorationList', 'awardList'
                                , 'hobbyList', 'childInfoData', 'qpArr', 'commissionTypeList', 'bloodGroupList', 'keyAppt'
                                , 'respList', 'milQualification')
        );
    }

    public static function getGenderWisePronounList($gender) {
        $pronounList = [];
        if ($gender == '1') {
            $pronounList = [
                '@He' => __('label.HE'),
                '@he' => __('label.HE_'),
                '@His' => __('label.HIS'),
                '@his' => __('label.HIS_'),
                '@Him' => __('label.HIM'),
                '@him' => __('label.HIM_'),
            ];
        } elseif ($gender == '2') {
            $pronounList = [
                '@He' => __('label.SHE'),
                '@he' => __('label.SHE_'),
                '@His' => __('label.HER'),
                '@his' => __('label.HER_'),
                '@Him' => __('label.HER'),
                '@him' => __('label.HER_'),
            ];
        }
        return $pronounList;
    }

    public static function getGenderList() {
        $genderList = [
            '1' => __('label.MALE'),
            '2' => __('label.FEMALE'),
        ];
        return $genderList;
    }

    public static function getCmActivationStateSummary($request, $loadView) {
        //echo '<pre>';        print_r($request->all()); exit;
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        $course = Course::where('id', $request->course_id)->first();
        $term = Term::where('id', $request->term_id)->first();
        $event = Event::where('id', $request->event_id)->first();
        $subEvent = SubEvent::where('id', $request->sub_event_id)->first();
        $subSubEvent = SubSubEvent::where('id', $request->sub_sub_event_id)->first();
        $subSubSubEvent = SubSubSubEvent::where('id', $request->sub_sub_sub_event_id)->first();



        $cmDataInfo = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_marking_group.cm_id')
                ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->where('marking_group.course_id', $request->course_id)
                ->where('marking_group.term_id', $request->term_id)
                ->where('marking_group.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $cmDataInfo = $cmDataInfo->where('marking_group.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $cmDataInfo = $cmDataInfo->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $cmDataInfo = $cmDataInfo->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }


        $cmDataInfo = $cmDataInfo->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                        , 'cm_marking_group.id as cm_marking_group_id', 'cm_marking_group.active'
                        , 'cm_basic_profile.id as cm_id'
                        , 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                        , 'rank.code as rank_name')
                ->orderBy('wing.order', 'asc')
                ->orderBy('rank.order', 'asc')
                ->orderBy('cm_basic_profile.personal_no', 'asc')
                ->get();



        $cmDataList = [];
        if (!$cmDataInfo->isEmpty()) {
            foreach ($cmDataInfo as $cm) {
                $cmDataList[$cm->cm_id] = $cm->toArray();
            }
        }

        // assessment marking data
        $eventAssessmentMarkingInfo = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
                    $join->on('event_assessment_marking_lock.course_id', 'event_assessment_marking.course_id');
                    $join->on('event_assessment_marking_lock.term_id', 'event_assessment_marking.term_id');
                    $join->on('event_assessment_marking_lock.event_id', 'event_assessment_marking.event_id');
                    $join->on('event_assessment_marking_lock.sub_event_id', 'event_assessment_marking.sub_event_id');
                    $join->on('event_assessment_marking_lock.sub_sub_event_id', 'event_assessment_marking.sub_sub_event_id');
                    $join->on('event_assessment_marking_lock.sub_sub_sub_event_id', 'event_assessment_marking.sub_sub_sub_event_id');
                    $join->on('event_assessment_marking_lock.locked_by', 'event_assessment_marking.updated_by');
                })
                ->where('event_assessment_marking.course_id', $request->course_id)
                ->where('event_assessment_marking.term_id', $request->term_id)
                ->where('event_assessment_marking.event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->where('event_assessment_marking.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $eventAssessmentMarkingInfo = $eventAssessmentMarkingInfo->select('event_assessment_marking.cm_id', 'event_assessment_marking.mks', 'event_assessment_marking_lock.locked_by')
                ->get();

        $eventAssessmentMarkingArr = [];

        if (!$eventAssessmentMarkingInfo->isEmpty()) {
            foreach ($eventAssessmentMarkingInfo as $markingInfo) {
                $eventAssessmentMarkingArr[$markingInfo->cm_id]['mks'] = $markingInfo->mks;
                $eventAssessmentMarkingArr[$markingInfo->cm_id]['locked_by'] = $markingInfo->locked_by;
            }
        }

        $view = view($loadView, compact('request', 'activeTrainingYearInfo', 'course', 'term', 'event', 'subEvent'
                        , 'subSubEvent', 'subSubSubEvent'
                        , 'cmDataList', 'eventAssessmentMarkingArr'))->render();
        return response()->json(['html' => $view]);
    }

    public static function getCommisioningDate(Request $request) {

        $commissioningCourse = CommissioningCourse::orderBy('commissioning_date', 'asc')
                        ->where('id', $request->commissioning_course_id)
                        ->select('commissioning_date as date')->first();
        $commisioningDate = !empty($commissioningCourse->date) ? Helper::formatDate($commissioningCourse->date) : '';
        return response()->json(['commisioningDate' => $commisioningDate]);
    }

    public static function getMilCourseType($milCourseId) {

        $milCourse = MilCourse::where('status', '1')
                ->where('id', $milCourseId)
                ->select('category_id')
                ->first();

        return !empty($milCourse->category_id) ? $milCourse->category_id : '0';
    }

    public static function getSvcResposibilityList() {

        $svcResposibilityList = [
            '1' => __('label.COMMAND_UNIT'),
            '2' => __('label.STAFF'),
            '3' => __('label.INSTRUCTOR'),
        ];

        return $svcResposibilityList;
    }

    public static function getCmMarkingSummary($request, $loadView) {
        $maProcess = $request->ma_process;
        //echo '<pre>';print_r($request->all()); exit;
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();
        $course = Course::where('id', $request->course_id)->first();
        $term = Term::where('id', $request->term_id)->first();
        $event = Event::where('id', $request->event_id)->first();
        $subEvent = SubEvent::where('id', $request->sub_event_id)->first();
        $subSubEvent = SubSubEvent::where('id', $request->sub_sub_event_id)->first();
        $subSubSubEvent = SubSubSubEvent::where('id', $request->sub_sub_sub_event_id)->first();

        //Lock Table
        $maMarkingLockInfo = MutualAssessmentMarkingLock::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id);
        if (!empty($request->sub_event_id)) {
            $maMarkingLockInfo = $maMarkingLockInfo->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $maMarkingLockInfo = $maMarkingLockInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $maMarkingLockInfo = $maMarkingLockInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $maMarkingLockInfo = $maMarkingLockInfo->pluck('marking_cm_id', 'marking_cm_id')->toArray();


        $totalMarkingCmInfo = [];
        if ($maProcess == '3') {
            $cmDataInfo = CmMarkingGroup::join('marking_group', 'marking_group.id', 'cm_marking_group.marking_group_id')
                    ->join('event_group', 'event_group.id', 'marking_group.event_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_marking_group.cm_id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->where('marking_group.event_id', $request->event_id);
            if (!empty($request->sub_event_id)) {
                $cmDataInfo = $cmDataInfo->where('marking_group.sub_event_id', $request->sub_event_id);
            }
            if (!empty($request->sub_sub_event_id)) {
                $cmDataInfo = $cmDataInfo->where('marking_group.sub_sub_event_id', $request->sub_sub_event_id);
            }
            if (!empty($request->sub_sub_sub_event_id)) {
                $cmDataInfo = $cmDataInfo->where('marking_group.sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
            }

            if (!empty($request->data_id)) {
                if ($request->data_id == '1') {
                    $cmDataInfo = $cmDataInfo->whereIn('cm_marking_group.cm_id', $maMarkingLockInfo);
                } elseif ($request->data_id == '2') {
                    $cmDataInfo = $cmDataInfo->whereNotIn('cm_marking_group.cm_id', $maMarkingLockInfo);
                }
            }

            $cmDataInfo = $cmDataInfo->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                            , 'cm_basic_profile.id as cm_id'
                            , 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                            , 'rank.code as rank_name', 'event_group.name as group')
                    ->orderBy('event_group.order', 'asc')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();
        } elseif (in_array($maProcess, ['1', '2'])) {

            $cmDataInfo = CmGroupMemberTemplate::join('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id')
                    ->join('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id')
                    ->join('cm_basic_profile', 'cm_basic_profile.id', 'cm_group_member_template.cm_basic_profile_id')
                    ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                    ->where('cm_group_member_template.course_id', $request->course_id)
                    ->where('cm_group_member_template.term_id', $request->term_id)
                    ->where('cm_group.type', $maProcess);

            if (!empty($request->data_id)) {
                if ($request->data_id == '1') {
                    $cmDataInfo = $cmDataInfo->whereIn('cm_basic_profile.id', $maMarkingLockInfo);
                } elseif ($request->data_id == '2') {
                    $cmDataInfo = $cmDataInfo->whereNotIn('cm_basic_profile.id', $maMarkingLockInfo);
                }
            }

            $cmDataInfo = $cmDataInfo->select(DB::raw("CONCAT(rank.code, ' ', cm_basic_profile.full_name, ' (', cm_basic_profile.personal_no, ')') as cm_name")
                            , 'cm_basic_profile.id as cm_id'
                            , 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                            , 'rank.code as rank_name', 'cm_group.name as group')
                    ->orderBy('cm_group.order', 'asc')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('cm_basic_profile.personal_no', 'asc')
                    ->get();
        }


        $cmDataList = [];
        if (!$cmDataInfo->isEmpty()) {
            foreach ($cmDataInfo as $cm) {
                $cmDataList[$cm->cm_id] = $cm->toArray();
            }
        }



        // assessment marking data
        $maMarkingInfo = MutualAssessmentMarking::where('course_id', $request->course_id)
                ->where('term_id', $request->term_id)
                ->where('event_id', $request->event_id);

        if (!empty($request->sub_event_id)) {
            $maMarkingInfo = $maMarkingInfo->where('sub_event_id', $request->sub_event_id);
        }
        if (!empty($request->sub_sub_event_id)) {
            $maMarkingInfo = $maMarkingInfo->where('sub_sub_event_id', $request->sub_sub_event_id);
        }
        if (!empty($request->sub_sub_sub_event_id)) {
            $maMarkingInfo = $maMarkingInfo->where('sub_sub_sub_event_id', $request->sub_sub_sub_event_id);
        }
        $maMarkingInfo = $maMarkingInfo->pluck('updated_by', 'updated_by')
                ->toArray();

        $view = view($loadView, compact('request', 'activeTrainingYearInfo', 'course', 'term', 'event', 'subEvent', 'subSubEvent'
                        , 'subSubSubEvent', 'cmDataList', 'maMarkingLockInfo', 'maMarkingInfo'))->render();
        return response()->json(['html' => $view]);
    }

    public static function getSpouseProfessionList() {
        $spouseProfession = Occupation::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        return $spouseProfession;
    }

    public static function getFurnishedCmName($cmName) {
        return !empty($cmName) ? strip_tags($cmName, '<b>') : '';
    }

    public static function getFormattedPhoneNumber($number) {
        return !empty($number) ? str_replace(['+880', '880'], '+88 0', $number) : '';
    }

    public static function getFormattedAmp($text) {
        return !empty($text) ? str_replace('&', '&amp;', $text) : '';
    }

    public static function getMonthList() {
        $monthList = [
            '00' => __('label.SELECT_MONTH_OPT'),
            '01' => date('F', mktime(0, 0, 0, 1, 1)),
            '02' => date('F', mktime(0, 0, 0, 2, 1)),
            '03' => date('F', mktime(0, 0, 0, 3, 1)),
            '04' => date('F', mktime(0, 0, 0, 4, 1)),
            '05' => date('F', mktime(0, 0, 0, 5, 1)),
            '06' => date('F', mktime(0, 0, 0, 6, 1)),
            '07' => date('F', mktime(0, 0, 0, 7, 1)),
            '08' => date('F', mktime(0, 0, 0, 8, 1)),
            '09' => date('F', mktime(0, 0, 0, 9, 1)),
            '10' => date('F', mktime(0, 0, 0, 10, 1)),
            '11' => date('F', mktime(0, 0, 0, 11, 1)),
            '12' => date('F', mktime(0, 0, 0, 12, 1)),
        ];

        return $monthList;
    }

    public static function getArchiveCompartmentList() {
        $compartmentList = [
            '1' => __('label.DS'),
            '2' => __('label.CM'),
            '3' => __('label.STAFF')
        ];
        return $compartmentList;
    }

    public static function getResultList() {
        $resultArr = [];

        //Check Dependency before deletion
        $cmMilCourseInfo = CmRelativeInDefence::select('cm_relative_info')->get();

        if (!$cmMilCourseInfo->isEmpty()) {
            foreach ($cmMilCourseInfo as $cmMil) {
                $cmCourseArr = !empty($cmMil->cm_relative_info) ? json_decode($cmMil->cm_relative_info, true) : [];

                if (!empty($cmCourseArr)) {
                    foreach ($cmCourseArr as $cKey => $cInfo) {
                        if (!empty($cInfo['result'])) {
                            $resultArr[$cInfo['result']] = $cInfo['result'];
                        }
                    }
                }
            }
        }
        $dsMilCourseInfo = UserRelativeInDefence::select('user_relative_info')->get();

        if (!$dsMilCourseInfo->isEmpty()) {
            foreach ($dsMilCourseInfo as $dsMil) {
                $dsCourseArr = !empty($dsMil->user_relative_info) ? json_decode($dsMil->user_relative_info, true) : [];

                if (!empty($dsCourseArr)) {
                    foreach ($dsCourseArr as $dKey => $dInfo) {
                        if (!empty($dInfo['result'])) {
                            $resultArr[$dInfo['result']] = $dInfo['result'];
                        }
                    }
                }
            }
        }

        $resultList = [
            '0' => __('label.SELECT_GRADE'),
            'A' => 'A',
            'B+' => 'B+',
            'B' => 'B',
            'B-' => 'B-',
            'C' => 'C',
            'D' => 'D',
            'F' => 'F',
            'AX' => 'AX',
            'AY+' => 'AY+',
            'AY' => 'AY',
            'AY-' => 'AY-',
            'AZ' => 'AZ',
            'B+X' => 'B+X',
            'B+Y+' => 'B+Y+',
            'B+Y' => 'B+Y',
            'B+Y-' => 'B+Y-',
            'B+Z' => 'B+Z',
            'BX' => 'BX',
            'BY+' => 'BY+',
            'BY' => 'BY',
            'BY-' => 'BY-',
            'BZ' => 'BZ',
            'BX' => 'BX',
            'BY+' => 'BY+',
            'BY' => 'BY',
            'BY-' => 'BY-',
            'BZ' => 'BZ',
            'B-X' => 'B-X',
            'B-Y+' => 'B-Y+',
            'B-Y' => 'B-Y',
            'B-Y-' => 'B-Y-',
            'B-Z' => 'B-Z',
            'CX' => 'CX',
            'CY+' => 'CY+',
            'CY' => 'CY',
            'CY-' => 'CY-',
            'CZ' => 'CZ',
            'Qual' => 'Qual',
        ];
        $resultList = $resultList + $resultArr;
//        ksort($resultList);
        return $resultList;
    }

    public static function getKnGradeList() {
        $resultList = [
            '0' => __('label.SELECT_GRADE'),
            'A' => 'A',
            'B+' => 'B+',
            'B' => 'B',
            'B-' => 'B-',
            'C' => 'C',
            'D' => 'D',
            'F' => 'F',
            'Qual' => 'Qual',
        ];
        return $resultList;
    }

    public static function getInstGradeList() {
        $resultList = [
            '0' => __('label.SELECT_GRADE'),
            'X' => 'X',
            'Y+' => 'Y+',
            'Y' => 'Y',
            'Y-' => 'Y-',
            'Z' => 'Z',
        ];
        return $resultList;
    }

    public static function getResList($res, $f) {
        $knGradeList = Common::getKnGradeList();
        $instGradeList = Common::getInstGradeList();
        $resArr[$res] = $res;
        if ($f == 1) {
            if (!empty($instGradeList)) {
                foreach ($instGradeList as $riK => $riV) {
                    if ($riK != '0') {
                        $ri = $res . $riV;
                        $resArr[$ri] = $ri;
                    }
                }
            }
        } elseif ($f == 2) {
            if (!empty($knGradeList)) {
                foreach ($knGradeList as $rkK => $rkV) {
                    if ($rkK != '0') {
                        $rk = $rkV . $res;
                        $resArr[$rk] = $rk;
                    }
                }
            }
        }

        return $resArr;
    }

    public static function getContentSummary($courseId) {
        $today = date("Y-m-d");
        $monthStart = date("Y-m-01");
        $monthEnd = date("Y-m-t");

        $contentArr['todays_total'] = Content::where('status', '1')->where('date_upload', $today)
                ->select(DB::raw("COUNT(id) as total"), 'origin')
                ->groupBy('origin')->pluck('total', 'origin')
                ->toArray();
        $contentArr['todays']['total'] = !empty($contentArr['todays_total']) ? array_sum($contentArr['todays_total']) : 0;

        $contentArr['month_total'] = Content::where('status', '1')->whereBetween('date_upload', [$monthStart, $monthEnd])
                ->select(DB::raw("COUNT(id) as total"), 'origin')
                ->groupBy('origin')->pluck('total', 'origin')
                ->toArray();
        $contentArr['month']['total'] = !empty($contentArr['month_total']) ? array_sum($contentArr['month_total']) : 0;

        $contentArr['course_total'] = Content::where('status', '1')->where('course_id', $courseId)
                ->select(DB::raw("COUNT(id) as total"), 'origin')
                ->groupBy('origin')->pluck('total', 'origin')
                ->toArray();
        $contentArr['course']['total'] = !empty($contentArr['course_total']) ? array_sum($contentArr['course_total']) : 0;

//        $contentArr = 

        return $contentArr;
    }

    public static function downloadFile(Request $request) {
        $fileName = $request->content;
        $fileOriginalName = $request->content_original;
        $contentType = $request->content_type;
        $fileType = '';
        if (!empty($contentType)) {
            if ($contentType == '1') {
                $fileType = 'file';
            } elseif ($contentType == '2') {
                $fileType = 'photo';
            } elseif ($contentType == '3') {
                $fileType = 'video';
            }
        }
        $filePath = 'public/uploads/content/' . $fileType . '/' . $fileName;
        clearstatcache();

        $header = [
            'Content-Description: File Transfer',
            'Content-Type: application/force-download',
            'Content-Disposition: attachment; filename=' . $fileName,
            'Content-Length: ' . filesize($filePath),
        ];

        return Response::download($filePath, $fileOriginalName, $header);
    }

}
