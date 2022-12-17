<?php $__env->startSection('data_count'); ?>
    <div class="col-md-12">
        <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.DS_EVAL_OF_GS'); ?>
                </div>
            </div>
            <div class="portlet-body">
                <?php echo Form::open(['group' => 'form', 'url' => 'dsEvalOfGs/filter', 'class' => 'form-horizontal']); ?>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?></label>
                            <div class="col-md-7">
                                <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYear->name); ?>

                                    </strong></div>
                                <?php echo Form::hidden('training_year_id', $activeTrainingYear->id, ['id' => 'trainingYearId']); ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?></label>
                            <div class="col-md-8">
                                <div class="control-label pull-left"> <strong> <?php echo e($activeCourse->name); ?> </strong>
                                </div>
                                <?php echo Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']); ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label col-md-2" for="gsId"><?php echo app('translator')->get('label.GS'); ?></label>
                            <div class="col-md-10">
                                <?php echo Form::select('gs_id', $activeGsList, Request::get('gs_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'gsId',
                                ]); ?>

                                <span class="text-danger"><?php echo e($errors->first('gs_id')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="subjectId"><?php echo app('translator')->get('label.SUBJECT'); ?></label>
                            <div class="col-md-9">
                                <?php echo Form::select('subject_id', $subjectList, Request::get('subject_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'subjectId',
                                ]); ?>

                                <span class="text-danger"><?php echo e($errors->first('subject_id')); ?></span>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row margin-top-20">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="lessonId"><?php echo app('translator')->get('label.LESSON'); ?></label>
                            <div class="col-md-8">
                                <?php echo Form::select('lesson_id', $lessonList, Request::get('lesson_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'lessonId',
                                ]); ?>

                                <span class="text-danger"><?php echo e($errors->first('lesson_id')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn"
                                id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> <?php echo app('translator')->get('label.PROCEED'); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <?php echo Form::close(); ?>




                <?php if($request->proceed == 'true'): ?>
                    <?php echo Form::open(['group' => 'form', 'url' => '#', 'class' => 'form-horizontal', 'id' => 'submitForm']); ?>

                    <?php echo Form::hidden('course_id', $courseId, ['id' => 'courseId']); ?>

                    <?php echo Form::hidden('gs_id', $gsId, ['id' => 'gsId']); ?>

                    <?php echo Form::hidden('subject_id', $subjectId, ['id' => 'subjectId']); ?>

                    <?php echo Form::hidden('lesson_id', $lessonId, ['id' => 'lessonId']); ?>

                    <div class="row margin-top-20">
                        <div class="col-md-8 col-md-offset-2  gs-eval-panel">
                            <div class="row">
                                <div class="col-md-12  text-center">
                                    <span
                                        class="bold underline uppercase font-size-14"><?php echo app('translator')->get('label.GS_EVAL_FORM'); ?><?php echo e($courseInfo->name ?? ''); ?></span>

                                    <br /><span class="bold uppercase font-size-14">(<?php echo app('translator')->get('label.FACULTY'); ?>)</span>
                                </div>
                            </div>
                            <div class="row margin-top-10">
                                <div class="col-md-12">
                                    <table class="table borderless">
                                        <tbody>
                                            <tr>
                                                <td width="5%"><?php echo app('translator')->get('label.SUBJECT'); ?></td>
                                                <td width="60%">:
                                                    <span class="">
                                                        <?php echo e($subjectInfo->title ?? ''); ?>

                                                    </span>
                                                </td>
                                                <td width="5%"><?php echo app('translator')->get('label.DATE'); ?></td>
                                                <td width="30%">: <span class="bold"><?php echo e($date); ?></span></td>
                                            </tr>
                                            <tr>
                                                <td width="5%"><?php echo app('translator')->get('label.LESSON'); ?></td>
                                                <td width="95%" colspan="3">
                                                    : <span class="bold">
                                                        <?php echo e(!empty($lessonInfo->title) ? '"' . $lessonInfo->title . '"' : ''); ?>

                                                    </span>
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row margin-top-10">
                                <div class="col-md-12">
                                    <span><?php echo app('translator')->get('label.LESSON_OBJ'); ?>: <?php if(!$objectiveArr->isEmpty()): ?> <?php echo app('translator')->get('label.THE_GS_IS_EXPECTED_TO_FOCUS_ON_THE_FOL'); ?> <?php endif; ?></span>
                                    <?php if(!$objectiveArr->isEmpty()): ?>
                                        <ul class="margin-top-10">
                                            <?php $__currentLoopData = $objectiveArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $objective): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li> <?php echo e($objective->name); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row margin-top-10">
                                <div class="col-md-9">
                                    <?php echo app('translator')->get('label.GS'); ?>: <span class="bold">
                                        <?php echo e(!empty($gsInfo->name) ? $gsInfo->name : ''); ?>

                                    </span>
                                </div>
                                <div class="col-md-3">
                                    
                                    <?php echo Form::select('grading', $gradingList, !empty($prevGrading) ? $prevGrading : end($gradingList), [
                                        'class' => 'form-control js-source-states',
                                        'id' => 'grading',
                                    ]); ?>




                                    <span class="text-danger"><?php echo e($errors->first('grading')); ?></span>
                                </div>
                            </div>

                            <div class="row margin-top-10">
                                <div class="col-md-12">
                                    <span><?php echo app('translator')->get('label.GRADING_SCALE_GS'); ?>:</span>
                                    <?php if(!$gradingArr->isEmpty()): ?>
                                        <table class="table borderless margin-top-5">
                                            <tbody>
                                                <?php $__currentLoopData = $gradingArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grading): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td width="5%"></td>
                                                        <td width="95%" class="vcenter">
                                                            <span class="">
                                                                <?php echo $grading->wt . ' - ' . $grading->title . ': ' . $grading->description; ?>

                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row margin-top-10">
                                <div class="col-md-12">
                                    <span><?php echo app('translator')->get('label.PL_CONSIDERATION_EVAL'); ?></span>
                                    <?php if(!$considerationArr->isEmpty()): ?>
                                        <ul class="margin-top-10">
                                            <?php $__currentLoopData = $considerationArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $consideration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li> <?php echo e($consideration->title); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row margin-top-10">
                                <div class="col-md-12">
                                    <span><?php echo app('translator')->get('label.COMMENTS'); ?>:</span>
                                    <?php if(!$commentArr->isEmpty()): ?>

                                        <?php $sl = 0; ?>
                                        <table class="table borderless margin-top-5">
                                            <tbody>
                                                <?php $__currentLoopData = $commentArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td width="5%"> <?php echo e(++$sl); ?></td>
                                                        <td width="95%" class="vcenter">
                                                            <div class="form-group">
                                                                <label
                                                                    for="comment_<?php echo e($comment->id); ?>"><?php echo $comment->title; ?>:</label>
                                                                <?php echo Form::textarea(
                                                                    'comment[' . $comment->id . ']',
                                                                    !empty($prevCommentArr[$comment->id]) ? $prevCommentArr[$comment->id] : '',
                                                                    [
                                                                        'class' => 'form-control min-height-45',
                                                                        'size' => '4x1',
                                                                        'id' => 'comment_' . $comment->id,
                                                                        'placeholder' => 'Provide Comment',
                                                                    ],
                                                                ); ?>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>

                            </div>
                            <div class="col-md-12 text-center">
                                <div class="form-group">

                                    

                                    <?php if(!empty($gsEvalLockStatus)): ?>
                                        <?php if($gsEvalLockStatus == '1'): ?>
                                            <button class="btn btn-circle label-purple-sharp request-for-unlock"
                                                type="button" id="buttonSubmitLock" data-target="#modalUnlockMessage"
                                                data-toggle="modal">
                                                <i class="fa fa-unlock"></i> <?php echo app('translator')->get('label.REQUEST_FOR_UNLOCK'); ?>
                                            </button>
                                        <?php elseif($gsEvalLockStatus == '2'): ?>
                                            <div class="alert alert-danger alert-dismissable">
                                                <p><strong><i class="fa fa-unlock"></i> <?php echo __('label.REQUEST_FOR_UNLOCK_HAS_BEEN_SEND'); ?></strong>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn btn-circle blue-steel button-submit" data-id="0"
                                            type="button" id="buttonSubmit">
                                            <i class="fa fa-file-text-o"></i> <?php echo app('translator')->get('label.SAVE_AS_DRAFT'); ?>
                                        </button>&nbsp;&nbsp;
                                        <button class="btn btn-circle green button-submit" data-id="1" type="button">
                                            <i class="fa fa-lock"></i> <?php echo app('translator')->get('label.SAVE_LOCK'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php echo Form::close(); ?>


                <?php endif; ?>


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

    <script type="text/javascript">
        $(document).ready(function() {

            $(document).on("change", "#gsId", function() {

                var courseId = $("#courseId").val();
                var gsId = $("#gsId").val();

                $('#showGsInfo').html('');
                $('#subjectId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUBJECT_OPT'); ?></option>");
                $('#lessonId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_LESSON_OPT'); ?></option>");

                if (gsId == '0' || subjectId == '0' || lessonId == '0') {
                    $('#showGenerateButton').html('');
                    return false;
                }

                var options = {
                    closeButton: true,
                    debug: false,
                    positionClass: "toast-bottom-right",
                    onclick: null
                };

                $.ajax({
                    url: "<?php echo e(URL::to('dsEvalOfGs/getSubject')); ?>",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId,
                        gs_id: gsId,
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#subjectId').html(res.html);
                        $('.js-source-states').select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        toastr.error('<?php echo app('translator')->get('label.SOMETHING_WENT_WRONG'); ?>', 'Error', options);
                        App.unblockUI();
                    }
                }); //ajax
            });

            $(document).on("change", "#subjectId", function() {

                var courseId = $("#courseId").val();
                var gsId = $("#gsId").val();
                var subjectId = $("#subjectId").val();

                if (gsId == '0' || subjectId == '0' || lessonId == '0') {
                    $('#showGenerateButton').html('');
                    return false;
                }

                $('#showGsInfo').html('');
                $('#lessonId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_LESSON_OPT'); ?></option>");
                var options = {
                    closeButton: true,
                    debug: false,
                    positionClass: "toast-bottom-right",
                    onclick: null
                };
                $.ajax({
                    url: "<?php echo e(URL::to('dsEvalOfGs/getLesson')); ?>",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId,
                        gs_id: gsId,
                        subject_id: subjectId,
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#lessonId').html(res.html);
                        $('.js-source-states').select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        toastr.error('<?php echo app('translator')->get('label.SOMETHING_WENT_WRONG'); ?>', 'Error', options);
                        App.unblockUI();
                    }
                }); //ajax
            });

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };


            $(document).on('click', '.button-submit', function(e) {
                e.preventDefault();
                var dataId = $(this).attr('data-id');

                // alert(dataId);
                // exit;
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
                    closeOnCancel: true,
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "<?php echo e(URL::to('dsEvalOfGs/storeGrading')); ?>",
                            type: "POST",
                            datatype: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            beforeSend: function() {
                                $('.button-submit').prop('disabled', true);

                                App.blockUI({
                                    boxed: true
                                });
                            },
                            success: function(res) {
                                $('.button-submit').prop('disabled', false);

                                toastr.success(res.message, '<?php echo app('translator')->get('label.GS_EVAL_SUCCESSFUL'); ?>',
                                    options);
                                location.reload();
                                App.unblockUI();
                            },
                            error: function(jqXhr, ajaxOptions, thrownError) {
                                if (jqXhr.status == 400) {
                                    var errorsHtml = '';
                                    var errors = jqXhr.responseJSON.message;
                                    $.each(errors, function(key, value) {
                                        errorsHtml += '<li>' + value[0] +
                                            '</li>';
                                    });
                                    toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                        options);
                                } else if (jqXhr.status == 401) {
                                    toastr.error(jqXhr.responseJSON.message, 'Error',
                                        options);
                                } else {
                                    toastr.error('<?php echo app('translator')->get('label.SOMETHING_WENT_WRONG'); ?>', 'Error',
                                        options);
                                }

                                $('.button-submit').prop('disabled', false);
                                App.unblockUI();
                            }

                        });
                    }
                });
            });

            //Rquest for unlock
            $(document).on('click', '.request-for-unlock', function(e) {
                e.preventDefault();

                var form_data = new FormData($('#submitForm')[0]);

                $.ajax({
                    url: "<?php echo e(URL::to('dsEvalOfGs/getRequestForUnlockModal')); ?>",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    beforeSend: function() {
                        $('#showMessage').html('');
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#showMessage').html(res.html);
                        $('.tooltips').tooltip();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 400) {
                            var errorsHtml = '';
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function(key, value) {
                                errorsHtml += '<li>' + value[0] + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, 'Error', options);
                        } else {
                            toastr.error('<?php echo app('translator')->get('label.SOMETHING_WENT_WRONG'); ?>', 'Error', options);
                        }
                        App.unblockUI();
                    }

                });
            });

            $(document).on('click', '.save-request-for-unlock', function(e) {
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
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "<?php echo e(URL::to('dsEvalOfGs/saveRequestForUnlock')); ?>",
                            type: "POST",
                            datatype: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(res) {
                                $('.modal').modal('hide');
                                toastr.success(res, '<?php echo app('translator')->get('label.REQUEST_FOR_UNLOCK_HAS_BEEN_SENT_SUCCESSFULLY'); ?>', options);
                                location.reload();
                            },
                            error: function(jqXhr, ajaxOptions, thrownError) {
                                if (jqXhr.status == 400) {
                                    var errorsHtml = '';
                                    var errors = jqXhr.responseJSON.message;
                                    $.each(errors, function(key, value) {
                                        errorsHtml += '<li>' + value[0] +
                                            '</li>';
                                    });
                                    toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                        options);
                                } else if (jqXhr.status == 401) {
                                    toastr.error(jqXhr.responseJSON.message, 'Error',
                                        options);
                                } else {
                                    toastr.error('<?php echo app('translator')->get('label.SOMETHING_WENT_WRONG'); ?>', 'Error',
                                    options);
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

<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/dsEvalOfGs/index.blade.php ENDPATH**/ ?>