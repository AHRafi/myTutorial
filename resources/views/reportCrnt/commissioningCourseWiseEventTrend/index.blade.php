@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.COMMISSIONING_COURSE_WISE_EVENT_TREND')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'commissioningCourseWiseEventTrendReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                        <label class="control-label col-md-4" for="commissioningCourseId">@lang('label.COMMISSIONING_COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id ="showCommissioningCourse">
                            {!! Form::select('commissioning_course_id[]', $commissioningCourseList, $commissioningCourseIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'commissioningCourseId', 'data-width' => '100%']) !!}
                            <span class="text-danger">{{ $errors->first('commissioning_course_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id ="showEvent">
                            {!! Form::select('event_id[]', $eventList, $eventIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'eventId', 'data-width' => '100%']) !!}
                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
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
                            {{__('label.TRAINING_YEAR')}} : <strong> {{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong> {{$courseList->name}} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} </strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="commissioningCourseWiseEventTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
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
//START:: Multiselect arms services
    var commissioningCourseAllSelected = false;
    $('#commissioningCourseId').multiselect({
        numberDisplayed: 0,
        includeSelectAllOption: true,
        buttonWidth: 'inherit',
        maxHeight: 250,
        nonSelectedText: "@lang('label.SELECT_COMMISSIONING_COURSE_OPT')",
        enableCaseInsensitiveFiltering: true,
        onSelectAll: function () {
            commissioningCourseAllSelected = true;
        },
        onChange: function () {
            commissioningCourseAllSelected = false;
        }
    });
//END:: Multiselect arms services


//START:: Multiselect Event
    var eventAllSelected = false;
    $('#eventId').multiselect({
        numberDisplayed: 0,
        includeSelectAllOption: true,
        buttonWidth: 'inherit',
        maxHeight: 250,
        nonSelectedText: "@lang('label.SELECT_EVENT_OPT')",
        enableCaseInsensitiveFiltering: true,
        onSelectAll: function () {
            eventAllSelected = true;
        },
        onChange: function () {
            eventAllSelected = false;
        }
    });
//END:: Multiselect Event

//START::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();
        $.ajax({
            url: "{{ URL::to('commissioningCourseWiseEventTrendReportCrnt/getCourse')}}",
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
                $('#showCommissioningCourse').html(res.html2);
                $('#showEvent').html(res.showEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect arms services
                var commissioningCourseAllSelected = false;
                $('#commissioningCourseId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_COMMISSIONING_COURSE_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        commissioningCourseAllSelected = true;
                    },
                    onChange: function () {
                        commissioningCourseAllSelected = false;
                    }
                });
                //End:: Multiselect arms services

                //Start:: Multiselect Event
                var eventAllSelected = false;
                $('#eventId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_EVENT_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        eventAllSelected = true;
                    },
                    onChange: function () {
                        eventAllSelected = false;
                    }
                });
                //End:: Multiselect Event
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        }); //ajax

    });
//END::Get Course


//START::Get Course Wise Arms Service & Event
    $(document).on("change", "#courseId", function () {
        var courseId = $("#courseId").val();
        $.ajax({
            url: "{{ URL::to('commissioningCourseWiseEventTrendReportCrnt/getTerm')}}",
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
                $('#showCommissioningCourse').html(res.html);
                $('#showEvent').html(res.showEventView);
                //Start:: Multiselect arms services
                var commissioningCourseAllSelected = false;
                $('#commissioningCourseId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_COMMISSIONING_COURSE_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        commissioningCourseAllSelected = true;
                    },
                    onChange: function () {
                        commissioningCourseAllSelected = false;
                    }
                });
                //End:: Multiselect arms services

                //Start:: Multiselect Event
                var eventAllSelected = false;
                $('#eventId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_EVENT_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        eventAllSelected = true;
                    },
                    onChange: function () {
                        eventAllSelected = false;
                    }
                });
                //End:: Multiselect Event
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
            url: "{{ URL::to('commissioningCourseWiseEventTrendReportCrnt/getCourseWiseCommissioningCourseEvent')}}",
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
                $('#showCommissioningCourse').html(res.html);
                $('#showEvent').html(res.showEventView);
                //Start:: Multiselect arms services
                var commissioningCourseAllSelected = false;
                $('#commissioningCourseId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_COMMISSIONING_COURSE_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        commissioningCourseAllSelected = true;
                    },
                    onChange: function () {
                        commissioningCourseAllSelected = false;
                    }
                });
                //End:: Multiselect arms services

                //Start:: Multiselect Event
                var eventAllSelected = false;
                $('#eventId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_EVENT_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        eventAllSelected = true;
                    },
                    onChange: function () {
                        eventAllSelected = false;
                    }
                });
                //End:: Multiselect Event
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
//END::Get Course Wise Arms Service & Event


//START :: Arms/Service Wise Trend Chart

    var maxMinArr = [];
    var counter1 = counter2 = max = min = 0;
