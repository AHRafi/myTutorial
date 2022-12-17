@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-power-off"></i>@lang('label.MKS_SUBMISSION_STATE')
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
                                            <th class="vcenter" colspan="{{Auth::user()->group_id == 4 ? 10 : 9}}">@lang('label.EVENT_ASSESSMENT') @lang('label.PROGRESS')</th>
                                        </tr>
                                        <tr>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.SL_NO')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_SUB_EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_SUB_SUB_EVENT')</th>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.ACTIVATION_STATUS')</th>
											
                                            @if(Auth::user()->group_id == 4)
                                            <th class="vcenter text-center" rowspan="2">@lang('label.MY_MKS_SUBMISSION_STATE')</th>
                                            @endif
                                            <th class="vcenter text-center" colspan="2">@lang('label.DS_MARKING')</th>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.CI_MODERATION_MARKING')</th>
                                            <!--<th class="vcenter text-center" rowspan="2">@lang('label.COMDT_MODERATION_MARKING')</th>-->
                                        </tr>
                                        <tr>
                                            <th class="vcenter text-center">@lang('label.FORWARDED')</th>
                                            <th class="vcenter text-center">@lang('label.NOT_FORWARDED')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($eventMksWtArr['mks_wt']))

                                        @foreach($eventMksWtArr['mks_wt'] as $termId => $evMksInfo)
            <!--                            <tr>
                                            <td class="vcenter text-center" colspan="9">{!! !empty($eventMksWtArr['event'][$termId]['name']) ? $eventMksWtArr['event'][$termId]['name'] : '' !!}</td>
                                        </tr>-->
                                        <?php $sl = 0; ?>
                                        @foreach($evMksInfo as $eventId => $evInfo)
                                        <tr>
                                            <td class="text-center" rowspan="{!! !empty($rowSpanArr['event'][$termId][$eventId]) ? $rowSpanArr['event'][$termId][$eventId] : 1 !!}">{!! ++$sl !!}</td>
                                            <td rowspan="{!! !empty($rowSpanArr['event'][$termId][$eventId]) ? $rowSpanArr['event'][$termId][$eventId] : 1 !!}">
                                                {!! !empty($eventMksWtArr['event'][$termId][$eventId]['name']) ? $eventMksWtArr['event'][$termId][$eventId]['name'] : '' !!}
                                            </td>

                                            @if(!empty($evInfo))
                                            <?php $i = 0; ?>
                                            @foreach($evInfo as $subEventId => $subEvInfo)
                                            <?php
                                            if ($i > 0) {
                                                echo '<tr>';
                                            }
                                            ?>
                                            <td class="vcenter"  rowspan="{!! !empty($rowSpanArr['sub_event'][$termId][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$termId][$eventId][$subEventId] : 1 !!}">
                                                {!! !empty($eventMksWtArr['event'][$termId][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$termId][$eventId][$subEventId]['name'] : '' !!}
                                            </td>

                                            @if(!empty($subEvInfo))
                                            <?php $j = 0; ?>
                                            @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                            <?php
                                            if ($j > 0) {
                                                echo '<tr>';
                                            }
                                            ?>
                                            <td class="vcenter"  rowspan="{!! !empty($rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] : 1 !!}">
                                                {!! !empty($eventMksWtArr['event'][$termId][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$termId][$eventId][$subEventId][$subSubEventId]['name'] : '' !!}
                                            </td>

                                            @if(!empty($subSubEvInfo))
                                            <?php $k = 0; ?>
                                            @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                                            <?php
                                            if ($k > 0) {
                                                echo '<tr>';
                                            }
                                            ?>
                                            <td class="vcenter">
                                                {!! !empty($eventMksWtArr['event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' !!}
                                            </td>
                                            <td class="vcenter text-center">
                                                <?php
                                                $color = !empty($assessmentActDeactArr[1][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? 'green-steel' : 'red-intense';
                                                $title = !empty($assessmentActDeactArr[1][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? __('label.ACTIVATED') : __('label.DEACTIVATED');
                                                ?>
                                                <i class="fa fa-power-off bold text-{{$color}} tooltips" title="{{$title}}"></i>
                                            </td>
                                            @if(Auth::user()->group_id == 4)
                                            <td class="vcenter text-center">
                                                <?php
                                                $state = __('label.N_A');
                                                $color = 'grey-mint';

                                                if (!empty($dsOwnMksSubmissionArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId])) {
                                                    $dsOwnMksSubmission = $dsOwnMksSubmissionArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId];
                                                    if (!empty($dsOwnMksSubmission['submitted'])) {
                                                        $state = __('label.SUBMITTED');
                                                        $color = 'purple';
                                                    } else if (!empty($dsOwnMksSubmission['drafted'])) {
                                                        $state = __('label.DRAFTED');
                                                        $color = 'blue-steel';
                                                    } else if (!empty($dsOwnMksSubmission['to_be_put'])) {
                                                        $state = __('label.NOT_SUBMITTED_YET');
                                                        $color = 'yellow';
                                                    } 
                                                }

                                                ?>
                                                <span class="label label-sm label-{{$color}}">{!! $state !!}</span>
                                            </td>
                                            @endif
                                            <td class="vcenter text-center">
                                                <?php
                                                $forwardedClass = '';
                                                $forwardedtype = '';
                                                $forwardedHref = '';
                                                if (!empty($subSubSubEvInfo['forwarded'])) {
                                                    $forwardedClass = 'ds-marking-status';
                                                    $forwardedtype = 'type=button';
                                                    $forwardedHref = '#dsMarkingSummaryModal';
                                                }
                                                ?>
                                                <a {{$forwardedtype}} class = "btn btn-xs bold {{$forwardedClass}} green-steel tooltips" term-id='{{$termId}}' 
                                                    event-id='{{$eventId}}' sub-event-id='{{$subEventId}}' sub-sub-event-id='{{$subSubEventId}}' 
                                                    sub-sub-sub-event-id='{{$subSubSubEventId}}'
                                                    data-id="1" title="@lang('label.FORWARDED')" href="{{$forwardedHref}}" data-toggle="modal">
                                                    {{ !empty($subSubSubEvInfo['forwarded']) ? $subSubSubEvInfo['forwarded'] : '0' }}
                                                </a>
                                            </td>
                                            <td class="vcenter text-center">
                                                <?php
                                                $notForwardedClass = '';
                                                $notForwardedtype = '';
                                                $notForwardedHref = '';
                                                if (!empty($subSubSubEvInfo['not_forwarded'])) {
                                                    $notForwardedClass = 'ds-marking-status';
                                                    $notForwardedtype = 'type=button';
                                                    $notForwardedHref = '#dsMarkingSummaryModal';
                                                }
                                                ?>
                                                <a  {{$notForwardedtype}} class = "btn btn-xs bold {{$notForwardedClass}} red-mint tooltips"  term-id='{{$termId}}' 
                                                    event-id='{{$eventId}}' sub-event-id='{{$subEventId}}' sub-sub-event-id='{{$subSubEventId}}' 
                                                    sub-sub-sub-event-id='{{$subSubSubEventId}}' 
                                                    data-id="2" title="@lang('label.NOT_FORWARDED')" href="{{$notForwardedHref}}" data-toggle="modal">
                                                    {{ !empty($subSubSubEvInfo['not_forwarded']) ? $subSubSubEvInfo['not_forwarded'] : '0' }}
                                                </a>
                                            </td> 

                                            <td class="text-center vcenter">
                                                @if(!empty($subSubSubEvInfo['ci_mod_lock']))
                                                <span class="label label-sm label-purple">@lang('label.FORWORDED')</span>
                                                @elseif(!empty($subSubSubEvInfo['ci_mod']))
                                                <span class="label label-sm label-blue-steel">@lang('label.DRAFTED')</span>
                                                @else
                                                <span class="label label-sm label-grey-mint">@lang('label.NO_MARKING_PUT_YET')</span>
                                                @endif
                                            </td>
            <!--                                <td class="text-center vcenter">
                                                @if(!empty($subSubSubEvInfo['comdt_mod_lock']))
                                                <span class="label label-sm label-purple">@lang('label.FORWORDED')</span>
                                                @elseif(!empty($subSubSubEvInfo['comdt_mod']))
                                                <span class="label label-sm label-blue-steel">@lang('label.DRAFTED')</span>
                                                @else
                                                <span class="label label-sm label-grey-mint">@lang('label.NO_MARKING_PUT_YET')</span>
                                                @endif
                                            </td>-->

                                            <?php
                                            if ($i < ($rowSpanArr['event'][$termId][$eventId] - 1)) {
                                                if ($j < ($rowSpanArr['sub_event'][$termId][$eventId][$subEventId] - 1)) {
                                                    if ($k < ($rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] - 1)) {
                                                        echo '</tr>';
                                                    }
                                                }
                                            }
                                            $k++;
                                            ?>
                                            @endforeach
                                            @endif

                                            <?php
                                            $j++;
                                            ?>
                                            @endforeach
                                            @endif

                                            <?php
                                            $i++;
                                            ?>
                                            @endforeach
                                            @endif
                                        </tr>
                                        @endforeach
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="9">@lang('label.NO_MARKING_GROUP_IS_ASSIGNED_YET')</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--            ds observation marking Status-->
                    <div class="row margin-top-30">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover table-head-fixer-color">
                                <thead>
                                    <tr>
                                        <th class="vcenter" colspan="2">@lang('label.DS_OBSN') @lang('label.PROGRESS')</th>
                                        <th class="vcenter text-center">
                                            <?php
                                            $color = !empty($assessmentActDeactArr[3][0][0][0][0]) ? 'green-steel' : 'red-intense';
                                            $title = !empty($assessmentActDeactArr[3][0][0][0][0]) ? __('label.ACTIVATED') : __('label.DEACTIVATED');
                                            ?>
                                            <i class="fa fa-power-off bold text-{{$color}} tooltips" title="{{$title}}"></i>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                        <th class="vcenter text-center">@lang('label.DS')</th>
                                        <th class="vcenter text-center">@lang('label.MARKING_STATUS')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($dsDataList))
                                    <?php $sl = 0; ?>
                                    @foreach($dsDataList as $dsId => $dsInfo)
                                    <?php
                                    $src = URL::to('/') . '/public/img/unknown.png';
                                    $alt = $dsInfo['ds_name'] ?? '';
                                    $personalNo = !empty($dsInfo['personal_no']) ? '(' . $dsInfo['personal_no'] . ')' : '';
                                    if (!empty($dsInfo['photo']) && File::exists('public/uploads/user/' . $dsInfo['photo'])) {
                                        $src = URL::to('/') . '/public/uploads/user/' . $dsInfo['photo'];
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center vcenter">{!! ++$sl !!}</td>
                                        <td class="text-center vcenter">
                                            <span class="tooltips" data-html="true" data-placement="bottom" title="
                                                  <div class='text-center'>
                                                  <img width='50' height='60' src='{!! $src !!}' alt='{!! $alt !!}'/><br/>
                                                  <strong>{!! $alt !!}<br/>
                                                  {!! $personalNo !!} </strong>
                                                  </div>
                                                  ">
                                                {{ $dsInfo['official_name'] ?? '' }}
                                            </span>
                                        </td>

                                        <td class="text-center vcenter width-160">
                                            @if(!empty($dsObservationMarkingArr) && array_key_exists($dsId, $dsObservationMarkingArr))
                                            @if(!empty($dsObservationMarkingLockArr) && array_key_exists($dsId, $dsObservationMarkingLockArr))
                                            <span class="label label-sm label-purple">@lang('label.FORWORDED')</span>
                                            @else
                                            <span class="label label-sm label-blue-steel">@lang('label.DRAFTED')</span>
                                            @endif
                                            @else
                                            <span class="label label-sm label-grey-mint">@lang('label.NO_MARKING_PUT_YET')</span>
                                            @endif

                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="3">@lang('label.NO_DATA_FOUND')</td>
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


<script type="text/javascript">
    $(function () {
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        $(".table-head-fixer-color").tableHeadFixer();

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
                url: "{{URL::to('mksSubmissionState/getDsMarkingSummary')}}",
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
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
@stop