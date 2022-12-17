@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CRITERIA_WISE_WT_DISTRIBUTION')
            </div>
            
            <div class="actions">
                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#" data-original-title="" title=""> </a>
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                            {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                            {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                        </div>
                    </div>
                </div>

                <!--get module data-->
                <div id="showCriteriaWt" class="col-md-offset-2 col-md-7">
                    <div class="row">
<!--                        @if(!empty($eventAssessmentMarkingData))
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.MARKING_HAS_ALREADY_BEEN_STARTED') !!}</strong></p>
                            </div>
                        </div>
                        @endif-->
                        @if(!empty($totalCourseWt))
                        <div class="col-md-8 margin-top-10">
                            <span class="label label-md bold label-blue-steel">
                                @lang('label.TOTAL_COURSE_WT'):&nbsp;{!! $totalCourseWt->total_course_wt ?? '' !!}
                            </span>
                            {!! Form::hidden('total_course_wt',!empty($totalCourseWt->total_course_wt) ? $totalCourseWt->total_course_wt : null,['id' => 'totalCourseWtId']) !!}
                        </div>
                        @endif
                        @if(empty($eventAssessmentMarkingData))
                        <div class="col-md-4 margin-top-5 text-right">
                            <button class="btn green btn-danger tooltips" type="button" id="buttonDelete" >
                                <i class="fa fa-trash"></i> &nbsp;@lang('label.DELETE_MKS_WT')
                            </button>
                        </div>
                        @endif
                        <div class="col-md-12 margin-top-10">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>

                                        <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                        <th class="text-center vcenter">@lang('label.CRITERIA')</th>
                                        <th class="text-center vcenter">@lang('label.WT')</th>

                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $sl = 0;
                                    $eventReadOnly = !empty($eventAssessmentMarkingData) ? 'readonly' : '';
                                    $dsReadOnly = !empty($dsObsnMarkingData) ? 'readonly' : '';
                                    $ciReadOnly = !empty($ciObsnMarkingData) ? 'readonly' : '';
                                    $comdtReadOnly = !empty($comdtObsnMarkingData) ? 'readonly' : '';
                                    ?>
                                    <tr>
                                        <td class="text-center vcenter">{!! ++$sl !!}</td>
                                        <td class="text-left vcenter">@lang('label.TOTAL_EVENT_WT')</td>
                                        <td class="text-center vcenter width-200">
                                            {!! Form::text('total_event_wt',!empty($criteriaWtArr['total_event_wt']) ? $criteriaWtArr['total_event_wt'] : null, ['id'=> 'totalEventWtId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off', $eventReadOnly]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center vcenter">{!! ++$sl !!}</td>
                                        <td class="text-left vcenter"> @lang('label.DS_OBSN_WT')</td>
                                        <td class="text-center vcenter width-200">
                                            {!! Form::text('ds_obsn_wt',!empty($criteriaWtArr['ds_obsn_wt']) ? $criteriaWtArr['ds_obsn_wt'] : null, ['id'=> 'dsObsnWtId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off', $dsReadOnly]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center vcenter">{!! ++$sl !!}</td>
                                        <td class="text-left vcenter"> @lang('label.CI_OBSN_WT')</td>
                                        <td class="text-center vcenter width-200">
                                            {!! Form::text('ci_obsn_wt',!empty($criteriaWtArr['ci_obsn_wt']) ? $criteriaWtArr['ci_obsn_wt'] : null, ['id'=> 'ciObsnWtId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off', $ciReadOnly]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center vcenter">{!! ++$sl !!}</td>
                                        <td class="text-left vcenter"> @lang('label.COMDT_OBSN_WT')</td>
                                        <td class="text-center vcenter width-200">
                                            {!! Form::text('comdt_obsn_wt',!empty($criteriaWtArr['comdt_obsn_wt']) ? $criteriaWtArr['comdt_obsn_wt'] : null, ['id'=> 'comdtObsnWtId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off', $comdtReadOnly]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                                        <td class="text-right width-200">
                                            <span class="total-wt bold">{!! !empty($criteriaWtArr['total_wt']) ? $criteriaWtArr['total_wt'] : '' !!}</span>
                                            {!! Form::hidden('total',!empty($criteriaWtArr['total_wt']) ? $criteriaWtArr['total_wt'] : null ,['class' => 'total-wt']) !!}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 text-center">
                            <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                                <i class="fa fa-check"></i> @lang('label.SUBMIT')
                            </button>
                            <a href="{{ URL::to('criteriaWiseWt') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == '0') {
                $('#showCriteriaWt').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('criteriaWiseWt/getCriteriaWt')}}",
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
                    $('#showCriteriaWt').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on('keyup', '#totalEventWtId', function () {
            var totalEventWt = parseFloat($(this).val());
            var totalCourseWt = parseFloat($("#totalCourseWtId").val());
            total();
            var totalWt = parseFloat($(".total-wt").val());
            if (totalEventWt > totalCourseWt) {
                swal({
                    title: '@lang("label.YOUR_GIVEN_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                       
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#totalEventWtId").val('');
                    $("#totalEventWtId").focus('');
                    total();
                    return false;
                });
            } else if (totalWt > totalCourseWt) {
                swal({
                    title: '@lang("label.TOTAL_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                       
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#totalEventWtId").val('');
                    $("#totalEventWtId").focus('');
                    total();
                    return false;
                });
            }
        });
        $(document).on('keyup', '#dsObsnWtId', function () {
            var dsObsnWt = parseFloat($(this).val());
            var totalCourseWt = parseFloat($("#totalCourseWtId").val());
            total();
            var totalWt = parseFloat($(".total-wt").val());
            if (dsObsnWt > totalCourseWt) {
                swal({
                    title: '@lang("label.YOUR_GIVEN_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                       
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#dsObsnWtId").val('');
                    $("#dsObsnWtId").focus('');
                    total();
                    return false;
                });
            } else if (totalWt > totalCourseWt) {
                swal({
                    title: '@lang("label.TOTAL_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                       
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#dsObsnWtId").val('');
                    $("#dsObsnWtId").focus('');
                    total();
                    return false;
                });
            }
        });
        $(document).on('keyup', '#ciObsnWtId', function () {
            var ciObsnWt = parseFloat($(this).val());
            var totalCourseWt = parseFloat($("#totalCourseWtId").val());
            total();
            var totalWt = parseFloat($(".total-wt").val());
            if (ciObsnWt > totalCourseWt) {
                swal({
                    title: '@lang("label.YOUR_GIVEN_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                       
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#ciObsnWtId").val('');
                    $("#ciObsnWtId").focus('');
                    total();
                    return false;
                });
            } else if (totalWt > totalCourseWt) {
                swal({
                    title: '@lang("label.TOTAL_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                       
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#ciObsnWtId").val('');
                    $("#ciObsnWtId").focus('');
                    total();
                    return false;
                });
            }
        });
        $(document).on('keyup', '#comdtObsnWtId', function () {
            var comdtObsnWt = parseFloat($(this).val());
            var totalCourseWt = parseFloat($("#totalCourseWtId").val());
            total();
            var totalWt = parseFloat($(".total-wt").val());
            if (comdtObsnWt > totalCourseWt) {
                swal({
                    title: '@lang("label.YOUR_GIVEN_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                       
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#comdtObsnWtId").val('');
                    $("#comdtObsnWtId").focus('');
                    total();
                    return false;
                });
            } else if (totalWt > totalCourseWt) {
                swal({
                    title: '@lang("label.TOTAL_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                       
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#comdtObsnWtId").val('');
                    $("#comdtObsnWtId").focus('');
                    total();
                    return false;
                });
            }
        });


        function total() {
            var totalEventWt = $('#totalEventWtId').val();
            if (isNaN(totalEventWt)) {
                totalEventWt = 0;
            }
            var dsObsnWt = $('#dsObsnWtId').val();
            if (isNaN(dsObsnWt)) {
                dsObsnWt = 0;
            }
            var ciObsnWt = $('#ciObsnWtId').val();
            if (isNaN(ciObsnWt)) {
                ciObsnWt = 0;
            }
            var comdtObsnWt = $('#comdtObsnWtId').val();
            if (isNaN(comdtObsnWt)) {
                comdtObsnWt = 0;
            }
            //var total = 0;
            var total = parseFloat(Number(totalEventWt) + Number(dsObsnWt) + Number(ciObsnWt) + Number(comdtObsnWt)).toFixed(2);
            $(".total-wt").text(total);
            $(".total-wt").val(total);
        }

        $(document).on('click', '.button-submit', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            swal({
                title: 'Are you sure?',
                   
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url: "{{URL::to('criteriaWiseWt/saveCriteriaWt')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res, '@lang("label.WT_DISTRIBUTED_SUCCESSFULLY")', options);
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
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            App.unblockUI();
                        }


                    });
                }

            });

        });

        //delete
        $(document).on('click', '#buttonDelete', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            swal({
                title: 'Are you sure?',
                   
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url: "{{URL::to('criteriaWiseWt/deleteCriteriaWt')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $("#buttonDelete").prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success(res, '@lang("label.MKS_WT_DELETED_SUCCESSFULLY")', options);
                            $("#buttonDelete").prop('disabled', false);
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
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            $("#buttonDelete").prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });
    });
</script>
@stop