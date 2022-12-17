<?php $__env->startSection('data_count'); ?>
    <div class="col-md-12">
        <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-power-off"></i><?php echo app('translator')->get('label.ACTIVATE_GS_FEEDBACK_FOR_DS'); ?>
                </div>
            </div>

            <div class="portlet-body">
                <?php echo Form::open([
                    'group' => 'form',
                    'url' => '#',
                    'class' => 'form-horizontal',
                    'id' => 'assessmentActDeactForm',
                ]); ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?>:</label>
                                    <div class="col-md-7">
                                        <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYearInfo->name); ?>

                                            </strong></div>
                                        <?php echo Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?>:</label>
                                    <div class="col-md-7">
                                        <div class="control-label pull-left"> <strong> <?php echo e($courseList->name); ?> </strong>
                                        </div>
                                        <?php echo Form::hidden('course_id', $courseList->id, ['id' => 'courseId']); ?>

                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Event assessment summary -->
                        <div class="row margin-top-10">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="vcenter text-center"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.LESSON'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.SUBJECT'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.GS'); ?></th>
                                            <th class="vcenter text-center"><?php echo app('translator')->get('label.ACTIVATION_STATUS'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($targetArr)): ?>

                                            <?php $sl = 0; ?>
                                            <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="text-center"><?php echo ++$sl; ?></td>
                                                    <td class="vcenter text-left"><?php echo $target->lesson ?? ''; ?></td>
                                                    <td class="vcenter text-left"><?php echo $target->subject ?? ''; ?></td>
                                                    <td class="vcenter text-left"><?php echo $target->gs_name ?? ''; ?></td>
                                                    <td class="text-center">
                                                        <div class="width-160">
                                                            <?php echo Form::checkbox(
                                                                'act_deact_stat[' . $target->subject_id . '][' . $target->lesson_id . '][' . $target->gs_id . ']',
                                                                0,
                                                                !empty($statArr[$target->subject_id][$target->lesson_id][$target->gs_id]) && $statArr[$target->subject_id][$target->lesson_id][$target->gs_id] == '1' ? 1 : 0,
                                                                [
                                                                    'id' => 'actDeactStat_' . $target->subject_id . '_' . $target->lesson_id . '_' . $target->gs_id,
                                                                    'class' => 'make-switch act-deact-switch',
                                                                    'data-on-text' => __('label.ACTIVATE'),
                                                                    'data-off-text' => __('label.DEACTIVATE'),
                                                                    'criteria' => '1',
                                                                    'course-id' => $courseList->id,
                                                                    'lesson-id' => $target->lesson_id,
                                                                    'subject-id' => $target->subject_id,
                                                                    'gs-id' => $target->gs_id,
                                                                ],
                                                            ); ?>

                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10"><?php echo app('translator')->get('label.NO_LESSON_FOUND'); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(function() {
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $(".act-deact-switch").bootstrapSwitch({
                offColor: 'danger'
            });

            $(".table-head-fixer-color").tableHeadFixer();

            $('#dataTable').dataTable({
                "paging": true,
                "pageLength": 100,
                "info": false,
                "order": false
            });


            //deligate reports
            $(document).on('switchChange.bootstrapSwitch', '.act-deact-switch', function() {

                var courseId = $(this).attr('course-id');
                var status = this.checked == true ? '1' : '0';
                var lessonId = $(this).attr('lesson-id');
                var subjectId = $(this).attr('subject-id');
                var gsId = $(this).attr('gs-id');

                $.ajax({
                    url: "<?php echo e(URL::to('activateGsFeedbackForDs/setStat')); ?>",
                    type: "POST",
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId,
                        status: status,
                        lesson_id: lessonId,
                        subject_id: subjectId,
                        gs_id: gsId,
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        toastr.success(res.message, res.heading, options);
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 400) {
                            var errorsHtml = '';
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function(key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                        App.unblockUI();
                    }
                });

            });

        });
    </script>
    <script src="<?php echo e(asset('public/js/custom.js')); ?>" type="text/javascript"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/activateGsFeedbackForDs/index.blade.php ENDPATH**/ ?>