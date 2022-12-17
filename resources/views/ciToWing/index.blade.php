@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.ASSIGN_CI_TO_WING')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-offset-2 col-md-6">

                    <div class="form-group">
                        <label class="control-label col-md-4" for="wingId">@lang('label.WING') :<span class="text-danger"> *</span></label>
                        <!--                        <div class="col-md-8">
                                                    {!! Form::select('wing_id', $wingList, null, ['class' => 'form-control js-source-states', 'id' => 'wingId']) !!}
                                                    <span class="text-danger">{{ $errors->first('wing_id') }}</span>
                                                </div>-->
                        <div class="col-md-8 margin-top-8 bold">
                            {!! $wingList[1] !!}
                            {!! Form::hidden('wing_id', 1) !!}
                            <span class="text-danger">{{ $errors->first('wing_id') }}</span>
                        </div>
                    </div>
                    <!--get Syn data-->
                    <!--<div id="showCi"></div>-->
                    <div class="form-group">
                        <label class="control-label col-md-4" for="ciId">@lang('label.SELECT_CI') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::select('ci_id', $ciList, null, ['class' => 'form-control js-source-states', 'id' => 'ciId']) !!}
                            <span class="text-danger">{{ $errors->first('ci_id') }}</span>
                        </div>
                    </div>

                    <div class = "form-actions">
                        <div class = "col-md-offset-4 col-md-8">
                            <button class = "button-submit btn btn-circle green" type="button">
                                <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                            </button>
                            <a href = "{{ URL::to('ciToWing') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
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
        //get module
        $(document).on("change", "#courseId", function () {
            var trainingYearId = $("#trainingYearId").val();
            var courseId = $("#courseId").val();

            if (courseId === '0') {
                $('#showCi').html('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('ciToWing/getCi')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSyn').html(res.html);
                    $('.tooltips').tooltip();
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
//                    App.unblockUI();
                }
            });//ajax
            App.unblockUI();
        });

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
                        url: "{{URL::to('ciToWing/saveCi')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res, 'CI has been assigned to this wing', options);
//                            $(document).trigger("change", "#courseId");
//                            var trainingYearId = $("#trainingYearId").val();
//                            var courseId = $("#courseId").val();
//                            $.ajax({
//                                url: "{{ URL::to('synToCourse/getSyn')}}",
//                                type: "POST",
//                                dataType: "json",
//                                headers: {
//                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                                },
//                                data: {
//                                    course_id: courseId,
//                                    training_year_id: trainingYearId
//                                },
//                                beforeSend: function () {
//                                    App.blockUI({boxed: true});
//                                },
//                                success: function (res) {
//                                    $('#showSyn').html(res.html);
//                                    $('.tooltips').tooltip();
//                                    $(".js-source-states").select2();
//                                    App.unblockUI();
//                                },
//                                error: function (jqXhr, ajaxOptions, thrownError) {
////                    App.unblockUI();
//                                }
//                            });//ajax


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
    });
</script>
@stop