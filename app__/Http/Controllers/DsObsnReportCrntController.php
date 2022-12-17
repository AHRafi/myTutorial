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
use App\CmBasicProfile;
use App\EventAssessmentMarking;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\GradingSystem;
use App\CmToSyn;
use App\CriteriaWiseWt;
use App\CiObsnMarkingLock;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use App\ComdtObsnMarkingLock;
use App\DsObsnMarkingLimit;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\DsMarkingGroup;
use App\User;
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

class DsObsnReportCrntController extends Controller {

    private $controller = 'DsObsnReportCrnt';

    public function index(Request $request) {
        //get only active training year
        $activeTrainingYearList = TrainingYear::select('name', 'id')->where('status', '1')->first();

        if (empty($activeTrainingYearList)) {
            $void['header'] = __('label.TERM_RESULT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $courseList = Course::where('training_year_id', $activeTrainingYearList->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($courseList)) {
            $void['header'] = __('label.TERM_RESULT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $courseStatusArr = TermToCourse::join('course', 'course.id', 'term_to_course.course_id')
                        ->where('course.training_year_id', $activeTrainingYearList->id)
                        ->where('term_to_course.status', '2')
                        ->pluck('course.id', 'course.id')->toArray();
        $termStatusArr = TermToCourse::where('course_id', $courseList->id)
                        ->where('status', '2')->pluck('term_id', 'term_id')->toArray();



        $termList = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                ->where('term_to_course.course_id', $courseList->id)
                ->where('term_to_course.status', '<>', '0');
        if (in_array(Auth::user()->group_id, [2])) {
            $termList = $termList->whereIn('id', $termStatusArr);
        }
        $termList = $termList->orderBy('term.order', 'asc')
                ->pluck('term.name', 'term.id')
                ->toArray();
        $termList = ['0' => __('label.SELECT_TERM_OPT')] + $termList;

        $sortByList = ['position' => __('label.POSITION'), 'svc' => __('label.WING'), 'syn' => __('label.SYN'), 'alphabatically' => __('label.ALPHABATICALLY'), 'svc_alpha' => __('label.WING') . ' & ' . __('label.ALPHABATICALLY'), 'personal_no' => __('label.PERSONAL_NO')];

        $assignedObsnInfo = $assignedDsObsnInfo = $gradeInfo = $comdtObsnLockInfo = $ciObsnLockInfo = 0;
        $eventMksWtArr = $eventWiseMksWtArr = $cmArr = $dsMksWtArr = $dsDataList = $rowSpanArr = $achieveMksWtArr = [];
        if ($request->generate == 'true') {

            $tyName = $request->training_year_id != '0' && !empty($activeTrainingYearList[$request->training_year_id]) ? '_' . $activeTrainingYearList[$request->training_year_id] : '';
            $courseName = $request->course_id != '0' && !empty($courseList[$request->course_id]) ? '_' . $courseList[$request->course_id] : '';
            $termName = $request->term_id != '0' && !empty($termList[$request->term_id]) ? '_' . $termList[$request->term_id] : '';
            $fileName = 'DS_Obsn_Report' . $tyName . $courseName . $termName;
            $fileName = Common::getFileFormatedName($fileName);


            // get assigned ci obsn wt
            $assignedObsnInfo = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')->where('course_id', $request->course_id)->first();

            $assignedDsObsnInfo = DsObsnMarkingLimit::select('mks_limit', 'obsn')
                    ->where('course_id', $request->course_id)->where('term_id', $request->term_id)
                    ->first();


            $dsDataInfo = DsMarkingGroup::join('marking_group', 'marking_group.id', 'ds_marking_group.marking_group_id')
                    ->join('users', 'users.id', 'ds_marking_group.ds_id')
                    ->join('rank', 'rank.id', 'users.rank_id')
                    ->leftJoin('wing', 'wing.id', 'users.wing_id')
                    ->leftJoin('user_basic_profile', 'user_basic_profile.user_id', 'users.id')
                    ->join('appointment', 'appointment.id', 'ds_marking_group.ds_appt_id')
                    ->where('marking_group.course_id', $request->course_id)
                    ->where('marking_group.term_id', $request->term_id)
                    ->select('appointment.code as appt', 'users.id as ds_id', 'users.photo'
                            , DB::raw("CONCAT(rank.code, ' ', users.full_name) as ds_name"), 'users.personal_no')
                    ->orderBy('wing.order', 'asc')
                    ->orderBy('appointment.order', 'asc')
                    ->orderBy('rank.order', 'asc')
                    ->orderBy('users.personal_no', 'asc')
                    ->get();
            
            if (!$dsDataInfo->isEmpty()) {
                foreach ($dsDataInfo as $ds) {
                    $dsDataList[$ds->ds_id] = $ds->toArray();
                }
            }

            // get grade system
            $gradeInfo = GradingSystem::select('id', 'marks_from', 'marks_to', 'grade_name')->get();

            $gradeArr = [];
            if (!$gradeInfo->isEmpty()) {
                foreach ($gradeInfo as $grade) {
                    $gradeArr[$grade->grade_name]['id'] = $grade->id;
                    $gradeArr[$grade->grade_name]['start'] = $grade->marks_from;
                    $gradeArr[$grade->grade_name]['end'] = $grade->marks_to;
                }
            }

            $cmDataArr = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id');
            if (!empty($request->sort) && $request->sort == 'syn') {
                $cmDataArr = $cmDataArr->leftJoin('cm_group_member_template', 'cm_group_member_template.cm_basic_profile_id', 'cm_basic_profile.id')
                        ->leftJoin('cm_group', 'cm_group.id', 'cm_group_member_template.cm_group_id');
            }
            $cmDataArr = $cmDataArr->where('cm_basic_profile.course_id', $request->course_id);
//            if (in_array(Auth::user()->group_id, [4])) {
//                $cmDataArr = $cmDataArr->whereIn('cm_basic_profile.id', $dsCmArr);
//            }
            $cmDataArr = $cmDataArr->where('cm_basic_profile.status', '1')
                    ->select('cm_basic_profile.id', 'cm_basic_profile.photo', 'cm_basic_profile.personal_no'
                    , 'cm_basic_profile.full_name', 'rank.code as rank_name');

            if (!empty($request->sort)) {
                if ($request->sort == 'syn') {
                    $cmDataArr = $cmDataArr->where('cm_group.order', '<=', '2')
                            ->orderBy('cm_group.order', 'asc')
                            ->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                } elseif ($request->sort == 'alphabatically') {
                    $cmDataArr = $cmDataArr->orderBy('cm_basic_profile.official_name', 'asc');
                } elseif ($request->sort == 'svc_alpha') {
                    $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')->orderBy('cm_basic_profile.official_name', 'asc');
                } else {
                    $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')
                            ->orderBy('rank.order', 'asc')
                            ->orderBy('cm_basic_profile.personal_no', 'asc');
                }
            } else {
                $cmDataArr = $cmDataArr->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc');
            }
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
                    ->where('ds_obsn_marking.course_id', $request->course_id)
                    ->where('ds_obsn_marking.term_id', $request->term_id)
                    ->select('ds_obsn_marking.cm_id', 'ds_obsn_marking.obsn_mks', 'ds_obsn_marking.obsn_wt', 'ds_obsn_marking.updated_by')
                    ->get();
            $dsMksWtArr = [];
            if (!$dsObsnMksWtInfo->isEmpty()) {
                foreach ($dsObsnMksWtInfo as $dsObsnInfo) {
                    $obsnWt = 0;
                    if (!empty($assignedDsObsnInfo->mks_limit)) {
                        $obsnWt = (($dsObsnInfo->obsn_mks * $assignedDsObsnInfo->obsn) / $assignedDsObsnInfo->mks_limit);
                    }

                    $dsMksWtArr[$dsObsnInfo->updated_by][$dsObsnInfo->cm_id]['mks'] = $dsObsnInfo->obsn_mks;
                    $dsMksWtArr[$dsObsnInfo->updated_by][$dsObsnInfo->cm_id]['wt'] = $obsnWt;
                }
            }
            $dsObsnMksWtAvgInfo = DsObsnMarking::join('ds_obsn_marking_lock', function($join) {
                        $join->on('ds_obsn_marking_lock.course_id', 'ds_obsn_marking.course_id');
                        $join->on('ds_obsn_marking_lock.term_id', 'ds_obsn_marking.term_id');
                        $join->on('ds_obsn_marking_lock.locked_by', 'ds_obsn_marking.updated_by');
                    })
                    ->where('ds_obsn_marking.course_id', $request->course_id)
                    ->where('ds_obsn_marking.term_id', $request->term_id)
                    ->select('ds_obsn_marking.cm_id', DB::raw('AVG(ds_obsn_marking.obsn_mks) as obsn_mks')
                            , DB::raw('AVG(ds_obsn_marking.obsn_wt) as obsn_wt'))
                    ->groupBy('ds_obsn_marking.cm_id')
                    ->get();
            $dsObsnMksWtArr = [];
            if (!$dsObsnMksWtAvgInfo->isEmpty()) {
                foreach ($dsObsnMksWtAvgInfo as $dsObsnInfo) {
                    $dsObsnMksWtArr[$dsObsnInfo->cm_id]['ds_obsn_mks'] = $dsObsnInfo->obsn_mks;
                    $dsObsnMksWtArr[$dsObsnInfo->cm_id]['ds_obsn_wt'] = $dsObsnInfo->obsn_wt;
                }
            }



            if (!empty($dsObsnMksWtArr)) {

                foreach ($dsObsnMksWtArr as $cmId => $info) {
                    $cmArr[$cmId]['total_assigned_wt'] = !empty($cmArr[$cmId]['total_assigned_wt']) ? $cmArr[$cmId]['total_assigned_wt'] : 0;
                    $cmArr[$cmId]['total_assigned_wt'] += (!empty($assignedDsObsnInfo->obsn) ? $assignedDsObsnInfo->obsn : 0);

                    $cmArr[$cmId]['ds_obsn_mks'] = $info['ds_obsn_mks'] ?? 0;
                    $cmArr[$cmId]['ds_obsn_wt'] = 0;
                    if (!empty($assignedDsObsnInfo->mks_limit)) {
                        $cmArr[$cmId]['ds_obsn_wt'] = (($cmArr[$cmId]['ds_obsn_mks'] * $assignedDsObsnInfo->obsn) / $assignedDsObsnInfo->mks_limit);
                    }

                    $cmArr[$cmId]['total_term_percent'] = 0;
                    if (!empty($assignedDsObsnInfo->obsn)) {
                        $cmArr[$cmId]['total_term_percent'] = ($cmArr[$cmId]['ds_obsn_wt'] * 100) / $assignedDsObsnInfo->obsn;
                    }
                    $termMarkingArr[$cmId]['percentage'] = $cmArr[$cmId]['total_term_percent'];
                    $totalPercentage = Helper::numberFormatDigit2($cmArr[$cmId]['total_term_percent']);
                    // grade
                    if (!empty($totalPercentage)) {
                        foreach ($gradeArr as $letter => $gradeRange) {
                            if ($cmArr[$cmId]['total_term_percent'] == 100) {
                                $cmArr[$cmId]['total_term_grade'] = "A+";
                                $cmArr[$cmId]['total_term_grade_id'] = $gradeRange['id'];
                            }
                            if ($gradeRange['start'] <= $totalPercentage && $totalPercentage < $gradeRange['end']) {
                                $cmArr[$cmId]['total_term_grade'] = $letter;
                                $cmArr[$cmId]['total_term_grade_id'] = $gradeRange['id'];
                            }
                        }
                    }
                }
            }

//            echo '<pre>';
//            print_r($cmArr);
//            exit;
            // get grade after term total
            $cmArr = Common::getGradeName($cmArr, $gradeInfo, 'total_term_percent', 'grade_after_term_total');

            // get postion after term total
            $cmArr = Common::getPosition($cmArr, 'total_term_percent', 'total_term_position');
            if (empty($request->sort) || $request->sort == 'position') {
                if (!empty($cmArr)) {
                    usort($cmArr, function ($item1, $item2) {
                        if (!isset($item1['total_term_percent'])) {
                            $item1['total_term_percent'] = '';
                        }

                        if (!isset($item2['total_term_percent'])) {
                            $item2['total_term_percent'] = '';
                        }
                        return $item2['total_term_percent'] <=> $item1['total_term_percent'];
                    });
                }
            }
        }

        if ($request->view == 'print') {
            return view('reportCrnt.dsObsn.print.index')->with(compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'sortByList', 'assignedObsnInfo', 'gradeInfo', 'dsMksWtArr', 'dsDataList'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr', 'assignedDsObsnInfo'));
        } elseif ($request->view == 'pdf') {
            $pdf = PDF::loadView('reportCrnt.dsObsn.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'sortByList', 'assignedObsnInfo', 'gradeInfo', 'dsMksWtArr', 'dsDataList'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr', 'assignedDsObsnInfo'))
                    ->setPaper('a4', 'landscape')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download($fileName . '.pdf');
        } elseif ($request->view == 'excel') {
            return Excel::download(new ExcelExport('reportCrnt.dsObsn.print.index', compact('activeTrainingYearList', 'courseList', 'termList'
                                    , 'sortByList', 'assignedObsnInfo', 'gradeInfo', 'dsMksWtArr', 'dsDataList'
                                    , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr', 'assignedDsObsnInfo')), $fileName . '.xlsx');
        }

        return view('reportCrnt.dsObsn.index', compact('activeTrainingYearList', 'courseList', 'termList'
                        , 'sortByList', 'assignedObsnInfo', 'gradeInfo', 'dsMksWtArr', 'dsDataList'
                        , 'eventMksWtArr', 'cmArr', 'rowSpanArr', 'achieveMksWtArr', 'assignedDsObsnInfo'));
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

        $html = view('reportCrnt.dsObsn.getCourse', compact('courseList'))->render();
        $html1 = view('reportCrnt.dsObsn.getCourseErr', compact('courseList'))->render();
        return Response::json(['html' => $html, 'html1' => $html1]);
    }

    public function getTerm(Request $request) {

        $termStatusArr = TermToCourse::where('course_id', $request->course_id)
                        ->where('status', '2')->pluck('term_id', 'term_id')->toArray();


        $termList = TermToCourse::join('term', 'term.id', '=', 'term_to_course.term_id')
                ->where('term_to_course.course_id', $request->course_id)
                ->where('term_to_course.status', '<>', '0');
        if (in_array(Auth::user()->group_id, [2])) {
            $termList = $termList->whereIn('id', $termStatusArr);
        }
        $termList = $termList->orderBy('term.order', 'asc')
                ->pluck('term.name', 'term.id')
                ->toArray();
        $termList = ['0' => __('label.SELECT_TERM_OPT')] + $termList;

        $html = view('reportCrnt.dsObsn.getTerm', compact('termList'))->render();
        return Response::json(['html' => $html]);
    }

    public function filter(Request $request) {

        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'term_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
            'term_id.not_in' => __('label.THE_TERM_FIELD_IS_REQUIRED'),
        ];
        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&term_id=' . $request->term_id . '&sort=' . $request->sort;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('dsObsnReportCrnt?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('dsObsnReportCrnt?generate=true&' . $url);
    }

}
