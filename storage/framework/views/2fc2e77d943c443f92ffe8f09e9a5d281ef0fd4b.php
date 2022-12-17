 
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.MA_PROCESS'); ?>
            </div>

            <div class="actions">
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
                                    <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYearInfo->name); ?> </strong></div>
                                    <?php echo Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']); ?>

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

                    <!--get module data-->
                    <div id="showMarkingLimit">
                        <div class="row margin-top-10">
                            <div class="col-md-8 table-responsive webkit-scrollbar col-md-offset-2">
                                <table class="table table-bordered" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.TERM'); ?></th>
                                            <th class="text-center vcenter"><?php echo app('translator')->get('label.PROCESS'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!$termArr->isEmpty()): ?>
                                        <?php $sl = 0; ?>
                                        <?php $__currentLoopData = $termArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $process = !empty($prevDataArr[$term->id]['process']) ? $prevDataArr[$term->id]['process'] : null;
                                        ?>
                                        <tr>
                                            <td class="text-center vcenter width-50"><?php echo ++$sl; ?></td>
                                            <td class="vcenter width-250">
                                                <div class="width-inherit"><?php echo $term->name ?? ''; ?></div>
                                            </td>
                                            <td class="text-center vcenter width-150">
                                                <?php echo Form::select('process['.$term->id.'][type]', $processList, $process, ['id'=> 'type_' . $term->id, 'class' => 'form-control js-source-states width-inherit', 'data-term-id' => $term->id]); ?>

                                                    
                                            </td>
                                        </tr>
                                        <?php echo Form::hidden('process['.$term->id.'][term_name]', $term->name ?? '', ['id' => 'termName_' . $term->id]); ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="4"><?php echo app('translator')->get('label.NO_TERM_IS_ASSIGNED_TO_THIS_COURSE'); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if(!$termArr->isEmpty()): ?>
                        <div class = "form-actions margin-top-10">
                            <div class = "col-md-offset-2 col-md-8 text-center">
                                <button class = "button-submit btn btn-circle green" type="button">
                                    <i class = "fa fa-check"></i> <?php echo app('translator')->get('label.SUBMIT'); ?>
                                </button>
                                <a href = "<?php echo e(URL::to('maProcess')); ?>" class = "btn btn-circle btn-outline grey-salsa"><?php echo app('translator')->get('label.CANCEL'); ?></a>
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

    $(document).on("change", "#courseId", function () {

        var courseId = $("#courseId").val();
        if (courseId == '0') {
            $('#showMarkingLimit').html('');
            return false;
        }
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };

        $.ajax({
            url: "<?php echo e(URL::to('ciComdtModerationMarkingLimit/getMarkingLimit')); ?>",
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
                $('#showMarkingLimit').html(res.html);
                $('.tooltips').tooltip();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                App.unblockUI();
            }
        });//ajax
    });

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
                    url: "<?php echo e(URL::to('maProcess/saveProcess')); ?>",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (res) {
                        toastr.success('<?php echo app('translator')->get("label.MA_PROCESS_HAS_BEEN_SET_SUCCESSFULLY"); ?>', res, options);
//                            $(document).trigger("change", "#courseId");
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
                            //toastr.error(jqXhr.responseJSON.message, '', options);
                            var errors = jqXhr.responseJSON.message;
                            var errorsHtml = '';
                            if (typeof (errors) === 'object') {
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value + '</li>';
                                });
                                toastr.error(errorsHtml, '', options);
                            } else {
                                toastr.error(jqXhr.responseJSON.message, '', options);
                            }
                        } else {
                            toastr.error('Error', '<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', options);
                        }
                        App.unblockUI();
                    }
                });
            }

        });

    });


});


</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/maProcess/index.blade.php ENDPATH**/ ?>