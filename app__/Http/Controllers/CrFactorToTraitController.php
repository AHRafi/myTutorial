<?php

namespace App\Http\Controllers;

use App\Course;
use App\CrFactorToTrait;
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

class CrFactorToTraitController extends Controller {

    public function index(Request $request) {

        //get only active training year
        $activeTrainingYearInfo = TrainingYear::where('status', '1')->first();

        if (empty($activeTrainingYearInfo)) {
            $void['header'] = __('label.RELATE_FACTOR_TO_TRAIT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_TRAINING_YEAR');
            return view('layouts.void', compact('void'));
        }

        $activeCourse = Course::where('training_year_id', $activeTrainingYearInfo->id)
                        ->where('status', '1')->orderBy('id', 'desc')->select('name', 'id')->first();
        if (empty($activeCourse)) {
            $void['header'] = __('label.RELATE_FACTOR_TO_TRAIT');
            $void['body'] = __('label.THERE_IS_NO_ACTIVE_COURSE');
            return view('layouts.void', compact('void'));
        }

        $traitList = ['0' => __('label.SELECT_TRAIT_OPT')] + CrTrait::where('para_id', '<>', 3)
                        ->orderBy('order', 'asc')
                        ->where('status', '1')->pluck('title', 'id')->toArray();

        return view('crSetup.factorToTrait.index')->with(compact('activeTrainingYearInfo', 'activeCourse', 'traitList'));
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
        //for prev stored factors
        $prevFactorData = CrFactorToTrait::select('marking_slab_id', 'factor')
                ->where('course_id', $request->course_id)
                ->where('trait_id', $request->trait_id)
                ->get();

        $prevFactorArr = [];
        if (!$prevFactorData->isEmpty()) {
            foreach ($prevFactorData as $data) {
                $factorArr = !empty($data->factor) ? json_decode($data->factor, true) : [];
                if (!empty($factorArr)) {
                    $prevFactorArr[$data->marking_slab_id] = $factorArr;
                }
            }
        }

        $prevCrGen = CrGeneration::select('id')
                ->where('course_id', $request->course_id)
                ->get();

        $html = view('crSetup.factorToTrait.getMarkingSlab', compact('request', 'markingSlabList'
                        , 'prevFactorArr', 'markingRefl', 'prevCrGen'))->render();
        return response()->json(['html' => $html]);
    }

    public function addFactor(Request $request) {
        $markingSlabId = $request->key;
        $html = view('crSetup.factorToTrait.addFactor', compact('markingSlabId'))->render();
        return response()->json(['html' => $html]);
    }

    public function saveFactorToTrait(Request $request) {
        $markingSlabList = CrMarkingSlab::where('status', '1')->orderBy('order', 'asc')
                        ->pluck('title', 'id')->toArray();
        $errMessage = [];
        $factorArr = $request->factor;

        if (!empty($factorArr)) {
            foreach ($factorArr as $markingSlabId => $factorData) {
                $box = 0;
                $markingSlab = !empty($markingSlabId) && !empty($markingSlabList[$markingSlabId]) ? $markingSlabList[$markingSlabId] : '';

                if (count(array_filter($factorData)) == 0) {
                    //if no factor is given to the slab
                    $errMessage[] = __('label.PLEASE_ENTER_ATLEAST_ONE_FACTOR_FOR_SLAB', ['slab' => $markingSlab]);
                } else {
                    foreach ($factorData as $key => $factor) {
                        $box++;
                        if (empty($factor)) {
                            //if factor box is opened but no value given
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
        if (!empty($factorArr)) {
            foreach ($factorArr as $markingSlabId => $factorData) {
                if (count(array_filter($factorData)) != 0) {
                    $factor = json_encode($factorData);
                    $data[$i]['course_id'] = $request->course_id;
                    $data[$i]['trait_id'] = $request->trait_id;
                    $data[$i]['marking_slab_id'] = $markingSlabId;
                    $data[$i]['factor'] = $factor;
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_by'] = Auth::user()->id;
                    $i++;
                }
            }
        }


        CrFactorToTrait::where('course_id', $request->course_id)
                ->where('trait_id', $request->trait_id)
                ->delete();

        if (CrFactorToTrait::insert($data)) {
            return Response::json(['success' => true], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.FACTOR_TO_TRAIT_COULD_NOT_BE_ASSIGNED')), 401);
        }
    }

}
