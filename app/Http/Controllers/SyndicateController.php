<?php

namespace App\Http\Controllers;

use Validator;
use App\Syndicate;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Illuminate\Http\Request;

class SyndicateController extends Controller {

    private $controller = 'Syndicate';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Syndicate::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Syndicate::select('syndicate.id', 'syndicate.name', 'syndicate.code', 'syndicate.order', 'syndicate.status')
                ->orderBy('syndicate.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('syndicate.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('syndicate.code', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/syndicate?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $syndicateCode = Syndicate::select('code')->where('id', Auth::user()->syndicate_id)->first();
            $pdf = PDF::loadView('syndicate.printSyndicate', compact('targetArr', 'syndicateCode'))->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('syndicateList.pdf');
        } else {
            return view('syndicate.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('syndicate.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:syndicate',
                    'code' => 'required|unique:syndicate',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('syndicate/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Syndicate;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.SYN_CREATED_SUCCESSFULLY'));
            return redirect('syndicate');
        } else {
            Session::flash('error', __('label.SYN_COULD_NOT_BE_CREATED'));
            return redirect('syndicate/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Syndicate::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('syndicate');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('syndicate.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = Syndicate::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:syndicate,name,' . $id,
                    'code' => 'required|unique:syndicate,code,' . $id,
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('syndicate/' . $id . '/edit' . $pageNumber)
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
            Session::flash('success', trans('label.SYN_UPDATED_SUCCESSFULLY'));
            return redirect('/syndicate' . $pageNumber);
        } else {
            Session::flash('error', trans('label.SYN_CUOLD_NOT_BE_UPDATED'));
            return redirect('syndicate/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Syndicate::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

//Check Dependency before deletion
        $dependencyArr = [
            'SynToCourse' => 'syn_id'
//            , 'EventMarkingLock' => 'syndicate_id', 'Marking' => 'syndicate_id'
//            , 'ParticularMarkingLock' => 'syndicate_id', 'SyndicateToBatch' => 'syndicate_id', 'PlCmdrToSyndicate' => 'syndicate_id'
//            , 'RctState' => 'syndicate_id', 'RecruitToSyndicate' => 'syndicate_id'
        ];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('syndicate' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.SYN_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.SYN_COULD_NOT_BE_DELETED'));
        }
        return redirect('syndicate' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('syndicate?' . $url);
    }

}
