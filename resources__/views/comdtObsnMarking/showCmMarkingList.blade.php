
<table class="table table-bordered table-hover table-head-fixer-color">
    <thead>
        <tr>
            <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
            <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
            <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
            <th class="vcenter" rowspan="2">@lang('label.CM')</th>
            <th class="vcenter" rowspan="2">@lang('label.PHOTO')</th>
            @if(!empty($termDataArr))
            @foreach($termDataArr as $termId => $termName)
            <th class="text-center vcenter" colspan="5">
                {!! !empty($termName) ? $termName : '' !!} (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['total_wt'][$termId]) ? Helper::numberFormatDigit2($eventMksWtArr['total_wt'][$termId]) : '0.00'}})
            </th>
            @endforeach
            @endif
            <th class="vcenter text-center" colspan="5">
                @lang('label.TERM_AGGREGATED_RESULT') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['agg_total_wt_limit']) ? Helper::numberFormatDigit2($eventMksWtArr['agg_total_wt_limit']) : '0.00'}})
            </th>
            <th class="vcenter text-center" colspan="2">@lang('label.CI_OBSN')</th>
            <th class="vcenter text-center" colspan="5">
                @lang('label.AFTER_CI_OBSN') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['after_ci_obsn']) ? Helper::numberFormatDigit2($eventMksWtArr['after_ci_obsn']) : '0.00'}})
            </th>
            <th class="vcenter text-center" colspan="2">@lang('label.COMDT_OBSN')</th>
            <th class="vcenter text-center" colspan="5">
                @lang('label.AFTER_COMDT_OBSN') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['after_comdt_obsn']) ? Helper::numberFormatDigit2($eventMksWtArr['after_comdt_obsn']) : '0.00'}})
            </th>
        </tr>
        <tr>
            @if(!empty($termDataArr))
            @foreach($termDataArr as $termId => $termName)
            <?php
            $termAggWtTotal = !empty($termAggWtTotal) ? $termAggWtTotal : 0;
            $termAggWtTotal += $eventMksWtArr['total_wt'][$termId];
            $finalWtLimit = $termAggWtTotal + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : '0.00') + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : '0.00');
            ?>
            <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
            <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
            <th class="vcenter text-center">@lang('label.PERCENT')</th>
            <th class="vcenter text-center">@lang('label.GRADE')</th>
            <th class="vcenter text-center">@lang('label.POSITION')</th>
            @endforeach
            @endif
            <!--term aggregated total-->
            <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
            <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
            <th class="vcenter text-center">@lang('label.PERCENT')</th>
            <th class="vcenter text-center">@lang('label.GRADE')</th>
            <th class="vcenter text-center">@lang('label.POSITION')</th>

            <th class="vcenter text-center">@lang('label.MKS')&nbsp;(100.00)</th>
            <th class="vcenter text-center">@lang('label.WT')&nbsp;({!! !empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : '0.00' !!})</th>


            <!--ci obsn-->
            <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
            <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
            <th class="vcenter text-center">@lang('label.PERCENT')</th>
            <th class="vcenter text-center">@lang('label.GRADE')</th>
            <th class="vcenter text-center">@lang('label.POSITION')</th>
            <?php
            $mksLimit = !empty($assignedObsnLimitInfo->comdt_mks_limit) ? $assignedObsnLimitInfo->comdt_mks_limit : 0;
            $limitPercent = !empty($assignedObsnLimitInfo->comdt_limit_percent) ? $assignedObsnLimitInfo->comdt_limit_percent : 0;
            ?>

            <th class="vcenter text-center">@lang('label.MKS')&nbsp;({!! $mksLimit !!})</th>
            {!! Form::hidden('assigned_mks', $mksLimit,['id' => 'assignedMksId']) !!}
            <th class="vcenter text-center">@lang('label.WT')&nbsp;({!! !empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : '0.00' !!})</th>
            {!! Form::hidden('assigned_wt',!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : 0,['id' => 'assignedWtId']) !!}


            <!--final-->
            <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
            <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
            <th class="vcenter text-center">@lang('label.PERCENT')</th>
            <th class="vcenter text-center">@lang('label.GRADE')</th>
            <th class="vcenter text-center">@lang('label.POSITION')</th>

        </tr>
    </thead>

    <tbody>
        <?php
        $sl = 0;
        $readonly = !empty($comdtObsnLockInfo) ? 'readonly' : '';
        $givenWt = !empty($comdtObsnLockInfo) ? '' : 'given-wt';
        ?>
        @foreach($cmArr as $cmId => $cmInfo)
        <?php
        $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
        $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');
