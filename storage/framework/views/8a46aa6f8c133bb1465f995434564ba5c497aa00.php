
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.RELATE_CM_GROUP_TO_COURSE'); ?>
            </div>
        </div>

        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')); ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?>:</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYear->name); ?> </strong></div>
                                    <?php echo Form::hidden('training_year_id', $activeTrainingYear->id, ['id' => 'trainingYearId']); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?>:</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> <?php echo e($activeCourse->name); ?> </strong></div>
                                    <?php echo Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']); ?>

                                </div>
                            </div>
                        </div>

                    </div>
                    <!--get Cm Group data-->
                    <div id="showCmGroup">
                        <?php if(!$targetArr->isEmpty()): ?>
                        <div class = "form-group">
                            <label class = "control-label col-md-4" for = "moduleId"><?php echo app('translator')->get('label.CHOOSE_CM_GROUP'); ?> :<span class = "text-danger"> *</span></label>
                            <div class = "col-md-4 margin-top-8">
                                <?php
                                //disable
                                $disabledCAll = '';
                                if (!empty($cmGroupTemplateDataArr)) {
                                    $disabledCAll = 'disabled';
                                }
                                ?>
                                <div class="md-checkbox">
                                    <input type="checkbox" id="checkedAll" class="md-check" <?php echo e($disabledCAll); ?>>
                                    <label for="checkedAll">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="bold"><?php echo app('translator')->get('label.CHECK_ALL'); ?></span>
                                </div>
                                <div class="form-group form-md-line-input table-responsive max-height-300 webkit-scrollbar">
                                    <div class="col-md-10">
                                        <div class="md-checkbox-list">
                                            <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                            //disable
                                            $disabled = '';
                                            if (!empty($cmGroupTemplateDataArr)) {
                                                $disabled = in_array($item->id, $cmGroupTemplateDataArr) ? 'disabled' : '';
                                            }
                                            $checked = '';
                                            $title = __('label.CHECK');
                                            if (!empty($previousDataList[$item->id])) {
                                                if (in_array($previousDataList[$item->id], $previousDataList)) {
                                                    $checked = 'checked';
                                                    $groupNmae = $item->name;
                                                    if (in_array($item->id, $cmGroupTemplateDataArr)) {
                                                        $title = __('label.CM_IS_ALREADY_ASSIGNED_TO_SYN', ['synName' => $groupNmae]);
                                                    }  else {
                                                        $title = __('label.UNCHECK');
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="md-checkbox">
                                                <?php echo Form::checkbox('cm_group_id['.$item->id.']',$item->id, false, ['id' => $item->id, 'data-id'=>$item->id,'class'=> 'md-check cm-group-to-course-check', $checked,$disabled]); ?>

                                                <?php if(!empty($disableCmGroup)): ?>
                                                <?php $__currentLoopData = $disableCmGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php echo Form::hidden('cm_group_id['.$key.']', $key); ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                                <?php if(!empty($disabled)): ?>
                                                <?php echo Form::hidden('cm_group_id['.$item->id.']', $item->id); ?>

                                                <?php endif; ?>
                                                <span class = "text-danger"><?php echo e($errors->first('cm_group_id')); ?></span>
                                                <label for="<?php echo e($item->id); ?>">
                                                    <span></span>
                                                    <span class="check tooltips" title="<?php echo e($title); ?>"></span>
                                                    <span class="box tooltips" title="Checked"></span><?php echo e($item->name); ?>

                                                </label>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class = "form-actions">
                            <div class = "col-md-offset-4 col-md-8">
                                <button class = "button-submit btn btn-circle green" type="button">
                                    <i class = "fa fa-check"></i> <?php echo app('translator')->get('label.SUBMIT'); ?>
                                </button>
                                <a href = "<?php echo e(URL::to('cmGroupToCourse')); ?>" class = "btn btn-circle btn-outline grey-salsa"><?php echo app('translator')->get('label.CANCEL'); ?></a>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><i class="fa fa-bell-o fa-fw"></i><?php echo app('translator')->get('label.NO_CM_GROUP_FOUND'); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>

</div>
<script src="<?php echo e(asset('public/js/custom.js')); ?>" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
    //get module
//        $(document).on("change", "#courseId", function () {
//            var trainingYearId = $("#trainingYearId").val();
//            var courseId = $("#courseId").val();
//
//            if (courseId === '0') {
//                $('#showCmGroup').html('');
//                return false;
//            }
//            $.ajax({
//                url: "<?php echo e(URL::to('cmGroupToCourse/getCmGroup')); ?>",
//                type: "POST",
//                dataType: "json",
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                data: {
//                    course_id: courseId,
//                    training_year_id: trainingYearId
//                },
//                beforeSend: function () {
//                    App.blockUI({boxed: true});
//                },
//                success: function (res) {
//                    $('#showCmGroup').html(res.html);
//                    $('.tooltips').tooltip();
//                    $(".js-source-states").select2();
//                    App.unblockUI();
//                },
//                error: function (jqXhr, ajaxOptions, thrownError) {
////                    App.unblockUI();
//                }
//            });//ajax
//            App.unblockUI();
//        });

    $(document).on('click', '.button-submit', function (e) {
        e.preventDefault();
        var form_data = new FormData($('#submitForm')[0]);

        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        swal({
            title: 'Are you sure?',
               
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, Save',
            cancelButtonText: 'No, Cancel',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "<?php echo e(URL::to('cmGroupToCourse/saveCmGroup')); ?>",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (res) {
                        toastr.success(res, "<?php echo app('translator')->get('label.CM_GROUP_HAS_BEEN_RELATED_TO_THIS_COURSE'); ?>", options);
//                            $(document).trigger("change", "#courseId");
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
                            toastr.error('Error', 'Something went wrong', options);
                        }
                        App.unblockUI();
                    }
                });
            }

        });

    });
});
//    CHECK ALL
$(document).ready(function () {
    // this code for  database 'check all' if all checkbox items are checked
    if ($('.cm-group-to-course-check:checked').length == $('.cm-group-to-course-check').length) {
        $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
    }

    $("#checkedAll").change(function () {
        if (this.checked) {
            $(".md-check").each(function () {
                if (!this.hasAttribute("disabled")) {
                    this.checked = true;
                }
            });
        } else {
            $(".md-check").each(function () {
                this.checked = false;
            });
        }
    });

    $('.cm-group-to-course-check').change(function () {
        if (this.checked == false) { //if this item is unchecked
            $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
        }

        //check 'check all' if all checkbox items are checked
        if ($('.cm-group-to-course-check:checked').length == $('.cm-group-to-course-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        }
    });

});
//    CHECK ALL
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/cmGroupToCourse/index.blade.php ENDPATH**/ ?>