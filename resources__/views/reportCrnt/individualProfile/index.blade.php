@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.INDIVIDUAL_PROFILE')
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'individualProfileReportCrnt/filter','class' => 'form-horizontal')) !!}
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
                        <label class="control-label col-md-4" for="cmId">@lang('label.CM')</label>
                        <div class="col-md-8">
                            {!! Form::select('cm_id', $cmList,  Request::get('cm_id'), ['class' => 'form-control js-source-states', 'id' => 'cmId']) !!}
                            <span class="text-danger">{{ $errors->first('cm_id') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="form-group">
                        <!--<label class="control-label col-md-4" for="">&nbsp;</label>-->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> @lang('label.GENERATE')
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            
            {!! Form::close() !!}
            <!--filter form close-->

            @if($request->generate == 'true')
            @if (!$targetArr->isEmpty())
            <div class="row">
                <div class="col-md-12 text-right">
                    <a class="btn btn-md btn-primary vcenter" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                        <span class="tooltips" title="@lang('label.PRINT')"><i class="fa fa-print"></i> </span> 
                    </a>
<!--                    <a class="btn btn-success vcenter" href="{!! URL::full().'&view=pdf' !!}">
                        <span class="tooltips" title="@lang('label.DOWNLOAD_PDF')"><i class="fa fa-file-pdf-o"></i></span>
                    </a>-->
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong> {{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong> {{$courseList->name}} |</strong>
                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <div class="max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SERIAL')</th>
                                    <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter">@lang('label.RANK')</th>
                                    <th class="vcenter">@lang('label.FULL_NAME')</th>
                                    <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                                    <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                    <th class="vcenter">@lang('label.ARMS_SERVICE')</th>
                                    <th class="vcenter">@lang('label.COMMISSIONING_COURSE')</th>
                                    <th class="vcenter">@lang('label.EMAIL')</th>
                                    <th class="vcenter">@lang('label.MOBILE')</th>
                                    <th class="vcenter text-center">@lang('label.PROFILE_DETAILS')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!$targetArr->isEmpty())
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($targetArr as $target)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">{!! !empty($target->personal_no) ? $target->personal_no:'' !!}</td>
                                    <td class="vcenter">{!! $target->rank?? '' !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target->full_name) !!}</td>
                                    <td class="vcenter">{!! $target->official_name??'' !!}</td>
                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target->photo) && File::exists('public/uploads/cm/' . $target->photo))
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target->photo}}" alt="{{$target->official_name?? ''}}"/>
                                        @else
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target->official_name?? ''}}"/>
                                        @endif
                                    </td>
                                    <td class="vcenter">{!! $target->arms_service_name ?? '' !!}</td>
                                    <td class="vcenter">{!! $target->comm_course_name ?? '' !!}</td>
                                    <td class="vcenter">{!! $target->email ?? '' !!}</td>
                                    <td class="vcenter">{!! $target->number ?? '' !!}</td>
                                    <td class="td-actions text-center vcenter">
                                        <div class="width-inherit">
                                            <a class="btn btn-xs green-seagreen tooltips vcenter" title="@lang('label.CLICK_HERE_TO_VIEW_PROFILE')" href="{!! URL::to('individualProfileReportCrnt/' . $target->id . '/profile'.Helper::queryPageStr($qpArr)) !!}">
                                                <i class="fa fa-user"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="11">@lang('label.NO_CM_FOUND')</td>
                                </tr>
                                @endif
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


<script type="text/javascript" src="{{asset('public/js/apexcharts.min.js')}}"></script>
<script type="text/javascript">

$(function () {
    //table header fix
    $(".table-head-fixer-color").tableHeadFixer('');
    //Start::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();
        if (trainingYearId == '0') {
            $("#courseId").html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
            $("#cmId").html("<option value='0'>@lang('label.ALL_CM_OPT')</option>");
            return false;
        }
        $.ajax({
            url: "{{ URL::to('individualProfileReportCrnt/getCourse')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                training_year_id: trainingYearId
            },
            beforeSend: function () {
                $("#cmId").html("<option value='0'>@lang('label.ALL_CM_OPT')</option>");
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#courseId').html(res.html);
                $(".js-source-states").select2();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        });//ajax

    });
    //End::Get Course
    //Start::Get Term
    $(document).on("change", "#courseId", function () {


        var courseId = $("#courseId").val();
        if (courseId == '0') {
            $("#cmId").html("<option value='0'>@lang('label.ALL_CM_OPT')</option>");
            return false;
        }

        $.ajax({
            url: "{{ URL::to('individualProfileReportCrnt/getCm')}}",
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
                $('#cmId').html(res.html);
                $(".js-source-states").select2();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        });//ajax

    });
    //End::Get Term

// Course profile Graph
    var courseProfileGraphOptions = {
        series: [{
                name: '@lang("label.WT_PERCENT")',
                data: [
<?php
if (!empty($termList)) {
    foreach ($termList as $termId => $termName) {
        $percent = !empty($achievedMksWtArr[$termId]['total_term_percent']) ? $achievedMksWtArr[$termId]['total_term_percent'] : 0;
        echo "'$percent',";
    }
}
?>
                ]
            }],
        chart: {
            type: 'bar',
            height: 270
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '35%',
                endingShape: 'rounded'
            },
        },
        colors: ["#4C87B9", "#8E44AD", "#F2784B", "#1BA39C", "#EF4836"],
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        title: {
            text: "@lang('label.COURSE_PROFILE_TERM_WISE')",
            align: 'left',
            style: {
                fontSize: '14px',
                fontWeight: '700',
            },
        },
        xaxis: {
            categories: [
<?php
if (!empty($termList)) {
    foreach ($termList as $termId => $termName) {
        echo "'$termName',";
    }
}
?>
            ],
            title: {
                text: '@lang("label.TERM")',
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: undefined,
                    fontSize: '10px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 900,
                    cssClass: 'apexcharts-xaxis-title',
                },
            }
        },
        yaxis: {
            title: {
                text: '@lang("label.WT_PERCENT")',
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: undefined,
                    fontSize: '10px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 900,
                    cssClass: 'apexcharts-yaxis-title',
                },
            },
            labels: {
                formatter: function (val) {
                    return parseFloat(val).toFixed(2);
                }
            },
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return parseFloat(val).toFixed(2) + '%'
                }
            }
        }
    };
    var courseProfileGraph = new ApexCharts(document.querySelector("#showCourseProfileGraph"), courseProfileGraphOptions);
    courseProfileGraph.render();
// End Course profile Graph
});

</script>
@stop