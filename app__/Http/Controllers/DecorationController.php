<?php

namespace App\Http\Controllers;

use Validator;
use App\Decoration;
use App\Service;
use App\CmOthers;
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

class DecorationController extends Controller {

    private $controller = 'Decoration';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Decoration::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Decoration::select('id', 'name', 'code'
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
            return redirect('/decoration?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $decorationCode = Decoration::select('code')->where('id', Auth::user()->appointment_id)->first();
            $pdf = PDF::loadView('decoration.printDecoration', compact('targetArr', 'decorationCode'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('decorationList.pdf');
        } else {
            return view('decoration.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('decoration.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
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
            return redirect('decoration/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Decoration;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.DECORATION_CREATED_SUCCESSFULLY'));
            return redirect('decoration');
        } else {
            Session::flash('error', __('label.DECORATION_COULD_NOT_BE_CREATED'));
            return redirect('decoration/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Decoration::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('decoration');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('decoration.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = Decoration::find($id);
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
                    'name' => 'required|unique:decoration,name,' . $id,
                    'code' => 'required|unique:decoration,code,' . $id,
                    'order' => 'required|not_in:0'
                        ], $messages);

        if ($validator->fails()) {
            return redirect('decoration/' . $id . '/edit' . $pageNumber)
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
            Session::flash('success', trans('label.DECORATION_UPDATED_SUCCESSFULLY'));
            return redirect('/decoration' . $pageNumber);
        } else {
            Session::flash('error', trans('label.DECORATION_CUOLD_NOT_BE_UPDATED'));
            return redirect('decoration/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Decoration::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        //START:: Check Dependency before deletion
        $decorationDataArr = CmOthers::select('decoration_id')->get()->toArray();

        $finalDecorationArr = $newDecorationArr = [];
        if (!empty($decorationDataArr)) {
            foreach ($decorationDataArr as $decoration) {
                $finalDecorationArr = !empty($decoration['decoration_id']) ? explode(',', $decoration['decoration_id']) : [];
                if(!empty($finalDecorationArr)){
                    foreach ($finalDecorationArr as $key => $decorationId){
                        $newDecorationArr[$decorationId] = $decorationId ;
                    }
                }
                
            }
        }


        if (!empty($newDecorationArr)) {
            if (in_array($id, $newDecorationArr)) {
                Session::flash('error', __('label.DECORATION_IS_RELATED_WITH_CM_OTHERS'));
                return redirect('decoration' . $pageNumber);
            }
        }
        //END:: Check Dependency before deletion

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.DECORATION_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.DECORATION_COULD_NOT_BE_DELETED'));
        }
        return redirect('decoration' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('decoration?' . $url);
    }

}