<?php
if (!empty($selectedCommissioningCourses)) {
    foreach ($selectedCommissioningCourses as $commissioningCourseId => $commissioningCourseCode) {
        ?>
            counter2 = 0;
            maxMinArr[counter1] = [];
        <?php
        if (!empty($selectedEvents)) {
            foreach ($selectedEvents as $eventId => $eventCode) {
                $max = !empty($commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['max']) ? $commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['max'] : 0;
                $min = !empty($commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['min']) ? $commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['min'] : 0;
                ?>
                    max = <?php echo $max; ?>;
                    min = <?php echo $min; ?>;
                    maxMinArr[counter1][counter2] = [];
                    maxMinArr[counter1][counter2]['max'] = max;
                    maxMinArr[counter1][counter2]['min'] = min;
                    counter2++;
                <?php
            }
        }
        ?>
            counter1++;
        <?php
    }
}
?>
    var commissioningCourseWiseMksOptions = {
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
        colors: ['#1f441e','#ff0000','#440a67','#C62700','#ABC400','#26001b','#ff005c','#21209c','#04BC06','#013C38','#8f4f4f','#435560','#025955','#8c0000','#763857','#28527a','#413c69','#484018','#1687a7','#41584b','#dd9866','#16a596','#649d66','#7a4d1d','#630B0B','#FF5600','#AF00A0','#000000','#290262','#9D0233'],
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
                colors: ['#1f441e','#ff0000','#440a67','#C62700','#ABC400','#26001b','#ff005c','#21209c','#04BC06','#013C38','#8f4f4f','#435560','#025955','#8c0000','#763857','#28527a','#413c69','#484018','#1687a7','#41584b','#dd9866','#16a596','#649d66','#7a4d1d','#630B0B','#FF5600','#AF00A0','#000000','#290262','#9D0233'],
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
<?php
if (!empty($selectedCommissioningCourses)) {
    foreach ($selectedCommissioningCourses as $commissioningCourseId => $commissioningCourseCode) {
        ?>
                    {
                        name: "{{$commissioningCourseCode}}",
                        data: [
        <?php
        if (!empty($selectedEvents)) {
            foreach ($selectedEvents as $eventId => $eventCode) {
                $mks = !empty($commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['mks_percent']) ? $commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['mks_percent'] : 0;
                echo $mks . ',';
            }
        }
        ?>
                        ]
                    },
        <?php
    }
}
?>

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
if (!empty($selectedEvents)) {
    foreach ($selectedEvents as $eventId => $eventCode) {
        echo "'$eventCode',";
    }
}
?>
            ],
            title: {
                text: "@lang('label.EVENTS')",
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
                    var maxValue = parseFloat(maxMinArr[seriesIndex][dataPointIndex]['max']).toFixed(2);
                    var minValue = parseFloat(maxMinArr[seriesIndex][dataPointIndex]['min']).toFixed(2);
                    return parseFloat(val).toFixed(2) + "% (@lang('label.AVG')), " + maxValue + "% (@lang('label.MAX')), " + minValue + "% (@lang('label.MIN'))"
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
    var commissioningCourseWiseMksOptions2 = {
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
        colors: ['#1f441e','#ff0000','#440a67','#C62700','#ABC400','#26001b','#ff005c','#21209c','#04BC06','#013C38','#8f4f4f','#435560','#025955','#8c0000','#763857','#28527a','#413c69','#484018','#1687a7','#41584b','#dd9866','#16a596','#649d66','#7a4d1d','#630B0B','#FF5600','#AF00A0','#000000','#290262','#9D0233'],
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
                colors: ['#1f441e','#ff0000','#440a67','#C62700','#ABC400','#26001b','#ff005c','#21209c','#04BC06','#013C38','#8f4f4f','#435560','#025955','#8c0000','#763857','#28527a','#413c69','#484018','#1687a7','#41584b','#dd9866','#16a596','#649d66','#7a4d1d','#630B0B','#FF5600','#AF00A0','#000000','#290262','#9D0233'],
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
                name: "@lang('label.MAX')",
                data: [
<?php
if (!empty($selectedCommissioningCourses)) {
    foreach ($selectedCommissioningCourses as $commissioningCourseId => $commissioningCourseCode) {
        if (!empty($selectedEvents)) {
            foreach ($selectedEvents as $eventId => $eventCode) {
                $mks = !empty($commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['max']) ? $commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['max'] : 0;
                echo $mks . ',';
            }
        }
    }
}
?>
                ]
            },
            {
                name: "@lang('label.AVG')",
                data: [
<?php
if (!empty($selectedCommissioningCourses)) {
    foreach ($selectedCommissioningCourses as $commissioningCourseId => $commissioningCourseCode) {
        if (!empty($selectedEvents)) {
            foreach ($selectedEvents as $eventId => $eventCode) {
                $mks = !empty($commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['mks_percent']) ? $commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['mks_percent'] : 0;
                echo $mks . ',';
            }
        }
    }
}
?>
                ]
            },
            {
                name: "@lang('label.MIN')",
                data: [
<?php
if (!empty($selectedCommissioningCourses)) {
    foreach ($selectedCommissioningCourses as $commissioningCourseId => $commissioningCourseCode) {
        if (!empty($selectedEvents)) {
            foreach ($selectedEvents as $eventId => $eventCode) {
                $mks = !empty($commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['min']) ? $commissioningCourseWiseMksArr[$commissioningCourseId][$eventId]['min'] : 0;
                echo $mks . ',';
            }
        }
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
if (!empty($selectedCommissioningCourses)) {
    foreach ($selectedCommissioningCourses as $commissioningCourseId => $commissioningCourseCode) {
        echo "'$commissioningCourseCode',";
    }
}
?>
            ],
            title: {
                text: "@lang('label.COMMISSIONING_COURSES')",
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
    };
<?php
if (!empty($selectedEvents)) {
    if (sizeof($selectedEvents) > 1) {
        ?>
            var commissioningCourseWiseMks = new ApexCharts(document.querySelector("#commissioningCourseWiseEventTrendChart"), commissioningCourseWiseMksOptions);
        <?php
    } else {
        ?>
            var commissioningCourseWiseMks = new ApexCharts(document.querySelector("#commissioningCourseWiseEventTrendChart"), commissioningCourseWiseMksOptions2);
        <?php
    }
    ?>
        commissioningCourseWiseMks.render();
    <?php
}
?>
//END :: Arms/Service Wise Trend Chart

});
</script>
@stop