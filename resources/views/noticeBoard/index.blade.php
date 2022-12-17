@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-clipboard"></i>@lang('label.NOTICE_LIST')
            </div>
            @if(in_array(Auth::user()->group_id, [1]))
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="{{ URL::to('noticeBoard/create'.Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_NOTICE')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
            @endif
        </div>
        <div class="portlet-body">

            <div class="row">
                <!-- Begin Filter-->
                {!! Form::open(array('group' => 'form', 'url' => 'noticeBoard/filter','class' => 'form-horizontal')) !!}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="headline">@lang('label.HEADLINE')</label>
                            <div class="col-md-8">
                                {!! Form::text('headline',  Request::get('headline'), ['class' => 'form-control tooltips', 'id' => 'headline', 'list' => 'headlineList', 'autocomplete' => 'off']) !!} 
                                <datalist id="headlineList">
                                    @if (!$headlineArr->isEmpty())
                                    @foreach($headlineArr as $item)
                                    <option value="{{$item->headline}}" />
                                    @endforeach
                                    @endif
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-group">

                            <label class="control-label col-md-2" for="fillDateFrom">@lang('label.PUBLISH_DATE_BETWEEN'):</label>
                            <div class="col-md-4"> 
                                <div class="input-group date datepicker2">
                                    {!! Form::text('fil_date_from', Request::get('fil_date_from'), ['id'=> 'fillDateFrom', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="fillDateFrom">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('fil_date_from') }}</span>
                            </div>
                            <div class="col-md-1 text-center margin-top-5"><span class="bold">@lang('label.TO')</span></div>
                            <div class="col-md-4"> 
                                <div class="input-group date datepicker2">
                                    {!! Form::text('fil_date_to', Request::get('fil_date_to'), ['id'=> 'fillDateTo', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="fillDateTo">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('fil_date_to') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="form">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                <i class="fa fa-search"></i> @lang('label.FILTER')
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- End Filter -->
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="vcenter">@lang('label.HEADLINE')</th>
                            <th class="vcenter">@lang('label.DESCRIPTION')</th>
                            <th class="vcenter">@lang('label.PUBLISH_DATE')</th>
                            <th class="vcenter">@lang('label.EXPIRE_DATE')</th>
                            <th class="text-center vcenter">@lang('label.STATUS')</th>
                            @if(in_array(Auth::user()->group_id, [1]))
                            <th class="td-actions text-center vcenter">@lang('label.ACTION')</th>
                            @endif
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
                            <td class="vcenter">{{ $target->headline }}</td>
                            <td class="vcenter">{!! $target->description !!}</td>
                            <td class="vcenter">{{ !empty($target->created_at) ? Helper::formatDate($target->created_at) : '' }}</td>
                            <td class="vcenter">{{ !empty($target->end_date) ? Helper::formatDate($target->end_date): '' }}</td>
                            <td class="text-center vcenter">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                @else
                                <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                @endif
                            </td>

                            @if(in_array(Auth::user()->group_id, [1]))
                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">
                                    {{ Form::open(array('url' => 'noticeBoard/' . $target->id.Helper::queryPageStr($qpArr))) }}
                                    {{ Form::hidden('_method', 'DELETE') }}

                                    <a class="btn btn-xs btn-primary tooltips " title="Edit" href="{{ URL::to('noticeBoard/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    {{ Form::close() }}
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="vcenter">@lang('label.NO_NOTICE_FOUND')</td>
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