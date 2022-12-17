<?php

namespace App\Http\Controllers;

use Validator;
use App\NoticeBoard;
use Session;
use Redirect;
use Helper;
use PDF;
use Auth;
use Illuminate\Http\Request;

class NoticeBoardController extends Controller {

    private $controller = 'NoticeBoard';

    public function __construct() {
        
    }

    public function index(Request $request) {
        $headlineArr = NoticeBoard::select('headline')->orderBy('created_at', 'asc')->get();

        $dateFrom = !empty($request->fil_date_from) ? Helper::dateFormatConvert($request->fil_date_from) . ' 00:00:00' : '';
        $dateTo = !empty($request->fil_date_to) ? Helper::dateFormatConvert($request->fil_date_to) . ' 23:59:59' : '';
        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = NoticeBoard::select('*')->orderBy('created_at', 'desc');
        
//        echo '<pre>';        print_r($dateFrom); exit;
        
        //begin filtering
        $searchText = $request->headline;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('headline', 'LIKE', '%' . $searchText . '%');
            });
        }

        if (!empty($request->fil_date_from) && !empty($request->fil_date_to)) {
            $targetArr = $targetArr->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        //end filtering

        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/noticeBoard?page=' . $page);
        }


        return view('noticeBoard.index')->with(compact('targetArr', 'qpArr', 'headlineArr'));
    }

    public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        
        return view('noticeBoard.create')->with(compact('qpArr'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update
        
        

        $messages = array(
            'end_date.required' => 'The Date field is required.',
        );

        
        $validator = Validator::make($request->all(), [
                    'headline' => 'required',
                    'description' => 'required',
                    'end_date' => 'required'], $messages);


        if ($validator->fails()) {
            return redirect('noticeBoard/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new NoticeBoard;
        $target->headline = $request->headline;
        $target->description = $request->description;
        $target->end_date = Helper::dateFormatConvert($request->end_date);
        $target->status = $request->status;

        if ($target->save()) {
            Session::flash('success', __('label.NOTICE_CREATED_SUCCESSFULLY'));
            return redirect('noticeBoard');
        } else {
            Session::flash('error', __('label.NOTICE_COULD_NOT_BE_CREATED'));
            return redirect('noticeBoard/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = NoticeBoard::find($id);
        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('noticeBoard');
        }

        //passing param for custom function
        $qpArr = $request->all();
        
        return view('noticeBoard.edit')->with(compact('target', 'qpArr'));
    }

    public function update(Request $request, $id) {
        $target = NoticeBoard::find($id);
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        $messages = array(
            'end_date.required' => 'The Date field is required.',
        );

        
        $validator = Validator::make($request->all(), [
                    'headline' => 'required',
                    'description' => 'required',
                    'end_date' => 'required'], $messages);

        if ($validator->fails()) {
            return redirect('noticeBoard/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->headline = $request->headline;
        $target->description = $request->description;
        $target->end_date = Helper::dateFormatConvert($request->end_date);
        $target->status = $request->status;

        if ($target->save()) {
            Session::flash('success', trans('label.NOTICE_UPDATED_SUCCESSFULLY'));
            return redirect('/noticeBoard' . $pageNumber);
        } else {
            Session::flash('error', trans('label.NOTICE_CUOLD_NOT_BE_UPDATED'));
            return redirect('noticeBoard/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = NoticeBoard::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

        //START:: Check Dependency before deletion
//        $dependencyArr = [
//            'User' => 'rank_id',
//            'CmBasicProfile' => 'rank_id'];
//
//        foreach ($dependencyArr as $model => $key) {
//            $namespacedModel = '\\App\\' . $model;
//            $dependentData = $namespacedModel::where($key, $id)->first();
//            if (!empty($dependentData)) {
//                Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL') . $model);
//                return redirect('rank' . $pageNumber);
//            }
//        }
        //END:: Check Dependency before deletion

        if ($target->delete()) {
            Session::flash('error', __('label.NOTICE_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.NOTICE_COULD_NOT_BE_DELETED'));
        }
        return redirect('noticeBoard' . $pageNumber);
    }

    public function filter(Request $request) {
        $rules = [
            'fil_date_from' => 'required',
            'fil_date_to' => 'required',
        ];

        $messages = [
            'fil_date_from.required' => __('label.THE_FROM_DATE_FIELD_IS_REQUIRED'),
            'fil_date_to.required' => __('label.THE_TO_DATE_FIELD_IS_REQUIRED'),
        ];
        $url = 'fil_date_from=' . $request->fil_date_from . '&fil_date_to=' . $request->fil_date_to;
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect('noticeBoard?' . $url)
                            ->withInput()
                            ->withErrors($validator);
        }


        
        $url = 'fil_date_from=' . $request->fil_date_from . '&fil_date_to=' . $request->fil_date_to;
        
        return Redirect::to('noticeBoard?' . $url);
    }

}
