@extends('layouts.default.master')
@section('data_count')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<div class="portlet-body">
    <!---<div class="page-bar bg-default">
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
    <!--- START::Scrollbar ------->
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

    <!--- END:: Scrollbar ------->
    <div class="clear-fix"></div>
    @endif
</div>

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
    <div class="col-md-6">
        @include('layouts.default.slider')
    </div>
    <div class="col-md-6">
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
<!--START Center wise all batch chart-->
<!--START Center wise all batch chart-->
<div class="row margin-top-20 margin-bottom-20">
    <!-- START:: total recruit and absent and planned--> 
    <!--        <div class="col-md-6">
                <div class="font-white dashboard-title-back-style">
                    <h4 class="text-center bold">@lang('label.RECRUIT_STATISTICS') (@lang('label.ALL_BATCHES'))</h4>
                </div>
                <div id="allBatchsBarChart" style="min-width: 310px; height: 350px; margin: 0 auto;"></div>
            </div>-->
    <!-- END:: total recruit and absent and planned--> 
    @if(!empty($batchList))
    <div class="col-md-6">
        <!--        <div class="font-white dashboard-title-back-style">
                    <h4 class="text-center bold">@lang('label.RECRUIT_STATISTICS') (@lang('label.ALL_BATCHES'))</h4>
                </div>
                <div id="allBatchBarChart" style="min-width: 310px; height: 350px; margin: 0 auto;"></div>-->
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
            <div class="tab-content" style="{{$border}}" >
                <div class="tab-pane active">
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
    @endif
    <!--Absent Data today-->
    <div class="col-md-6">
        <div class="font-white dashboard-title-back-style">
            <h4 class="text-center bold">@lang('label.ABSENT') (@lang('label.TODAY'))</h4>
        </div>
        <div id="absentChartDiv" style="min-width:250px; height: 300px; margin: 0 auto;"></div>
    </div>
</div>
<!--END Center wise all batch chart-->
@endif
<div class="row margin-top-20 margin-bottom-20">
    <div class="col-md-12">
        <!--Data count-->
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
                    <div class="col-md-3">
                        <div style="text-align:center">
                            <div class="dot bg-white rounded dot-border4 margin-bottom-10">
                                <span class="bold text-center">{{$recruitCurrent}}</span>
                            </div><br/>
                            <span class="bold">@lang('label.CURRENT_STRENGTH')</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--End Data count div-->
</div>
<!--END Center wise all batch chart-->

<!--START CHART-->
@if(!empty($batchList))
<!--    <div class="row margin-bottom-20">
        <div class="col-md-7">
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
                <div class="tab-content" style="{{ $border }}">
                    <div class="tab-pane active">
                        get chart
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
        <div class="col-md-5">
            <div class="font-white dashboard-title-back-style">
                <h4 class="text-center bold">@lang('label.DROP_STATUS_LAST_FIVE_YEARS')</h4>
            </div>
            <div id="lastYearDropStatus" style="min-width:300px; height: 350px; margin: 0 auto;"></div>
        </div>
    </div>-->
