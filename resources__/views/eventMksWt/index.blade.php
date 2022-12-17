@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EVENT_MKS_WT_DISTRIBUTION')
            </div>
            <div class="actions">
                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#" data-original-title="" title=""> </a>
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-3"></div>
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

                    </div>

                    <!--get module data-->
                    <div id="showEventMksWt" class="col-md-offset-2 col-md-7">
                        <div class="row">
                            <!--                            @if(!empty($eventAssessmentMarkingData))
                                                        <div class="col-md-12">
                                                            <div class="alert alert-danger alert-dismissable">
                                                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.MARKING_HAS_ALREADY_BEEN_STARTED') !!}</strong></p>
                                                            </div>
                                                        </div>
                                                        @endif-->
                            @if(!empty($totalEventWt))
                            @if(!empty($eventArr))

                            <div class="col-md-8 margin-top-10">
                                <span class="label label-md bold label-blue-steel">
                                    @lang('label.TOTAL_EVENT'):&nbsp;{!! sizeof($eventArr) !!}
                                </span>&nbsp;
                                <span class="label label-md bold label-green-soft">
                                    @lang('label.TOTAL_EVENT_WT'):&nbsp;{!! $totalEventWt->total_event_wt ?? '' !!}
                                    {!! Form::hidden('total_event_wt', !empty($totalEventWt->total_event_wt) ? $totalEventWt->total_event_wt : null,['id' => 'totalEventWt'] )!!}
                                </span>
                            </div>
                            @if(empty($eventAssessmentMarkingData))
                            <div class="col-md-4 margin-top-5 text-right">
                                <button class="btn green btn-danger tooltips" type="button" id="buttonDelete" >
                                    <i class="fa fa-trash"></i> &nbsp;@lang('label.DELETE_MKS_WT')
                                </button>
                            </div>
                            @endif
                            <div class="col-md-12 margin-top-10">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>

                                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                            <th class="vcenter">@lang('label.EVENT')</th>
                                            <th class="text-center vcenter">@lang('label.MKS')</th>
                                            <th class="text-center vcenter">@lang('label.HIGHEST')</th>
                                            <th class="text-center vcenter">@lang('label.LOWEST')</th>
                                            <th class="text-center vcenter">@lang('label.WT')</th>

                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $i = 0;
                                        
                                        ?>
                                        @foreach($eventArr as $eventId => $eventName)
                                        <?php
                                        $readOnly = '';
                                        if (!empty($eventAssessmentMarkingData)) {
                                            if (array_key_exists($eventId, $eventAssessmentMarkingData)) {
                                                $readOnly = !empty($eventAssessmentMarkingData) ? 'readonly' : '';
                                            }
                                        }


                                        $eventMksLimit = !empty($eventMksWtArr[$eventId]['mks_limit']) ? $eventMksWtArr[$eventId]['mks_limit'] : (!empty($eventMksWtArr['mks_limit']) ? $eventMksWtArr['mks_limit'] : null);
                                        $eventHighestMksLimit = !empty($eventMksWtArr[$eventId]['highest_mks_limit']) ? $eventMksWtArr[$eventId]['highest_mks_limit'] : (!empty($eventMksWtArr['highest_mks_limit']) ? $eventMksWtArr['highest_mks_limit'] : null);
                                        $eventLowestMksLimit = !empty($eventMksWtArr[$eventId]['lowest_mks_limit']) ? $eventMksWtArr[$eventId]['lowest_mks_limit'] : (!empty($eventMksWtArr['lowest_mks_limit']) ? $eventMksWtArr['lowest_mks_limit'] : null);
                                        $eventWt = !empty($eventMksWtArr[$eventId]['wt']) ? $eventMksWtArr[$eventId]['wt'] : null;
                                        ?>
                                        <tr>
                                            <td class="vcenter text-center">{!! ++$i !!}</td>
                                            <td class="vcenter">{!! $eventName ?? '' !!}</td>
                                            <td class="vcenter width-80">
                                                {!! Form::text('event_mks_wt['.$eventId.'][mks]', $eventMksLimit,['id' => 'mksLimit_'.$eventId, 'data-key' => $eventId, 'class' => 'mks-limit form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readOnly]) !!}
                                            </td>
                                            <td class="vcenter width-80">
                                                {!! Form::text('event_mks_wt['.$eventId.'][highest]', $eventHighestMksLimit,['id' => 'highestLimit_'.$eventId, 'data-key' => $eventId, 'class' => 'highest-mks form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readOnly]) !!}
                                            </td>
                                            <td class="vcenter width-80">
                                                {!! Form::text('event_mks_wt['.$eventId.'][lowest]', $eventLowestMksLimit,['id' => 'lowestLimit_'.$eventId, 'data-key' => $eventId, 'class' => 'lowest-limit form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readOnly]) !!}
                                            </td>
                                            <td class="vcenter width-80">
                                                {!! Form::text('event_mks_wt['.$eventId.'][wt]', $eventWt,['id' => 'wt_'.$eventId, 'data-key' => $eventId, 'class' => 'wt-distributed form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readOnly]) !!}
                                            </td>
                                        </tr>
                                        @endforeach
                                        {!! Form::hidden('total_wt', !empty($total) ? $total : null,['id' => 'totalWt']) !!}
                                        <tr>
                                            <td class="vcenter text-right bold" colspan="5">@lang('label.TOTAL')</td>
                                            <td class="vcenter text-right">
                                                <span class="total-wt bold">{!! !empty($total) ? Helper::numberFormat2Digit($total) : '' !!}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 text-center">
                                <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                                    <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                </button>
                                <a href="{{ URL::to('eventMksWt') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                            </div>
                            @else
                            <div class="col-md-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_COURSE') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                            @else
                            <div class="col-md-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.EVENT_WT_IS_NOT_ASSIGNED_YET') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script src="{{asset('public/js/custom.js')}}"></script>
<script type="text/javascript">
$(function () {

//        $(document).on("change", "#courseId", function () {
//            var courseId = $("#courseId").val();
//            if (courseId == '0') {
//                $('#showEventMksWt').html('');
//                return false;
//            }
//            var options = {
//                closeButton: true,
//                debug: false,
//                positionClass: "toast-bottom-right",
//                onclick: null
//            };
//
//            $.ajax({
//                url: "{{ URL::to('eventMksWt/getEventMksWt')}}",
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
//                    $('#showEventMksWt').html(res.html);
//                    $('.js-source-states').select2();
//                    App.unblockUI();
//                },
//                error: function (jqXhr, ajaxOptions, thrownError) {
//                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
//                    App.unblockUI();
//                }
//            });//ajax
//        });

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
                    url: "{{URL::to('eventMksWt/saveEventMksWt')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    beforeSend: function () {
                        $("#buttonSubmit").prop('disabled', true);
                        App.blockUI({boxed: true});
                    },
                    success: function (res) {
                        toastr.success(res, '@lang("label.WT_DISTRIBUTED_SUCCESSFULLY")', options);
                        App.unblockUI();
                        $("#buttonSubmit").prop('disabled', false);
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
                        $("#buttonSubmit").prop('disabled', false);
                        App.unblockUI();
                    }


                });
            }

        });

    });

    //delete
    $(document).on('click', '#buttonDelete', function (e) {
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
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'No, Cancel',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                $.ajax({
                    url: "{{URL::to('eventMksWt/deleteEventMksWt')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    beforeSend: function () {
                        $("#buttonDelete").prop('disabled', true);
                        App.blockUI({boxed: true});
                    },
                    success: function (res) {
                        toastr.success(res, '@lang("label.MKS_WT_DELETED_SUCCESSFULLY")', options);
                        $("#buttonDelete").prop('disabled', false);
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
                        $("#buttonDelete").prop('disabled', false);
                        App.unblockUI();
                    }
                });
            }

        });

    });
});

