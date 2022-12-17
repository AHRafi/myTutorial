@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.GRADE_LIST')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="{{ URL::to('gradingSystem/create'.Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_GRADE')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <div class="col-md-12">
                    <!-- Begin Filter-->
                    {!! Form::open(array('group' => 'form', 'url' => 'gradingSystem/filter','class' => 'form-horizontal')) !!}
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                                <div class="col-md-8">
                                    {!! Form::text('fil_search',  Request::get('fil_search'), ['class' => 'form-control tooltips', 'id' => 'filSearch', 'title' => __('label.GRADE_NAME'), 'placeholder' =>  __('label.GRADE_NAME'), 'list' => 'gradingSystemCode', 'autocomplete' => 'off']) !!} 
                                    <datalist id="gradingSystemCode">
                                        @if (!$nameArr->isEmpty())
                                        @foreach($nameArr as $item)
                                        <option value="{{$item->grade_name}}" />
                                        @endforeach
                                        @endif
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="form">
                                <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                    <i class="fa fa-search"></i> @lang('label.FILTER')
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                    </div>
                    {!! Form::close() !!}
                    <!-- End Filter -->
                </div>
            </div>

            <!--            <div class="row">
                            <div class="col-md-offset-8 col-md-4" id="manageEvDiv">
                                <a class="btn btn-icon-only btn-warning tooltips vcenter" title="Download PDF" 
                                   href="{{action('RankController@index', ['download'=>'pdf', 'fil_search' => Request::get('fil_search'), 'fil_service_id' => Request::get('fil_service_id') ])}}">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>-->

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="text-center vcenter">@lang('label.GRADE_NAME')</th>
                            <th class="text-center vcenter">@lang('label.RANGE')</th>
                            <th class="text-center vcenter">@lang('label.ORDER')</th>
                            <th class="text-center vcenter">@lang('label.STATUS')</th>
                            <th class="td-actions text-center vcenter">@lang('label.ACTION')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$targetArr->isEmpty())
                        <?php
                        $page = Request::get('page');
                        $page = empty($page) ? 1 : $page;
                        $sl = ($page - 1) * Session::get('paginatorCount');
                        ?>
                        @foreach($targetArr as $target)
                        <tr>
                            <td class="text-center vcenter">{{ ++$sl }}</td>
                            <td class="text-center vcenter">{{ $target->grade_name }}</td>
                            <td class="text-center vcenter">
                                
                                {{ $target->marks_from }}<span> @lang('label.TO') {!! $target->marks_to < 100 ? '<i class="fa fa-angle-left bold"></i> ' : '' !!}</span>{{ $target->marks_to }}</td>
                            <td class="text-center vcenter">{{ $target->order }}</td>
                            <td class="text-center vcenter">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                @else
                                <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                @endif
                            </td>
                            <td class="td-actions vcenter text-center">
                                <div class="width-inherit">
                                    {{ Form::open(array('url' => 'gradingSystem/' . $target->id.Helper::queryPageStr($qpArr))) }}
                                    {{ Form::hidden('_method', 'DELETE') }}

                                    <a class="btn btn-xs btn-primary tooltips " title="Edit" href="{{ URL::to('gradingSystem/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    {{ Form::close() }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="vcenter">@lang('label.NO_GRADE_FOUND')</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @include('layouts.paginator')
        </div>	
    </div>
</div>
@stop