
<div class="col-md-12">
    <div class="row">
        <?php if(!$targetArr->isEmpty()): ?>
        <?php if(!empty($apptList)): ?>

        <div class="col-md-12 margin-top-10">
            <span class="label label-success">
                <?php echo app('translator')->get('label.TOTAL_NO_OF_CM'); ?>:&nbsp;<?php echo !empty($targetArr)?sizeof($targetArr):0; ?>

            </span>&nbsp;
            <button class="label label-primary btn-label-groove tooltips" href="#modalAssignedAppt" id="assignedAppt" 
                    data-course-id="<?php echo !empty($request->course_id) ? $request->course_id : 0; ?>" 
                    data-term-id="<?php echo !empty($request->term_id) ? $request->term_id : 0; ?>" 
                    data-event-id="<?php echo !empty($request->event_id) ? $request->event_id : 0; ?>" 
                    data-sub-event-id="<?php echo !empty($request->sub_event_id) ? $request->sub_event_id : 0; ?>" 
                    data-sub-sub-event-id="<?php echo !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0; ?>" 
                    data-sub-sub-sub-event-id="<?php echo !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0; ?>" 
                    data-toggle="modal" title="<?php echo app('translator')->get('label.SHOW_ASSIGNED_APPT'); ?>">
                <?php echo app('translator')->get('label.TOTAL_NO_OF_ASSIGNED_APPT'); ?>: &nbsp;<?php echo !empty($apptList)?sizeof($apptList):0; ?>&nbsp; <i class="fa fa-search-plus"></i>
            </button>
        </div>

        <div class="col-md-12 margin-top-10">
            <div class="table-responsive webkit-scrollbar">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th class="vcenter text-center"><?php echo app('translator')->get('label.SL_NO'); ?></th>

<!--                        <th class="vcenter" width="15%">
    <div class="md-checkbox has-success">
        <?php echo Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check']); ?>

        <label for="checkAll">
            <span class="inc"></span>
            <span class="check mark-caheck"></span>
            <span class="box mark-caheck"></span>
        </label>&nbsp;&nbsp;
        <span class="bold"><?php echo app('translator')->get('label.CHECK_ALL'); ?></span>
    </div>
