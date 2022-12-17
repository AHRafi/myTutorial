@extends('layouts.default.master')
@section('data_count')
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i>@lang('label.SUBJECT_TO_DS')
                </div>
            </div>

            <div class="portlet-body">
                {!! Form::open(['group' => 'form', 'url' => '', 'class' => 'form-horizontal', 'id' => 'submitForm']) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                    <div class="col-md-7">
                                        <div class="control-label pull-left"> <strong>
                                                {{ $activeTrainingYear->name ?? '' }}</strong></div>
                                        {!! Form::hidden('training_year_id', $activeTrainingYear->id ?? null, ['id' => 'trainingYearId']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                    <div class="col-md-7">
                                        <div class="control-label pull-left"> <strong> {{ $activeCourse->name ?? null }}
                                            </strong></div>
                                        {!! Form::hidden('course_id', $activeCourse->id ?? null, ['id' => 'courseId']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="subjectId">@lang('label.SUBJECT'):</label>
                                    <div class="col-md-7">
                                        {!! Form::select('subject_id', $subjectArr, null, [
                                            'class' => 'form-control js-source-states',
                                            'id' => 'subjectId',
                                        ]) !!}
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div id="showDsList"></div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

    </div>

    <!--Assigned Cm list-->
    <div class="modal fade" id="modalAssignedDs" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div id="showAssignedDs">

            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            // # Get Ds Start
            $(document).on("change", "#subjectId", function() {
                var trainingYearId = $("#trainingYearId").val();
                var subject_id = $(this).val();
                // alert(subject_id); return false;
                if (subject_id === '0') {
                    $('#showDsList').html('');
                    return false;
                }
                $.ajax({
                    url: "{{ URL::to('subjectToDs/getDsList') }}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        subject_id: subject_id,
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#showDsList').html(res.html);
                        $('.tooltips').tooltip();
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        App.unblockUI();
                    }
                }); //ajax
                App.unblockUI();
            });
            // # Get Ds End

            // # Saving Data Start
            $(document).on('click', '.button-submit', function(e) {
                e.preventDefault();
                var oTable = $('#dataTable').dataTable();
                var x = oTable.$('input,select,textarea').serializeArray();
                $.each(x, function(i, field) {

                    $("#submitForm").append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', field.name)
                        .val(field.value));
                });
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
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "{{ URL::to('subjectToDs/store') }}",
                            type: "POST",
                            datatype: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: form_data,
                            success: function(res) {
                                $("#assignedDs").html(
                                    `@lang('label.DS_ASSIGNED_TO_THIS_SUBJECT'): ${res.countSubjectToDs} <i class="fa fa-search-plus">`
                                    )
                                toastr.success(res, "@lang('label.DS_HAS_BEEN_RELATED_TO_THIS_SUBJECT')", options);
                            },
                            error: function(jqXhr, ajaxOptions, thrownError) {
                                App.unblockUI();
                                if (jqXhr.status == 400) {
                                    var errorsHtml = '';
                                    var errors = jqXhr.responseJSON.message;
                                    $.each(errors, function(key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                        options);
                                } else if (jqXhr.status == 401) {
                                    toastr.error(jqXhr.responseJSON.message, '',
                                        options);
                                } else {
                                    toastr.error('Error', 'Something went wrong',
                                        options);
                                }
                                App.unblockUI();
                            }
                        });
                    }

                });

            });
            // # Saving Data End

            // Get Assigned DS Start
            $(document).on('click', '#assignedDs', function(e) {
                e.preventDefault();
                var subjectId = $("#subjectId").val();
                var courseId = $("#courseId").val();

                var options = {
                    closeButton: true,
                    debug: false,
                    positionClass: "toast-bottom-right",
                    onclick: null
                };
                $.ajax({
                    url: "{{ URL::to('subjectToDs/getAssignedDs') }}",
                    type: "POST",
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        subject_id: subjectId,
                        course_id: courseId,
                    },
                    success: function(res) {
                        $("#showAssignedDs").html(res.html);
                        // toastr.success(res, "@lang('label.DS_HAS_BEEN_RELATED_TO_THIS_SUBJECT')", options);
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        App.unblockUI();
                        if (jqXhr.status == 400) {
                            var errorsHtml = '';
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function(key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '',
                                options);
                        } else {
                            toastr.error('Error', 'Something went wrong',
                                options);
                        }
                        App.unblockUI();
                    }
                });

            });
            // Get Assigned DS End

        });
    </script>
    {{-- <script src="{{ asset('public/js/custom.js') }}" type="text/javascript"></script> --}}
@stop
