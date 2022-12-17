@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.COMMISSIONING_COURSE_WISE_PERFORMANCE_TREND')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'commissioningCourseWisePerformanceTrendReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commissioningCourseId">@lang('label.COMMISSIONING_COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id ="showCommissioningCourseView">
                            {!! Form::select('commissioning_course_id[]', $commissioningCourseList, $commissioningCourseIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'commissioningCourseId', 'data-width' => '100%']) !!}
                            <span class="text-danger">{{ $errors->first('commissioning_course_id') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 text-center">
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
                            {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong>{{$courseList->name}}</strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="commissioningCourseWisePerformanceTrendChart" style="width: 100%; height: 400px; margin: 0 auto;"></div>
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
//Start:: Multiselect Commissioning Courses
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
    //End:: Multiselect Commissioning Courses

//START::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();
        $.ajax({
            url: "{{ URL::to('commissioningCourseWisePerformanceTrendReportCrnt/getCourse')}}",
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
                $('#showCommissioningCourseView').html(res.showCommissioningCourseView);
                $(".js-source-states").select2();
                //Start:: Multiselect Commissioning Courses
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
                //End:: Multiselect Commissioning Courses
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        }); //ajax

    });
//END::Get Course


//START::Get Course Wise Commissioning Course
    $(document).on("change", "#courseId", function () {
        var courseId = $("#courseId").val();
        $.ajax({
            url: "{{ URL::to('commissioningCourseWisePerformanceTrendReportCrnt/getCourseWiseCommissioningCourse')}}",
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
                $('#showCommissioningCourseView').html(res.showCommissioningCourseView);
                //Start:: Multiselect Commissioning Courses
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
                //End:: Multiselect Commissioning Courses
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
//END::Get Course Wise Commissioning Course


//START :: Commissioning Course Wise Performance Trend Chart
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
<?php
if (!empty($selectedCommissioningCourses)) {
    foreach ($selectedCommissioningCourses as $commissioningCourseId => $commissioningCourseInfo) {
        ?>
                    {
                        name: "{{$commissioningCourseInfo}}",
                        data: [
        <?php
        if (!empty($gradeList)) {
            foreach ($gradeList as $gradeId => $gradeName) {
                $noOfCm = !empty($commissioningCourseWiseMksArr[$gradeId][$commissioningCourseId]) ? $commissioningCourseWiseMksArr[$gradeId][$commissioningCourseId] : 0;
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

    var commissioningCourseWisePerformanceTrendChart = new ApexCharts(document.querySelector("#commissioningCourseWisePerformanceTrendChart"), commissioningCourseWiseMksOptions);
    commissioningCourseWisePerformanceTrendChart.render();
//START :: Commissioning Course Wise Performance Trend Chart

});
</script>
@stop