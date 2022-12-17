@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.REPORT_ACTIVATION')
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
                    <div class="form-group act-deact-btn">

                    </div>
                </div>  
            </div>


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
            $(".act-deact-btn").html("");
            if (trainingYearId == '0') {
                return false;
            }
            $.ajax({
                url: "{{ URL::to('crReportActivation/getCourse')}}",
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

        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            $(".act-deact-btn").html("");
            if (courseId == '0') {
                return false;
            }
            $.ajax({
                url: "{{ URL::to('crReportActivation/getActDeactBtn')}}",
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
//                    $('#epeExamDate').html(response.html2);
                    $('.act-deact-btn').html(res.html);
                    $(".js-source-states").select2();
                    $(".act-deact-switch").bootstrapSwitch({
                        offColor: 'danger'
                    });
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

        $(document).on('switchChange.bootstrapSwitch', '.act-deact-switch', function () {

            var status = this.checked == true ? '1' : '0';
            var criteria = $(this).attr('criteria');
            var courseId = $("#courseId").val();


            $.ajax({
                url: "{{URL::to('crReportActivation/setStat')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    criteria: criteria,
                    status: status,
                    course_id: courseId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    toastr.success(res.message, res.heading, options);
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
                        toastr.error(jqXhr.responseJSON.message, '', options);
                    } else {
                        toastr.error('Error', 'Something went wrong', options);
                    }
                    App.unblockUI();
                }
            });

        });




    });

</script>
@stop