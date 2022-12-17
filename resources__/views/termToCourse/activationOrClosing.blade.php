@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.TERM_SCHEDULING_ACTIVATION_CLOSING')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            {{csrf_field()}}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-3"></div>
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

                    </div>

                    <div id="termSchedule">
                        <div class="row">
                            <div class="col-md-12 table-responsive">
                                <div class="webkit-scrollbar">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                                <th class="vcenter">@lang('label.TERM')</th>
                                                <th class="text-center vcenter">@lang('label.INITIAL_DATE')</th>
                                                <th class="text-center vcenter">@lang('label.TERMINATION_DATE')</th>
                                                <th class="text-center vcenter">@lang('label.NUMBER_OF_WEEK')</th>
                                                <th class="text-center vcenter">@lang('label.ACTIVE')</th>
                                                <th class="text-center vcenter">@lang('label.STATUS')</th>
                                                <th class="text-center vcenter">@lang('label.ACTION')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!$activeInactiveTerm->isEmpty())
                                            <?php $sl = 0; ?>
                                            @foreach($activeInactiveTerm as $termInfo)
                                            <?php
                                            //            check and show previous value
                                            $checked = '';
                                            $radioChecked = '';
                                            $disabled = '';
                                            if (in_array($termInfo->status, ['0', '2'])) {
                                                $disabled = 'disabled';
                                            } elseif ($termInfo->active == '1') {
                                                $radioChecked = 'checked';
                                            }
                                            ?>
                                            <tr>
                                                <td class="text-center vcenter">{!! ++$sl !!}</td>
                                                <td class="vcenter">{{$termInfo->term_name}}</td>
                                                <td class="vcenter text-center">{{!empty($termInfo->initial_date) ? Helper::formatDate($termInfo->initial_date) : ''}}</td>
                                                <td class="vcenter text-center">{{!empty($termInfo->initial_date) ? Helper::formatDate($termInfo->termination_date) : ''}}</td>
                                                <td class="vcenter text-center">{{ !empty($termInfo->number_of_week) ? $termInfo->number_of_week : '' }}</td>
                                                <td class="vcenter">

                                                    <div class="md-radio-list">
                                                        <div class="md-radio">
                                                            <input class="redioAcIn md-radiobtn" type="radio" id="radio-{{$termInfo->id}}" name="admin_si" value="{{$termInfo->id}}" data-course-id="{!! !empty($termInfo->course_id)?$termInfo->course_id:'' !!}" data-term-id="{!! !empty($termInfo->term_id)?$termInfo->term_id:'' !!}" data-id="{!! !empty($termInfo->id)?$termInfo->id:'' !!}"  data-status="{!! $termInfo->status=='0'?'1':'0'!!}"  {{ $radioChecked }} {{$disabled}}>
                                                            <label for="radio-{{$termInfo->id}}">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> @lang('label.YES')</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="vcenter text-center">
                                                    @if($termInfo->status=='0')
                                                    <span class="label label-sm label-blue-soft">@lang('label.NOT_INITIATED')</span>
                                                    @elseif($termInfo->status=='2')
                                                    <span class="label label-sm label-red-intense">@lang('label.CLOSED')</span>
                                                    @else
                                                    <span class="label label-sm label-green-seagreen">@lang('label.INITIATED')</span>
                                                    @endif
                                                </td>
                                                <td class="vcenter text-center">
                                                    @if($termInfo->status=='0')
                                                    <button class="btn btn-success btn-xs activeIn tooltips activeInactive"  type="button" data-placement="top" data-rel="tooltip" data-course-id="{!! !empty($termInfo->course_id)?$termInfo->course_id:'' !!}" data-term-id="{!! !empty($termInfo->term_id)?$termInfo->term_id:'' !!}" data-id="{!! !empty($termInfo->id)?$termInfo->id:'' !!}"  data-status="{!! $termInfo->status=='0'?'1':'0'!!}" title="Activate/Initiate This Term">
                                                        <i class="fa fa-play"></i>
                                                    </button>
                                                    @elseif($termInfo->status=='1')
                                                    @if(!empty($closeConditionArr['has_close'][$termInfo->term_id]))
                                                    <?php
                                                    $disabled = 'cursor-default';
                                                    $btnType = 'type=button';
                                                    $btnClass = '';
                                                    $btnColor = 'grey-mint';
                                                    $btnLabel = __('label.ASSESSMENT_PROCESS_OF_THIS_TERM_IS_NOT_COMPLETED_YET');
                                                    if (!empty($closeConditionArr['can_close'][$termInfo->term_id])) {
                                                        $disabled = '';
                                                        $btnType = '';
                                                        $btnClass = 'activeInactive';
                                                        $btnColor = 'green-seagreen';
                                                        $btnLabel = __('label.CLOSE_THIS_TERM');
                                                    }
                                                    ?>
                                                    <button {{$btnType}} class="btn btn-xs {{$btnColor}} {{$btnClass}} tooltips {{$disabled}}"  
                                                        data-placement="top" data-rel="tooltip" title="{{$btnLabel}}"
                                                        data-course-id="{!! !empty($termInfo->course_id)?$termInfo->course_id:'' !!}" 
                                                        data-term-id="{!! !empty($termInfo->term_id)?$termInfo->term_id:'' !!}"  
                                                        data-id="{!! !empty($termInfo->id)?$termInfo->id:'' !!}" 
                                                        data-status="{!!$termInfo->status=='1'?'2':'1'!!}"  data-original-title="{{$btnLabel}}">
                                                        <i class="fa fa-stop"></i>
                                                    </button>
                                                    @endif
                                                    @elseif($termInfo->status=='2')
                                                    @if($ciObsnInfo->isEmpty())
                                                    <button class="btn btn-success btn-xs activeIn tooltips activeInactive"  type="button" 
                                                            data-course-id="{!! !empty($termInfo->course_id)?$termInfo->course_id:'' !!}" 
                                                            data-term-id="{!! !empty($termInfo->term_id)?$termInfo->term_id:'' !!}" 
                                                            data-id="{!! !empty($termInfo->id)?$termInfo->id:'' !!}" 
                                                            data-status="{!!$termInfo->status=='2'?'1':'0'!!}"  title="@lang('label.REACTIVATE_THIS_TERM')">
                                                        <i class="fa fa-fast-forward"></i>
                                                    </button>
                                                    @endif
                                                    @endif
                                                    @if($termInfo->status !='0')
                                                    <button class="btn btn-xs purple-wisteria bold tooltips term-marking-status"
                                                            title="@lang('label.CLICK_HERE_TO_VIEW_TERM_STATUS_SUMMARY')" type=" button" data-placement="top"
                                                            data-rel="tooltip" course-id="{!! $termInfo->course_id !!}" term-id="{!! $termInfo->term_id !!}"
                                                            data-original-title="@lang('label.CLICK_HERE_TO_VIEW_TERM_STATUS_SUMMARY')" data-target="#modalInfo" data-toggle="modal">
                                                        <i class="fa fa-info-circle"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($sl < sizeof($activeInactiveTerm))
                                            <tr class="active">
                                                <td class="text-center vcenter"></td>
                                                <td class="vcenter">{{__('label.RECESS_NO', ['sl' => $sl])}}</td>
                                                <td class="vcenter text-center">{{!empty($termInfo->recess_initial_date) ? Helper::formatDate($termInfo->recess_initial_date) : ''}}</td>
                                                <td class="vcenter text-center">{{!empty($termInfo->recess_initial_date) ? Helper::formatDate($termInfo->recess_termination_date) : ''}}</td>
                                                <td class="vcenter text-center">{{ !empty($termInfo->recess_number_of_week) ? $termInfo->recess_number_of_week : '' }}</td>
                                                <td class="vcenter text-center" colspan="3"></td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="10">@lang('label.NO_INITIATED_TERM_FOUND')</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
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

                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null,
        };
