@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EVENT_RESULT')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'eventResultReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                        <div class="col-md-7">
                                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearList->name}} </strong></div>
                                            {!! Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                        <div class="col-md-7">
                                            <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                                            {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                                        <div class="col-md-8">
                                            {!! Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger required-sub-event"> {{ !empty($hasSubEvent) ? '*' : ''}}</span></label>
                                        <div class="col-md-8">
                                            {!! Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
                                            {!! Form::hidden('has_sub_event',$hasSubEvent,['id' => 'hasSubEvent']) !!}
                                            <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>    
                            <div class="row"> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :<span class="text-danger required-sub-sub-event"> {{ !empty($hasSubSubEvent) ? '*' : ''}}</span></label>
                                        <div class="col-md-8">
                                            {!! Form::select('sub_sub_event_id', $subSubEventList, Request::get('sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']) !!}
                                            {!! Form::hidden('has_sub_sub_event',$hasSubSubEvent,['id' => 'hasSubSubEvent']) !!}
                                            <span class="text-danger">{{ $errors->first('sub_sub_event_id') }}</span>
                                        </div>
                                    </div>
                                </div>   
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="subSubSubEventId">@lang('label.SUB_SUB_SUB_EVENT') :<span class="text-danger required-sub-sub-sub-event"> {{ !empty($hasSubSubSubEvent) ? '*' : ''}}</span></label>
                                        <div class="col-md-8">
                                            {!! Form::select('sub_sub_sub_event_id', $subSubSubEventList, Request::get('sub_sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId']) !!}
                                            {!! Form::hidden('has_sub_sub_sub_event',$hasSubSubSubEvent,['id' => 'hasSubSubSubEvent']) !!}
                                            <span class="text-danger">{{ $errors->first('sub_sub_sub_event_id') }}</span>
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
                        </div>
                        <div class="col-md-4">
                            @if(Request::get('generate') == 'true')
                            @if(!empty($dsDataList))
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-head-fixer-color">
                                    <thead>
                                        <tr>
                                            <th class="text-center vcenter" colspan="4">@lang('label.MKS_WT_INFO')</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center vcenter">@lang('label.MKS_LIMIT')</th>
                                            <th class="text-center vcenter">@lang('label.HIGHEST_MKS_LIMIT')</th>
                                            <th class="text-center vcenter">@lang('label.LOWEST_MKS_LIMIT')</th>
                                            <th class="text-center vcenter">@lang('label.WT')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php
                                            $mksLimitTextAlign = !empty($assingedMksWtInfo['mks_limit']) ? 'right' : 'center';
                                            $mksLimitHighTextAlign = !empty($assingedMksWtInfo['highest_mks_limit']) ? 'right' : 'center';
                                            $mksLimitLowTextAlign = !empty($assingedMksWtInfo['lowest_mks_limit']) ? 'right' : 'center';
                                            $mksLimitWtTextAlign = !empty($assingedMksWtInfo['wt']) ? 'right' : 'center';
                                            ?>
                                            <td class="text-{{$mksLimitTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['mks_limit']) : '0.00' !!}</td>
                                            <td class="text-{{$mksLimitHighTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['highest_mks_limit']) : '0.00' !!}</td>
                                            <td class="text-{{$mksLimitLowTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['lowest_mks_limit']) : '0.00' !!}</td>
                                            <td class="text-{{$mksLimitWtTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['wt']) ? Helper::numberFormat2Digit($assingedMksWtInfo['wt']) : '0.00' !!}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if(Request::get('generate') == 'true')
            @if(!empty($cmArr))
            @if(!empty($dsDataList))
            <div class="row">
                <div class="col-md-12 text-right">
                    <!--                    <a class="btn btn-md btn-primary vcenter tooltips" title="@lang('label.PRINT')" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                                            <span><i class="fa fa-print"></i> </span> 
                                        </a>
                                        <a class="btn btn-success vcenter tooltips" title="@lang('label.DOWNLOAD_PDF')" href="{!! URL::full().'&view=pdf' !!}">
                                            <span><i class="fa fa-file-pdf-o"></i></span>
                                        </a>-->
                    <a class="btn btn-warning vcenter tooltips" title="@lang('label.DOWNLOAD_EXCEL')" href="{!! URL::full().'&view=excel' !!}">
                        <span><i class="fa fa-file-excel-o"></i> </span>
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
            @endif
            <div class="row">
                <div class="col-md-12 margin-top-10">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong>{{$courseList->name}} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} |</strong>
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }} |</strong>
                            @if(!empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0)
                            {{__('label.SUB_EVENT')}} : <strong>{{ $subEventList[Request::get('sub_event_id')] }} |</strong>
                            @endif
                            @if(!empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0)
                            {{__('label.SUB_SUB_EVENT')}} : <strong>{{ $subSubEventList[Request::get('sub_sub_event_id')] }} |</strong>
                            @endif
                            @if(!empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0)
                            {{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ $subSubSubEventList[Request::get('sub_sub_sub_event_id')] }} |</strong>
                            @endif
                            @if(!empty($dsDataList))
                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($cmArr) ? sizeof($cmArr) : 0 }} </strong>
                            @endif
                            <!--{{__('label.TOTAL_NO_OF_DS')}} : <strong>{{ !empty($dsArr) ? sizeof($dsArr) : 0 }}</strong>-->

                        </h5>
                    </div>
                </div>
                <div class="col-md-12  margin-top-10">

                    <?php $dsDeligationList = Common::getDsDeligationList(); ?>
                    @if(!empty($dsDataList))
                    <div class=" table-responsive max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <?php
                            $eventMkslimit = !empty($assingedMksWtInfo['mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['mks_limit']) : '0.00';
                            $eventHighestMkslimit = !empty($assingedMksWtInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['highest_mks_limit']) : '0.00';
                            $eventLowestMkslimit = !empty($assingedMksWtInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['lowest_mks_limit']) : '0.00';
                            $eventWt = !empty($assingedMksWtInfo['wt']) ? Helper::numberFormat2Digit($assingedMksWtInfo['wt']) : '0.00';
                            ?>
                            <thead>
                                <tr>
                                    <?php $rowspan = !in_array(Auth::user()->group_id, [4]) || (!empty($dsDeligationList) && in_array(Auth::user()->id, $dsDeligationList)) ? 3 : 1; ?>
                                    <th class="text-center vcenter" rowspan="{{$rowspan}}">@lang('label.SL_NO')</th>
                                    <th class="vcenter" rowspan="{{$rowspan}}">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter" rowspan="{{$rowspan}}">@lang('label.RANK')</th>
                                    <th class="vcenter" rowspan="{{$rowspan}}">@lang('label.CM')</th>
                                    <th class="vcenter" rowspan="{{$rowspan}}">@lang('label.PHOTO')</th>
                                    <!--<th class="vcenter" rowspan="3">@lang('label.SYNDICATE')</th>-->
                                    @if(!in_array(Auth::user()->group_id, [4]) || (!empty($dsDeligationList) && in_array(Auth::user()->id, $dsDeligationList)))
                                    <th class="text-center vcenter" colspan="{{ (!empty($dsDataList) ? sizeof($dsDataList) : 1)*2 }}">@lang('label.DS_MARKING')</th>
                                    <th class="text-center vcenter" rowspan="2" colspan="2">@lang('label.AVERAGE')</th>
                                    <th class="text-center vcenter" rowspan="3">@lang('label.CI_MODERATION')</th>
                                    <!--<th class="text-center vcenter" rowspan="2" colspan="2">@lang('label.AFTER_CI_MODERATION')</th>-->
<!--                                    <th class="text-center vcenter" rowspan="3">@lang('label.COMDT_MODERATION')</th>
                                    <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AFTER_COMDT_MODERATION')</th>-->
                                    <th class="text-center vcenter" rowspan="2" colspan="5">@lang('label.FINAL')</th>
                                    @else
                                    <th class="vcenter text-center">@lang('label.MKS')</th>
                                    <th class="text-center vcenter">@lang('label.WT')</th>
                                    @endif
                                </tr>
                                @if(!in_array(Auth::user()->group_id, [4]) || (!empty($dsDeligationList) && in_array(Auth::user()->id, $dsDeligationList)))
                                <tr>
                                    @if(!empty($dsDataList))
                                    @foreach($dsDataList as $dsId => $dsInfo)
                                    <?php
                                    $src = URL::to('/') . '/public/img/unknown.png';
                                    $alt = $dsInfo['ds_name'] ?? '';
                                    $personalNo = !empty($dsInfo['personal_no']) ? '(' . $dsInfo['personal_no'] . ')' : '';
                                    if (!empty($dsInfo['photo']) && File::exists('public/uploads/user/' . $dsInfo['photo'])) {
                                        $src = URL::to('/') . '/public/uploads/user/' . $dsInfo['photo'];
                                    }
                                    ?>
                                    <th class="text-center vcenter" colspan="2">
                                        <span class="tooltips" data-html="true" data-placement="bottom" title="
                                              <div class='text-center'>
                                              <img width='50' height='60' src='{!! $src !!}' alt='{!! $alt !!}'/><br/>
                                              <strong>{!! $alt !!}<br/>
                                              {!! $personalNo !!} </strong>
                                              </div>
                                              ">
                                            {{ $dsInfo['appt'] ?? '' }}
                                        </span>

                                    </th>
                                    @endforeach
                                    @endif
                                </tr>
                                <tr>
                                    <!--DS Marking-->
                                    @if(!empty($dsDataList))
                                    @foreach($dsDataList as $dsId => $dsInfo)
                                    <th class="vcenter text-center">
                                        <span class="tooltips" data-html="true" title="
                                              <div class='text-left'>
                                              @lang('label.HIGHEST_MKS_LIMIT'): &nbsp;{!! $eventHighestMkslimit !!}<br/>
                                              @lang('label.LOWEST_MKS_LIMIT'): &nbsp;{!! $eventLowestMkslimit !!}<br/>
                                              </div>
                                              ">
                                            @lang('label.MKS')
                                        </span>
                                    </th>
                                    <th class="text-center vcenter">@lang('label.WT')</th>
<!--                                    <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                    <th class="text-center vcenter">@lang('label.GRADE') </th>-->
                                    @endforeach
                                    @endif


                                    <!--Average-->
                                    <th class="vcenter text-center">@lang('label.MKS')</th>
                                    <th class="text-center vcenter">@lang('label.WT')</th>

                                    <!--After CI Moderation-->
<!--                                    <th class="vcenter text-center">@lang('label.MKS')</th>
                                    <th class="text-center vcenter">@lang('label.WT')</th>-->

                                    <!--                                    After Comdt Moderation
                                                                        <th class="vcenter text-center">@lang('label.MKS')</th>
                                                                        <th class="text-center vcenter">@lang('label.WT')</th>
                                                                        <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                                                        <th class="text-center vcenter">@lang('label.GRADE') </th>-->

                                    <!--Final Marking-->
                                    <th class="vcenter text-center">@lang('label.MKS')</th>
                                    <th class="text-center vcenter">@lang('label.WT')</th>
                                    <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                    <th class="text-center vcenter">@lang('label.GRADE') </th>
                                    <th class="text-center vcenter">@lang('label.POSITION') </th>

                                </tr>
                                @endif
                            </thead>

                            <tbody>
                                <?php $sl = 0; ?>
                                @foreach($cmArr as $cmId => $cmInfo)
                                <?php
                                $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
                                $readonly = !empty($comdtModerationMarkingLockInfo) ? 'readonly' : '';
                                $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');

                                $avgDsMark = !empty($avgDsMksWtArr['mks'][$cmId]) ? $avgDsMksWtArr['mks'][$cmId] : 0;
                                $modLimit = !empty($comdtMksInfo->comdt_mod) ? $comdtMksInfo->comdt_mod : 0;
                                $modMark = (($avgDsMark * $modLimit) / 100);
                                $title = __('label.RECOMMENDED_MAX_MIN_VALUE', ['mod_mark' => $modMark]);
//                                $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');
                                ?>
                                <tr>
                                    <td class="vcenter tex-center width-80">
                                        <div class="width-inherit text-center">{!! ++$sl !!}</div>
                                    </td>
                                    <td class="vcenter width-80">
                                        <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-50">
                                        <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-200">
                                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                                    </td>
                                    <td class="vcenter" width="50px">
                                        @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                        @else
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                        @endif
                                    </td>

                                    <!--DS Marking-->
                                    @if(!empty($dsDataList))
                                    @foreach($dsDataList as $dsId => $dsInfo)
                                    <?php
                                    $dsMarkingTextAlign = !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? 'right' : 'center';
                                    $dsMarkingRemarks = !empty($dsMksWtArr[$dsId][$cmId]['remarks']) ? $dsMksWtArr[$dsId][$cmId]['remarks'] : '';
                                    $dsMarkingRemarksColor = !empty($dsMksWtArr[$dsId][$cmId]['remarks']) ? 'text-danger' : '';
                                    ?>
                                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80 tooltips" title="{{$dsMarkingRemarks}}">
                                        <span class="width-inherit bold {{$dsMarkingRemarksColor}}">{!! !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? Helper::numberFormat2Digit($dsMksWtArr[$dsId][$cmId]['mks']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['wt']) ? Helper::numberFormat2Digit($dsMksWtArr[$dsId][$cmId]['wt']) : '--' !!}</span>
                                    </td>
<!--                                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['percentage']) ? $dsMksWtArr[$dsId][$cmId]['percentage'] : '--' !!}</span>
                                    </td>
                                    <td class="text-center  vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['grade_name']) ? $dsMksWtArr[$dsId][$cmId]['grade_name'] : '--' !!}</span>
                                    </td>-->

                                    @endforeach
                                    @endif

                                    @if(!in_array(Auth::user()->group_id, [4]) || (!empty($dsDeligationList) && in_array(Auth::user()->id, $dsDeligationList)))
                                    <!--Average-->
                                    <?php
                                    $dsAvgMarkingTextAlign = !empty($avgDsMksWtArr['mks'][$cmId]) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$dsAvgMarkingTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['mks'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['mks'][$cmId]) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$dsAvgMarkingTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['wt'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['wt'][$cmId]) : '--' !!}</span>
                                    </td>

                                    <!--CI Moderation-->
                                    <?php
                                    $ciModTextAlign = !empty($ciMksWtArr[$cmId]['ci_moderation']) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$ciModTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['ci_moderation']) ? Helper::numberFormat2Digit($ciMksWtArr[$cmId]['ci_moderation']) : '--' !!}</span>
                                    </td>

                                    <!--After CI Moderation-->
                                    <?php
                                    $ciModerationTextAlign = !empty($ciMksWtArr[$cmId]['mks']) ? 'right' : 'center';
                                    ?>
