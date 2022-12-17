@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EVENT_AVG_TREND')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'eventAvgTrendReport/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} </strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="eventAvgTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
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
url: "{{ URL::to('eventAvgTrendReport/getCourse')}}",
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
        $('#termId').html(res.html2);
        $('#showEvent').html(res.showEventView);
        $(".js-source-states").select2();
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

//START::Get Term
$(document).on("change", "#courseId", function () {
var courseId = $("#courseId").val();
$.ajax({
url: "{{ URL::to('eventAvgTrendReport/getTerm')}}",
        type: "POST",
        dataType: "json",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
        course_id: courseId
        },
        beforeSend: function () {
        App.blockUI({boxed: true});
        },
        success: function (res) {
        $('#termId').html(res.html);
        $('#showEvent').html(res.showEventView);
        $(".js-source-states").select2();
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
//END::Get Term


//START::Get Course Wise Event
$(document).on("change", "#termId", function () {
var courseId = $("#courseId").val();
var termId = $("#termId").val();
$.ajax({
url: "{{ URL::to('eventAvgTrendReport/getCourseWiseEvent')}}",
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
        $('#showEvent').html(res.showEventView);
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
//END::Get Course Wise Event


//START :: Event Avg Trend Chart

var maxMinArr = [];
var counter2 = max = min = 0;
<?php
if (!empty($selectedEvents)) {
    foreach ($selectedEvents as $eventId => $eventCode) {

        $max = !empty($eventAvgMksArr[$eventId]['max']) ? $eventAvgMksArr[$eventId]['max'] : 0;
        $min = !empty($eventAvgMksArr[$eventId]['min']) ? $eventAvgMksArr[$eventId]['min'] : 0;
        ?>
        max = <?php echo $max; ?>;
        min = <?php echo $min; ?>;
        maxMinArr[counter2] = [];
        maxMinArr[counter2]['max'] = max;
        maxMinArr[counter2]['min'] = min;
        counter2++;
        <?php
    }
}
?>

var eventAvgMksOptions = {
chart: {
height: 400,
        type: "<?php echo!empty($selectedEvents) && sizeof($selectedEvents) > 1 ? 'line' : 'bar'; ?>",
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
<?php
if (!empty($selectedEvents) && sizeof($selectedEvents) == 1) {
    ?>
    plotOptions: {
    bar: {
    horizontal: false,
            columnWidth: '15%',
            endingShape: 'rounded',
            distributed: true,
            dataLabels: {
            position: 'top', // top, center, bottom
            },
    },
    },
    <?php
}
?>
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
                offsetY: - 10,
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
<?php ?>
        {
        name: "@lang('label.MKS')",
                data: [
<?php
if (!empty($selectedEvents)) {
    if (sizeof($selectedEvents) > 1) {
        foreach ($selectedEvents as $eventId => $eventCode) {
            $mks = !empty($eventAvgMksArr[$eventId]['mks_percent']) ? $eventAvgMksArr[$eventId]['mks_percent'] : 0;
            echo $mks . ',';
        }
    } else {
        foreach ($selectedEvents as $eventId => $eventCode) {
            $mks = !empty($eventAvgMksArr[$eventId]['mks_percent']) ? $eventAvgMksArr[$eventId]['mks_percent'] : 0;
            $max = !empty($eventAvgMksArr[$eventId]['max']) ? $eventAvgMksArr[$eventId]['max'] : 0;
            $min = !empty($eventAvgMksArr[$eventId]['min']) ? $eventAvgMksArr[$eventId]['min'] : 0;
            echo $max . ',' . $mks . ',' . $min;
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
if (!empty($selectedEvents)) {
    if (sizeof($selectedEvents) > 1) {
        foreach ($selectedEvents as $eventId => $eventCode) {
            echo "'$eventCode',";
        }
    } else {
        ?>
                "@lang('label.MAX')", "@lang('label.AVG')", "@lang('label.MIN')"
        <?php
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
<?php
if (!empty($selectedEvents)) {
    if (sizeof($selectedEvents) > 1) {
        ?>
                formatter: function (val, { series, dataPointIndex, w }) {
                var maxValue = parseFloat(maxMinArr[dataPointIndex]['max']).toFixed(2);
                var minValue = parseFloat(maxMinArr[dataPointIndex]['min']).toFixed(2);
                return parseFloat(val).toFixed(2) + "% (@lang('label.AVG')), " + maxValue + "% (@lang('label.MAX')), " + minValue + "% (@lang('label.MIN'))"
                }
        <?php
    } else {
        ?>
                formatter: function (val) {
                return parseFloat(val).toFixed(2) + "%"
                }
        <?php
    }
}
?>

        }
        },
        legend: {
        position: 'bottom',
                horizontalAlign: 'center',
                floating: false,
                offsetY: 0,
                offsetX: - 5
        }
}

var eventAvgMks = new ApexCharts(document.querySelector("#eventAvgTrendChart"), eventAvgMksOptions);
eventAvgMks.render();
//END :: Event Avg Trend Chart

});
</script>
@stop