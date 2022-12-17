@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.APPT_TO_CM')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'apptToCmReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                        <div class="col-md-7">
                                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearList->name}} </strong></div>
                                            {!! Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
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
                                        <label class="control-label col-md-5" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
                                        <div class="col-md-7">
                                            {!! Form::select('term_id', $termList, Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                                            <span class="text-danger">{{ $errors->first('term_id') }}</span>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label col-md-5" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                                        <div class="col-md-7">
                                            {!! Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>    
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label col-md-5" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger required-sub-event"> {{ !empty($hasSubEvent) ? '*' : ''}}</span></label>
                                        <div class="col-md-7">
                                            {!! Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
                                            {!! Form::hidden('has_sub_event',$hasSubEvent,['id' => 'hasSubEvent']) !!}
                                            <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label col-md-5" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :<span class="text-danger required-sub-sub-event"> {{ !empty($hasSubSubEvent) ? '*' : ''}}</span></label>
                                        <div class="col-md-7">
                                            {!! Form::select('sub_sub_event_id', $subSubEventList, Request::get('sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']) !!}
                                            {!! Form::hidden('has_sub_sub_event',$hasSubSubEvent,['id' => 'hasSubSubEvent']) !!}
                                            <span class="text-danger">{{ $errors->first('sub_sub_event_id') }}</span>
                                        </div>
                                    </div>
                                </div> 

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label col-md-5" for="subSubSubEventId">@lang('label.SUB_SUB_SUB_EVENT') :<span class="text-danger required-sub-sub-sub-event"> {{ !empty($hasSubSubSubEvent) ? '*' : ''}}</span></label>
                                        <div class="col-md-7">
                                            {!! Form::select('sub_sub_sub_event_id', $subSubSubEventList, Request::get('sub_sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId']) !!}
                                            {!! Form::hidden('has_sub_sub_sub_event',$hasSubSubSubEvent,['id' => 'hasSubSubSubEvent']) !!}
                                            <span class="text-danger">{{ $errors->first('sub_sub_sub_event_id') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="eventCombineReportId" value="Show Filter Info" data-id="1">
                                            <i class="fa fa-search"></i> @lang('label.GENERATE')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(Request::get('generate') == 'true')
            @if(!$targetArr->isEmpty())
            <div class="row">
                <div class="col-md-12 text-right">
                    <a class="btn btn-md btn-primary vcenter tooltips" title="@lang('label.PRINT')" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                        <span class=""><i class="fa fa-print"></i> </span> 
                    </a>
                    <a class="btn btn-success vcenter tooltips" title="@lang('label.DOWNLOAD_PDF')" href="{!! URL::full().'&view=pdf' !!}">
                        <span class=""><i class="fa fa-file-pdf-o"></i></span>
                    </a>
                    <a class="btn btn-warning vcenter tooltips" title="@lang('label.DOWNLOAD_EXCEL')" href="{!! URL::full().'&view=excel' !!}">
                        <span class=""><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12 margin-top-10">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong>{{$courseList->name}} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} |</strong>
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }}</strong>
                            @if(!empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0)
                            <strong>| </strong>{{__('label.SUB_EVENT')}} : <strong>{{ $subEventList[Request::get('sub_event_id')] }}</strong>
                            @endif
                            @if(!empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0)
                            <strong>| </strong>{{__('label.SUB_SUB_EVENT')}} : <strong>{{ $subSubEventList[Request::get('sub_sub_event_id')] }}</strong>
                            @endif
                            @if(!empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0)
                            <strong>| </strong>{{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ $subSubSubEventList[Request::get('sub_sub_sub_event_id')] }}</strong>
                            @endif
                            @if(!empty($targetArr))
                            <strong>| </strong>{{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }} </strong>
                            @endif
                        </h5>
                    </div>
                </div>
                <div class="col-md-12  margin-top-10">
                    <div class=" table-responsive max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter">@lang('label.RANK')</th>
                                    <th class="vcenter">@lang('label.CM')</th>
                                    <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                    <th class="vcenter">@lang('label.SYN')</th>
                                    <th class="vcenter">@lang('label.APPT')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if(!$targetArr->isEmpty())
                                <?php $sl = 0; ?>
                                @foreach($targetArr as $cmInfo)
                                <tr>
                                    <td class="vcenter tex-center width-80">
                                        <div class="width-inherit text-center">{!! ++$sl !!}</div>
                                    </td>
                                    <td class="vcenter width-80">
                                        <div class="width-inherit">{!! $cmInfo->personal_no ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-50">
                                        <div class="width-inherit">{!! $cmInfo->rank_code ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-200">
                                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo->full_name) !!}</div>
                                    </td>
                                    <td class="vcenter text-center" width="30px">
                                        @if(!empty($cmInfo->photo) && File::exists('public/uploads/cm/' . $cmInfo->photo))
                                        <img width="30" height="33" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo->photo}}" alt="{!! Common::getFurnishedCmName($cmInfo->full_name) !!}">
                                        @else
                                        <img width="30" height="33" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo->full_name) !!}">
                                        @endif
                                    </td>
                                    <td class="vcenter width-80">
                                        <div class="width-inherit">{!! $cmInfo->syn ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-80">
                                        <div class="width-inherit">{!! $cmInfo->appt ?? '' !!}</div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="7">@lang('label.NO_DATA_FOUND')</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
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
            onclick: null
        };
        //table header fix
        $(".table-head-fixer-color").tableHeadFixer('');

        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            if (trainingYearId == 0) {
                $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
                $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-event').text('');
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('apptToCmReportCrnt/getCourse')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                    $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                    $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                    $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-event').text('');
                    $('.required-sub-sub-event').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#courseId').html(res.html);
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            });//ajax

        });
        //End::Get Course

        //Start::Get Term
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == 0) {
                $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-event').text('');
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('apptToCmReportCrnt/getTerm')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                    $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                    $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-event').text('');
                    $('.required-sub-sub-event').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#termId').html(res.html);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    $("#previewMarkingSheet").prop("disabled", false);
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax
        });
        //End::Get Term

        //Start::Get Event
        $(document).on("change", "#termId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            if (termId == 0) {
                $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-event').text('');
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('apptToCmReportCrnt/getEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                },
                beforeSend: function () {
                    $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                    $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-event').text('');
                    $('.required-sub-sub-event').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#eventId').html(res.html);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    $("#previewMarkingSheet").prop("disabled", false);
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax
        });
        //End::Get Event

        //Start::Get Sub Event
        $(document).on("change", "#eventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            $('#hasSubEvent').val(0);
            $('#hasSubSubEvent').val(0);
            $('#hasSubSubSubEvent').val(0);
            if (eventId == 0) {
                $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-event').text('');
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('apptToCmReportCrnt/getSubEventReportCrnt')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                    event_id: eventId,
                },
                beforeSend: function () {
                    $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-event').text('');
                    $('.required-sub-sub-event').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    if (res.html != '') {
                        $('#subEventId').html(res.html);
                        $('.required-sub-event').text('*');
                        $('#hasSubEvent').val(1);
                    } else {
                        $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                        $('.required-sub-event').text('');
                        $('#hasSubEvent').val(0);
                    }
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    $("#previewMarkingSheet").prop("disabled", false);
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax
        });
        //End::Get Sub Event

        //Start::Get Sub Sub Event
        $(document).on("change", "#subEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            $('#hasSubSubEvent').val(0);
            $('#hasSubSubSubEvent').val(0);
            if (subEventId == 0) {
                $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('apptToCmReportCrnt/getSubSubEventReportCrnt')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                },
                beforeSend: function () {
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-sub-event').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    if (res.html != '') {
                        $('#subSubEventId').html(res.html);
                        $('.required-sub-sub-event').text('*');
                        $('#hasSubSubEvent').val(1);
                    } else {
                        $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                        $('.required-sub-sub-event').text('');
                        $('#hasSubSubEvent').val(0);
                    }
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    $("#previewMarkingSheet").prop("disabled", false);
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax
        });
        //End::Get Sub Sub Event

        //Start::Get Sub Sub Sub Event
        $(document).on("change", "#subSubEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            $('#hasSubSubSubEvent').val(0);
            if (subSubEventId == 0) {
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-show').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('apptToCmReportCrnt/getSubSubSubEventReportCrnt')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                },
                beforeSend: function () {
                    $('.required-show').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#subSubSubEventId').html(res.html);
                    if (res.html != '') {
                        $('#subSubSubEventId').html(res.html);
                        $('.required-sub-sub-sub-event').text('*');
                        $('#hasSubSubSubEvent').val(1);
                    } else {
                        $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                        $('.required-sub-sub-sub-event').text('');
                        $('#hasSubSubSubEvent').val(0);
                    }
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    $("#previewMarkingSheet").prop("disabled", false);
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax
        });
        //End::Get Sub Sub Sub Event


    });
</script>


@stop