 
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.DS_REMARKS_REPORT'); ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => 'dsRemarksReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')); ?>

            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?>:</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYearList->name); ?> </strong></div>
                            <?php echo Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?>:</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> <?php echo e($courseList->name); ?> </strong></div>
                            <?php echo Form::hidden('course_id', $courseList->id, ['id' => 'courseId']); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId"><?php echo app('translator')->get('label.TERM'); ?> :<span class="text-danger"> </span></label>
                        <div class="col-md-8">
                            <?php echo Form::select('term_id', $termList, Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('term_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cmId"><?php echo app('translator')->get('label.CM'); ?> :</label>
                        <div class="col-md-8">
                            <?php echo Form::select('cm_id', $cmList, Request::get('cm_id'), ['class' => 'form-control js-source-states', 'id' => 'cmId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('cm_id')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="maEventId"><?php echo app('translator')->get('label.EVENT'); ?> :</label>
                        <div class="col-md-8">
                            <?php echo Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('event_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="form-group">
                        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="eventCombineReportId" value="Show Filter Info" data-id="1">
                            <i class="fa fa-search"></i> <?php echo app('translator')->get('label.GENERATE'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php if(Request::get('generate') == 'true'): ?>
            <?php if(!$dsRemarksArr->isEmpty()): ?>
            <div class="row">
                <div class="col-md-12 text-right">
                    <a class="btn btn-md btn-primary vcenter tooltips" title="<?php echo app('translator')->get('label.PRINT'); ?>" target="_blank"  href="<?php echo URL::full().'&view=print'; ?>">
                        <span class=""><i class="fa fa-print"></i> </span> 
                    </a>
                    <a class="btn btn-success vcenter tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_PDF'); ?>" href="<?php echo URL::full().'&view=pdf'; ?>">
                        <span class=""><i class="fa fa-file-pdf-o"></i></span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            <?php echo e(__('label.TRAINING_YEAR')); ?> : <strong><?php echo e($activeTrainingYearList->name); ?> |</strong>
                            <?php echo e(__('label.COURSE')); ?> : <strong><?php echo e($courseList->name); ?></strong>
                            <?php echo e(__('label.TERM')); ?> : <strong><?php echo e(!empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL')); ?> |</strong>
                            <?php if(!empty(Request::get('cm_id'))): ?>
                            <strong> |</strong>
                            <?php echo e(__('label.CM')); ?> : <strong><?php echo e(!empty($cmList[Request::get('cm_id')]) && Request::get('cm_id') != 0 ? $cmList[Request::get('cm_id')] : __('label.N_A')); ?></strong>
                            <?php endif; ?>
                            <?php if(!empty(Request::get('event_id'))): ?>
                            <strong> |</strong>
                            <?php echo e(__('label.EVENT')); ?> : <strong><?php echo e(!empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A')); ?></strong>
                            <?php endif; ?>
                        </h5>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="max-height-500 table-responsive webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.SL'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.DATE'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.TERM'); ?></th>
                                    <?php if(empty(Request::get('cm_id'))): ?>
                                    <th class="vcenter"><?php echo app('translator')->get('label.CM'); ?></th>
                                    <?php endif; ?>
                                    <?php if(empty(Request::get('event_id'))): ?>
                                    <th class="vcenter"><?php echo app('translator')->get('label.EVENT'); ?></th>
                                    <?php endif; ?>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.RMKS'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.REMARKED_BY'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!$dsRemarksArr->isEmpty()): ?>
                                <?php
                                $sl = 0;
                                ?>
                                <?php $__currentLoopData = $dsRemarksArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $remarks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="vcenter text-center"><?php echo ++$sl; ?></td>
                                    <td class="vcenter"><?php echo e(!empty($remarks->date) ? Helper::formatDate($remarks->date) : ''); ?></td>
                                    <td class="vcenter"><?php echo $remarks->term; ?></td>
                                    <?php if(empty(Request::get('cm_id'))): ?>
                                    <td class="vcenter"><?php echo $remarks->cm; ?></td>
                                    <?php endif; ?>
                                    <?php if(empty(Request::get('event_id'))): ?>
                                    <td class="vcenter"><?php echo e($remarks->event); ?></td>
                                    <?php endif; ?>
                                    <td class="vcenter"><?php echo e($remarks->remarks ?? ''); ?></td>
                                    <td class="vcenter text-center"><?php echo e($remarks->official_name); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="7"><strong><?php echo app('translator')->get('label.NO_DS_REMARKS_FOUND'); ?></strong></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php endif; ?>
            <?php echo Form::close(); ?>

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
        $(".table-head-fixer-color").tableHeadFixer('');
        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            if (trainingYearId == 0) {
                $('#courseId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_COURSE_OPT'); ?></option>");
                $('#termId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_TERM_OPT'); ?></option>");
                $('#eventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_OPT'); ?></option>");
                $('#cmId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_CM_OPT'); ?></option>");
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('dsRemarksReportCrnt/getCourse')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    $('#termId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_TERM_OPT'); ?></option>");
                    $('#eventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_OPT'); ?></option>");
                    $('#cmId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_CM_OPT'); ?></option>");
                    $('.required-show').text('');
                    $('#hasSubSyn').val(0);
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#courseId').html(res.html);
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            });//ajax

        });
        //End::Get Course

        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == 0) {
                $('#termId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_TERM_OPT'); ?></option>");
                $('#eventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_OPT'); ?></option>");
                $('.required-show').text('');
                $('#hasSubSyn').val(0);
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('dsRemarksReportCrnt/getEvent')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    $('#termId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_TERM_OPT'); ?></option>");
                    $('#eventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_OPT'); ?></option>");
                    $('#cmId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_CM_OPT'); ?></option>");
                    $('.required-show').text('');
                    $('#hasSubSyn').val(0);
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#eventId').html(res.html);
                    $('#cmId').html(res.html2);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    $("#previewMarkingSheet").prop("disabled", false);
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
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }

                }
            });//ajax
        });
        
        //Start::Get Event
        $(document).on("change", "#termId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            if (termId == 0) {
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('dsRemarksReportCrnt/getEvent')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#eventId').html(res.html);
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
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }

                }
            });//ajax
        });
        //End::Get Event


    });
</script>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/reportCrnt/dsRemarks/index.blade.php ENDPATH**/ ?>