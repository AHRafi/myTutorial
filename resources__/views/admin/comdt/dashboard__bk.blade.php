@extends('layouts.default.master')
@section('data_count')
@if (session('status'))

<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<div class="portlet-body">
    <!-- <div class="page-bar bg-default">
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
<!--- START:: Slider Area ------->
<div class="row row margin-bottom-20 slider-div">
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
<br/>
<!--START CHART-->
<div class="row">
    <div class="col-md-6">
        <div class="font-white dashboard-title-back-style">
            <h4 class="text-center bold">@lang('label.RECRUIT_STATISTICS') (@lang('label.ALL_BATCHES'))</h4>
        </div>
        <div id="allBatchBarChart" style="min-width: 310px; height: 350px; margin: 0 auto;"></div>
    </div>
    <div class="col-md-6">
        <div class="font-white dashboard-title-back-style">
            <h4 class="text-center bold">@lang('label.ABSENT') (@lang('label.TODAY'))</h4>
        </div>
        <div id="absentChartDiv" style="min-width:250px; height: 300px; margin: 0 auto;"></div>
    </div>
</div>
<!--END CHART-->
<!--data count-->
<div class="row margin-bottom-20">
    <?php
    $colMd = "8"
    ?>
    @if($recruitDropStatus->isEmpty())
    <?php
    $colMd = "12"
    ?>
    @endif
    <div class="col-md-{{$colMd}}">
        <div class="count-style">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="dot bg-white rounded dot-border1 margin-bottom-10">
                                <span class="bold text-center">{{$countCurrentBatch}}</span>
                            </div><br/>
                            <span class="bold">@lang('label.CURRENT_BATCHES')</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="text-align:center">
                            <div class="dot bg-white rounded  dot-border2 margin-bottom-10">
                                <span class="bold text-center">{{count($countCenter)}}</span>
                            </div><br/>
                            <span class="bold">@lang('label.TOTAL_CENTER')</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="text-align:center">
                            <div class="dot bg-white rounded dot-border3 margin-bottom-10">
                                <span class="bold text-center">{{count($countTotalEvent)}}</span>
                            </div><br/>
                            <span class="bold">@lang('label.TOTAL_EVENT')</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="text-align:center">
                            <div class="dot bg-white rounded dot-border4 margin-bottom-10">
                                <span class="bold text-center">{{$recruitJoint}}</span>
                            </div><br/>
                            <span class="bold">@lang('label.TOTAL_STRENGTH')</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--drop chart center wise-->
    @if(!$recruitDropStatus->isEmpty())
    <div class="col-md-4">
        <!--pie chart-->
        <div class="font-white dashboard-title-back-style">
            <h4 class="text-center bold">@lang('label.ACTIVE_TRAINING_YEAR_DROP_STATUS')</h4>
        </div>
        <div id="pieChartDiv" style="min-width: 50%; height:150px; margin: 0 auto;"></div>  
    </div>
    @endif
</div>
<!--data count-->
<!--Active year START CHART-->
@if(!empty($batchList))
@if(!empty($activeYearRecruitPlain))
<div class="row margin-bottom-10">
    <div class="col-md-12">  
        <div class="font-white dashboard-title-back-style">
            <h4 class="text-center bold">@lang('label.RECRUIT_STATISTICS') (@lang('label.ACTIVE_TRAINING_YEAR'))</h4>
        </div>
        <div class="tabbable-line">
            <ul class="nav nav-tabs ">
                @if(!empty($batchList))
                <?php
                $sl = 0;
                ?>
                @foreach($batchList as $batchId=>$item)
                <?php
                ++$sl;
                if ($sl == 1) {
                    $active = 'active';
                } else {
                    $active = '';
                }
                ?>
                <li class="{{$active}}">
                    <a class="batch" data-id="{{$batchId}}" data-toggle="tab">{{$item}}</a>
                </li>
                @endforeach
                @endif
            </ul>
            <?php
            if (empty($batchList)) {
                $border = 'border: 1px solid #F8F9F9;text-align: center;';
            } else {
                $border = '';
            }
            ?>
            <div class="tab-content" style="{{$border}}">
                <div class="tab-pane active">
                    <!--get chart-->
                    @if(!empty($batchList))
                    <div id="getChart">
                    </div>
                    @else
                    <span>@lang('label.BATCH_NOT_ASSIGN')</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endif
