@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.RELATE_FACTOR_TO_TRAIT')
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
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']) !!}
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
                                <label class="control-label col-md-5" for="traitId">@lang('label.TRAIT') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('trait_id', $traitList, null, ['class' => 'form-control js-source-states', 'id' => 'traitId']) !!}
                                </div>
                            </div>
                        </div>

                    </div>



                    <!--get module data-->


                </div>
            </div>

            <div class="margin-top-10" id="showMarkingSlab"></div>
            {!! Form::close() !!}
        </div>
    </div>

</div>



<script type="text/javascript">
    $(function () {
        $(document).on("change", "#traitId", function () {

            var courseId = $("#courseId").val();
            var traitId = $("#traitId").val();
            if (traitId == '0') {
                $('#showMarkingSlab').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('crFactorToTrait/getMarkingSlab')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    trait_id: traitId,
                },
                beforeSend: function () {
                    $('#showMarkingSlab').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showMarkingSlab').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        //add multiple phone number
        $(document).on("click", ".add-factor", function () {
            var key = $(this).attr("data-marking-slab-id");

            $.ajax({
                url: "{{ URL::to('crFactorToTrait/addFactor')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    key: key,
                },
                beforeSend: function () {
                    $('.tooltip').hide();
                },
                success: function (res) {
                    $("#newFactor_" + key).append(res.html);
//                    $(".tooltips").tooltip();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });


        //remove  row
        $(document).on('click', '.remove-factor', function () {
            $(this).parent().parent().remove();
            $('.tooltip').hide();
            return false;
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
                        url: "{{URL::to('crFactorToTrait/saveFactorToTrait')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.FACTOR_TO_TRAIT_ASSIGNED_ASSIGNED_SUCCESSFULLY")', res, options);


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
                    });
                }

            });

        });
    });

</script>








@stop