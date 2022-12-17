<table class="table table-bordered table-hover table-head-fixer-color">
    <thead>
        <tr>
            <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
            <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
            <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
            <th class="vcenter" rowspan="2">@lang('label.CM')</th>
            <th class="text-center vcenter" rowspan="2">@lang('label.PHOTO')</th>
            <!--<th class="text-center vcenter" colspan="2">@lang('label.EVENT_TOTAL')</th>
            <th class="text-center vcenter" rowspan="2">@lang('label.PERCENT')</th>-->
            <th class="text-center vcenter" colspan="2">
                @lang('label.DS_OBSN')
            </th>

        </tr>
        <tr>
            <!--<th class="text-center vcenter">@lang('label.ASSIGNED_WT')</th>
            <th class="text-center vcenter">@lang('label.ACHIEVED_WT') </th>-->
            <th class="text-center vcenter">
                @lang('label.MKS')

            </th>
            <th class="text-center vcenter">@lang('label.WT')</th>
            <?php
            $assignedEventWt = !empty($eventMksWtArr['total_wt']) ? $eventMksWtArr['total_wt'] : 0;
            $assignedWt = !empty($assignedObsnInfo->obsn) ? $assignedObsnInfo->obsn : 0;
            $totalAssignedWt = $assignedEventWt + $assignedWt;
            ?>
        </tr>

    </thead>
    <tbody>
        <?php
        $sl = 0;
        $readonly = !empty($dsObsnLockInfo) ? 'readonly' : '';
        $givenObsn = !empty($dsObsnLockInfo) ? 'readonly' : 'given-mks';
        ?>
        @foreach($cmArr as $cmId => $cmInfo)
        <?php
        $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');
        $limitPercent = !empty($assignedObsnInfo->limit_percent) ? $assignedObsnInfo->limit_percent : 0;
        $eventPercent = !empty($cmEventMksArr[$cmId]['percent']) ? $cmEventMksArr[$cmId]['percent'] : 0;
        $totalEventWt = !empty($cmEventMksArr[$cmId]['achieved_wt']) ? $cmEventMksArr[$cmId]['achieved_wt'] : 0;

        $mksLimit = !empty($assignedObsnInfo->mks_limit) ? $assignedObsnInfo->mks_limit : 0;
        $eventObsableMks = ($eventPercent * $mksLimit) / 100;
        $eventObsableLimit = ($eventObsableMks * $limitPercent) / 100;
        $highRange = $eventObsableMks + $eventObsableLimit;
        $lowRange = $eventObsableMks - $eventObsableLimit;
        $title = __('label.RECOMMENDED_RANGE_OF_MKS', ['high' => Helper::numberFormatDigit3($highRange), 'low' => Helper::numberFormatDigit3($lowRange)]);
        ?>
        <tr>
            <td class="text-center vcenter witdh-50">
                <div class="width-inherit">{!! ++$sl !!}</div>
            </td>
            <td class="vcenter width-80">
                <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
            </td>
            <td class="vcenter width-80">
                <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
            </td>
            <td class="vcenter width-400">
                <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                {!! Form::hidden('cm_name['.$cmId.']',Common::getFurnishedCmName($cmName),['id' => 'cmId'])!!}
            </td>
            <td class="vcenter" width="50px">
                @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{{ Common::getFurnishedCmName($cmInfo['full_name']) }}">
                @else
                <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($cmInfo['full_name']) }}">
                @endif
            </td>

            <!--Start :: Event Total-->
            <!--<td class="text-center vcenter width-80">
                <span id="averageMks_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                    {!! !empty($cmEventMksArr[$cmId]['assigned_wt']) ? Helper::numberFormat2Digit($cmEventMksArr[$cmId]['assigned_wt']) : '' !!}
                </span>
            </td>
            <td class="text-center vcenter width-80">
                <span id="averageMks_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                    {!! !empty($cmEventMksArr[$cmId]['achieved_wt']) ? Helper::numberFormatDigit3($cmEventMksArr[$cmId]['achieved_wt']) : '' !!}
                </span>
            </td>
            <td class="text-center vcenter width-80">
                <span id="averageMks_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                    {!! !empty($cmEventMksArr[$cmId]['percent']) ? Helper::numberFormat2Digit($cmEventMksArr[$cmId]['percent']) : '' !!}
                </span>
            </td>-->
            <!--End :: Event Total-->

            <!--Start :: DS Obsn-->
            <td class="text-center vcenter width-80">
                {!! Form::text('mks_wt['.$cmId.'][obsn_mks]',  !empty($prevMksWtArr[$cmId]['obsn_mks']) ? Helper::numberFormatDigit3($prevMksWtArr[$cmId]['obsn_mks']) : null
                , ['id'=> 'dsObsn_'.$cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right '.$givenObsn.' tooltips', $readonly
                , 'data-key' => $cmId, 'data-high' => $highRange, 'data-low' => $lowRange, 'data-assigned-wt' => $assignedWt
                , 'data-mks-limit' => $mksLimit, 'autocomplete' => 'off', 'title' => $title]) !!}
            </td>
            {!! Form::hidden('mks_wt['.$cmId.'][high_range]', $highRange, ['id' => 'highRange_' . $cmId]) !!}
            {!! Form::hidden('mks_wt['.$cmId.'][low_range]', $lowRange, ['id' => 'lowRange_' . $cmId]) !!}
            {!! Form::hidden('mks_wt['.$cmId.'][assigned_wt]', $assignedWt, ['id' => 'assignedWt_' . $cmId]) !!}
            {!! Form::hidden('mks_wt['.$cmId.'][mks_limit]', $mksLimit, ['id' => 'mksLimit_' . $cmId]) !!}
            {!! Form::hidden('mks_wt['.$cmId.'][event_percent]',  !empty($eventPercent) ? $eventPercent : null, ['id'=> 'eventPercent_'.$cmId,'class'=>'event-percent', 'data-key' => $cmId,'data-mks-limit' => $mksLimit, 'data-assigned-wt' => $assignedWt]) !!}
            <td class="text-center vcenter width-80">
                <span id="obsnWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                    {!! !empty($prevMksWtArr[$cmId]['obsn_wt']) ? Helper::numberFormatDigit3($prevMksWtArr[$cmId]['obsn_wt']) : null !!}
                </span>
                {!! Form::hidden('mks_wt['.$cmId.'][obsn_wt]', !empty($prevMksWtArr[$cmId]['obsn_wt']) ? $prevMksWtArr[$cmId]['obsn_wt'] : null, ['id'=> 'obsnWt_Val_'.$cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right', 'data-key' => $cmId, 'autocomplete' => 'off', 'readonly']) !!}
            </td>
            <!--End :: DS Obsn-->


        </tr>
        @endforeach
    </tbody>
</table>

<script src="{{asset('public/js/custom.js')}}"></script>
<script type="text/javascript">

$(document).ready(function () {

    $('.tooltips').tooltip({
        container: 'body',
    });
    $(".table-head-fixer-color").tableHeadFixer({left: 5});


});

</script>