<div class="row">
    @if(in_array(Auth::user()->group_id, [3]))
    <!--RELATIONSHIP SETUP SHORT ICON-->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2  blue-madison tooltips" href="{{URL::to('centerToBatch')}}" title="@lang(                                               'label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-exchange"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-exchange"></i>
                </div>
                <div class="desc">@lang('label.CENTER_TO_BATCH')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green tooltips" href="{{URL::to('moduleToBatch')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-arrows-h"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-arrows-h"></i>
                </div>
                <div class="desc">@lang('label.MODULE_TO_BATCH')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 yellow-mint tooltips" href="{{URL::to('subjectToModule')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-exchange"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-exchange"></i>
                </div>
                <div class="desc">@lang('label.SUBJECT_TO_MODULE')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple-plum tooltips" href="{{URL::to('eventToSubject')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-arrows-h"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-arrows-h"></i>
                </div>
                <div class="desc">@lang('label.EVENT_TO_SUBJECT')</div>
            </div>
        </a>
    </div>  <!--END RELATIONSHIP SETUP SHORT ICON-->
    <!--MARK AND WT DISTR SETUP SHORT ICON-->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red-soft tooltips" href="{{URL::to('moduleWtDistr')}}" title="@lang('label.MKS_WT_DISTR')">
            <div class="visual">
                <i class="fa fa-filter"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-filter"></i>
                </div>
                <div class="desc">@lang('label.MODULE_WT_DISTR')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-madison tooltips" href="{{URL::to('subjectWtDistr')}}" title="@lang('label.MKS_WT_DISTR')">
            <div class="visual">
                <i class="fa fa-balance-scale"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-balance-scale"></i>
                </div>
                <div class="desc">@lang('label.SUBJECT_WT_DISTR')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green tooltips" href="{{URL::to('eventWtDistr')}}" title="@lang('label.MKS_WT_DISTR')">
            <div class="visual">
                <i class="fa fa-calculator"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-calculator"></i>
                </div>
                <div class="desc">@lang('label.EVENT_WT_DISTR')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 yellow-mint tooltips" href="{{URL::to('observationWtDistr')}}" title="@lang('label.MKS_WT_DISTR')">
            <div class="visual">
                <i class="fa fa-balance-scale"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-balance-scale"></i>
                </div>
                <div class="desc">@lang('label.OBSERVATION_WT_DISTR')</div>
            </div>
        </a>
    </div>    <!--END MARK AND WT DISTR SETUP SHORT ICON-->
    @endif
    <!--REPORT SETUP SHORT ICON-->
    @if(in_array(Auth::user()->group_id, [1,2]))
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-seagreen tooltips" href="{{URL::to('platoonWiseResult')}}" title="@lang('label.REPORT')">
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
        <a class="dashboard-stat dashboard-stat-v2 red-soft tooltips" href="{{URL::to('eventWiseResult')}}" title="@lang('label.REPORT')">
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
        <a class="dashboard-stat dashboard-stat-v2 blue-madison tooltips" href="{{URL::to('courseResult')}}" title="@lang('label.REPORT')">
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
        <a class="dashboard-stat dashboard-stat-v2 green   tooltips" href="{{URL::to('courseDetailsResult')}}" title="@lang('label.REPORT')">
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

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 yellow-gold tooltips" href="{{URL::to('markWtDistr')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-filter"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-filter"></i>
                </div>
                <div class="desc">@lang('label.MARKS_WT_DISTRIBUTION')</div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-seagreen tooltips" href="{{URL::to('recruitList')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-list"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>
                        <i class="fa fa-list" ></i>
                    </span>
                </div>
                <div class="desc">@lang('label.RECRUIT_LIST')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red-soft tooltips" href="{{URL::to('platoonList')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-reorder"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span>
                        <i class="fa fa-reorder" ></i>
                    </span>
                </div>
                <div class="desc">@lang('label.PLATOON_LIST')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-madison tooltips" href="{{URL::to('recruitBioData')}}" title="@lang('label.REPORT')">
            <div class="visual">
                <i class="fa fa-file-text-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <div class="desc">@lang('label.RECRUIT_BIO_DATA')</div>
            </div>
        </a>
    </div>
    @endif
</div>
<script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
                    $(function () {
                    //BAR CHART START
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
                                            "@lang('label.ON_PARADE')",
                                            "@lang('label.ABSENT')",
                                    ],
                            },
                            yaxis: {
                            title: {
                            text: 'Recruit Statistics (All Batches)'
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
                            document.querySelector("#allBatchBarChart"),
                            options
                            );
                    chart.render();
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
//BAR CHART END

//        pie chart
<?php if (!$recruitDropStatus->isEmpty()) { ?>
                        // Create chart instance
                        var piechart = am4core.create("pieChartDiv", am4charts.PieChart3D);
                        piechart.hiddenState.properties.opacity = 0; // this creates initial fade-in

                        // Set data
                        var selected;
                        var types = [
    <?php
    foreach ($recruitDropStatus as $item) {
        ?>
                            {type: "<?php echo $item->name; ?>", percent:<?php echo!empty($totalDropArr[$item->id]) ? $totalDropArr[$item->id] : 0; ?>},
        <?php
    }
    ?>

                        ];
                        // Add data
                        piechart.data = generateChartData();
                        piechart.innerRadius = am4core.percent(40);
                        piechart.depth = 20;
                        // Add and configure Series
                        var pieSeries = piechart.series.push(new am4charts.PieSeries3D());
                        pieSeries.dataFields.value = "percent";
                        pieSeries.dataFields.category = "type";
                        //        pieSeries.slices.template.propertyFields.fill = "color";
                        pieSeries.slices.template.propertyFields.isActive = "pulled";
                        pieSeries.slices.template.strokeWidth = 0;
                        pieSeries.slices.template.cornerRadius = 5;
                        pieSeries.colors.step = 3;
                        function generateChartData() {
                        var chartData = [];
                        for (var i = 0; i < types.length; i++) {
                        if (i == selected) {
                        for (var x = 0; x < types[i].subs.length; x++) {
                        chartData.push({
                        type: types[i].subs[x].type,
                                percent: types[i].subs[x].percent,
                                color: types[i].color,
                                pulled: true
                        });
                        }
                        } else {
                        chartData.push({
                        type: types[i].type,
                                percent: types[i].percent,
                                color: types[i].color,
                                id: i
                        });
                        }
                        }
                        return chartData;
                        }

                        pieSeries.slices.template.events.on("hit", function (event) {
                        if (event.target.dataItem.dataContext.id != undefined) {
                        selected = event.target.dataItem.dataContext.id;
                        } else {
                        selected = undefined;
                        }
                        piechart.data = generateChartData();
                        });
<?php } ?>


                    //        ajax start
                    $(document).on("click", ".batch", function () {
                    var batchId = $(this).data("id");
                    var options = {
                    closeButton: true,
                            debug: false,
                            positionClass: "toast-bottom-right",
                            onclick: null
                    };
                    $.ajax({
                    url: "{{ URL::to('/dashboard/getBatchActiveYear')}}",
                            type: "POST",
                            dataType: "json",
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                            batch_id: batchId,
                            },
                            beforeSend: function () {
                            App.blockUI({boxed: true});
                            },
                            success: function (res) {
                            $('#getChart').html(res.html);
                            $('.js-source-states').select2();
                            App.unblockUI();
                            },
                            error: function (jqXhr, ajaxOptions, thrownError) {
                            toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                            App.unblockUI();
                            }
                    }); //ajax
                    });
                    //batch wise data

                    var batchId = $(".batch").data("id");
                    var options = {
                    closeButton: true,
                            debug: false,
                            positionClass: "toast-bottom-right",
                            onclick: null
                    };
                    $.ajax({
                    url: "{{ URL::to('/dashboard/getBatchActiveYear')}}",
                            type: "POST",
                            dataType: "json",
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                            batch_id: batchId,
                            },
                            beforeSend: function () {
                            App.blockUI({boxed: true});
                            },
                            success: function (res) {
                            $('#getChart').html(res.html);
                            $('.js-source-states').select2();
                            App.unblockUI();
                            },
                            error: function (jqXhr, ajaxOptions, thrownError) {
                            toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                            App.unblockUI();
                            }
                    }); //ajax

                    });
</script>

<!--EOF SHORT ICON-->
@endsection