<?php

namespace App\Http\Controllers;

use Validator;
use App\TrainingYear;
use App\Rank;
use App\Course;
use Redirect;
use Session;
use App;
use View;
use PDF;
use Auth;
use Response;
use Helper;
use Illuminate\Http\Request;

class TrainingYearController extends Controller {

    public function index(Request $request) {

        $nameArr = TrainingYear::select('year')->orderBy('year', 'desc')->get();

        //passing param for custom function
        $qpArr = $request->all();
        $targetArr = TrainingYear::select('training_year.id', 'training_year.name', 'training_year.year', 'training_year.start_date', 'training_year.end_date', 'training_year.status')
                ->orderBy('year', 'desc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('training_year.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('training_year.year', 'LIKE', '%' . $searchText . '%');
            });
        }
        //end filtering

        if ($request->download == 'pdf') {
            $targetArr = $targetArr->get();
        } else {
            $targetArr = $targetArr->paginate(Session::get('paginatorCount'));
        }

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/trainingYear?page=' . $page);
        }

        
        if ($request->download == 'pdf') {
            $rankCode = Rank::select('code')->where('id', Auth::user()->rank_id)->first();

            $pdf = PDF::loadView('trainingYear.printTrainingYear', compact('targetArr', 'rankCode'))->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('trainingYearList.pdf');
        } else {
            return view('trainingYear.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) { //passing param for custom function
        $qpArr = $request->all();
        return view('trainingYear.create')->with(compact('qpArr'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:training_year',
                    'year' => 'required|unique:training_year|min:4|max:4',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return redirect('trainingYear/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target = new TrainingYear;
        $target->name = $request->name;
        $target->year = $request->year;
        $target->start_date = Helper::dateFormatConvert($request->start_date);
        $target->end_date = Helper::dateFormatConvert($request->end_date);

        if ($target->save()) {
            Session::flash('success', __('label.TRAINING_YEAR_CREATED_SUCCESSFULLY'));
            return redirect('trainingYear');
        } else {
            Session::flash('error', __('label.TRAINING_YEAR_COULD_NOT_BE_CREATED'));
            return redirect('trainingYear/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = TrainingYear::find($id);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('trainingYear');
        }

        //passing param for custom function
        $qpArr = $request->all();
        return view('trainingYear.edit')->with(compact('target', 'qpArr'));
    }

    public function update(Request $request, $id) {
        $target = TrainingYear::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:training_year,name,' . $id,
                    'year' => 'required|min:4|max:4|unique:training_year,year,' . $id,
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return redirect('trainingYear/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->year = $request->year;
        $target->start_date = Helper::dateFormatConvert($request->start_date);
        $target->end_date = Helper::dateFormatConvert($request->end_date);

        if ($target->save()) {
            Session::flash('success', __('label.TRAINING_YEAR_UPDATED_SUCCESSFULLY'));
            return redirect('trainingYear' . $pageNumber);
        } else {
            Session::flash('error', __('label.TRAINING_YEAR_COULD_NOT_BE_UPDATED'));
            return redirect('trainingYear/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = TrainingYear::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        //Check Dependency before deletion
        $dependencyArr = ['Course' => 'training_year_id'];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('trainingYear' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Session::flash('error', __('label.TRAINING_YEAR_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.TRAINING_YEAR_COULD_NOT_BE_DELETED'));
        }
        return redirect('trainingYear' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('trainingYear?' . $url);
    }

    public function changeStatus(Request $request) {

        $trainingYearName = TrainingYear::select('name')->where('id', $request->id)->first();

        if ($request->status == 1) {
            $trainingYearInfo = TrainingYear::select('name')->where('status', '1')->first();
//            print_r($trainingYearInfo);exit;
            if (!empty($trainingYearInfo->name)) {
                return Response::json(array('success' => false, 'message' => $trainingYearInfo->name . ' ' . __('label.IS_ALREADY_ACTIVE')), 401);
            } else {
                $update = TrainingYear::where('id', $request->id)->update(['status' => $request->status]);
                if ($update) {
                    return Response::json(['success' => true, 'message' => $trainingYearName->name . ' ' . __('label.HAS_BEEN_ACTIVATED')], 200);
                } else {
                    return Response::json(array('success' => false, 'message' => __('label.COULD_NOT_BE_ACTIVATED')), 401);
                }
            }
        } else {
            $openCourse = Course::select('id')->where('status', '1')->where('training_year_id', $request->id)->count();
            if (!empty($openCourse)) {
                return Response::json(array('success' => false, 'message' => __('label.ALL_COURSES_ARE_NOT_CLOSED_OF_THIS_TRAINING_YEAR')), 401);
            }
            $update = TrainingYear::where('id', $request->id)->update(['status' => $request->status]);
            if ($update) {
                return Response::json(['success' => true, 'message' => $trainingYearName->name . ' ' . __('label.HAS_BEEN_CLOSED')], 200);
            } else {
                return Response::json(array('success' => false, 'message' => __('label.TRAINING_YEAR_COULD_NOT_BE_CLOSED')), 401);
            }
        }
    }

}
