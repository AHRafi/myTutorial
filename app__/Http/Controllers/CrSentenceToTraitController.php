<?php

namespace App\Http\Controllers;

use App\Course;
use App\CrSentenceToTrait;
use App\CrMarkingSlab;
use App\CrMarkingReflection;
use App\CrTrait;
use App\TermToSubSubEvent;
use App\TermToSubSubSubEvent;
use App\TrainingYear;
use App\Event;
use App\EventAssessmentMarking;
use App\Term;
use App\CrGeneration;
//use App\Marking;
use Auth;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;

class CrSentenceToTraitController extends Controller {

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

        $traitList = ['0' => __('label.SELECT_TRAIT_OPT')] + CrTrait::orderBy('order', 'asc')
                        ->where('for_grading_sentence', '0')
                        ->where('for_recomnd_sentence', '0')
                        ->where('status', '1')->pluck('title', 'id')->toArray();

        return view('crSetup.sentenceToTrait.index')->with(compact('trainingYearList', 'courseList'
                                , 'traitList'));
    }

    public function getCourse(Request $request) {

        $courseList = Course::where('training_year_id', $request->training_year_id)
                ->orderBy('training_year_id', 'desc')
                ->orderBy('id', 'desc')->pluck('name', 'id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;
        $html = view('crSetup.sentenceToTrait.getCourse', compact('courseList'))->render();
        return Response::json(['html' => $html]);
    }

    public function getMarkingSlab(Request $request) {
        $markingRefl = CrMarkingReflection::select('reflection_type as type')
                ->where('course_id', $request->course_id)
                ->where('trait_id', $request->trait_id)
                ->first();
        $markingSlabList = [];
        if (!empty($markingRefl->type)) {
            $markingSlabList = CrMarkingSlab::where('status', '1')->orderBy('order', 'asc');

            if (in_array($markingRefl->type, ['1', '2'])) {
                $markingSlabList = $markingSlabList->where('type', '1');
            } elseif (in_array($markingRefl->type, ['3'])) {
                $markingSlabList = $markingSlabList->where('type', '2');
            }

            $markingSlabList = $markingSlabList->pluck('title', 'id')->toArray();
        }
        //for prev stored sentences
        $prevSentenceData = CrSentenceToTrait::select('marking_slab_id', 'sentence')
                ->where('course_id', $request->course_id)
                ->where('trait_id', $request->trait_id)
                ->get();

        $prevSentenceArr = [];
        if (!$prevSentenceData->isEmpty()) {
            foreach ($prevSentenceData as $data) {
                $sentenceArr = !empty($data->sentence) ? json_decode($data->sentence, true) : [];
                if (!empty($sentenceArr)) {
                    $prevSentenceArr[$data->marking_slab_id] = $sentenceArr;
                }
            }
        }

        $prevCrGen = CrGeneration::select('id')
                ->where('course_id', $request->course_id)
                ->get();

        $html = view('crSetup.sentenceToTrait.getMarkingSlab', compact('request', 'markingSlabList'
                        , 'prevSentenceArr', 'markingRefl', 'prevCrGen'))->render();
        return response()->json(['html' => $html]);
    }

    public function addSentence(Request $request) {
        $markingSlabId = $request->key;
        $html = view('crSetup.sentenceToTrait.addSentence', compact('markingSlabId'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveSentenceToTrait(Request $request) {
        $markingSlabList = CrMarkingSlab::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('title', 'id')->toArray();
        $errMessage = [];
        $sentenceArr = $request->sentence;

        if (!empty($sentenceArr)) {
            foreach ($sentenceArr as $markingSlabId => $sentenceData) {
                $box = 0;
                $markingSlab = !empty($markingSlabId) && !empty($markingSlabList[$markingSlabId]) ? $markingSlabList[$markingSlabId] : '';

                if (count(array_filter($sentenceData)) == 0) {
                    //if no sentence is given to the slab
                    $errMessage[] = __('label.PLEASE_ENTER_ATLEAST_ONE_FACTOR_FOR_SLAB', ['slab' => $markingSlab]);
                } else {
                    foreach ($sentenceData as $key => $sentence) {
                        $box++;
                        if (empty($sentence)) {
                            //if sentence box is opened but no value given
                            $errMessage[] = __('label.PLEASE_ENTER_ATLEAST_ONE_FACTOR_FOR_SLAB_IN_BOX', ['slab' => $markingSlab, 'box' => $box]);
                        }
                    }
                }
            }
        }

        if (!empty($errMessage)) {
            return Response::json(array('success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $errMessage), 400);
        }

        $rules = [
            'course_id' => 'required|not_in:0',
            'trait_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }



        $data = [];
        $i = 0;
        if (!empty($sentenceArr)) {
            foreach ($sentenceArr as $markingSlabId => $sentenceData) {
                if (count(array_filter($sentenceData)) != 0) {
                    $sentence = json_encode($sentenceData);
                    $data[$i]['course_id'] = $request->course_id;
                    $data[$i]['trait_id'] = $request->trait_id;
                    $data[$i]['marking_slab_id'] = $markingSlabId;
                    $data[$i]['sentence'] = $sentence;
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_by'] = Auth::user()->id;
                    $i++;
                }
            }
        }


        CrSentenceToTrait::where('course_id', $request->course_id)
                ->where('trait_id', $request->trait_id)
                ->delete();

        if (CrSentenceToTrait::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FACTOR_TO_TRAIT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

    public function getCloneSentenceToTrait(Request $request) {

        $rules = [
            'training_year_id' => 'required|not_in:0',
            'course_id' => 'required|not_in:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['success' => false, 'heading' => __('label.VALIDATION_ERROR'), 'message' => $validator->errors()], 400);
        }

        $toClonecouseInfo = Course::where('id', $request->course_id ?? 0)->select('name')->first();

        $courseList = CrSentenceToTrait::join('course', 'course.id', '=', 'cr_sentence_to_trait.course_id')
                ->where('course.id', '<>', $request->course_id ?? 0)
                ->orderBy('course.training_year_id', 'desc')
                ->orderBy('course.id', 'desc')
                ->pluck('course.name', 'course.id')
                ->toArray();

        $courseList = ['0' => __('label.SELECT_COURSE_OPT')] + $courseList;

        $html = view('crSetup.sentenceToTrait.getCloneSentenceToTrait', compact('request', 'courseList', 'toClonecouseInfo'))->render();
        return Response::json(['html' => $html]);
    }

    public function getTraitList(Request $request) {
        $traitList = $prevSentenceToTraitArr = $courseInfoArr = $markingReflArr = [];
        if (!empty($request->related_course_id)) {
            $courseInfoArr = Course::pluck('name', 'id')->toArray();
            $prevSentenceToTraitArr = CrSentenceToTrait::where('course_id', $request->related_course_id ?? 0)->pluck('sentence', 'trait_id')->toArray();
            $markingReflArr = CrMarkingReflection::where('course_id', $request->selected_course_id ?? 0)->pluck('reflection_type', 'trait_id')->toArray();

            $traitList = CrTrait::orderBy('order', 'asc')
                            ->where('for_grading_sentence', '0')
                            ->where('for_recomnd_sentence', '0')
                            ->where('status', '1')->pluck('title', 'id')->toArray();
        }


        $html = view('crSetup.sentenceToTrait.showCourseWiseTraitList', compact('request', 'prevSentenceToTraitArr', 'traitList', 'courseInfoArr', 'markingReflArr'))->render();
        return Response::json(['html' => $html]);
    }

    public function cloneSentenceToTrait(Request $request) {
        $previousDataArr = CrSentenceToTrait::where('course_id', $request->related_course_id)->select('*')->get();

        $data = [];
        $i = 0;
        DB::beginTransaction();
        try {
            if (!$previousDataArr->isEmpty()) {
                foreach ($previousDataArr as $item) {
                    $data[$i]['course_id'] = $request->selected_course_id;
                    $data[$i]['trait_id'] = $item['trait_id'];
                    $data[$i]['marking_slab_id'] = $item['marking_slab_id'];
                    $data[$i]['sentence'] = $item['sentence'];
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_by'] = Auth::user()->id;
                    $i++;
                }
            }

            CrSentenceToTrait::where('course_id', $request->selected_course_id)->delete();
			CrSentenceToTrait::insert($data);
            DB::commit();
            return Response::json(['success' => true], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::json(array('success' => false, 'message' => __('label.SENTENCE_TO_TRAIT_COULD_NOT_BE_CLONED')), 401);
        }
    }

}
