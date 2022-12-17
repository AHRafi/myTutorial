@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.RELATE_EVENT_TO_SUB_SUB_SUB_EVENT')
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
                                <label class="control-label col-md-5" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('sub_event_id', $subEventList, null, ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('sub_sub_event_id', $subSubEventList, null, ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--get module data-->
                    <div id="showSubSubSubEvent"></div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>

<!--Assigned Sub Sub Sub Event list-->
<div class="modal fade" id="modalAssignedSubSubSubEvent" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showAssignedSubSubSubEvent">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(document).on("change", "#eventId", function () {

            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();

            $('#showSubSubSubEvent').html('');
            $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
            $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('eventToSubSubSubEvent/getSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    event_id: eventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#subEventId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subEventId", function () {

            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();

            $('#showSubSubSubEvent').html('');
            $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('eventToSubSubSubEvent/getSubSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#subSubEventId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subSubEventId", function () {

            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();

            if (subSubEventId == '0') {
                $('#showSubSubSubEvent').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('eventToSubSubSubEvent/getSubSubSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubSubEvent').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
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
                        url: "{{URL::to('eventToSubSubSubEvent/saveEventToSubSubSubEvent')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.SUB_SUB_SUB_EVENT_HAS_BEEN_RELATED_TO_THIS_EVENT")', res, options);
                            $("#subSubEventId").trigger('change');
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

        // Start Show Assigned Sub Event Modal
        $(document).on("click", "#assignedSubSubSubEvent", function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            $.ajax({
                url: "{{ URL::to('eventToSubSubSubEvent/getAssignedSubSubSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                },
                success: function (res) {
                    $("#showAssignedSubSubSubEvent").html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });
        // End Show Assigned CM Modal
    });

</script>

@stop