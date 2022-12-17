<?php $__env->startSection('data_count'); ?>
    <div class="col-md-12">
        <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.MODULE_TO_SUBJECT'); ?>
                </div>
                <div class="actions">
                    <button class="btn btn-category green tooltips" id="cloneCourseButton" data-toggle="modal"
                        data-target="#modalCloneModule" data-placement="top" title="<?php echo app('translator')->get('label.CLONE_MODULE'); ?>">
                        <i class="fa fa-exchange" aria-hidden="true"></i> <?php echo app('translator')->get('label.CLONE_COURSE'); ?>
                    </button>
                </div>
            </div>

            <div class="portlet-body">
                <?php echo Form::open(['group' => 'form', 'url' => '', 'class' => 'form-horizontal', 'id' => 'submitForm']); ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?>:</label>
                                    <div class="col-md-7">
                                        <div class="control-label pull-left"> <strong>
                                                <?php echo e($activeTrainingYear->name ?? ''); ?></strong></div>
                                        <?php echo Form::hidden('training_year_id', $activeTrainingYear->id ?? null, ['id' => 'trainingYearId']); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?>:</label>
                                    <div class="col-md-7">
                                        <div class="control-label pull-left"> <strong> <?php echo e($activeCourse->name ?? null); ?>

                                            </strong></div>
                                        <?php echo Form::hidden('course_id', $activeCourse->id ?? null, ['id' => 'courseId']); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="moduleId"><?php echo app('translator')->get('label.MODULE'); ?>:</label>
                                    <div class="col-md-7">
                                        <?php echo Form::select('module_id', $moduleArr, null, [
                                            'class' => 'form-control js-source-states',
                                            'id' => 'moduleId',
                                        ]); ?>

                                    </div>
                                </div>

                            </div>

                        </div>
                        <div id="showDsList"></div>
                    </div>
                </div>
                <?php echo Form::close(); ?>

            </div>
        </div>

    </div>

    <!--Assigned Cm list-->
    <div class="modal fade" id="modalAssignedSubject" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div id="showAssignedSubject">

            </div>
        </div>
    </div>
    <div class="modal fade" id="modalCloneModule" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div id="showCloneModal">

            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {

            // # Get Ds Start
            $(document).on("change", "#moduleId", function() {
                var trainingYearId = $("#trainingYearId").val();
                var module_id = $(this).val();
                var course_id = $("#courseId").val();
                // alert(subject_id); return false;
                if (module_id === '0') {
                    $('#showDsList').html('');
                    return false;
                }
                $.ajax({
                    url: "<?php echo e(URL::to('moduleToSubject/getDsList')); ?>",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        module_id: module_id,
                        course_id: course_id,
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true,
                        });
                    },
                    success: function(res) {
                        $('#showDsList').html(res.html);
                        $('.tooltips').tooltip();
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        App.unblockUI();
                    }
                }); //ajax
                App.unblockUI();
            });
            // # Get Ds End

            // # Saving Data Start
            $(document).on('click', '.button-submit', function(e) {
                e.preventDefault();
                var oTable = $('#dataTable').dataTable();
                var x = oTable.$('input,select,textarea').serializeArray();
                $.each(x, function(i, field) {

                    $("#submitForm").append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', field.name)
                        .val(field.value));
                });
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
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "<?php echo e(URL::to('moduleToSubject/store')); ?>",
                            type: "POST",
                            datatype: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: form_data,
                            success: function(res) {
                                $("#assignedSubject").html(
                                    `<?php echo app('translator')->get('label.SUBJECT_ASSIGNED_TO_THIS_MODULE'); ?>:  ${res.countModuleToSubject} <i class="fa fa-search-plus">`
                                )
                                toastr.success(res.message, res.heading, options);
                            },
                            error: function(jqXhr, ajaxOptions, thrownError) {
                                App.unblockUI();
                                if (jqXhr.status == 400) {
                                    var errorsHtml = '';
                                    var errors = jqXhr.responseJSON.message;
                                    $.each(errors, function(key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                        options);
                                } else if (jqXhr.status == 401) {
                                    toastr.error(jqXhr.responseJSON.message, jqXhr
                                        .responseJSON.heading,
                                        options);
                                } else {
                                    toastr.error('Error', 'Something went wrong',
                                        options);
                                }
                                App.unblockUI();
                            }
                        });
                    }

                });

            });
            // # Saving Data End

            // Get Assigned Subject Start
            $(document).on('click', '#assignedSubject', function(e) {
                e.preventDefault();
                var moduleId = $("#moduleId").val();
                var courseId = $("#courseId").val();

                var options = {
                    closeButton: true,
                    debug: false,
                    positionClass: "toast-bottom-right",
                    onclick: null
                };
                $.ajax({
                    url: "<?php echo e(URL::to('moduleToSubject/getAssignedSubject')); ?>",
                    type: "POST",
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        module_id: moduleId,
                        course_id: courseId,
                    },
                    success: function(res) {
                        $("#showAssignedSubject").html(res.html);
                        // toastr.success(res, "<?php echo app('translator')->get('label.SUBJECT_HAS_BEEN_RELATED_TO_THIS_MODULE'); ?>", options);
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        App.unblockUI();
                        if (jqXhr.status == 400) {
                            var errorsHtml = '';
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function(key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading,
                                options);
                        } else {
                            toastr.error('Error', 'Something went wrong',
                                options);
                        }
                        App.unblockUI();
                    }
                });

            });
            // Get Assigned Subject End

            // # Delete Module Start
            $(document).on('click', '#buttonDelete', function(e) {
                e.preventDefault();
                var moduleId = $(this).data('moduleid');
                var courseId = $("#courseId").val();
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
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'No, Cancel',
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "<?php echo e(URL::to('moduleToSubject/deleteModule')); ?>",
                            type: "POST",
                            datatype: 'json',
                            cache: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                module_id: moduleId,
                                course_id: courseId,
                            },
                            success: function(res) {
                                $('#showDsList').html(res.html);
                                $('.tooltips').tooltip();
                                $(".js-source-states").select2();
                                toastr.success(res.message, res.heading, options);
                            },
                            error: function(jqXhr, ajaxOptions, thrownError) {
                                App.unblockUI();
                                if (jqXhr.status == 400) {
                                    var errorsHtml = '';
                                    var errors = jqXhr.responseJSON.message;
                                    $.each(errors, function(key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                        options);
                                } else if (jqXhr.status == 401) {
                                    toastr.error(jqXhr.responseJSON.message, jqXhr
                                        .responseJSON.heading,
                                        options);
                                } else {
                                    toastr.error('Error', 'Something went wrong',
                                        options);
                                }
                                App.unblockUI();
                            }
                        });
                    }

                });

            });
            // # Delete Module End
            // # Delete Module Start
            $(document).on('click', '#cloneCourseButton', function(e) {
                e.preventDefault();
                var course_id = $("#courseId").val();
                var options = {
                    closeButton: true,
                    debug: false,
                    positionClass: "toast-bottom-right",
                    onclick: null
                };
                $.ajax({
                    url: "<?php echo e(URL::to('moduleToSubject/cloneCourse')); ?>",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: course_id,
                    },
                    success: function(res) {
                        $("#showCloneModal").html(res.html);
                        // toastr.success(res.message, res.heading, options);

                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        App.unblockUI();
                    }
                });

            });
            // # Delete Module End
            // # Get Course Details Start
            $(document).on("change", "#previousCourseId", function() {
                var PreviousCourse_id = $("#previousCourseId").val();
                if (PreviousCourse_id == '0') {
                    $('#courseWiseModuleTable').html('');
                    $("#cloneSubmitButton").attr('disabled', true);
                    return false;
                }
                // alert(subject_id); return false;

                $.ajax({
                    url: "<?php echo e(URL::to('moduleToSubject/getCourseDetails')); ?>",
                    type: "POST",
                    dataType: "json",
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        previous_course_id: PreviousCourse_id,
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true,
                        });
                    },
                    success: function(res) {
                        $('#courseWiseModuleTable').html(res.html);
                        if (parseInt(res.count) > 0) {
                            $("#cloneSubmitButton").show();
                            $("#cloneSubmitButton").attr('disabled', false);
                        }
                        $('.tooltips').tooltip();
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        App.unblockUI();
                    }
                }); //ajax
                App.unblockUI();
            });
            // # Get Course Details End
            // # Saving Data Start
            $(document).on('click', '#cloneSubmitButton', function(e) {
                e.preventDefault();
                var form_data = new FormData($('#courseCloneForm')[0]);

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
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "<?php echo e(URL::to('moduleToSubject/clone')); ?>",
                            type: "POST",
                            datatype: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: form_data,
                            success: function(res) {
                                $("#assignedSubject").html(
                                    `<?php echo app('translator')->get('label.SUBJECT_ASSIGNED_TO_THIS_MODULE'); ?>:  ${res.countModuleToSubject} <i class="fa fa-search-plus">`
                                )
                                toastr.success(res.message, res.heading, options);
                                location.reload();
                            },
                            error: function(jqXhr, ajaxOptions, thrownError) {
                                App.unblockUI();
                                if (jqXhr.status == 400) {
                                    var errorsHtml = '';
                                    var errors = jqXhr.responseJSON.message;
                                    $.each(errors, function(key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                        options);
                                } else if (jqXhr.status == 401) {
                                    toastr.error(jqXhr.responseJSON.message, jqXhr
                                        .responseJSON.heading,
                                        options);
                                } else {
                                    toastr.error('Error', 'Something went wrong',
                                        options);
                                }
                                App.unblockUI();
                            }
                        });
                    }

                });

            });
            // # Saving Data End

        });
    </script>
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/moduleToSubject/index.blade.php ENDPATH**/ ?>