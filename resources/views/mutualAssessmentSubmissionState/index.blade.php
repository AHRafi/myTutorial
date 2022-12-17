@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-users"></i>@lang('label.MTL_ASSESSMENT_SUBMISSION_STATE')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'assessmentActDeactForm')) !!}
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
                                    {!! Form::hidden('ma_process',$maProcess,['id'=>'maProcess'])!!}
                                </div>
                            </div>
                        </div>

                    </div><!--Start::Event assessment summary -->
                    <div class="row margin-top-10">
                        <div class="col-md-12">
                            <div class=" table-responsive webkit-scrollbar">
                                <table class="table table-bordered table-head-fixer-color">
                                    <thead>
                                        <tr>
                                            <th class="vcenter" colspan="{{in_array($maProcess, ['1','2']) ? 5 : 8}}">@lang('label.MTL_ASSESSMENT_PROGRESS')</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
                                            @if(in_array($maProcess, ['1' ,'2']))
                                            <th class="vcenter" rowspan="2">@lang('label.SYN_OR_SUB_SYN')</th>
                                            @elseif(in_array($maProcess, ['3']))
                                            <th class="vcenter" rowspan="2">@lang('label.EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_SUB_EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_SUB_SUB_EVENT')</th>
                                            @endif
                                            <th class="vcenter text-center" rowspan="2">@lang('label.ACTIVATION_STATUS')</th>
                                            <th class="vcenter text-center" colspan="2">@lang('label.CM_MARKING')</th>
                                        </tr>
                                        <tr>
                                            <th class="vcenter text-center">@lang('label.FORWARDED')</th>
                                            <th class="vcenter text-center">@lang('label.NOT_FORWARDED')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($maEventMksWtArr['mks_wt']))
                                        <?php $sl = 0; ?>
                                        @foreach($maEventMksWtArr['mks_wt'] as $eventId => $eventInfo)
                                        @foreach($eventInfo as $subEventId => $subEventInfo)
                                        @foreach($subEventInfo as $subSubEventId => $subSubEventInfo)
                                        @foreach($subSubEventInfo as $subSubSubEventId => $info)
                                        <tr>
                                            <td class="text-center vcenter width-80">{!! ++$sl !!}</td>
                                            @if(in_array($maProcess, ['1','2']))
                                            <td class="vcenter">{{ $info['name'] }}</td>
                                            @elseif(in_array($maProcess, ['3']))
                                            <td class="vcenter">{{ $maEventMksWtArr['event'][$eventId]['name'] ?? '' }}</td>
                                            <td class="vcenter">{{ $maEventMksWtArr['event'][$eventId][$subEventId]['name'] ?? '' }}</td>
                                            <td class="vcenter">{{ $maEventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] ?? '' }}</td>
                                            <td class="vcenter">{{ $maEventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] ?? '' }}</td>
                                            @endif

                                            <td class="vcenter text-center">
                                                <?php
                                                $color = !empty($assessmentActDeactArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? 'green-steel' : 'red-intense';
                                                $title = !empty($assessmentActDeactArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? __('label.ACTIVATED') : __('label.DEACTIVATED');
                                                ?>
                                                <i class="fa fa-power-off bold text-{{$color}} tooltips" title="{{$title}}"></i>
                                            </td>
                                            <td class="vcenter text-center">
                                                <?php
                                                $forwardedClass = '';
                                                $forwardedtype = '';
                                                $forwardedHref = '';
                                                if (!empty($info['forwarded'])) {
                                                    $forwardedClass = 'cm-marking-status';
                                                    $forwardedtype = 'type=button';
                                                    $forwardedHref = '#cmMarkingSummaryModal';
                                                }
                                                ?>
                                                <a {{$forwardedtype}} class = "btn btn-xs bold {{$forwardedClass}} green-steel tooltips" term-id='{{$activeTermInfo->id}}' 
                                                    event-id='{{$eventId}}' sub-event-id='{{$subEventId}}' sub-sub-event-id='{{$subSubEventId}}' 
                                                    sub-sub-sub-event-id='{{$subSubSubEventId}}'
                                                    data-id="1" title="@lang('label.FORWARDED')" href="{{$forwardedHref}}" data-toggle="modal">
                                                    {{ !empty($info['forwarded']) ? $info['forwarded'] : '0' }}
                                                </a>
                                            </td>
                                            <td class="vcenter text-center">
                                                <?php
                                                $notForwardedClass = '';
                                                $notForwardedtype = '';
                                                $notForwardedHref = '';
                                                if (!empty($info['not_forwarded'])) {
                                                    $notForwardedClass = 'cm-marking-status';
                                                    $notForwardedtype = 'type=button';
                                                    $notForwardedHref = '#cmMarkingSummaryModal';
                                                }
                                                ?>
                                                <a  {{$notForwardedtype}} class = "btn btn-xs bold {{$notForwardedClass}} red-mint tooltips"  term-id='{{$activeTermInfo->id}}' 
                                                    event-id='{{$eventId}}' sub-event-id='{{$subEventId}}' sub-sub-event-id='{{$subSubEventId}}' 
                                                    sub-sub-sub-event-id='{{$subSubSubEventId}}' 
                                                    data-id="2" title="@lang('label.NOT_FORWARDED')" href="{{$notForwardedHref}}" data-toggle="modal">
                                                    {{ !empty($info['not_forwarded']) ? $info['not_forwarded'] : '0' }}
                                                </a>
                                            </td>
                                            
                                        </tr>
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="{{in_array($maProcess, ['1','2']) ? 5 : 8}}">@lang('label.NO_DATA_FOUND')</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
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
<div class="modal fade test" id="cmMarkingSummaryModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showCmMarkingSummary"></div>
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
        $(".table-head-fixer-color").tableHeadFixer();

        //CM Marking Summary Modal
        $(document).on('click', '.cm-marking-status', function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var maProcess = $("#maProcess").val();
            var dataId = $(this).attr('data-id');
            var termId = $(this).attr('term-id');
            var eventId = $(this).attr('event-id');
            var subEventId = $(this).attr('sub-event-id');
            var subSubEventId = $(this).attr('sub-sub-event-id');
            var subSubSubEventId = $(this).attr('sub-sub-sub-event-id');
            $.ajax({
                url: "{{URL::to('mutualAssessmentSubmissionState/getCmMarkingSummary')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    ma_process: maProcess,
                    course_id: courseId,
                    data_id: dataId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    $('#showCmMarkingSummary').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmMarkingSummary').html(res.html);
                    $(".table-head-fixer-color").tableHeadFixer();
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
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
@stop