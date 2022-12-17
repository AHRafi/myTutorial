@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DS_EVENT_TREND')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'dsEventTrendReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM')</label>
                        <div class="col-md-8" id="showTerm">
                            {!! Form::select('term_id', $termList,  Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                            <span class="text-danger">{{ $errors->first('term_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id ="showEvent">
                            {!! Form::select('event_id', $eventList, Request::get('event_id'),  ['class' => 'form-control js-source-states', 'id' => 'eventId', 'data-width' => '100%']) !!}
                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group" id ="showSubEvent">
                        <label class="control-label col-md-4" for="subEventId">@lang('label.SUB_EVENT') :@if(sizeof($subEventList) > 1)<span class="text-danger"> *</span>@endif</label>
                        <div class="col-md-8">
                            {!! Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'),  ['class' => 'form-control js-source-states', 'id' => 'subEventId', 'data-width' => '100%']) !!}
                            {!! Form::hidden('has[sub_event]', sizeof($subEventList) > 1 ? 1 : 0) !!}
                            <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" id ="showSubSubEvent">
                        <label class="control-label col-md-4" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :@if(sizeof($subSubEventList) > 1)<span class="text-danger"> *</span>@endif</label>
                        <div class="col-md-8">
                            {!! Form::select('sub_sub_event_id', $subSubEventList, Request::get('sub_sub_event_id'),  ['class' => 'form-control js-source-states', 'id' => 'subSubEventId', 'data-width' => '100%']) !!}
                            {!! Form::hidden('has[sub_sub_event]', sizeof($subSubEventList) > 1 ? 1 : 0) !!}
                            <span class="text-danger">{{ $errors->first('sub_sub_event_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" id ="showSubSubSubEvent">
                        <label class="control-label col-md-4" for="subSubSubEventId">@lang('label.SUB_SUB_SUB_EVENT') :@if(sizeof($subSubSubEventList) > 1)<span class="text-danger"> *</span>@endif</label>
                        <div class="col-md-8">
                            {!! Form::select('sub_sub_sub_event_id', $subSubSubEventList, Request::get('sub_sub_sub_event_id'),  ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId', 'data-width' => '100%']) !!}
                            {!! Form::hidden('has[sub_sub_sub_event]', sizeof($subSubSubEventList) > 1 ? 1 : 0) !!}
                            <span class="text-danger">{{ $errors->first('sub_sub_sub_event_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cmId">@lang('label.CM') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id ="showCm">
                            {!! Form::select('cm_id[]', $cmArr, $cmIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'cmId', 'data-width' => '100%']) !!}
                            <span class="text-danger">{{ $errors->first('cm_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="">@lang('label.RANGE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            <div class="input-group bootstrap-touchspin width-inherit">
                                {!! Form::text('range_start', !empty(Request::get('range_start')) ? Request::get('range_start') : 0, ['id'=> 'rangeStart', 'class' => 'form-control integer-only text-input-width-100-per text-right','autocomplete' => 'off', 'max' => '100']) !!}
                                <span class="input-group-addon bootstrap-touchspin-postfix bold">&#45;</span>
                                {!! Form::text('range_end', !empty(Request::get('range_end')) ? Request::get('range_end') : 100, ['id'=> 'rangeEnd', 'class' => 'form-control integer-only text-input-width-100-per text-right','autocomplete' => 'off', 'max' => '100']) !!}
                            </div>
                            <span class="text-danger">{{ $errors->first('range_start') }}</span><br/>
                            <span class="text-danger">{{ $errors->first('range_end') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="form-group">
                        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" value="Show Filter Info" data-id="1">
                            <i class="fa fa-search"></i> @lang('label.GENERATE')
                        </button>
                    </div>
                </div>
            </div>
            @if(Request::get('generate') == 'true')
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList->name) ? $activeTrainingYearList->name : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList->name) != 0 ? $courseList->name : __('label.N_A') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} |</strong>
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }}</strong>
                            @if(!empty(Request::get('sub_event_id')))
                            <strong> |</strong>
                            {{__('label.SUB_EVENT')}} : <strong>{{ !empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0 ? $subEventList[Request::get('sub_event_id')] : __('label.N_A') }}</strong>
                            @endif
                            @if(!empty(Request::get('sub_sub_event_id')))
                            <strong> |</strong>
                            {{__('label.SUB_SUB_EVENT')}} : <strong>{{ !empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0 ? $subSubEventList[Request::get('sub_sub_event_id')] : __('label.N_A') }}</strong>
                            @endif
                            @if(!empty(Request::get('sub_sub_sub_event_id')))
                            <strong> |</strong>
                            {{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ !empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0 ? $subSubSubEventList[Request::get('sub_sub_sub_event_id')] : __('label.N_A') }}</strong>
                            @endif
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if(!empty($eventWiseDsMksArr))
                    <div id="dsEventTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
                    @else
                    <div class="alert alert-danger alert-dismissable">
                        <p>
                            <strong>
                                <i class="fa fa-bell-o fa-fw"></i> {!! __('label.THE_CHART_CANNOT_BE_SHOWN_PLEASE_LOCK_YOUR_MARKING_FOR_THIS_EVENT') !!}
                            </strong>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
    var options = {
        closeButton: true,
        debug: false,
        positionClass: "toast-bottom-right",
        onclick: null
    };
//START:: Multiselect CM
    var cmAllSelected = false;
    $('#cmId').multiselect({
        numberDisplayed: 0,
        includeSelectAllOption: true,
        buttonWidth: 'inherit',
        maxHeight: 250,
        nonSelectedText: "@lang('label.SELECT_CM_OPT')",
        enableCaseInsensitiveFiltering: true,
        onSelectAll: function () {
            cmAllSelected = true;
        },
        onChange: function () {
            cmAllSelected = false;
        }
    });
//END:: Multiselect CM

//START::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();
        $.ajax({
            url: "{{ URL::to('dsEventTrendReportCrnt/getCourse')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                training_year_id: trainingYearId
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#courseId').html(res.html);
                $('#termId').html(res.html1);
                $('#showCm').html(res.html2);
                $('#showEvent').html(res.showEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect CM
                var cmAllSelected = false;
                $('#cmId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_CM_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        cmAllSelected = true;
                    },
                    onChange: function () {
                        cmAllSelected = false;
                    }
                });
                //End:: Multiselect CM
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        }); //ajax

    });
//END::Get Course


//START::Get Course Wise Cm & Event
    $(document).on("change", "#courseId", function () {
        var courseId = $("#courseId").val();
        $.ajax({
            url: "{{ URL::to('dsEventTrendReportCrnt/getTerm')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#termId').html(res.html1);
                $('#showCm').html(res.html);
                $('#showEvent').html(res.showEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect Cm
                var cmAllSelected = false;
                $('#cmId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_CM_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        cmAllSelected = true;
                    },
                    onChange: function () {
                        cmAllSelected = false;
                    }
                });
                //End:: Multiselect Cm
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                App.unblockUI();
                var errorsHtml = '';
                if (jqXhr.status == 400) {
                    var errors = jqXhr.responseJSON.message;
                    $.each(errors, function (key, value) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                } else if (jqXhr.status == 401) {
                    toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                } else {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                }

            }
        }); //ajax
    });
    $(document).on("change", "#termId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        $.ajax({
            url: "{{ URL::to('dsEventTrendReportCrnt/getCourseWiseCmEvent')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showCm').html(res.html);
                $('#showEvent').html(res.showEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect Cm
                var cmAllSelected = false;
                $('#cmId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_CM_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        cmAllSelected = true;
                    },
                    onChange: function () {
                        cmAllSelected = false;
                    }
                });
                //End:: Multiselect Cm
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                App.unblockUI();
                var errorsHtml = '';
                if (jqXhr.status == 400) {
                    var errors = jqXhr.responseJSON.message;
                    $.each(errors, function (key, value) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                } else if (jqXhr.status == 401) {
                    toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                } else {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                }

            }
        }); //ajax
    });
//END::Get Course Wise Cm & Event
//START::Get Course Wise Cm & Sub Event
    $(document).on("change", "#eventId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        $.ajax({
            url: "{{ URL::to('dsEventTrendReportCrnt/getCourseWiseCmSubEvent')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
                event_id: eventId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showCm').html(res.html);
                $('#showSubEvent').html(res.showSubEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect Cm
                var cmAllSelected = false;
                $('#cmId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_CM_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        cmAllSelected = true;
                    },
                    onChange: function () {
                        cmAllSelected = false;
                    }
                });
                //End:: Multiselect Cm
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                App.unblockUI();
                var errorsHtml = '';
                if (jqXhr.status == 400) {
                    var errors = jqXhr.responseJSON.message;
                    $.each(errors, function (key, value) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                } else if (jqXhr.status == 401) {
                    toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                } else {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                }

            }
        }); //ajax
    });
//END::Get Course Wise Cm & Sub Event
//START::Get Course Wise Cm & Sub Sub Event
    $(document).on("change", "#subEventId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        var subEventId = $("#subEventId").val();
        $.ajax({
            url: "{{ URL::to('dsEventTrendReportCrnt/getCourseWiseCmSubSubEvent')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
                event_id: eventId,
                sub_event_id: subEventId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showCm').html(res.html);
                $('#showSubSubEvent').html(res.showSubSubEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect Cm
                var cmAllSelected = false;
                $('#cmId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_CM_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        cmAllSelected = true;
                    },
                    onChange: function () {
                        cmAllSelected = false;
                    }
                });
                //End:: Multiselect Cm
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                App.unblockUI();
                var errorsHtml = '';
                if (jqXhr.status == 400) {
                    var errors = jqXhr.responseJSON.message;
                    $.each(errors, function (key, value) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                } else if (jqXhr.status == 401) {
                    toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                } else {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                }

            }
        }); //ajax
    });
