@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.MUTUAL_ASSESSMENT_DETAILED_REPORT')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'mutualAssessmentDetailedReport/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::select('term_id', $termList, Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                            <span class="text-danger">{{ $errors->first('term_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="synOrEventGroup">
                    <div class="col-md-4">
                        @if($maProcess == '1')
                        <div class="form-group">
                            <label class="control-label col-md-4" for="synId">@lang('label.SYN') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('syn_id', $synList, Request::get('syn_id'), ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
                                <span class="text-danger">{{ $errors->first('syn_id') }}</span>
                            </div>
                        </div>
                        @elseif($maProcess == '2')
                        <div class="form-group">
                            <label class="control-label col-md-4" for="subSynId">@lang('label.SUB_SYN') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('sub_syn_id', $subSynList, Request::get('sub_syn_id'), ['class' => 'form-control js-source-states', 'id' => 'subSynId']) !!}
                                <span class="text-danger">{{ $errors->first('sub_syn_id') }}</span>
                            </div>
                        </div>
                        @elseif($maProcess == '3')
                        <div class="form-group">
                            <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                                <span class="text-danger">{{ $errors->first('event_id') }}</span>
                            </div>
                        </div>
                        @else
                        <div class="form-group">
                            <label class="control-label col-md-4" for="synId">@lang('label.SYN') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('syn_id', $synList, Request::get('syn_id'), ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
                                <span class="text-danger">{{ $errors->first('syn_id') }}</span>
                            </div>
                        </div>
                        @endif
                        {!! Form::hidden('ma_process', !empty($maProcess) ? $maProcess : 0, ['id' => 'maProcess']) !!}
                    </div>
                </div>

                <div id="showSubEvent">
                    @if($maProcess == '3')
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger"> {{sizeof($subEventList) > 1 ? '*' : ''}}</span></label>
                            <div class="col-md-7">
                                {!! Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
                                <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
                            </div>
                        </div>
                    </div>
                    {!! Form::hidden('has_sub_event', sizeof($subEventList) > 1 ? 1 : 0) !!}
                    @endif
                </div>
                <div id="showSubSubEvent">
                    @if($maProcess == '3')
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :<span class="text-danger"> {{sizeof($subSubEventList) > 1 ? '*' : ''}}</span></label>
                            <div class="col-md-7">
                                {!! Form::select('sub_sub_event_id', $subSubEventList, Request::get('sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']) !!}
                                <span class="text-danger">{{ $errors->first('sub_sub_event_id') }}</span>
                            </div>
                        </div>
                    </div>
                    {!! Form::hidden('has_sub_sub_event', sizeof($subSubEventList) > 1 ? 1 : 0) !!}
                    @endif
                </div>
                @if($maProcess == '3')
            </div>
            <div class="row">
                @endif

                <div id="showSubSubSubEvent">
                    @if($maProcess == '3')
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="subSubSubEventId">@lang('label.SUB_SUB_SUB_EVENT') :<span class="text-danger"> {{sizeof($subSubSubEventList) > 1 ? '*' : ''}}</span></label>
                            <div class="col-md-7">
                                {!! Form::select('sub_sub_sub_event_id', $subSubSubEventList, Request::get('sub_sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId']) !!}
                                <span class="text-danger">{{ $errors->first('sub_sub_sub_event_id') }}</span>
                            </div>
                        </div>
                    </div>
                    {!! Form::hidden('has_sub_sub_sub_event', sizeof($subSubSubEventList) > 1 ? 1 : 0) !!}

                    @endif
                </div>

                <div id="showEventGroup">
                    @if($maProcess == '3')
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="eventGroupId">@lang('label.EVENT_GROUP') :<span class="text-danger"> *</span></label>
                            <div class="col-md-7">
                                {!! Form::select('event_group_id', $eventGroupList, Request::get('event_group_id'), ['class' => 'form-control js-source-states', 'id' => 'eventGroupId']) !!}
                                <span class="text-danger">{{ $errors->first('event_group_id') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                @if($maProcess == '3')
            </div>
            <div class="row">
                @endif
                <div class="col-md-{{$maProcess == '3' ? 12 : 4}} text-center">
                    <div class="form-group">
                        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="eventCombineReportId" value="Show Filter Info" data-id="1">
                            <i class="fa fa-search"></i> @lang('label.GENERATE')
                        </button>
                    </div>
                </div>
            </div>
            @if(Request::get('generate') == 'true')
            @if(!empty($cmArr))
            <div class="row">
                <div class="col-md-12 text-right">
                    <!--                    <a class="btn btn-md btn-primary vcenter tooltips" title="@lang('label.PRINT')" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                                            <span class=""><i class="fa fa-print"></i> </span>
                                        </a>
                                        <a class="btn btn-success vcenter tooltips" title="@lang('label.DOWNLOAD_PDF')" href="{!! URL::full().'&view=pdf' !!}">
                                            <span class=""><i class="fa fa-file-pdf-o"></i></span>
                                        </a>-->
                    <a class="btn btn-warning vcenter tooltips" title="@lang('label.DOWNLOAD_EXCEL')" href="{!! URL::full().'&view=excel' !!}">
                        <span class=""><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                    <label class="control-label" for="sortBy">@lang('label.SORT_BY') :</label>&nbsp;

                    <label class="control-label" for="sortBy">
                        {!! Form::select('sort', $sortByList, Request::get('sort'),['class' => 'form-control','id'=>'sortBy']) !!}
                    </label>

                    <button class="btn green-jungle filter-btn"  id="sortByHref" type="submit">
                        <i class="fa fa-arrow-right"></i>  @lang('label.GO')
                    </button>


                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} |</strong>
                            @if($maProcess == '1')
                            {{__('label.SYNDICATE')}} : <strong>{{ !empty($synList[Request::get('syn_id')]) && Request::get('syn_id') != 0 ? $synList[Request::get('syn_id')] : __('label.N_A') }} |</strong>
                            @elseif($maProcess == '2')
                            {{__('label.SUB_SYNDICATE')}} : <strong>{{ !empty($subSynList[Request::get('sub_syn_id')]) && Request::get('sub_syn_id') != 0 ? $subSynList[Request::get('sub_syn_id')] : __('label.N_A') }} |</strong>
                            @elseif($maProcess == '3')
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }} |</strong>
                            @if(!empty(Request::get('sub_event_id')))
                            {{__('label.SUB_EVENT')}} : <strong>{{ !empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0 ? $subEventList[Request::get('sub_event_id')] : __('label.N_A') }} |</strong>
                            @endif
                            @if(!empty(Request::get('sub_sub_event_id')))
                            {{__('label.SUB_SUB_EVENT')}} : <strong>{{ !empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0 ? $subSubEventList[Request::get('sub_sub_event_id')] : __('label.N_A') }} |</strong>
                            @endif
                            @if(!empty(Request::get('sub_sub_sub_event_id')))
                            {{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ !empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0 ? $subSubSubEventList[Request::get('sub_sub_sub_event_id')] : __('label.N_A') }} |</strong>
                            @endif
                            {{__('label.EVENT_GROUP')}} : <strong>{{ !empty($eventGroupList[Request::get('event_group_id')]) && Request::get('event_group_id') != 0 ? $eventGroupList[Request::get('event_group_id')] : __('label.N_A') }} |</strong>
                            @endif
                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($cmArr) ? sizeof($cmArr) : 0 }}</strong>

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
                                    <th class="vcenter text-center" rowspan="3">@lang('label.SL')</th>
                                    <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                                    <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                                    <th class="vcenter" rowspan="3">@lang('label.PHOTO')</th>
                                    <th class="vcenter text-center" colspan="{{!empty($cmArr) && !empty($factorList) ? sizeof($cmArr)*sizeof($factorList) : 1}}">@lang('label.CM_MARKING')</th>
                                    <th class="vcenter text-center" rowspan="2" colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">@lang('label.AVG')</th>
                                    <th class="vcenter text-center" rowspan="2"colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">@lang('label.POSITION')</th>
                                </tr>
                                <tr>
                                    @if(!empty($markingCmArr))
                                    @foreach($markingCmArr as $cmId => $cm)
                                    <?php
                                    $cmName = (!empty($cm['rank_name']) ? $cm['rank_name'] : '') . ' ' . (!empty($cm['official_name']) ? $cm['official_name'] : '') . ' (' . (!empty($cm['personal_no']) ? $cm['personal_no'] : '') . ')';
                                    ?>
                                    <th class="vcenter text-center"colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">{!! $cmName ?? '' !!}</th>
                                    @endforeach
                                    @endif
                                </tr>
                                <tr>
                                    @if(!empty($markingCmArr))
                                    @foreach($markingCmArr as $cmId => $cm)
                                    @if(!empty($factorList))
                                    @foreach($factorList as $factorId => $factor)
                                    <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                                    @endforeach
                                    @endif
                                    @endforeach
                                    @endif
                                    @if(!empty($factorList))
                                    @foreach($factorList as $factorId => $factor)
                                    <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                                    @endforeach
                                    @endif
                                    @if(!empty($factorList))
                                    @foreach($factorList as $factorId => $factor)
                                    <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                                    @endforeach
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($cmArr) && !empty($markingCmArr))
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($cmArr as $cmId => $cm)
                                <?php
                                $cmId = !empty($cm['id']) ? $cm['id'] : 0;
                                $cmName = (!empty($cm['rank_name']) ? $cm['rank_name'] : '') . ' ' . (!empty($cm['official_name']) ? $cm['official_name'] : '') . ' (' . (!empty($cm['personal_no']) ? $cm['personal_no'] : '') . ')';
                                ?>
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">
                                        <div class="width-inherit">{!! $cm['personal_no'] ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter">
                                        <div class="width-inherit">{!! $cm['rank_name'] ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-180">
                                        <div class="width-inherit text-left">{!! $cm['full_name'] ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter" width="50px">
                                        @if(!empty($cm['photo']) && File::exists('public/uploads/cm/' . $cm['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cm['photo']}}" alt="{!! Common::getFurnishedCmName($cm['full_name']) !!}">
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cm['full_name']) !!}">
                                        @endif
                                    </td>
                                    @foreach($markingCmArr as $markingCmId => $markingCm)
                                    @if(!empty($factorList))
                                    @foreach($factorList as $factorId => $factor)
                                    <?php
                                    $alignment = !empty($markingPositionArr[$markingCmId][$cmId][$factorId]['pos']) ? 'right' : 'center';
                                    ?>
                                    <td class="vcenter text-{{$alignment}}">{!! $markingPositionArr[$markingCmId][$cmId][$factorId]['pos'] ?? '--' !!}</td>
                                    @endforeach
                                    @endif
                                    @endforeach
                                    @if(!empty($factorList))
                                    @foreach($factorList as $factorId => $factor)
                                    <td class="vcenter text-{{!empty($cm['avg_'.$factorId]) ? 'right' : 'center'}}">
                                        {!! !empty($cm['avg_'.$factorId]) ? Helper::numberFormat2Digit($cm['avg_'.$factorId]) : '--' !!}
                                    </td>
                                    @endforeach
                                    @endif
                                    @if(!empty($factorList))
                                    @foreach($factorList as $factorId => $factor)
                                    <td class="vcenter text-center">{!! $cm['position_'.$factorId] ?? '' !!}</td>
                                    @endforeach
                                    @endif
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5">
                                        <strong>
                                            @if(!empty(Request::get('has_grouping')))
                                            @lang('label.NO_CM_IS_ASSIGNED_TO_THIS_EVENT_GROUP')
                                            @else
                                            @lang('label.NO_CM_IS_ASSIGNED_TO_THIS_SYNDICATE')
                                            @endif
                                        </strong>
                                    </td>
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
        $(".table-head-fixer-color").tableHeadFixer({left: 5});

        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();

            $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
            $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");

            $('#synOrEventGroup').html('');
            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            if (trainingYearId == 0) {
                return false;
            }
            $.ajax({
                url: "{{ URL::to('mutualAssessmentDetailedReport/getCourse')}}",
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
            $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
            $('#synOrEventGroup').html('');
            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            if (courseId == 0) {
                return false;
            }
            $.ajax({
                url: "{{ URL::to('mutualAssessmentDetailedReport/getTerm')}}",
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

        $(document).on("change", "#termId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();

            $('#synOrEventGroup').html('');
            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            $.ajax({
                url: "{{ URL::to('mutualAssessmentDetailedReport/getSynOrGp')}}",
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
                    $('#synOrEventGroup').html(res.html);
                    if (res.maProcess == '3') {
                        $('#showSubEvent').html(res.html1);
                        $('#showSubSubEvent').html(res.html2);
                        $('#showSubSubSubEvent').html(res.html3);
                        $('#showEventGroup').html(res.html4);
                    }
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

        $(document).on("change", "#eventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();

            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            $.ajax({
                url: "{{ URL::to('mutualAssessmentDetailedReport/getSubEvent')}}",
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
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubEvent').html(res.html);
                    $('#showSubSubEvent').html(res.html2);
                    $('#showSubSubSubEvent').html(res.html3);
                    $('#showEventGroup').html(res.html1);
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

        $(document).on("change", "#subEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();

            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            $.ajax({
                url: "{{ URL::to('mutualAssessmentDetailedReport/getSubSubEvent')}}",
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
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubEvent').html(res.html);
                    $('#showSubSubSubEvent').html(res.html2);
                    $('#showEventGroup').html(res.html1);
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

        $(document).on("change", "#subSubEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();

            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            $.ajax({
                url: "{{ URL::to('mutualAssessmentDetailedReport/getSubSubSubEvent')}}",
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
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubSubEvent').html(res.html);
                    $('#showEventGroup').html(res.html1);
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

        $(document).on("change", "#subSubSubEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();

            $('#showEventGroup').html('');

            $.ajax({
                url: "{{ URL::to('mutualAssessmentDetailedReport/getEventGroup')}}",
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
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showEventGroup').html(res.html);
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

    });
</script>


@stop
