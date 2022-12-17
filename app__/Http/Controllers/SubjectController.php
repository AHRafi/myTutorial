<?php

namespace App\Http\Controllers;

use Validator;
use App\Subject;
use App\Rank;
use Session;
use Redirect;
use Helper;
use App;
use View;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;

class SubjectController extends Controller
{

    private $controller = 'Subject';

    public function __construct()
    {
    }

    public function index(Request $request)
    {

        $nameArr = Subject::select('title')->orderBy('order', 'asc')->get();
        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Subject::select(
            'subject.id',
            'subject.title',
            'subject.code',
            'subject.gs_feedback',
            'subject.order',
            'subject.status'
        )
            ->orderBy('subject.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('subject.title', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/subject?page=' . $page);
        }

        return view('subject.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
    }

    public function create(Request $request)
    {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('subject.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request)
    {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update


        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:subject,title',
            'code' => 'required|unique:subject,code',
            'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('subject/create' . $pageNumber)
                ->withInput()
                ->withErrors($validator);
        }

        $target = new Subject;
        $target->title = $request->title;
        $target->code = $request->code;
        $target->order = $request->order ?? 0;
        $target->gs_feedback = $request->gs_feedback ?? '0';
        $target->status = $request->status;

        if ($target->save()) {
            Helper::insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.SUBJECT_CREATED_SUCCESSFULLY'));
            return redirect('subject');
        } else {
            Session::flash('error', __('label.SUBJECT_COULD_NOT_BE_CREATED'));
            return redirect('subject/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id)
    {
        $target = Subject::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('subject');
        }

        //passing param for custom function
        $qpArr = $request->all();
        return view('subject.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id)
    {
        $target = Subject::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:subject,title,' . $id,
            'code' => 'required|unique:subject,code,' . $id,
            'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('subject/' . $id . '/edit' . $pageNumber)
                ->withInput()
                ->withErrors($validator);
        }

        $target->title = $request->title;
        $target->code = $request->code;
        $target->order = $request->order;
        $target->gs_feedback = $request->gs_feedback ?? '0';
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper::updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', __('label.SUBJECT_UPDATED_SUCCESSFULLY'));
            return redirect('subject' . $pageNumber);
        } else {
            Session::flash('error', __('label.SUBJECT_COULD_NOT_BE_UPDATED'));
            return redirect('subject/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id)
    {
        $target = Subject::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '?page=';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        //Check Dependency before deletion
        $dependencyArr = [
            'SubjectToDs' => 'subject_id'
            , 'ModuleToSubject' => 'subject_id'
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            try {
                $dependentData = $namespacedModel::where($key, $id)->first();
                if (!empty($dependentData)) {
                    Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                    return redirect('subject' . $pageNumber);
                }
            } catch (\Throwable $th) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA'));
                // throw $th;
                return redirect('subject' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper::deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.SUBJECT_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.SUBJECT_COULD_NOT_BE_DELETED'));
        }
        return redirect('subject' . $pageNumber);
    }

    public function filter(Request $request)
    {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('subject?' . $url);
    }
}
