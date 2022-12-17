<div class="row marking-cm-list">
    <div class="col-md-12">
        <?php if(!empty($assingedMksWtInfo)): ?>
        <?php if(!empty($cmArr)): ?>
        <?php if(!empty($prevActDeactInfo)): ?>
        <div class="col-md-12 margin-top-10">
            <div class="col-md-7">
                <span class="label label-md bold label-blue-steel">
                    <?php echo app('translator')->get('label.TOTAL_NO_OF_CM'); ?>: <?php echo sizeof($cmArr); ?>

                </span>&nbsp;
                <span class="label label-md bold label-yellow-saffron">
                    <?php echo app('translator')->get('label.HIGHEST_MKS_LIMIT'); ?>: <?php echo $assingedMksWtInfo->highest_mks_limit ?? '0.00'; ?>

                    <?php echo Form::hidden('highest_mks', !empty($assingedMksWtInfo->highest_mks_limit) ? $assingedMksWtInfo->highest_mks_limit : null,['id' => 'highestMksId']); ?>

                </span>&nbsp;
                <span class="label label-md bold label-purple-sharp">
                    <?php echo app('translator')->get('label.LOWEST_MKS_LIMIT'); ?>: <?php echo $assingedMksWtInfo->lowest_mks_limit ?? '0.00'; ?>

                    <?php echo Form::hidden('lowest_mks', !empty($assingedMksWtInfo->lowest_mks_limit) ? $assingedMksWtInfo->lowest_mks_limit : null, ['id' => 'lowestMksId']); ?>

                </span>&nbsp;
            </div>


            <div class="col-md-2 text-right">
                <?php if($ciModMarkingInfo->isEmpty() && $dsObsnMarkingInfo->isEmpty()): ?>
                <?php if(!$prevMksWtDataArr->isEmpty()): ?>
                <?php if(empty($eventAssessmentMarkingLockInfo)): ?>
                <button class="btn btn-sm btn-danger tooltips" type="button" id="buttonDelete" >
                    <?php echo app('translator')->get('label.CLEAR_MARKING'); ?>
                </button>
                <?php endif; ?>        
                <?php endif; ?>        
                <?php endif; ?> 
            </div>


            <div class="col-md-3 text-right">
                <div class="form-group">
                    <label class="col-md-4" for="sortBy"><?php echo app('translator')->get('label.SORT_BY'); ?> :</label>
                    <div class="col-md-8" for="sortBy">
                        <?php echo Form::select('sort', $sortByList, Request::get('sort_by'),['class' => 'form-control js-source-states','id'=>'sortBy']); ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 margin-top-5">
            <div class="max-height-500 table-responsive webkit-scrollbar">
                <table class="table table-bordered table-hover table-head-fixer-color">
                    <thead>
                        <tr>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.PERSONAL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.RANK'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.CM'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.PHOTO'); ?></th>
                            <!--<th class="vcenter"><?php echo app('translator')->get('label.SYN'); ?></th>-->
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.MKS'); ?> (<?php echo !empty($assingedMksWtInfo->mks_limit) ? $assingedMksWtInfo->mks_limit : '0.00'; ?>)</th>
                            <?php echo Form::hidden('mks_limit', !empty($assingedMksWtInfo->mks_limit) ? $assingedMksWtInfo->mks_limit : '',['id' => 'mksLimitId']); ?>

                            <th class="text-center vcenter"><?php echo app('translator')->get('label.WT'); ?> (<?php echo !empty($assingedMksWtInfo->wt) ? $assingedMksWtInfo->wt : '0.00'; ?>)</th>
                            <?php echo Form::hidden('assigned_wt', !empty($assingedMksWtInfo->wt) ? $assingedMksWtInfo->wt : '',['id' => 'assignedWtId']); ?>

                            <th class="text-center vcenter"><?php echo app('translator')->get('label.REMARKS'); ?> </th>
                            <!--<th class="text-center vcenter"><?php echo app('translator')->get('label.GRADE'); ?> </th>-->
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $sl = 0;
                        $readonly = !empty($eventAssessmentMarkingLockInfo) || !$ciModMarkingInfo->isEmpty() ? 'readonly' : '';
                        $givenMks = !empty($eventAssessmentMarkingLockInfo) || !$ciModMarkingInfo->isEmpty() ? '' : 'given-mks';
                        ?>
                        <?php echo Form::hidden('total_given_mks', $totalGivenMks , ['id' => 'totalGivenMks']); ?>

                        <?php $__currentLoopData = $cmArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cmId => $cmInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');
                        $cmRmksReadonly = !empty($prevMksWtArr[$cmId]['mks']) ? 'readonly' : '';
