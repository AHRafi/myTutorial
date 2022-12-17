@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.RELATE_SYN_SUB_SYN')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYear->name}} </strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYear->id, ['id' => 'trainingYearId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeCourse->name}} </strong></div>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="synId">@lang('label.SYN') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7 show-syn">
                                    {!! Form::select('syn_id', $synList, Request::get('syn_id'),  ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
                                </div>
                            </div>
                        </div>

                    </div>



                    <!--get Sub Syn data-->
                    <div id="showSubSyn"></div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {

//        $(document).on("change", "#courseId", function () {
//            var courseId = $("#courseId").val();
//            $('#synId').html("<option value='0'>@lang('label.SELECT_SYN_OPT')</option>");
//            $('#showSubSyn').html('');
//
//            var options = {
//                closeButton: true,
//                debug: false,
//                positionClass: "toast-bottom-right",
//                onclick: null
//            };
//
//            $.ajax({
//                url: "{{ URL::to('synToSubSyn/getSyn')}}",
//                type: "POST",
//                dataType: "json",
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                data: {
//                    course_id: courseId,
//                },
//                beforeSend: function () {
//                    App.blockUI({boxed: true});
//                },
//                success: function (res) {
//                    $('#synId').html(res.html);
//                    $('.js-source-states').select2();
//                    App.unblockUI();
//                },
//                error: function (jqXhr, ajaxOptions, thrownError) {
//                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
//                    App.unblockUI();
//                }
//            });//ajax
//        });

        //get module
        $(document).on("change", "#synId", function () {
            var courseId = $("#courseId").val();
            var synId = $("#synId").val();

            if (synId === '0') {
                $('#showSubSyn').html('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('synToSubSyn/getSubSyn')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    syn_id: synId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSyn').html(res.html);
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
                        url: "{{URL::to('synToSubSyn/saveSubSyn')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.SUB_SYN_RELATED_WITH_SYN")', res, options);
//                            $(document).trigger("change", "#synId");

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