</th>-->

                            <th class="text-center vcenter"><?php echo app('translator')->get('label.PHOTO'); ?></th>
                            <th class=" vcenter"><?php echo app('translator')->get('label.PERSONAL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.RANK'); ?></th>
                            <th class=" vcenter"><?php echo app('translator')->get('label.FULL_NAME'); ?></th>
                            <th class=" vcenter"><?php echo app('translator')->get('label.ASSIGNED_SYN'); ?></th>
                            <th class=" vcenter"><?php echo app('translator')->get('label.APPT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php $sl = 0; ?>
                        <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>


                        <?php
                        $checked = '';
                        $selectedAppt = null;
                        $disabled = 'disabled';
                        $title = __('label.CHECK');
                        if (!empty($previousDataList) && array_key_exists($target->id, $previousDataList)) {
                            $selectedAppt = $previousDataList[$target->id];
                            $checked = 'checked';
                            $disabled = '';
                            $title = __('label.UNCHECK');
                        }
                        ?>

                        <tr>
                            <td class="vcenter text-center"><?php echo ++$sl; ?></td>
    <!--                        <td class="vcenter">
                                <div class="md-checkbox has-success tooltips" title="" >
                                    <?php echo Form::checkbox('cm_id['.$target->id.']',$target->id, $checked, ['id' => $target->id, 'data-id'=>$target->id, 'class'=> 'md-check appt-to-cm']); ?>

                                    <label for="<?php echo $target->id; ?>">
                                        <span class="inc"></span>
                                        <span class="check mark-caheck tooltips" title="<?php echo e($title); ?>"></span>
                                        <span class="box mark-caheck tooltips" title="<?php echo e($title); ?>"></span>
                                    </label>
                                </div>
                            </td>-->
                            <td class="text-center vcenter" width="50px">
                                <?php if (!empty($target->photo && File::exists('public/uploads/cm/' . $target->photo))) { ?>
                                    <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($target->photo); ?>" alt="<?php echo e(Common::getFurnishedCmName($target->full_name)); ?>"/>
                                <?php } else { ?>
                                    <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo e(Common::getFurnishedCmName($target->full_name)); ?>"/>
                                <?php } ?>
                            </td>
                            <td class="vcenter"><?php echo $target->personal_no; ?></td>
                            <td class="vcenter"><?php echo !empty($target->rank_code) ? $target->rank_code : ''; ?> </td>
                            <td class="vcenter"><?php echo Common::getFurnishedCmName($target->full_name); ?></td>
                            <td class="vcenter"><?php echo !empty($target->syn_name) ? $target->syn_name : ''; ?></td>
                            <?php echo Form::hidden('syn_id['.$target->id.']', $target->syn_id); ?>

                            <td class="vcenter width-200">
                                <select class="form-control width-inherit js-source-states  appt-select appt-select-syn-<?php echo e($target->syn_id); ?>  appt-select-<?php echo e($target->id); ?>" 
                                        name="appt_id[<?php echo $target->id; ?>]" id="apptId_<?php echo $target->id; ?>" data-syn-id="<?php echo $target->syn_id; ?>" data-id="<?php echo e($target->id); ?>">
                                    <?php $__currentLoopData = $apptList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $apptId => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $optionDisabled = !empty($previousDataOptionDisabledList[$target->syn_id][$target->id]) && in_array($apptId, $previousDataOptionDisabledList[$target->syn_id][$target->id]) ? 'disabled' : ''; ?>
                                    <option value="<?php echo $apptId; ?>" id="<?php echo $target->id.'_'.$apptId; ?>" <?php echo e($optionDisabled); ?>

                                            data-cm-id="<?php echo $target->id; ?>"  data-option-id="<?php echo $apptId; ?>" 
                                            data-unique="<?php echo $info['is_unique']; ?>"  data-syn-id="<?php echo $target->syn_id; ?>"
                                            <?php
                                            if ($selectedAppt == $apptId) {
                                                echo 'selected="selected"';
                                            } else {
                                                echo '';
                                            }
                                            ?>><?php echo $info['name']; ?> 
                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>
                </table>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                            <i class="fa fa-check"></i> <?php echo app('translator')->get('label.SUBMIT'); ?>
                        </button>
                        <a href="<?php echo e(URL::to('apptToCm')); ?>" class="btn btn-circle btn-outline grey-salsa"><?php echo app('translator')->get('label.CANCEL'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.APPT_MATRIX_IS_NOT_SET_YET'); ?></strong></p>
            </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_CM_IS_ASSIGNED_TO_THIS_GROUP'); ?></strong></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- Modal end-->
<script type="text/javascript">
    //    CHECK ALL
    $(document).ready(function () {

<?php
if (!$targetArr->isEmpty()) {
    if (!empty($apptList)) {
        ?>

                //            $('#dataTable').dataTable({
                //                "paging": true,
                //                "info": false,
                //                "order": false
                //            });

                //                $('#checkAll').change(function () {  //'check all' change
                //                    $('.appt-to-cm').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                //                    $('.appt-select').prop('disabled', !$(this).prop('checked')); //change all 'checkbox' checked status
                //                });
                //
                //                $('.appt-to-cm').change(function () {
                //                    var key = $(this).attr('data-id');
                //                    if (this.checked == false) { //if this item is unchecked
                //                        $('#checkAll').prop('checked', false); //change 'check all' checked status to false
                //                        $('.appt-select-' + key).prop('disabled', true);
                //                    } else {
                //                        $('.appt-select-' + key).prop('disabled', false);
                //                    }
                //                    //check 'check all' if all checkbox items are checked
                //                    allCheck();
                //                });
                //                allCheck();

                $(document).on('change', '.appt-select', function () {
                    var selections = [];
                    var synId = $(this).attr('data-syn-id');

                    $('select.appt-select-syn-' + synId + ' option:selected').each(function () {
                        var optionId = $(this).attr('data-option-id');
                        selections.push(optionId);
                    });

                    $('select.appt-select-syn-' + synId + ' option').each(function () {
                        var isUnique = $(this).attr('data-unique');
                        $(this).prop('disabled', $.inArray($(this).val(), selections) > -1 && !$(this).is(":selected") && isUnique == '1');

                    });
                    $('select.js-source-states').select2();


                });
        <?php
    }
}
?>
    });


    function allCheck() {

        if ($('.appt-to-cm:checked').length == $('.appt-to-cm').length) {
            $('#checkAll').prop('checked', true); //change 'check all' checked status to true
        } else {
            $('#checkAll').prop('checked', false);
        }
    }

</script>
<script src="<?php echo e(asset('public/js/custom.js')); ?>" type="text/javascript"></script>

<?php /**PATH C:\xampp\htdocs\afwc\resources\views/apptToCm/showCmAppt.blade.php ENDPATH**/ ?>