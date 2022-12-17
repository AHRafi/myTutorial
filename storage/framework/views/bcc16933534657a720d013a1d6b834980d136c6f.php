 
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.RELATE_TERM_TO_EVENT'); ?>
            </div>
        </div>

        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')); ?>

            <div class="row">
                <div class="col-md-12">

                    <div class="row">
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

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="termId"><?php echo app('translator')->get('label.TERM'); ?> :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    <?php echo Form::select('term_id', $termList, null, ['class' => 'form-control js-source-states', 'id' => 'termId']); ?>

                                </div>
                            </div>
                        </div>

                    </div>



                    <!--get module data-->
                    <div id="showEvent"></div>

                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>

</div>


<!--Assigned Sub Event list-->
<div class="modal fade" id="modalAssignedEvent" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showAssignedEvent">

        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
//        $(document).on("change", "#courseId", function () {
//            var courseId = $("#courseId").val();
//            $('#showEvent').html('');
//            $('#termId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_TERM_OPT'); ?></option>");
//            var options = {
//                closeButton: true,
//                debug: false,
//                positionClass: "toast-bottom-right",
//                onclick: null
//            };
//
//            $.ajax({
//                url: "<?php echo e(URL::to('termToEvent/getTerm')); ?>",
//                type: "POST",
//                dataType: "json",
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                data: {
//                    course_id: courseId,
//                },
//                beforeSend: function () {
//                    App.blockUI({boxed: true});
//                },
//                success: function (res) {
//                    $('#termId').html(res.html);
//                    $('.js-source-states').select2();
//                    App.unblockUI();
//                },
//                error: function (jqXhr, ajaxOptions, thrownError) {
//                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
//                    App.unblockUI();
//                }
//            });//ajax
//        });

        $(document).on("change", "#termId", function () {

            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            if (termId == '0') {
                $('#showEvent').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "<?php echo e(URL::to('termToEvent/getEvent')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showEvent').html(res.html);
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
                        url: "<?php echo e(URL::to('termToEvent/saveTermToEvent')); ?>",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('<?php echo app('translator')->get("label.EVENT_RELATED_WITH_TERM"); ?>', res, options);
//                            $(document).trigger("change", "#termId");
                            var courseId = $("#courseId").val();
                            var termId = $("#termId").val();
                            if (termId == '0') {
                                $('#showEvent').html('');
                                return false;
                            }

                            $.ajax({
                                url: "<?php echo e(URL::to('termToEvent/getEvent')); ?>",
                                type: "POST",
                                dataType: "json",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    course_id: courseId,
                                    term_id: termId,
                                },
                                beforeSend: function () {
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showEvent').html(res.html);
                                    $('.tooltips').tooltip();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
                                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                                    App.unblockUI();
                                }
                            });//ajax

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
        
        //delete
        $(document).on('click', '#buttonDelete', function (e) {
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
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url: "<?php echo e(URL::to('termToEvent/deleteTermToEvent')); ?>",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $("#buttonDelete").prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success(res, '<?php echo app('translator')->get("label.EVENT_DELETED_SUCCESSFULLY"); ?>', options);
                            $("#buttonDelete").prop('disabled', false);
                            $("#termId").trigger("change");
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
                            $("#buttonDelete").prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }
            });

        });

        // Start Show Assigned Event Modal
        $(document).on("click", "#assignedEvent", function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            $.ajax({
                url: "<?php echo e(URL::to('termToEvent/getAssignedEvent')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                },
                success: function (res) {
                    $("#showAssignedEvent").html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });
        // End Show Assigned Event Modal
    });

</script>








<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/termToEvent/index.blade.php ENDPATH**/ ?>