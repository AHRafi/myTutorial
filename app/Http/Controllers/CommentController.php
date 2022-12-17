<?php

namespace App\Http\Controllers;

use App\Comment;
use Session;
use Redirect;
use Helper;
use Validator;
use Response;
use App;
use View;
use PDF;
use Auth;
use Input;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private $controller = 'Comment';

    public function index(Request $request) {
        $nameArr = Comment::select('title')->orderBy('order', 'asc')->get();
        $qpArr = $request->all();
        $targetArr = Comment::select('id', 'title', 'order', 'status')
                ->orderBy('comment.order', 'asc');
        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('title', 'LIKE', '%' . $searchText . '%');
            });
        }


        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));



        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/gsmodule?page=' . $page);
        }


        return view('comment.index')->with(compact('qpArr', 'targetArr', 'nameArr'));
    }
    
     public function create(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $lastOrderNumber = Helper::getLastOrder($this->controller, 1);

        return view('comment.create')->with(compact('qpArr', 'orderList', 'lastOrderNumber'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();
        
        $pageNumber = !empty($qpArr['page']) ? $qpArr['page'] : '';
        //end back same page after update


        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'order' => 'required|not_in:0'
        ]);


        if ($validator->fails()) {
            return redirect('comment/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }


        $target = new Comment;
        $target->title = $request->title;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.COMMENT_CREATED_SUCCESSFULLY'));
            return redirect('comment');
        } else {
            Session::flash('error', __('label.COMMENT_COULD_NOT_BE_CREATED'));
            return redirect('comment/create' . $pageNumber);
        }
    }
    
     public function edit(Request $request, $id) {
        $target = Comment::find($id);
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);

        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('comment');
        }

        //passing param for custom function
        $qpArr = $request->all();

        return view('comment.edit')->with(compact('target', 'qpArr', 'orderList'));
    }
    
    public function update(Request $request, $id) {
        $target = Comment::find($id);
        $presentOrder = $target->order;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter'];
        //end back same page after update

        
        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('comment/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->title = $request->title;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', trans('label.COMMENT_UPDATED_SUCCESSFULLY'));
            return redirect('/comment' . $pageNumber);
        } else {
            Session::flash('error', trans('label.COMMENT_CUOLD_NOT_BE_UPDATED'));
            return redirect('comment/' . $id . '/edit' . $pageNumber);
        }
    }
    
    public function destroy(Request $request, $id) {
        $target = Comment::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }
     

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.COMMENT_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.COMMENT_COULD_NOT_BE_DELETED'));
        }
        return redirect('comment' . $pageNumber);
    }
    
    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('comment?' . $url);
    }
}
