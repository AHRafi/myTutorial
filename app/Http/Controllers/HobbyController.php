<?php

namespace App\Http\Controllers;

use Validator;
use App\CmAppointment;
use App\Service;
use App\Hobby;
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

class HobbyController extends Controller {

    private $controller = 'Hobby';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Hobby::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Hobby::select('id', 'name', 'code'
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
            return redirect('/hobby?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $hobbyCode = Hobby::select('code')->where('id', Auth::user()->appointment_id)->first();
            $pdf = PDF::loadView('hobby.printHobby', compact('targetArr', 'hobbyCode'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('hobbyList.pdf');
        } else {
            return view('hobby.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('hobby.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
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
                    'name' => 'required|unique:hobby',
                    'code' => 'required|unique:hobby',
                    'order' => 'required|not_in:0'
                        ], $messages);

        if ($validator->fails()) {
            return redirect('hobby/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Hobby;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.HOBBY_CREATED_SUCCESSFULLY'));
            return redirect('hobby');
        } else {
            Session::flash('error', __('label.HOBBY_COULD_NOT_BE_CREATED'));
            return redirect('hobby/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Hobby::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('hobby');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('hobby.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = Hobby::find($id);
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
                    'name' => 'required|unique:hobby,name,'.$id,
                    'code' => 'required|unique:hobby,code,'.$id,
                    'order' => 'required|not_in:0'
                        ], $messages);

        if ($validator->fails()) {
            return redirect('hobby/' . $id . '/edit' . $pageNumber)
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
            Session::flash('success', trans('label.HOBBY_UPDATED_SUCCESSFULLY'));
            return redirect('/hobby' . $pageNumber);
        } else {
            Session::flash('error', trans('label.HOBBY_CUOLD_NOT_BE_UPDATED'));
            return redirect('hobby/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Hobby::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        //START:: Check Dependency before deletion
        $hobbyDataArr = CmOthers::select('hobby_id')->get()->toArray();

        $finalHobbyArr = $newHobbyArr = [];
        if (!empty($hobbyDataArr)) {
            foreach ($hobbyDataArr as $hobby) {
                $finalHobbyArr = !empty($hobby['hobby_id']) ? explode(',', $hobby['hobby_id']) : [];
                if(!empty($finalHobbyArr)){
                    foreach ($finalHobbyArr as $key => $hobbyId){
                        $newHobbyArr[$hobbyId] = $hobbyId ;
                    }
                }
                
            }
        }

        if (!empty($newHobbyArr)) {
            if (in_array($id, $newHobbyArr)) {
                Session::flash('error', __('label.HOBBY_IS_RELATED_WITH_CM_OTHERS'));
                return redirect('decoration' . $pageNumber);
            }
        }
        //END:: Check Dependency before deletion

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.HOBBY_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.HOBBY_COULD_NOT_BE_DELETED'));
        }
        return redirect('hobby' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('hobby?' . $url);
    }

}
