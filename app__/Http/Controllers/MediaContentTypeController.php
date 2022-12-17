<?php

namespace App\Http\Controllers;

use Validator;
use App\MediaContentType;
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

class MediaContentTypeController extends Controller         {
    
    private $controller = 'MediaContentTypeController';
    
    public function __construct() {
        ;
    }
    
    public function index(Request $request) {

        $nameArr = MediaContentType::select('name')->orderBy('order', 'asc')->get();

        //passing param for custom function
        $qpArr = $request->all();

        $targetArr = MediaContentType::select('media_content_type.id', 'media_content_type.name', 'media_content_type.order')
                ->orderBy('media_content_type.order', 'asc');

        //begin filtering
        $searchText = $request->search;
        if (!empty($searchText)) {
            $targetArr->where(function ($query) use ($searchText) {
                $query->where('name', 'LIKE', '%' . $searchText . '%');
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
            return view('mediaContentType.index')->with(compact('targetArr', 'qpArr', 'nameArr'));
        }
    }
    
    public function filter(Request $request) {
        $url = 'search=' . urlencode($request->search);
        return Redirect::to('mediaContentType?' . $url);
    }
  
}