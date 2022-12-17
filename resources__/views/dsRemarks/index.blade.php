@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DS_REMARKS')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="{{ URL::to('dsRemarks/create'.Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_DS_REMARKS')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'dsRemarks/filter','class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearList->name}} </strong></div>
                            {!! Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                            {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> </span></label>
                        <div class="col-md-8">
                            {!! Form::select('term_id', $termList, Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                            <span class="text-danger">{{ $errors->first('term_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cmId">@lang('label.CM') :</label>
                        <div class="col-md-8">
                            {!! Form::select('cm_id', $cmList, Request::get('cm_id'), ['class' => 'form-control js-source-states', 'id' => 'cmId']) !!}
                            <span class="text-danger">{{ $errors->first('cm_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="maEventId">@lang('label.EVENT') :</label>
                        <div class="col-md-8">
                            {!! Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
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

            {!! Form::close() !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="max-height-500 table-responsive webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SL')</th>
                                    <th class="vcenter">@lang('label.DATE')</th>
                                    <th class="vcenter">@lang('label.TERM')</th>
                                    <th class="vcenter">@lang('label.CM')</th>
                                    <th class="vcenter">@lang('label.EVENT')</th>
                                    <th class="vcenter text-center">@lang('label.RMKS')</th>
                                    <th class="vcenter text-center">@lang('label.REMARKED_BY')</th>
                                    <th class="td-actions text-center vcenter">@lang('label.ACTION')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if(!$targetArr->isEmpty())
                                <?php
                                $page = Request::get('page');
                                $page = empty($page) ? 1 : $page;
                                $sl = ($page - 1) * Session::get('paginatorCount');
                                ?>
                                @foreach($targetArr as $remarks)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">{{ !empty($remarks->date) ? Helper::formatDate($remarks->date) : '' }}</td>
                                    <td class="vcenter">{!! $remarks->term !!}</td>
                                    <td class="vcenter">{!! $remarks->cm !!}</td>
                                    <td class="vcenter">{{ $remarks->event }}</td>
                                    <td class="vcenter">{{ $remarks->remarks ?? '' }}</td>
                                    <td class="vcenter text-center">{{ $remarks->official_name }}</td>
                                    <td class="td-actions text-center vcenter">
                                        <div class="width-inherit">
                                            {{ Form::open(array('url' => 'dsRemarks/' . $remarks->id.Helper::queryPageStr($qpArr))) }}
                                            {{ Form::hidden('_method', 'DELETE') }}

                                            <a class="btn btn-xs btn-primary tooltips " title="Edit" href="{{ URL::to('dsRemarks/' . $remarks->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
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
                                    <td colspan="9"><strong>@lang('label.NO_DS_REMARKS_FOUND')</strong></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @include('layouts.paginator')
                </div>
            </div>

        </div>
    </div>
</div>
@stop