$(document).ready(function () {

    $(document).on('keyup', '.wt-distributed', function () {
        total();
        var key = $(this).attr('data-key');
        var totalAssignedWt = parseFloat($("#totalEventWt").val());
        var totalGivenWt = parseFloat($(".total-wt").text());
        if (totalGivenWt == '' || isNaN(totalGivenWt)) {
            totalGivenWt = 0;
        }
        if (totalGivenWt > totalAssignedWt) {
            swal({
                title: '@lang("label.TOTAL_EVENT_WT_EXCEEDED_FROM_ASSIGNED_TOTAL_EVENT_WT")',

                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $('#wt_' + key).val('');
                setTimeout(function () {
                    $('#wt_' + key).focus();
                }, 250);
                total();
                return false;
            });

        }
    });

    //start: highest limit can't exceed event mks limit
    $(document).on('keyup', '.mks-limit', function () {
        var key = $(this).attr('data-key');
        var eventMksLimit = parseFloat($(this).val());
        var highestMksLimit = parseFloat($("#highestLimit_" + key).val());
        if (highestMksLimit == '' || isNaN(highestMksLimit)) {
            highestMksLimit = 0;
        }

        if (eventMksLimit < highestMksLimit) {
            swal({
                title: '@lang("label.HIGHEST_LIMIT_CAN_NOT_EXCEED_EVENT_MKS_LIMIT")',

                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $('#mksLimit_' + key).val('');
                $('#highestLimit_' + key).val('');
                $('#lowestLimit_' + key).val('');
                setTimeout(function () {
                    $('#mksLimit_' + key).focus();
                }, 250);
                return false;
            });

        }
    });

    $(document).on('keyup', '.highest-mks', function () {

        var key = $(this).attr('data-key');
        var eventMksLimit = parseFloat($("#mksLimit_" + key).val());
        var highestMksLimit = parseFloat($(this).val());
        var lowestMksLimit = parseFloat($('#lowestLimit_' + key).val());
        if (highestMksLimit == '' || isNaN(highestMksLimit)) {
            highestMksLimit = 0;
        }
        if (eventMksLimit < highestMksLimit) {
            swal({
                title: "@lang('label.HIGHEST_LIMIT_CAN_NOT_EXCEED_EVENT_MKS_LIMIT') ",
                text: "",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('label.OK')",
                closeOnConfirm: true,
                closeOnCancel: true,
            }, function (isConfirm) {
                $('#highestLimit_' + key).val('');
                $('#lowestLimit_' + key).val('');
                setTimeout(function () {
                    $('#highestLimit_' + key).focus();
                }, 250);
                return false;
            });
        } else if (lowestMksLimit > highestMksLimit) {
            swal({
                title: "@lang('label.LOWEST_LIMIT_CAN_NOT_EXCEED_HIGHEST_LIMIT') ",
                text: "",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('label.OK')",
                closeOnConfirm: true,
                closeOnCancel: true,
            }, function (isConfirm) {
                $('#highestLimit_' + key).val('');
                $('#lowestLimit_' + key).val('');
                setTimeout(function () {
                    $('#highestLimit_' + key).focus();
                }, 250);
                return false;
            });
        }
    });
    //end: highest limit can't exceed event mks limit

    //start: lowest limit can't exceed highest limit
    $(document).on('keyup', '.lowest-limit', function () {
        var key = $(this).attr('data-key');
        var highestMksLimit = parseFloat($("#highestLimit_" + key).val());
        var lowestMksLimit = parseFloat($(this).val());
        if (lowestMksLimit == '' || isNaN(lowestMksLimit)) {
            lowestMksLimit = 0;
        }
        if (highestMksLimit < lowestMksLimit) {
            swal({
                title: "@lang('label.LOWEST_LIMIT_CAN_NOT_EXCEED_HIGHEST_LIMIT') ",
                text: "",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('label.OK')",
                closeOnConfirm: true,
                closeOnCancel: true,
            }, function (isConfirm) {
                $('#lowestLimit_' + key).val('');
                setTimeout(function () {
                    $('#lowestLimit_' + key).focus();
                }, 250);
                return false;
            });
        }
    });
    //end: lowest limit can't exceed highest limit

    function total() {
        var sum = 0;
        $('.wt-distributed').each(function () {
            var wt = $(this).val();
            if (wt == '' || isNaN(wt)) {
                wt = 0;
            }
            sum += parseFloat(wt);
        });
        $("#totalWt").val(sum);
        $('.total-wt').text(sum.toFixed(2));
    }
});

</script>
@stop