@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DS_OBSN')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'dsObsnReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
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
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
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
            <?php $dsDeligationList = Common::getDsDeligationList(); ?>

            @if(Request::get('generate') == 'true')
            @if(!empty($cmArr))
            <div class="row">
                <div class="col-md-12 text-right">
                    @if(!in_array(Auth::user()->group_id, [4]) || (!empty($dsDeligationList) && in_array(Auth::user()->id, $dsDeligationList)))
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
                            {{__('label.TRAINING_YEAR')}} : <strong> {{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong> {{$courseList->name}} |</strong>
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
                                    <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.CM')</th>
                                    <th class="vcenter" rowspan="2">@lang('label.PHOTO')</th>
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
                                    <th class="vcenter text-center" colspan="5">@lang('label.TOTAL')</th>
                                </tr>
                                <tr>
                                    @if(!empty($dsDataList))
                                    @foreach($dsDataList as $dsId => $dsInfo)
                                    <th class="vcenter text-center">
                                        @lang('label.MKS')&nbsp;({!! !empty($assignedDsObsnInfo->mks_limit) ? Helper::numberFormat2Digit($assignedDsObsnInfo->mks_limit) : '0.00' !!})
                                    </th>
                                    <th class="vcenter text-center">
                                        @lang('label.WT')&nbsp;({!! !empty($assignedDsObsnInfo->obsn) ? Helper::numberFormat2Digit($assignedDsObsnInfo->obsn) : '0.00' !!})
                                    </th>
                                    @endforeach
                                    @endif
                                    <th class="vcenter text-center">
                                        @lang('label.MKS')&nbsp;({!! !empty($assignedDsObsnInfo->mks_limit) ? Helper::numberFormat2Digit($assignedDsObsnInfo->mks_limit) : '0.00' !!})
                                    </th>
                                    <th class="vcenter text-center">
                                        @lang('label.WT')&nbsp;({!! !empty($assignedDsObsnInfo->obsn) ? Helper::numberFormat2Digit($assignedDsObsnInfo->obsn) : '0.00' !!})
                                    </th>
                                    <th class="vcenter text-center">@lang('label.PERCENT')</th>
                                    <th class="vcenter text-center">@lang('label.GRADE')</th>
                                    <th class="vcenter text-center">@lang('label.POSITION')</th>
                                </tr>

                            </thead>

                            <tbody>
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($cmArr as $cmId => $cmInfo)
                                <?php
                                $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;

                                $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');
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
                                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                                        {!! Form::hidden('cm_name['.$cmId.']',Common::getFurnishedCmName($cmName),['id' => 'cmId'])!!}
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
                                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? Helper::numberFormat2Digit($dsMksWtArr[$dsId][$cmId]['mks']) : '--' !!}</span>
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
                                    $totalPercentageTextAlign = !empty($cmInfo['total_term_percent']) ? 'right' : 'center';
                                    ?>
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
                url: "{{ URL::to('dsObsnReportCrnt/getCourse')}}",
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
                    App.unblockUI();
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
        //End::Get Course

        //Start::Get Term
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == 0) {
                $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
                return false;
            }
            $.ajax({
                url: "{{ URL::to('dsObsnReportCrnt/getTerm')}}",
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