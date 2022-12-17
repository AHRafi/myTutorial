 
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.GENERATE_COURSE_REPORT'); ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => 'crGeneration/filter','class' => 'form-horizontal', 'id' => 'submitForm')); ?>


            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?>:</label>
                        <div class="col-md-8">
                            <?php echo Form::select('training_year_id', $trainingYearList, Request::get('training_year_id'), ['class' => 'form-control js-source-states', 'id' => 'trainingYearId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('training_year_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?>:</label>
                        <div class="col-md-8">
                            <?php echo Form::select('course_id', $courseList, Request::get('course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('course_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 show-cm">
                    <?php if(empty(Request::get('generate')) || Request::get('generate') == 'false' || (Request::get('generate') == 'true' && !empty($assessmentActDeact))): ?>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cmId"><?php echo app('translator')->get('label.CM'); ?> :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            <?php echo Form::select('cm_id', $cmArr, Request::get('cm_id'), ['class' => 'form-control js-source-states', 'id' => 'cmId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('cm_id')); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div> 
            </div> 
            <div class="row proceed">
                <?php if(empty(Request::get('generate')) || Request::get('generate') == 'false' || (Request::get('generate') == 'true' && !empty($assessmentActDeact))): ?>
                <div class="col-md-12 text-center">
                    <div class="form-group">
                        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="eventCombineReportId" value="Show Filter Info" data-id="1">
                            <i class="fa fa-pencil"></i> <?php echo app('translator')->get('label.PROCEED'); ?>
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <div class="col-md-12 margin-top-10">
                    <div class="alert alert-danger alert-dismissable">
                        <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.COURSE_REPORT_GENERATION_HAS_NOT_BEEN_ACTIVATED_YET'); ?></strong></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php echo Form::hidden('performance', $perfomance, ['id' => 'performance']); ?>


            <?php if(Request::get('generate') == 'true'): ?>
            <div class="row report-panel margin-top-20">
                <?php if(!empty($traitList)): ?>
                <div class="col-md-12">
                    <table class="table table-borderless">
                        <tbody>
                            <?php
                            $i = 0;
                            $pI = 0;
                            $typeId = null;
                            ?>
                            <?php $__currentLoopData = $traitList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $trtInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $trtInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $traitId => $trt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $trait = !empty($trt['title']) ? $trt['title'] : '';
                            $para = !empty($trt['para']) ? $trt['para'] : '';
                            ?>
                            <?php
                            if ($typeId != $type) {
                                $typeId = $type;
                                ?>
                                <tr class="margin-top-10">
                                    <td colspan="2">
                                        <div class="margin-top-20">
                                            <span class="bold uppercase font-size-16"><?php echo e(__('label.PARA').'-'.++$pI.': '.$para); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td width="20px">
                                    <span class="bold font-size-14"><?php echo e(++$i); ?>.</span>
                                </td>
                                <td>
                                    <span class="bold font-size-14"><?php echo e($trait); ?></span>
                                    <div class="col-md-12 margin-top-10">

                                        <?php echo Form::select('sentence['.$traitId.']', $sentenceArr[$traitId] ?? [], $reportDataArr['sentence'][$traitId] ?? null, ['class' => 'form-control js-source-states-cr width-inherit sentence', 'id' => 'sentence_'.$traitId, 'data-trait-id' => $traitId]); ?>

                                    </div>
                                </td>
                            </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 margin-top-20 text-center">
                    <button class="btn green-seagreen save-sentences" type="button">
                        <i class="fa fa-file-text-o"></i> <?php echo app('translator')->get('label.SAVE_SENTENCES_N_PROCEED'); ?>
                    </button>

                    <?php
                    $filePath = 'public/CourseReportFiles/' . $activeCourse->name;
                    $uploadClass = !empty($reportDataArr['file']) && file_exists($filePath . '/'. $reportDataArr['file']) ? '' : 'display-none';
                    ?>
                    <button class="btn blue-steel tooltips upload-modified-doc <?php echo e($uploadClass); ?>" href="#modalUploadModifiedDoc"  data-toggle="modal">
                        <i class="fa fa-upload"></i> <?php echo app('translator')->get('label.UPLOAD_MODIFIED_DOC'); ?>
                    </button>
                </div>
                <?php else: ?>
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissable">
                        <p><i class="fa fa-bell-o fa-fw"></i><?php echo app('translator')->get('label.PLEASE_SET_MARKING_REFL_AND_FACTOR_TO_TRAITS'); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php echo Form::close(); ?>

        </div>
    </div>
</div>

<!--Assigned Cm list-->
<div class="modal fade" id="modalUploadModifiedDoc" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showUploadModifiedDoc">

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

        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            $('#courseId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_COURSE_OPT'); ?></option>");
            $('#cmId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_CM_OPT'); ?></option>");
            if (trainingYearId == 0) {
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('crGeneration/getCourse')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#courseId').html(res.html);
                    $(".js-source-states").select2();
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
        //End::Get Course
        //Start::Get CM
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            $('#cmId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_CM_OPT'); ?></option>");
            $(".report-panel").html('');
            if (courseId == 0) {
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('crGeneration/getCm')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('.show-cm').html(res.html);
                    $('.proceed').html(res.html1);
                    $(".js-source-states").select2();
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
        //End::Get CM

        //save sentences and proceed
        $(document).on('click', '.save-sentences', function (e) {
            e.preventDefault();

            var form_data = new FormData($('#submitForm')[0]);
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
                        url: "<?php echo e(URL::to('crGeneration/saveSentences')); ?>",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.save-sentences').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('.save-sentences').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);
                            $(".report-panel").html(res.html);
                            App.unblockUI();
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
                            $('.save-sentences').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });
        //on change of trait sentence
        $(document).on("click", ".generate-doc", function () {

            var form_data = new FormData($('#submitForm')[0]);

            $.ajax({
                url: "<?php echo e(URL::to('crGeneration/generateDoc')); ?>",
                type: "POST",
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: form_data,
                beforeSend: function () {
                    $('.generate-doc').prop('disabled', true);
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    window.location.href = res.filePath;
                    $(".upload-modified-doc").removeClass('display-none');
                    $('.generate-doc').prop('disabled', false);
                    App.unblockUI();
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
                    $('.generate-doc').prop('disabled', false);
                    App.unblockUI();
                }
            });//ajax
        });

        // Start  :: Upload Modified Doc
        $(document).on("click", ".upload-modified-doc", function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var cmId = $("#cmId").val();
            $.ajax({
                url: "<?php echo e(URL::to('crGeneration/getUploadModifiedDoc')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    cm_id: cmId,
                },
                beforeSend: function () {
                    $("#showUploadModifiedDoc").html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $("#showUploadModifiedDoc").html(res.html);
                    $('tooltips').tooltip();
                    App.unblockUI();
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
            }); //ajax
        });
        $(document).on('click', '.save-modified-doc', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#saveModifiedDocForm')[0]);

            swal({
                title: 'Are you sure?',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Upload',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "<?php echo e(URL::to('crGeneration/setUploadModifiedDoc')); ?>",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.save-modified-doc').prop('disabled', true);
                            $(".modal").modal('hide');
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('.save-modified-doc').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);
                            App.unblockUI();
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
                            $('.save-modified-doc').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });
        // End :: Upload Modified Doc

    });
</script>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/crSetup/generation/index.blade.php ENDPATH**/ ?>