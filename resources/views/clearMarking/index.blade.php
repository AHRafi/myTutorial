@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-eraser"></i>@lang('label.CLEAR_MARKING')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'clearMarkingForm')) !!}
            <div class="row">
                <div class="col-md-12">
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
                                <label class="control-label col-md-6" for="termId">@lang('label.TERM') :</label>
                                <div class="col-md-6">
                                    <div class="control-label pull-left"> <strong> {{$activeTermInfo->name}} </strong></div>
                                    {!! Form::hidden('term_id',$activeTermInfo->id,['id'=>'termId'])!!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="criteriaId">@lang('label.ASSESSMENT_CRITERIA') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('criteria_id', $criteriaList, null, ['class' => 'form-control crt-check js-source-states', 'id' => 'criteriaId']) !!}
                                </div>
                            </div>
                        </div>
                        <div id="showDs">

                        </div>
                        <div id="showEvent">

                        </div>

                    </div>
                    <div class="row">

                        <div id="showSubEvent">

                        </div>
                        <div id="showSubSubEvent">

                        </div>
                        <div id="showSubSubSubEvent">

                        </div>
                    </div>
                    <div class="row form-actions">
                        <div class = "col-md-12 text-center">
                            <button class="btn btn-circle red-soft clear-marking"type="button" {{ $clearBtnDisabled }} >
                                <i class="fa fa-eraser"></i> @lang('label.CLEAR_MARKING')
                            </button>&nbsp;&nbsp;
                            <button class="btn btn-circle purple-wisteria bold tooltips term-marking-status"
                                    title="@lang('label.CLICK_HERE_TO_VIEW_TERM_STATUS_SUMMARY')" type=" button" data-placement="top"
                                    data-rel="tooltip" course-id="{!! $courseList->id !!}" term-id="{!! $activeTermInfo->id !!}"
                                    data-original-title="@lang('label.CLICK_HERE_TO_VIEW_TERM_STATUS_SUMMARY')" data-target="#modalInfo" data-toggle="modal">
                                <i class="fa fa-info-circle"></i> @lang('label.TERM_STATUS_SUMMARY')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Start Course Status Summary modal -->
<div class="modal fade" id="modalInfo" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showCourseStatus"></div>
    </div>
</div>
<!--End Start Course Status Summary modal -->


<!-- DS Marking Summary modal -->
<div class="modal fade test" id="dsMarkingSummaryModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showDsMarkingSummary"></div>
    </div>
</div>
<!-- End DS Marking Summary modal -->


<script type="text/javascript">
    $(function () {
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };

        $(document).on("change", "#criteriaId", function () {
            var criteriaId = $("#criteriaId").val();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();

            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');

            if (criteriaId == '0') {
                $('#showDs').html('');
                $('#showEvent').html('');
                $('.clear-marking').prop('disabled', true);
                return false;
            }



            if (criteriaId == '1') {
                $.ajax({
                    url: "{{ URL::to('clearMarking/getDsEvent')}}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId,
                        term_id: termId,
                    },
                    success: function (res) {
                        $('#showDs').html(res.html);
                        $('#showEvent').html(res.showEventView);
                        $('.clear-marking').removeAttr('disabled');
                        $('.js-source-states').select2();
                        App.unblockUI();

                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, 'Error', options);
                        } else {
                            toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                        }
                        App.unblockUI();
                    }
                });//ajax
            }

            if (criteriaId == '2') {
                $.ajax({
                    url: "{{ URL::to('clearMarking/getEvent')}}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId,
                        term_id: termId,
                    },
                    success: function (res) {
                        $('#showDs').html('');
                        $('#showEvent').html(res.showEventView);
                        $('.clear-marking').removeAttr('disabled');
                        $('.js-source-states').select2();
                        App.unblockUI();

                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, 'Error', options);
                        } else {
                            toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                        }
                        App.unblockUI();
                    }
                });//ajax
            }

            if (criteriaId == '3') {
                $.ajax({
                    url: "{{ URL::to('clearMarking/getDs')}}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId,
                        term_id: termId,
                    },
                    success: function (res) {
                        $('#showDs').html(res.html);
                        $('#showEvent').html('');
                        $('.clear-marking').removeAttr('disabled');
                        $('.js-source-states').select2();
                        App.unblockUI();

                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, 'Error', options);
                        } else {
                            toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                        }
                        App.unblockUI();
                    }
                });//ajax
            }
        });

        $(document).on("change", "#eventId", function () {
            var criteriaId = $("#criteriaId").val();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();

            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');

            if (eventId == '0') {
                return false;
            }

            $.ajax({
                url: "{{ URL::to('clearMarking/getSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                },
                success: function (res) {
                    $('#showSubEvent').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();

                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, 'Error', options);
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subEventId", function () {
            var criteriaId = $("#criteriaId").val();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();

            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');

            if (subEventId == '0') {
                return false;
            }

            $.ajax({
                url: "{{ URL::to('clearMarking/getSubSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                },
                success: function (res) {
                    $('#showSubSubEvent').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();

                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, 'Error', options);
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subSubEventId", function () {
            var criteriaId = $("#criteriaId").val();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();

            $('#showSubSubSubEvent').html('');

            if (subSubEventId == '0') {
                return false;
            }

            $.ajax({
                url: "{{ URL::to('clearMarking/getSubSubSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                },
                success: function (res) {
                    $('#showSubSubSubEvent').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();

                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, 'Error', options);
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }
                    App.unblockUI();
                }
            });//ajax
        });


        //Start:: Request for course status summary
        $(document).on('click', '.term-marking-status', function (e) {
            e.preventDefault();
            var courseId = $(this).attr('course-id');
            var termId = $(this).attr('term-id');
            $.ajax({
                url: "{{URL::to('clearMarking/requestCourseSatatusSummary')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                },
                success: function (res) {
                    $('#showCourseStatus').html(res.html);
                    $('.tooltips').tooltip();
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
                        toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
                    }
                    App.unblockUI();
                }
            });
        });
        //end:: Request for course status summary

        //DS Marking Summary Modal
        $(document).on('click', '.ds-marking-status', function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var dataId = $(this).attr('data-id');
            var termId = $(this).attr('term-id');
            var eventId = $(this).attr('event-id');
            var subEventId = $(this).attr('sub-event-id');
            var subSubEventId = $(this).attr('sub-sub-event-id');
            var subSubSubEventId = $(this).attr('sub-sub-sub-event-id');
            $.ajax({
                url: "{{URL::to('clearMarking/getDsMarkingSummary')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    data_id: dataId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    $('#showDsMarkingSummary').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showDsMarkingSummary').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
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
                        toastr.error(jqXhr.responseJSON.message, 'Error', options);
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }
                    App.unblockUI();
                }

            });
        });


        //deligate reports
        $(document).on('click', '.clear-marking', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#clearMarkingForm')[0]);


            swal({
                title: 'Are you sure?',
                text: "@lang('label.THIS_ACTION_WILL_CLEAR_ALL_MARKING_OF_SELECTED_CRITERIA')",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Clear Marking',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('clearMarking/clear')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.clear-marking').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('.clear-marking').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);
                            location.reload();
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
                            $('.clear-marking').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });
        //    CHECK ALL
        // this code for  database 'check all' if all checkbox items are checked
        if ($('.crt-check:checked').length == $('.crt-check').length) {
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

        $('.crt-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.crt-check:checked').length == $('.crt-check').length) {
                $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
            }
        });

        //    CHECK ALL
    });
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
@stop