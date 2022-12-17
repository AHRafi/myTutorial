@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-power-off"></i>@lang('label.ASSESSMENT_ACTIVATE_DEACTIVATE')
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
                            @if(!empty($openTerms))
                            <div class="form-group">
                                <label class="control-label col-md-6" for="termId">@lang('label.TERM') :</label>
                                <div class="col-md-6">
                                    <div class="control-label pull-left"> <strong> {{$activeTermInfo->name}} </strong></div>
                                    {!! Form::hidden('term_id',$activeTermInfo->id,['id'=>'termId'])!!}
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>
                    @if(!empty($openTerms))
                    <!-- Event assessment summary -->
                    <div class="row margin-top-10">
                        <div class="col-md-12">
                            <div class=" table-responsive webkit-scrollbar">
                                <table class="table table-bordered table-head-fixer-color">
                                    <thead>
                                        <tr>
                                            <th class="vcenter" colspan="10">@lang('label.EVENT_ASSESSMENT') @lang('label.PROGRESS')</th>
                                        </tr>
                                        <tr>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.SL_NO')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_SUB_EVENT')</th>
                                            <th class="vcenter" rowspan="2">@lang('label.SUB_SUB_SUB_EVENT')</th>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.ACTIVATION_STATUS')</th>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.CM_ACTIVATION')</th>
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
                                                <div class="width-160">
                                                    {!! Form::checkbox('act_deact_stat['.$eventId.']['.$subEventId.']['.$subSubEventId.']['.$subSubSubEventId.']'
                                                    , 1, !empty($assessmentActDeactArr[1][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? 1:0
                                                    , ['id'=> 'actDeactStat_'.$eventId.'_'.$subEventId.'_'.$subSubEventId.'_'.$subSubSubEventId
                                                    , 'class' => 'make-switch act-deact-switch','data-on-text'=> __('label.ACTIVATE')
                                                    ,'data-off-text'=>__('label.DEACTIVATE'), 'criteria' => '1', 'course-id' => $courseList->id
                                                    , 'term-id' => $termId , 'event-id' => $eventId , 'sub-event-id' => $subEventId 
                                                    , 'sub-sub-event-id' => $subSubEventId , 'sub-sub-sub-event-id' => $subSubSubEventId]) !!} 
                                                </div>
                                            </td>
                                            <td class="vcenter text-center">
                                                <?php
                                                $cmActivationClass = "cm-activation-status";
                                                $cmActivationHref = "#cmActivationModal";
                                                ?>
                                                <a type="button" class = "btn bold  btn-warning tooltips {{$cmActivationClass}}" term-id='{{$termId}}' 
                                                   event-id='{{$eventId}}' sub-event-id='{{$subEventId}}' sub-sub-event-id='{{$subSubEventId}}' 
                                                   sub-sub-sub-event-id='{{$subSubSubEventId}}'
                                                   title="@lang('label.CLICK_HERE_TO_VIEW_CM_ACTIVATION_LIST')" href="{{$cmActivationHref}}" data-toggle="modal">
                                                    <i class="fa fa-user"></i>
                                                </a>
                                            </td>
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
                                            <td colspan="10">@lang('label.NO_MARKING_GROUP_IS_ASSIGNED_YET')</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- ds observation marking Status-->
                    <div class="row margin-top-30">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover table-head-fixer-color">
                                <thead>
                                    <tr>
                                        <th class="vcenter" colspan="2">@lang('label.DS_OBSN') @lang('label.PROGRESS')</th>
                                        <th class="vcenter text-center">
                                            <div class="width-160">
                                                {!! Form::checkbox('act_deact_stat[0][0][0][0]'
                                                , 1, !empty($assessmentActDeactArr[3][0][0][0][0]) ? 1:0
                                                , ['id'=> 'actDeactStat_3_0_0_0_0'
                                                , 'class' => 'make-switch act-deact-switch','data-on-text'=> __('label.ACTIVATE')
                                                ,'data-off-text'=>__('label.DEACTIVATE'), 'criteria' => '3', 'course-id' => $courseList->id
                                                , 'term-id' => $termId , 'event-id' => '0' , 'sub-event-id' => '0' 
                                                , 'sub-sub-event-id' => '0' , 'sub-sub-sub-event-id' => '0']) !!} 
                                            </div>
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

                    @if(!empty($maProcess))
                    <div class="row margin-top-30">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover table-head-fixer-color">
                                <thead>
                                    <tr>
                                        <th class="vcenter" colspan="{{in_array($maProcess, ['1','2']) ? 3 : 6}}">@lang('label.MUTUAL_ASSESSMENT')</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                        @if(in_array($maProcess, ['1','2']))
                                        <th class="vcenter">@lang('label.SYN_OR_SUB_SYN')</th>
                                        @elseif(in_array($maProcess, ['3']))
                                        <th class="vcenter">@lang('label.EVENT')</th>
                                        <th class="vcenter">@lang('label.SUB_EVENT')</th>
                                        <th class="vcenter">@lang('label.SUB_SUB_EVENT')</th>
                                        <th class="vcenter">@lang('label.SUB_SUB_SUB_EVENT')</th>
                                        @endif
                                        <th class="vcenter text-center">@lang('label.ACTIVATION_STATUS')</th>
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

                                        <td class="text-center vcenter width-160">
                                            {!! Form::checkbox('act_deact_stat['.$eventId.']['.$subEventId.']['.$subSubEventId.']['.$subSubSubEventId.']'
                                            , 1, !empty($assessmentActDeactArr[5][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? 1:0
                                            , ['id'=> 'actDeactStat_5_'.$eventId.'_'.$subEventId.'_'.$subSubEventId.'_'.$subSubSubEventId
                                            , 'class' => 'make-switch act-deact-switch','data-on-text'=> __('label.ACTIVATE')
                                            ,'data-off-text'=>__('label.DEACTIVATE'), 'criteria' => '5', 'course-id' => $courseList->id
                                            , 'term-id' => $termId , 'event-id' => $eventId , 'sub-event-id' => $subEventId 
                                            , 'sub-sub-event-id' => $subSubEventId , 'sub-sub-sub-event-id' => $subSubSubEventId]) !!} 
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="{{in_array($maProcess, ['1','2']) ? 3 : 6}}">@lang('label.NO_DATA_FOUND')</td>
                                    </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    @endif
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


<!-- START:: CM Activation State modal -->
<div class="modal fade" id="cmActivationModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showCmActivationView"></div>
    </div>
</div>
<!-- END:: CM Activation State modal -->


<script type="text/javascript">
    $(function () {
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        $(".act-deact-switch").bootstrapSwitch({
            offColor: 'danger'
        });

        $(".table-head-fixer-color").tableHeadFixer();

        // START:: CM Activation Modal
        $(document).on('click', '.cm-activation-status', function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var termId = $(this).attr('term-id');
            var eventId = $(this).attr('event-id');
            var subEventId = $(this).attr('sub-event-id');
            var subSubEventId = $(this).attr('sub-sub-event-id');
            var subSubSubEventId = $(this).attr('sub-sub-sub-event-id');
            $.ajax({
                url: "{{URL::to('assessmentActDeact/getCmActivationState')}}",
                type: "POST",
                datatype: 'json',
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
                    $('#showCmActivationView').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmActivationView').html(res.html);
                    $(".on-pause-switch").bootstrapSwitch({
                        offColor: 'danger'
                    });
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
        // END:: CM Activation Modal

        //**** START:: To Pause Swicth ***********// 
        $(document).on('switchChange.bootstrapSwitch', '.on-pause-switch', function () {

            var cmMarkingGroupId = $(this).attr('cm-marking-group-id');
            var status = this.checked == true ? '1' : '0';

            $.ajax({
                url: "{{URL::to('assessmentActDeact/setCmMarkingGroupStat')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    cm_marking_group_id: cmMarkingGroupId,
                    status: status,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    toastr.success(res.message, res.heading, options);
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
            });

        });
        //************ END: To Pause Switch *************/


        $(document).on('click', '.force-sumbit-cm', function (e) {
            e.preventDefault();
            var courseId = $(this).attr('course-id');
            var dsId = $(this).attr('ds-id');
            var termId = $(this).attr('term-id');
            var eventId = $(this).attr('event-id');
            var subEventId = $(this).attr('sub-event-id');
            var subSubEventId = $(this).attr('sub-sub-event-id');
            var subSubSubEventId = $(this).attr('sub-sub-sub-event-id');

            swal({
                title: '@lang("label.ARE_YOU_SURE_YOU_WANT_TO_FORCE_SUBMIT")',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Submit',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true

            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('assessmentActDeact/setCmForceSubmit')}}",
                        type: "POST",
                        datatype: 'json',
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
                            ds_id: dsId,
                        },
                        beforeSend: function () {
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success(res.message, res.heading, options);
                            App.unblockUI();
                            $.ajax({
                                url: "{{URL::to('assessmentActDeact/getDsMarkingSummary')}}",
                                type: "POST",
                                datatype: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    course_id: courseId,
                                    data_id: '2',
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
                }
            });
        });

        $(document).on('click', '.close-ds-summary', function (e) {
            window.location.reload();

        });



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
                url: "{{URL::to('assessmentActDeact/getDsMarkingSummary')}}",
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


        //deligate reports
        $(document).on('switchChange.bootstrapSwitch', '.act-deact-switch', function () {

            var courseId = $(this).attr('course-id');
            var status = this.checked == true ? '1' : '0';
            var criteria = $(this).attr('criteria');
            var termId = $(this).attr('term-id');
            var eventId = $(this).attr('event-id');
            var subEventId = $(this).attr('sub-event-id');
            var subSubEventId = $(this).attr('sub-sub-event-id');
            var subSubSubEventId = $(this).attr('sub-sub-sub-event-id');

            $.ajax({
                url: "{{URL::to('assessmentActDeact/setStat')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    criteria: criteria,
                    status: status,
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
                    toastr.success(res.message, res.heading, options);
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
            });

        });

    });
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
@stop