@endif
<!--End Data Count Active year-->
<!--END CHART-->
<div class="row">
    @if(in_array(Auth::user()->group_id,[5]))
    <!--RELATIONSHIP SETUP SHORT ICON-->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2  yellow-mint tooltips" href="{{URL::to('particularToEvent')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-exchange"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-exchange"></i>
                </div>
                <div class="desc">@lang('label.PARTICULAR_TO_EVENT')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple-plum tooltips" href="{{URL::to('platoonToBatch')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-arrows-h"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-arrows-h"></i>
                </div>
                <div class="desc">@lang('label.PLATOON_TO_BATCH')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red-soft tooltips" href="{{URL::to('ciToBatch')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-exchange"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-exchange"></i>
                </div>
                <div class="desc">@lang('label.CI_TO_BATCH')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2  blue-madison  tooltips" href="{{URL::to('oicToBatch')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-arrows-h"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-arrows-h"></i>
                </div>
                <div class="desc">@lang('label.OIC_TO_BATCH')</div>
            </div>
        </a>
    </div> 
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green tooltips" href="{{URL::to('pLCmdrToPlatoon')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-plus-circle"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-plus-circle"></i>
                </div>
                <div class="desc">@lang('label.PL_CMDR_TO_PLATOON')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 yellow-mint tooltips" href="{{URL::to('termToEvent')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-plus"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="desc">@lang('label.TERM_TO_EVENT')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-seagreen tooltips" href="{{URL::to('termToParticular')}}" title="@lang('label.RELATIONSHIP_SETUP')">
            <div class="visual">
                <i class="fa fa-plus-square"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-plus-square"></i>
                </div>
                <div class="desc">@lang('label.TERM_TO_PARTICULAR')</div>
            </div>
        </a>
    </div>

    <!--END RELATIONSHIP SETUP SHORT ICON-->
    <!--MARK AND WT DISTR SETUP SHORT ICON-->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red-soft tooltips" href="{{URL::to('particularWtDistr')}}" title="@lang('label.MKS_WT_DISTR')">
            <div class="visual">
                <i class="fa fa-balance-scale"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-balance-scale"></i>
                </div>
                <div class="desc">@lang('label.PARTICULAR_WT_DISTR')</div>
            </div>
        </a>
    </div> <!--END MARK AND WT DISTR SETUP SHORT ICON-->
    @endif
    <!--REPORT SETUP SHORT ICON-->
    @if(in_array(Auth::user()->group_id,[4]))
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red-soft tooltips" href="{{URL::to('platoonWiseResult')}}" title="@lang('label.REPORT')">
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
        <a class="dashboard-stat dashboard-stat-v2  blue-madison  tooltips" href="{{URL::to('eventWiseResult')}}" title="@lang('label.REPORT')">
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
        <a class="dashboard-stat dashboard-stat-v2 blue-hoki tooltips" href="{{URL::to('courseResult')}}" title="@lang('label.REPORT')">
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
        <a class="dashboard-stat dashboard-stat-v2 yellow-soft   tooltips" href="{{URL::to('courseDetailsResult')}}" title="@lang('label.REPORT')">
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
    @endif
    <!--CI AND OIC REPORT AND MANAGEMENT-->
    @if(in_array(Auth::user()->group_id,[6,7]))
    <!--6=CI,7=OIC-->
    @if(Auth::user()->group_id == 6)
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-hoki tooltips" href="{{URL::to('unlockDonationMark/oicUnlockReq')}}" title="@lang('label.RECRUIT_ASSESSMENT')">
            <div class="visual">
                <i class="fa fa-unlock"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-unlock"></i>
                </div>
                <div class="desc">@lang('label.UNLOCK_REQUEST')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-madison tooltips" href="{{URL::to('ciDonationMks')}}" title="@lang('label.CI_DONATION_MKS')">
            <div class="visual">
                <i class="fa fa-pencil"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-pencil"></i>
                </div>
                <div class="desc">@lang('label.OBSN_MKS')</div>
            </div>
        </a>
    </div>
    @endif
    @if(Auth::user()->group_id == 7)
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-hoki tooltips" href="{{URL::to('unlockRequest')}}" title="@lang('label.RECRUIT_ASSESSMENT')">
            <div class="visual">
                <i class="fa fa-unlock"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-unlock"></i>
                </div>
                <div class="desc">@lang('label.UNLOCK_REQUEST')</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue-madison tooltips" href="{{URL::to('oicDonationMks')}}" title="@lang('label.OIC_DONATION_MKS')">
            <div class="visual">
                <i class="fa fa-pencil"></i>
            </div>
            <div class="details">
                <div class="number">
                    <i class="fa fa-pencil"></i>
                </div>
                <div class="desc">@lang('label.OBSN_MKS')</div>
            </div>
        </a>
    </div>
    @endif
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
    <!--End CI AND OIC REPORT AND MANAGEMENT-->
</div>
<!--EOF SHORT ICON-->
<script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>
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


//                    //ABSENT BAR CHART START
                    var colors = ['#008FFB', '#26a69a', '#775DD0', '#FF4560', '#00E396', '#FEB019', '#546E7A', '#D10CE8'];
                    var options = {
                    chart: {
                    height: 375,
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

                    $(document).on("click", ".batch", function () {
                    var batchId = $(this).data("id");
                    var options = {
                    closeButton: true,
                            debug: false,
                            positionClass: "toast-bottom-right",
                            onclick: null
                    };
                    $.ajax({
                    url: "{{ URL::to('/dashboard/getRecruitData')}}",
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
                    url: "{{ URL::to('/dashboard/getRecruitData')}}",
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

                    // *********************last five year drop status ******************
                    var dropChart = AmCharts.makeChart("lastYearDropStatus", {
                    "theme": "none",
                            "type": "serial",
                            "startDuration": 2,
                            "dataProvider": [
<?php
//if (!$centerArr->isEmpty()) {
foreach ($targetArr as $year => $target) {
    ?>
                                {

                                "year": "<?php echo $year; ?>",
    <?php
    foreach ($target as $key => $batchName) {
        ?>
                                    "batch<?php echo $key; ?>":<?php echo $batchName['value']; ?>,
                                            "description<?php echo $key; ?>":"<?php echo $batchName['description']; ?>",
        <?php
    }
    ?>
                                "color": "<?php echo $colorArr[$year]; ?>"
                                },
    <?php
}
//} 
?>
                            ],
                            "valueAxes": [{
                            "position": "left",
                                    "axisAlpha": 0,
                                    "gridAlpha": 0
                            }],
                            "graphs": [
<?php
foreach ($batchArr as $year => $value) {
    foreach ($value as $key => $item) {
        ?>
                                    {
                                    "balloonText": "<b> [[description]]</b>",
                                            "colorField": "color",
                                            "fillAlphas": 0.85,
                                            "lineAlpha": 0.1,
                                            "type": "column",
                                            "topRadius": 1,
                                            "valueField": "batch<?php echo $key; ?>",
                                            "descriptionField": "description<?php echo $key; ?>",
                                    },
        <?php
    }
}
?>
                            ],
                            "depth3D": 40,
                            "angle": 30,
                            "chartCursor": {
                            "categoryBalloonEnabled": false,
                                    "cursorAlpha": 0,
                                    "zoomable": false
                            },
                            "categoryField": "year",
                            "categoryAxis": {
                            "gridPosition": "start",
                                    "axisAlpha": 0,
                                    "gridAlpha": 0

                            },
                            "export": {
                            "enabled": false
                            }

                    }, 0);
                    //ENDOF LAST FIVE YEAR DROP CHART
                    });
</script>
@endsection