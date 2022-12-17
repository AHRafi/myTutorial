<?php

namespace App\Http\Controllers;

use Validator;
use App\CmAppointment;
use App\Service;
use App\Award;
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

class AwardController extends Controller {

    private $controller = 'Award';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Award::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Award::select('id', 'name', 'code'
                        , 'order', 'status')
                ->orderBy('order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('code', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/award?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $awardCode = Award::select('code')->where('id', Auth::user()->appointment_id)->first();
            $pdf = PDF::loadView('award.printAward', compact('targetArr', 'awardCode'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('awardList.pdf');
        } else {
            return view('award.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('award.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
//        echo '<pre>';        print_r($qpArr); exit;
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Info field is required.',
            'code.unique' => 'The Short info has already been taken.',
        );

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:decoration',
                    'code' => 'required|unique:decoration',
                    'order' => 'required|not_in:0'
                        ], $messages);


        if ($validator->fails()) {
            return redirect('award/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Award;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.AWARD_CREATED_SUCCESSFULLY'));
            return redirect('award');
        } else {
            Session::flash('error', __('label.AWARD_COULD_NOT_BE_CREATED'));
            return redirect('award/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Award::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('award');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('award.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = Award::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Info field is required.',
            'code.unique' => 'The Short info has already been taken.',
        );

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:award,name,'.$id,
                    'code' => 'required|unique:award,code,'.$id,
                    'order' => 'required|not_in:0'
                        ], $messages);


        if ($validator->fails()) {
            return redirect('award/' . $id . '/edit' . $pageNumber)
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
            Session::flash('success', trans('label.AWARD_UPDATED_SUCCESSFULLY'));
            return redirect('/award' . $pageNumber);
        } else {
            Session::flash('error', trans('label.AWARD_CUOLD_NOT_BE_UPDATED'));
            return redirect('award/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Award::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        //Check Dependency before deletion
//        $dependencyArr = ['User' => 'award_id'];
//
//        foreach ($dependencyArr as $model => $key) {
//            $namespacedModel = '\\App\\' . $model;
//            $dependentData = $namespacedModel::where($key, $id)->first();
//            if (!empty($dependentData)) {
//                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
//                return redirect('award' . $pageNumber);
//            }
//        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.AWARD_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.AWARD_COULD_NOT_BE_DELETED'));
        }
        return redirect('award' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('award?' . $url);
    }

}
