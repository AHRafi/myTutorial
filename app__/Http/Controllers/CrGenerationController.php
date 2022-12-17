<?php

namespace App\Http\Controllers;

use App\Course;
use App\AssessmentActDeact;
use App\CrGeneration;
use App\CrMarkingSlab;
use App\CrTrait;
use App\TrainingYear;
use App\Event;
use App\EventAssessmentMarking;
use App\User;
use App\CrMarkingReflection;
use App\CmMarkingGroup;
use App\CrGrouping;
use App\TermToEvent;
use App\TermToSubEvent;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\CmBasicProfile;
use App\GradingSystem;
use App\CriteriaWiseWt;
use App\CiObsnMarking;
use App\ComdtObsnMarking;
use App\ComdtObsnMarkingLock;
use App\CiObsnMarkingLock;
use App\CiModerationMarking;
use App\ComdtModerationMarking;
use App\DsObsnMarkingLimit;
use App\DsObsnMarking;
use App\DsObsnMarkingLock;
use App\MutualAssessmentMarking;
use App\CrSentenceToTrait;
use App\CrSentence;
use Auth;
use Common;
use Helper;
use DB;
use URL;
use File;
use Illuminate\Http\Request;
use Response;
use Validator;
use Excel;
use App\Exports\ExcelExport;

