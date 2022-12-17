@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EVENT_MKS_WT')
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'eventListReport/filter','class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            {!! Form::select('training_year_id', !empty($activeTrainingYearInfo)?$activeTrainingYearInfo:null,  Request::get('training_year_id'), ['class' => 'form-control js-source-states', 'id' => 'trainingYearId']) !!}
                            <span class="text-danger">{{ $errors->first('training_year_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') <span class="text-danger">*</span></label>
                        <div class="col-md-8" id="showCourse">
                            {!! Form::select('course_id', !empty($courseList)?$courseList:null,  Request::get('course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') </label>
                        <div class="col-md-8" id="showTerm">
                            {!! Form::select('term_id', $termList,  Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                            <span class="text-danger">{{ $errors->first('term_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="form-group">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn margin-bottom-20" id="generateId" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> @lang('label.GENERATE')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <!--filter form close-->
            @if($request->generate == 'true')
            @if (!empty($eventMksWtArr['mks_wt']))
            <div class="row">
                <div class="col-md-12 text-right">
                    <a class="btn btn-md btn-primary vcenter tooltips" title="@lang('label.PRINT')" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                        <span class=""><i class="fa fa-print"></i> </span> 
                    </a>
                    <!--                    <a class="btn btn-success vcenter tooltips" title="@lang('label.DOWNLOAD_PDF')" href="{!! URL::full().'&view=pdf' !!}">
                                            <span class=""><i class="fa fa-file-pdf-o"></i></span>
                                        </a>-->
                    <a class="btn btn-warning vcenter tooltips" title="@lang('label.DOWNLOAD_EXCEL')" href="{!! URL::full().'&view=excel' !!}">
                        <span class=""><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                    @if(empty(Request::get('term_id')))
                    <label class="control-label" for="sortBy">@lang('label.SORT_BY') :</label>&nbsp;

                    <label class="control-label" for="sortBy">
                        {!! Form::select('sort', $sortByList, Request::get('sort'),['class' => 'form-control','id'=>'sortBy']) !!}
                    </label>

                    <button class="btn green-jungle filter-btn"  id="sortByHref" type="submit">
                        <i class="fa fa-arrow-right"></i>  @lang('label.GO')
                    </button>
                    @endif
                </div>
            </div>
            @endif
            {!! Form::close() !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearInfo[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearInfo[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} </strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="table-responsive max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.EVENT')</th>
                                    <th class="vcenter">@lang('label.SUB_EVENT')</th>
                                    <th class="vcenter">@lang('label.SUB_SUB_EVENT')</th>
                                    <th class="vcenter">@lang('label.SUB_SUB_SUB_EVENT')</th>
                                    <th class="vcenter text-center">@lang('label.MKS_LIMIT')</th>
                                    <th class="vcenter text-center">@lang('label.HIGHEST_MKS_LIMIT')</th>
                                    <th class="vcenter text-center">@lang('label.LOWEST_MKS_LIMIT')</th>
                                    <th class="vcenter text-center">@lang('label.WT')</th>
                                    @if(empty(Request::get('term_id')))
                                    <th class="vcenter text-center">@lang('label.EVENT_TOTAL')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($eventMksWtArr['mks_wt']))
                                <?php $sl = 0; ?>
                                @foreach($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                <?php $eventId = !empty($evInfo['event_id']) ? $evInfo['event_id'] : 0; ?>
                                <tr>
                                    <td class="vcenter text-center" rowspan="{!! !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 !!}">{!! ++$sl !!}</td>
                                    <td class="vcenter"  rowspan="{!! !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 !!}">
                                        {!! !empty($eventMksWtArr['event'][$eventId]['name']) ? $eventMksWtArr['event'][$eventId]['name'] : '' !!}
                                    </td>

                                    @if(!empty($evInfo) && !is_numeric($evInfo))
                                    <?php $i = 0; ?>
                                    @foreach($evInfo as $subEventId => $subEvInfo)
                                    @if(is_numeric($subEventId))
                                    <?php
                                    if ($i > 0) {
                                        echo '<tr>';
                                    }
                                    ?>

                                    <td class="vcenter"  rowspan="{!! !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] : 1 !!}">
                                        {!! !empty($eventMksWtArr['event'][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId]['name'] : '' !!}
                                    </td>

                                    <?php $j = 0; ?>
                                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                    <?php
                                    if ($j > 0) {
                                        echo '<tr>';
                                    }
                                    ?>
                                    <td class="vcenter"  rowspan="{!! !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] : 1 !!}">
                                        {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] : '' !!}
                                    </td>

                                    @if(!empty($subSubEvInfo) && !is_numeric($subSubEvInfo))
                                    <?php $k = 0; ?>
                                    @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                                    <?php
                                    if ($k > 0) {
                                        echo '<tr>';
                                    }
                                    ?>
                                    <td class="vcenter">
                                        {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' !!}
                                    </td>
                                    <?php
                                    $eventMkslimit = !empty($subSubSubEvInfo['mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['mks_limit']) : '--';
                                    $eventHighestMkslimit = !empty($subSubSubEvInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['highest_mks_limit']) : '--';
                                    $eventLowestMkslimit = !empty($subSubSubEvInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['lowest_mks_limit']) : '--';
                                    $eventWt = !empty($subSubSubEvInfo['wt']) ? Helper::numberFormat2Digit($subSubSubEvInfo['wt']) : '--';
                                    $eventWiseTotalWt = !empty($eventMksWtArr['event_total_wt'][$eventId]) ? Helper::numberFormat2Digit($eventMksWtArr['event_total_wt'][$eventId]) : '--';

                                    $eventMkslimitTextAlign = !empty($subSubSubEvInfo['mks_limit']) ? 'right' : 'center';
                                    $eventHighestMkslimitTextAlign = !empty($subSubSubEvInfo['highest_mks_limit']) ? 'right' : 'center';
                                    $eventLowestMkslimitTextAlign = !empty($subSubSubEvInfo['lowest_mks_limit']) ? 'right' : 'center';
                                    $eventWtTextAlign = !empty($subSubSubEvInfo['wt']) ? 'right' : 'center';
                                    $eventWiseTotalWtTextAlign = !empty($eventMksWtArr['event_total_wt'][$eventId]) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$eventMkslimitTextAlign}} width-80">
                                        <span class="width-inherit">{!! $eventMkslimit !!}</span>
                                    </td>
                                    <td class="text-{{$eventHighestMkslimitTextAlign}} width-80">
                                        <span class="width-inherit">{!! $eventHighestMkslimit !!}</span>
                                    </td>
                                    <td class="text-{{$eventLowestMkslimitTextAlign}} width-80">
                                        <span class="width-inherit">{!! $eventLowestMkslimit !!}</span>
                                    </td>
                                    <td class="text-{{$eventWtTextAlign}} width-80">
                                        <span class="width-inherit">{!! $eventWt !!}</span>
                                    </td>
                                    @if(empty(Request::get('term_id')))
                                    @if($i == 0 && $j == 0 && $k == 0)
                                    <td class="vcenter text-{{$eventWiseTotalWtTextAlign}} width-80" rowspan="{!! !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 !!}">
                                        <span class="width-inherit">{!! $eventWiseTotalWt !!}</span>
                                    </td>
                                    @endif
                                    @endif
                                    <?php
                                    if ($i < ($rowSpanArr['event'][$eventId] - 1)) {
                                        if ($j < ($rowSpanArr['sub_event'][$eventId][$subEventId] - 1)) {
                                            if ($k < ($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] - 1)) {
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
                                <tr class="info">
<!--                                    <th class="text-right bold" colspan="5"> @lang('label.TOTAL') </th>
                                    <th class="text-right width-80">
                                        <span class="width-inherit bold">{!! !empty($eventMksWtArr['total_mks_limit']) ? Helper::numberFormat2Digit($eventMksWtArr['total_mks_limit']) : '0.00' !!}</span>
                                    </th>-->

                                    <th class="text-right" colspan="{{ (empty(Request::get('term_id')) ? 3 : 2) + 6 }}">@lang('label.TOTAL')</th>
                                    <th class="text-right width-80">
                                        <span class="width-inherit bold">{!! !empty($eventMksWtArr['total_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['total_wt']) : '0.00' !!}</span>
                                    </th>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="9">@lang('label.NO_EVENT_IS_ASSIGNED_TO_THIS_TERM')</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            @if(empty(Request::get('term_id')))
            <div class="row margin-top-10">
                <div class="col-md-8">
                    @if(!$dsObsnMksWtInfo->isEmpty())
                    <div class="table-responsive max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.DS_OBSN')</th>
                                    <th class="vcenter text-center">@lang('label.MKS_LIMIT')</th>
                                    <th class="vcenter text-center">@lang('label.LIMIT_PERCENT')</th>
                                    <th class="vcenter text-center">@lang('label.WT')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sl = 0; ?>
                                @foreach($dsObsnMksWtInfo as $dsObsn)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">{!! $dsObsn->term ?? '' !!}</td>
                                    <td class="vcenter text-right">{!! !empty($dsObsn->mks_limit) ? Helper::numberFormat2Digit($dsObsn->mks_limit) : '--' !!}</td>
                                    <td class="vcenter text-right">{!! !empty($dsObsn->limit_percent) ? Helper::numberFormat2Digit($dsObsn->limit_percent) : '--' !!}</td>
                                    <td class="vcenter text-right">{!! !empty($dsObsn->obsn) ? Helper::numberFormat2Digit($dsObsn->obsn) : '--' !!}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th class="text-right bold" colspan="2"> @lang('label.TOTAL') </th>
                                    <th class="text-right width-80">
                                        <span class="width-inherit bold">{!! !empty($dsObsnMksWtArr['total_mks']) ? Helper::numberFormat2Digit($dsObsnMksWtArr['total_mks']) : '0.00' !!}</span>
                                    </th>
                                    <th></th>
                                    <th class="text-right width-80">
                                        <span class="width-inherit bold">{!! !empty($dsObsnMksWtArr['total_wt']) ? Helper::numberFormat2Digit($dsObsnMksWtArr['total_wt']) : '0.00' !!}</span>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                <div class="col-md-4">
                    @if(!empty($courseWtInfo))
                    <div class="table-responsive max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.CRITERIA')</th>
                                    <th class="vcenter text-center">@lang('label.WT')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="vcenter text-center">1</td>
                                    <td class="vcenter">@lang('label.TOTAL_EVENT_WT')</td>
                                    <td class="vcenter text-right">{!! !empty($courseWtInfo->total_event_wt) ? Helper::numberFormat2Digit($courseWtInfo->total_event_wt) : '--' !!}</td>
                                </tr>
                                <tr>
                                    <td class="vcenter text-center">2</td>
                                    <td class="vcenter">@lang('label.DS_OBSN_WT')</td>
                                    <td class="vcenter text-right">{!! !empty($courseWtInfo->ds_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->ds_obsn_wt) : '--' !!}</td>
                                </tr>
                                <tr>
                                    <td class="vcenter text-center">3</td>
                                    <td class="vcenter">@lang('label.CI_OBSN_WT')</td>
                                    <td class="vcenter text-right">{!! !empty($courseWtInfo->ci_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->ci_obsn_wt) : '--' !!}</td>
                                </tr>
                                <tr>
                                    <td class="vcenter text-center">4</td>
                                    <td class="vcenter">@lang('label.COMDT_OBSN_WT')</td>
                                    <td class="vcenter text-right">{!! !empty($courseWtInfo->comdt_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->comdt_obsn_wt) : '--' !!}</td>
                                </tr>
                                <tr>
                                    <th class="vcenter bold text-right" colspan="2">@lang('label.TOTAL')</th>
                                    <th class="vcenter text-right bold">{!! !empty($courseWtInfo->total_wt) ? Helper::numberFormat2Digit($courseWtInfo->total_wt) : '--' !!}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
<script src="{{asset('/public/js/custom.js')}}"></script>
<script type="text/javascript">

$(function () {
    //table header fix
    $(".table-head-fixer-color").tableHeadFixer('');
    //Start::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();

        $.ajax({
            url: "{{ URL::to('eventListReport/getCourse')}}",
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
                $('#showCourse').html(res.view);
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
        $.ajax({
            url: "{{ URL::to('eventListReport/getTerm')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showTerm').html(res.view);
                $(".js-source-states").select2();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        });//ajax

    });
    //End::Get Term
});

</script>
@stop