@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CM_COURSE_REPORT')
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'cmCourseReportCrnt/filter','class' => 'form-horizontal')) !!}
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

            <!--filter form close-->

            @if($request->generate == 'true')
            @if (!empty($targetArr))
            <div class="row">
                <div class="col-md-12 text-right">
                    <!--                    <a class="btn btn-md btn-primary vcenter" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                                            <span class="tooltips" title="@lang('label.PRINT')"><i class="fa fa-print"></i> </span> 
                                        </a>-->
                    <!--                    <a class="btn btn-success vcenter" href="{!! URL::full().'&view=pdf' !!}">
                                            <span class="tooltips" title="@lang('label.DOWNLOAD_PDF')"><i class="fa fa-file-pdf-o"></i></span>
                                        </a>-->
                    <!--                    <a class="btn btn-warning vcenter" href="{!! URL::full().'&view=excel' !!}">
                                            <span class="tooltips" title="@lang('label.DOWNLOAD_EXCEL')"><i class="fa fa-file-excel-o"></i> </span>
                                        </a>-->

                    <label class="control-label" for="sortBy">@lang('label.SORT_BY') :</label>&nbsp;

                    <label class="control-label" for="sortBy">
                        {!! Form::select('sort', $sortByList, Request::get('sort'),['class' => 'form-control','id'=>'sortBy']) !!}
                    </label>

                    <button class="btn green-jungle filter-btn"  id="sortByHref" type="submit">
                        <i class="fa fa-arrow-right"></i>  @lang('label.GO')
                    </button>
                </div>
            </div>
            @endif
            {!! Form::close() !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong>{{$courseList->name}} |</strong>
                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }} |</strong>
                            {{__('label.TOTAL_COURSE_REPORT_GENERATED')}} : <strong>{{ !empty($crReportList) ? sizeof($crReportList) : 0 }}</strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <div class="webkit-scrollbar max-height-500">
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
                                    <th class="vcenter text-center">@lang('label.COURSE_REPORT')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($targetArr))
                                <?php
                                $sl = 0;
                                ?>

                                @foreach($targetArr as $id => $target)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">{!! !empty($target['personal_no']) ? $target['personal_no']:'' !!}</td>
                                    <td class="vcenter">{!! $target['rank']?? '' !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>
                                    <td class="vcenter">{!! $target['official_name']??'' !!}</td>
                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    <td class="vcenter">{!! $target['arms_service_name'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['comm_course_name'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['email'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['number'] ?? '' !!}</td>
                                    <td class="td-actions text-center vcenter">
                                        <div class="width-inherit">
                                            <?php
                                            $title = __('label.COURSE_REPORT_IS_NOT_GENERATED_YET');
                                            $class = 'grey-mint cursor-default';
                                            if (!empty($crReportList[$target['id']]) && file_exists('public/CourseReportFiles/' . $courseList->name . '/' . $crReportList[$target['id']])) {
                                                $title = __('label.CLICK_HERE_TO_DOWNLOAD_COURSE_REPORT');
                                                $class = 'green-seagreen download-file';
                                            }
                                            ?>
                                            <a class="btn btn-xs {{$class}} tooltips vcenter " type="button" title="{{$title}}" data-file="{{$crReportList[$target['id']] ?? 0}}"
                                               data-course="{{$courseList->name}}">
                                                <i class="fa fa-download"></i>
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


<script type="text/javascript">

    $(function () {
        //table header fix
        $(".table-head-fixer-color").tableHeadFixer('');
        
        $(document).on("click", '.download-file', function (e) {
            var file = $(this).attr("data-file");
            var course = $(this).attr("data-course");

            var pathType = '';
            var basePath = "{!! URL::to('/') !!}";
            var path = basePath + '/public/CourseReportFiles/' + course + '/' + file;
            var a = document.createElement("a");
            a.href = path;
            a.setAttribute("download", file);
            a.click();
        });
        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            if (trainingYearId == '0') {
                $("#courseId").html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
                $("#termId").html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                return false;
            }
            $.ajax({
                url: "{{ URL::to('cmCourseReportCrnt/getCourse')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    $("#termId").html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
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


    });

</script>
@stop