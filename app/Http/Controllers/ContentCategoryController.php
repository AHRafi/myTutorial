<?php

namespace App\Http\Controllers;

use Validator;
use App\ContentCategory;
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
use Common;
use Illuminate\Http\Request;

class ContentCategoryController extends Controller {

    private $controller = 'ContentCategory';
    private $parentArr = [];

    public function index(Request $request) {
        //passing param for custom function
        $qpArr = $request->all();
        $compartmentList = Common::getArchiveCompartmentList();
        
//        echo '<pre>';
//        print_r($compartmentList);
//        exit;
//        
        $targetArr = ContentCategory::select('content_category.*')->orderBy('name', 'asc');
        $nameArr = ContentCategory::select('name')->orderBy('name', 'asc')->get();
//begin filtering
        $searchText = $request->search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('name', 'LIKE', '%' . $searchText . '%');
            });
        }
        //end filtering

        $targetArr = $targetArr->paginate(Session::get('paginatorCount'));

        if (!$targetArr->isEmpty()) {
            foreach ($targetArr as $target) {

                if (!empty($target->parent_id)) {
                    //calling recursive function findParentCategory
                    $this->findParentCategory($target->parent_id, $target->id);
                }
            }
        }
        //change page number after delete if no data has current page
        if ($targetArr->isEmpty() && isset($qpArr['page']) && ($qpArr['page'] > 1)) {
            $page = ($qpArr['page'] - 1);
            return redirect('/contentCategory?page=' . $page);
        }

        $parentArr = $this->parentArr;
        //echo '<pre>';print_r($parentArr);exit;

        return view('contentCategory.index')->with(compact('targetArr', 'qpArr', 'parentArr'
                                , 'compartmentList', 'nameArr'));
    }

    public function findParentCategory($parentId = 0, $id = 0) {
        $dataArr = ContentCategory::where('id', $parentId)->first();

        $parent = !empty($this->parentArr[$id]) ? $this->parentArr[$id] : '';
        if (!empty($dataArr->name)) {
            $parent = $dataArr->name . ' &raquo; ' . $parent;
        }

        $this->parentArr[$id] = $parent;

        if (!empty($dataArr->parent_id)) {
            $this->findParentCategory($dataArr->parent_id, $id);
        }

        //exclude last &raquo; sign
        $this->parentArr[$id] = trim($this->parentArr[$id], ' &raquo; ');

        return true;
    }

    public function create(Request $request) { //passing param for custom function
        $qpArr = $request->all();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 1);
        $categoryArr = ContentCategory::where('status', '1')->orderBy('name', 'asc')->select('name', 'id', 'parent_id')->get();

        $compartmentList = Common::getArchiveCompartmentList();

        $parentArr = [];

        if (!$categoryArr->isEmpty()) {

            foreach ($categoryArr as $category) {
                //calling recursive function findParentCategory
                $this->findParentCategory($category->parent_id, $category->id);
                $parentArr[$category->id] = trim($this->parentArr[$category->id] . ' &raquo; ' . $category->name, ' &raquo; ');
            }
        }
        return view('contentCategory.create')->with(compact('qpArr', 'parentArr', 'orderList', 'compartmentList'));
    }

    public function store(Request $request) {
        //begin back same page after update
        $qpArr = $request->all();

//        echo '<pre>';
//        print_r($qpArr);
//        exit;
//        
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:content_category,name',
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('contentCategory/create' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target = new ContentCategory;
        $target->name = $request->name;
        $target->parent_id = $request->parent_id;
        $target->related_compartment = !empty($request->related_compartment) ? implode(',', $request->related_compartment) : NULL;
        $target->short_description = $request->short_description;
        $target->order = 0;
        $target->status = $request->status;

        if ($target->save()) {
            Helper :: insertOrder($this->controller, $request->order, $target->id);
            Session::flash('success', __('label.CONTENT_CATEGORY_CREATED_SUCCESSFULLY'));
            return redirect('contentCategory');
        } else {
            Session::flash('error', __('label.CONTENT_CATEGORY_COULD_NOT_BE_CREATED'));
            return redirect('contentCategory/create' . $pageNumber);
        }
    }

    public function edit(Request $request, $id) {
        $target = ContentCategory::find($id);
        $compartmentList = Common::getArchiveCompartmentList();
        $orderList = array('0' => __('label.SELECT_ORDER_OPT')) + Helper::getOrderList($this->controller, 2);
        if (empty($target)) {
            Session::flash('error', trans('label.INVALID_DATA_ID'));
            return redirect('contentCategory');
        }
        //calling recursive function findParentCategory

        $categoryArr = ContentCategory::where('status', 1)->orderBy('name', 'asc')->select('id', 'parent_id', 'name')->where('id', '!=', $id)->get();
        //echo '<pre>';print_r($categoryArr);exit;
        $parentArr = [];

        if (!$categoryArr->isEmpty()) {
            foreach ($categoryArr as $category) {
                //calling recursive function findParentCategory
                $this->findParentCategory($category->parent_id, $category->id);
                $parentArr[$category->id] = trim($this->parentArr[$category->id] . ' &raquo; ' . $category->name, ' &raquo; ');
            }
        }
        //passing param for custom function
        $qpArr = $request->all();

        return view('contentCategory.edit')->with(compact('target', 'qpArr', 'parentArr', 'orderList', 'compartmentList'));
    }

    public function update(Request $request, $id) { //print_r($request->all());exit;
        $target = ContentCategory::find($id);
        $presentOrder = $target->order;
        //echo '<pre>';print_r($target);exit;
        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = $qpArr['filter']; //!empty($qpArr['page']) ? '?page='.$qpArr['page'] : '';
        //end back same page after update

        $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:content_category,name,' . $id,
                    'order' => 'required|not_in:0'
        ]);

        if ($validator->fails()) {
            return redirect('contentCategory/' . $id . '/edit' . $pageNumber)
                            ->withInput()
                            ->withErrors($validator);
        }

        $target->name = $request->name;
        $target->parent_id = $request->parent_id;
        $target->related_compartment = !empty($request->related_compartment) ? implode(',', $request->related_compartment) : NULL;
        $target->short_description = $request->short_description;
        $target->order = $request->order;
        $target->status = $request->status;

        if ($target->save()) {
            if ($request->order != $presentOrder) {
                Helper :: updateOrder($this->controller, $request->order, $target->id, $presentOrder);
            }
            Session::flash('success', __('label.CONTENT_CATEGORY_UPDATED_SUCCESSFULLY'));
            return redirect('contentCategory' . $pageNumber);
        } else {
            Session::flash('error', __('label.CONTENT_CATEGORY_COULD_NOT_BE_UPDATED'));
            return redirect('contentCategory/' . $id . '/edit' . $pageNumber);
        }
    }

    public function destroy(Request $request, $id) {
        $target = ContentCategory::find($id);

        //begin back same page after update
        $qpArr = $request->all();
        $pageNumber = !empty($qpArr['page']) ? '?page=' . $qpArr['page'] : '?page=';
        //end back same page after update

        if (empty($target)) {
            Session::flash('error', __('label.INVALID_DATA_ID'));
        }

//        //Dependency
        $dependencyArr = [
            'ContentCategory' => ['1' => 'parent_id'],
//            'Content' => ['1' => 'content_category_id'],
        ];
        foreach ($dependencyArr as $model => $val) {
            foreach ($val as $index => $key) {
                $namespacedModel = '\\App\\' . $model;
                $dependentData = $namespacedModel::where($key, $id)->first();
                if (!empty($dependentData)) {
                    Session::flash('error', __('label.COULD_NOT_DELETE_DATA_HAS_RELATION_WITH_MODEL', ['model' => $model]));
                    return redirect('contentCategory' . $pageNumber);
                }
            }
        }

        if ($target->delete()) {
            Helper :: deleteOrder($this->controller, $target->order);
            Session::flash('error', __('label.CONTENT_CATEGORY_DELETED_SUCCESSFULLY'));
        } else {
            Session::flash('error', __('label.CONTENT_CATEGORY_COULD_NOT_BE_DELETED'));
        }
        return redirect('contentCategory' . $pageNumber);
    }

    public function filter(Request $request) {
        $url = 'search=' . urlencode($request->search);
        return Redirect::to('contentCategory?' . $url);
    }

}
