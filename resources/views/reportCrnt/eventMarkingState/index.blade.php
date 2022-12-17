@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EVENT_MARKING_STATE')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'eventMarkingStateReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearList->name}} </strong></div>
                            {!! Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
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
                        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                            <span class="text-danger">{{ $errors->first('event_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
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
                    <!--                    <a class="btn btn-md btn-primary vcenter tooltips" title="@lang('label.PRINT')" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                                            <span class=""><i class="fa fa-print"></i> </span> 
                                        </a>
                                        <a class="btn btn-success vcenter tooltips" title="@lang('label.DOWNLOAD_PDF')" href="{!! URL::full().'&view=pdf' !!}">
                                            <span class=""><i class="fa fa-file-pdf-o"></i></span>
                                        </a>-->
                    <a class="btn btn-warning vcenter tooltips" title="@lang('label.DOWNLOAD_EXCEL')" href="{!! URL::full().'&view=excel' !!}">
                        <span class=""><i class="fa fa-file-excel-o"></i> </span>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong>{{$courseList->name}}</strong>
                            @if(!empty($cmArr))
                            <strong> | </strong>{{__('label.TOTAL_CM_WITH_NO_PARTICIPATION')}} : <strong>{{!empty($eventMksWtArr['total_none_pct_cm']) ? $eventMksWtArr['total_none_pct_cm'] : 0}}</strong>
                            @endif
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
                                    <th class="text-center vcenter" rowspan="4">@lang('label.SL_NO')</th>
                                    <th class="vcenter" rowspan="4">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter" rowspan="4">@lang('label.RANK')</th>
                                    <th class="vcenter" rowspan="4">@lang('label.CM')</th>
                                    <th class="vcenter" rowspan="4">@lang('label.PHOTO')</th>
                                    <!--<th class="vcenter" rowspan="5">@lang('label.SYNDICATE')</th>-->
                                    @if (!empty($eventMksWtArr['mks_wt']))
                                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                    <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId]) && sizeof($eventMksWtArr['event'][$eventId]) > 1 ? 1 : 4 !!}"
                                        colspan="{!! !empty($rowSpanArr['event'][$eventId]) && $rowSpanArr['event'][$eventId] > 1 ? $rowSpanArr['event'][$eventId] : 1 !!}">
                                        {!! !empty($eventMksWtArr['event'][$eventId]['name']) ? $eventMksWtArr['event'][$eventId]['name'] : '' !!}
                                    </th>
                                    @endforeach
                                    @endif
                                    <th class="vcenter text-center" rowspan="4">@lang('label.TOTAL_MARKED_EVENT')</th>

                                </tr>
                                <tr>
                                    @if (!empty($eventMksWtArr['mks_wt']))
                                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                    @foreach($evInfo as $subEventId => $subEvInfo)
                                    @if(!empty($subEventId))
                                    <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId][$subEventId]) && sizeof($eventMksWtArr['event'][$eventId][$subEventId]) > 1 ? 1 : 3 !!}"
                                        colspan="{!! !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] : 1 !!}">
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
                                        colspan="{!! !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] : 1 !!}">
                                        {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] : '' !!}
                                    </th>
                                    @endif
                                    @endforeach
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
                                    <th class="vcenter text-center">
                                        {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' !!}
                                    </th>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endif
                                </tr>


                            </thead>

                            <tbody>
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($cmArr as $cmId => $cmInfo)
                                <?php
                                $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
                                $totalMked = !empty($cmInfo['total_mked']) ? $cmInfo['total_mked'] : 0;
                                $totalMkedSign = !empty($cmInfo['total_mked']) ? '' : 'warning';
                                $totalMkedColor = !empty($cmInfo['total_mked']) ? 'green-steel' : 'red-intense';
                                ?>
                                <tr>
                                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                                    <td class="vcenter width-80">
                                        <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}&nbsp;<span class=" bold text-red-intense"><i class="fa fa-{{$totalMkedSign}}"></i></span></div>
                                    </td>
                                    <td class="vcenter width-80">
                                        <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                                    </td>
                                    <td class="vcenter width-180">
                                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                                    </td>
                                    <td class="vcenter" width="50px">
                                        @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                        @else
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
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
                                    $mksSign = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) ? 'check' : 'close';
                                    $mksColor = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) ? 'green-steel' : 'red-intense';
                                    ?>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold text-{{$mksColor}}"><i class="fa fa-{{$mksSign}}"></i></span>
                                    </td>

                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endif

                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold text-{{$totalMkedColor}}">{!! $totalMked !!}</span>
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
                return false;
            }
            $.ajax({
                url: "{{ URL::to('eventMarkingStateReportCrnt/getCourse')}}",
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
                    $('#courseId').html(res.html);
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            });//ajax

        });
        //End::Get Course
    });
</script>


@stop