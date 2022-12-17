<?php

namespace App\Http\Controllers;

use Validator;
use App\ServiceAppointment;
use App\Service;
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

class ServiceAppointmentController extends Controller {

    private $controller = 'ServiceAppointment';

    public function __construct() {

    }

    public function index(Request $request) {

        $nameArr = ServiceAppointment::select('code')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = ServiceAppointment::select('service_appointment.id', 'service_appointment.name', 'service_appointment.code'
                , 'service_appointment.order', 'service_appointment.status')
                ->orderBy('service_appointment.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('service_appointment.name', 'LIKE', '%' . $searchText . '%')
                        ->orWhere('service_appointment.code', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/serviceAppointment?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $serviceAppointmentCode = ServiceAppointment::select('code')->where('id', Auth::user()->appointment_id)->first();
            $pdf = PDF::loadView('serviceAppointment.printServiceAppointment', compact('targetArr', 'serviceAppointmentCode'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('serviceAppointmentList.pdf');
        } else {
            return view('serviceAppointment.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('serviceAppointment.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
//        echo '<pre>';        print_r($qpArr); exit;
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Name field is required.',
            'code.unique' => 'The Short Name has already been taken.',
        );
        
        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:service_appointment',
                    'code' => 'required|unique:service_appointment',
                    'order' => 'required|not_in:0'
        ], $messages);


        if ($validator->fails()) {
            return redirect('serviceAppointment/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new ServiceAppointment;
        $target->name = $request->name;
        $target->code = $request->code;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.SERVICE_APPOINTMENT_CREATED_SUCCESSFULLY'));
            return redirect('serviceAppointment');
        } else {
            Session::flash('error', __('label.SERVICE_APPOINTMENT_COULD_NOT_BE_CREATED'));
            return redirect('serviceAppointment/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = ServiceAppointment::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('serviceAppointment');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('serviceAppointment.edit')->with(compact('target', 'qpArr', 'orderList'));
    }

    public function update(Request $request, $id) {
        $target = ServiceAppointment::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
            'code.required' => 'The Short Name field is required.',
            'code.unique' => 'The Short Name has already been taken.',
        );
        
        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:service_appointment,name,'.$id,
                    'code' => 'required|unique:service_appointment,code,'.$id,
                    'order' => 'required|not_in:0'
        ], $messages);

        if ($validator->fails()) {
            return redirect('serviceAppointment/' . $id . '/edit' . $pageNumber)
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
            Session::flash('success', trans('label.SERVICE_APPOINTMENT_UPDATED_SUCCESSFULLY'));
            return redirect('/serviceAppointment' . $pageNumber);
        } else {
            Session::flash('error', trans('label.SERVICE_APPOINTMENT_CUOLD_NOT_BE_UPDATED'));
            return redirect('serviceAppointment/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = ServiceAppointment::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' .$qpArr['page'] : '';
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
                return redirect('serviceAppointment' . $pageNumber);
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.SERVICE_APPOINTMENT_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.SERVICE_APPOINTMENT_COULD_NOT_BE_DELETED'));
        }
        return redirect('serviceAppointment' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('serviceAppointment?' . $url);
    }

}
