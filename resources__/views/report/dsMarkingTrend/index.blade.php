@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DS_MARKING_TREND')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'dsMarkingTrendReport/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::select('training_year_id', $activeTrainingYearList, Request::get('training_year_id'), ['class' => 'form-control js-source-states', 'id' => 'trainingYearId']) !!}
                            <span class="text-danger">{{ $errors->first('training_year_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::select('course_id', $courseList, Request::get('course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
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
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
                        </div>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger required-sub-event"></span></label>
                        <div class="col-md-8">
                            {!! Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
                            <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :<span class="text-danger required-sub-sub-event"></span></label>
                        <div class="col-md-8">
                            {!! Form::select('sub_sub_event_id', $subSubEventList, Request::get('sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']) !!}
                            <span class="text-danger">{{ $errors->first('sub_sub_event_id') }}</span>
                        </div>
                    </div>
                </div>   
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="subSubSubEventId">@lang('label.SUB_SUB_SUB_EVENT') :<span class="text-danger required-sub-sub-sub-event"></span></label>
                        <div class="col-md-8">
                            {!! Form::select('sub_sub_sub_event_id', $subSubSubEventList, Request::get('sub_sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId']) !!}
                            <span class="text-danger">{{ $errors->first('sub_sub_sub_event_id') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="dsId">@lang('label.DS') :</label>
                        <div class="col-md-8" id ="showDs">
                            {!! Form::select('ds_id[]', $dsList, $dsIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'dsId', 'data-width' => '100%']) !!}
                            <span class="text-danger">{{ $errors->first('ds_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="threshold">@lang('label.THRESHOLD') :<span class="text-danger"> </span></label>
                        <div class="col-md-8">
                            {!! Form::text('threshold', Request::get('threshold'), ['class' => 'form-control integer-decimal-only text-right', 'id' => 'threshold']) !!}
                            <span class="text-danger">{{ $errors->first('threshold') }}</span>
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


                <div class="col-md-12 text-center">
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
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} |</strong>
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }} |</strong>
                            {{__('label.AVG_MKS_LINE')}} : <strong>{{ Helper::numberFormat2Digit($totalDsAvgLine) . '%' }} |</strong>
                            {{__('label.THRESHOLD')}} : <strong>{{ !empty(Request::get('threshold')) ? Helper::numberFormat2Digit(Request::get('threshold')) : 0.00 }}% </strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div id="dsMarkingTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
                </div>

                <div class="col-md-5">
                    <div class="max-height-500 table-responsive webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center" rowspan="2">@lang('label.SL_NO')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.DS')</th>
                                    <th class="vcenter text-center" rowspan="2">@lang('label.AVG_MKS') (%)</th>
                                    <th class="vcenter text-center" colspan="2">@lang('label.DEVIATION') (%)</th>
                                </tr>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.AVG_LINE')</th>
                                    <th class="vcenter text-center">@lang('label.THRESHOLD')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($selectedDs))
                                <?php $sl = 0; ?>
                                @foreach($selectedDs as $dsId => $dsName)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">{!! $dsName !!}</td>
                                    <?php
                                    $mks = !empty($dsMksArr[$dsId]['total_mks_percent']) ? $dsMksArr[$dsId]['total_mks_percent'] : 0;
                                    $threshold = !empty(Request::get('threshold')) ? Request::get('threshold') : 0;
                                    $deviation = $mks - $totalDsAvgLine;
                                    $thresholdDeviation = $mks - $threshold;
                                    $color = 'green';
                                    $sign = '+';
                                    $color2 = 'green';
                                    $sign2 = '+';
                                    if ($deviation < 0) {
                                        $color = 'danger';
                                        $sign = '';
                                    }
                                    if ($thresholdDeviation < 0) {
                                        $color2 = 'danger';
                                        $sign2 = '';
                                    }
                                    ?>
                                    <td class="vcenter text-right">{!! Helper::numberFormat2Digit($mks) . '%' !!}</td>
                                    <td class="vcenter text-right">{!! '<span class="text-'.$color.'">' . $sign .Helper::numberFormat2Digit($deviation) . '%</span>' !!}</td>
                                    <td class="vcenter text-{{!empty($threshold) ? 'right' : 'center'}}">
                                        {!! !empty($threshold) ? '<span class="text-'.$color2.'">' . $sign2 .Helper::numberFormat2Digit($thresholdDeviation) . '%</span>' : '--' !!}
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <th class="vcenter" colspan="5">@lang('label.NO_DATA_FOUND')</th>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
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
//START:: Multiselect Ds
    var dsAllSelected = false;
    $('#dsId').multiselect({
        numberDisplayed: 0,
        includeSelectAllOption: true,
        buttonWidth: 'inherit',
        maxHeight: 250,
        nonSelectedText: "@lang('label.SELECT_DS_OPT')",
        enableCaseInsensitiveFiltering: true,
        onSelectAll: function () {
            dsAllSelected = true;
        },
        onChange: function () {
            dsAllSelected = false;
        }
    });
//END:: Multiselect Ds


//START::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();

        $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
        $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
        $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
        $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
        $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
        $.ajax({
            url: "{{ URL::to('dsMarkingTrendReportCrnt/getCourse')}}",
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
                $('#showDs').html(res.html2);
                $('#eventId').html(res.showEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect Dss
                var dsAllSelected = false;
                $('#dsId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_DS_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        dsAllSelected = true;
                    },
                    onChange: function () {
                        dsAllSelected = false;
                    }
                });
                //End:: Multiselect Ds
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        }); //ajax

    });
//END::Get Course


//START::Get Course Wise Ds & Event
    $(document).on("change", "#courseId", function () {
        var courseId = $("#courseId").val();

        $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
        $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
        $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
        $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
        $.ajax({
            url: "{{ URL::to('dsMarkingTrendReport/getTerm')}}",
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
                $('#showDs').html(res.html2);
                $('#eventId').html(res.showEventView);
                //Start:: Multiselect Ds
                var dsAllSelected = false;
                $('#dsId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_DS_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        dsAllSelected = true;
                    },
                    onChange: function () {
                        dsAllSelected = false;
                    }
                });
                //End:: Multiselect Ds
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
        $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
        $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
        $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
        $.ajax({
            url: "{{ URL::to('dsMarkingTrendReport/getCourseWiseDsEvent')}}",
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
                $('#termId').html(res.html1);
                $('#showDs').html(res.html);
                $('#eventId').html(res.showEventView);
                //Start:: Multiselect Ds
                var dsAllSelected = false;
                $('#dsId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_DS_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        dsAllSelected = true;
                    },
                    onChange: function () {
                        dsAllSelected = false;
                    }
                });
                //End:: Multiselect Ds
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
    $(document).on("change", "#eventId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
        $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");

        if (eventId == 0) {
            $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
            return false;
        }
        $.ajax({
            url: "{{ URL::to('dsMarkingTrendReport/getCourseWiseDsSubEvent')}}",
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
                $('#termId').html(res.html1);
                $('#showDs').html(res.html);
                $('#subEventId').html(res.showSubEventView);
                //Start:: Multiselect Ds
                var dsAllSelected = false;
                $('#dsId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_DS_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        dsAllSelected = true;
                    },
                    onChange: function () {
                        dsAllSelected = false;
                    }
                });
                //End:: Multiselect Ds
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

    $(document).on("change", "#subEventId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        var subEventId = $("#subEventId").val();
        $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
        if (subEventId == 0) {
            $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
            return false;
        }
        $.ajax({
            url: "{{ URL::to('dsMarkingTrendReport/getCourseWiseDsSubSubEvent')}}",
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
                $('#termId').html(res.html1);
                $('#showDs').html(res.html);
                $('#subSubEventId').html(res.showSubSubEventView);
                //Start:: Multiselect Ds
                var dsAllSelected = false;
                $('#dsId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_DS_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        dsAllSelected = true;
                    },
                    onChange: function () {
                        dsAllSelected = false;
                    }
                });
                //End:: Multiselect Ds
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

    $(document).on("change", "#subSubEventId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        var subEventId = $("#subEventId").val();
        var subSubEventId = $("#subSubEventId").val();
        if (subSubEventId == 0) {
            $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
            return false;
        }
        $.ajax({
            url: "{{ URL::to('dsMarkingTrendReport/getCourseWiseDsSubSubSubEvent')}}",
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
                $('#termId').html(res.html1);
                $('#showDs').html(res.html);
                $('#subSubSubEventId').html(res.showSubSubSubEventView);
                //Start:: Multiselect Ds
                var dsAllSelected = false;
                $('#dsId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_DS_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        dsAllSelected = true;
                    },
                    onChange: function () {
                        dsAllSelected = false;
                    }
                });
                //End:: Multiselect Ds
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

    $(document).on("change", "#subSubSubEventId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        var subEventId = $("#subEventId").val();
        var subSubEventId = $("#subSubEventId").val();
        var subSubSubEventId = $("#subSubSubEventId").val();
        if (eventId == 0) {
            $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
            return false;
        }
        $.ajax({
            url: "{{ URL::to('dsMarkingTrendReport/getCourseWiseDs')}}",
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
                $('#termId').html(res.html1);
                $('#showDs').html(res.html);
//                $('#subSubSubEventId').html(res.showSubSubSubEventView);
                //Start:: Multiselect Ds
                var dsAllSelected = false;
                $('#dsId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_DS_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        dsAllSelected = true;
                    },
                    onChange: function () {
                        dsAllSelected = false;
                    }
                });
                //End:: Multiselect Ds
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
//END::Get Course Wise Ds


//START :: Ds Wise Trend Chart
    var avgLine = <?php echo $totalDsAvgLine; ?>;
    var thresoldLine = <?php echo!empty(Request::get('threshold')) ? Request::get('threshold') : 0 ?>;
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
        annotations: {
            yaxis: [{
                    y: parseFloat(avgLine).toFixed(2),
                    borderColor: '#FF0000',
                    strokeDashArray: 0,
                    label: {
                        borderColor: '#FF0000',
                        borderWidth: 1,
                        borderRadius: 2,
                        textAnchor: 'end',
                        position: 'right',
                        offsetX: -5,
                        offsetY: 5,
                        style: {
                            color: '#fff',
                            background: '#FF0000',
                            fontSize: '12px',
                            fontWeight: 900,
                            cssClass: 'apexcharts-yaxis-annotation-label',
                            padding: {
                                left: 5,
                                right: 5,
                                top: 0,
                                bottom: 2,
                            }
                        },
                        text: "@lang('label.AVG_LINE'): " + parseFloat(avgLine).toFixed(2) + '%',
                    }
                }, {
                    y: parseFloat(thresoldLine).toFixed(2),
                    borderColor: '#4f218a',
                    strokeDashArray: 0,
                    label: {
                        borderColor: '#4f218a',
                        borderWidth: 1,
                        borderRadius: 2,
                        textAnchor: 'end',
                        position: 'right',
                        offsetX: -5,
                        offsetY: 5,
                        style: {
                            color: '#fff',
                            background: '#4f218a',
                            fontSize: '12px',
                            fontWeight: 900,
                            cssClass: 'apexcharts-yaxis-annotation-label',
                            padding: {
                                left: 5,
                                right: 5,
                                top: 0,
                                bottom: 2,
                            }
                        },
                        text: "@lang('label.THRESHOLD'): " + parseFloat(thresoldLine).toFixed(2) + '%',
                    }
                },
            ],
        },
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
            curve: 'straight'
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '15%',
                endingShape: 'rounded',
                distributed: true,
                dataLabels: {
                    position: 'top', // top, center, bottom
                },
//                colors: {
//                    ranges: [{
//                            from: 0,
//                            to: 0,
//                            colors: ['#1BA39C', '#C49F47', '#5E738B', '#7F6084', '#4B77BE', '#E35B5A', '#F2784B', '#369EAD', '#5E738B', '#9A12B3', '#E87E04', '#D91E18', '#8E44AD', '#555555'],
//                        }],
//                    backgroundBarColors: [],
//                    backgroundBarOpacity: 1,
//                    backgroundBarRadius: 0,
//                },
            }
        },
        series: [
            {
                name: "@lang('label.AVG_MKS')",
                type: 'line',
                data: [
<?php
if (!empty($selectedDs)) {
    foreach ($selectedDs as $dsId => $dsName) {
        $mks = !empty($dsMksArr[$dsId]['total_mks_percent']) ? $dsMksArr[$dsId]['total_mks_percent'] : 0;
        echo $mks . ',';
    }
}
?>
                ],
//                colors: ['#1BA39C', '#C49F47', '#5E738B', '#7F6084', '#4B77BE', '#E35B5A', '#F2784B', '#369EAD', '#5E738B', '#9A12B3', '#E87E04', '#D91E18', '#8E44AD', '#555555'],

            },
            {
                name: "@lang('label.AVG_MKS')",
                type: 'bar',
                data: [
<?php
if (!empty($selectedDs)) {
    foreach ($selectedDs as $dsId => $dsName) {
        $mks = !empty($dsMksArr[$dsId]['total_mks_percent']) ? $dsMksArr[$dsId]['total_mks_percent'] : 0;
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
if (!empty($selectedDs)) {
    foreach ($selectedDs as $dsId => $dsName) {
        echo "'$dsName',";
    }
}
?>
            ],
            title: {
                text: "@lang('label.DS')",
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
            labels: {
                show: true,
                rotate: -45,
                rotateAlways: true,
                hideOverlappingLabels: false,
                showDuplicates: true,
                trim: true,
                minHeight: 100,
                maxHeight: 180,
                style: {
                    color: undefined,
                    fontSize: '10px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
        },
        yaxis: {
            title: {
                text: "@lang('label.AVG_MKS_PERCENT')",
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
                min: <?php echo !empty(Request::get('range_start')) ? Request::get('range_start') : 0;?>,
                max: <?php echo !empty(Request::get('range_end')) ? Request::get('range_end') : 100;?>,
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
                formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
                    var deviation = val - avgLine;
                    var color = 'green';
                    var sign = '+';
                    if (deviation < 0) {
                        color = 'danger';
                        sign = '';
                    }
                    var thresholdDeviation = val - thresoldLine;
                    var color2 = 'green';
                    var sign2 = '+';
                    if (thresholdDeviation < 0) {
                        color2 = 'danger';
                        sign2 = '';
                    }

                    if (seriesIndex == 0) {
                        return parseFloat(val).toFixed(2) + "% (@lang('label.DEVIATION'): <span class='text-" + color + "'>" + sign + parseFloat(deviation).toFixed(2) + "% (@lang('label.AVG_LINE'))</span>, <span class='text-" + color2 + "'>" + sign2 + parseFloat(thresholdDeviation).toFixed(2) + "% (@lang('label.THRESHOLD'))</span>)";
                }
                }
            },
            x: {
                formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
                    if (seriesIndex == 0) {
                        return val
                }
                }
            }
        },
        legend: {
            show: false,
            position: 'bottom',
            horizontalAlign: 'center',
            floating: false,
            offsetY: 0,
            offsetX: -5
        }
    };
    var dsMks = new ApexCharts(document.querySelector("#dsMarkingTrendChart"), dsMksOptions);
    dsMks.render();
//END :: Ds Wise Trend Chart

});
</script>
@stop