class CrGenerationController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $trainingYearList = ['0' => __('label.SELECT_TRAINING_YEAR_OPT')];
        $closedTrainingYear = TrainingYear::where('status', '2')->orderBy('start_date', 'desc')
                        ->select('name', 'id')->first();

        $activeTrainingYear = TrainingYear::where('status', '1')->select('name', 'id')->first();

        if (!empty($activeTrainingYear)) {
            $trainingYearList[$activeTrainingYear->id] = $activeTrainingYear->name;
        }
        if (!empty($closedTrainingYear)) {
            $trainingYearList[$closedTrainingYear->id] = $closedTrainingYear->name;
        }

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')
                ->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;


        $assessmentActDeact = AssessmentActDeact::where('course_id', $request->course_id)
                ->where('status', '1')->where('criteria', '4')
                ->count();



        $activeCourse = Course::where('id', $request->course_id)->select('name')->first();


        //CM list of the DS
        $cmArr = ['0' => __('label.SELECT_CM_OPT')] + CrGrouping::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cr_grouping.cm_id')
                        ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                        ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->where('cr_grouping.course_id', $request->course_id)
                        ->where('cr_grouping.ds_id', Auth::user()->id)
                        ->where('cm_basic_profile.status', '1')
                        ->pluck('cm_name', 'cm_basic_profile.id')
                        ->toArray();

        $gradingTraitId = 0;
        $perfomance = '';
        $sentenceArr = $traitIdArr = $traitList = $sentenceArr = $cm = $sentenceList = $reportDataArr = [];
        if ($request->generate == 'true') {
            //trait wise performance list of CM 
            $perfomanceArr = $this->getTraitWiseRefl($request);
            $perfomance = json_encode($perfomanceArr);
            $perfomanceReflArr = $perfomanceArr[0];
            $perfomanceReflSlabArr = $perfomanceArr[1];

            if (!empty($perfomanceReflSlabArr)) {
                foreach ($perfomanceReflSlabArr as $traitId => $slabId) {
                    $traitIdArr[$traitId] = $traitId;
                }
            }

            $traitInfo = CrTrait::join('cr_para', 'cr_para.id', 'cr_trait.para_id')
                    ->whereIn('cr_trait.id', $traitIdArr)
                    ->where('cr_trait.for_recomnd_sentence', '0')
                    ->where('cr_trait.status', '1')
                    ->orderBy('cr_trait.order', 'asc')
                    ->select('cr_trait.para_id', 'cr_trait.title as title'
                            , 'cr_para.title as para', 'cr_trait.id')
                    ->get();

            $traitArr = [];
            if (!$traitInfo->isEmpty()) {
                foreach ($traitInfo as $trt) {
                    $traitList[$trt->para_id][$trt->id]['title'] = $trt->title;
                    $traitList[$trt->para_id][$trt->id]['para'] = $trt->para;
                    if ($trt->para_id == 3) {
                        $traitList[$trt->para_id][$trt->id]['mks'] = $perfomanceReflArr[$trt->id] ?? 0;
                        $traitList[$trt->para_id][$trt->id]['slab'] = $perfomanceReflSlabArr[$trt->id] ?? 0;
                    }
                }
            }

            $cm = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                    ->where('cm_basic_profile.id', $request->cm_id)
                    ->select('cm_basic_profile.official_name as cm_name', 'rank.name as rank'
                            , 'cm_basic_profile.gender', 'cm_basic_profile.personal_no')
                    ->first();
            $pronounList = Common::getGenderWisePronounList($cm->gender);
            $pronounKeys = array_keys($pronounList);
            $pronounValues = array_values($pronounList);
            $cmNameFormatArr = ['xxx', 'XXX', 'NAME'];
            $cmRankFormatArr = ['Lt Col', '@rank', 'RANK'];


            //start :: trait wise sentence list of CM 
            $sentenceToTraitInfo = CrSentenceToTrait::where('course_id', $request->course_id)
                    ->select('trait_id', 'marking_slab_id', 'sentence')
                    ->get();


            if (!$sentenceToTraitInfo->isEmpty()) {
                foreach ($sentenceToTraitInfo as $ftt) {
                    if (!empty($perfomanceReflSlabArr[$ftt->trait_id]) && $perfomanceReflSlabArr[$ftt->trait_id] == $ftt->marking_slab_id) {
                        $sentences = !empty($ftt->sentence) ? json_decode($ftt->sentence, true) : [];
                        if (!empty($sentences)) {
                            foreach ($sentences as $fKey => $sentence) {
                                //change rank and name
                                //$sentence = str_replace($cmRankFormatArr, $cm->rank, $sentence);
                                $sentence = str_replace($cmNameFormatArr, $cm->rank . ' ' . $cm->cm_name, $sentence);
                                //change pronouns
                                $sentence = str_replace($pronounKeys, $pronounValues, $sentence);

                                $sentenceArr[$ftt->trait_id][$sentence] = $sentence;
                            }
                        }
                    }
                }
            }
            //End :: trait wise sentence list of CM



            $gradingTrait = CrTrait::where('status', '1')->where('for_grading_sentence', '1')
                            ->select('id')->first();
            $gradingTraitId = !empty($gradingTrait->id) ? $gradingTrait->id : 0;
            $overAllMks = !empty($perfomanceReflArr[$gradingTraitId]) ? Helper::numberFormat2Digit($perfomanceReflArr[$gradingTraitId]) : '0.000';
            $gradingSentence = [
                '0' => __('label.CR_GRADING_SENTENCE_1', ['mks' => $overAllMks, 'He' => $pronounList['@He']]),
                '1' => __('label.CR_GRADING_SENTENCE_2', ['mks' => $overAllMks, 'He' => $pronounList['@He']]),
                '2' => __('label.CR_GRADING_SENTENCE_3', ['mks' => $overAllMks, 'He' => $pronounList['@He']]),
            ];
            $sentenceArr[$gradingTraitId] = [
                $gradingSentence['0'] => $gradingSentence['0'],
                $gradingSentence['1'] => $gradingSentence['1'],
                $gradingSentence['2'] => $gradingSentence['2'],
            ];

            //prev report data 
            //save report data for CM
            $prevCrGen = CrGeneration::where('course_id', $request->course_id)->where('cm_id', $request->cm_id)
                    ->select('id', 'report_data', 'report_file')
                    ->first();

            $reportDataArr = [];
            if (!empty($prevCrGen)) {
                $reportDataArr['sentence'] = !empty($prevCrGen->report_data) ? json_decode($prevCrGen->report_data, true) : [];
                $reportDataArr['file'] = !empty($prevCrGen->report_file) ? $prevCrGen->report_file : '';
                $reportDataArr['id'] = !empty($prevCrGen->id) ? $prevCrGen->id : 0;
            }
        }

        return view('crSetup.generation.index')->with(compact('trainingYearList', 'courseList', 'activeCourse', 'cmArr'
                                , 'sentenceArr', 'traitList', 'sentenceList', 'sentenceArr', 'perfomance', 'cm'
                                , 'reportDataArr', 'gradingTraitId', 'assessmentActDeact'));
    }

    public function getCourse(Request $request) {

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;

        $html = view('crSetup.generation.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getCm(Request $request) {

        $assessmentActDeact = AssessmentActDeact::where('course_id', $request->course_id)
                ->where('status', '1')->where('criteria', '4')
                ->count();


        //CM list of the DS
        $cmArr = ['0' => __('label.SELECT_CM_OPT')] + CrGrouping::join('cm_basic_profile', 'cm_basic_profile.id', '=', 'cr_grouping.cm_id')
                        ->leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                        ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                        ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name"), 'cm_basic_profile.id')
                        ->orderBy('wing.order', 'asc')
                        ->orderBy('rank.order', 'asc')
                        ->orderBy('cm_basic_profile.personal_no', 'asc')
                        ->where('cr_grouping.course_id', $request->course_id)
                        ->where('cr_grouping.ds_id', Auth::user()->id)
                        ->where('cm_basic_profile.status', '1')
                        ->pluck('cm_name', 'cm_basic_profile.id')
                        ->toArray();
        $html = view('crSetup.generation.getCm', compact('cmArr', 'assessmentActDeact'))->render();
        $html1 = view('crSetup.generation.proceed', compact('assessmentActDeact'))->render();

        return Response::json(['html' => $html, 'html1' => $html1]);
    }

    public function saveSentences(Request $request) {
        $traitList = CrTrait::where('status', '1')->orderBy('order', 'asc')->pluck('title', 'id')->toArray();

        //start :: general validation
        $rules = $message = $errMessage = [];
        $rules = [
            'course_id' => 'required|not_in:0',
            'cm_id' => 'required|not_in:0',
        ];

        $sentenceArr = $request->sentence;

        if (!empty($sentenceArr)) {
            foreach ($sentenceArr as $traitId => $sentence) {
                $rules['sentence.' . $traitId] = 'required';
                $trait = !empty($traitId) && !empty($traitList[$traitId]) ? $traitList[$traitId] : '';
                $message['sentence.' . $traitId . '.required'] = __('label.PLEASE_CHOOSE_A_SENTENCE_FOR_TRAIT', ['trait' => $trait]);
            }
        }

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        //end :: general validation
        //start :: validation for empty sentence box
        //end :: validation for empty sentence box

        $perfomanceArr = json_decode($request->performance, true);
        $perfomanceReflArr = $perfomanceArr[0];
        $perfomanceReflSlabArr = $perfomanceArr[1];

        $traitInfo = CrTrait::select('para_id', 'title', 'id', 'for_recomnd_sentence', 'for_grading_sentence')
                        ->where('status', '1')->orderBy('order', 'asc')->get();



        $traitArr = $specialTraitArr = [];
        if (!$traitInfo->isEmpty()) {
            foreach ($traitInfo as $trt) {
                $traitArr[$trt->para_id][$trt->id]['title'] = $trt->title;

                if ($trt->for_recomnd_sentence == '1') {
                    $specialTraitArr['for_recomnd_sentence'] = $trt->id;
                } elseif ($trt->for_grading_sentence == '1') {
                    $specialTraitArr['for_grading_sentence'] = $trt->id;
                }

                if ($trt->para_id == 3) {
                    $traitArr[$trt->para_id][$trt->id]['mks'] = $perfomanceReflArr[$trt->id] ?? 0;
                    $traitArr[$trt->para_id][$trt->id]['slab'] = $perfomanceReflSlabArr[$trt->id] ?? 0;
                    $traitArr[$trt->para_id][$trt->id]['for_recomnd_sentence'] = $trt->for_recomnd_sentence;
                }
            }
        }

        $cm = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->where('cm_basic_profile.id', $request->cm_id)
                ->select('cm_basic_profile.official_name', 'cm_basic_profile.personal_no', 'rank.name as rank'
                        , 'cm_basic_profile.full_name', 'wing.name as wing', 'cm_basic_profile.gender')
                ->first();
        $course = Course::where('id', $request->course_id)->select('name')->first();

        $signAuthorityInfo = User::leftJoin('rank', 'rank.id', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', 'users.wing_id')
                ->leftJoin('appointment', 'appointment.id', 'users.appointment_id')
                ->whereIn('users.group_id', [2, 3])
                ->where('users.status', '1')
                ->select('appointment.name as appt', 'rank.name as rank', 'users.group_id'
                        , 'users.full_name', 'users.id')
                ->get();
        $signAuthorityArr = [];
        if (!$signAuthorityInfo->isEmpty()) {
            foreach ($signAuthorityInfo as $sign) {
                $signAuthorityArr[$sign->group_id]['name'] = !empty($sign->full_name) ? Common::getFullNameWithoutDecoration($sign->full_name) : '';
                $signAuthorityArr[$sign->group_id]['rank'] = $sign->rank;
                $signAuthorityArr[$sign->group_id]['appt'] = $sign->appt;
            }
        }

        $bPlusMarkingSlabList = CrMarkingSlab::where('b_plus_n_above', '1')->pluck('id', 'id')->toArray();

        $gradingTraitId = !empty($specialTraitArr['for_grading_sentence']) ? $specialTraitArr['for_grading_sentence'] : 0;

        $overAllMks = !empty($perfomanceReflArr[$gradingTraitId]) ? Helper::numberFormat2Digit($perfomanceReflArr[$gradingTraitId]) : '0.000';

        $sentences = !empty($request->sentence) ? json_encode($request->sentence) : '';

        //save report data for CM
        $prevCrGen = CrGeneration::where('course_id', $request->course_id)
                ->where('cm_id', $request->cm_id)->select('id', 'report_file')
                ->first();
        $target = !empty($prevCrGen->id) ? CrGeneration::find($prevCrGen->id) : new CrGeneration;
        $target->course_id = $request->course_id;
        $target->cm_id = $request->cm_id;
        $target->overall_mks = !empty($overAllMks) ? $overAllMks : 0;
        $target->report_data = $sentences;
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;

        if ($target->save()) {
            $html = view('crSetup.generation.getFinalDoc', compact('request', 'traitArr', 'cm', 'course'
                            , 'signAuthorityArr', 'prevCrGen', 'specialTraitArr', 'bPlusMarkingSlabList'))->render();
            return Response::json(['success' => true, 'message' => __('label.SENTENCES_SAVED_SUCCESSFULLY')
                        , 'html' => $html], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FALIED_TO_SAVE_SENTENCES')), 401);
        }
    }

    public function generateDoc(Request $request) {
        //start :: general validation
        $rules = $message = $errMessage = [];
        $rules = [
            'course_id' => 'required|not_in:0',
            'cm_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        //end :: general validation

        $perfomanceArr = json_decode($request->performance, true);
        $perfomanceReflArr = $perfomanceArr[0];
        $perfomanceReflSlabArr = $perfomanceArr[1];

        $traitInfo = CrTrait::select('para_id', 'title', 'id', 'for_recomnd_sentence', 'for_grading_sentence')
                        ->where('status', '1')->orderBy('order', 'asc')->get();

        $traitArr = $specialTraitArr = [];
        if (!$traitInfo->isEmpty()) {
            foreach ($traitInfo as $trt) {
                $traitArr[$trt->para_id][$trt->id]['title'] = $trt->title;

                if ($trt->for_recomnd_sentence == '1') {
                    $specialTraitArr['for_recomnd_sentence'] = $trt->id;
                } elseif ($trt->for_grading_sentence == '1') {
                    $specialTraitArr['for_grading_sentence'] = $trt->id;
                }

                if ($trt->para_id == 3) {
                    $traitArr[$trt->para_id][$trt->id]['mks'] = $perfomanceReflArr[$trt->id] ?? 0;
                    $traitArr[$trt->para_id][$trt->id]['slab'] = $perfomanceReflSlabArr[$trt->id] ?? 0;
                    $traitArr[$trt->para_id][$trt->id]['for_recomnd_sentence'] = $trt->for_recomnd_sentence;
                }
            }
        }

        $cm = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->where('cm_basic_profile.id', $request->cm_id)
                ->select('cm_basic_profile.official_name', 'cm_basic_profile.personal_no', 'rank.name as rank'
                        , 'cm_basic_profile.full_name', 'wing.name as wing', 'cm_basic_profile.gender')
                ->first();

        $course = Course::where('id', $request->course_id)->select('name')->first();

        $signAuthorityInfo = User::leftJoin('rank', 'rank.id', 'users.rank_id')
                ->leftJoin('wing', 'wing.id', 'users.wing_id')
                ->leftJoin('appointment', 'appointment.id', 'users.appointment_id')
                ->whereIn('users.group_id', [2, 3])
                ->where('users.status', '1')
                ->select('appointment.name as appt', 'rank.name as rank', 'users.group_id'
                        , 'users.full_name', 'users.id')
                ->get();
        $signAuthorityArr = [];
        if (!$signAuthorityInfo->isEmpty()) {
            foreach ($signAuthorityInfo as $sign) {
                $signAuthorityArr[$sign->group_id]['name'] = !empty($sign->full_name) ? Common::getFullNameWithoutDecoration($sign->full_name) : '';
                $signAuthorityArr[$sign->group_id]['rank'] = $sign->rank;
                $signAuthorityArr[$sign->group_id]['appt'] = $sign->appt;
            }
        }

        $bPlusMarkingSlabList = CrMarkingSlab::where('b_plus_n_above', '1')->pluck('id', 'id')->toArray();

        $gradingTraitId = !empty($specialTraitArr['for_grading_sentence']) ? $specialTraitArr['for_grading_sentence'] : 0;

        $overAllMks = !empty($perfomanceReflArr[$gradingTraitId]) ? Helper::numberFormat2Digit($perfomanceReflArr[$gradingTraitId]) : '0.000';


        //save report data for CM
        $prevCrGen = CrGeneration::where('course_id', $request->course_id)->where('cm_id', $request->cm_id)
                ->select('id', 'report_data', 'report_file')
                ->first();

        $reportDataArr = [];
        if (!empty($prevCrGen)) {
            $reportDataArr['sentence'] = !empty($prevCrGen->report_data) ? json_decode($prevCrGen->report_data, true) : [];
            $reportDataArr['file'] = !empty($prevCrGen->report_file) ? $prevCrGen->report_file : '';
            $reportDataArr['id'] = !empty($prevCrGen->id) ? $prevCrGen->id : 0;
        }


        //start :: word file generation
        $phpWord = new \PhpOffice\PhpWord\PhpWord();


        $phpWord->addFontStyle('r2Style', array('name' => 'Arial', 'bold' => true, 'italic' => false, 'size' => 11, 'underline' => 'single'));
        $phpWord->addParagraphStyle('p2Style', array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));

        $phpWord->addFontStyle('rHF1Style', array('name' => 'Arial', 'bold' => false, 'italic' => false, 'size' => 11, 'allCaps' => true));
        $phpWord->addFontStyle('r2HF1Style', array('name' => 'Arial', 'bold' => false, 'italic' => false, 'size' => 6, 'allCaps' => true));
        $phpWord->addFontStyle('r3HF1Style', array('name' => 'Arial', 'bold' => false, 'italic' => false, 'size' => 12, 'allCaps' => true));
        $phpWord->addParagraphStyle('pHF1Style', array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
        $phpWord->addParagraphStyle('pHFConfStyle', array('spaceBefore' => 0, 'spaceAfter' => 0
            , 'indentation' => array('firstLine' => 3693)));

        $phpWord->addFontStyle('rHF2Style', array('name' => 'Arial', 'bold' => true, 'italic' => false, 'size' => 12, 'allCaps' => true, 'underline' => 'single'));
        $phpWord->addParagraphStyle('pHF2Style', array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
        $phpWord->addParagraphStyle('pHFNDCStyle', array('spaceBefore' => 0, 'spaceAfter' => 0
            , 'indentation' => array('firstLine' => 2698)));
        $phpWord->addParagraphStyle('pHFBDStyle', array('spaceBefore' => 0, 'spaceAfter' => 0
            , 'indentation' => array('firstLine' => 3750)));
        $phpWord->addParagraphStyle('pHFCRStyle', array('spaceBefore' => 0, 'spaceAfter' => 0
            , 'indentation' => array('firstLine' => 2344)));

        $phpWord->addFontStyle('r1p1Style', array('name' => 'Arial', 'bold' => false, 'italic' => false, 'size' => 11));
        $phpWord->addParagraphStyle('r2p2Style', array('align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0));

        $phpWord->addParagraphStyle('r2p3Style', array('align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0));

        $imageStyle = array(
            'width' => 40,
            'height' => 40,
            'wrappingStyle' => 'square',
            'positioning' => 'absolute',
            'posHorizontal' => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_CENTER,
            'posHorizontalRel' => 'margin',
            'posVerticalRel' => 'line',
            'marginBottom' => 600,
        );

        $imageStyle2 = array(
            'width' => 130,
            'height' => 130,
            'wrappingStyle' => 'square',
            'positioning' => 'absolute',
            'posHorizontal' => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_CENTER,
            'posHorizontalRel' => 'margin',
            'posVerticalRel' => 'line',
            'wrapDistanceBottom' => 100,
        );

        $section = $phpWord->addSection(
                array('marginLeft' => 1420, 'marginRight' => 682,
                    'marginTop' => 508, 'marginBottom' => 508,
					'headerHeight' => 508, 'footerHeight' => 508)
        );
        // add header and footer

        $phpWord->addParagraphStyle('HerderFooterStyle', array('align' => 'center'));

        //header
        $header = $section->createHeader();
        $header->addText(__('label.IN_CONFIDENCE'), 'rHF1Style', 'pHFConfStyle');
        $header->addText('', 'rHF1Style', 'pHF1Style');
        $header->addText('', 'rHF1Style', 'pHF1Style');
        $header->addText('', 'rHF1Style', 'pHF1Style');
        $header->addText('', 'rHF1Style', 'pHF1Style');
        $header->addText('', 'rHF1Style', 'pHF1Style');
        $header->addText('', 'rHF1Style', 'pHF1Style');
        $header->addText(' ', 'r2HF1Style', 'pHF1Style');
        $header->addText(' ', 'r2HF1Style', 'pHF1Style');

        $header->addText(__('label.NATIONAL_DEFENCE_COLLEGE'), 'rHF2Style', 'pHFNDCStyle');
        $header->addText(__('label.BANGLADESH'), 'rHF2Style', 'pHFBDStyle');
        $header->addText('', 'rHF1Style', 'pHF1Style');
        $header->addText(__('label.MEMBERS_PERFORMANCE_REPORT'), 'rHF2Style', 'pHFCRStyle');
        $header->addText('', 'rHF1Style', 'pHF1Style');
        $header->addText('', 'rHF1Style', 'pHF1Style');

        //footer
        $footer = $section->createFooter();
        $footer->addText('', 'rHF1Style', 'pHF1Style');
        $footer->addText('', 'rHF1Style', 'pHF1Style');
        $footer->addText('', 'rHF1Style', 'pHF1Style');
        $footer->addText('', 'rHF1Style', 'pHF1Style');
        $footer->addText('', 'rHF1Style', 'pHF1Style');

        $ciName = ($signAuthorityArr[3]['name'] ?? '');
        $ciRank = $signAuthorityArr[3]['rank'] ?? '';
        $ciAppt = $signAuthorityArr[3]['appt'] ?? '';
        $ciText = '<w:r><w:rPr><w:b/></w:rPr><w:rPr><w:sz w:val="22"/></w:rPr><w:t>' . $ciName . '</w:t></w:r><w:r><w:rPr><w:sz w:val="22"/></w:rPr><w:t> <w:br/>' . $ciRank . '<w:br/>' . $ciAppt . '</w:t></w:r>';

        $comdtName = $signAuthorityArr[2]['name'] ?? '';
        $comdtRank = $signAuthorityArr[2]['rank'] ?? '';
        $comdtAppt = $signAuthorityArr[2]['appt'] ?? '';

        $comdtText = '<w:r><w:rPr><w:b/></w:rPr><w:rPr><w:sz w:val="22"/></w:rPr><w:t>' . $comdtName . '</w:t></w:r><w:r><w:rPr><w:sz w:val="22"/></w:rPr><w:t> <w:br/>' . $comdtRank . '<w:br/>' . $comdtAppt . '</w:t></w:r>';

        $dateText = __('label.DATE') . ':         ' . date("F Y");

        $styleTable = array('borderSize' => 1, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $styleCell = array('valign' => 'center');
        //, 'allCaps' => true
        $fontStyle = array('name' => 'Arial', 'bold' => false, 'align' => 'left', 'size' => 11, 'spaceBefore' => 0, 'spaceAfter' => 0);
        $fontStyle1 = array('name' => 'Arial', 'bold' => false, 'allCaps' => true, 'align' => 'left', 'size' => 11, 'spaceBefore' => 0, 'spaceAfter' => 0);
        $fontStyle2 = array('name' => 'Arial', 'bold' => true, 'align' => 'left', 'size' => 11, 'spaceBefore' => 0, 'spaceAfter' => 0);
        $phpWord->addTableStyle('Signatory Table', $styleTable);
        $table = $footer->addTable('Signatory Table');
        $table->addRow(50);
        $table->addCell(4000, $styleCell)->addText($ciText, $fontStyle1);
        $table->addCell(6000, $styleCell)->addText('', $fontStyle);
        $table->addCell(4000, $styleCell)->addText($comdtText, $fontStyle1);
        $table->addRow(50);
        $table->addCell(4000, $styleCell)->addText($dateText, $fontStyle);
        $table->addCell(6000, $styleCell)->addText('', $fontStyle);
        $table->addCell(4000, $styleCell)->addText($dateText, $fontStyle);

        $footer->addText('', 'r3HF1Style', 'pHF1Style');
        $footer->addText(__('label.IN_CONFIDENCE'), 'rHF1Style', 'pHFConfStyle');

        //first table
        $tableText1 = '  ' . ($cm->personal_no ?? '');
        $tableText2 = '  ' . ($cm->rank ?? '');
        $tableText3 = !empty($cm->full_name) ? strip_tags($cm->full_name) : '';
        $tableText3 = '  ' . (str_replace(['&nbsp;'], ' ', $tableText3));
        $tableText4 = '  ' . __('label.SERVICE') . ': ' . ($cm->wing ?? '');
        $tableText5 = '  ' . __('label.COURSE') . ': ' . ($course->name ?? '');


        $styleTable = array('borderSize' => 1, 'cellMargin' => 0, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $styleCell = array('valign' => 'center');
        $styleCellColSpan2 = array('valign' => 'center', 'gridSpan' => 2);
        $styleCellColSpan3 = array('valign' => 'center', 'gridSpan' => 3);
        $styleCellColSpan5 = array('valign' => 'center', 'gridSpan' => 5);
        $fontStyle = array('name' => 'Arial', 'bold' => true, 'align' => 'left', 'size' => 11, 'spaceBefore' => 40, 'spaceAfter' => 0);
        $phpWord->addTableStyle('Fancy Table', $styleTable);
        $table = $section->addTable('Fancy Table');
        $table->addRow(50);
        $table->addCell(2000, $styleCell)->addText($tableText1, $fontStyle);
        $table->addCell(3500, $styleCellColSpan2)->addText($tableText2, $fontStyle);
        $table->addCell(8500, $styleCellColSpan5)->addText($tableText3, $fontStyle);
        $table->addRow(50);
        $table->addCell(5500, $styleCellColSpan3)->addText($tableText4, $fontStyle);
        $table->addCell(8500, $styleCellColSpan5)->addText($tableText5, $fontStyle);

        $section->addText('', 'rHF1Style', 'r2p3Style');


        $trtText1 = $trtText2 = $trtText3 = '';
        $recommend = 0;

        if (!empty($traitArr)) {
            foreach ($traitArr as $para => $info) {
                if ($para != 3) {
                    foreach ($info as $traitId => $trait) {
                        if ($para == 1) {
                            $trtText1 .= ' ' . $reportDataArr['sentence'][$traitId];
                        } elseif ($para == 2) {
                            $trtText2 .= ' ' . ($reportDataArr['sentence'][$traitId] ?? '');
                        }
                    }
                } else {
                    foreach ($info as $traitId => $trait) {
                        if (!empty($specialTraitArr['for_recomnd_sentence']) && $specialTraitArr['for_recomnd_sentence'] != $traitId) {
                            $trtText3 .= ' ' . ($reportDataArr['sentence'][$traitId] ?? '');
                        }
                    }
                    if (!empty($specialTraitArr['for_recomnd_sentence'])) {
                        if (!empty($info[$specialTraitArr['for_recomnd_sentence']]['slab']) && !empty($bPlusMarkingSlabList) && in_array($info[$specialTraitArr['for_recomnd_sentence']]['slab'], $bPlusMarkingSlabList)) {
                            $recommend = 1;
                        }
                    }
                }
            }
        }

        $phpWord->addFontStyle('trtFStyle', array('name' => 'Arial', 'bold' => false, 'italic' => false, 'size' => 11));
        $phpWord->addParagraphStyle('trtPStyle', array(
            'align' => 'both', 'spaceBefore' => 0, 'spaceAfter' => 0
            , 'indentation' => array('firstLine' => 540)
        ));
        $section->addText($trtText1, 'trtFStyle', 'trtPStyle');
        $section->addText('', 'rHF1Style', 'r2p3Style');
        $section->addText($trtText2, 'trtFStyle', 'trtPStyle');
        $section->addText('', 'rHF1Style', 'r2p3Style');
        $section->addText($trtText3, 'trtFStyle', 'trtPStyle');
        $section->addText('', 'rHF1Style', 'r2p3Style');

        if (!empty($recommend)) {
            $recommendText1 = '  ' . __('label.RECOMMENDATION_AS_DS_AFWC');
            $recommendText2 = '  ' . __('label.RECOMMENDED');
            $fontStyle2 = array('bold' => true, 'align' => 'center', 'size' => 11, 'spaceBefore' => 0, 'spaceAfter' => 0);
            $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
            $phpWord->addTableStyle('Recommendation Table', $styleTable);
            $table = $section->addTable('Recommendation Table');
            $table->addRow(50);
            $table->addCell(7500, $styleCellColSpan5)->addText($recommendText1, $fontStyle2, $cellHCentered);
            $table->addCell(6500, $styleCellColSpan3)->addText($recommendText2, $fontStyle2, $cellHCentered);
        }
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        $fileDir = 'public/CourseReportFiles/' . $course->name;
        $fileName = 'Course Report ' . $cm->personal_no . '.docx';
        $fileName = Common::getFileFormatedName($fileName);
        $filePath = public_path('CourseReportFiles/' . $course->name . '/' . $fileName);
        $filePath2 = URL::to('/' . $fileDir . '/' . $fileName);
        $filePath3 = $fileDir . '/' . $fileName;

        if (!file_exists($fileDir)) {
            mkdir($fileDir, 0777, true);
        }
        if (File::exists($filePath3)) {
            gc_collect_cycles();
            File::delete($filePath3);
        }

        $target = CrGeneration::find($prevCrGen->id);
        $target->report_file = $fileName;
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;



        $objWriter->save($filePath);
        if ($target->save()) {
            return Response::json(['success' => true, 'message' => __('label.COURSE_REPORT_GENERATED_SUCCESSFULLY')
                        , 'filePath' => $filePath2], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FALIED_TO_GENERATE_COURSE_REPORT')), 401);
        }

//            return Response::json(array('success' => false, 'message' => __('label.SOME_PROBLEM_OCCURED_DURING_DOC_FILE_GENERATION')), 401);
        //end :: word file generation
    }

    public function getUploadModifiedDoc(Request $request) {
        $cm = CmBasicProfile::leftJoin('rank', 'rank.id', 'cm_basic_profile.rank_id')
                ->leftJoin('wing', 'wing.id', 'cm_basic_profile.wing_id')
                ->where('cm_basic_profile.id', $request->cm_id)
                ->select(DB::raw("CONCAT(rank.code,' ',cm_basic_profile.official_name,' (',cm_basic_profile.personal_no,')') as cm_name")
                        , 'cm_basic_profile.personal_no')
                ->first();
        $course = Course::where('id', $request->course_id)->select('name')->first();

        $html = view('crSetup.generation.getUploadModifiedDoc', compact('request', 'cm', 'course'))->render();
        return response()->json(['html' => $html]);
    }

    public function setUploadModifiedDoc(Request $request) {
		//echo '<pre>';
		//print_r($rquest->all());
		//exit;
        //start :: general validation
        $rules = $message = [];
        $rules = [
            'course_id' => 'required|not_in:0',
            'cm_id' => 'required|not_in:0',
            'report_file' => 'max:2048|mimes:docx,pdf',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()), 400);
        }
        //end :: general validation
        $cmPn = $request->cm_pn;
        $courseName = $request->course_name;
		
		$prevCrGen = CrGeneration::where('course_id', $request->course_id)->where('cm_id', $request->cm_id)
                ->select('id', 'report_file')
                ->first();
		
		$prevFile = !empty($prevCrGen->report_file) ? $prevCrGen->report_file : '';
		//$prevFile = Common::getFileFormatedName($prevFile);

        if (!empty($request->report_file)) {
            $prevFileName = 'public/CourseReportFiles/' . $courseName . '/' . $prevFile;

            if (File::exists($prevFileName)) {
                File::delete($prevFileName);
            }
        }

        $file = $request->file('report_file');
        if (!empty($file)) {
            $fileName = "Course Report " . $cmPn . "." . $file->getClientOriginalExtension();
			
			$fileName = Common::getFileFormatedName($fileName);
            $file->move('public/CourseReportFiles/' . $courseName, $fileName);
        }

        //save report data for CM
        $prevCrGen = CrGeneration::where('course_id', $request->course_id)
                ->where('cm_id', $request->cm_id)->select('id', 'report_file')
                ->first();
        $target = !empty($prevCrGen->id) ? CrGeneration::find($prevCrGen->id) : new CrGeneration;
        $target->report_file = !empty($fileName) ? $fileName : '';
        $target->updated_at = date('Y-m-d H:i:s');
        $target->updated_by = Auth::user()->id;

        if ($target->save()) {
            return Response::json(['success' => true, 'message' => __('label.MODIFIED_DOC_UPLOADED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FALIED_TO_UPLOAD_MODIFIED_DOC')), 401);
        }
    }

    public function getTraitWiseRefl(Request $request) {
        $traitWiseReflArr = [];

        $markingReflInfo = CrMarkingReflection::join('cr_trait', 'cr_trait.id', 'cr_marking_reflection.trait_id')
                        ->where('cr_marking_reflection.course_id', $request->course_id)
                        ->select('cr_marking_reflection.trait_id', 'cr_marking_reflection.reflection_type'
                                , 'cr_marking_reflection.wt_reflection')
                        ->where('cr_trait.status', '1')->get();

        $markingReflArr = [];
        if (!$markingReflInfo->isEmpty()) {
            foreach ($markingReflInfo as $refl) {
                $markingReflArr[$refl->trait_id]['type'] = $refl->reflection_type;
                if ($refl->reflection_type == '2') {
                    $wtReflArr = !empty($refl->wt_reflection) ? json_decode($refl->wt_reflection, true) : [];
                    $markingReflArr[$refl->trait_id]['wt_refl'] = $wtReflArr;
                }
            }
        }

//        echo '<pre>';
//        print_r($markingReflInfo->toArray());
//        print_r($markingReflArr);
//        exit;
        // Get Assigned CI obsn wt
        $assignedObsnInfo = CriteriaWiseWt::select('ci_obsn_wt', 'comdt_obsn_wt')
                        ->where('course_id', $request->course_id)->first();
        $assignedDsObsnInfo = DsObsnMarkingLimit::select('term_id', 'mks_limit', 'obsn')
                        ->where('course_id', $request->course_id)->get();

        if (!$assignedDsObsnInfo->isEmpty()) {
            foreach ($assignedDsObsnInfo as $dsObsn) {
                $assignedDsObsnArr[$dsObsn->term_id]['mks_limit'] = $dsObsn->mks_limit;
                $assignedDsObsnArr[$dsObsn->term_id]['obsn'] = $dsObsn->obsn;
            }
        }


        //START:: Event Information
        $eventInfo = TermToEvent::join('event', 'event.id', '=', 'term_to_event.event_id')
                ->leftJoin('event_mks_wt', 'event_mks_wt.event_id', '=', 'term_to_event.event_id')
                ->where('term_to_event.course_id', $request->course_id)
                ->where('event.status', '1')
                ->select('event.event_code', 'event.id as event_id'
                        , 'event_mks_wt.wt', 'event.has_sub_event')
                ->get();


        if (!$eventInfo->isEmpty()) {
            foreach ($eventInfo as $ev) {
                if (empty($ev->has_sub_event)) {
                    $eventMksWtArr['mks_wt'][$ev->event_id][0][0][0]['wt'] = !empty($ev->wt) ? $ev->wt : 0;
                }
            }
        }
        //END:: Event Information
        //START:: Sub Event information
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
                ->where('sub_event.status', '1')
                ->select('sub_event.event_code as sub_event_code', 'sub_event.id as sub_event_id', 'sub_event_mks_wt.wt'
                        , 'event_to_sub_event.has_sub_sub_event'
                        , 'event_to_sub_event.event_id', 'event.event_code', 'event_to_sub_event.avg_marking')
                ->get();

        if (!$subEventInfo->isEmpty()) {
            foreach ($subEventInfo as $subEv) {
                $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['avg_marking'] = $subEv->avg_marking;
                if ($subEv->has_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subEv->event_id][$subEv->sub_event_id][0][0]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;
                } else {
                    if ($subEv->avg_marking == '1') {
                        $eventMksWtArr['avg_marking'][$subEv->event_id][$subEv->sub_event_id]['wt'] = !empty($subEv->wt) ? $subEv->wt : 0;
                    }
                }
            }
        }
        //END:: Sub Event information
        //START:: Sub Sub Event Information
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
                ->where('sub_sub_event.status', '1')
                ->select('sub_sub_event.event_code as sub_sub_event_code', 'sub_sub_event.id as sub_sub_event_id', 'sub_sub_event_mks_wt.wt'
                        , 'event_to_sub_sub_event.has_sub_sub_sub_event'
                        , 'event_to_sub_sub_event.event_id', 'event_to_sub_sub_event.sub_event_id'
                        , 'sub_event.event_code as sub_event_code', 'event.event_code', 'event_to_sub_event.avg_marking')
                ->get();


        if (!$subSubEventInfo->isEmpty()) {
            foreach ($subSubEventInfo as $subSubEv) {
                if ($subSubEv->has_sub_sub_sub_event == '0') {
                    $eventMksWtArr['mks_wt'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id][0]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                }

                if ($subSubEv->avg_marking == '1') {
                    $eventMksWtArr['avg_marking'][$subSubEv->event_id][$subSubEv->sub_event_id][$subSubEv->sub_sub_event_id]['wt'] = !empty($subSubEv->wt) ? $subSubEv->wt : 0;
                }
            }
        }
        //END:: Sub Sub Event Information
        //START:: Sub Sub Sub Event Information
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
                ->where('sub_sub_sub_event.status', '1')
                ->select('sub_sub_sub_event.event_code as sub_sub_sub_event_code', 'sub_sub_sub_event.id as sub_sub_sub_event_id', 'sub_sub_sub_event_mks_wt.mks_limit'
                        , 'sub_sub_sub_event_mks_wt.wt', 'event_to_sub_sub_sub_event.event_id'
                        , 'event_to_sub_sub_sub_event.sub_event_id', 'event_to_sub_sub_sub_event.sub_sub_event_id'
                        , 'sub_sub_event.event_code as sub_sub_event_code', 'sub_event.event_code as sub_event_code'
                        , 'event.event_code', 'event_to_sub_event.avg_marking')
                ->get();


        if (!$subSubSubEventInfo->isEmpty()) {
            foreach ($subSubSubEventInfo as $subSubSubEv) {
                $eventMksWtArr['mks_wt'][$subSubSubEv->event_id][$subSubSubEv->sub_event_id][$subSubSubEv->sub_sub_event_id][$subSubSubEv->sub_sub_sub_event_id]['wt'] = !empty($subSubSubEv->wt) ? $subSubSubEv->wt : 0;
            }
        }
        //END:: Sub Sub Sub Event Information
        //START:: Event Wise Mks
        $eventWiseMksInfo = EventAssessmentMarking::join('event_assessment_marking_lock', function($join) {
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
                ->whereNotNull('event_assessment_marking.mks')
                ->select('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id'
                        , 'event_assessment_marking.sub_sub_sub_event_id', DB::raw("AVG(event_assessment_marking.wt) as avg_wt")
                        , DB::raw("AVG(event_assessment_marking.mks) as avg_mks"))
                ->groupBy('event_assessment_marking.event_id', 'event_assessment_marking.sub_event_id', 'event_assessment_marking.sub_sub_event_id'
                        , 'event_assessment_marking.sub_sub_sub_event_id')
                ->get();

        $cmEventCountArr = [];
        if (!$eventWiseMksInfo->isEmpty()) {
            foreach ($eventWiseMksInfo as $eventMksInfo) {
                if (!empty($eventMksInfo->avg_mks)) {
                    $cmEventCountArr[$eventMksInfo->event_id][$eventMksInfo->sub_event_id] = !empty($cmEventCountArr[$eventMksInfo->event_id][$eventMksInfo->sub_event_id]) ? $cmEventCountArr[$eventMksInfo->event_id][$eventMksInfo->sub_event_id] : 0;
                    $cmEventCountArr[$eventMksInfo->event_id][$eventMksInfo->sub_event_id] += 1;
                }
                $eventWiseMksArr[$eventMksInfo->event_id][$eventMksInfo->sub_event_id][$eventMksInfo->sub_sub_event_id][$eventMksInfo->sub_sub_sub_event_id]['avg_wt'] = $eventMksInfo->avg_wt;
            }
        }
        //END:: Event Wise Mks
        //START:: CI Moderation Wise Mks 
        $ciModWiseMksInfo = CiModerationMarking::join('ci_moderation_marking_lock', function($join) {
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
                ->select('ci_moderation_marking.event_id', 'ci_moderation_marking.sub_event_id', 'ci_moderation_marking.sub_sub_event_id', 'ci_moderation_marking.sub_sub_sub_event_id'
                        , 'ci_moderation_marking.wt')
                ->get();

        if (!$ciModWiseMksInfo->isEmpty()) {
            foreach ($ciModWiseMksInfo as $ciMksInfo) {
                $eventWiseMksArr[$ciMksInfo->event_id][$ciMksInfo->sub_event_id][$ciMksInfo->sub_sub_event_id][$ciMksInfo->sub_sub_sub_event_id]['ci_wt'] = $ciMksInfo->wt;
            }
        }
        //END:: CI Moderation Wise Mks
        //START:: COMDT Moderation Wise Mks 
        $comdtModWiseMksInfo = ComdtModerationMarking::join('comdt_moderation_marking_lock', function($join) {
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
                ->select('comdt_moderation_marking.event_id', 'comdt_moderation_marking.sub_event_id', 'comdt_moderation_marking.sub_sub_event_id', 'comdt_moderation_marking.sub_sub_sub_event_id'
                        , 'comdt_moderation_marking.wt')
                ->get();
        if (!$comdtModWiseMksInfo->isEmpty()) {
            foreach ($comdtModWiseMksInfo as $comdtMksInfo) {
                $eventWiseMksArr[$comdtMksInfo->event_id][$comdtMksInfo->sub_event_id][$comdtMksInfo->sub_sub_event_id][$comdtMksInfo->sub_sub_sub_event_id]['comdt_wt'] = $comdtMksInfo->wt;
            }
        }
        //END:: COMDT Moderation Wise Mks
        //ds obsn marking info
        $dsObsnMksWtInfo = DsObsnMarking::join('ds_obsn_marking_lock', function($join) {
                    $join->on('ds_obsn_marking_lock.course_id', 'ds_obsn_marking.course_id');
                    $join->on('ds_obsn_marking_lock.term_id', 'ds_obsn_marking.term_id');
                    $join->on('ds_obsn_marking_lock.locked_by', 'ds_obsn_marking.updated_by');
                })
                ->where('ds_obsn_marking.course_id', $request->course_id)
                ->where('ds_obsn_marking.cm_id', $request->cm_id)
                ->select('ds_obsn_marking.term_id'
                        , DB::raw('AVG(ds_obsn_marking.obsn_wt) as obsn_wt')
                        , DB::raw('AVG(ds_obsn_marking.obsn_mks) as obsn_mks'))
                ->groupBy('ds_obsn_marking.term_id')
                ->get();
        $dsObsnMksWtArr = [];
        if (!$dsObsnMksWtInfo->isEmpty()) {
            foreach ($dsObsnMksWtInfo as $dsObsnInfo) {
                $dsObsnMksWtArr[$dsObsnInfo->term_id]['wt'] = $dsObsnInfo->obsn_wt;
                $dsObsnMksWtArr[$dsObsnInfo->term_id]['mks'] = $dsObsnInfo->obsn_mks;
            }
        }

        $traitWiseReflArr = [];

        $countArr = [];
        if (!empty($eventMksWtArr['mks_wt'])) {
            foreach ($eventMksWtArr['mks_wt'] as $eventId => $subEvInfo) {
                foreach ($subEvInfo as $subEventId => $subSubEvInfo) {
                    foreach ($subSubEvInfo as $subSubEventId => $subSubSubEvInfo) {
                        foreach ($subSubSubEvInfo as $subSubSubEventId => $info) {
                            $comdtWt = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['comdt_wt']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['comdt_wt'] : 0;
                            $ciWt = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['ci_wt']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['ci_wt'] : 0;
                            $eventAvgWt = !empty($eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['avg_wt']) ? $eventWiseMksArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['avg_wt'] : 0;

                            $TotalTermWt = !empty($comdtWt) ? $comdtWt : (!empty($ciWt) ? $ciWt : $eventAvgWt);
                            $assignedWt = !empty($info['wt']) ? $info['wt'] : 0;

                            //count average where avg marking is enabled
                            $totalCount = 0;
                            if (!empty($cmEventCountArr[$eventId][$subEventId])) {
                                if (!empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking']) && $eventMksWtArr['avg_marking'][$eventId][$subEventId]['avg_marking'] == '1') {
                                    $totalCount = $cmEventCountArr[$eventId][$subEventId];
                                    $subEventMksLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['mks_limit'] : 0;
                                    $subEventWtLimit = !empty($eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt']) ? $eventMksWtArr['avg_marking'][$eventId][$subEventId]['wt'] : 0;

                                    $mksLimit = !empty($subSubSubEventId) ? $subSubEventMksLimit : $subEventMksLimit;
                                    $wtLimit = !empty($subSubSubEventId) ? $subSubEventWtLimit : $subEventWtLimit;

                                    $unitMksLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks_limit'] : 0;
                                    $unitWtLimit = !empty($eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? $eventMksWtArr['mks_wt'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt'] : 0;

                                    if ($totalCount != 0) {
                                        $assignedWt = $subEventWtLimit / $totalCount;
                                        $TotalTermWt = ($TotalTermWt * $subEventWtLimit) / ($totalCount * $unitWtLimit);
                                    }
                                }
                            }

                            if (!empty($markingReflArr)) {
                                foreach ($markingReflArr as $traitId => $traitInfo) {
                                    $type = $traitInfo['type'];

                                    if ($type == '1') {
                                        //trait wise total assigned wt in events
                                        $eventMksWtArr['total_wt'][$traitId] = !empty($eventMksWtArr['total_wt'][$traitId]) ? $eventMksWtArr['total_wt'][$traitId] : 0;
                                        if (!empty($TotalTermWt)) {
                                            $eventMksWtArr['total_wt'][$traitId] += (!empty($assignedWt) ? $assignedWt : 0);
                                        }
                                        //trait wise total achieved wt in events
                                        $traitWiseReflArr[$traitId] = !empty($traitWiseReflArr[$traitId]) ? $traitWiseReflArr[$traitId] : 0;
                                        $traitWiseReflArr[$traitId] += $TotalTermWt;
                                    } elseif ($type == '2') {
                                        $refl = $traitInfo['wt_refl'];
                                        if (!empty($refl[2][$eventId][$subEventId][$subSubEventId][$subSubSubEventId])) {
                                            //trait wise total assigned wt in events
                                            $eventMksWtArr['total_wt'][$traitId] = !empty($eventMksWtArr['total_wt'][$traitId]) ? $eventMksWtArr['total_wt'][$traitId] : 0;
                                            if (!empty($TotalTermWt)) {
                                                $eventMksWtArr['total_wt'][$traitId] += (!empty($assignedWt) ? $assignedWt : 0);
                                            }
                                            //trait wise total achieved wt in events
                                            $traitWiseReflArr[$traitId] = !empty($traitWiseReflArr[$traitId]) ? $traitWiseReflArr[$traitId] : 0;
                                            $traitWiseReflArr[$traitId] += $TotalTermWt;
                                        }
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
            foreach ($dsObsnMksWtArr as $termId => $info) {
                if (!empty($markingReflArr)) {
                    foreach ($markingReflArr as $traitId => $traitInfo) {
                        $type = $traitInfo['type'];

                        if ($type == '1') {
                            //trait wise total assigned wt in events
                            $eventMksWtArr['total_wt'][$traitId] = !empty($eventMksWtArr['total_wt'][$traitId]) ? $eventMksWtArr['total_wt'][$traitId] : 0;
                            $eventMksWtArr['total_wt'][$traitId] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);

                            $dsObsnWt = 0;
                            if (!empty($assignedDsObsnArr[$termId]['mks_limit'])) {
                                $dsObsnWt = (($info['mks'] * $assignedDsObsnArr[$termId]['obsn']) / $assignedDsObsnArr[$termId]['mks_limit']);
                            }

                            //trait wise total achieved wt in events
                            $traitWiseReflArr[$traitId] = !empty($traitWiseReflArr[$traitId]) ? $traitWiseReflArr[$traitId] : 0;
                            $traitWiseReflArr[$traitId] += $dsObsnWt ?? 0;
                        } elseif ($type == '2') {
                            $refl = $traitInfo['wt_refl'];
                            if (!empty($refl[1][0][0][0][0])) {
                                //trait wise total assigned wt in events
                                $eventMksWtArr['total_wt'][$traitId] = !empty($eventMksWtArr['total_wt'][$traitId]) ? $eventMksWtArr['total_wt'][$traitId] : 0;
                                $eventMksWtArr['total_wt'][$traitId] += (!empty($assignedDsObsnArr[$termId]['obsn']) ? $assignedDsObsnArr[$termId]['obsn'] : 0);

                                $dsObsnWt = 0;
                                if (!empty($assignedDsObsnArr[$termId]['mks_limit'])) {
                                    $dsObsnWt = (($info['mks'] * $assignedDsObsnArr[$termId]['obsn']) / $assignedDsObsnArr[$termId]['mks_limit']);
                                }

                                //trait wise total achieved wt in events
                                $traitWiseReflArr[$traitId] = !empty($traitWiseReflArr[$traitId]) ? $traitWiseReflArr[$traitId] : 0;
                                $traitWiseReflArr[$traitId] += $dsObsnWt ?? 0;
                            }
                        }
                    }
                }
            }
        }


        //START:: CI Obsn Wise Mks 
        $ciObsnWiseMksInfo = CiObsnMarking::join('ci_obsn_marking_lock', 'ci_obsn_marking_lock.course_id', 'ci_obsn_marking.course_id')
                ->where('ci_obsn_marking.course_id', $request->course_id)
                ->where('ci_obsn_marking.cm_id', $request->cm_id)
                ->select('ci_obsn_marking.ci_obsn')
                ->first();

        if (!empty($ciObsnWiseMksInfo)) {
            $TotalTermWt = $ciObsnWiseMksInfo->ci_obsn;

            if (!empty($markingReflArr)) {
                foreach ($markingReflArr as $traitId => $traitInfo) {
                    $type = $traitInfo['type'];

                    if ($type == '1') {
                        //trait wise total assigned wt in events
                        $eventMksWtArr['total_wt'][$traitId] = !empty($eventMksWtArr['total_wt'][$traitId]) ? $eventMksWtArr['total_wt'][$traitId] : 0;
                        if (!empty($TotalTermWt)) {
                            $eventMksWtArr['total_wt'][$traitId] += (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : 0);
                        }
                        //trait wise total achieved wt in events
                        $traitWiseReflArr[$traitId] = !empty($traitWiseReflArr[$traitId]) ? $traitWiseReflArr[$traitId] : 0;
                        $traitWiseReflArr[$traitId] += $TotalTermWt;
                    }
                }
            }
        }
        //END:: CI Obsn Wise Mks
        //START:: COMDT Obsn Wise Mks 
        $comdtObsnWiseMksInfo = ComdtObsnMarking::join('comdt_obsn_marking_lock', 'comdt_obsn_marking_lock.course_id', 'comdt_obsn_marking.course_id')
                ->where('comdt_obsn_marking.course_id', $request->course_id)
                ->where('comdt_obsn_marking.cm_id', $request->cm_id)
                ->select('comdt_obsn_marking.comdt_obsn')
                ->first();

        if (!empty($comdtObsnWiseMksInfo)) {
            $TotalTermWt = $comdtObsnWiseMksInfo->comdt_obsn;

            if (!empty($markingReflArr)) {
                foreach ($markingReflArr as $traitId => $traitInfo) {
                    $type = $traitInfo['type'];

                    if ($type == '1') {
                        //trait wise total assigned wt in events
                        $eventMksWtArr['total_wt'][$traitId] = !empty($eventMksWtArr['total_wt'][$traitId]) ? $eventMksWtArr['total_wt'][$traitId] : 0;
                        if (!empty($TotalTermWt)) {
                            $eventMksWtArr['total_wt'][$traitId] += (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0);
                        }
                        //trait wise total achieved wt in events
                        $traitWiseReflArr[$traitId] = !empty($traitWiseReflArr[$traitId]) ? $traitWiseReflArr[$traitId] : 0;
                        $traitWiseReflArr[$traitId] += $TotalTermWt;
                    }
                }
            }
        }
        //END:: COMDT Obsn Wise Mks
        // Get marking slab
        $markingSlabInfo = CrMarkingSlab::select('id', 'start_range', 'end_range')->get();
        $markingSlabArr = [];
        if (!$markingSlabInfo->isEmpty()) {
            foreach ($markingSlabInfo as $grade) {
                $markingSlabArr[$grade->id]['id'] = $grade->id;
                $markingSlabArr[$grade->id]['start'] = $grade->start_range;
                $markingSlabArr[$grade->id]['end'] = $grade->end_range;
            }
        }

        $perfomanceReflArr = $perfomanceReflSlabArr = [];
        if (!empty($traitWiseReflArr)) {
            foreach ($traitWiseReflArr as $traitId => $totalAchievedWt) {
                $perfomanceReflArr[$traitId] = 0;
                if (!empty($eventMksWtArr['total_wt'][$traitId])) {
                    $perfomanceReflArr[$traitId] = ($totalAchievedWt / $eventMksWtArr['total_wt'][$traitId]) * 100;
                }
                $totalPercentage = $perfomanceReflArr[$traitId];

                if (!empty($markingSlabArr)) {
                    foreach ($markingSlabArr as $slabId => $slab) {
                        if ($totalPercentage == 100) {
                            $perfomanceReflSlabArr[$traitId] = $slabId;
                        } elseif ($slab['start'] <= $totalPercentage && $totalPercentage < $slab['end']) {
                            $perfomanceReflSlabArr[$traitId] = $slabId;
                        }
                    }
                }
            }
        }

        //Start :: get MUA traits position
        $muaPos = $this->getMutualAssessmentPosition($request);
        if (!empty($markingReflArr)) {
            foreach ($markingReflArr as $traitId => $traitInfo) {
                if ($traitInfo['type'] == '3') {
                    $perfomanceReflArr[$traitId] = $muaPos;
                    if (!empty($markingSlabArr)) {
                        foreach ($markingSlabArr as $slabId => $slab) {
                            if ($slab['start'] <= $muaPos && $muaPos <= $slab['end']) {
                                $perfomanceReflSlabArr[$traitId] = $slabId;
                            }
                        }
                    }
                }
            }
        }
        //End :: get MUA traits position

        ksort($perfomanceReflArr);
        ksort($perfomanceReflSlabArr);

        return [$perfomanceReflArr, $perfomanceReflSlabArr];
    }

    public function getMutualAssessmentPosition(Request $request) {
        $mutualAssessmentInfo = MutualAssessmentMarking::where('mutual_assessment_marking.course_id', $request->course_id)
                ->where('mutual_assessment_marking.factor_id', 5)
                ->select('mutual_assessment_marking.cm_id', 'mutual_assessment_marking.term_id'
                        , 'mutual_assessment_marking.event_id', 'mutual_assessment_marking.sub_event_id'
                        , 'mutual_assessment_marking.sub_sub_event_id', 'mutual_assessment_marking.sub_sub_sub_event_id'
                        , 'mutual_assessment_marking.syndicate_id', 'mutual_assessment_marking.sub_syndicate_id'
                        , DB::raw("AVG(mutual_assessment_marking.position) as avg_pos"))
                ->groupBy('mutual_assessment_marking.cm_id', 'mutual_assessment_marking.term_id'
                        , 'mutual_assessment_marking.event_id', 'mutual_assessment_marking.sub_event_id'
                        , 'mutual_assessment_marking.sub_sub_event_id', 'mutual_assessment_marking.sub_sub_sub_event_id'
                        , 'mutual_assessment_marking.syndicate_id', 'mutual_assessment_marking.sub_syndicate_id')
                ->get();
        $mutualAssessmentArr = [];
        if (!$mutualAssessmentInfo->isEmpty()) {
            foreach ($mutualAssessmentInfo as $mua) {
                $mutualAssessmentArr[$mua->cm_id]['total_pos'] = !empty($mutualAssessmentArr[$mua->cm_id]['total_pos']) ? $mutualAssessmentArr[$mua->cm_id]['total_pos'] : 0;
                $mutualAssessmentArr[$mua->cm_id]['total_pos'] += $mua->avg_pos;
                $mutualAssessmentArr[$mua->cm_id]['count'] = !empty($mutualAssessmentArr[$mua->cm_id]['count']) ? $mutualAssessmentArr[$mua->cm_id]['count'] : 0;
                $mutualAssessmentArr[$mua->cm_id]['count'] += 1;

                $mutualAssessmentArr[$mua->cm_id]['pos'] = $mutualAssessmentArr[$mua->cm_id]['total_pos'] / $mutualAssessmentArr[$mua->cm_id]['count'];
            }
        }

        if (!empty($mutualAssessmentArr)) {
            $mutualAssessmentArr = Common::getPosition($mutualAssessmentArr, 'pos', 'final_pos', 1);
        }

        return $mutualAssessmentArr[$request->cm_id]['final_pos'];
    }

    public function filter(Request $request) {
        $messages = [];
        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
            'cm_id' => 'required|not_in:0',
        ];
        $messages = [
            'training_year_id.not_in' => __('label.THE_TRAINING_YEAR_FIELD_IS_REQUIRED'),
            'course_id.not_in' => __('label.THE_COURSE_FIELD_IS_REQUIRED'),
            'cm_id.not_in' => __('label.PLEASE_CHOOSE_CM_FOR_COURSE_REPORT_GENERATION'),
        ];

        $url = 'training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id
                . '&cm_id=' . $request->cm_id;

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('crGeneration?generate=false&' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }
        return redirect('crGeneration?generate=true&' . $url);
    }

}
