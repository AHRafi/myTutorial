<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.TERM_STATUS_SUMMARY')
        </h3>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-4">
                <span class="bold">
                    @lang('label.COURSE'): {!! !empty($courseName->name) ? $courseName->name : '' !!}
                </span>        
            </div>
            <div class="col-md-4">
                <span class="bold">
                    @lang('label.TERM'): {!! !empty($termName->name) ? $termName->name : '' !!}
                </span>        
            </div>
        </div>
        <!--Start::Event assessment summary -->
        <div class="row margin-top-10">
            <div class="col-md-12">
                <div class=" table-responsive webkit-scrollbar max-height-500">
                    <table class="table table-bordered table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter" colspan="9">@lang('label.EVENT_ASSESSMENT') @lang('label.PROGRESS')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center" rowspan="2">@lang('label.SL_NO')</th>
                                <th class="vcenter" rowspan="2">@lang('label.EVENT')</th>
                                <th class="vcenter" rowspan="2">@lang('label.SUB_EVENT')</th>
                                <th class="vcenter" rowspan="2">@lang('label.SUB_SUB_EVENT')</th>
                                <th class="vcenter" rowspan="2">@lang('label.SUB_SUB_SUB_EVENT')</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.ACTIVATION_STATUS')</th>
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
                                    <a {{$forwardedtype}} class = "btn btn-xs bold {{$forwardedClass}} green-steel tooltips" 
                                        course-id='{{$request->course_id}}' term-id='{{$termId}}' 
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
                                    <a  {{$notForwardedtype}} class = "btn btn-xs bold {{$notForwardedClass}} red-mint tooltips"  
                                        course-id='{{$request->course_id}}' term-id='{{$termId}}' 
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
                            <th class="text-center vcenter">
                                <span class="tooltips" data-html="true" data-placement="bottom" title="
                                      <div class='text-center'>
                                      <img width='50' height='60' src='{!! $src !!}' alt='{!! $alt !!}'/><br/>
                                      <strong>{!! $alt !!}<br/>
                                      {!! $personalNo !!} </strong>
                                      </div>
                                      ">
                                    {{ $dsInfo['official_name'] ?? '' }}
                                </span>
                            </th>
                            <td class="text-center vcenter width-160">
                                @if(!empty($dsObservationMarkingArr[$request->term_id]) && array_key_exists($dsId, $dsObservationMarkingArr[$request->term_id]))
                                @if(!empty($dsObservationMarkingLockArr[$request->term_id]) && array_key_exists($dsId, $dsObservationMarkingLockArr[$request->term_id]))
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

    <div class="modal-footer">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

<script type="text/javascript">
    $(".table-head-fixer-color").tableHeadFixer();
</script>
<!-- END:: Contact Person Information-->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

