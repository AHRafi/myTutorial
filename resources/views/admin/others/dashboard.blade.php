@extends('layouts.default.master')
@section('data_count')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<div class="portlet-body">
    <!--<div class="page-bar bg-default">
        <ul class="page-breadcrumb margin-top-10">
            <li>
                <a href="{{url('dashboard')}}">@lang('label.DASHBOARD')</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span class="bold" style="color: #006400">@lang('label.WELCOME_TO_RECRUIT_TRAINING_MANAGEMENT_SOFTWARE_STCS')</span>
            </li>
        </ul>
        <div class="page-toolbar margin-top-15">
            <h5 class="dashboard-date bold" style="color: #006400"><span class="icon-calendar"></span> Today is <span class="bold" style="color: #006400">{!! date('d F Y') !!}</span> </h5>   
        </div>
    </div>-->
    @if(!$majorEvent->isEmpty())
    <div class="dashboard-scroller">
        <div class="marquee">

            <?php
            $str = '';
            ?>
            @foreach($majorEvent as $item)
            <?php $str .= '<i class="fa fa-envelope-o"></i> ' . $item->name . ' (' . Helper::formatDate2($item->from_date) . __('label.TO') . Helper::formatDate2($item->to_date) . ')' . ' || '; ?>
            @endforeach
            <?php
            echo trim($str, " || ");
            ?>

        </div>
    </div>
    <div class="clear-fix"></div>
</div>
@endif

@if(!empty($userDashboard))
<?php
$col1Slider = '';
if(!empty($dashboardLayout[1]) && in_array(1, $dashboardLayout[1])){
    $col1Slider = 'slider-div';
}

?>
<div class="row margin-top-20 {{$col1Slider}}">
    <div class="col-md-6">
        @if(!empty($dashboardLayout[1][0]))
        @include('admin.layoutContent.content'.$dashboardLayout[1][0])
        @endif
    </div>
    <div class="col-md-6">
        @if(!empty($dashboardLayout[2][0]))
        @include('admin.layoutContent.content'.$dashboardLayout[2][0])
        @endif
    </div>
</div>
<div class="row {{$col1Slider}}">
    <div class="col-md-6">
        @if(!empty($dashboardLayout[1][1]))
        @include('admin.layoutContent.content'.$dashboardLayout[1][1])
        @endif
    </div>
    <div class="col-md-6">
        @if(!empty($dashboardLayout[2][1]))
        @include('admin.layoutContent.content'.$dashboardLayout[2][1])
        @endif
    </div>
</div>
<div class="row {{$col1Slider}}">
    <div class="col-md-6">
        @if(!empty($dashboardLayout[1][2]))
        @include('admin.layoutContent.content'.$dashboardLayout[1][2])
        @endif
    </div>
    <div class="col-md-6">
        @if(!empty($dashboardLayout[2][2]))
        @include('admin.layoutContent.content'.$dashboardLayout[2][2])
        @endif
    </div>
</div>
<div class="row {{$col1Slider}}">
    <div class="col-md-6">
        @if(!empty($dashboardLayout[1][3]))
        @include('admin.layoutContent.content'.$dashboardLayout[1][3])
        @endif
    </div>
    <div class="col-md-6">
        @if(!empty($dashboardLayout[2][3]))
        @include('admin.layoutContent.content'.$dashboardLayout[2][3])
        @endif
    </div>
</div>
@else
<!--- START:: Slider Area ------->
<div class="row slider-div">
    <div class="col-md-7">
        @include('layouts.default.slider')
    </div>
    <div class="col-md-5">
        <!--major events-->
        <div class="border border-default" >
            <div class="font-white dashboard-title-back-style">
                <h4 class="text-center bold">@lang('label.MAJOR_EVENTS')</h4>
            </div>
            <div>
                <marquee direction="up" onmouseover="this.stop();" onmouseout="this.start();" class="dash-marquee">
                    @if(!$majorEvent->isEmpty())
                    @foreach($majorEvent as $item)
                    <ul class="feeds margin-bottom-10 bold">
                        <li style="border-radius:4% !important">
                            <div class="col1">
                                <div class="cont">
                                    <div class="cont-col2">
                                        <div class="desc font-green">@lang('label.EVENT_ITEM'): {{$item->name}}</div>
                                        <div class="desc font-blue-dark">@lang('label.DATE'): {{Helper::formatDate2($item->from_date)}} {{isset($item->to_date)?__('label.TO').' '. Helper::formatDate2($item->to_date):'' }}</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    @endforeach
                    @endif
                </marquee>
            </div>
        </div>
    </div>
