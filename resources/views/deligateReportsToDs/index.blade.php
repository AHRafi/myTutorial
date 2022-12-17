@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DELIGATE_REPORTS_TO_DS')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'delegateReportsToDsForm')) !!}
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                                <label class="control-label col-md-4" for="dsId">@lang('label.DS') :<span class="text-danger"> </span></label>
                                <div class="col-md-8" id ="showCm">
									<?php 
									$dsIds = !empty($prevDataInfo->ds_id) ? explode(',', $prevDataInfo->ds_id) : (!empty($prevDataInfo->ds_id) && $prevDataInfo->ds_id == '0' ? $dsList : []); 
									?>
									{!! Form::select('ds_id[]', $dsList, $dsIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'dsId', 'data-width' => '100%']) !!}
									<span class="text-danger">{{ $errors->first('ds_id') }}</span>
								</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-md-offset-3">
                    <div class="form-group">
                        <label class = "control-label col-md-4" for = "checkedAll">@lang('label.REPORT_PRIVILEGES') :<span class = "text-danger"> *</span></label>
                        <div class = "col-md-8 margin-top-8">
                            <div class="md-checkbox">

                                {!! Form::checkbox('check_all',1,false,['id' => 'checkedAll','class'=> 'md-check']) !!} 
                                <label for="checkedAll">
                                    <span></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                </label>
                                <span class="bold">@lang('label.CHECK_ALL')</span>
                            </div>
                            <div class="form-group form-md-line-input">
                                <div class="col-md-10">
                                    <div class="md-checkbox-list">

                                        <?php
                                        $checked1 = (!empty($prevDataArr) && in_array(1, $prevDataArr)) ? 'checked' : '';
                                        $checked2 = (!empty($prevDataArr) && in_array(2, $prevDataArr)) ? 'checked' : '';
                                        $checked3 = (!empty($prevDataArr) && in_array(3, $prevDataArr)) ? 'checked' : '';
                                        $checked4 = (!empty($prevDataArr) && in_array(4, $prevDataArr)) ? 'checked' : '';
                                        $checked5 = (!empty($prevDataArr) && in_array(5, $prevDataArr)) ? 'checked' : '';
                                        ?>
                                        <div class="md-checkbox">
                                            {!! Form::checkbox('report[3]',3, $checked3, ['id' => 3, 'data-id'=>3,'class'=> 'md-check report-check', $checked3]) !!}

                                            <span class = "text-danger">{{ $errors->first('report') }}</span>
                                            <label for="{{3}}">
                                                <span></span>
                                                <span class="check tooltips"></span>
                                                <span class="box tooltips"></span>@lang('label.EVENT_RESULT_COMBINED_REPORT')
                                            </label>
                                        </div>
                                        <div class="md-checkbox">
                                            {!! Form::checkbox('report[4]',4, $checked4, ['id' => 4, 'data-id'=>4,'class'=> 'md-check report-check', $checked4]) !!}

                                            <span class = "text-danger">{{ $errors->first('report') }}</span>
                                            <label for="{{4}}">
                                                <span></span>
                                                <span class="check tooltips"></span>
                                                <span class="box tooltips"></span>@lang('label.PERFORMANCE_ANALYSIS_REPORT')
                                            </label>
                                        </div>
                                        <div class="md-checkbox">
                                            {!! Form::checkbox('report[1]',1, $checked1, ['id' => 1, 'data-id'=>1,'class'=> 'md-check report-check', $checked1]) !!}

                                            <span class = "text-danger">{{ $errors->first('report') }}</span>
                                            <label for="{{1}}">
                                                <span></span>
                                                <span class="check tooltips"></span>
                                                <span class="box tooltips"></span>@lang('label.TERM_RESULT_REPORT')
                                            </label>
                                        </div>

                                        <div class="md-checkbox">
                                            {!! Form::checkbox('report[2]',2, $checked2, ['id' => 2, 'data-id'=>2,'class'=> 'md-check report-check', $checked2]) !!}

                                            <span class = "text-danger">{{ $errors->first('report') }}</span>
                                            <label for="{{2}}">
                                                <span></span>
                                                <span class="check tooltips"></span>
                                                <span class="box tooltips"></span>@lang('label.COURSE_RESULT_REPORT')
                                            </label>
                                        </div>
                                        <div class="md-checkbox">
                                            {!! Form::checkbox('report[5]',5, $checked5, ['id' => 5, 'data-id'=>5,'class'=> 'md-check report-check', $checked5]) !!}

                                            <span class = "text-danger">{{ $errors->first('report') }}</span>
                                            <label for="{{5}}">
                                                <span></span>
                                                <span class="check tooltips"></span>
                                                <span class="box tooltips"></span>@lang('label.INDIVIDUAL_PROFILE')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class = "form-actions">
                    <div class = "col-md-offset-4 col-md-8">
                        <button class="btn btn-circle red-soft deligate-reports"type="button">
                            <i class="fa fa-gears"></i> @lang('label.DELIGATE_REPORTS')
                        </button>&nbsp;&nbsp;
                        @if(!empty($prevDataArr))
                        <button class="btn btn-circle red-mint cancel-deligation" type="button">
                            <i class="fa fa-times-circle"></i> @lang('label.CANCEL_DELIGATION')
                        </button>
                        @endif
                    </div>
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
		
		//START:: Multiselect CM
    var dsAllSelected = false;
    $('#dsId').multiselect({
        numberDisplayed: 0,
        includeSelectAllOption: true,
        buttonWidth: '100%',
        maxHeight: 250,
        nonSelectedText: "@lang('label.SELECT_DS_OPT')",
        enableCaseInsensitiveFiltering: true,
        onSelectAll: function () {
            dsAllSelected = true;
        },
        onChange: function () {
            dsAllSelected = false;
        }
    });
//END:: Multiselect CM
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        //deligate reports
        $(document).on('click', '.deligate-reports', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#delegateReportsToDsForm')[0]);


            swal({
                title: 'Are you sure?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Delegate Reports',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('deligateReportsToDs/setDeligation')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.deligate-reports').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('.deligate-reports').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);
                            setInterval(function () {
                                location.reload();
                            }, 1000);
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
                            $('.deligate-reports').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });

        //cancel deligation
        $(document).on('click', '.cancel-deligation', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#delegateReportsToDsForm')[0]);


            swal({
                title: 'Are you sure?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Cancel Delegation',
                cancelButtonText: 'No, Retain Delegation',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('deligateReportsToDs/cancelDeligation')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.deligate-reports').prop('disabled', true);
                            $('.cancel-deligation').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('.deligate-reports').prop('disabled', false);
                            $('.cancel-deligation').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);
                            setInterval(function () {
                                location.reload();
                            }, 1000);
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
                            $('.deligate-reports').prop('disabled', false);
                            $('.cancel-deligation').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });
        //    CHECK ALL
        // this code for  database 'check all' if all checkbox items are checked
        if ($('.report-check:checked').length == $('.report-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        }

        $("#checkedAll").change(function () {
            if (this.checked) {
                $(".md-check").each(function () {
                    if (!this.hasAttribute("disabled")) {
                        this.checked = true;
                    }
                });
            } else {
                $(".md-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.report-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.report-check:checked').length == $('.report-check').length) {
                $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
            }
        });

//    CHECK ALL
    });
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
@stop