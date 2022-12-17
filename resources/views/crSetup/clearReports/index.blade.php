@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CLEAR_COURSE_REPORTS')
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'crClearReport/filter','class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            {!! Form::select('training_year_id', $trainingYearList,  Request::get('training_year_id'), ['class' => 'form-control js-source-states', 'id' => 'trainingYearId']) !!}
                            <span class="text-danger">{{ $errors->first('training_year_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') <span class="text-danger">*</span></label></label>
                        <div class="col-md-8">
                            {!! Form::select('course_id', $courseList,  Request::get('course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
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
            @if (!empty($pervReportInfo))
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-md red-soft clear-report pull-right">
                        <i class="fa fa-eraser"></i> @lang('label.CLEAR_REPORTS')
                    </button>
                </div>
            </div>
            @endif
            <div class="row margin-top-10">
                <div class="col-md-12 table-responsive">
                    <div class="webkit-scrollbar max-height-500">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SERIAL')</th>
                                    <th class="vcenter text-center" colspan="3">@lang('label.CM')</th>
                                    <th class="vcenter">@lang('label.GENERATED_BY')</th>
                                    <th class="vcenter text-center">@lang('label.GENERATED_AT')</th>
                                    <th class="vcenter text-center">@lang('label.COURSE_REPORT')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($pervReportInfo))
                                <?php
                                $sl = 0;
                                ?>

                                @foreach($pervReportInfo as $key => $target)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>

                                    <td class="vcenter">{!! !empty($target['personal_no']) ? $target['personal_no']:'' !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['cm_name']) !!}</td>
                                    <td class="vcenter">{!! $target['generated_by'] ?? '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['updated_at']) ? Helper::formatDate($target['updated_at']) : '' !!}</td>

                                    <td class="td-actions text-center vcenter">
                                        <div class="width-inherit">
                                            <?php
                                            $title = __('label.COURSE_REPORT_IS_NOT_GENERATED_YET');
                                            $class = 'grey-mint cursor-default';
                                            if (!empty($target['report_file']) && file_exists('public/CourseReportFiles/' . $courseList[Request::get('course_id')] . '/' . $target['report_file'])) {
                                                $title = __('label.CLICK_HERE_TO_DOWNLOAD_COURSE_REPORT');
                                                $class = 'green-seagreen download-file';
                                            }
                                            ?>
                                            <a class="btn btn-xs {{$class}} tooltips vcenter " type="button" title="{{$title}}" data-file="{{$target['report_file']}}"
                                               data-course="{{$courseList[Request::get('course_id')]}}">
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
            {!! Form::close() !!}
        </div>
    </div>
</div>


<script type="text/javascript">

    $(function () {
        //table header fix
        $(".table-head-fixer-color").tableHeadFixer('');
        //Start::Get Course
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            $("#courseId").html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
            if (trainingYearId == '0') {
                return false;
            }
            $.ajax({
                url: "{{ URL::to('crClearReport/getCourse')}}",
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
                    if (jqXhr.status == 400) {
                        var errorsHtml = '';
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                    } else if (jqXhr.status == 401) {
                        //toastr.error(jqXhr.responseJSON.message, '', options);
                        var errors = jqXhr.responseJSON.message;
                        var errorsHtml = '';
                        if (typeof (errors) === 'object') {
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, '', options);
                        } else {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        }
                    } else {
                        toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
                    }
                    App.unblockUI();
                }
            });//ajax

        });
        //End::Get Course

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

        $(document).on('click', '.clear-report', function () {
            var courseId = $("#courseId").val();

            swal({
                title: 'Are you sure?',
                text: 'Once "Yes, Clear" is clicked, all generated course reports of this course will be permanently cleared.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Clear',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('crClearReport/clear')}}",
                        type: "POST",
                        datatype: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
//                        cache: false,
//                        contentType: false,
//                        processData: false,
                        data: {
                            course_id: courseId,
                        },
                        beforeSend: function () {
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success('@lang("label.ALL_GENERATED_COURSE_REPORTS_OF_THIS_COURSE_HAS_BEEN_CLEARED_SUCCESSFULLY")', res, options);
                            App.unblockUI();
                            setTimeout(function () {
                                location.reload();
                            }, 500);
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
                }
            });
        });


    });

</script>
@stop