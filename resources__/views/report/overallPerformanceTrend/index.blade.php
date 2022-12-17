@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.OVERALL_PERFORMANCE_TREND')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'overallPerformanceTrendReport/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                <div class="col-md-6">
                    <div id="overallPerformanceTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
                </div>
                <div class="col-md-6">
                    <div id="overallPerformanceTrendDonutChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
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

//START::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();
        $.ajax({
            url: "{{ URL::to('overallPerformanceTrendReport/getCourse')}}",
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
                $(".js-source-states").select2();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        }); //ajax

    });
//END::Get Course


//START :: Overall Performance Trend Chart
    var overallMksOptions = {
        chart: {
            height: 400,
            type: 'area',
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
                return val
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
                name: "@lang('label.NO_OF_CM')",
                data: [
<?php
if (!empty($gradeList)) {
    foreach ($gradeList as $gradeId => $gradeName) {
        $noOfCm = !empty($overallMksArr[$gradeId]) ? $overallMksArr[$gradeId] : 0;
        echo $noOfCm . ',';
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
if (!empty($gradeList)) {
    foreach ($gradeList as $gradeId => $gradeName) {
        echo "'$gradeName',";
    }
}
?>
            ],
            title: {
                text: "@lang('label.GRADES')",
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
                text: "@lang('label.NO_OF_CM')",
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
            min: 0,
            max: <?php echo!empty($maxCm) ? $maxCm + 1 : 0; ?>,
            forceNiceScale: true,
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
                    return val
                },
            },
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val
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

    var overallPerformanceTrendChart = new ApexCharts(document.querySelector("#overallPerformanceTrendChart"), overallMksOptions);
    overallPerformanceTrendChart.render();
//END :: Overall Performance Trend Chart


//*************** START :: Overall Performance Donut Chart **********//
var overallPerformanceTrendDonutChartOptions = {
series: [
<?php
if (!empty($gradeList)) {
    foreach ($gradeList as $gradeId => $gradeName) {
        $noOfCm = !empty($overallMksArr[$gradeId]) ? $overallMksArr[$gradeId] : 0;
        echo $noOfCm . ',';
    }
}
?>
],
        labels: [
<?php
if (!empty($gradeList)) {
    foreach ($gradeList as $gradeId => $gradeName) {
        echo "'$gradeName', ";
    }
}
?>
        ],
        chart: {
        width: 415,
                type: 'donut',
        },
        plotOptions: {
        pie: {
        startAngle: - 90,
                endAngle: 270
        }
        },
        colors: ["#1BA39C", "#203354", "#5C9BD1", "#8E44AD", "#525E64"],
        dataLabels: {
        enabled: true
        },
        fill: {
        type: 'gradient',
        },
        legend: {
            position: 'bottom',
        formatter: function(val, opts) {
        return val + ": " + opts.w.globals.series[opts.seriesIndex]
        }
        },
        title: {
        text: ''
        },
        responsive: [{
        breakpoint: 480,
                options: {
                chart: {
                width: 200
                },
                        legend: {
                        position: 'bottom'
                        }
                }
        }]
        };
var overallPerformanceTrendDonutChart = new ApexCharts(document.querySelector("#overallPerformanceTrendDonutChart"), overallPerformanceTrendDonutChartOptions);
overallPerformanceTrendDonutChart.render();
//***************END :: Overall Performance Donut Chart **********//

});
</script>
@stop