<?php echo Form::open(['group' => 'form', 'url' => '#', 'class' => 'form-horizontal', 'id' => 'submitLessonForm']); ?>

<?php echo Form::hidden('subject_id', $subjectId, ['id' => 'subjectId']); ?>

<?php echo Form::hidden('course_id', $courseId, ['id' => 'courseId']); ?>


<div class="row margin-top-10">
    <div class="col-md-12">
        <span class="label label-success"><?php echo app('translator')->get('label.TOTAL_NUM_OF_LESSONS'); ?>: <?php echo !empty($lessonList) ? count($lessonList) : 0; ?></span>
        <span class="label label-purple total-related-lessons"><?php echo app('translator')->get('label.TOTAL_RELATED_LESSONS'); ?>: &nbsp;<?php echo !empty($prevAllLessonList) ? sizeof($prevAllLessonList) : 0; ?></span>

        <button class="label label-primary tooltips" href="#modalAssignedLesson" id="assignedLesson" data-toggle="modal"
                title="<?php echo app('translator')->get('label.CLICK_HERE_TO_VIEW_LESSONS_RELATED_TO_THIS_SUBJECT'); ?>">
                <!--<?php echo app('translator')->get('label.DS_ASSIGNED_TO_THIS_GROUP'); ?>: <?php echo !empty($previousDataList) ? count($previousDataList) : 0; ?>&nbsp; <i class="fa fa-search-plus"></i>-->
            <?php echo app('translator')->get('label.LESSONS_RELATED_TO_THIS_SUBJECT'); ?>: &nbsp;<?php echo !empty($prevlessonList) ? sizeof($prevlessonList) : 0; ?>&nbsp; <i class="fa fa-search-plus"></i>
        </button>
    </div>
</div>

<div class="row margin-top-10">
    <div class="col-md-12">
        <div class="table-responsive webkit-scrollbar">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr class="info">
                        <th class="vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                        <th class="vcenter">
                            <?php if(sizeof($lessonList) == 0): ?>
                            #
                            <?php elseif(sizeof($lessonList) >= 1): ?>
                            <div class="md-checkbox padding-left-10">
                                <?php echo Form::checkbox('lesson_check_all', 1, false, ['id' => 'lessonCheckAll', 'class' => 'md-check']); ?>

                                <label for="lessonCheckAll">
                                    <span class=""></span>
                                    <span class="check"></span>
                                    <span class="box"></span><?php echo app('translator')->get('label.CHECK_ALL'); ?>
                                </label>

                            </div>
                            <?php endif; ?>
                        </th>
                        <th class="vcenter"><?php echo app('translator')->get('label.LESSON'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($lessonList)): ?>
                    <?php $sl = 0; ?>
                    <?php $__currentLoopData = $lessonList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lessonId => $lessonName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $checkedLesson = '';
                    if (!empty($prevlessonList[$lessonId])) {
                        $checkedLesson = 'checked';
                    }
                    
                    $disabledLesson = '';
                    $disabledTitle = '';
                    if (!empty($prevAllLessonList[$lessonId]['subject_id']) && $prevAllLessonList[$lessonId]['subject_id'] != $subjectId) {
                        $disabledLesson = 'disabled';
                        $disabledTitle = __('label.THIS_LESSON_IS_ALREADY_RELATED_TO_SUBJECT', ['subject' => $prevAllLessonList[$lessonId]['subject'] ?? '']);
                    }
                    ?>
                    <tr>
                        <td class="vcenter" width="5%"><?php echo ++$sl; ?></td>
                        <td class="text-center vcenter" width="10%">
                            <div class="md-checkbox has-success tooltips" title="<?php echo $disabledTitle; ?>">
                                <?php echo Form::checkbox('lesson_id[' . $lessonId . ']', $lessonId, $checkedLesson, [
                                'id' => 'lessonId_' . $lessonId,
                                'data-id' => $lessonId,
                                'class' => 'md-check lesson-check',
                                $disabledLesson
                                ]); ?>

                                <label for="<?php echo 'lessonId_' . $lessonId; ?>">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                </label>
                            </div>
                        </td>
                        <td class="vcenter"><?php echo $lessonName; ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <?php echo app('translator')->get('label.NO_LESSON_FOUND'); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!empty($lessonList)): ?>
<div class="form-actions margin-top-10">
    <div class="row">
        <div class="col-md-offset-5 col-md-12">

            <button class="button-submit btn btn-circle green" id="lessonBtn" type="button">
                <i class="fa fa-check"></i> <?php echo app('translator')->get('label.SUBMIT'); ?>
            </button>

            <a href="<?php echo e(URL::to('subjectToLesson')); ?>"
               class="btn btn-circle btn-outline grey-salsa"><?php echo app('translator')->get('label.CANCEL'); ?></a>

        </div>
    </div>
</div>
<?php endif; ?>
<?php echo Form::close(); ?>

<div class="modal fade" id="modalAssignedLesson" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="placeAssignedLesson">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#dataTable').dataTable({
            "paging": true,
            "pageLength": 100,
            "info": false,
            "order": false
        });

        $("#lessonCheckAll").change(function () {
            if (this.checked) {
                $(".lesson-check").each(function () {
                    this.checked = true;
                });
            } else {
                $(".lesson-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.lesson-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#lessonCheckAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.lesson-check:checked').length == $('.lesson-check').length) {
                $('#lessonCheckAll')[0].checked = true; //change 'check all' checked status to true
            }
        });


    });
</script>
<?php /**PATH C:\xampp\htdocs\afwc\resources\views/subjectToLesson/getLessonList.blade.php ENDPATH**/ ?>