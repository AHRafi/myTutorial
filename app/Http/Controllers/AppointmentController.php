<?php

namespace App\Http\Controllers;

use Validator;
use App\Appointment;
use App\Service;
use Session;
use Redirect;
use Helper;
use Common;
use Response;
use App;
use View;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;

class AppointmentController extends Controller {

    private $controller = 'Appointment';

    public function __construct() {
        
    }

    public function index(Request $request) {

        $nameArr = Appointment::select('code')->orderBy('order', 'asc')->get();

        $respList = ['0' => __('label.SELECT_RESPONSIBILITY_OPT')] + Common::getSvcResposibilityList();
        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = Appointment::select('appointment.id', 'appointment.name', 'appointment.code'
                        , 'appointment.order', 'appointment.status', 'appointment.responsibility')
                ->orderBy('appointment.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('appointment.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('appointment.code', 'LIKE', '%' . $searchText . '%');
            });
        }
        if (!empty($request->fil_resp_id)) {
            $targetArr = $targetArr->where('appointment.responsibility', $request->fil_resp_id);
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
            return redirect('/appointment?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $appointmentCode = Appointment::select('code')->where('id', Auth::user()->appointment_id)->first();
            $pdf = PDF::loadView('appointment.printAppointment', compact('targetArr', 'appointmentCode'))->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('appointmentList.pdf');
        } else {
            return view('appointment.index')->with(compact('targetArr', 'qpArr', 'nameArr', 'respList'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);
        $respList = ['0' => __('label.SELECT_RESPONSIBILITY_OPT')] + Common::getSvcResposibilityList();

        return view('appointment.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber', 'respList'));
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
                    'order' => 'required|not_in:0',
                    'responsibility' => 'required|not_in:0'
                        ], $messages);


        if ($validator->fails()) {
            return redirect('appointment/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Appointment;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->responsibility = $request->responsibility;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.APPOINTMENT_CREATED_SUCCESSFULLY'));
            return redirect('appointment');
        } else {
            Session::flash('error', __('label.APPOINTMENT_COULD_NOT_BE_CREATED'));
            return redirect('appointment/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = Appointment::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('appointment');
        }

        //passing param for custom function
        $qpArr = $request->all();
        $respList = ['0' => __('label.SELECT_RESPONSIBILITY_OPT')] + Common::getSvcResposibilityList();

        return view('appointment.edit')->with(compact('target', 'qpArr', 'orderList', 'respList'));
    }

    public function update(Request $request, $id) {
        $target = Appointment::find($id);
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
                    'order' => 'required|not_in:0',
                    'responsibility' => 'required|not_in:0'
                        ], $messages);

        if ($validator->fails()) {
            return redirect('appointment/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->code = $request->code;
        $target->responsibility = $request->responsibility;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.APPOINTMENT_UPDATED_SUCCESSFULLY'));
            return redirect('/appointment' . $pageNumber);
        } else {
            Session::flash('error', trans('label.APPOINTMENT_CUOLD_NOT_BE_UPDATED'));
            return redirect('appointment/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Appointment::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
        //Check Dependency before deletion
        $dependencyArr = ['User' => 'appointment_id'];

        foreach ($dependencyArr as $model => $key) {
            $namespacedModel = '\\App\\' . $model;
            $dependentData = $namespacedModel::where($key, $id)->first();
            if (!empty($dependentData)) {
                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
                return redirect('appointment' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.APPOINTMENT_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.APPOINTMENT_COULD_NOT_BE_DELETED'));
        }
        return redirect('appointment' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search) . '&fil_resp_id=' . $request->fil_resp_id;
        return Redirect::to('appointment?' . $url);
    }

}
