<?php

namespace App\Http\Controllers;

use Validator;
use App\Rank;
use App\Service;
use App\Wing;
use Session;
use Redirect;
use Helper;
use Response;
use App;
use View;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;

class RankController extends Controller {

    private $controller = 'Rank';

    public function __construct() {

    }

    public function index(Request $request) {

        $nameArr = Rank::select('code')->orderBy('order', 'asc')->get();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('order', 'asc')->pluck('name', 'id')->toArray();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Rank::join('wing', 'wing.id', '=', 'rank.wing_id')
                ->select('rank.id', 'rank.name', 'rank.code', 'rank.order', 'rank.status', 'wing.code as wing'
                , 'rank.for_course_member')
                ->orderBy('rank.order', 'asc');
        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('rank.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('rank.code', 'LIKE', '%' . $searchText . '%');
            });
        }
        if (!empty($request->fil_wing_id)) {
            $targetArr = $targetArr->where('rank.wing_id', '=', $request->fil_wing_id);
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
            return redirect('/rank?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $rankCode = Rank::select('code')->where('id', Auth::user()->rank_id)->first();
            $pdf = PDF::loadView('rank.printRank', compact('targetArr', 'rankCode'))->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('rankList.pdf');
        } else {
            return view('rank.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'wingList'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        return view('rank.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber','wingList'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Name field is required.',
        );
        
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'code' => 'required',
                    'wing_id' => 'required|not_in:0',
                    'order' => 'required|not_in:0'
        ], $messages);


        if ($validator->fails()) {
            return redirect('rank/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Rank;
        $target->name = $request->name;
        $target->wing_id = $request->wing_id;
        $target->code = $request->code;
        $target->for_course_member = !empty($request->for_course_member) ? '1' : '0';
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.RANK_CREATED_SUCCESSFULLY'));
            return redirect('rank');
        } else {
            Session::flash('error', __('label.RANK_COULD_NOT_BE_CREATED'));
            return redirect('rank/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Rank::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('rank');
        }

        //passing param for custom function
        $qpArr = $request->all();
        $wingList = array('0' => __('label.SELECT_WING_OPT')) + Wing::orderBy('id', 'desc')->pluck('name', 'id')->toArray();

        return view('rank.edit')->with(compact('target', 'qpArr', 'orderList' , 'wingList'));
    }

    public function update(Request $request, $id) {
        $target = Rank::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Name field is required.',
        );
        
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'code' => 'required',
                    'order' => 'required|not_in:0'
        ], $messages);

        if ($validator->fails()) {
            return redirect('rank/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->wing_id = $request->wing_id;
        $target->code = $request->code;
        $target->for_course_member = !empty($request->for_course_member) ? '1' : '0';
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.RANK_UPDATED_SUCCESSFULLY'));
            return redirect('/rank' . $pageNumber);
        } else {
            Session::flash('error', trans('label.RANK_CUOLD_NOT_BE_UPDATED'));
            return redirect('rank/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Rank::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        
        //START:: Check Dependency before deletion
        $dependencyArr = [
            'User' => 'rank_id',
            'CmBasicProfile' => 'rank_id'];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('rank' . $pageNumber);
            }
        }
        //END:: Check Dependency before deletion

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.RANK_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.RANK_COULD_NOT_BE_DELETED'));
        }
        return redirect('rank' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fil_wing_id=' . $request->fil_wing_id;
        return Redirect::to('rank?' . $url);
    }

}