</div>
<!--- END:: Slider Area ------->
<!--chart-->
<div class="row margin-top-10">
    <div class="col-md-6">
        <div class="font-white dashboard-title-back-style">
            <h4 class="text-center bold">@lang('label.RECRUIT_STATISTICS') (@lang('label.ALL_BATCHES'))</h4>
        </div>
        <div id="allBatchsBarChart" style="min-width: 310px; height: 350px; margin: 0 auto;"></div>
    </div>
    <!--last five year data-->
    <div class="col-md-6">
        <div class="font-white dashboard-title-back-style">
            <h4 class="text-center bold">@lang('label.ABSENT') (@lang('label.TODAY'))</h4>
        </div>
        <div id="absentChartDiv" style="min-width:250px; height: 300px; margin: 0 auto;"></div>
    </div>
</div>
<!--chart-->
@endif

<!--REPORT SHORT ICON-->
<div class="row margin-top-10">
    @if(!empty($assignedCmdrInfo))
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-hoki tooltips" href="{{URL::to('rctState')}}" title="@lang('label.RCT_STATE')">
            <div class="visual">
                <i class="fa fa-check"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-check"></i>
                </div>
                <div class="desc">@lang('label.RCT_STATE')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-madison tooltips" href="{{URL::to('assignMks')}}" title="@lang('label.ASSIGN_MKS')">
            <div class="visual">
                <i class="fa fa-pencil"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-pencil"></i>
                </div>
                <div class="desc">@lang('label.ASSIGN_MKS')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green tooltips" href="{{URL::to('changePassword')}}" title="@lang('label.CHANGE_PASSWORD')">
            <div class="visual">
                <i class="fa fa-key"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-key"></i>
                </div>
                <div class="desc">@lang('label.CHANGE_PASSWORD')</div>
            </div>
        </a>
    </div>
    @endif
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 yellow-gold tooltips" href="{{URL::to('platoonWiseResult')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-file-text-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <div class="desc">@lang('label.PLATOON_WISE_RESULT')</div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-seagreen tooltips" href="{{URL::to('eventWiseResult')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-list-alt"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-list-alt"></i>
                </div>
                <div class="desc">@lang('label.EVENT_WISE_RESULT')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-hoki  tooltips" href="{{URL::to('courseResult')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-file-text-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <div class="desc">@lang('label.COURSE_RESULT')</div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red tooltips" href="{{URL::to('courseDetailsResult')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-list-alt"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-list-alt"></i>
                </div>
                <div class="desc">@lang('label.COURSE_DETAILS_RESULT')</div>
            </div>
        </a>
    </div>
    @if(!empty($assignedCmdrInfo))
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green tooltips" href="{{URL::to('stateWiseDetails')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-th-large"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-th-large"></i>
                </div>
                <div class="desc">@lang('label.STATE_WISE_DETAILS')</div>
            </div>
        </a>
    </div>
    @endif
</div>
<!--EOF SHORT ICON--><script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
                    $(function () {
                    //BAR CHART START
                    //*************** ALL BATCH BAR CHART ***************
                    var colors = ['#008FFB', '#26a69a', '#775DD0', '#FF4560', '#00E396', '#FEB019', '#546E7A', '#D10CE8'];
                    var options = {
                    chart: {
                    height: 300,
                            type: 'bar',
                            toolbar: {
                            show: false
                            },
                    },
                            colors: colors,
                            plotOptions: {
                            bar: {
                            horizontal: false,
                                    columnWidth: '50%',
                                    distributed: true,
//                                    endingShape: 'rounded',
                                    dataLabels: {
                                    position: 'top', // top, center, bottom
                                    },
                            },
                            },
                            dataLabels: {
                            enabled: true,
                                    formatter: function (val) {
                                    return val;
                                    },
                                    offsetY: - 20,
                                    style: {
                                    fontSize: '12px',
                                            colors: ["#000"]
                                    }
                            },
                            stroke: {
                            show: true,
                                    width: 2,
//                                colors: ['transparent']
                            },
                            series: [{
                            name: "@lang('label.RECRUIT')",
                                    data: [
                                            "{{$recruitPlanned}}",
                                            "{{$recruitJoint}}",
                                            "{{$recruitCurrent}}",
                                            "{{$onParadeCount}}",
                                            "{{$absentcount}}",
                                    ]
                            },
                            ],
                            xaxis: {
                            labels: {
                            rotate: - 45,
                                    style: {
                                    colors: colors,
                                            fontSize: '14px'
                                    }
                            },
                                    categories: [
                                            "@lang('label.PLANNED_STRENGTH')",
                                            "@lang('label.TOTAL_STRENGTH')",
                                            "@lang('label.CURRENT_STRENGTH')",
                                            "@lang('label.ON_PARADE')",
                                            "@lang('label.ABSENT')",
                                    ],
                            },
                            yaxis: {
                            title: {
                            text: 'Recruit Statistics'
                            }
                            },
                            fill: {
//                                opacity: 1
                            type: 'gradient',
                                    gradient: {
                                    shade: 'light',
                                            type: "horizontal",
                                            shadeIntensity: 0.50,
                                            gradientToColors: undefined,
                                            inverseColors: true,
                                            opacityFrom: 0.95,
                                            opacityTo: 0.95,
                                            stops: [80, 50, 80]
                                    },
                            },
                            tooltip: {
                            y: {
                            formatter: function (val) {
                            return  val
                            }
                            }
                            }
                    }

                    var chart = new ApexCharts(
                            document.querySelector("#allBatchsBarChart"),
                            options
                            );
                    chart.render();
//ENDOF ALL BATCH BAR CHART

                    //        ******* ABSENT CHART ALL BACHES && ALL CENTER ***

                    //ABSENT BAR CHART START
                    var colors = ['#008FFB', '#26a69a', '#775DD0', '#FF4560', '#00E396', '#FEB019', '#546E7A', '#D10CE8'];
                    var options = {
                    chart: {
                    height: 300,
                            type: 'bar',
                            toolbar: {
                            show: false
                            },
                    },
                            colors: colors,
                            plotOptions: {
                            bar: {
                            horizontal: false,
                                    columnWidth: '50%',
                                    distributed: true,
//                                    endingShape: 'rounded',
                                    dataLabels: {
                                    position: 'top', // top, center, bottom
                                    },
                            },
                            },
                            dataLabels: {
                            enabled: true,
                                    formatter: function (val) {
                                    return val;
                                    },
                                    offsetY: - 20,
                                    style: {
                                    fontSize: '12px',
                                            colors: ["#000"]
                                    }
                            },
                            stroke: {
                            show: true,
                                    width: 2,
//                                colors: ['transparent']
                            },
                            series: [{
                            name: "@lang('label.RECRUIT')",
                                    data: [
<?php
$hosp = !empty($otherAbsentArr[2]) ? count($otherAbsentArr[2]) : 0;
$ppgf = !empty($otherAbsentArr[3]) ? count($otherAbsentArr[3]) : 0;
$awol = !empty($otherAbsentArr[4]) ? count($otherAbsentArr[4]) : 0;
$osl = !empty($otherAbsentArr[5]) ? count($otherAbsentArr[5]) : 0;
?>

                                    "{{$hosp}}",
                                            "{{$ppgf}}",
                                            "{{$awol}}",
                                            "{{$osl}}",
                                    ]
                            },
                            ],
                            xaxis: {
                            labels: {
                            rotate: - 45,
                                    style: {
                                    colors: colors,
                                            fontSize: '14px'
                                    }
                            },
                                    categories: [
                                            "@lang('label.HOSP')",
                                            "@lang('label.PPGF')",
                                            "@lang('label.AWOL')",
                                            "@lang('label.OSL')",
                                    ],
                            },
                            yaxis: {
                            title: {
                            text: 'Absent'
                            }
                            },
                            fill: {
//                                opacity: 1
                            type: 'gradient',
                                    gradient: {
                                    shade: 'light',
                                            type: "horizontal",
                                            shadeIntensity: 0.50,
                                            gradientToColors: undefined,
                                            inverseColors: true,
                                            opacityFrom: 0.95,
                                            opacityTo: 0.95,
                                            stops: [80, 50, 80]
                                    },
                            },
                            tooltip: {
                            y: {
                            formatter: function (val) {
                            return  val
                            }
                            }
                            }
                    }

                    var chart = new ApexCharts(
                            document.querySelector("#absentChartDiv"),
                            options
                            );
                    chart.render();
                    //Endof absent Chart
                    //********************** endof bar chart *******************





                    });
</script>
@endsection