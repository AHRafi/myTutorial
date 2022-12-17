@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-pencil"></i>
                @lang('label.COMDT_OBSN_MARKING')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            {!! Form::hidden('auto_save', $autoSave, ['id' => 'autoSave']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label class="control-label col-md-2" for="trainingYearId">@lang('label.TRAINING_YEAR') :</label>
                                <div class="col-md-8">
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="courseId">@lang('label.COURSE') :</label>
                                <div class="col-md-8"> <div class="control-label pull-left"> <strong> {{$activeCourse->name}} </strong></div>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            @if(!empty($assignedObsnInfo))
                            @if(!empty($assignedObsnLimitInfo))
                            <div class="table-responsive webkit-scrollbar">
                                <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center vcenter" colspan="3">@lang('label.COMDT_OBSN_INFO')</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center vcenter">@lang('label.MKS')</th>
                                            <th class="text-center vcenter">@lang('label.LIMIT_PERCENT')</th>
                                            <th class="text-center vcenter">@lang('label.WT')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $obsnAlign = !empty($assignedObsnInfo->comdt_obsn_wt) ? 'right' : 'center';
                                        $mksLimitAlign = !empty($assignedObsnLimitInfo->comdt_mks_limit) ? 'right' : 'center';
                                        $limitPercentAlign = !empty($assignedObsnLimitInfo->comdt_limit_percent) ? 'right' : 'center';
                                        ?>
                                        <tr>
                                            <td class="vcenter text-{{$mksLimitAlign}} width-80">{!! !empty($assignedObsnLimitInfo->comdt_mks_limit) ? Helper::numberFormat2Digit($assignedObsnLimitInfo->comdt_mks_limit) : '--' !!}</td>
                                            <td class="vcenter text-{{$limitPercentAlign}} width-80">{!! !empty($assignedObsnLimitInfo->comdt_limit_percent) ? '&plusmn'.Helper::numberFormat2Digit($assignedObsnLimitInfo->comdt_limit_percent).'%' : '--' !!}</td>
                                            <td class="vcenter text-{{$obsnAlign}} width-80">{!! !empty($assignedObsnInfo->comdt_obsn_wt) ? Helper::numberFormat2Digit($assignedObsnInfo->comdt_obsn_wt) : '--' !!}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.OBSN_LIMIT_IS_NOT_ASSIGNED_YET') !!}</strong></p>
                            </div>
                            @endif
                            @else
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.OBSN_WT_IS_NOT_DISTRIBUTED_YET') !!}</strong></p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!--get module data-->
                    <div id="showCmMarkingList">
                        <div class="row">
                            @if(!empty($assignedObsnInfo))
                            @if(!empty($assignedObsnLimitInfo))
                            @if(!empty($ciObsnLockInfo))
                            @if(!empty($cmArr))
                            <div class="col-md-4 margin-top-10">
                                <span class="label label-md bold label-blue-steel">
                                    @lang('label.TOTAL_NO_OF_CM'): &nbsp;{!! sizeof($cmArr) !!}
                                </span>&nbsp;
                                <a class = "btn btn-sm bold label-green-seagreen tooltips" title="@lang('label.CLICK_HERE_TO_SEE_COURSE_MARKING_STATUS_SUMMARY')" type="button" href="#modalInfo" data-toggle="modal" id="courseStatusSummaryId">
                                    @lang('label.COURSE_STATUS_SUMMARY')
                                </a>
                            </div>

                            <div class="col-md-2 text-right">
                                @if(!$comdtObsnDataArr->isEmpty())
                                @if(empty($comdtObsnLockInfo))
                                <button class="btn btn-sm btn-danger tooltips margin-top-10" type="button" id="buttonDelete" >
                                    @lang('label.CLEAR_MARKING')
                                </button>
                                @endif        
                                @endif
                            </div>
                            <div class="col-md-3 margin-top-15">
                                @if(empty($comdtObsnLockInfo))
                                <div class="md-checkbox vcenter">
                                    {!! Form::checkbox('auto_fill',1,null,['id' => 'checkAutoFill', 'class'=> 'md-check auto-fill']) !!}
                                    <label for="checkAutoFill">
                                        <span></span>
                                        <span class="check bold"></span>
                                        <span class="box bold"></span>
                                    </label>
                                    <span class="bold">@lang('label.PUT_TICK_TO_AUTO_FILL')</span>
                                </div>
                                @endif        
                            </div>

                            <div class="col-md-3 margin-top-10 text-right">
                                <label class="control-label" for="sortBy">@lang('label.SORT_BY') :</label>&nbsp;
                                <label class="control-label width-150" for="sortBy">
                                    {!! Form::select('sort', $sortByList, Request::get('sort_by'),['class' => 'form-control js-source-states','id'=>'sortBy']) !!}
                                </label>
                            </div>
                            <div class="col-md-12 margin-top-10">
                                <div class="max-height-500 table-responsive webkit-scrollbar cm-marking-list">
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
                                </div>
                            </div>
                            <div class="col-md-12 margin-top-10">
                                <div class="row">
                                    @if(!empty($comdtObsnLockInfo))
                                    @if($comdtObsnLockInfo['status'] == '1')
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-circle label-purple-sharp request-for-unlock" type="button" id="buttonSubmitLock" data-target="#modalUnlockMessage" data-toggle="modal">
                                            <i class="fa fa-unlock"></i> @lang('label.REQUEST_FOR_UNLOCK')
                                        </button>
                                    </div>
                                    @elseif($comdtObsnLockInfo['status'] == '2')
                                    <div class="col-md-12">
                                        <div class="alert alert-danger alert-dismissable">
                                            <p><strong><i class="fa fa-unlock"></i> {!! __('label.REQUESTED_TO_SUPER_ADMIN_FOR_UNLOCK') !!}</strong></p>
                                        </div>
                                    </div>
                                    @endif
                                    @else
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-circle label-blue-steel button-submit" data-id="1" type="button" id="buttonSubmit" >
                                            <i class="fa fa-file-text-o"></i> @lang('label.SAVE_AS_DRAFT')
                                        </button>&nbsp;&nbsp;
                                        <button class="btn btn-circle green button-submit" data-id="2" type="button" id="buttonSubmitLock" >
                                            <i class="fa fa-lock"></i> @lang('label.SAVE_LOCK')
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="col-md-12 margin-top-10">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_ASSIGNED_TO_THIS_COURSE') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                            @else
                            <div class="col-md-12 margin-top-10">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.CI_OBSN_MARKING_IS_NOT_LOCKED_YET') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                            @else
                            <div class="col-md-12 margin-top-10">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.OBSN_LIMIT_IS_NOT_ASSIGNED_YET') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                            @else
                            <div class="col-md-12 margin-top-10">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.OBSN_WT_IS_NOT_DISTRIBUTED_YET') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!-- Unlock message modal -->
<div class="modal fade test" id="modalUnlockMessage" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showMessage"></div>
    </div>
</div>
<!-- End Unlock message modal -->

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
<script src="{{asset('public/js/custom.js')}}"></script>
<script type="text/javascript">
$(function () {

    var options = {
        closeButton: true,
        debug: false,
        positionClass: "toast-bottom-right",
        timeOut: 1000,
        onclick: null
    };


//table header fix
    $(".table-head-fixer-color").tableHeadFixer({left: 5});

    $(document).on('click', '.auto-fill', function (e) {
        $('.total-percent').each(function () {
            var key = $(this).attr('data-key');

            var givenMks = parseFloat($(this).val());
            var assignedMks = parseFloat($("#assignedMksId").val());
            var assignedWt = parseFloat($("#assignedWtId").val());
            var totalWtAfterCiObsn = parseFloat($("#totalWtAfterCi_" + key).val());
            var afterCiWt = parseFloat($("#afterCiWt_" + key).text());
            var afterComdtWt = parseFloat($("#afterComdtWt_" + key).text());

            var givenWt = (givenMks * assignedWt) / assignedMks;

            if (totalWtAfterCiObsn == '' || isNaN(totalWtAfterCiObsn)) {
                totalWtAfterCiObsn = 0;
            }
            afterComdtWt = Number(assignedWt) + Number(afterCiWt);

            var wt = parseFloat(Number(totalWtAfterCiObsn) + Number(givenWt)).toFixed(3);
            var wtVal = parseFloat(Number(totalWtAfterCiObsn) + Number(givenWt)).toFixed(6);
            var wtPercent = parseFloat((wt / afterComdtWt) * 100).toFixed(3);
            if ($(".auto-fill").prop("checked") == true && !isNaN(givenWt)) {
                $("#comdtObsnMksId_" + key).val(parseFloat(givenMks).toFixed(3));
                $("#comdtObsnIdVal_" + key).val(parseFloat(givenWt).toFixed(6));
                $("#comdtObsnWt_" + key).text(parseFloat(givenWt).toFixed(3));
                $("#afterComdtWt_" + key).text(parseFloat(afterComdtWt).toFixed(3));
                $("#totalWt_" + key).text(wt);
                $("#totalWt_Val_" + key).val(wtVal);
                $("#percentId_" + key).val(wtPercent);
                $("#gradeName_" + key).text(findGradeName(gradeArr, wtPercent));
                $("#gradeId_" + key).val(findGradeId(gradeIdArr, wtPercent));
            } else {
                $("#comdtObsnMksId_" + key).val('');
                $("#comdtObsnIdVal_" + key).val('');
                $("#comdtObsnWt_" + key).text('');
                $("#afterComdtWt_" + key).text(parseFloat(afterCiWt).toFixed(3));
                $("#totalWt_" + key).text('');
                $("#totalWt_Val_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeName_" + key).text('');
                $("#gradeId_" + key).val('');
            }
        });
    });


    $(document).on("change", "#courseId", function () {
        var courseId = $("#courseId").val();
        if (courseId == '0') {
            $('#showCmMarkingList').html('');
            return false;
        }
        $.ajax({
            url: "{{ URL::to('comdtObsnMarking/showCmMarkingList')}}",
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
                $('#showCmMarkingList').html(res.html);
                $('.tooltips').tooltip();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                App.unblockUI();
            }
        });//ajax
    });

    // Start::Sort
    $(document).on("change", "#sortBy", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var eventId = $("#eventId").val();
        var sortBy = $("#sortBy").val();

        $.ajax({
            url: "{{ URL::to('comdtObsnMarking/filter')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
                event_id: eventId,
                sort_by: sortBy,
            },
            beforeSend: function () {
                $('.cm-marking-list').html('');
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $(".auto-fill").prop("checked", false)
                $('.cm-marking-list').html(res.html);
                $('.js-source-states').select2();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                App.unblockUI();
            }
        });//ajax
    });
    //End::Sort

    //form submit
    $(document).on('click', '.button-submit', function (e) {
        e.preventDefault();
        var dataId = $(this).attr('data-id');
        var confMsg = dataId == '2' ? 'Send' : 'Save';
        var form_data = new FormData($('#submitForm')[0]);
        form_data.append('data_id', dataId);

        swal({
            title: 'Are you sure?',

            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, ' + confMsg,
            cancelButtonText: 'No, Cancel',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{URL::to('comdtObsnMarking/saveObsnMarking')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    beforeSend: function () {
                        $('.button-submit').prop('disabled', true);
                        App.blockUI({boxed: true});
                    },
                    success: function (res) {
                        $('.button-submit').prop('disabled', false);
                        toastr.success(res.message, res.heading, options);
//                        $("#courseId").trigger('change');
                        location.reload();
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
                        $('.button-submit').prop('disabled', false);
                        App.unblockUI();
                    }

                });
            }
        });
    });

    //start :: auto save
    setInterval(function () {
        if ($('#autoSave').val() == 1) {
            var dataId = 1;
            var form_data = new FormData($('#submitForm')[0]);
            form_data.append('data_id', dataId);
            form_data.append('auto_saving', 1);
            $.ajax({
                url: "{{URL::to('comdtObsnMarking/saveObsnMarking')}}",
                type: "POST",
                datatype: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                beforeSend: function () {
                    $('.button-submit').prop('disabled', true);
                    toastr.info("@lang('label.SAVING')", "", options);
                },
                success: function (res) {
                    $('.button-submit').prop('disabled', false);
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
                    $('.button-submit').prop('disabled', false);
                }

            });
        }

    }, 30000);
    //end :: auto save

    //delete
    $(document).on('click', '#buttonDelete', function (e) {
        e.preventDefault();
        var form_data = new FormData($('#submitForm')[0]);

        swal({
            title: 'Are you sure?',

            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'No, Cancel',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{URL::to('comdtObsnMarking/clearMarking')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    beforeSend: function () {
                        $('#buttonDelete').prop('disabled', true);
                        App.blockUI({boxed: true});
                    },
                    success: function (res) {
                        $('#buttonDelete').prop('disabled', false);
                        toastr.success(res.message, res.heading, options);
                        location.reload();
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
            }
        });
    });


//Rquest for unlock
    $(document).on('click', '.request-for-unlock', function (e) {
        e.preventDefault();

        var form_data = new FormData($('#submitForm')[0]);

        $.ajax({
            url: "{{URL::to('comdtObsnMarking/getRequestForUnlockModal')}}",
            type: "POST",
            datatype: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            beforeSend: function () {
                $('#showMessage').html('');
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showMessage').html(res.html);
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

    $(document).on('click', '.save-request-for-unlock', function (e) {
        e.preventDefault();
        var unlockMessage = $("#unlockMsgId").val();
        var form_data = new FormData($('#submitForm')[0]);
        form_data.append('unlock_message', unlockMessage);

        swal({
            title: 'Are you sure?',

            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, Send',
            cancelButtonText: 'No, Cancel',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{URL::to('comdtObsnMarking/saveRequestForUnlock')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (res) {
                        $('.modal').modal('hide');
                        toastr.success(res, '@lang("label.REQUEST_FOR_UNLOCK_HAS_BEEN_SENT_TO_SUPERADMIN_SUCCESSFULLY")', options);
//                        $("#courseId").trigger('change');
                        location.reload();
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

    //Start:: Request for course status summary
    $(document).on('click', '#courseStatusSummaryId', function (e) {
        e.preventDefault();
        var courseId = $("#courseId").val();
        $.ajax({
            url: "{{URL::to('comdtObsnMarking/requestCourseSatatusSummary')}}",
            type: "POST",
            datatype: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
            },
            success: function (res) {
                $('#showCourseStatus').html(res.html);
                $('.tooltips').tooltip();
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
                    toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
                }
                App.unblockUI();
            }
        });
    });
    //end:: Request for course status summary

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
            url: "{{URL::to('comdtObsnMarking/getDsMarkingSummary')}}",
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

    $(document).on('keyup', '.given-wt', function () {
        var key = $(this).attr('data-key');
        var givenMks = parseFloat($(this).val());
        var assignedMks = parseFloat($("#assignedMksId").val());
        var assignedWt = parseFloat($("#assignedWtId").val());
        var totalWtAfterCiObsn = parseFloat($("#totalWtAfterCi_" + key).val());
        var afterCiWt = parseFloat($("#afterCiWt_" + key).text());
        var afterComdtWt = parseFloat($("#afterComdtWt_" + key).text());
        var highestMks = parseFloat($(this).attr('data-high'));

        var givenWt = (givenMks * assignedWt) / assignedMks;
        var highestWt = (highestMks * assignedWt) / assignedMks;

        if (totalWtAfterCiObsn == '' || isNaN(totalWtAfterCiObsn)) {
            totalWtAfterCiObsn = 0;
        }
        afterComdtWt = Number(assignedWt) + Number(afterCiWt);


        if (givenWt > highestWt) {
            swal({
                title: '@lang("label.YOUR_GIVEN_MKS_EXCEEDED_FROM_HIGHEST_MKS")',

                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#comdtObsnMksId_" + key).val('');
                $("#comdtObsnIdVal_" + key).val('');
                $("#comdtObsnWt_" + key).text('');
                $("#afterComdtWt_" + key).text(parseFloat(afterCiWt).toFixed(3));
                $("#totalWt_" + key).text('');
                $("#totalWt_Val_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeId_" + key).val('');
                $("#gradeName_" + key).text('');
                setTimeout(function () {
                    $("#comdtObsnMksId_" + key).focus();
                }, 250);
                return false;
            });
        } else {
            var wt = parseFloat(Number(totalWtAfterCiObsn) + Number(givenWt)).toFixed(3);
            var wtVal = parseFloat(Number(totalWtAfterCiObsn) + Number(givenWt)).toFixed(6);
            var wtPercent = parseFloat((wt / afterComdtWt) * 100).toFixed(3);
            if (!isNaN(givenWt)) {
                $("#comdtObsnIdVal_" + key).val(parseFloat(givenWt).toFixed(6));
                $("#comdtObsnWt_" + key).text(parseFloat(givenWt).toFixed(3));
                $("#afterComdtWt_" + key).text(parseFloat(afterComdtWt).toFixed(3));
                $("#totalWt_" + key).text(wt);
                $("#totalWt_Val_" + key).val(wtVal);
                $("#percentId_" + key).val(wtPercent);
                $("#gradeName_" + key).text(findGradeName(gradeArr, wtPercent));
                $("#gradeId_" + key).val(findGradeId(gradeIdArr, wtPercent));
            } else {
                $("#comdtObsnIdVal_" + key).val('');
                $("#comdtObsnWt_" + key).text('');
                $("#afterComdtWt_" + key).text(parseFloat(afterCiWt).toFixed(3));
                $("#totalWt_" + key).text('');
                $("#totalWt_Val_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeName_" + key).text('');
                $("#gradeId_" + key).val('');
            }
        }

    });

    $(document).on('blur', '.given-wt', function () {
        var key = $(this).attr('data-key');
        var givenMks = parseFloat($(this).val());
        var assignedMks = parseFloat($("#assignedMksId").val());
        var assignedWt = parseFloat($("#assignedWtId").val());
        var afterCiWt = parseFloat($("#afterCiWt_" + key).text());
        var lowestMks = parseFloat($(this).attr('data-low'));

        var givenWt = (givenMks * assignedWt) / assignedMks;
        var lowestWt = (lowestMks * assignedWt) / assignedMks;

        if (givenWt < lowestWt) {
            swal({
                title: '@lang("label.YOUR_GIVEN_MKS_GRATHER_THEN_LOWEST_MKS")',

                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#comdtObsnMksId_" + key).val('');
                $("#comdtObsnIdVal_" + key).val('');
                $("#comdtObsnWt_" + key).text('');
                $("#afterComdtWt_" + key).text(parseFloat(afterCiWt).toFixed(3));
                $("#totalWt_" + key).text('');
                $("#totalWt_Val_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeId_" + key).val('');
                $("#gradeName_" + key).text('');
                setTimeout(function () {
                    $("#comdtObsnMksId_" + key).focus();
                }, 250);
                return false;
            });
        }
    });

//start :: produce grade arr for javascript
    var gradeArr = [];
    var gradeIdArr = [];
    var letter = '';
    var letterId = '';
    var startRange = 0;
    var endRange = 0;
<?php
if (!$gradeInfo->isEmpty()) {
    foreach ($gradeInfo as $grade) {
        ?>
            letter = '<?php echo $grade->grade_name; ?>';
            letterId = '<?php echo $grade->id; ?>';
            startRange = <?php echo $grade->marks_from; ?>;
            endRange = <?php echo $grade->marks_to; ?>;
            gradeArr[letter] = [];
            gradeArr[letter]['start'] = startRange;
            gradeArr[letter]['end'] = endRange;

            gradeIdArr[letterId] = [];
            gradeIdArr[letterId]['start'] = startRange;
            gradeIdArr[letterId]['end'] = endRange;
        <?php
    }
}
?>
    function findGradeName(gradeArr, mark) {
        var achievedGrade = '';
        for (var letter in gradeArr) {
            var range = gradeArr[letter];
            if (mark == 100) {
                achievedGrade = "A+";
            }
            if (range['start'] <= mark && mark < range['end']) {
                achievedGrade = letter;
            }
        }

        return achievedGrade;
    }

    function findGradeId(gradeIdArr, mark) {
        var achievedGradeId = '';
        for (var letterId in gradeIdArr) {
            var range = gradeIdArr[letterId];
            if (mark == 100) {
                achievedGradeId = 1;
            }
            if (range['start'] <= mark && mark < range['end']) {
                achievedGradeId = letterId;
            }
        }

        return achievedGradeId;
    }
//end :: produce grade arr for javascript
});

</script>
@stop