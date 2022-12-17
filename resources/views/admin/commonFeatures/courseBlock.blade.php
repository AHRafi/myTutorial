@if(!empty($termToCourseArr))
@foreach($termToCourseArr as $courseId => $courseInfo)
@if(!empty($dsDeligationList) && array_key_exists($courseId, $dsDeligationList))
@if(in_array(Auth::user()->group_id, [3]) || $dsDeligationList[$courseId] == Auth::user()->id)

<div class="row margin-bottom-10">
    <div class="col-md-12 text-center">
        <div class="alert alert-info alert-dismissable glow-info">
            <p>
                <strong>
                    <i class="fa fa-gears"></i> {!! __('label.CI_ACCOUNT_IS_DELIGATED_TO_DS', ['ds' => $dsApptList[$dsDeligationList[$courseId]]]) !!}.
                    @if(in_array(Auth::user()->group_id, [3]))
                    <a class="quick-link-a-tag" href="{{ URL::to('/deligateCiAcctToDs')}}">@lang('label.CLICK_HERE_TO_CHANGE_DELIGARTION').</a>
                    @endif
                </strong>
            </p>
        </div>
    </div>
</div>
@endif
@endif
<div class="row margin-bottom-10">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <h2 class="course-title text-center bold">
            @lang('label.COURSE'): {{!empty($courseInfo['course']) ? $courseInfo['course'] : ''}}
        </h2>
        <?php
        $courseTenure = !empty($courseInfo['course_initial_date']) && !empty($courseInfo['course_termination_date']) ? Helper::formatDate($courseInfo['course_initial_date']) . ' - ' . Helper::formatDate($courseInfo['course_termination_date']) : '';
        ?>
        <h6 class="course-tenure text-center bold">{{$courseTenure}}</h6>
        <div class="row margin-top-20">
            @foreach($courseInfo as $termId => $termInfo)
            @if(is_int($termId))
            <?php
            $class = 'gray-mint';
            $label = __('label.NOT_INITIATED');
            $percent = $termInfo['percent'];
            if ($termInfo['status'] == '0') {
                $class = 'gray-mint';
                $label = __('label.NOT_INITIATED');
            } else if ($termInfo['status'] == '1') {
                if ($termInfo['active'] == '0') {
                    $class = 'blue-hoki';
                    $label = __('label.INITIATED');
                } else if ($termInfo['active'] == '1') {
                    $class = 'green-sharp';
                    $label = __('label.ACTIVE');
                }
            } else if ($termInfo['status'] == '2') {
                $class = 'red-haze';
                $label = __('label.CLOSED');
            }
            ?>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 tooltips" data-html=true>
                <a class=" term-marking-status tooltips" type=" button" data-placement="top"  data-toggle="modal"
                   data-rel="tooltip" course-id="{!! $courseId !!}" term-id="{!! $termId !!}"
                   data-original-title="@lang('label.CLICK_HERE_TO_VIEW_TERM_STATUS_SUMMARY')" href="#termMarkingStatusSummaryModal">
                    <div class="dashboard-stat2 term-block term-block-{{$class}}">
                        <div class="display">
                            <div class="number">
                                <h4 class="font-{{$class}} bold">
                                    <span>{{!empty($termInfo['term']) ? $termInfo['term'] : ''}}</span>
                                </h4>
                                <?php
                                $termTenure = !empty($termInfo['initial_date']) && !empty($termInfo['termination_date']) ? Helper::formatDate($termInfo['initial_date']) . ' - ' . Helper::formatDate($termInfo['termination_date']) : '';
                                ?>
                                <span class="font-blue-oleo bold font-size-11">{{$termTenure}}</span>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="icon  bold text-right">
                                <i class="icon-pie-chart font-{{$class}} font-size-25"></i>
                            </div>
                            <div class="progress" style="background-color:white;" >
                                <span  style="width: {{$percent}}%;"  class="progress-bar progress-bar-success {{$class}}  bg-font-blue-oleo">
                                    <span class="sr-only">{{$percent}}% progress</span>
                                </span>
                            </div>
                            <div class="status">
                                <div class="status-title font-{{$class}}">{{$label}}</div>
                                <div class="status-number font-{{$class}}">{{($percent > 100) ? 100 : $percent}}%</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endforeach