//                    $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');
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
            <td class="vcenter width-150">
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
            @if(!empty($termDataArr))
            @foreach($termDataArr as $termId => $termName)
            <?php
            $totalAssignedWtTextAlign = !empty($cmInfo['term_total'][$termId]['total_assigned_wt']) ? 'right' : 'center';
            $totalWtTextAlign = !empty($cmInfo['term_total'][$termId]['total_wt']) ? 'right' : 'center';
            $totalPercentageTextAlign = !empty($cmInfo['term_total'][$termId]['percentage']) ? 'right' : 'center';
            ?>
            <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right">{!! !empty($cmInfo['term_total'][$termId]['total_assigned_wt']) ? Helper::numberFormatDigit2($cmInfo['term_total'][$termId]['total_assigned_wt']) : '' !!}</span>
            </td>
            <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right">{!! !empty($cmInfo['term_total'][$termId]['total_wt']) ? Helper::numberFormatDigit2($cmInfo['term_total'][$termId]['total_wt']) : '' !!}</span>
            </td>
            <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right">{!! !empty($cmInfo['term_total'][$termId]['percentage']) ? Helper::numberFormatDigit2($cmInfo['term_total'][$termId]['percentage']) : '' !!}</span>
            </td>
            <td class="text-center vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit">{!! !empty($cmInfo['term_total'][$termId]['total_grade']) ? $cmInfo['term_total'][$termId]['total_grade'] : '' !!} </span>
            </td>
            <td class="text-center vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit">{!! !empty($cmInfo['term_total'][$termId]['position']) ? $cmInfo['term_total'][$termId]['position'] : '' !!} </span>
            </td>
            @endforeach
            @endif

            <?php
            $totalAssignedWtTextAlign = !empty($cmInfo['agg_total_wt_limit']) ? 'right' : 'center';
            $totalWtTextAlign = !empty($cmInfo['term_agg_total_wt']) ? 'right' : 'center';
            $totalPercentageTextAlign = !empty($cmInfo['term_agg_percentage']) ? 'right' : 'center';
            ?>
            <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right" id="totalTermAssignedWt_{{$cmId}}">{!! !empty($cmInfo['agg_total_wt_limit']) ? Helper::numberFormatDigit2($cmInfo['agg_total_wt_limit']) : '' !!}</span>
            </td>
            <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right" id="totalTermWt_{{$cmId}}">{!! !empty($cmInfo['term_agg_total_wt']) ? Helper::numberFormatDigit2($cmInfo['term_agg_total_wt']) : '' !!}</span>
            </td>
            <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right">{!! !empty($cmInfo['term_agg_percentage']) ? Helper::numberFormatDigit2($cmInfo['term_agg_percentage']) : '' !!}</span>
            </td>
            <td class="text-center vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit">{!! !empty($cmInfo['term_agg_total_grade']) ? $cmInfo['term_agg_total_grade'] : '' !!} </span>
            </td>
            <td class="text-center vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit">{!! !empty($cmInfo['total_term_agg_position']) ? $cmInfo['total_term_agg_position'] : '' !!} </span>
            </td>
            <!--Start:: CI obsn-->
            <td class="vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right">
                    {!! !empty($cmInfo['ci_obsn_mks']) ? Helper::numberFormatDigit3($cmInfo['ci_obsn_mks']) : '' !!} 
                </span>
            </td>
            <td class="vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right">
                    {!! !empty($cmInfo['ci_obsn']) ? Helper::numberFormatDigit3($cmInfo['ci_obsn']) : '' !!} 
                </span>
            </td>

            <td class="vcenter width-80">
                <span id="afterCiWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit text-right">
                    {!! !empty($cmInfo['after_ci_obsn']) ? Helper::numberFormatDigit3($cmInfo['after_ci_obsn']) : '' !!} 
                </span>
            </td>
            <td class="vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right">
                    {!! !empty($cmInfo['total_wt']) ? Helper::numberFormatDigit3($cmInfo['total_wt']) : '' !!} 
                </span>
                {!! Form::hidden('wt['.$cmId.'][total_wt_after_ci]',!empty($cmInfo['total_wt']) ? $cmInfo['total_wt'] : null,['id' => 'totalWtAfterCi_'.$cmId, 'data-key' => $cmId, 'class' => 'form-control','autocomplete'=>'off']) !!} 
            </td>
            <td class="vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-right">
                    {!! !empty($cmInfo['percent']) ? Helper::numberFormatDigit3($cmInfo['percent']) : '' !!} 
                </span>
                {!! Form::hidden('wt['.$cmId.'][total_percent]',!empty($cmInfo['percent']) ? $cmInfo['percent'] : null,['id' => 'totalPercent_'.$cmId, 'data-key' => $cmId, 'class' => 'form-control total-percent','autocomplete'=>'off']) !!} 
            </td>

            <?php
            $totalPercent = !empty($cmInfo['percent']) ? $cmInfo['percent'] : 0;
            $obsnMksLimit = ($totalPercent * $limitPercent) / 100;
            $highRange = $totalPercent + $obsnMksLimit;
            $lowRange = $totalPercent - $obsnMksLimit;
            $title = __('label.RECOMMENDED_RANGE_OF_MKS', ['high' => Helper::numberFormatDigit3($highRange), 'low' => Helper::numberFormatDigit3($lowRange)]);
            ?>
            <td class="vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-center bold">
                    {!! $cmInfo['grade'] ?? '' !!}
                </span>
            </td>
            <td class="vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-center">
                    {!! !empty($cmInfo['position_after_ci_obsn']) ? $cmInfo['position_after_ci_obsn'] : '' !!}
                </span>
            </td>
            <!--end:: CI obsn-->
            <td class="vcenter width-80">
                {!! Form::text('wt['.$cmId.'][comdt_obsn_mks]',!empty($cmInfo['comdt_obsn_mks']) ? Helper::numberFormatDigit3($cmInfo['comdt_obsn_mks']) : null
                ,['id' => 'comdtObsnMksId_'.$cmId, 'data-key' => $cmId
                , 'class' => 'form-control integer-decimal-only width-inherit tooltips text-right ' . $givenWt
                , 'title' => $title,'autocomplete'=>'off',$readonly
                , 'data-high' => $highRange, 'data-low' => $lowRange]) !!} 

                {!! Form::hidden('wt['.$cmId.'][high_range]',$highRange,['id' => 'highRange_'.$cmId, 'data-key' => $cmId]) !!} 
                {!! Form::hidden('wt['.$cmId.'][low_range]',$lowRange,['id' => 'lowRange_'.$cmId, 'data-key' => $cmId]) !!} 

            </td>
            <td class="vcenter width-80">
                <span id="comdtObsnWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                    {!! !empty($cmInfo['comdt_obsn']) ? Helper::numberFormatDigit3($cmInfo['comdt_obsn']) : null !!}
                </span>
                {!! Form::hidden('wt['.$cmId.'][comdt_obsn]',!empty($cmInfo['comdt_obsn']) ? $cmInfo['comdt_obsn'] : null,['id' => 'comdtObsnIdVal_'.$cmId, 'data-key' => $cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right','readonly','autocomplete'=>'off']) !!} 
            </td>
            <td class="vcenter width-80">
                <span id="afterComdtWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                    {!! !empty($cmInfo['after_comdt_obsn']) ? Helper::numberFormatDigit3($cmInfo['after_comdt_obsn']) : null !!}
                </span>
            </td>
            <td class="vcenter width-80">
                <span id="totalWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                    {!! !empty($cmInfo['total_wt_after_comdt']) ? Helper::numberFormatDigit3($cmInfo['total_wt_after_comdt']) : null !!}
                </span>
                {!! Form::hidden('wt['.$cmId.'][total_wt]',!empty($cmInfo['total_wt_after_comdt']) ? $cmInfo['total_wt_after_comdt'] : null,['id' => 'totalWt_Val_'.$cmId, 'data-key' => $cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right','readonly','autocomplete'=>'off']) !!} 
            </td>
            <td class="vcenter width-80">
                {!! Form::text('wt['.$cmId.'][percentage]',!empty($cmInfo['percent_after_comdt']) ? Helper::numberFormatDigit3($cmInfo['percent_after_comdt']): null,['id' => 'percentId_'.$cmId, 'data-key' => $cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right','readonly','autocomplete'=>'off']) !!} 
            </td>
            <td class="vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-center bold" id="gradeName_{{$cmId}}">
                    {!! $cmInfo['grade_after_comdt'] ?? '' !!}
                </span>
            </td>
            {!! Form::hidden('wt['.$cmId.'][grade_id]',!empty($cmInfo['grade_id_after_comdt']) ? $cmInfo['grade_id_after_comdt'] : null,['id' => 'gradeId_'.$cmId]) !!}
            <td class="vcenter width-80">
                <span class="form-control integer-decimal-only width-inherit text-center">
                    {!! !empty($cmInfo['position_after_comdt_obsn']) ? $cmInfo['position_after_comdt_obsn'] : '' !!}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script src="{{asset('public/js/custom.js')}}"></script>
<script type="text/javascript">

$(document).ready(function () {
    $('tooltips').tooltip({
        container: 'body'
    });
    $(".table-head-fixer-color").tableHeadFixer({left: 5});


});

</script>