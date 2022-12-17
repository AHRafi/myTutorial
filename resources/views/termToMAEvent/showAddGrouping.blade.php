<div class="modal-content">
    <div class="modal-header clone-modal-header">
        <button type="button" data-dismiss="modal" data-placement="bottom" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">
            @lang('label.CLOSE')
        </button>
        <h3 class="modal-title text-center">
            @lang('label.ADD_GROUPING')
        </h3>
    </div>
    {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitGroupingForm')) !!}

    <div class="modal-body">
        <div class="row">
            <div class="col-md-4">
                @lang('label.COURSE') : <strong>{!! !empty($request->course) ? $request->course : '' !!}</strong>
                {!! Form::hidden('ma_course_id', $request->course_id, ['id' => 'maCourseId']) !!}
            </div>
            <div class="col-md-4">
                @lang('label.TERM') : <strong>{!! !empty($request->term) ? $request->term : '' !!}</strong>
                {!! Form::hidden('ma_term_id', $request->term_id, ['id' => 'maTermId']) !!}
            </div>
            <div class="col-md-4">
                @lang('label.EVENT') : <strong>{!! !empty($request->ma_event) ? $request->ma_event : '' !!}</strong>
                {!! Form::hidden('ma_event_id', $request->event_id, ['id' => 'maEventId']) !!}
            </div>
        </div>
        <div class="row margin-top-10">
            @if(sizeof($eventList) > 1)
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label col-md-5" for="eventId">@lang('label.GROUPING')&nbsp;@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                    <div class="col-md-7">
                        {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                    </div>
                </div>
            </div>
            @else
            <div class="col-md-12">
                <div class="alert alert-danger alert-dismissable">
                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_HAS_BEEN_MARKED_FOR_MUTUAL_ASSESSMENT_GROUPING') !!}</strong></p>
                </div>
            </div>
            @endif
            <div id="showSubEvent"></div>
            <div id="showSubSubEvent"></div>
            <div id="showSubSubSubEvent"></div>
            <div id="showEventGroup"></div>
        </div>
        <div id="showEventGroupingCm"></div>
    </div>

    <div class="modal-footer">
        <button class="set-grouping btn green" type="button" disabled>
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <button class="delete-grouping btn red-intense" type="button" disabled>
            <i class = "fa fa-trash"></i> @lang('label.DELETE_GROUPING')
        </button>
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-outline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
    {!! Form::close() !!}
</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
    var options = {
        closeButton: true,
        debug: false,
        positionClass: "toast-bottom-right",
        onclick: null
    };
    $(document).on('change', '#eventId', function () {
        var courseId = $("#maCourseId").val();
        var termId = $("#maTermId").val();
        var eventId = $(this).val();

        $('#showSubEvent').html('');
        $('#showSubSubEvent').html('');
        $('#showSubSubSubEvent').html('');
        $('#showEventGroup').html('');
        $('#showEventGroupingCm').html('');
        $('.set-grouping').attr('disabled', 'disabled');
        $('.delete-grouping').attr('disabled', 'disabled');

        if (eventId == '0') {
            return false;
        }

        $.ajax({
            url: "{{ URL::to('termToMAEvent/getSubEventOrGroup')}}",
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
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showSubEvent').html(res.html);
                $('#showEventGroup').html(res.html1);
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

    $(document).on('change', '#subEventId', function () {
        var courseId = $("#maCourseId").val();
        var termId = $("#maTermId").val();
        var eventId = $('#eventId').val();
        var subEventId = $(this).val();

        $('#showSubSubEvent').html('');
        $('#showSubSubSubEvent').html('');
        $('#showEventGroup').html('');
        $('#showEventGroupingCm').html('');
        $('.set-grouping').attr('disabled', 'disabled');
        $('.delete-grouping').attr('disabled', 'disabled');

        if (subEventId == '0') {
            return false;
        }

        $.ajax({
            url: "{{ URL::to('termToMAEvent/getSubSubEventOrGroup')}}",
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
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showSubSubEvent').html(res.html);
                $('#showEventGroup').html(res.html1);
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

    $(document).on('change', '#subSubEventId', function () {
        var courseId = $("#maCourseId").val();
        var termId = $("#maTermId").val();
        var eventId = $('#eventId').val();
        var subEventId = $('#subEventId').val();
        var subSubEventId = $(this).val();

        $('#showSubSubSubEvent').html('');
        $('#showEventGroup').html('');
        $('#showEventGroupingCm').html('');
        $('.set-grouping').attr('disabled', 'disabled');
        $('.delete-grouping').attr('disabled', 'disabled');

        if (subSubEventId == '0') {
            return false;
        }

        $.ajax({
            url: "{{ URL::to('termToMAEvent/getSubSubSubEventOrGroup')}}",
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
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showSubSubSubEvent').html(res.html);
                $('#showEventGroup').html(res.html1);
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

    $(document).on('change', '#subSubSubEventId', function () {
        var courseId = $("#maCourseId").val();
        var termId = $("#maTermId").val();
        var eventId = $("#eventId").val();
        var subEventId = $('#subEventId').val();
        var subSubEventId = $('#subSubEventId').val();
        var subSubSubEventId = $(this).val();

        $('#showEventGroup').html('');
        $('#showEventGroupingCm').html('');
        $('.set-grouping').attr('disabled', 'disabled');
        $('.delete-grouping').attr('disabled', 'disabled');

        if (eventId == '0') {
            return false;
        }

        $.ajax({
            url: "{{ URL::to('termToMAEvent/getGroup')}}",
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
                $('#showEventGroup').html(res.html1);
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

    $(document).on('change', '#eventGroupId', function () {
        var courseId = $("#maCourseId").val();
        var termId = $("#maTermId").val();
        var maEventId = $("#maEventId").val();
        var eventId = $("#eventId").val();
        var subEventId = $('#subEventId').val();
        var subSubEventId = $('#subSubEventId').val();
        var subSubSubEventId = $("#subSubSubEventId").val();
        var eventGroupId = $(this).val();

        $('#showEventGroupingCm').html('');
        $('.set-grouping').attr('disabled', 'disabled');
        $('.delete-grouping').attr('disabled', 'disabled');

        if (typeof subEventId == 'undefined') {
            subEventId = '0';
        }
        if (typeof subSubEventId == 'undefined') {
            subSubEventId = '0';
        }
        if (typeof subSubSubEventId == 'undefined') {
            subSubSubEventId = '0';
        }
        if (eventGroupId == '0') {
            return false;
        }

        $.ajax({
            url: "{{ URL::to('termToMAEvent/getGroupingCm')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
                ma_event_id: maEventId,
                event_id: eventId,
                sub_event_id: subEventId,
                sub_sub_event_id: subSubEventId,
                sub_sub_sub_event_id: subSubSubEventId,
                event_group_id: eventGroupId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showEventGroupingCm').html(res.html);
                if (res.enableSubmit == 1) {
                    $('.set-grouping').removeAttr('disabled');
                    $('.delete-grouping').removeAttr('disabled');
                }
                $('.tooltips').tooltip();
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
        });//ajax
    });
    $(document).on('click', '#assignSelectedCm', function () {
        var courseId = $("#maCourseId").val();
        var termId = $("#maTermId").val();
        var eventId = $("#eventId").val();
        var subEventId = $('#subEventId').val();
        var subSubEventId = $('#subSubEventId').val();
        var subSubSubEventId = $("#subSubSubEventId").val();
        var eventGroupId = $("#eventGroupId").val();

        var gpArr = [];
        $(".gp-check:checked").each(function () {
            var gpId = $(this).attr('data-id');
            gpArr.push(gpId);
        });


        if (typeof subEventId == 'undefined') {
            subEventId = '0';
        }
        if (typeof subSubEventId == 'undefined') {
            subSubEventId = '0';
        }
        if (typeof subSubSubEventId == 'undefined') {
            subSubSubEventId = '0';
        }

        $('.set-grouping').attr('disabled', 'disabled');
        $('.delete-grouping').attr('disabled', 'disabled');

        $.ajax({
            url: "{{ URL::to('termToMAEvent/setGroupingCm')}}",
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
                event_group_id: eventGroupId,
                gp_arr: gpArr,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showCmList').html(res.html);
                if (res.enableSubmit == 1) {
                    $('.set-grouping').removeAttr('disabled');
                }
                $('.tooltips').tooltip();
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
                    toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
                }
                App.unblockUI();
            }
        });//ajax
    });


//        Start:: Save  Grouping
    $(document).on('click', '.set-grouping', function (e) {
        e.preventDefault();
        var form_data = new FormData($('#submitGroupingForm')[0]);
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
                    url: "{{URL::to('termToMAEvent/setAddGrouping')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: form_data,
                    beforeSend: function () {
                        $(this).prop('disablded', true);
                        App.blockUI({boxed: true});
                    },
                    success: function (res) {
                        toastr.success(res, res.message, options);
                        $(this).prop('disablded', false);
                        $('delete-grouping').removeAttr('disablded');
                        App.unblockUI();

                        var courseId = $("#maCourseId").val();
                        var termId = $("#maTermId").val();
                        var maEventId = $("#maEventId").val();
                        var eventId = $("#eventId").val();
                        var subEventId = $('#subEventId').val();
                        var subSubEventId = $('#subSubEventId').val();
                        var subSubSubEventId = $("#subSubSubEventId").val();
                        var eventGroupId = $("#eventGroupId").val();

                        $('#showEventGroupingCm').html('');
                        $('.set-grouping').attr('disabled', 'disabled');
                        $('.delete-grouping').attr('disabled', 'disabled');

                        if (typeof subEventId == 'undefined') {
                            subEventId = '0';
                        }
                        if (typeof subSubEventId == 'undefined') {
                            subSubEventId = '0';
                        }
                        if (typeof subSubSubEventId == 'undefined') {
                            subSubSubEventId = '0';
                        }
                        if (eventGroupId == '0') {
                            return false;
                        }

                        $.ajax({
                            url: "{{ URL::to('termToMAEvent/getGroupingCm')}}",
                            type: "POST",
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                course_id: courseId,
                                term_id: termId,
                                ma_event_id: maEventId,
                                event_id: eventId,
                                sub_event_id: subEventId,
                                sub_sub_event_id: subSubEventId,
                                sub_sub_sub_event_id: subSubSubEventId,
                                event_group_id: eventGroupId,
                            },
                            beforeSend: function () {
                                App.blockUI({boxed: true});
                            },
                            success: function (res) {
                                $('#showEventGroupingCm').html(res.html);
                                if (res.enableSubmit == 1) {
                                    $('.set-grouping').removeAttr('disabled');
                                    $('.delete-grouping').removeAttr('disabled');
                                }
                                $('.tooltips').tooltip();
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
                        });//ajax
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
                        $(this).prop('disablded', false);
                        App.unblockUI();
                    }
                });
            }
        });
    });
//        End:: Save Grouping

//        Start:: Delete  Grouping
    $(document).on('click', '.delete-grouping', function (e) {
        e.preventDefault();
        var form_data = new FormData($('#submitGroupingForm')[0]);
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
                    url: "{{URL::to('termToMAEvent/deleteGrouping')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: form_data,
                    beforeSend: function () {
                        $(this).prop('disablded', true);
                        $(".set-grouping").prop('disablded', true);
                        App.blockUI({boxed: true});
                    },
                    success: function (res) {
                        toastr.success(res, res.message, options);
                        $(this).prop('disablded', false);
                        $('.set-grouping').prop('disablded', false);
                        App.unblockUI();

                        var courseId = $("#maCourseId").val();
                        var termId = $("#maTermId").val();
                        var maEventId = $("#maEventId").val();
                        var eventId = $("#eventId").val();
                        var subEventId = $('#subEventId').val();
                        var subSubEventId = $('#subSubEventId').val();
                        var subSubSubEventId = $("#subSubSubEventId").val();
                        var eventGroupId = $("#eventGroupId").val();

                        $('#showEventGroupingCm').html('');
                        $('.set-grouping').attr('disabled', 'disabled');
                        $('.delete-grouping').attr('disabled', 'disabled');

                        if (typeof subEventId == 'undefined') {
                            subEventId = '0';
                        }
                        if (typeof subSubEventId == 'undefined') {
                            subSubEventId = '0';
                        }
                        if (typeof subSubSubEventId == 'undefined') {
                            subSubSubEventId = '0';
                        }
                        if (eventGroupId == '0') {
                            return false;
                        }

                        $.ajax({
                            url: "{{ URL::to('termToMAEvent/getGroupingCm')}}",
                            type: "POST",
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                course_id: courseId,
                                term_id: termId,
                                ma_event_id: maEventId,
                                event_id: eventId,
                                sub_event_id: subEventId,
                                sub_sub_event_id: subSubEventId,
                                sub_sub_sub_event_id: subSubSubEventId,
                                event_group_id: eventGroupId,
                            },
                            beforeSend: function () {
                                App.blockUI({boxed: true});
                            },
                            success: function (res) {
                                $('#showEventGroupingCm').html(res.html);
                                if (res.enableSubmit == 1) {
                                    $('.set-grouping').removeAttr('disabled');
                                    $('.delete-grouping').removeAttr('disabled');
                                }
                                $('.tooltips').tooltip();
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
                        });//ajax
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
                        $(this).prop('disablded', false);
                        $('.set-grouping').prop('disablded', false);
                        App.unblockUI();
                    }
                });
            }
        });
    });
//        End:: Delete Grouping

});
</script>