@endif
<!-- START::Overall Performance Graph -->
@if(!empty($course->id))
@if(in_array(Auth::user()->group_id,[2,3]) || in_array(Auth::user()->id, $dsDeligationList))
@if($courseTotalCm != 0)
@if(!empty($eventAssessmentCmList))
@if( sizeof($eventAssessmentCmList) == $courseTotalCm )
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12 margin-top-10">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase font-dark font-size-14">
                        @lang('label.CURRENT_COURSE_OVERALL_PERFORMANCE_PROGRESSIVE', ['course' => !empty($course->name)?$course->name.' ':''])
                    </span>
                    <span class="caption-helper"></span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                        <div id="overallPerformanceTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                        <div id="wingPerWisePerformanceTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endif
@endif
@endif
@endif
<!-- END::Overall Performance Graph -->

<div class="row">
    <!--Start :: CM participation (last 5 courses)-->
    <div class="col-md-6 col-sm-12 col-xs-12 margin-top-10">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase font-dark font-size-14">
                        @lang('label.CM_PARTICIPATION_LAST_FIVE_COURSES')
                    </span>
                    <span class="caption-helper"></span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="cmParticipationLast5Courses" style="width: 100%; height: 400px; margin: 0 auto;"></div>
            </div>
        </div>
    </div>
    <!--End :: CM participation (last 5 courses)-->
    <!--Start :: CM participation (wing wise)-->

    @if(!empty($course->id))
    <div class="col-md-6 col-sm-12 col-xs-12 margin-top-10">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase font-dark font-size-14">
                        @lang('label.CURRENT_COURSE_CM_PARTICIPATION_WING_WISE', ['course' => !empty($course->name)?$course->name.' ':''])
                    </span>
                    <span class="caption-helper"></span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="cmParticipationWingWise" style="width: 100%; height: 416px; margin: 0 auto;"></div>
            </div>
        </div>
    </div>
    
    
    <!-- Start:: Content Archive List-->
    <!-- START :: Content Summary -->
    <div class="col-md-6 col-sm-12 col-xs-12 margin-top-10">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase font-dark font-size-14">
                        @lang('label.CONTENT_SUMMARY')
                    </span>
                    <span class="caption-helper"></span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="contentSummary" class="row content-summary" style="width: 100%; height: 200px; margin: 0 auto;">
                    <div class="col-md-12">
                        <div class="table-responsive webkit-scrollbar" style="max-height: 400px;">
                            <table class="table table-hover table-head-fixer-color-grey-mint">
                                <thead>
                                    <tr>
                                        <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                        <th class="vcenter">@lang('label.AUTHORITY')</th>
                                        <th class="vcenter text-center">@lang('label.THIS_DAY')</th>
                                        <th class="vcenter text-center">@lang('label.THIS_MONTH')</th>
                                        <th class="vcenter text-center">@lang('label.THIS_COURSE')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="vcenter text-center">1</td>
                                        <td class="vcenter">@lang('label.DS')</td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-green-seagreen">{!! !empty($contentArr['todays_total']['1']) ? $contentArr['todays_total']['1'] : 0 !!}</span>
                                        </td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-yellow-casablanca">{!! !empty($contentArr['month_total']['1']) ? $contentArr['month_total']['1'] : 0 !!}</span>
                                        </td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-purple-sharp">{!! !empty($contentArr['course_total']['1']) ? $contentArr['course_total']['1'] : 0 !!}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="vcenter text-center">2</td>
                                        <td class="vcenter">@lang('label.CM')</td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-green-seagreen">{!! !empty($contentArr['todays_total']['2']) ? $contentArr['todays_total']['2'] : 0 !!}</span>
                                        </td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-yellow-casablanca">{!! !empty($contentArr['month_total']['2']) ? $contentArr['month_total']['2'] : 0 !!}</span>
                                        </td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-purple-sharp">{!! !empty($contentArr['course_total']['2']) ? $contentArr['course_total']['2'] : 0 !!}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="vcenter text-center">3</td>
                                        <td class="vcenter">@lang('label.STAFF')</td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-green-seagreen">{!! !empty($contentArr['todays_total']['3']) ? $contentArr['todays_total']['3'] : 0 !!}</span>
                                        </td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-yellow-casablanca">{!! !empty($contentArr['month_total']['3']) ? $contentArr['month_total']['3'] : 0 !!}</span>
                                        </td>
                                        <td class="vcenter text-center">
                                            <span class="badge bold badge-purple-sharp">{!! !empty($contentArr['course_total']['3']) ? $contentArr['course_total']['3'] : 0 !!}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="vcenter text-right bold" colspan="2">@lang('label.TOTAL')</td>
                                        <td class="vcenter text-center bold">
                                            <span class="badge bold badge-green-seagreen">{!! !empty($contentArr['todays']['total']) ? $contentArr['todays']['total'] : 0 !!}</span>
                                        </td>
                                        <td class="vcenter text-center bold">
                                            <span class="badge bold badge-yellow-casablanca">{!! !empty($contentArr['month']['total']) ? $contentArr['month']['total'] : 0 !!}</span>
                                        </td>
                                        <td class="vcenter text-center bold">
                                            <span class="badge bold badge-purple-sharp">{!! !empty($contentArr['course']['total']) ? $contentArr['course']['total'] : 0 !!}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END :: Contents Summary -->
    <!-- End:: Content Archive List-->
    @endif
    <!--End :: CM participation (wing wise)-->
