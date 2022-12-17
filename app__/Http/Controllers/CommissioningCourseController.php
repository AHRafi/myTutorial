<?php

namespace App\Http\Controllers;

use Helper;
use App;
use App\CommissioningCourse;
use App\TrainingYear;
use App\Wing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Redirect;
use Response;
use Session;
use Validator;
use View;

class CommissioningCourseController extends Controller {

    public function index(Request $request) {

        //passing param for custom function
        $qpArr = $request->all();
        $nameArr = CommissioningCourse::select('name')->orderBy('name', 'asc')->get();
        $wingList = ['0' => __('label.SELECT_WING_OPT')] + Wing::where('status', '1')
                        ->orderBy('order', 'asc')->pluck('code', 'id')->toArray();

        $targetArr = CommissioningCourse::join('wing', 'wing.id', '=', 'commissioning_course.wing_id')
                ->select('commissioning_course.id', 'commissioning_course.name', 'commissioning_course.short_info'
                        , 'commissioning_course.commissioning_date', 'commissioning_course.status', 'wing.code as wing')
                ->orderBy('commissioning_course.commissioning_date', 'asc')
                ->orderBy('wing.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('commissioning_course.name', 'LIKE', '%' . $searchText . '%');
            });
        }
        if (!empty($request->fill_wing_id)) {
            $targetArr = $targetArr->where('commissioning_course.wing_id', $request->fill_wing_id);
        }
        //end filtering
        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/commissioningCourse?page=' . $page);
        }

        return view('commissioningCourse.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'wingList'));
    }

    public function create(Request $request) { //passing param for custom function
        $qpArr = $request->all();
        $wingList = ['0' => __('label.SELECT_WING_OPT')] + Wing::where('status', '1')
                        ->orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        return view('commissioningCourse.create')->with(compact('qpArr', 'wingList'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => [
                        'required', Rule::unique('commissioning_course')
                                ->where('wing_id', $request->wing_id)
                    ],
                    'commissioning_date' => 'required',
                    'wing_id' => 'required|not_in:0',
        ]);

        if ($validator->fails()) {
            return redirect('commissioningCourse/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target = new CommissioningCourse;
        $target->name = $request->name;
        $target->short_info = $request->short_info;
        $target->wing_id = $request->wing_id;
        $target->commissioning_date = Helper::dateFormatConvert($request->commissioning_date);
        $target->status = $request->status;

        if ($target->save()) {
            Session::flash('success', __('label.COMMISSIONING_COURSE_CREATED_SUCCESSFULLY'));
            return redirect('commissioningCourse');
        } else {
            Session::flash('error', __('label.COMMISSIONING_COURSE_COULD_NOT_BE_CREATED'));
            return redirect('commissioningCourse/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = CommissioningCourse::find($id);
        $wingList = ['0' => __('label.SELECT_WING_OPT')] + Wing::where('status', '1')
                        ->orderBy('order', 'asc')->pluck('code', 'id')->toArray();
        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('commissioningCourse');
        }

        //passing param for custom function
        $qpArr = $request->all();
        return view('commissioningCourse.edit')->with(compact('target', 'qpArr', 'wingList'));
    }

    public function update(Request $request, $id) {
        $target = CommissioningCourse::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:commissioning_course,name,' . $id . ',id,wing_id,' . $request->wing_id,
                    'commissioning_date' => 'required',
                    'wing_id' => 'required|not_in:0',
        ]);

        if ($validator->fails()) {
            return redirect('commissioningCourse/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->short_info = $request->short_info;
        $target->wing_id = $request->wing_id;
        $target->commissioning_date = Helper::dateFormatConvert($request->commissioning_date);
        $target->status = $request->status;

        if ($target->save()) {
            Session::flash('success', __('label.COMMISSIONING_COURSE_UPDATED_SUCCESSFULLY'));
            return redirect('commissioningCourse' . $pageNumber);
        } else {
            Session::flash('error', __('label.COMMISSIONING_COURSE_COULD_NOT_BE_UPDATED'));
            return redirect('commissioningCourse/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = CommissioningCourse::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        //START:: Check Dependency before deletion
        $dependencyArr = ['CmBasicProfile' => 'commissioning_course_id'];


        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('commissioningCourse' . $pageNumber);
            }
        }
        //END:: Check Dependency before deletion

        if ($target->delete()) {
            Session::flash('error', __('label.COMMISSIONING_COURSE_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.COMMISSIONING_COURSE_COULD_NOT_BE_DELETED'));
        }
        return redirect('commissioningCourse' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fill_wing_id=' . $request->fill_wing_id;
        return Redirect::to('commissioningCourse?' . $url);
    }

    public function close(Request $request) {

        $target = CommissioningCourse::find($request->id);
        if (empty($target)) {
            return Response::json(array('success' => false, 'message' => __('label.INVALID_DATA_ID')), 401);
        }
        $target->status = '2';

        if ($target->save()) {
            return Response::json(['success' => true, 'message' => $target->name . ' ' . __('label.HAS_BEEN_CLOSED')], 200);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.BATCH_COULD_NOT_BE_CLOSED')), 401);
        }
    }

}
