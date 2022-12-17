<?php

namespace App\Http\Controllers;

use App\Objective;
use Session;
use Redirect;
use Helper;
use Illuminate\Support\Facades\Validator;
use Response;
use App;
use App\Lesson;
use View;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ObjectiveController extends Controller
{
    private $controller = 'Objective';

    public function index(Request $request)
    {
        $nameArr = Objective::select('name')->orderBy('order', 'asc')->get();
        $qpArr = $request->all();
        $targetArr = Objective::join('lesson', 'lesson.id', 'objective.lesson_id')
            ->select('objective.id', 'objective.name', 'objective.order', 'objective.status', 'lesson.title as lesson')
            ->orderBy('objective.order', 'asc');
        //begin filtering



        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('name', 'LIKE', '%' . $searchText . '%');
            });
        }

        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/gsmodule?page=' . $page);
        }


        return view('objective.index')->with(compact('qpArr', 'targetArr', 'nameArr'));
    }

    public function create(Request $request)
    {
        //passing param for custom function
        $qpArr = $request->all();
        $lessonArr = array('0' => __('label.SELECT_LESSON')) + Lesson::where('consider_gs_feedback', '1')->orderBy('order')->pluck('title', 'id')->toArray();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('objective.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'lessonArr'));
    }

    public function store(Request $request)
    {
        //begin back same page after update
        $qpArr = $request->all();


        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update


        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|not_in:0',
            'name' => ['required', Rule::unique('objective')
                ->where('lesson_id', $request->lesson_id)],
            'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('objective/create' . $pageNumber)
                ->withInput()
                ->withErrors($validator);
        }


        $target = new Objective;
        $target->name = $request->name;
        $target->lesson_id = $request->lesson_id;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper::insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.OBJECTIVE_CREATED_SUCCESSFULLY'));
            return redirect('objective');
        } else {
            Session::flash('error', __('label.OBJECTIVE_COULD_NOT_BE_CREATED'));
            return redirect('objective/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id)
    {
        $target = Objective::find($id);
        $lessonArr = array('0' => __('label.SELECT_LESSON')) + Lesson::where('consider_gs_feedback', '1')->orderBy('order')->pluck('title', 'id')->toArray();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('objective');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('objective.edit')->with(compact('target', 'qpArr', 'orderList', 'lessonArr'));
    }

    public function update(Request $request, $id)
    {
        $target = Objective::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update
        $lessonId = $request->lesson_id ?? 0;


        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|not_in:0',
            // 'name' => 'required|unique:objective,name,lesson_id', $id, 'id' ,$request->lesson_id,
            'name' => 'required|unique:objective,name,' . $id . ',id,lesson_id,' . $lessonId,
            'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('objective/' . $id . '/edit' . $pageNumber)
                ->withInput()
                ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->lesson_id = $request->lesson_id;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper::updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.OBJECTIVE_UPDATED_SUCCESSFULLY'));
            return redirect('/objective' . $pageNumber);
        } else {
            Session::flash('error', trans('label.OBJECTIVE_CUOLD_NOT_BE_UPDATED'));
            return redirect('objective/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id)
    {
        $target = Objective::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }


        if ($target->delete()) {
            Helper::deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.OBJECTIVE_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.  '));
        }
        return redirect('objective' . $pageNumber);
    }

    public function filter(Request $request)
    {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('objective?' . $url);
    }
}
