@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.RELATE_EVENT_TO_SUB_EVENT')
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
                    </div>

                    <!--get module data-->
                    <div id="showSubEvent"></div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>

<!--Assigned Sub Event list-->
<div class="modal fade" id="modalAssignedSubEvent" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showAssignedSubEvent">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(document).on("change", "#eventId", function () {
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            if (eventId == '0') {
                $('#showSubEvent').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('eventToSubEvent/getSubEvent')}}",
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
                    $('#showSubEvent').html(res.html);
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
                        url: "{{URL::to('eventToSubEvent/saveEventToSubEvent')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.SUB_EVENT_HAS_BEEN_RELATED_TO_THIS_EVENT")', res, options);
                            $("#eventId").trigger('change');
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
        $(document).on("click", "#assignedSubEvent", function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            $.ajax({
                url: "{{ URL::to('eventToSubEvent/getAssignedSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    event_id: eventId
                },
                success: function (res) {
                    $("#showAssignedSubEvent").html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });
        // End Show Assigned CM Modal
    });

</script>

@stop