//                    $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');
                        ?>
                        <tr>
                            <td class="text-center vcenter witdh-50">
                                <div class="width-inherit"><?php echo ++$sl; ?></div>
                            </td>
                            <td class="vcenter width-80">
                                <div class="width-inherit"><?php echo $cmInfo['personal_no'] ?? ''; ?></div>
                            </td>
                            <td class="vcenter width-80">
                                <div class="width-inherit"><?php echo $cmInfo['rank_name'] ?? ''; ?></div>
                            </td>
                            <td class="vcenter width-350">
                                <div class="width-inherit"><?php echo Common::getFurnishedCmName($cmInfo['full_name']); ?></div>
                            </td>
                            <?php echo Form::hidden('cm_name['.$cmId.']',Common::getFurnishedCmName($cmName),['id' => 'cmId']); ?>

                            <td class="vcenter" width="50px">
                                <?php if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo'])): ?>
                                <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($cmInfo['photo']); ?>" alt="<?php echo e(Common::getFurnishedCmName($cmInfo['full_name'])); ?>">
                                <?php else: ?>
                                <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo e(Common::getFurnishedCmName($cmInfo['full_name'])); ?>">
                                <?php endif; ?>
                            </td>

                            <td class="text-center vcenter width-80">
                                <?php echo Form::text('mks_wt['.$cmId.'][mks]', !empty($prevMksWtArr[$cmId]['mks']) ? Helper::numberFormatDigit3($prevMksWtArr[$cmId]['mks']) : null, ['id'=> 'mksId_'.$cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right ' . $givenMks, 'data-key' => $cmId, 'autocomplete' => 'off',$readonly]); ?>

                            </td>
                            <td class="text-center vcenter width-80">
                                <span id="wtId_<?php echo e($cmId); ?>" class="form-control integer-decimal-only width-inherit bold text-center">
                                    <?php echo !empty($prevMksWtArr[$cmId]['wt']) ? Helper::numberFormatDigit3($prevMksWtArr[$cmId]['wt']) : null; ?>

                                </span>
                                <?php echo Form::hidden('mks_wt['.$cmId.'][wt]', !empty($prevMksWtArr[$cmId]['wt']) ? $prevMksWtArr[$cmId]['wt'] : null, ['id'=> 'wtId_Val_'.$cmId, 'class' => 'form-control integer-decimal-only width-inherit text-right given-wt', 'data-key' => $cmId, 'autocomplete' => 'off','readonly']); ?>

                            </td>
                            <td class="text-center vcenter width-80">
                                <?php echo Form::text('mks_wt['.$cmId.'][remarks]', !empty($prevMksWtArr[$cmId]['remarks']) ? $prevMksWtArr[$cmId]['remarks'] : null, ['id'=> 'remarksId_'.$cmId, 'class' => 'form-control width-inherit', 'data-key' => $cmId, 'autocomplete' => 'off',$readonly, $cmRmksReadonly]); ?>

                            </td>

<!--<td class="text-center vcenter width-80">-->
                            <?php echo Form::hidden('mks_wt['.$cmId.'][percent]', !empty($prevMksWtArr[$cmId]['percentage']) ? Helper::numberFormatDigit2($prevMksWtArr[$cmId]['percentage']) : null, ['id'=> 'percentId_'.$cmId, 'class' => 'integer-decimal-only given-percent', 'data-key' => $cmId, 'autocomplete' => 'off','readonly']); ?>

                            <!--</td>-->
    <!--                        <td class="text-center vcenter width-50">
                                <span id="gradeName_<?php echo e($cmId); ?>" class="form-control integer-decimal-only width-inherit bold text-center">
                                    <?php echo !empty($prevMksWtArr[$cmId]['grade_name']) ? $prevMksWtArr[$cmId]['grade_name'] : ''; ?>

                                </span>
                            </td>-->
                            <?php echo Form::hidden('mks_wt['.$cmId.'][grade_id]',!empty($prevMksWtArr[$cmId]['grade_id']) ? $prevMksWtArr[$cmId]['grade_id'] : null,['id' => 'gradeId_'.$cmId]); ?>

                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>
                </table>
            </div>
        </div>
        <?php if(empty($eventAssessmentMarkingLockInfo)): ?>
        <div class="col-md-12 common-rmks-block margin-top-10">
            <div class="form-group">
                <label class="control-label col-md-6 bold" for="commonRmks"><?php echo app('translator')->get('label.COMMON_RMKS_LABEL'); ?> </label>
                <div class="col-md-5">
                    <?php echo Form::text('common_rmks', null, ['id'=> 'commonRmks', 'class' => 'form-control'
                    , 'placeholder' => __('label.COMMON_RMKS_PLACEHOLDER'), 'autocomplete' => 'off']); ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="col-md-12  text-center margin-top-10">
            <?php if($ciModMarkingInfo->isEmpty() && $dsObsnMarkingInfo->isEmpty()): ?>
            <?php if(!empty($eventAssessmentMarkingLockInfo)): ?>
            <?php if($eventAssessmentMarkingLockInfo['status'] == '1'): ?>
            <button class="btn btn-circle label-purple-sharp request-for-unlock" type="button" id="buttonSubmitLock" data-target="#modalUnlockMessage" data-toggle="modal">
                <i class="fa fa-unlock"></i> <?php echo app('translator')->get('label.REQUEST_FOR_UNLOCK'); ?>
            </button>
            <?php elseif($eventAssessmentMarkingLockInfo['status'] == '2'): ?>
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-unlock"></i> <?php echo __('label.REQUESTED_TO_CI_FOR_UNLOCK'); ?></strong></p>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <button class="btn btn-circle blue-steel button-submit" data-id="1" type="button" id="buttonSubmit" >
                <i class="fa fa-file-text-o"></i> <?php echo app('translator')->get('label.SAVE_AS_DRAFT'); ?>
            </button>&nbsp;&nbsp;
            <button class="btn btn-circle green button-submit" data-id="2" type="button" id="buttonSubmitLock" >
                <i class="fa fa-lock"></i> <?php echo app('translator')->get('label.SAVE_LOCK'); ?>
            </button>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.ASSESSMENT_IS_NOT_ACTIVATED_YET_FOR_THIS_EVENT'); ?></strong></p>
            </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_CM_IS_ASSIGNED_TO_MARKING_GROUP_TO_DS'); ?></strong></p>
            </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.MKS_WT_IS_NOT_DISTRIBUTED_YET'); ?></strong></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script src="<?php echo e(asset('public/js/custom.js')); ?>"></script>
<script>
$(document).ready(function () {
    //table header fix
    $(".table-head-fixer-color").tableHeadFixer();

    if ($('#totalGivenMks').val() == 0) {
        $('.common-rmks-block').show();
    } else {
        $('.common-rmks-block').hide();
    }
    $(document).on('keyup', '.given-mks', function () {
        var key = $(this).attr('data-key');
        var givenMks = parseFloat($("#mksId_" + key).val());
        var $highestMks = parseFloat($("#highestMksId").val());
        var assignedWt = parseFloat($("#assignedWtId").val());
        var mksLimit = parseFloat($("#mksLimitId").val());

        var sum = 0;
        $('.given-mks').each(function () {
            var mks = $(this).val();
            mks = !isNaN(mks) ? mks : 0;
            sum += Number(mks);
        });

        $('#totalGivenMks').val(sum);
        if ($('#totalGivenMks').val() == 0) {
            $('.common-rmks-block').show();
        } else {
            $('.common-rmks-block').hide();
        }
        if (isNaN(givenMks)) {
            $("#remarksId_" + key).prop("readonly", false);
        } else {
            $("#remarksId_" + key).val('');
            $("#remarksId_" + key).prop("readonly", true);
        }

        if (givenMks > $highestMks) {
            swal({
                title: '<?php echo app('translator')->get("label.YOUR_GIVEN_MKS_EXCEEDED_FROM_HIGHEST_MKS"); ?>',
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#mksId_" + key).val('');
                $("#wtId_" + key).text('');
                $("#wtId_Val_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeId_" + key).val('');
                $("#gradeName_" + key).text('');
                $('#totalGivenMks').val(sum - givenMks);
                if ($('#totalGivenMks').val() == 0) {
                    $('.common-rmks-block').show();
                } else {
                    $('.common-rmks-block').hide();
                }
                setTimeout(function () {
                    $("#mksId_" + key).focus();
                }, 250);
                return false;
            });
        } else if (givenMks == 0) {
            swal({
                title: '<?php echo app('translator')->get("label.YOUR_GIVEN_MKS_MUST_NOT_BE_0"); ?>',
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#mksId_" + key).val('');
                $("#wtId_" + key).text('');
                $("#wtId_Val_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeId_" + key).val('');
                $("#gradeName_" + key).text('');
                $('#totalGivenMks').val(sum - givenMks);
                if ($('#totalGivenMks').val() == 0) {
                    $('.common-rmks-block').show();
                } else {
                    $('.common-rmks-block').hide();
                }
                setTimeout(function () {
                    $("#mksId_" + key).focus();
                }, 250);
                return false;
            });
        } else {
            var wt = parseFloat((assignedWt / mksLimit) * givenMks).toFixed(3);
            var wtVal = parseFloat((assignedWt / mksLimit) * givenMks).toFixed(6);
            var wtPercent = parseFloat((wt / assignedWt) * 100).toFixed(3);
            if (!isNaN(givenMks)) {
                $("#wtId_" + key).text(wt);
                $("#wtId_Val_" + key).val(wtVal);
                $("#percentId_" + key).val(wtPercent);
                $("#gradeName_" + key).text(findGradeName(gradeArr, wtPercent));
                $("#gradeId_" + key).val(findGradeId(gradeIdArr, wtPercent));
            } else {
                $("#wtId_" + key).text('');
                $("#wtId_Val_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeName_" + key).text('');
                $("#gradeId_" + key).val('');
            }
        }

    });

    $(document).on('blur', '.given-mks', function () {
        var key = $(this).attr('data-key');
        var givenMks = parseFloat($("#mksId_" + key).val());
        var lowestMks = parseFloat($("#lowestMksId").val());

        var sum = 0;
        $('.given-mks').each(function () {
            var mks = $(this).val();
            mks = !isNaN(mks) ? mks : 0;
            sum += Number(mks);
        });

        $('#totalGivenMks').val(sum);
        if ($('#totalGivenMks').val() == 0) {
            $('.common-rmks-block').show();
        } else {
            $('.common-rmks-block').hide();
        }

        if (isNaN(givenMks)) {
            $("#remarksId_" + key).prop("readonly", false);
        } else {
            $("#remarksId_" + key).val('');
            $("#remarksId_" + key).prop("readonly", true);
        }

        if (givenMks < lowestMks) {
            swal({
                title: '<?php echo app('translator')->get("label.YOUR_GIVEN_MKS_GRATHER_THEN_LOWEST_MKS"); ?>',

                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#mksId_" + key).val('');
                $("#wtId_" + key).text('');
                $("#wtId_Val_" + key).val('');
                $("#percentId_" + key).val('');
                $("#gradeId_" + key).val('');
                $("#gradeName_" + key).text('');
                $('#totalGivenMks').val(sum - givenMks);
                setTimeout(function () {
                    $("#mksId_" + key).focus();
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


<?php /**PATH C:\xampp\htdocs\afwc\resources\views/eventAssessmentMarking/showMarkingCmList.blade.php ENDPATH**/ ?>