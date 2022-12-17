@extends('layouts.default.master')
@section('data_count')
    <div class="col-md-12">
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i>@lang('label.CREATE_LESSON')
                </div>
            </div>
            <div class="portlet-body form">
                {!! Form::open(['group' => 'form', 'url' => 'lesson', 'files' => true, 'class' => 'form-horizontal']) !!}
                {!! Form::hidden('page', Helper::queryPageStr($qpArr)) !!}
                {{ csrf_field() }}
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-7">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="title">@lang('label.LESSON_TITLE') :<span
                                        class="text-danger"> *</span></label>
                                <div class="col-md-8">
                                    {!! Form::text('title', null, ['id' => 'title', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">@lang('label.DATE_OF_EVAL') :<span class="text-danger">
                                        *</span></label>
                                <div class="col-md-8">
                                    <div class="input-group date datepicker2">
                                        {!! Form::text('eval_date', Request::get('eval_date') ?? null, [
                                            'class' => 'form-control',
                                            'id' => 'dobFrom',
                                            'placeholder' => 'DD/MM/YYYY',
                                            'readonly' => '',
                                        ]) !!}
                                        <span class="input-group-btn">
                                            <button class="btn default reset-date" type="button" remove="dobFrom">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <button class="btn default date-set" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('eval_date') }}</span>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">@lang('label.DEADLINE_OF_EVAL') :<span class="text-danger">
                                        *</span></label>
                                <div class="col-md-8">
                                    <div class="input-group date datepicker2">
                                        {!! Form::text('eval_deadline', Request::get('eval_deadline') ?? null, [
                                            'class' => 'form-control',
                                            'id' => 'dobFrom',
                                            'placeholder' => 'DD/MM/YYYY',
                                            'readonly' => '',
                                        ]) !!}
                                        <span class="input-group-btn">
                                            <button class="btn default reset-date" type="button" remove="dobFrom">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <button class="btn default date-set" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('eval_deadline') }}</span>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4">@lang('label.CONSIDER_GS_FEEDBACK') :<span class="text-danger">
                                    </span></label>
                                <div class="col-md-8 padding-left-0">
                                    <div class="md-checkbox">
                                        {!! Form::checkbox('consider_gs_feedback', 1, null, [
                                            'class' => 'form-control',
                                            'id' => 'considerGsFeedback',
                                            'class' => 'md-check ',
                                        ]) !!}
                                        <label for="considerGsFeedback">
                                            <span class="inc"></span>
                                            <span class="check mark-caheck tooltips"></span>
                                            <span class="box mark-caheck tooltips"></span>
                                        </label>
                                        <span class="padding-left-10"> @lang('label.PUT_TICK_TO_CONSIDER_GS_FB')</span>
                                    </div>
                                </div>
                            </div>

                            <div id="order">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="order">@lang('label.ORDER') :<span
                                            class="text-danger"> *</span></label>
                                    <div class="col-md-8">
                                        {!! Form::select('order', $orderList, $lastOrderNumber, [
                                            'class' => 'form-control js-source-states',
                                            'id' => 'order',
                                        ]) !!}
                                        <span class="text-danger">{{ $errors->first('order') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4" for="status">@lang('label.STATUS') :</label>
                                <div class="col-md-8">
                                    {!! Form::select('status', ['1' => __('label.ACTIVE'), '2' => __('label.INACTIVE')], '1', [
                                        'class' => 'form-control',
                                        'id' => 'status',
                                    ]) !!}
                                    <span class="text-danger">{{ $errors->first('status') }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-4 col-md-8">
                            <button class="btn btn-circle green" type="submit">
                                <i class="fa fa-check"></i> @lang('label.SUBMIT')
                            </button>
                            <a href="{{ URL::to('/lesson' . Helper::queryPageStr($qpArr)) }}"
                                class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@stop