//END::Get Course Wise Cm & Sub Sub Event
//START::Get Course Wise Cm & Sub Sub Sub Event
    $(document).on("change", "#subSubEventId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        var subEventId = $("#subEventId").val();
        var subSubEventId = $("#subSubEventId").val();
        $.ajax({
            url: "{{ URL::to('dsEventTrendReportCrnt/getCourseWiseCmSubSubSubEvent')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
                event_id: eventId,
                sub_event_id: subEventId,
                sub_sub_event_id: subSubEventId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showCm').html(res.html);
                $('#showSubSubSubEvent').html(res.showSubSubSubEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect Cm
                var cmAllSelected = false;
                $('#cmId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_CM_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        cmAllSelected = true;
                    },
                    onChange: function () {
                        cmAllSelected = false;
                    }
                });
                //End:: Multiselect Cm
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                App.unblockUI();
                var errorsHtml = '';
                if (jqXhr.status == 400) {
                    var errors = jqXhr.responseJSON.message;
                    $.each(errors, function (key, value) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                } else if (jqXhr.status == 401) {
                    toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                } else {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                }

            }
        }); //ajax
    });
//END::Get Course Wise Cm & Sub Sub Sub Event
//START::Get Course Wise Cm
    $(document).on("change", "#subSubSubEventId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        var subEventId = $("#subEventId").val();
        var subSubEventId = $("#subSubEventId").val();
        var subSubSubEventId = $("#subSubSubEventId").val();
        $.ajax({
            url: "{{ URL::to('dsEventTrendReportCrnt/getCourseWiseCm')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
                event_id: eventId,
                sub_event_id: subEventId,
                sub_sub_event_id: subSubEventId,
                sub_sub_sub_event_id: subSubSubEventId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showCm').html(res.html);
                $(".js-source-states").select2();
                //Start:: Multiselect Cm
                var cmAllSelected = false;
                $('#cmId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_CM_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        cmAllSelected = true;
                    },
                    onChange: function () {
                        cmAllSelected = false;
                    }
                });
                //End:: Multiselect Cm
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                App.unblockUI();
                var errorsHtml = '';
                if (jqXhr.status == 400) {
                    var errors = jqXhr.responseJSON.message;
                    $.each(errors, function (key, value) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                } else if (jqXhr.status == 401) {
                    toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                } else {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                }

            }
        }); //ajax
    });
