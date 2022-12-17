@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DS_REMARKS_REPORT')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'dsRemarksReport/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::select('training_year_id', $activeTrainingYearList, Request::get('training_year_id'), ['class' => 'form-control js-source-states', 'id' => 'trainingYearId']) !!}
                            <span class="text-danger">{{ $errors->first('training_year_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::select('course_id', $courseList, Request::get('course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> </span></label>
                        <div class="col-md-8">
                            {!! Form::select('term_id', $termList, Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                            <span class="text-danger">{{ $errors->first('term_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cmId">@lang('label.CM') :</label>
                        <div class="col-md-8">
                            {!! Form::select('cm_id', $cmList, Request::get('cm_id'), ['class' => 'form-control js-source-states', 'id' => 'cmId']) !!}
                            <span class="text-danger">{{ $errors->first('cm_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="maEventId">@lang('label.EVENT') :</label>
                        <div class="col-md-8">
                            {!! Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">
                    <div class="form-group">
                        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="eventCombineReportId" value="Show Filter Info" data-id="1">
                            <i class="fa fa-search"></i> @lang('label.GENERATE')
                        </button>
                    </div>
                </div>
            </div>
            @if(Request::get('generate') == 'true')
            @if(!$dsRemarksArr->isEmpty())
            <div class="row">
                <div class="col-md-12 text-right">
                    <a class="btn btn-md btn-primary vcenter tooltips" title="@lang('label.PRINT')" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                        <span class=""><i class="fa fa-print"></i> </span> 
                    </a>
                    <a class="btn btn-success vcenter tooltips" title="@lang('label.DOWNLOAD_PDF')" href="{!! URL::full().'&view=pdf' !!}">
                        <span class=""><i class="fa fa-file-pdf-o"></i></span>
                    </a>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} </strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} |</strong>
                            @if(!empty(Request::get('cm_id')))
                            <strong> |</strong>
                            {{__('label.CM')}} : <strong>{{ !empty($cmList[Request::get('cm_id')]) && Request::get('cm_id') != 0 ? $cmList[Request::get('cm_id')] : __('label.N_A') }}</strong>
                            @endif
                            @if(!empty(Request::get('event_id')))
                            <strong> |</strong>
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }}</strong>
                            @endif
                        </h5>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="max-height-500 table-responsive webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SL')</th>
                                    <th class="vcenter">@lang('label.DATE')</th>
                                    <th class="vcenter">@lang('label.TERM')</th>
                                    @if(empty(Request::get('cm_id')))
                                    <th class="vcenter">@lang('label.CM')</th>
                                    @endif
                                    @if(empty(Request::get('event_id')))
                                    <th class="vcenter">@lang('label.EVENT')</th>
                                    @endif
                                    <th class="vcenter text-center">@lang('label.RMKS')</th>
                                    <th class="vcenter text-center">@lang('label.REMARKED_BY')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$dsRemarksArr->isEmpty())
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($dsRemarksArr as $remarks)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">{{ !empty($remarks->date) ? Helper::formatDate($remarks->date) : '' }}</td>
                                    <td class="vcenter">{!! $remarks->term !!}</td>
                                    @if(empty(Request::get('cm_id')))
                                    <td class="vcenter">{!! $remarks->cm !!}</td>
                                    @endif
                                    @if(empty(Request::get('event_id')))
                                    <td class="vcenter">{{ $remarks->event }}</td>
                                    @endif
                                    <td class="vcenter">{{ $remarks->remarks ?? '' }}</td>
                                    <td class="vcenter text-center">{{ $remarks->official_name }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="6"><strong>@lang('label.NO_DS_REMARKS_FOUND')</strong></td>
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

            $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
            $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
            $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
            $('#cmId').html("<option value='0'>@lang('label.SELECT_CM_OPT')</option>");

            if (trainingYearId == 0) {
                return false;
            }
            $.ajax({
                url: "{{ URL::to('dsRemarksReport/getCourse')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
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

        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
            $('#cmId').html("<option value='0'>@lang('label.SELECT_CM_OPT')</option>");
            if (courseId == 0) {
                return false;
            }
            $.ajax({
                url: "{{ URL::to('dsRemarksReport/getTerm')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#termId').html(res.html);
                    $('#cmId').html(res.html2);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
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

        //Start::Get Event
        $(document).on("change", "#termId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            if (termId == 0) {
                return false;
            }
            $.ajax({
                url: "{{ URL::to('dsRemarksReport/getEvent')}}",
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
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#eventId').html(res.html);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
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


    });
</script>


@stop