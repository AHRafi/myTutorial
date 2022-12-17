@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-calendar"></i>@lang('label.COURSE_ID_LIST')
            </div>
            <div class="actions">
                @if(empty($activeCourse))
                <a class="btn btn-default btn-sm create-new"
                   href="{{ URL::to('courseId/create'.Helper::queryPageStr($qpArr)) }}">
                    @lang('label.CREATE_NEW_COURSE_ID')
                    <i class="fa fa-plus create-new"></i>
                </a>
                @endif
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <!-- Begin Filter-->
                {!! Form::open(array('group' => 'form', 'url' => 'courseId/filter','class' => 'form-horizontal'))
                !!}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                            <div class="col-md-8">
                                {!! Form::text('fil_search', Request::get('fil_search'), ['class' => 'form-control
                                tooltips', 'id' => 'filSearch', 'title' => 'Name', 'placeholder' => 'Name',
                                'list' => 'courseIdName', 'autocomplete' => 'off']) !!}
                                <datalist id="courseIdName">
                                    @if (!$nameArr->isEmpty())
                                    @foreach($nameArr as $item)
                                    <option value="{{$item->name}}" />
                                    @endforeach
                                    @endif
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                <i class="fa fa-search"></i> @lang('label.FILTER')
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- End Filter -->
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="vcenter">@lang('label.TRAINING_YEAR')</th>
                            <th class="vcenter">@lang('label.COURSE_ID')</th>
                            <th class="text-center vcenter">@lang('label.TENURE')</th>
                            <th class="text-center vcenter">@lang('label.NO_OF_WEEKS')</th>
                            <th class="text-center vcenter">@lang('label.SHORT_INFO')</th>
                            <th class="text-center vcenter">@lang('label.TOTAL_COURSE_WT')</th>
                            <th class="text-center vcenter">@lang('label.EVENT_MKS_LIMIT')</th>
                            <th class="text-center vcenter">@lang('label.HIGHEST_MKS_LIMIT')</th>
                            <th class="text-center vcenter">@lang('label.LOWEST_MKS_LIMIT')</th>
                            <th class="text-center vcenter">@lang('label.STATUS')</th>
                            <th class="td-actions text-center vcenter">@lang('label.ACTION')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$targetArr->isEmpty())
                        <?php
                        $page = Request::get('page');
                        $page = empty($page) ? 1 : $page;
                        $sl = ($page - 1) * Session::get('paginatorCount');
                        ?>
                        @foreach($targetArr as $target)
                        <tr>
                            <td class="text-center vcenter">{{ ++$sl }}</td>
                            <td class="vcenter">{{ $target->tranining_year_name }}</td>
                            <td class="vcenter">{{ $target->name }}</td>
                            <td class="text-center vcenter">
                                {{ Helper::printDate($target->initial_date) .' '. __('label.TO') .' '.  Helper::printDate($target->termination_date) }}
                            </td>
                            <td class="text-center vcenter">{{ $target->no_of_weeks }}</td>
                            <td class="text-center vcenter">{{ $target->short_info }}</td>
                            <td class="text-center vcenter">{{ $target->total_course_wt }}</td>
                            <td class="text-center vcenter">{{ $target->event_mks_limit }}</td>
                            <td class="text-center vcenter">{{ $target->highest_mks_limit }}</td>
                            <td class="text-center vcenter">{{ $target->lowest_mks_limit }}</td>
                            <td class="text-center vcenter">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                @elseif($target->status == '0')
                                <span class="label label-sm label-info">@lang('label.INACTIVE')</span>
                                @else
                                <span class="label label-sm label-warning">@lang('label.CLOSED')</span>
                                @endif
                            </td>
                            <?php
                            $disabled = 'cursor-default';
                            $btnType = '';
                            $btnClass = '';
                            $btnColor = 'grey-mint';
                            $btnLabel = __('label.ASSESSMENT_PROCESS_OF_THIS_COURSE_IS_NOT_COMPLETED_YET');
                            if (!empty($comdtObsnList)) {
                                if (in_array($target->id, $comdtObsnList)) {
                                    $disabled = '';
                                    $btnType = 'type="button"';
                                    $btnClass = 'close-btn';
                                    $btnColor = 'green-seagreen';
                                    $btnLabel = __('label.CLOSE_THIS_COURSE');
                                }
                            }
                            ?>
                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">
                                    @if ($target->status !='2' )
                                    <button class="btn btn-xs {{$btnColor}} {{$btnClass}} tooltips {{$disabled}}"
                                            title="{{$btnLabel}}" {{$btnType}} data-placement="top"
                                            data-rel="tooltip" data-id="{!! $target->id !!}"
                                            data-original-title="{{$btnLabel}}">
                                        <i class="fa fa-stop"></i>
                                    </button>
                                    @endif
                                    @if($target->status == '2')
                                    <!--                                    <button class="btn btn-xs btn-success reactive-btn tooltips"  type="button" data-course-id="" data-term-id="" data-id="{!! $target->id !!}" data-status=""  title="@lang('label.REACTIVE_THIS_COURSE')">
                                                                            <i class="fa fa-fast-forward"></i>
                                                                        </button>-->
                                    @endif
                                    @if ($target->status =='1' && empty($target->event_cloned))
                                    <button class="btn btn-xs yellow-casablanca clone-event tooltips"
                                            title="{{__('label.CLICK_HERE_TO_CLONE_EVENTS')}}" type="button" data-placement="top"
                                            data-rel="tooltip" data-id="{!! $target->id !!}"
                                            data-original-title="{{__('label.CLICK_HERE_TO_CLONE_EVENTS')}}" 
                                            data-target="#cloneEventModalInfo" data-toggle="modal">
                                        <i class="fa fa-clone"></i>
                                    </button>
                                    @endif
                                    <button class="btn btn-xs purple-wisteria bold tooltips course-marking-summary"
                                            title="@lang('label.CLICK_HERE_TO_SEE_COURSE_MARKING_STATUS_SUMMARY')" type=" button" data-placement="top"
                                            data-rel="tooltip" course-id="{!! $target->id !!}"
                                            data-original-title="@lang('label.CLICK_HERE_TO_SEE_COURSE_MARKING_STATUS_SUMMARY')" data-target="#modalInfo" data-toggle="modal" id="courseStatusSummaryId">
                                        <i class="fa fa-info-circle"></i>
                                    </button>
                                    @if($target->status != '2')
                                    {{ Form::open(array('url' => 'courseId/' . $target->id.'/'.Helper::queryPageStr($qpArr))) }}
                                    {{ Form::hidden('_method', 'DELETE') }}
                                    <a class="btn btn-xs btn-primary tooltips margin-top-5" title="Edit"
                                       href="{{ URL::to('courseId/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips margin-top-5" title="Delete"
                                            type="submit" data-placement="top" data-rel="tooltip"
                                            data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    {{ Form::close() }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="12" class="vcenter">@lang('label.NO_COURSE_ID_FOUND')</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @include('layouts.paginator')
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


<!--Cloned Event Modal -->
<div class="modal fade" id="cloneEventModalInfo" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showCloneEvent"></div>
    </div>
</div>
<!--End Cloned Event Modal -->


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
            onclick: null,
        };

        $(document).on('click', '.close-btn', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal({
                title: 'Are you sure,You want to Close?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Close',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('courseId/close')}}",
                        type: "POST",
                        datatype: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                        },

                        success: function (res) {
                            toastr.success(res.message, 'Success', options);
                            setTimeout(location.reload.bind(location), 1000);
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
                                var errorsHtml = 'SI Impr Mks have not been Locked for following Wing :';
                                if (typeof (errors) === 'object') {
                                    $.each(errors, function (key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, '', options);
                                } else {
                                    toastr.error(jqXhr.responseJSON.message, '', options);
                                }
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            App.unblockUI();
                        }
                    });
                }
            });
        });
        // Start:: Course Reactive
        $(document).on('click', '.reactive-btn', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal({
                title: 'Are you sure,You want to Reactivate?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Reactivate',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('courseId/reactive')}}",
                        type: "POST",
                        datatype: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                        },

                        success: function (res) {
                            toastr.success(res.message, 'Success', options);
                            setTimeout(location.reload.bind(location), 1000);
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
                                var errorsHtml = 'SI Impr Mks have not been Locked for following Wing :';
                                if (typeof (errors) === 'object') {
                                    $.each(errors, function (key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, '', options);
                                } else {
                                    toastr.error(jqXhr.responseJSON.message, '', options);
                                }
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            App.unblockUI();
                        }
                    });
                }
            });
        });

        // End:: Course Reactive

        //Start:: Request for course status summary
        $(document).on('click', '#courseStatusSummaryId', function (e) {
            e.preventDefault();
            var courseId = $(this).attr('course-id');
            $.ajax({
                url: "{{URL::to('courseId/requestCourseSatatusSummary')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
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


        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            if (eventId == '0') {
                $('#showSubEvent').html('');
                return false;
            }

            $.ajax({
                url: "{{ URL::to('eventToSubEvent/getClonedEvent')}}",
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


        //Start:: Cloned Event Status
        $(document).on('click', '.clone-event', function (e) {
            e.preventDefault();
            var courseId = $(this).attr('data-id');


            $('#showCloneEvent').html('');
            $('#showPrevCourseEvent').html('');
            $('#submitEventCloning').remove();
            if (courseId == '0') {
                return false;
            }
            $.ajax({
                url: "{{URL::to('courseId/getCloneEvent')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                },
                success: function (res) {
                    $('#showCloneEvent').html(res.html);
                    $('.js-source-states').select2();
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


        //Start:: Cloned Event Status
        $(document).on('change', '#prevCourseId', function (e) {
            e.preventDefault();
            var prevCourseId = $('#prevCourseId').val();

            $('#showPrevCourseEvent').html('');
            $('#submitEventCloning').remove();
            if (prevCourseId == '0') {
                return false;
            }
            $.ajax({
                url: "{{URL::to('courseId/getPrevCourseEvent')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    prev_course_id: prevCourseId,
                },
                beforeSend: function () {
                },
                success: function (res) {
                    $('#showPrevCourseEvent').html(res.html);
                    $('.modal-footer').prepend(res.html2);
                    $('.js-source-states').select2();
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

        //end:: Cloned Status Status

        $(document).on('click', '#submitEventCloning', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitCloneEventForm')[0]);
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
                        url: "{{URL::to('courseId/setCloneEvent')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('#submitEventCloning').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success(res.message, res.heading, options);
                            location.reload();
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
                            $('#submitEventCloning').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });


        //DS Marking Summary Modal
        $(document).on('click', '.ds-marking-status', function (e) {
            e.preventDefault();
            var courseId = $(this).attr('course-id');
            var dataId = $(this).attr('data-id');
            var termId = $(this).attr('term-id');
            var eventId = $(this).attr('event-id');
            var subEventId = $(this).attr('sub-event-id');
            var subSubEventId = $(this).attr('sub-sub-event-id');
            var subSubSubEventId = $(this).attr('sub-sub-sub-event-id');
            $.ajax({
                url: "{{URL::to('courseId/getDsMarkingSummary')}}",
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
    });
</script>

@stop