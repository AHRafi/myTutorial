<?php

namespace App\Http\Controllers;

use Validator;
use App\UserGroup;
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

class UserGroupController extends Controller {

    private $controller = 'UserGroupController';

    public function __construct() {

    }

    public function index(Request $request) {

        $nameArr = UserGroup::select('name')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = UserGroup::select('user_group.id', 'user_group.name', 'user_group.order')
                ->orderBy('user_group.order', 'asc');

        //begin filtering
        $searchText = $request->fil_search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('user_group.name', 'LIKE', '%' . $searchText . '%');
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
            return redirect('/userGroup?page=' . $page);
        }

        if ($request->download == 'pdf') {
            $userGroupCode = userGroup::select('name')->where('id', Auth::user()->group_id)->first();
            $pdf = PDF::loadView('userGroup.printUserGroup', compact('targetArr', 'userGroupCode'))
                    ->setPaper('a4', 'portrait')
                    ->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->download('userGroupList.pdf');
        } else {
            return view('userGroup.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }
    
    public function filter(Request $request) {
        $url = 'fil_search=' . urlencode($request->fil_search);
        return Redirect::to('userGroup?' . $url);
    }

}
