@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-pencil"></i>@lang('label.DS_OBSN_MARKING')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            {!! Form::hidden('auto_save', $autoSave, ['id' => 'autoSave']) !!}
            <div class="row" id="sortedCm">
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') :</label>
                        <div class="col-md-8">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :</label>
                        <div class="col-md-4">
                            <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                            {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') :</label>
                        <div class="col-md-4">
                            <div class="control-label pull-left"> <strong> {{$openTermInfo->name}} </strong></div>
                            {!! Form::hidden('term_id', $openTermInfo->id, ['id' => 'termId']) !!}
                        </div>
                    </div>
                    <!--get module data-->
                </div>
                <div class="col-md-6">
                    @if(!empty($assignedObsnInfo))
                    <div class="table-responsive webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center vcenter" colspan="3">@lang('label.DS_OBSN_INFO')</th>
                                </tr>
                                <tr>
                                    <th class="text-center vcenter">@lang('label.MKS')</th>
                                    <th class="text-center vcenter">@lang('label.LIMIT_PERCENT')</th>
                                    <th class="text-center vcenter">@lang('label.WT')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $obsnAlign = !empty($assignedObsnInfo->obsn) ? 'right' : 'center';
                                $mksLimitAlign = !empty($assignedObsnInfo->mks_limit) ? 'right' : 'center';
                                $limitPercentAlign = !empty($assignedObsnInfo->limit_percent) ? 'right' : 'center';
                                ?>
                                <tr>
                                    <td class="vcenter text-{{$mksLimitAlign}} width-80">{!! !empty($assignedObsnInfo->mks_limit) ? Helper::numberFormat2Digit($assignedObsnInfo->mks_limit) : '--' !!}</td>
                                    <td class="vcenter text-{{$limitPercentAlign}} width-80">{!! !empty($assignedObsnInfo->limit_percent) ? '&plusmn'.Helper::numberFormat2Digit($assignedObsnInfo->limit_percent).'%' : '--' !!}</td>
                                    <td class="vcenter text-{{$obsnAlign}} width-80">{!! !empty($assignedObsnInfo->obsn) ? Helper::numberFormat2Digit($assignedObsnInfo->obsn) : '--' !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-danger alert-dismissable">
                        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.OBSN_WT_IS_NOT_DISTRIBUTED_YET') !!}</strong></p>
                    </div>
                    @endif
                </div>
                <div class="col-md-12 margin-top-10" id="showCmMarkingList">
                    @if(!empty($assignedObsnInfo))
                    <div class="row">
                        @if(!empty($cmArr))
                        <div class="col-md-7 margin-top-10">
                            <span class="label label-md bold label-blue-steel">
                                @lang('label.TOTAL_NO_OF_CM'):&nbsp;{!! sizeof($cmArr) !!}
                            </span>&nbsp;
                            <!--                            <a class = "btn btn-sm bold label-green-seagreen tooltips" title="@lang('label.CLICK_HERE_TO_SEE_COURSE_MARKING_STATUS_SUMMARY')" type="button" href="#modalInfo" data-toggle="modal" id="courseStatusSummaryId">
                                                            @lang('label.COURSE_STATUS_SUMMARY')
                                                        </a>-->
                        </div>

                        <div class="col-md-2 text-right">
                            @if($ciObsnMarkingInfo->isEmpty())
                            @if(!$dsObsnMksInfo->isEmpty())
                            @if(empty($dsObsnLockInfo))
                            <button class="btn btn-sm btn-danger tooltips margin-top-10" type="button" id="buttonDelete" >
                                @lang('label.CLEAR_MARKING')
                            </button>
                            @endif        
                            @endif        
                            @endif 
                        </div>
                        @if(!empty($totalEventDsCount) && !empty($eventMksLock) && ($totalEventDsCount == $eventMksLock))
                        @if(!empty($prevActDeactInfo))
<!--                        <div class="col-md-2 margin-top-8">
                            <div class="md-checkbox vcenter">
                                {!! Form::checkbox('auto_fill',1,null,['id' => 'checkAutoFill', 'class'=> 'md-check auto-fill']) !!}
                                <label for="checkAutoFill">
                                    <span></span>
                                    <span class="check bold"></span>
                                    <span class="box bold"></span>
                                </label>
                                <span class="bold">@lang('label.PUT_TICK_TO_AUTO_FILL')</span>
                            </div>
                        </div>-->

                        <div class="col-md-3 text-right">
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
                            </div>
                        </div>
                        <div class="col-md-12 margin-top-10">
                            <div class="row">
                                @if(!empty($dsObsnLockInfo))
                                @if($dsObsnLockInfo['status'] == '1')
                                <div class="col-md-12 text-center">
                                    <button class="btn btn-circle label-purple-sharp request-for-unlock" type="button" id="buttonSubmitLock" data-target="#modalUnlockMessage" data-toggle="modal">
                                        <i class="fa fa-unlock"></i> @lang('label.REQUEST_FOR_UNLOCK')
                                    </button>
                                </div>
                                @elseif($dsObsnLockInfo['status'] == '2')
                                <div class="col-md-12">
                                    <div class="alert alert-danger alert-dismissable">
                                        <p><strong><i class="fa fa-unlock"></i> {!! __('label.REQUESTED_TO_CI_FOR_UNLOCK') !!}</strong></p>
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
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.ASSESSMENT_IS_NOT_ACTIVATED_YET_FOR_THIS_EVENT') !!}</strong></p>
                            </div>
                        </div>
                        @endif

                        @else
                        <div class="col-md-12 margin-top-10">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.MARKING_OF_ALL_EVENTS_ARE_NOT_FINISHED_YET') !!}</strong></p>
                            </div>
                        </div>
                        @endif
                        @else
                        <div class="col-md-12 margin-top-10">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_ASSIGNED_TO_THIS_COURSE') !!}</strong></p>
                            </div>
                        </div>
                        @endif
                    </div>

                    @endif
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


<script type="text/javascript">
    $(function () {

        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            timeOut: 1000,
            onclick: null
        };

        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == '0') {
                $('#showCmMarkingList').html('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('dsObsnMarking/showCmMarkingList')}}",
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
                url: "{{ URL::to('dsObsnMarking/filter')}}",
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
                    $('#checkAutoFill').prop('checked',false);
                    $('.cm-marking-list').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
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

        //table header fix
        $(".table-head-fixer-color").tableHeadFixer();

        $(document).on('keyup', '.given-mks', function () {
            var key = $(this).attr('data-key');
            var givenMks = parseFloat($(this).val());
            var highestMks = parseFloat($(this).attr('data-high'));
            var assignedWt = parseFloat($(this).attr('data-assigned-wt'));
            var mksLimit = parseFloat($(this).attr('data-mks-limit'));

//        var lowestMks = $("#lowestMksId").val().length;
//        alert(lowestMks);

            if (givenMks > highestMks) {
                swal({
                    title: '@lang("label.YOUR_GIVEN_MKS_EXCEEDED_FROM_HIGHEST_MKS")',

                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#dsObsn_" + key).val('');
                    $("#obsnWt_" + key).text('');
                    $("#obsnWt_Val_" + key).val('');
                    setTimeout(function () {
                        $("#dsObsn_" + key).focus();
                    }, 250);
                    return false;
                });
            } else {
                var wt = parseFloat((assignedWt / mksLimit) * givenMks).toFixed(3);
                var wtVal = parseFloat((assignedWt / mksLimit) * givenMks).toFixed(6);
                if (!isNaN(givenMks)) {
                    $("#obsnWt_" + key).text(wt);
                    $("#obsnWt_Val_" + key).val(wtVal);
                }
            }

        });

        $(document).on('blur', '.given-mks', function () {
            var key = $(this).attr('data-key');
            var givenMks = parseFloat($(this).val());
            var lowestMks = parseFloat($(this).attr('data-low'));

            if (givenMks < lowestMks) {
                swal({
                    title: '@lang("label.YOUR_GIVEN_MKS_GRATHER_THEN_LOWEST_MKS")',

                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $("#dsObsn_" + key).val('');
                    $("#obsnWt_" + key).text('');
                    $("#obsnWt_Val_" + key).val('');
                    setTimeout(function () {
                        $("#dsObsn_" + key).focus();
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
                        url: "{{URL::to('dsObsnMarking/saveObsnMarking')}}",
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
                    url: "{{URL::to('dsObsnMarking/saveObsnMarking')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    beforeSend: function () {
                        $('.button-submit').prop('disabled', true);
                        toastr.info("@lang('label.SAVING')", "", options);
//                            App.blockUI({boxed: true});
                    },
                    success: function (res) {
                        $('.button-submit').prop('disabled', false);
//                        toastr.success(res.message, res.heading, options);
//                            location.reload();
//                            App.unblockUI();
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
//                            App.unblockUI();
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
                        url: "{{URL::to('dsObsnMarking/clearMarking')}}",
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
                url: "{{URL::to('dsObsnMarking/getRequestForUnlockModal')}}",
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
                        url: "{{URL::to('dsObsnMarking/saveRequestForUnlock')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res, '@lang("label.REQUEST_FOR_UNLOCK_HAS_BEEN_SENT_TO_COMDT_SUCCESSFULLY")', options);
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
            var termId = $("#termId").val();
            $.ajax({
                url: "{{URL::to('dsObsnMarking/requestCourseSatatusSummary')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
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
                url: "{{URL::to('dsObsnMarking/getDsMarkingSummary')}}",
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

        $(document).on('click', '.auto-fill', function (e) {
            $('.event-percent').each(function () {
                var cmId = $(this).data('key');
                var mksLimit = $(this).data('mks-limit');
                var assignedWt = $(this).data('assigned-wt');
                var eventPercent = $(this).val();

                var wt = parseFloat((assignedWt / mksLimit) * eventPercent).toFixed(3);
                var wtVal = parseFloat((assignedWt / mksLimit) * eventPercent).toFixed(6);

                if ($(".auto-fill").prop("checked") == true) {
                    if (!isNaN(eventPercent)) {
                        $("#obsnWt_" + cmId).text(wt);
                        $("#obsnWt_Val_" + cmId).val(wtVal);
                        $("#dsObsn_" + cmId).val(parseFloat(eventPercent).toFixed(3));
                    }
                } else {
                    $("#obsnWt_" + cmId).text('');
                    $("#obsnWt_Val_" + cmId).val('');
                    $("#dsObsn_" + cmId).val('');
                }
            });
        });

    });

</script>
@stop