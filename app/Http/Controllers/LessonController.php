<?php

namespace App\Http\Controllers;

use App\Lesson;
use App\GsModule;
use App\GsGrading;
use App\Objective;
use App\Comment;
use App\Considerations;
use Session;
use Redirect;
use Helper;
use Validator;
use Response;
use App;
use View;
use DB;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;

class LessonController extends Controller
{

    private $controller = 'Lesson';

    public function index(Request $request)
    {
        $gsModuleList = GsModule::pluck('name', 'id')->toArray();
        $nameArr = Lesson::select('title')->orderBy('order', 'asc')->get();
        $qpArr = $request->all();
        $targetArr = Lesson::select(
            'lesson.id',
            'lesson.title',
            'lesson.eval_date',
            'lesson.eval_deadline',
            'lesson.consider_gs_feedback',
            'lesson.order',
            'lesson.status',
            'lesson.related_consideration',
            'lesson.related_grading',
            'lesson.related_comment'
        )
            ->orderBy('lesson.order', 'asc');
        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('title', 'LIKE', '%' . $searchText . '%');
            });
        }
        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));




        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/lesson?page=' . $page);
        }


        return view('lesson.index')->with(compact('qpArr', 'targetArr', 'nameArr', 'gsModuleList'));
    }

    public function create(Request $request)
    {
        //passing param for custom function
        $qpArr = $request->all();
        $moduleList = ['0' => __('label.SELECT_GS_MODULE_OPT')] + GsModule::pluck('name', 'id')->toArray();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('lesson.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'moduleList'));
    }

    public function store(Request $request)
    {
        //begin back same page after update
        $qpArr = $request->all();

        // echo "<pre>";
        // print_r($qpArr);
        // exit;


        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update


        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:lesson,title',
            'eval_date' => 'required',
            'eval_deadline' => 'required',
            'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('lesson/create' . $pageNumber)
                ->withInput()
                ->withErrors($validator);
        }


        $activeGradingInfo = GsGrading::where('status', '1')->pluck('id', 'id')->toArray();
        $activeCommentInfo = Comment::where('status', '1')->pluck('id', 'id')->toArray();
        $activeConsiderationInfo = Considerations::where('status', '1')->pluck('id', 'id')->toArray();



        $activeGradingArr = [];
        if (!empty($activeGradingInfo)) {
            foreach ($activeGradingInfo as $gradingId => $gradingId) {
                $activeGradingArr[] = $gradingId;
            }
        }

        $activeCommentArr = [];
        if (!empty($activeCommentInfo)) {
            foreach ($activeCommentInfo as $commentId => $commentId) {
                $activeCommentArr[] = $commentId;
            }
        }

        $activeConsiderationArr = [];
        if (!empty($activeConsiderationInfo)) {
            foreach ($activeConsiderationInfo as $ConsiderationId => $ConsiderationId) {
                $activeConsiderationArr[] = $ConsiderationId;
            }
        }

        $addGradingInfo = !empty($activeGradingArr) ? json_encode($activeGradingArr, true) : '';
        $addCommentInfo = !empty($activeCommentArr) ? json_encode($activeCommentArr, true) : '';
        $addConsiderationInfo = !empty($activeConsiderationArr) ?  json_encode($activeConsiderationArr, true) : '';

        // echo "<pre>";
        // print_r($addGradingInfo);
        // exit;






        $target = new Lesson;
        $target->title = $request->title;
        $target->eval_date = Helper::dateFormatConvert($request->eval_date);
        $target->eval_deadline = Helper::dateFormatConvert($request->eval_deadline);
        $target->consider_gs_feedback = !empty($request->consider_gs_feedback) ? $request->consider_gs_feedback : '0';
        $target->order = 0;
        $target->related_grading = $addGradingInfo;
        $target->related_comment = $addCommentInfo;
        $target->related_consideration = $addConsiderationInfo;
        $target->status = $request->status;

        if ($target->save()) {
            Helper::insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.LESSON_CREATED_SUCCESSFULLY'));
            return redirect('lesson');
        } else {
            Session::flash('error', __('label.LESSON_COULD_NOT_BE_CREATED'));
            return redirect('lesson/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id)
    {
        $target = Lesson::find($id);
        $gsModuleList = GsModule::pluck('name', 'id')->toArray();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('lesson');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('lesson.edit')->with(compact('target', 'qpArr', 'orderList', 'gsModuleList'));
    }

    public function update(Request $request, $id)
    {
        $target = Lesson::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:lesson,title,' . $id,
            'eval_date' => 'required',
            'eval_deadline' => 'required',
            'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('lesson/create' . $pageNumber)
                ->withInput()
                ->withErrors($validator);
        }

        $target->title = $request->title;
        $target->eval_date = Helper::dateFormatConvert($request->eval_date);
        $target->eval_deadline = Helper::dateFormatConvert($request->eval_deadline);
        $target->consider_gs_feedback = !empty($request->consider_gs_feedback) ? $request->consider_gs_feedback : '0';
        $target->order = $target->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper::updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.LESSON_UPDATED_SUCCESSFULLY'));
            return redirect('/lesson' . $pageNumber);
        } else {
            Session::flash('error', trans('label.LESSON_CUOLD_NOT_BE_UPDATED'));
            return redirect('lesson/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id)
    {
        $target = Lesson::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }


        if ($target->delete()) {
            Helper::deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.LESSON_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.LESSON_COULD_NOT_BE_DELETED'));
        }
        return redirect('lesson' . $pageNumber);
    }

    public function filter(Request $request)
    {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('lesson?' . $url);
    }

    public function manageLesson(Request $request, $id)
    {
        $gsModuleList = GsModule::pluck('name', 'id')->toArray();
        $target = Lesson::find($id);


        $prevRelatedConsideration = !empty($target->related_consideration) ? json_decode($target->related_consideration, true) : [];
        $prevRelatedGrading = !empty($target->related_grading) ? json_decode($target->related_grading, true) : [];
        $prevRelatedCmnt = !empty($target->related_comment) ? json_decode($target->related_comment, true) : [];


        $objectiveArr = Objective::orderBy('order', 'asc')->pluck('name', 'id')->toArray();
        $considerationArr = Considerations::orderBy('order', 'asc')->pluck('title', 'id')->toArray();
        $gradingArr = GsGrading::orderBy('order', 'asc')->select('title', 'id', 'wt')->get();
        $cmntArr = Comment::orderBy('order', 'asc')->pluck('title', 'id')->toArray();



        $targetInfo = Lesson::select('id', 'title', 'eval_date', 'eval_deadline', 'order', 'status')
            ->where('id', $id)->first();
        return view('lesson.manageLesson')->with(compact(
            'target',
            'targetInfo',
            'gsModuleList',
            'objectiveArr',
            'prevRelatedConsideration',
            'considerationArr',
            'prevRelatedGrading',
            'gradingArr',
            'cmntArr',
            'prevRelatedCmnt'
        ));
    }

    // public function saveObjective(Request $request) {

    //     $rules = [
    //         'objective' => 'required',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->fails()) {
    //         return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
    //     }

    //     $objectiveArr = $request->objective;
    //     //Prepare Buyer to Finished Goods as Array
    //     $lessonObjectiveArr = [];
    //     if (!empty($objectiveArr)) {
    //         foreach ($objectiveArr as $key => $objectiveId) {
    //             $lessonObjectiveArr[] = $objectiveId;
    //         }
    //     }
    //     $relatedObjectives['related_objective'] = json_encode($lessonObjectiveArr);

    //     $profileComplitionInfo = Lesson::select('related_objective', 'related_consideration', 'related_grading', 'related_comment')
    //                     ->whereNotNull('related_objective')
    //                     ->whereNotNull('related_consideration')
    //                     ->whereNotNull('related_grading')
    //                     ->whereNotNull('related_comment')
    //                     ->where('id', $request->lesson_id)->first();


    //     if (!empty($profileComplitionInfo)) {
    //         Lesson::where('id', $request->lesson_id)->update(['profile_complition_status' => 2]);
    //     }


    //     $updateObjectives = Lesson::where('id', $request->lesson_id)->update($relatedObjectives);
    //     if ($updateObjectives) {
    //         return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.OBJECTIVE_ASSIGNED_SUCCESSFULLY')], 200);
    //     } else {
    //         return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.OBJECTIVE_DID_NOT_ADDED_SUCCESSFULLY')], 401);
    //     }
    // }

    public function saveConsideration(Request $request)
    {

        $rules = [
            'consideration' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        $considerationArr = $request->consideration;


        $lessonConsiderationArr = [];
        if (!empty($considerationArr)) {
            foreach ($considerationArr as $considerationId => $considerationId) {
                $lessonConsiderationArr[] = $considerationId;
            }
        }



        $relatedConsideration['related_consideration'] = !empty($lessonConsiderationArr) ? json_encode($lessonConsiderationArr, true) : '';


        $profileComplitionInfo = Lesson::select('related_consideration', 'related_grading', 'related_comment')
            ->whereNotNull('related_consideration')
            ->whereNotNull('related_grading')
            ->whereNotNull('related_comment')
            ->where('id', $request->lesson_id)->first();

        if (!empty($profileComplitionInfo)) {
            $relatedConsideration['profile_complition_status'] = '2';
        }


        $updateConsideration = Lesson::where('id', $request->lesson_id)->update($relatedConsideration);
        if ($updateConsideration) {
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.CONSIDERATION_ASSIGNED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.CONSIDERATION_DID_NOT_ADDED_SUCCESSFULLY')], 401);
        }
    }

    public function saveGrading(Request $request)
    {
        //        print_r($request->all());
        //        exit;
        $rules = [
            'grading' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        $gradingArr = $request->grading;


        $lessonGradingArr = [];
        if (!empty($gradingArr)) {
            foreach ($gradingArr as $gradingId => $gradingId) {
                $lessonGradingArr[] = $gradingId;
            }
        }

        $relatedGrading['related_grading'] = !empty($lessonGradingArr) ? json_encode($lessonGradingArr,true) : '';

        $profileComplitionInfo = Lesson::select('related_consideration', 'related_grading', 'related_comment')
            ->whereNotNull('related_consideration')
            ->whereNotNull('related_grading')
            ->whereNotNull('related_comment')
            ->where('id', $request->lesson_id)->first();

        if (!empty($profileComplitionInfo)) {
            $relatedGrading['profile_complition_status'] = '2';
        }

        $updateGrading = Lesson::where('id', $request->lesson_id)->update($relatedGrading);
        if ($updateGrading) {
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.GRADING_ASSIGNED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.GRADING_DID_NOT_ADDED_SUCCESSFULLY')], 401);
        }
    }

    public function saveCmnt(Request $request)
    {

        $rules = [
            'cmnt' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array('success' => false, 'heading' => 'Validation Error', 'message' => $validator->errors()), 400);
        }

        $cmntArr = $request->cmnt;


        $lessonCmntArr = [];
        if (!empty($cmntArr)) {
            foreach ($cmntArr as $cmntId => $cmntId) {
                $lessonCmntArr[] = $cmntId;
            }
        }

        $relatedCmnt['related_comment'] = !empty($lessonCmntArr) ? json_encode($lessonCmntArr) : '';

        $profileComplitionInfo = Lesson::select('related_consideration', 'related_grading', 'related_comment')
            ->whereNotNull('related_consideration')
            ->whereNotNull('related_grading')
            ->whereNotNull('related_comment')
            ->where('id', $request->lesson_id)->first();

        if (empty($profileComplitionInfo)) {
            $relatedCmnt['profile_complition_status'] = '2';
        }

        $updateCmnt = Lesson::where('id', $request->lesson_id)->update($relatedCmnt);
        if ($updateCmnt) {
            return Response::json(['success' => true, 'heading' => __('label.SUCCESS'), 'message' => __('label.COMMENT_ASSIGNED_SUCCESSFULLY')], 200);
        } else {
            return Response::json(['success' => false, 'heading' => __('label.ERROR'), 'message' => __('label.COMMENT_DID_NOT_ADDED_SUCCESSFULLY')], 401);
        }
    }

    public function showProfileComplitionStatus(Request $request)
    {
        $id = $request->lesson_id ?? 0;

        $target = Lesson::select('id', 'related_consideration', 'related_grading', 'related_comment')->where('id', $id)->first();

        $targetInfo = Lesson::select('id', 'title', 'eval_date', 'eval_deadline', 'order', 'status')
            ->where('id', $id)->first();

        $gsModuleList = GsModule::pluck('name', 'id')->toArray();


        if ($target) {
            $html = view('lesson.showProfileStatus', compact('target', 'targetInfo', 'gsModuleList'))->render();
            return Response::json(['html' => $html]);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.INVALID_DATA_ID')), 401);
        }
    }
}
