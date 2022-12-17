<?php

namespace App\Http\Controllers;

use App\Gs;
use Validator;
use Session;
use Response;
use Redirect;
use Auth;
use File;
use PDF;
use URL;
use Hash;
use Common;
use DB;
use Helper;
use Illuminate\Http\Request;

class GsController extends Controller {

    public function index(Request $request) {

        $nameArr = Gs::select('name')->get();

        $qpArr = $request->all();
        $targetArr = Gs::select('gs.*');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('name', 'LIKE', '%' . $searchText . '%');
            });
        }


        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));



        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/gs?page=' . $page);
        }


        return view('gs.index')->with(compact('qpArr', 'nameArr', 'targetArr'));
    }

    public function create(Request $request) {

        $qpArr = $request->all();
        return view('gs.create')->with(compact('qpArr'));
    }

    public function store(Request $request) {

        $qpArr = $request->all();

        $rules = [
            'name' => 'required|not_in:0'
        ];

        if (!empty($request->photo)) {
            $rules['photo'] = 'max:1024|mimes:jpeg,png,jpg';
        }


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect('gs/create')
                            ->withInput($request->except('photo', 'password', 'conf_password'))
                            ->withErrors($validator);
        }

        //file upload
        $file = $request->file('photo');
        if (!empty($file)) {
            $imagedata = file_get_contents($file);
            // alternatively specify an URL, if PHP settings allow
            $request['encoded_photo'] = base64_encode($imagedata);
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/gs', $fileName);
        }

        $target = new Gs;

        $target->name = $request->name;
        $target->unit = $request->unit;
        $target->conduct_date = Helper::dateFormatConvert($request->conduct_date);
        $target->summary_expertise = $request->summary_expertise;
        $target->number = $request->number;
        $target->alt_number = $request->alt_number;
        $target->email = $request->email;
        $target->present_address = $request->present_address;
        $target->permanent_address = $request->permanent_address;
        $target->status = $request->status;
        $target->photo = !empty($fileName) ? $fileName : '';

        if ($target->save()) {
            Session::flash('success', __('label.GS_CREATED_SUCCESSFULLY'));
            return redirect('gs');
        } else {
            Session::flash('error', __('label.GS_COULD_NOT_BE_CREATED'));
            return redirect('gs/create');
        }
    }

    public function edit(Request $request, $id) {
        $target = Gs::find($id);


        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('gs');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('gs.edit')->with(compact('target', 'qpArr'));
    }

    public function update(Request $request, $id) {

        $target = Gs::find($id);
        $previousFileName = $target->photo;

        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];

        $rules = [
            'name' => 'required|not_in:0'
        ];

        if (!empty($request->photo)) {
            $rules['photo'] = 'max:1024|mimes:jpeg,png,jpg';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect('gs/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        if (!empty($request->photo)) {
            $prevfileName = 'public/uploads/gs/' . $target->photo;

            if (File::exists($prevfileName)) {
                File::delete($prevfileName);
            }
        }
        $file = $request->file('photo');
        if (!empty($file)) {
            $imagedata = file_get_contents($file);
            // alternatively specify an URL, if PHP settings allow
            $request['encoded_photo'] = base64_encode($imagedata);
            $fileName = uniqid() . "_" . Auth::user()->id . "." . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move('public/uploads/gs', $fileName);
        }
        $target->name = $request->name;
        $target->unit = $request->unit;
        $target->conduct_date = Helper::dateFormatConvert($request->conduct_date);
        $target->summary_expertise = $request->summary_expertise;
        $target->number = $request->number;
        $target->alt_number = $request->alt_number;
        $target->email = $request->email;
        $target->present_address = $request->present_address;
        $target->permanent_address = $request->permanent_address;
        $target->status = $request->status;
        $target->photo = !empty($fileName) ? $fileName : $previousFileName;

        if ($target->save()) {
            Session::flash('success', trans('label.GS_UPDATED_SUCCESSFULLY'));
            return redirect('/gs' . $pageNumber);
        } else {
            Session::flash('error', trans('label.GS_CUOLD_NOT_BE_UPDATED'));
            return redirect('gs/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = Gs::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        $fileName = 'public/uploads/gs/' . $target->photo;
        if (File::exists($fileName)) {
            File::delete($fileName);
        }


        if ($target->delete()) {
           Session::flash('error', __('label.GS_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.GS_COULD_NOT_BE_DELETED'));
        }
        return redirect('gs' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('gs?' . $url);
    }

    public function showGsInfo(Request $request)
    {
        $id = $request->gs_id ?? 0;

        $target = Gs::select('gs.*')->where('id', $id)->first();


        if ($target) {
            $html = view('gs.showGsInfo', compact('target'))->render();
            return Response::json(['html' => $html]);
        } else {
            return Response::json(array('success' => false, 'message' => __('label.INVALID_DATA_ID')), 401);
        }
    }

}
