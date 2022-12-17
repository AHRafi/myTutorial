@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-book"></i>
                @if(in_array(Auth::user()->group_id,[3]))
                @lang('label.MODERATION_MARKING')
                @elseif (in_array(Auth::user()->id, $dsDeligationList)) 
                @lang('label.CI_MODERATION_MARKING')
                @endif
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            {!! Form::hidden('auto_save', 0, ['id' => 'autoSave']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-6" for="trainingYearId">@lang('label.TRAINING_YEAR') :</label>
                                <div class="col-md-6">
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId">@lang('label.COURSE') :</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                                    {!! Form::hidden('course_id',$courseList->id,['id'=>'courseId'])!!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-6" for="termId">@lang('label.TERM') :</label>
                                <div class="col-md-6">
                                    <div class="control-label pull-left"> <strong> {{$activeTermInfo->name}} </strong></div>
                                    {!! Form::hidden('term_id',$activeTermInfo->id,['id'=>'termId'])!!}
                                </div>
                            </div>
                        </div>
                        @if(!empty($cmDataArr))
                        @if(sizeof($eventList) > 1)
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="row">
                            <div class="col-md-offset-2 col-md-7">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_TERM') !!}</strong></p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @else
                        <div class="row">
                            <div class="col-md-offset-2 col-md-7">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_ASSIGNED_TO_THIS_COURSE') !!}</strong></p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div id="showSubEventOrCmList"></div>

                    <div id="showSubSubEventOrCmList"></div>

                    <div id="showSubSubSubEventOrCmList"></div>

                    <div id="showCmList"></div>

                    <!-- DS Marking Summary modal -->
                    <div class="modal fade test" id="modalDsMarkingSummary" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div id="showDsMarkingSummary"></div>
                        </div>
                    </div>
                    <!-- End DS Marking Summary modal -->

                    <!-- Unlock message modal -->
                    <div class="modal fade test" id="modalUnlockMessage" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div id="showMessage"></div>
                        </div>
                    </div>
                    <!-- End Unlock message modal -->
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {

        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            timeOut: 1000,
            onclick: null
        };

        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == '0') {
                $('#showTermEvent').html('');
                $('#showSubEventOrCmList').html('');
                $('#showSubSubEventOrCmList').html('');
                $('#showSubSubSubEventOrCmList').html('');
                $('#showCmList').html('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('ciModerationMarking/getTermEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                success: function (res) {
                    $('#showTermEvent').html(res.html);
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

        $(document).on("change", "#eventId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            if (eventId == '0') {
                $('#subEventId').html("<select><option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option></select>");
                $('#showSubEventOrCmList').html('');
                $('#showSubSubEventOrCmList').html('');
                $('#showSubSubSubEventOrCmList').html('');
                $('#showCmList').html('');
                return false;
            }

            $.ajax({
                url: "{{ URL::to('ciModerationMarking/getSubEvent')}}",
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
                beforeSend: function () {
                    $('#showSubEventOrCmList').html('');
                    $('#showSubSubEventOrCmList').html('');
                    $('#showSubSubSubEventOrCmList').html('');
                    $('#showCmList').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubEventOrCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
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
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            if (subEventId == '0') {
                $('#subSubEventId').html("<select><option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option></select>");
                $('#showSubSubSubEventOrCmList').html('');
                $('#showSubSubEventOrCmList').html('');
                $('#showCmList').html('');
                return false;
            }

            $.ajax({
                url: "{{ URL::to('ciModerationMarking/getSubSubEvent')}}",
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
                beforeSend: function () {
                    $('#showSubSubSubEventOrCmList').html('');
                    $('#showSubSubEventOrCmList').html('');
                    $('#showCmList').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubEventOrCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
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
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            if (subSubEventId == '0') {
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('#showCmList').html('');
                $('#showSubSubSubEventOrCmList').html('');
                return false;
            }

            $.ajax({
                url: "{{ URL::to('ciModerationMarking/getSubSubSubEvent')}}",
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
                beforeSend: function () {
                    $('#showSubSubSubEventOrCmList').html('');
                    $('#showCmList').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubSubEventOrCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subSubSubEventId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            if (subSubSubEventId == '0') {
                $('#showCmList').html('');
                return false;
            }

            $.ajax({
                url: "{{ URL::to('ciModerationMarking/showMarkingCmList')}}",
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
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        // Start::Sort
        $(document).on("change", "#sortBy", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var sortBy = $("#sortBy").val();

            $.ajax({
                url: "{{ URL::to('ciModerationMarking/showMarkingCmList')}}",
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
                    sub_sub_sub_event_id: subSubSubEventId,
                    sort_by: sortBy,
                },
                beforeSend: function () {
                    $('.marking-cm-list').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });
        //End::Sorty
        //DS Marking Summary Modal
        $(document).on('click', '#buttonDsMarkinSummary', function (e) {
            e.preventDefault();

            var form_data = new FormData($('#submitForm')[0]);
            $.ajax({
                url: "{{URL::to('ciModerationMarking/getDsMarkingSummary')}}",
                type: "POST",
                datatype: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
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

//form submit
        $(document).on('click', '.button-submit', function (e) {
            e.preventDefault();
            var dataId = $(this).attr('data-id');
            var confMsg = dataId == '2' ? 'Send' : 'Save';
            var form_data = new FormData($('#submitForm')[0]);
            form_data.append('data_id', dataId);

            swal({
                title: 'Are you sure?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, ' + confMsg,
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('ciModerationMarking/saveCiModerationMarking')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.button-submit').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('.button-submit').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);

                            var courseId = res.loadData.course_id;
                            var termId = res.loadData.term_id;
                            var eventId = res.loadData.event_id;
                            var subEventId = res.loadData.sub_event_id;
                            var subSubEventId = res.loadData.sub_sub_event_id;
                            var subSubSubEventId = res.loadData.sub_sub_sub_event_id;
                            $.ajax({
                                url: "{{ URL::to('ciModerationMarking/showMarkingCmList')}}",
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
                                    sub_sub_sub_event_id: subSubSubEventId,
                                },
                                beforeSend: function () {
                                    if (subEventId == '0') {
                                        $('#showSubEventOrCmList').html('');
                                    }
                                    if (subSubEventId == '0') {
                                        $('#showSubSubEventOrCmList').html('');
                                    }
                                    if (subSubSubEventId == '0') {
                                        $('#showSubSubSubEventOrCmList').html('');
                                    }
                                    $('#showCmList').html('');
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showCmList').html(res.html);
                                    $('.js-source-states').select2();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
                                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                                    App.unblockUI();
                                }
                            });//ajax
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
                            $('.button-submit').prop('disabled', false);
                            App.unblockUI();
                        }

                    });
                }
            });
        });

        //start :: auto save
        setInterval(function () {
            if ($('#autoSave').val() == 1) {
                var dataId = 1;
                var form_data = new FormData($('#submitForm')[0]);
                form_data.append('data_id', dataId);
                form_data.append('auto_saving', 1);
                $.ajax({
                    url: "{{URL::to('ciModerationMarking/saveCiModerationMarking')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    beforeSend: function () {
                        $('.button-submit').prop('disabled', true);
                        if (dataId == 2) {
                            $('#autoSave').val(0);
                        }
                        toastr.info("@lang('label.SAVING')", "", options);
                    },
                    success: function (res) {
                        $('.button-submit').prop('disabled', false);

                        if (dataId == 2) {
                            $('#autoSave').val(0);
                        }
                        //toastr.success(res.message, res.heading, options);
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
                        $('.button-submit').prop('disabled', false);
                    }

                });
            }

        }, 30000);
        //end :: auto save

//Rquest for unlock
        $(document).on('click', '.request-for-unlock', function (e) {
            e.preventDefault();

            var form_data = new FormData($('#submitForm')[0]);

            $.ajax({
                url: "{{URL::to('ciModerationMarking/getRequestForUnlockModal')}}",
                type: "POST",
                datatype: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                beforeSend: function () {
                    $('#showMessage').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showMessage').html(res.html);
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

//delete
        $(document).on('click', '#buttonDelete', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);

            swal({
                title: 'Are you sure?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('ciModerationMarking/clearMarking')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('#buttonDelete').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('#buttonDelete').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);

                            var courseId = res.loadData.course_id;
                            var termId = res.loadData.term_id;
                            var eventId = res.loadData.event_id;
                            var subEventId = res.loadData.sub_event_id;
                            var subSubEventId = res.loadData.sub_sub_event_id;
                            var subSubSubEventId = res.loadData.sub_sub_sub_event_id;
                            $.ajax({
                                url: "{{ URL::to('ciModerationMarking/showMarkingCmList')}}",
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
                                    sub_sub_sub_event_id: subSubSubEventId,
                                },
                                beforeSend: function () {
                                    if (subEventId == '0') {
                                        $('#showSubEventOrCmList').html('');
                                    }
                                    if (subSubEventId == '0') {
                                        $('#showSubSubEventOrCmList').html('');
                                    }
                                    if (subSubSubEventId == '0') {
                                        $('#showSubSubSubEventOrCmList').html('');
                                    }
                                    $('#showCmList').html('');
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showCmList').html(res.html);
                                    $('.js-source-states').select2();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
                                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                                    App.unblockUI();
                                }
                            });//ajax
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
                }
            });
        });

        $(document).on('click', '.save-request-for-unlock', function (e) {
            e.preventDefault();
            var unlockMessage = $("#unlockMsgId").val();
//            alert(unlockMessage); return false;
            var form_data = new FormData($('#submitForm')[0]);
            form_data.append('unlock_message', unlockMessage);

            swal({
                title: 'Are you sure?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Send',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('ciModerationMarking/saveRequestForUnlock')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            $('.modal').modal('hide');
                            toastr.success(res, '@lang("label.REQUEST_FOR_UNLOCK_HAS_BEEN_SENT_TO_COMDT_SUCCESSFULLY")', options);
                            var courseId = res.loadData.course_id;
                            var termId = res.loadData.term_id;
                            var eventId = res.loadData.event_id;
                            var subEventId = res.loadData.sub_event_id;
                            var subSubEventId = res.loadData.sub_sub_event_id;
                            var subSubSubEventId = res.loadData.sub_sub_sub_event_id;
                            $.ajax({
                                url: "{{ URL::to('ciModerationMarking/showMarkingCmList')}}",
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
                                    sub_sub_sub_event_id: subSubSubEventId,
                                },
                                beforeSend: function () {
                                    if (subEventId == '0') {
                                        $('#showSubEventOrCmList').html('');
                                    }
                                    if (subSubEventId == '0') {
                                        $('#showSubSubEventOrCmList').html('');
                                    }
                                    if (subSubSubEventId == '0') {
                                        $('#showSubSubSubEventOrCmList').html('');
                                    }
                                    $('#showCmList').html('');
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showCmList').html(res.html);
                                    $('.js-source-states').select2();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
                                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                                    App.unblockUI();
                                }
                            });//ajax

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
                }
            });
        });
    });
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
@stop