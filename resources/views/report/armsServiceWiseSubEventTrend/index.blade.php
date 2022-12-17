@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.ARMS_SERVICE_WISE_EVENT_TREND')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'armsServiceWiseSubEventTrendReport/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                        <label class="control-label col-md-4" for="armsServiceId">@lang('label.ARMS_SERVICE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id ="showArmsService">
                            {!! Form::select('arms_service_id[]', $armsServiceList, $armsServiceIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'armsServiceId', 'data-width' => '100%']) !!}
                            <span class="text-danger">{{ $errors->first('arms_service_id') }}</span>
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
                        <label class="control-label col-md-4" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id ="showSubEvent">
                            {!! Form::select('sub_event_id[]', $subEventList, $subEventIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'subEventId', 'data-width' => '100%']) !!}
                            <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
                        </div>
                    </div>
                </div
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
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }}</strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="armsServiceWiseSubEventTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
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
    var armsServiceAllSelected = false;
    $('#armsServiceId').multiselect({
        numberDisplayed: 0,
        includeSelectAllOption: true,
        buttonWidth: 'inherit',
        maxHeight: 250,
        nonSelectedText: "@lang('label.SELECT_ARMS_SERVICE_OPT')",
        enableCaseInsensitiveFiltering: true,
        onSelectAll: function () {
            armsServiceAllSelected = true;
        },
        onChange: function () {
            armsServiceAllSelected = false;
        }
    });
//END:: Multiselect arms services


//START:: Multiselect Sub Event
    var subEventAllSelected = false;
    $('#subEventId').multiselect({
        numberDisplayed: 0,
        includeSelectAllOption: true,
        buttonWidth: 'inherit',
        maxHeight: 250,
        nonSelectedText: "@lang('label.SELECT_SUB_EVENT_OPT')",
        enableCaseInsensitiveFiltering: true,
        onSelectAll: function () {
            subEventAllSelected = true;
        },
        onChange: function () {
            subEventAllSelected = false;
        }
    });
//END:: Multiselect Sub Event

//START::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();
        $.ajax({
            url: "{{ URL::to('armsServiceWiseSubEventTrendReport/getCourse')}}",
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
                $('#showArmsService').html(res.html2);
                $('#eventId').html(res.showEventView);
                $('#showSubEvent').html(res.showSubEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect arms services
                var armsServiceAllSelected = false;
                $('#armsServiceId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_ARMS_SERVICE_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        armsServiceAllSelected = true;
                    },
                    onChange: function () {
                        armsServiceAllSelected = false;
                    }
                });
                //End:: Multiselect arms services

                //START:: Multiselect Sub Event
                var subEventAllSelected = false;
                $('#subEventId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_SUB_EVENT_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        subEventAllSelected = true;
                    },
                    onChange: function () {
                        subEventAllSelected = false;
                    }
                });
//END:: Multiselect Sub Event
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
            url: "{{ URL::to('armsServiceWiseSubEventTrendReport/getCourseWiseArmsServiceEvent')}}",
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
                $('#showArmsService').html(res.html);
                $('#eventId').html(res.showEventView);
                $('#showSubEvent').html(res.showSubEventView);
                $(".js-source-states").select2();
                //Start:: Multiselect arms services
                var armsServiceAllSelected = false;
                $('#armsServiceId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_ARMS_SERVICE_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        armsServiceAllSelected = true;
                    },
                    onChange: function () {
                        armsServiceAllSelected = false;
                    }
                });
                //End:: Multiselect arms services

                //START:: Multiselect Sub Event
                var subEventAllSelected = false;
                $('#subEventId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_SUB_EVENT_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        subEventAllSelected = true;
                    },
                    onChange: function () {
                        subEventAllSelected = false;
                    }
                });
//END:: Multiselect Sub Event
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
//START::Get Sub Event
    $(document).on("change", "#eventId", function () {
        var courseId = $("#courseId").val();
        var eventId = $("#eventId").val();
        $.ajax({
            url: "{{ URL::to('armsServiceWiseSubEventTrendReport/getSubEvent')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                event_id: eventId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showSubEvent').html(res.html);
                //START:: Multiselect Sub Event
                var subEventAllSelected = false;
                $('#subEventId').multiselect({
                    numberDisplayed: 0,
                    includeSelectAllOption: true,
                    buttonWidth: 'inherit',
                    maxHeight: 250,
                    nonSelectedText: "@lang('label.SELECT_SUB_EVENT_OPT')",
                    enableCaseInsensitiveFiltering: true,
                    onSelectAll: function () {
                        subEventAllSelected = true;
                    },
                    onChange: function () {
                        subEventAllSelected = false;
                    }
                });
//END:: Multiselect Sub Event
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
//END::Get Sub Event


//START :: Arms/Service Wise Trend Chart

    var maxMinArr = [];
    var counter1 = counter2 = max = min = 0;
<?php
if (!empty($selectedArmsServices)) {
    foreach ($selectedArmsServices as $armsServiceId => $armsServiceCode) {
        ?>
            counter2 = 0;
            maxMinArr[counter1] = [];
        <?php
        if (!empty($selectedSubEvents)) {
            foreach ($selectedSubEvents as $subEventId => $eventCode) {
                $max = !empty($armsSvcWiseMksArr[$armsServiceId][$subEventId]['max']) ? $armsSvcWiseMksArr[$armsServiceId][$subEventId]['max'] : 0;
                $min = !empty($armsSvcWiseMksArr[$armsServiceId][$subEventId]['min']) ? $armsSvcWiseMksArr[$armsServiceId][$subEventId]['min'] : 0;
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
    var armsServiceWiseMksOptions = {
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
if (!empty($selectedArmsServices)) {
    foreach ($selectedArmsServices as $armsServiceId => $armsServiceCode) {
        ?>
                    {
                        name: "{{$armsServiceCode}}",
                        data: [
        <?php
        if (!empty($selectedSubEvents)) {
            foreach ($selectedSubEvents as $subEventId => $eventCode) {
                $mks = !empty($armsSvcWiseMksArr[$armsServiceId][$subEventId]['mks_percent']) ? $armsSvcWiseMksArr[$armsServiceId][$subEventId]['mks_percent'] : 0;
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
if (!empty($selectedSubEvents)) {
    foreach ($selectedSubEvents as $subEventId => $eventCode) {
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
            }
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
            min: 0,
            max: 100,
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

    var armsServiceWiseMks = new ApexCharts(document.querySelector("#armsServiceWiseSubEventTrendChart"), armsServiceWiseMksOptions);
    armsServiceWiseMks.render();
//END :: Arms/Service Wise Trend Chart

});
</script>
@stop