</div>

<!--Start Course Status Summary modal -->
<div class="modal fade" id="termMarkingStatusSummaryModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showTermStatusSummary"></div>
    </div>
</div>
<!--End Start Course Status Summary modal -->

<!-- DS Marking Summary modal -->
<div class="modal fade test" id="dsMarkingSummaryModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showDsMarkingStatusSummary"></div>
    </div>
</div>
<!-- End DS Marking Summary modal -->

<script type="text/javascript" src="{{asset('public/js/apexcharts.min.js')}}"></script>
<script>
$(function(){
//Start:: Request for course status summary
$(document).on('click', '.term-marking-status', function (e) {
e.preventDefault();
var courseId = $(this).attr('course-id');
var termId = $(this).attr('term-id');
$.ajax({
url: "{{URL::to('dashboard/requestCourseSatatusSummary')}}",
        type: "POST",
        datatype: 'json',
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
        course_id: courseId,
                term_id: termId,
        },
        beforeSend: function () {
        $('#showTermStatusSummary').html('');
        },
        success: function (res) {
        $('#showTermStatusSummary').html(res.html);
        $('.tooltips').tooltip();
        },
        error: function (jqXhr, ajaxOptions, thrownError) {
        if (jqXhr.status == 400) {
        var errorsHtml = '';
        var errors = jqXhr.responseJSON.message;
        $.each(errors, function (key, value) {
        errorsHtml += '<li>' + value + '</li>';
        });
        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
        } else if (jqXhr.status == 401) {
        toastr.error(jqXhr.responseJSON.message, '', options);
        } else {
        toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
        }
        App.unblockUI();
        }
});
});
//end:: Request for course status summary

//DS Marking Summary Modal
$(document).on('click', '.ds-marking-status', function (e) {
e.preventDefault();
var courseId = $(this).attr('course-id');
var dataId = $(this).attr('data-id');
var termId = $(this).attr('term-id');
var eventId = $(this).attr('event-id');
var subEventId = $(this).attr('sub-event-id');
var subSubEventId = $(this).attr('sub-sub-event-id');
var subSubSubEventId = $(this).attr('sub-sub-sub-event-id');
$.ajax({
url: "{{URL::to('dashboard/getDsMarkingSummary')}}",
        type: "POST",
        datatype: 'json',
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
        course_id: courseId,
                data_id: dataId,
                term_id: termId,
                event_id: eventId,
                sub_event_id: subEventId,
                sub_sub_event_id: subSubEventId,
                sub_sub_sub_event_id: subSubSubEventId,
        },
        beforeSend: function () {
        $('#showDsMarkingStatusSummary').html('');
        App.blockUI({boxed: true});
        },
        success: function (res) {
        $('#showDsMarkingStatusSummary').html(res.html);
        $('.tooltips').tooltip();
        App.unblockUI();
        },
        error: function (jqXhr, ajaxOptions, thrownError) {
        if (jqXhr.status == 400) {
        var errorsHtml = '';
        var errors = jqXhr.responseJSON.message;
        $.each(errors, function (key, value) {
        errorsHtml += '<li>' + value[0] + '</li>';
        });
        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
        } else if (jqXhr.status == 401) {
        toastr.error(jqXhr.responseJSON.message, 'Error', options);
        } else {
        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
        }
        App.unblockUI();
        }

});
});
//***************start :: cm participation last 5 courses**********//
var cmParticipationLast5CoursesOptions = {
chart: {
type: 'bar',
        height: 400,
        toolbar: {
        show: false
        }
},
        series: [{
        name: "@lang('label.NO_OF_CM')",
                data: [
<?php
if (!empty($lastFiveCourseList)) {
    foreach ($lastFiveCourseList as $courseId => $courseName) {
        $noOfCm = !empty($courseWiseCmNoList[$courseId]) ? $courseWiseCmNoList[$courseId] : 0;
        ?>
                        "{{$noOfCm}}",
        <?php
    }
}
?>
                ]
        }],
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
        colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233'],
        dataLabels: {
        enabled: false,
                enabledOnSeries: undefined,
                formatter: function (val) {
                return val
                },
                textAnchor: 'middle',
                distributed: true,
                offsetX: 0,
                offsetY: - 10,
                style: {
                fontSize: '12px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                        fontWeight: 'bold',
                        colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233']
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
        legend: {
        show: false
        },
        stroke: {
        show: true,
                width: 2,
                colors: ['transparent']
        },
        xaxis: {
        labels: {
        show: true,
                //                rotate: - 60,
                //                rotateAlways: true,
                //                hideOverlappingLabels: true,
                showDuplicates: false,
                trim: false,
                minHeight: undefined,
                maxHeight: 180,
                offsetX: 0,
                offsetY: 0,
                formatter: function (val) {
                return val;
                },
                format: undefined,
        },
                categories: [
<?php
if (!empty($lastFiveCourseList)) {
    foreach ($lastFiveCourseList as $courseId => $courseName) {
        echo "'$courseName', ";
    }
}
?>
                ],
                title: {
                text: "@lang('label.COURSES')",
                        offsetX: 0,
                        offsetY: 0,
                        style: {
                        color: undefined,
                                fontSize: '11px',
                                fontFamily: 'Helvetica, Arial, sans-serif',
                                fontWeight: 700,
                                cssClass: 'apexcharts-xaxis-title',
                        },
                },
        },
        yaxis: {
        title: {
        text: "@lang('label.NO_OF_CM')",
                offsetX: 0,
                offsetY: 0,
                style: {
                color: undefined,
                        fontSize: '11px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                        fontWeight: 700,
                        cssClass: 'apexcharts-xaxis-title',
                },
        }
        },
        fill: {
        type: 'gradient',
                gradient: {
                shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.20,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.85,
                        opacityTo: 1.85,
                        stops: [85, 50, 100]
                },
        },
        };
var cmParticipationLast5Courses = new ApexCharts(document.querySelector("#cmParticipationLast5Courses"), cmParticipationLast5CoursesOptions);
cmParticipationLast5Courses.render();
//***************end :: cm participation last 5 courses**********//
//***************start :: cm participation wing wise**********//
var cmParticipationWingWiseOptions = {
series: [
<?php
if (!empty($wingList)) {
    foreach ($wingList as $wingId => $wingName) {
        $noOfCm = !empty($wingWiseCmNoList[$wingId]) ? $wingWiseCmNoList[$wingId] : 0.00;
        echo $noOfCm . ',';
    }
}
?>
],
        labels: [
<?php
if (!empty($wingList)) {
    foreach ($wingList as $wingId => $wingName) {
        echo "'$wingName', ";
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
        colors: ['#295939', '#0f3057', '#3390ff'],
        dataLabels: {
        enabled: true
        },
        fill: {
        type: 'gradient',
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
var cmParticipationWingWise = new ApexCharts(document.querySelector("#cmParticipationWingWise"), cmParticipationWingWiseOptions);
cmParticipationWingWise.render();
//***************end :: cm participation wing wise**********//


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
        colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233'],
        dataLabels: {
        enabled: false,
                enabledOnSeries: undefined,
                formatter: function (val) {
                return val
                },
                textAnchor: 'middle',
                distributed: false,
                offsetX: 0,
                offsetY: - 10,
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
                offsetX: - 5
        }
}

var wingList = [];
var a = b = 0;
<?php
if (!empty($wingList)) {
    foreach ($wingList as $wingId => $wingCode) {
        ?>
        b = 0;
        wingList[a] = [];
        <?php
        if (!empty($gradeList)) {
            foreach ($gradeList as $gradeId => $gradeName) {
                $noOfCm = !empty($wingWiseMksArr[$gradeId][$wingId]) ? $wingWiseMksArr[$gradeId][$wingId] : 0;
                ?>
                var noOfCm = <?php echo $noOfCm; ?>;
                wingList[a][b] = noOfCm;
                b++;
                <?php
            }
        }
        ?>
        a++;
        <?php
    }
}
?>

var overallPerformanceTrendChart = new ApexCharts(document.querySelector("#overallPerformanceTrendChart"), overallMksOptions);
overallPerformanceTrendChart.render();
//END :: Overall Performance Trend Chart
//START :: Wing Percentahe Wise Performance Trend Chart
var wingPerWiseMksOptions = {
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
        colors: ['#295939', '#0f3057', '#3390ff'],
        dataLabels: {
        enabled: false,
                enabledOnSeries: undefined,
                formatter: function (val) {
                return val
                },
                textAnchor: 'middle',
                distributed: false,
                offsetX: 0,
                offsetY: - 10,
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
<?php
if (!empty($wingList)) {
    foreach ($wingList as $wingId => $wingCode) {
        ?>
                {
                name: "{{$wingCode}}",
                        data: [
        <?php
        if (!empty($gradeList)) {
            foreach ($gradeList as $gradeId => $gradeName) {
                $noOfCm = !empty($wingWiseMksPer[$gradeId][$wingId]) ? $wingWiseMksPer[$gradeId][$wingId] : 0;
                echo $noOfCm . ',';
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
        text: "@lang('label.PERCENTAGE_OF_CM')",
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
        formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
        var value = wingList[seriesIndex][dataPointIndex];
        return parseFloat(val).toFixed(2) + "% (" + value + ")";
        },
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

var wingPerWisePerformanceTrendChart = new ApexCharts(document.querySelector("#wingPerWisePerformanceTrendChart"), wingPerWiseMksOptions);
wingPerWisePerformanceTrendChart.render();
//END :: Wing Percentahe Wise Performance Trend Chart


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
        tooltip: {
        y: {
        formatter: function (val) {
        return val
        }
        }
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