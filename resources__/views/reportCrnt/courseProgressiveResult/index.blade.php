@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.COURSE_PROGRESSIVE_RESULT')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'courseProgressiveResultReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"></span></label>
                        <div class="col-md-8">
                            {!! Form::select('term_id', $termList, Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                            <span class="text-danger">{{ $errors->first('term_id') }}</span>
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
                            {{__('label.TRAINING_YEAR')}} : <strong> {{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong> {{$courseList->name}} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} </strong>
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
                                    <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.CM')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.PHOTO')</th>
                                    @if(!empty($termDataArr))
                                    @foreach($termDataArr as $termId => $termName)
                                    <th class="text-center vcenter" colspan="5">
                                        {!! !empty($termName) ? $termName : '' !!} (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['total_wt'][$termId]) ? Helper::numberFormat2Digit($eventMksWtArr['total_wt'][$termId]) : '0.00'}})
                                    </th>
                                    @endforeach
                                    @endif
                                    <th class="vcenter text-center" colspan="5">
                                        @lang('label.TERM_AGGREGATED_RESULT') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['agg_total_wt_limit']) ? Helper::numberFormat2Digit($eventMksWtArr['agg_total_wt_limit']) : '0.00'}})
                                    </th>
                                    @if(empty($request->term_id))
                                    <th class="vcenter text-center" rowspan="2">@lang('label.CI_OBSN')&nbsp;({!! !empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : '0.00' !!})</th>
                                    <th class="vcenter text-center" rowspan="2">@lang('label.COMDT_OBSN')&nbsp;({!! !empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : '0.00' !!})</th>
                                    <th class="vcenter text-center" colspan="5">
                                        @lang('label.FINAL') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['final_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['final_wt']) : '0.00'}})
                                    </th>
                                    @endif
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

                                    <!--final-->
                                    @if(empty($request->term_id))
                                    <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
                                    <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
                                    <th class="vcenter text-center">@lang('label.PERCENT')</th>
                                    <th class="vcenter text-center">@lang('label.GRADE')</th>
                                    <th class="vcenter text-center">@lang('label.POSITION')</th>
                                    @endif
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
                                            <a class="text-decoration-none-blue tooltips"  title="@lang('label.CLICK_HERE_TO_VIEW_CM_PROFILE_WITH_RESULT')" target="_new"
                                               href="{{URL::to('individualProfileReportCrnt?generate=true&training_year_id='.$activeTrainingYearList->id.'&course_id='.$courseList->id.'&cm_id='.$cmId)}}">
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


                                    @if(!empty($termDataArr))
                                    @foreach($termDataArr as $termId => $termName)
                                    <?php
                                    $totalAssignedWtTextAlign = !empty($cmInfo['term_total'][$termId]['total_assigned_wt']) ? 'right' : 'center';
                                    $totalWtTextAlign = !empty($cmInfo['term_total'][$termId]['total_wt']) ? 'right' : 'center';
                                    $totalPercentageTextAlign = !empty($cmInfo['term_total'][$termId]['percentage']) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_assigned_wt']) ? Helper::numberFormat2Digit($cmInfo['term_total'][$termId]['total_assigned_wt']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_wt']) ? Helper::numberFormat2Digit($cmInfo['term_total'][$termId]['total_wt']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['percentage']) ? Helper::numberFormat2Digit($cmInfo['term_total'][$termId]['percentage']) : '--' !!}</span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_grade']) ? $cmInfo['term_total'][$termId]['total_grade'] : '' !!} </span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['position']) ? $cmInfo['term_total'][$termId]['position'] : '' !!} </span>
                                    </td>
                                    @endforeach
                                    @endif


                                    <?php
                                    $totalAssignedWtTextAlign = !empty($cmInfo['agg_total_wt_limit']) ? 'right' : 'center';
                                    $totalWtTextAlign = !empty($cmInfo['term_agg_total_wt']) ? 'right' : 'center';
                                    $totalPercentageTextAlign = !empty($cmInfo['term_agg_percentage']) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['agg_total_wt_limit']) ? Helper::numberFormat2Digit($cmInfo['agg_total_wt_limit']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_total_wt']) ? Helper::numberFormat2Digit($cmInfo['term_agg_total_wt']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_percentage']) ? Helper::numberFormat2Digit($cmInfo['term_agg_percentage']) : '--' !!}</span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_total_grade']) ? $cmInfo['term_agg_total_grade'] : '' !!} </span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['total_term_agg_position']) ? $cmInfo['total_term_agg_position'] : '' !!} </span>
                                    </td>

                                    @if(empty($request->term_id))
                                    <!--ci comdt obsn-->
                                    <?php
                                    $ciObsnTextAlign = !empty($cmInfo['ci_obsn']) ? 'right' : 'center';
                                    $comdtObsnTextAlign = !empty($cmInfo['comdt_obsn']) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$ciObsnTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['ci_obsn']) ? Helper::numberFormat2Digit($cmInfo['ci_obsn']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$comdtObsnTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['comdt_obsn']) ? Helper::numberFormat2Digit($cmInfo['comdt_obsn']) : '--' !!}</span>
                                    </td>

                                    <!--final-->
                                    <?php
                                    $finalAssignedWtTextAlign = !empty($cmInfo['final_assigned_wt']) ? 'right' : 'center';
                                    $finalWtTextAlign = !empty($cmInfo['final_wt']) ? 'right' : 'center';
                                    $finalPerTextAlign = !empty($cmInfo['final_percentage']) ? 'right' : 'center';
                                    ?>
                                    <td class="text-{{$finalAssignedWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['final_assigned_wt']) ? Helper::numberFormat3Digit($cmInfo['final_assigned_wt']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$finalWtTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['final_wt']) ? Helper::numberFormat3Digit($cmInfo['final_wt']) : '--' !!}</span>
                                    </td>
                                    <td class="text-{{$finalPerTextAlign}} vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['final_percentage']) ? Helper::numberFormat2Digit($cmInfo['final_percentage']) : '--' !!}</span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['final_grade']) ? $cmInfo['final_grade'] : '' !!} </span>
                                    </td>
                                    <td class="text-center vcenter width-80">
                                        <span class="width-inherit bold">{!! !empty($cmInfo['final_position']) ? $cmInfo['final_position'] : '' !!} </span>
                                    </td>
                                    @endif

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
        $(".table-head-fixer-color").tableHeadFixer({left:5});

        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            if (trainingYearId == 0) {
                $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
                $('.course-err').html('');
                $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                return false;
            }
            $.ajax({
                url: "{{ URL::to('courseProgressiveResultReportCrnt/getCourse')}}",
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
                url: "{{ URL::to('courseProgressiveResultReportCrnt/getTerm')}}",
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