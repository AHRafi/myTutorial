@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.ARMS_SERVICE_TREND')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'armsServiceTrendReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                        <label class="control-label col-md-4" for="armsServiceId">@lang('label.ARMS_SERVICE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id="showArmsService">
                            {!! Form::select('arms_service_id[]', $armsServiceList, [], ['multiple'=>'multiple', 'class' => 'form-control mt-multiselect', 'id' => 'armsServiceId']) !!}
                            <span class="text-danger">{{ $errors->first('arms_service_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8" id="showEvent">
                            {!! Form::select('event_id[]', $eventList, [], ['multiple'=>'multiple', 'class' => 'form-control mt-multiselect', 'id' => 'eventId']) !!}
                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="form-group">
                        <button type="submit" class="btn btn-md green btn-outline filter-btn" id="eventCombineReportId" value="Show Filter Info" data-id="1">
                            <i class="fa fa-search"></i> @lang('label.GENERATE')
                        </button>
                    </div>
                </div>
            </div>
            @if(Request::get('generate') == 'true')

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
    //Start::Get Course
    $(document).on("change", "#trainingYearId", function () {
    var trainingYearId = $("#trainingYearId").val();
    if (trainingYearId == 0) {
    $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
    return false;
    }
    $.ajax({
    url: "{{ URL::to('armsServiceTrendReportCrnt/getCourse')}}",
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
    }); //ajax

    });
    //End::Get Course

    //Start::Get Course
    $(document).on("change", "#courseId", function () {
    var courseId = $("#courseId").val();
    $.ajax({
    url: "{{ URL::to('armsServiceTrendReportCrnt/getArmsServiceEvent')}}",
            type: "POST",
            dataType: "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
            course_id: courseId
            },
            beforeSend: function () {
            App.blockUI({boxed: true});
            },
            success: function (res) {
//                    console.log(res.html);
            $('#showArmsService').html(res.html);
            $('#showEvent').html(res.html2);
            $(".js-source-states").select2();
            App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
    }); //ajax

    });
    //End::Get Course


    //Start:: Multiselect decorations(arms/service)
    var armsServiceAllSelected = false;
    $('#armsServiceId').multiselect({
    numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            maxHeight: 250,
            nonSelectedText: "@lang('label.ARMS_SERVICE_OPT')",
            //        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
            armsServiceAllSelected = true;
            },
            onChange: function () {
            armsServiceAllSelected = false;
            }
    });
    //End:: Multiselect decorations

    //Start:: Multiselect decorations(event)
    var eventAllSelected = false;
    $('#eventId').multiselect({
    numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            maxHeight: 250,
            nonSelectedText: "@lang('label.EVENT_OPT')",
            //        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
            eventAllSelected = true;
            },
            onChange: function () {
            eventAllSelected = false;
            }
    });
    //End:: Multiselect decorations
    });
</script>


@stop