<!--                                    <td class="text-{{$ciModTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['mks']) ? $ciMksWtArr[$cmId]['mks'] : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$ciModTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['wt']) ? $ciMksWtArr[$cmId]['wt'] : '--' !!}</span>
                                    </td>
                                    -->
                                    <!--Comdt Moderation-->
                                    <?php
                                    $comdtModTextAlign = !empty($prevMksWtArr[$cmId]['comdt_moderation']) ? 'right' : 'center';
                                    ?>
<!--                                    <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['comdt_moderation']) ? $prevMksWtArr[$cmId]['comdt_moderation'] : '--' !!}</span>
                                    </td>-->

                                    <!--After Comdt Moderation-->
                                    <?php
                                    $comdtModerationTextAlign = !empty($prevMksWtArr[$cmId]['mks']) ? 'right' : 'center';
                                    ?>
<!--                                    <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['mks']) ? $prevMksWtArr[$cmId]['mks'] : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['wt']) ? $prevMksWtArr[$cmId]['wt'] : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['percentage']) ? $prevMksWtArr[$cmId]['percentage'] : '--' !!}</span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['grade_name']) ? $prevMksWtArr[$cmId]['grade_name'] : '--' !!}</span>
                                    </td>-->

                                    <!--final Marking-->
                                    <?php
                                    $finalMksTextAlign = !empty($cmInfo['final_mks']) ? 'right' : 'center';
                                    $finalMks = !empty($cmInfo['final_mks']) ? Helper::numberFormat2Digit($cmInfo['final_mks']) : '--';
                                    $finalWt = !empty($cmInfo['final_wt']) ? Helper::numberFormat3Digit($cmInfo['final_wt']) : '--';
                                    $finalPercentage = !empty($cmInfo['final_percentage']) ? Helper::numberFormat2Digit($cmInfo['final_percentage']) : '--';
                                    $finalGrade = !empty($cmInfo['final_grade_name']) ? $cmInfo['final_grade_name'] : '--';
                                    $finalPosition = !empty($cmInfo['position']) ? $cmInfo['position'] : '--';
                                    ?>
                                    <td class="text-{{$finalMksTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold"> {{$finalMks}} </span>
                                    </td>
                                    <td class="text-{{$finalMksTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold"> {{$finalWt}} </span>
                                    </td>
                                    <td class="text-{{$finalMksTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold"> {{$finalPercentage}} </span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold"> {{$finalGrade}} </span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold"> {{$finalPosition}} </span>
                                    </td>

                                    @endif
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-danger alert-dismissable">
                        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_MARKING_GROUP_IS_ASSIGNED_YET') !!}</strong></p>
                    </div>
                    @endif
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
        $(".table-head-fixer-color").tableHeadFixer({left:5});
        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            if (trainingYearId == 0) {
                $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
                $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-event').text('');
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('eventResultReportCrnt/getCourse')}}",
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
                    $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                    $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                    $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-event').text('');
                    $('.required-sub-sub-event').text('');
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

        //Start::Get Term
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == 0) {
                $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-event').text('');
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('eventResultReportCrnt/getTerm')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                    $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                    $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-event').text('');
                    $('.required-sub-sub-event').text('');
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

        //Start::Get Event
        $(document).on("change", "#termId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            if (termId == 0) {
                $('#eventId').html("<option value='0'>@lang('label.SELECT_EVENT_OPT')</option>");
                $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-event').text('');
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('eventResultReportCrnt/getEvent')}}",
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
                    $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                    $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-event').text('');
                    $('.required-sub-sub-event').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#eventId').html(res.html);
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
        //End::Get Event

        //Start::Get Sub Event
        $(document).on("change", "#eventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            $('#hasSubEvent').val(0);
            $('#hasSubSubEvent').val(0);
            $('#hasSubSubSubEvent').val(0);
            if (eventId == 0) {
                $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-event').text('');
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('eventResultReportCrnt/getSubEventReportCrnt')}}",
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
                    $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-event').text('');
                    $('.required-sub-sub-event').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    if (res.html != '') {
                        $('#subEventId').html(res.html);
                        $('.required-sub-event').text('*');
                        $('#hasSubEvent').val(1);
                    } else {
                        $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
                        $('.required-sub-event').text('');
                        $('#hasSubEvent').val(0);
                    }
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
        //End::Get Sub Event

        //Start::Get Sub Sub Event
        $(document).on("change", "#subEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            $('#hasSubSubEvent').val(0);
            $('#hasSubSubSubEvent').val(0);
            if (subEventId == 0) {
                $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-sub-sub-event').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('eventResultReportCrnt/getSubSubEventReportCrnt')}}",
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
                    $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                    $('.required-sub-sub-event').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    if (res.html != '') {
                        $('#subSubEventId').html(res.html);
                        $('.required-sub-sub-event').text('*');
                        $('#hasSubSubEvent').val(1);
                    } else {
                        $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
                        $('.required-sub-sub-event').text('');
                        $('#hasSubSubEvent').val(0);
                    }
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
        //End::Get Sub Sub Event

        //Start::Get Sub Sub Sub Event
        $(document).on("change", "#subSubEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            $('#hasSubSubSubEvent').val(0);
            if (subSubEventId == 0) {
                $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                $('.required-show').text('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('eventResultReportCrnt/getSubSubSubEventReportCrnt')}}",
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
                    $('.required-show').text('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#subSubSubEventId').html(res.html);
                    if (res.html != '') {
                        $('#subSubSubEventId').html(res.html);
                        $('.required-sub-sub-sub-event').text('*');
                        $('#hasSubSubSubEvent').val(1);
                    } else {
                        $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
                        $('.required-sub-sub-sub-event').text('');
                        $('#hasSubSubSubEvent').val(0);
                    }
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
        //End::Get Sub Sub Sub Event


    });
</script>


@stop