@extends('layouts.default.master')
@section('data_count')	
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CREATE_ASSESSMENT_WEEK')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::open(array('group' => 'form', 'url' => '#','id' => 'assessmentWeekForm' , 'files'=> true, 'class' => 'form-horizontal')) !!}
            {!! Form::hidden('page', Helper::queryPageStr($qpArr)) !!}
            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('course_id', $courseList, null, ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                                <span class="text-danger">{{ $errors->first('course_id') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('term_id', $termList, null, ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                                <span class="text-danger">{{ $errors->first('term_id') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="name">@lang('label.WEEK_NAME') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('name',  null, ['id'=> 'name', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="initialDate">@lang('label.INITIAL_DATE') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('initial_date', null, ['id'=> 'initialDate', 'class' => 'form-control
                                    course-date', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!}
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="initialDate">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-danger">{{ $errors->first('initial_date') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="terminationDate">@lang('label.TERMINATION_DATE')
                                :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('termination_date', null, ['id'=> 'terminationDate', 'class' =>
                                    'form-control course-date', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!}
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="terminationDate">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-danger">{{ $errors->first('termination_date') }}</span>
                                </div>
                            </div>
                        </div>

                        <div id="order">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="order">@lang('label.ORDER') :<span class="text-danger"> *</span></label>
                                <div class="col-md-8">
                                    {!! Form::select('order', $orderList, $lastOrderNumber, ['class' => 'form-control js-source-states', 'id' => 'order']) !!} 
                                    <span class="text-danger">{{ $errors->first('order') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="status">@lang('label.STATUS') :</label>
                            <div class="col-md-8">
                                {!! Form::select('status', ['1' => __('label.ACTIVE'), '2' => __('label.INACTIVE')], '1', ['class' => 'form-control', 'id' => 'status']) !!}
                                <span class="text-danger">{{ $errors->first('status') }}</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn btn-circle green" id="submit" type="submit">
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href="{{ URL::to('/assessmentweek'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
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
            $('#showEvent').html('');
            $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('assessmentweek/getTerm')}}",
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
                    $('#termId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        //function for checking termination date
        $(document).on('change', '.course-date', function () {
            var initialDate = new Date($('#initialDate').val());
            var terminationDate = new Date($('#terminationDate').val());
            if (terminationDate < initialDate) {
                swal('Termination Date must be greater than InitialDate ');
                $('#terminationDate').val('');
                $('noOfWeeks').val('');
                return false;
            }
        });
        $(document).on('click', '#submit', function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var form_data = new FormData($('#assessmentWeekForm')[0]);
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
                    var options = {
                        closeButton: true,
                        debug: false,
                        positionClass: "toast-bottom-right",
                        onclick: null
                    };
                    $.ajax({
                        url: "{{url('assessmentweek')}}",
                        type: "post",
                        data: form_data,
                        dataType: "json",
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            toastr.success('@lang("label.ASSESSMENT_WEEK_CREATED_SUCCESSFULLY")', res, options);
                            window.location.href = "{{url('/assessmentweek')}}";
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
                        }

                    });
                }

            });

        });
    });

</script>
@stop