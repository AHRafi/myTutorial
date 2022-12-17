<?php if(!$targetArr->isEmpty()): ?>
    <div class="row">
        <div class="col-md-12">
            <span class="label label-sm label-blue-steel">
                <?php echo app('translator')->get('label.TOTAL_NO_OF_LESSON'); ?>:&nbsp;<?php echo !empty($targetArr) ? sizeOf($targetArr) : 0; ?>

            </span>&nbsp;
            <span class="label label-purple"><?php echo app('translator')->get('label.TOTAL_NO_OF_LESSON_ASSIGNED'); ?>:
                &nbsp;<?php echo !empty($count) ? $count : 0; ?>

            </span>&nbsp;

            <button class="label label-sm label-green-seagreen btn-label-groove tooltips" href="#modalAssignedLessen"
                id="assignedLesson" data-toggle="modal" title="<?php echo app('translator')->get('label.SHOW_LESSON_ASSIGNED_TO_THIS_GS'); ?>">
                <?php echo app('translator')->get('label.TOTAL_NO_OF_LESSON_ASSIGNED_TO_THIS_GS'); ?>:&nbsp;<?php echo !empty($count) ? $count : 0; ?>&nbsp; <i class="fa fa-search-plus"></i>
            </button>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th class="text-center vcenter " width="5%"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                        <th class="vcenter" width="10%">
                            <?php
                            //disable
                            $disabledCAll = '';
                            if (!empty($disableDataArr)) {
                                $disabledCAll = 'disabled';
                            }
                            ?>

                            <div class="md-checkbox has-success">
                                <?php echo Form::checkbox('check_all', 1, false, ['id' => 'checkAll', 'class' => 'md-check', $disabledCAll]); ?>

                                <label for="checkAll">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>&nbsp;&nbsp;
                                <span class="bold"><?php echo app('translator')->get('label.CHECK_ALL'); ?></span>
                            </div>
                        </th>
                        <th class="vcenter"><?php echo app('translator')->get('label.LESSON'); ?></th>
                        <th class="vcenter"><?php echo app('translator')->get('label.SUBJECT'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sl = 0; ?>

                    <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $disabled = '';
                        $checked = '';
                        $title = __('label.CHECK');
                        if (!empty($disableDataArr[$target->subject_id][$target->id])) {
                            $disabled = 'disabled';
                            $title = __('label.THIS_LESSON_IS_ALREADY_ASSIGNED_TO_GS', ['gs' => $disableDataArr[$target->subject_id][$target->id]]);
                        }


                        if (!empty($assignedLesson[$target->subject_id][$target->id])) {
                            $checked = 'checked';
                        }
                        ?>
                        <tr>
                            <td class="text-center vcenter"><?php echo ++$sl; ?></td>
                            <td class="vcenter">
                                <div class="md-checkbox has-success tooltips">
                                    <?php echo Form::checkbox('lesson[' . $target->subject_id . '][' . $target->id . ']', $target->id, $checked, [
                                        'id' => $target->id . '_' . $target->subject_id,
                                        'data-id' => $target->id,
                                        'class' => 'md-check gs-to-lesson',
                                        $disabled,
                                    ]); ?>


                                    <label for="<?php echo $target->id . '_' . $target->subject_id; ?>">
                                        <span class="inc"></span>
                                        <span class="check mark-caheck tooltips" title="<?php echo e($title); ?>"></span>
                                        <span class="box mark-caheck tooltips" title="<?php echo e($title); ?>"></span>
                                    </label>
                                </div>
                            </td>
                            <td class="vcenter"><?php echo $target->lesson ?? ''; ?></td>
                            <td class="vcenter"><?php echo $target->subject ?? ''; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- if submit wt chack Start -->
    <div class="form-actions">
        <div class="col-md-offset-4 col-md-8">
            <button class="button-submit btn btn-circle green" type="button">
                <i class="fa fa-check"></i> <?php echo app('translator')->get('label.SUBMIT'); ?>
            </button>
            <a href="<?php echo e(URL::to('gsToLesson')); ?>" class="btn btn-circle btn-outline grey-salsa"><?php echo app('translator')->get('label.CANCEL'); ?></a>
        </div>
    </div>
<?php else: ?>
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i><?php echo app('translator')->get('label.NO_LESSON_FOUND'); ?></p>
        </div>
    </div>
<?php endif; ?>
<!-- if submit wt chack End -->

<script type="text/javascript">
    //   Start: CHECK ALL
    $(document).ready(function() {

        <?php if (!$targetArr->isEmpty()) { ?>
        allCheck();
        $('#dataTable').dataTable({
            "paging": true,
            "pageLength": 100,
            "info": false,
            "order": false
        });
        <?php } ?>

        //'check all' change
        $(document).on('click', '#checkAll', function() {
            if ($('#checkAll').is(':checked')) {
                $('.gs-to-lesson').each(function() {
                    if (this.checked == false) {
                        var key = $(this).attr('data-id');
                        $(this).prop('checked', true);
                    }
                });
            } else {
                $(".gs-to-lesson").removeAttr('checked');
                $(".has-checked").attr('disabled', true);
                $(".has-checked").removeAttr('checked');
            }
        });

        $(document).on('click', '.gs-to-lesson', function() {
            allCheck();
        });

    });

    function allCheck() {

        if ($('.gs-to-lesson:checked').length == $('.gs-to-lesson').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
    // End:  CHECK ALL
</script>
<script src="<?php echo e(asset('public/js/custom.js')); ?>" type="text/javascript"></script>
<?php /**PATH C:\xampp\htdocs\afwc\resources\views/gsToLesson/getLesson.blade.php ENDPATH**/ ?>