//        $(document).on('change', '#courseId', function () {
//            var trainingYearId = $("#trainingYearId").val();
//            var courseId = $("#courseId").val();
//            if (courseId == '0' || trainingYearId == '0') {
//                $('#termSchedule').html('');
//                return false;
//            }
//            $.ajax({
//                url: "{{URL::to('termToCourse/getActiveOrClose')}}",
//                type: "POST",
//                datatype: 'json',
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                data: {
//                    training_year_id: trainingYearId,
//                    course_id: courseId,
//
//                },
//                beforeSend: function () {
//                    App.blockUI({boxed: true});
//                },
//                success: function (res) {
//                    $("#termSchedule").html(res.html);
//                    $(".previnfo").html('');
//                    $('.tooltips').tooltip();
//                    App.unblockUI();
//                },
//                error: function (jqXhr, ajaxOptions, thrownError) {
//                    if (jqXhr.status == 400) {
//                        var errorsHtml = '';
//                        var errors = jqXhr.responseJSON.message;
//                        $.each(errors, function (key, value) {
//                            errorsHtml += '<li>' + value[0] + '</li>';
//                        });
//                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
//                    } else if (jqXhr.status == 401) {
//                        toastr.error(jqXhr.responseJSON.message, '', options);
//                    } else {
//                        toastr.error('Error', 'Something went wrong', options);
//                    }
//                    App.unblockUI();
//                }
//            });
//        });


        $(document).on('click', '.activeInactive', function (e) {
            e.preventDefault();
            var termId = $(this).data('term-id');
            var courseId = $(this).data('course-id');
            var status = $(this).data('status');
            var id = $(this).data('id');
            var confirm = 'Activate/Initiate';
            if (status == '2') {
                confirm = 'Close';
            }

            swal({
                title: 'Are you sure?',
                   
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, ' + confirm,
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('termToCourse/activeInactive')}}",
                        type: "POST",
                        datatype: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            course_id: courseId,
                            term_id: termId,
                            status: status,
                            id: id,
                        },
                        success: function (res) {
                            //console.log(res);return false;
                            toastr.success(res.message, 'Success', options);
                            // setTimeout(location.reload.bind(location), 2000);
                            var trainingYearId = $("#trainingYearId").val();
                            var courseId = $("#courseId").val();
                            if (courseId == '0' || trainingYearId == '0') {
                                $('#termSchedule').html('');
                                return false;
                            }
                            $.ajax({
                                url: "{{URL::to('termToCourse/getActiveOrClose')}}",
                                type: "POST",
                                datatype: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    training_year_id: trainingYearId,
                                    course_id: courseId,

                                },
                                beforeSend: function () {
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $("#termSchedule").html(res.html);
                                    $(".previnfo").html('');
                                    $('.tooltips').tooltip();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
                                    toastr.error('Error', 'Something went wrong', options);
                                    App.unblockUI();
                                }
                            });

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


        $(document).on('click', '.redioAcIn', function (e) {
            e.preventDefault();
            var termId = $(this).data('term-id');
            var courseId = $(this).data('course-id');
            var status = $(this).data('status');
            var id = $(this).data('id');

            $.ajax({
                url: "{{URL::to('termToCourse/redioAcIn')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    status: status,
                    id: id,
                },
                success: function (res) {
                    //console.log(res);return false;
                    toastr.success(res.message, 'Success', options);
                    // setTimeout(location.reload.bind(location), 2000);
                    var trainingYearId = $("#trainingYearId").val();
                    var courseId = $("#courseId").val();
                    if (courseId == '0' || trainingYearId == '0') {
                        $('#termSchedule').html('');
                        return false;
                    }
                    $.ajax({
                        url: "{{URL::to('termToCourse/getActiveOrClose')}}",
                        type: "POST",
                        datatype: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            training_year_id: trainingYearId,
                            course_id: courseId,

                        },
                        beforeSend: function () {
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $("#termSchedule").html(res.html);
                            $(".previnfo").html('');
                            $('.tooltips').tooltip();
                            App.unblockUI();
                        },
                        error: function (jqXhr, ajaxOptions, thrownError) {
                            toastr.error('Error', 'Something went wrong', options);
                            App.unblockUI();
                        }
                    });

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
        });

        //Start:: Request for course status summary
        $(document).on('click', '.term-marking-status', function (e) {
            e.preventDefault();
            var courseId = $(this).attr('course-id');
            var termId = $(this).attr('term-id');
            $.ajax({
                url: "{{URL::to('termToCourse/requestCourseSatatusSummary')}}",
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
                url: "{{URL::to('termToCourse/getDsMarkingSummary')}}",
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