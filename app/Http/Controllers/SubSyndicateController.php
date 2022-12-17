<?php

namespace App\Http\Controllers;

use Validator;
use App\Syndicate;
use App\SubSyndicate;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Illuminate\Http\Request;

class SubSyndicateController extends Controller {

    private $controller = 'SubSyndicate';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = SubSyndicate::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = SubSyndicate::select('sub_syndicate.id', 'sub_syndicate.name', 'sub_syndicate.code', 'sub_syndicate.order', 'sub_syndicate.status')
                ->orderBy('sub_syndicate.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('sub_syndicate.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('sub_syndicate.code', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/subSyndicate?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $subSyndicateCode = SubSyndicate::select('code')->where('id', Auth::user()->sub_syndicate_id)->first();
            $pdf = PDF::loadView('subSyndicate.printSubSyndicate', compact('targetArr', 'subSyndicateCode'))->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('subSyndicateList.pdf');
        } else {
            return view('subSyndicate.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('subSyndicate.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:sub_syndicate',
                    'code' => 'required|unique:sub_syndicate',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('subSyndicate/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new SubSyndicate;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.SUB_SYN_CREATED_SUCCESSFULLY'));
            return redirect('subSyndicate');
        } else {
            Session::flash('error', __('label.SUB_SYN_COULD_NOT_BE_CREATED'));
            return redirect('subSyndicate/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = SubSyndicate::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('subSyndicate');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('subSyndicate.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = SubSyndicate::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:sub_syndicate,name,' . $id,
                    'code' => 'required|unique:sub_syndicate,code,' . $id,
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('subSyndicate/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.SUB_SYN_UPDATED_SUCCESSFULLY'));
            return redirect('/subSyndicate' . $pageNumber);
        } else {
            Session::flash('error', trans('label.SUB_SYN_CUOLD_NOT_BE_UPDATED'));
            return redirect('subSyndicate/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = SubSyndicate::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

//Check Dependency before deletion
        $dependencyArr = [
            'SubSynToCourse' => 'sub_syn_id'
//            , 'EventMarkingLock' => 'sub_syndicate_id', 'Marking' => 'sub_syndicate_id'
//            , 'ParticularMarkingLock' => 'sub_syndicate_id', 'SubSyndicateToBatch' => 'sub_syndicate_id', 'PlCmdrToSubSyndicate' => 'sub_syndicate_id'
//            , 'RctState' => 'sub_syndicate_id', 'RecruitToSubSyndicate' => 'sub_syndicate_id'
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('subSyndicate' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.SUB_SYN_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.SUB_SYN_COULD_NOT_BE_DELETED'));
        }
        return redirect('subSyndicate' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('subSyndicate?' . $url);
    }

}
