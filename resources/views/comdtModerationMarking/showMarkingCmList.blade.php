<div class="row">
    @if(!empty($comdtMksInfo))
    <div class="col-md-12 margin-top-10">
        <span class="label label-md bold label-blue-steel">
            @lang('label.TOTAL_NO_OF_CM'): {!! sizeof($cmArr) !!}
        </span>&nbsp;
        {!! Form::hidden('comdt_mod', !empty($comdtMksInfo->comdt_mod) ? $comdtMksInfo->comdt_mod : null,['id' => 'comdtMod']) !!}

        <button class="label label-sm label-green-seagreen btn-label-groove tooltips" type="button" id="buttonDsMarkinSummary" data-target="#modalDsMarkingSummary" data-toggle="modal" title="@lang('label.SHOW_DS_MARKING_SUMMARY')">
            @lang('label.DS_MARKING_SUMMARY')
        </button>
    </div>

    @if((!empty($totalCiMarkingList)) && (!empty($totalCiLockList)) && (sizeof($totalCiMarkingList) == sizeof($totalCiLockList)))
    <div class="col-md-12 margin-top-10">
        <div class="max-height-500 table-responsive webkit-scrollbar">
            <table class="table table-bordered table-hover table-head-fixer-color">
                <?php
                $eventMkslimit = !empty($assingedMksWtInfo['mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['mks_limit']) : '0.00';
                $eventHighestMkslimit = !empty($assingedMksWtInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['highest_mks_limit']) : '0.00';
                $eventLowestMkslimit = !empty($assingedMksWtInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['lowest_mks_limit']) : '0.00';
                $eventWt = !empty($assingedMksWtInfo['wt']) ? Helper::numberFormat2Digit($assingedMksWtInfo['wt']) : '0.00';
                ?>
                <thead>

                    <tr>
                        <th class="text-center vcenter" rowspan="3">@lang('label.SL_NO')</th>
                        <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                        <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                        <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                        <th class="vcenter" rowspan="3">@lang('label.PHOTO')</th>
                        <!--<th class="vcenter" rowspan="3">@lang('label.SYN')</th>-->
                        <th class="text-center vcenter" colspan="{{ (!empty($dsDataList) ? sizeof($dsDataList) : 1)*4 }}">@lang('label.DS_MARKING')</th>
                        <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AVERAGE')</th>
                        <th class="text-center vcenter" rowspan="3">@lang('label.CI_MODERATION')</th>
                        <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AFTER_CI_MODERATION')</th>
                        <th class="text-center vcenter" rowspan="3">@lang('label.COMDT_MODERATION')</th>
                        <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AFTER_COMDT_MODERATION')</th>
                        <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                        <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                        <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                        <th class="vcenter" rowspan="3">@lang('label.PHOTO')</th>
                        <!--<th class="vcenter" rowspan="3">@lang('label.SYN')</th>-->
                    </tr>
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
                        <th class="text-center vcenter" colspan="4">
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
                                @lang('label.MKS')&nbsp;({!! $eventMkslimit !!})
                            </span>
                        </th>
                        <th class="text-center vcenter">@lang('label.WT') ({!! !empty($assingedMksWtInfo->wt) ? $assingedMksWtInfo->wt : '0.00' !!})</th>
                        <th class="text-center vcenter">@lang('label.PERCENT') </th>
                        <th class="text-center vcenter">@lang('label.GRADE') </th>
                        @endforeach
                        @endif

                        <!--Average-->
                        <th class="vcenter text-center">
                            @lang('label.MKS')&nbsp;({!! $eventMkslimit !!})
                        </th>
                        <th class="text-center vcenter">@lang('label.WT') ({!! !empty($assingedMksWtInfo->wt) ? $assingedMksWtInfo->wt : '0.00' !!})</th>
                        <th class="text-center vcenter">@lang('label.PERCENT') </th>
                        <th class="text-center vcenter">@lang('label.GRADE') </th>

                        <!--After CI Moderation-->
                        <th class="vcenter text-center">
                            @lang('label.MKS')&nbsp;({!! $eventMkslimit !!})
                        </th>
                        {!! Form::hidden('mks_limit', !empty($assingedMksWtInfo->mks_limit) ? $assingedMksWtInfo->mks_limit : '',['id' => 'mksLimitId']) !!}
                        <th class="text-center vcenter">@lang('label.WT') ({!! !empty($assingedMksWtInfo->wt) ? $assingedMksWtInfo->wt : '0.00' !!})</th>
                        {!! Form::hidden('assigned_wt', !empty($assingedMksWtInfo->wt) ? $assingedMksWtInfo->wt : '',['id' => 'assignedWtId']) !!}
                        <th class="text-center vcenter">@lang('label.PERCENT') </th>
                        <th class="text-center vcenter">@lang('label.GRADE') </th>

                        <!--After Comdt Moderation-->
                        <th class="vcenter text-center">
                            @lang('label.MKS')&nbsp;({!! $eventMkslimit !!})
                        </th>
                        <th class="text-center vcenter">@lang('label.WT') ({!! !empty($assingedMksWtInfo->wt) ? $assingedMksWtInfo->wt : '0.00' !!})</th>
                        <th class="text-center vcenter">@lang('label.PERCENT') </th>
                        <th class="text-center vcenter">@lang('label.GRADE') </th>
                    </tr>
                </thead>

                <tbody>
                    <?php $sl = 0; ?>
                    @foreach($cmArr as $cmId => $cmInfo)
                    <?php
                    $readonly = !empty($comdtModerationMarkingLockInfo) ? 'readonly' : '';
                    $givenMod = !empty($comdtModerationMarkingLockInfo) ? '' : 'given-moderation';
                    $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');

//                    $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');

                    $avgDsMark = !empty($ciMksWtArr[$cmId]['mks']) ? $ciMksWtArr[$cmId]['mks'] : 0;
                    $modLimit = !empty($comdtMksInfo->comdt_mod) ? $comdtMksInfo->comdt_mod : 0;
                    $modMark = Helper::numberFormatDigit2(($avgDsMark * $modLimit) / 100);
                    $title = __('label.RECOMMENDED_MAX_MIN_VALUE', ['mod_mark' => $modMark]);
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
                        </td>
                        {!! Form::hidden('cm_name['.$cmId.']',Common::getFurnishedCmName($cmName),['id' => 'cmId'])!!}
                        <td class="vcenter" width="50px">
                            @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                            <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{{ Common::getFurnishedCmName($cmInfo['full_name']) }}">
                            @else
                            <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($cmInfo['full_name']) }}">
                            @endif
                        </td>
                        <!--DS Marking-->
                        @if(!empty($dsDataList))
                        @foreach($dsDataList as $dsId => $dsInfo)
                        <td class="text-center vcenter width-80">
                            <span id="dsMks_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? $dsMksWtArr[$dsId][$cmId]['mks'] : '' !!}
                            </span>
                        </td>
                        <td class="text-center vcenter width-80">
                            <span id="dsWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($dsMksWtArr[$dsId][$cmId]['wt']) ? $dsMksWtArr[$dsId][$cmId]['wt'] : '' !!}
                            </span>
                        </td>
                        <td class="text-center vcenter width-80">
                            <span id="dsPercentage_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($dsMksWtArr[$dsId][$cmId]['percentage']) ? $dsMksWtArr[$dsId][$cmId]['percentage'] : '' !!}
                            </span>
                        </td>
                        <td class="text-center vcenter width-50">
                            <span id="dsGradeName_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($dsMksWtArr[$dsId][$cmId]['grade_name']) ? $dsMksWtArr[$dsId][$cmId]['grade_name'] : '' !!}
                            </span>
                        </td>
                        @endforeach
                        @endif

                        <!--Average-->
                        <td class="text-center vcenter width-80">
                            <span id="averageMks_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($avgDsMksWtArr['mks'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['mks'][$cmId]) : '' !!}
                            </span>
                        </td>
                        <td class="text-center vcenter width-80">
                            <span id="avgWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($avgDsMksWtArr['wt'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['wt'][$cmId]) : '' !!}
                            </span>
                        </td>
                        <td class="text-center vcenter width-80">
                            <span id="avgWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($avgDsMksWtArr['percentage'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['percentage'][$cmId]) : '' !!}
                            </span>
                        </td>
                        <td class="text-center vcenter width-50">
                            <span id="avgGradeName_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($avgDsMksWtArr['grade'][$cmId]) ? $avgDsMksWtArr['grade'][$cmId] : '' !!}
                            </span>
                        </td>

                        <!--CI Moderation-->
                        <td class="text-center vcenter width-80">
                            <span id="ciModeration_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($ciMksWtArr[$cmId]['ci_moderation']) ? $ciMksWtArr[$cmId]['ci_moderation'] : '' !!}
                            </span>
                        </td>


                        <!--After CI Moderation-->
                        <td class="text-center vcenter width-80">
                            <span id="ciModMks_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($ciMksWtArr[$cmId]['mks']) ? $ciMksWtArr[$cmId]['mks'] : '' !!}
                            </span>
                        </td>
                        {!! Form::hidden('ci_mks['.$cmId.']', !empty($ciMksWtArr[$cmId]['mks']) ? $ciMksWtArr[$cmId]['mks'] : null,['id' => 'ciMks_'.$cmId]) !!}
                        <td class="text-center vcenter width-80">
                            <span id="ciWt_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($ciMksWtArr[$cmId]['wt']) ? $ciMksWtArr[$cmId]['wt'] : '' !!}
                            </span>
                        </td>
                        <td class="text-center vcenter width-80">
                            <span id="ciPercentage_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($ciMksWtArr[$cmId]['percentage']) ? $ciMksWtArr[$cmId]['percentage'] : '' !!}
                            </span>
                        </td>
                        <td class="text-center vcenter width-50">
                            <span id="ciGradeName_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($ciMksWtArr[$cmId]['grade_name']) ? $ciMksWtArr[$cmId]['grade_name'] : '' !!}
                            </span>
                        </td>

                        <!--Comdt Moderation-->
                        <td class="text-center vcenter width-80">
                            {!! Form::text('mks_wt['.$cmId.'][moderation]',  !empty($prevMksWtArr[$cmId]['comdt_moderation']) ? $prevMksWtArr[$cmId]['comdt_moderation'] : null
                            , ['id'=> 'comdtModeration_'.$cmId, 'class' => 'form-control width-inherit text-right '.$givenMod.' tooltips', $readonly
                            , 'data-key' => $cmId, 'autocomplete' => 'off', 'title' => $title]) !!}
                        </td>
                        {!! Form::hidden('mks_wt['.$cmId.'][mod_mark]', $modMark) !!}

                        <!--After C Moderation-->
                        <td class="text-center vcenter width-80">
                            {!! Form::text('mks_wt['.$cmId.'][mks]', !empty($prevMksWtArr[$cmId]['mks']) ? $prevMksWtArr[$cmId]['mks'] : (!empty($ciMksWtArr[$cmId]['mks']) ? $ciMksWtArr[$cmId]['mks'] : null), ['id'=> 'mksId_'.$cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right given-mks', 'data-key' => $cmId, 'autocomplete' => 'off', 'readonly']) !!}
                        </td>
                        <td class="text-center vcenter width-80">
                            {!! Form::text('mks_wt['.$cmId.'][wt]', !empty($prevMksWtArr[$cmId]['wt']) ? $prevMksWtArr[$cmId]['wt'] : (!empty($ciMksWtArr[$cmId]['wt']) ? $ciMksWtArr[$cmId]['wt'] : null), ['id'=> 'wtId_'.$cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right given-wt', 'data-key' => $cmId, 'autocomplete' => 'off','readonly']) !!}
                        </td>
                        <td class="text-center vcenter width-80">
                            {!! Form::text('mks_wt['.$cmId.'][percent]', !empty($prevMksWtArr[$cmId]['percentage']) ? $prevMksWtArr[$cmId]['percentage'] : (!empty($ciMksWtArr[$cmId]['percentage']) ? $ciMksWtArr[$cmId]['percentage'] : null), ['id'=> 'percentId_'.$cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right given-percent', 'data-key' => $cmId, 'autocomplete' => 'off','readonly']) !!}
                        </td>
                        <td class="text-center vcenter width-50">
                            <span id="gradeName_{{$cmId}}" class="form-control integer-decimal-only width-inherit bold text-center">
                                {!! !empty($prevMksWtArr[$cmId]['grade_name']) ? $prevMksWtArr[$cmId]['grade_name'] : (!empty($ciMksWtArr[$cmId]['grade_name']) ? $ciMksWtArr[$cmId]['grade_name'] : '') !!}
                            </span>
                        </td>
                        {!! Form::hidden('mks_wt['.$cmId.'][grade_id]',!empty($prevMksWtArr[$cmId]['grade_id']) ? $prevMksWtArr[$cmId]['grade_id'] : (!empty($ciMksWtArr[$cmId]['grade_id']) ? $ciMksWtArr[$cmId]['grade_id'] : null),['id' => 'gradeId_'.$cmId]) !!}
                        <td class="vcenter width-80">
                            <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                        </td>
                        <td class="vcenter width-80">
                            <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                        </td>
                        <td class="vcenter width-150">
                            <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                        </td>
                        <td class="vcenter" width="50px">
                            @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                            <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{{ Common::getFurnishedCmName($cmInfo['full_name']) }}">
                            @else
                            <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($cmInfo['full_name']) }}">
                            @endif
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-12 margin-top-10">
        <div class="row">
            @if(!empty($comdtModerationMarkingLockInfo))
            @if($comdtModerationMarkingLockInfo['status'] == '1')
            <div class="col-md-12 text-center">
                <button class="btn btn-circle label-purple-sharp request-for-unlock" type="button" id="buttonSubmitLock" data-target="#modalUnlockMessage" data-toggle="modal">
                    <i class="fa fa-unlock"></i> @lang('label.REQUEST_FOR_UNLOCK')
                </button>
            </div>
            @elseif($comdtModerationMarkingLockInfo['status'] == '2')
            <div class="col-md-12">
                <div class="alert alert-danger alert-dismissable">
                    <p><strong><i class="fa fa-unlock"></i> {!! __('label.REQUESTED_TO_COMDT_FOR_UNLOCK') !!}</strong></p>
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
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.ALL_CI_MODERATION_HAS_NOT_BEEN_LOCKED_YET') !!}</strong></p>
        </div>
    </div>
    @endif
    @else
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.COMDT_MODERATION_MARKING_LIMIT_IS_NOT_DISTRIBUTED_YET') !!}</strong></p>
        </div>
    </div>
    @endif
</div>
<script src="{{asset('public/js/custom.js')}}"></script>
<script>
$(document).ready(function () {
//table header fix
    $(".table-head-fixer-color").tableHeadFixer();

    $(document).on('keyup', '.given-moderation', function () {
        var key = $(this).attr('data-key');
        var givenModeration = $("#comdtModeration_" + key).val();
        var ciMks = parseFloat($("#ciMks_" + key).val());
//        alert(ciMks); return false;
        var moderationLimit = parseFloat($("#comdtMod").val());
        var highestModeration = ((ciMks * moderationLimit) / 100).toFixed(2);
        var lowestModeration = (highestModeration * (-1)).toFixed(2);
        var assignedWt = parseFloat($("#assignedWtId").val());
        var mksLimit = parseFloat($("#mksLimitId").val());
        if (givenModeration == '') {
            givenModeration = 0;
        }

        givenModeration = parseFloat(givenModeration);
        if (givenModeration > highestModeration) {
            swal({
                title: '@lang("label.YOUR_GIVEN_MARK_EXCEEDED_HIGHEST_MODERATION_LIMIT")',
                   
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#comdtModeration_" + key).val('');
                $("#mksId_" + key).val('');
                $("#wtId_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeId_" + key).val('');
                setTimeout(function () {
                    $("#comdtModeration_" + key).focus();
                }, 250);
                return false;
            });
        } else if (givenModeration < lowestModeration) {
            swal({
                title: '@lang("label.YOUR_GIVEN_MARK_DECEEDED_LOWEST_MODERATION_LIMIT")',
                   
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#comdtModeration_" + key).val('');
                $("#mksId_" + key).val('');
                $("#wtId_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeId_" + key).val('');
                setTimeout(function () {
                    $("#comdtModeration_" + key).focus();
                }, 250);
                return false;
            });
        } else {
            var mks = (ciMks + givenModeration).toFixed(2);
            var wt = ((assignedWt / mksLimit) * mks).toFixed(2);
//        alert(wt); return false;
            var wtPercent = ((wt / assignedWt) * 100).toFixed(2);
            if (!isNaN(givenModeration)) {
                $("#mksId_" + key).val(mks);
                $("#wtId_" + key).val(wt);
                $("#percentId_" + key).val(wtPercent);
                $("#gradeName_" + key).text(findGradeName(gradeArr, wtPercent));
                $("#gradeId_" + key).val(findGradeId(gradeIdArr, wtPercent));
            } else {
                $("#mksId_" + key).val('');
                $("#wtId_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeName_" + key).text('');
                $("#gradeId_" + key).val('');
            }
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