//END::Get Course Wise Cm


//START :: Cm Wise Event Trend Chart
    var dsMksOptions = {
        chart: {
            height: 400,
            type: 'line',
            shadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 1
            },
            toolbar: {
                show: false
            }
        },
        colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233'],
        dataLabels: {
            enabled: false,
            enabledOnSeries: undefined,
            formatter: function (val) {
                return parseFloat(val).toFixed(2)
            },
            textAnchor: 'middle',
            distributed: false,
            offsetX: 0,
            offsetY: -10,
            style: {
                fontSize: '12px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 'bold',
                colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233'],
            },
            background: {
                enabled: true,
                foreColor: '#fff',
                padding: 4,
                borderRadius: 2,
                borderWidth: 1,
                borderColor: '#fff',
                opacity: 0.9,
                dropShadow: {
                    enabled: false,
                    top: 1,
                    left: 1,
                    blur: 1,
                    color: '#000',
                    opacity: 0.45
                }
            },
            dropShadow: {
                enabled: false,
                top: 1,
                left: 1,
                blur: 1,
                color: '#000',
                opacity: 0.45
            }
        },
        stroke: {
            curve: 'smooth'
        },
        series: [

            {
                name: "<?php echo Auth::user()->official_name; ?> @lang('label.MKS_PERCENT')",
                data: [
<?php
if (!empty($selectedCms)) {
    foreach ($selectedCms as $cmId => $cmName) {
        $mks = !empty($dsPercentageArr[$cmId]['ds']) ? $dsPercentageArr[$cmId]['ds'] : 0;
        echo $mks . ',';
    }
}
?>
                ]
            },
            {
                name: "@lang('label.ALL_DS_AVG_MKS_PERCENT')",
                data: [
<?php
if (!empty($selectedCms)) {
    foreach ($selectedCms as $cmId => $cmName) {
        $mks = !empty($dsPercentageArr[$cmId]['over_all']) ? $dsPercentageArr[$cmId]['over_all'] : 0;
        echo $mks . ',';
    }
}
?>
                ]
            },
        ],
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                opacity: 0.5
            },
        },
        markers: {

            size: 6
        },
        xaxis: {
            categories: [
<?php
if (!empty($selectedCms)) {
    foreach ($selectedCms as $cmId => $cmName) {
        echo "'$cmName',";
    }
}
?>
            ],
            title: {
                text: "@lang('label.CM')",
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-xaxis-title',
                },
            }
        },
        yaxis: {
            title: {
                text: "@lang('label.MKS_PERCENT')",
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
            min: <?php echo!empty(Request::get('range_start')) ? Request::get('range_start') : 0; ?>,
            max: <?php echo!empty(Request::get('range_end')) ? Request::get('range_end') : 100; ?>,
//            forceNiceScale: true,
            labels: {
                show: true,
                align: 'right',
                minWidth: 0,
                maxWidth: 160,
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 400,
                    cssClass: 'apexcharts-xaxis-title',
                },
                offsetX: 0,
                offsetY: 0,
                rotate: 0,
                formatter: (val) => {
                    return parseFloat(val).toFixed(2)
                },
            },
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return parseFloat(val).toFixed(2) + "%"
                }
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            floating: false,
            offsetY: 0,
            offsetX: -5
        }
    }

    var dsMks = new ApexCharts(document.querySelector("#dsEventTrendChart"), dsMksOptions);
    dsMks.render();
//END :: Cm Wise Event Trend Chart

});
</script>
@stop