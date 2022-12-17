@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.TERM_RESULT')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'termResultReport/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                            <span class="text-danger course-err"></span>
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
                <div class="col-md-12 text-center">
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
                    @if(!in_array(Auth::user()->group_id, [4]))
                    <!--                    <a class="btn btn-md btn-primary vcenter tooltips" title="@lang('label.PRINT')" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                                            <span class=""><i class="fa fa-print"></i> </span> 
                                        </a>
                                        <a class="btn btn-success vcenter tooltips" title="@lang('label.DOWNLOAD_PDF')" href="{!! URL::full().'&view=pdf' !!}">
                                            <span class=""><i class="fa fa-file-pdf-o"></i></span>
                                        </a>-->
                    <a class="btn btn-warning vcenter tooltips" title="@lang('label.DOWNLOAD_EXCEL')" href="{!! URL::full().'&view=excel' !!}">
                        <span class=""><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                    @endif
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
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} </strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                @if(!empty($cmArr))
                <div class="col-md-12 margin-top-10">
                    <div class="max-height-500 table-responsive webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color">
                            <thead>
                                <tr>
                                    <th class="text-center vcenter" rowspan="5">@lang('label.SL_NO')</th>
                                    <th class="vcenter" rowspan="5">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter" rowspan="5">@lang('label.RANK')</th>
                                    <th class="vcenter" rowspan="5">@lang('label.CM')</th>
                                    <th class="vcenter" rowspan="5">@lang('label.PHOTO')</th>
                                    <!--<th class="vcenter" rowspan="5">@lang('label.SYNDICATE')</th>-->
                                    @if (!empty($eventMksWtArr['mks_wt']))
                                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                    <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId]) && sizeof($eventMksWtArr['event'][$eventId]) > 1 ? 1 : 4 !!}"
                                        colspan="{!! !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] * 2 : 2 !!}">
                                        {!! !empty($eventMksWtArr['event'][$eventId]['name']) ? $eventMksWtArr['event'][$eventId]['name'] : '' !!}
                                    </th>
                                    @endforeach
                                    @endif
                                    <th class="vcenter text-center" colspan="2" rowspan="4">@lang('label.DS_OBSN')</th>
                                    <th class="vcenter text-center" colspan="5" rowspan="3">@lang('label.TERM_TOTAL')</th>
                                </tr>
                                <tr>
                                    @if (!empty($eventMksWtArr['mks_wt']))
                                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                    @foreach($evInfo as $subEventId => $subEvInfo)
                                    @if(!empty($subEventId))
                                    <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId][$subEventId]) && sizeof($eventMksWtArr['event'][$eventId][$subEventId]) > 1 ? 1 : 3 !!}"
                                        colspan="{!! !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] * 2 : 2 !!}">
                                        {!! !empty($eventMksWtArr['event'][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId]['name'] : '' !!}
                                    </th>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @endif
                                </tr>
                                <tr>
                                    @if (!empty($eventMksWtArr['mks_wt']))
                                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                    @foreach($evInfo as $subEventId => $subEvInfo)
                                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                    @if(!empty($subSubEventId))
                                    <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]) && sizeof($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]) > 1 ? 1 : 2 !!}"
                                        colspan="{!! !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] * 2 : 2 !!}">
                                        {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] : '' !!}
                                    </th>
                                    @endif
                                    @endforeach
                                    @if(!empty($eventMksWtArr['avg_mks_sub_ev'][$eventId][$subEventId]))
                                    <th class="vcenter text-center" rowspan="2" colspan="2">
                                        @lang('label.AVG')
                                    </th>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @endif
                                </tr>
                                <tr>
                                    @if (!empty($eventMksWtArr['mks_wt']))
                                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                    @foreach($evInfo as $subEventId => $subEvInfo)
                                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                    @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                                    @if(!empty($subSubSubEventId))
                                    <th class="vcenter text-center" colspan="2">
                                        {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' !!}
                                    </th>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endif
                                    <th class="vcenter text-center" colspan="2">
                                        @lang('label.WT') (@lang('label.TOTAL'): {{!empty($eventMksWtArr['total_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['total_wt']) : '0.00'}})
                                    </th>

                                    <th class="vcenter text-center" rowspan="2">@lang('label.PERCENT')</th>
                                    <th class="vcenter text-center" rowspan="2">@lang('label.GRADE')</th>
                                    <th class="vcenter text-center" rowspan="2">@lang('label.POSITION')</th>
                                </tr>
                                <tr>
                                    @if (!empty($eventMksWtArr['mks_wt']))
                                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                    @foreach($evInfo as $subEventId => $subEvInfo)
                                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                    @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                                    <?php
                                    $eventMkslimit = !empty($subSubSubEvInfo['mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['mks_limit']) : '0.00';
                                    $eventHighestMkslimit = !empty($subSubSubEvInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['highest_mks_limit']) : '0.00';
                                    $eventLowestMkslimit = !empty($subSubSubEvInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['lowest_mks_limit']) : '0.00';
                                    $eventWt = !empty($subSubSubEvInfo['wt']) ? Helper::numberFormat2Digit($subSubSubEvInfo['wt']) : '0.00';
                                    ?>
                                    <th class="vcenter text-center">
                                        <span class="tooltips" data-html="true" title="
                                              <div class='text-left'>
                                              @lang('label.HIGHEST_MKS_LIMIT'): &nbsp;{!! $eventHighestMkslimit !!}<br/>
                                              @lang('label.LOWEST_MKS_LIMIT'): &nbsp;{!! $eventLowestMkslimit !!}<br/>
                                              </div>
                                              ">
                                            @lang('label.MKS')&nbsp;({!! $eventMkslimit !!})
                                        </span>
                                    </th>
                                    <th class="vcenter text-center">
                                        @lang('label.WT')&nbsp;({!! $eventWt !!})
                                    </th>
<!--                                    <th class="vcenter text-center">@lang('label.PERCENT')</th>
                                    <th class="vcenter text-center">@lang('label.GRADE')</th>-->
                                    @endforeach
                                    @endforeach
                                    @if(!empty($eventMksWtArr['avg_mks_sub_ev'][$eventId][$subEventId]))
                                    <?php
                                    $avgEventMkslimit = !empty($eventMksWtArr['avg_mks_sub_ev_mks'][$eventId][$subEventId]) ? Helper::numberFormat2Digit($eventMksWtArr['avg_mks_sub_ev_mks'][$eventId][$subEventId]) : '0.00';
                                    $avgEventWt = !empty($eventMksWtArr['avg_mks_sub_ev_wt'][$eventId][$subEventId]) ? Helper::numberFormat2Digit($eventMksWtArr['avg_mks_sub_ev_wt'][$eventId][$subEventId]) : '0.00';
                                    ?>
                                    <th class="vcenter text-center">
                                        @lang('label.MKS')&nbsp;({!! $avgEventMkslimit !!})
                                    </th>
                                    <th class="vcenter text-center">
                                        @lang('label.WT')&nbsp;({!! $avgEventWt !!})
                                    </th>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @endif
                                    <th class="vcenter text-center">
                                        @lang('label.MKS')&nbsp;({!! !empty($assignedDsObsnInfo->mks_limit) ? Helper::numberFormat2Digit($assignedDsObsnInfo->mks_limit) : '0.00' !!})
                                    </th>
                                    <th class="vcenter text-center">
                                        @lang('label.WT')&nbsp;({!! !empty($assignedDsObsnInfo->obsn) ? Helper::numberFormat2Digit($assignedDsObsnInfo->obsn) : '0.00' !!})
                                    </th>
                                    <th class="vcenter text-center">
                                        @lang('label.ASSIGNED')
                                    </th>                                    
                                    <th class="vcenter text-center">
                                        @lang('label.ACHIEVED')
                                    </th>                
                                </tr>

                            </thead>

                            <tbody>
                                <?php
                                $sl = 0;
                                $readonly = !empty($comdtObsnLockInfo) ? 'readonly' : '';
                                $givenWt = !empty($comdtObsnLockInfo) ? 'given-wt' : '';
                                ?>
                                @foreach($cmArr as $cmId => $cmInfo)
                                <?php
                                $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;

                                $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');
                                $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');
                                ?>
                                <tr>
                                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                                    <td class="vcenter width-80">
                                        <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-80">
                                        <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-150">
                                        <div class="width-inherit">
                                            @if(in_array(Auth::user()->group_id,[3,4]))
                                            <a class="text-decoration-none-blue tooltips" title="@lang('label.CLICK_HERE_TO_VIEW_CM_PROFILE_WITH_RESULT')" target="_new"
                                               href="{{URL::to('individualProfileReport?generate=true&training_year_id='.Request::get('training_year_id').'&course_id='.Request::get('course_id').'&cm_id='.$cmId)}}">
                                                {!! Common::getFurnishedCmName($cmInfo['full_name']) !!}
                                            </a>
                                            @else
                                            {!! Common::getFurnishedCmName($cmInfo['full_name']) !!}
                                            @endif
                                        </div>
                                        {!! Form::hidden('cm_name['.$cmId.']',Common::getFurnishedCmName($cmName),['id' => 'cmId'])!!}
                                    </td>
                                    <td class="vcenter" width="50px">
                                        @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                        @endif
                                    </td>
                                    <?php
                                    $totalMks = 0;
                                    $totalWt = 0;
                                    ?>
                                    @if (!empty($eventMksWtArr['mks_wt']))
                                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                    @foreach($evInfo as $subEventId => $subEvInfo)
                                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                    @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)

                                    <?php
                                    $mksTextAlign = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) ? 'right' : 'center';
                                    $wtTextAlign = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? 'right' : 'center';
                                    $percentageTextAlign = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['percentage']) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$mksTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) ? Helper::numberFormat2Digit($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$wtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) ? Helper::numberFormat2Digit($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['wt']) : '--' !!}</span>
                                    </td>
<!--                                    <td class="text-{{$percentageTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['percentage']) ? $achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['percentage'] : '--' !!}</span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['grade_name']) ? $achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['grade_name'] : '--' !!}</span>
                                    </td>-->
                                    @endforeach
                                    @endforeach
                                    @if(!empty($eventMksWtArr['avg_mks_sub_ev'][$eventId][$subEventId]))
                                    <?php
                                    $avgMksTextAlign = !empty($cmInfo['avg_mks'][$eventId][$subEventId]) ? 'right' : 'center';
                                    $avgWtTextAlign = !empty($cmInfo['avg_wt'][$eventId][$subEventId]) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$avgMksTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['avg_mks'][$eventId][$subEventId]) ? Helper::numberFormat2Digit($cmInfo['avg_mks'][$eventId][$subEventId]) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$avgWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['avg_wt'][$eventId][$subEventId]) ? Helper::numberFormat2Digit($cmInfo['avg_wt'][$eventId][$subEventId]) : '--' !!}</span>
                                    </td>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @endif
                                    <?php
                                    $dsObsnMksTextAlign = !empty($cmInfo['ds_obsn_mks']) ? 'right' : 'center';
                                    $dsObsnWtTextAlign = !empty($cmInfo['ds_obsn_wt']) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$dsObsnMksTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['ds_obsn_mks']) ? Helper::numberFormat2Digit($cmInfo['ds_obsn_mks']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$dsObsnWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['ds_obsn_wt']) ? Helper::numberFormat2Digit($cmInfo['ds_obsn_wt']) : '--' !!}</span>
                                    </td>
                                    <?php
                                    $totalAssignedWtTextAlign = !empty($cmInfo['total_assigned_wt']) ? 'right' : 'center';
                                    $totalWtTextAlign = !empty($cmInfo['total_term_wt']) ? 'right' : 'center';
                                    $totalPercentageTextAlign = !empty($cmInfo['total_term_percent']) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['total_assigned_wt']) ? Helper::numberFormat2Digit($cmInfo['total_assigned_wt']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['total_term_wt']) ? Helper::numberFormat3Digit($cmInfo['total_term_wt']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['total_term_percent']) ? Helper::numberFormat2Digit($cmInfo['total_term_percent']) : '--' !!}</span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['grade_after_term_total']) ? $cmInfo['grade_after_term_total'] : '' !!} </span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['total_term_position']) ? $cmInfo['total_term_position'] : '' !!} </span>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="col-md-12 margin-top-10">
                    <div class="alert alert-danger alert-dismissable">
                        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_ASSIGNED_TO_THIS_COURSE') !!}</strong></p>
                    </div>
                </div>
                @endif
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
            if (trainingYearId == 0) {
                $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
                $('.course-err').html("");
                $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                return false;
            }
            $.ajax({
                url: "{{ URL::to('termResultReport/getCourse')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#courseId').html(res.html);
                    $('.course-err').html(res.html1);
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            });//ajax

        });
        //End::Get Course

        //Start::Get Term
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == 0) {
                $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                return false;
            }
            $.ajax({
                url: "{{ URL::to('termResultReport/getTerm')}}",
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
                    $("#previewMarkingSheet").prop("disabled", false);
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
        //End::Get Term
    });
</script>


@stop