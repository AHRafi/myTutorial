 
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.RELATE_APPT_TO_CM'); ?>
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?>:</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> <?php echo e($activeCourse->name); ?> </strong></div>
                                    <?php echo Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']); ?>

                                </div>
                            </div>
                        </div>
                        <!--get term -->
                        <div id="showTermEvent">
                            <?php if(!empty($activeTermInfo)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-6" for="termId"><?php echo app('translator')->get('label.TERM'); ?> :<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <div class="control-label pull-left"> <strong> <?php echo e($activeTermInfo->name); ?> </strong></div>
                                    </div>
                                    <?php echo Form::hidden('term_id', $activeTermInfo->id, ['id' => 'termId']); ?>

                                </div> 
                            </div> 

                            <?php if(sizeof($eventList) > 1): ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="eventId"><?php echo app('translator')->get('label.EVENT'); ?> :<span class="text-danger"> *</span></label>
                                    <div class="col-md-7">
                                        <?php echo Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']); ?>

                                    </div>
                                </div>
                            </div>

                            <?php else: ?>
                            <div class="col-md-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><i class="fa fa-bell-o fa-fw"></i><?php echo app('translator')->get('label.NO_ACTIVE_EVENT_FOUND'); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php else: ?>
                            <div class="col-md-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><i class="fa fa-bell-o fa-fw"></i><?php echo app('translator')->get('label.NO_ACTIVE_TERM_FOUND'); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <!--get sub event list or Appt Matrix -->
                        <div id="showSubEventCmAppt"></div>
                        <!--get sub sub event list or Appt Matrix -->
                        <div id="showSubSubEventCmAppt"></div>
                        <!--get sub sub sub event list or Appt Matrixs -->
                        <div id="showSubSubSubEventCmAppt"></div>
                    </div>
                    <!--get  Appt Matrix -->
                    <div id="showCmAppt"></div>



                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>

</div>

<!--Assigned Appt-->
<div class="modal fade" id="modalAssignedAppt" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showAssignedAppt">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
//        $(document).on("change", "#courseId", function () {
//
//            var courseId = $("#courseId").val();
//
//            $('#showTermEvent').html('');
//            $('#showSubEventCmAppt').html('');
//            $('#showSubSubEventCmAppt').html('');
//            $('#showSubSubSubEventCmAppt').html('');
//            $('#showCmAppt').html('');
//            $('#showAppt').html('');
//            $('#subEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_EVENT_OPT'); ?></option>");
//            $('#subSubEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_EVENT_OPT'); ?></option>");
//            $('#subSubSubEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_SUB_EVENT_OPT'); ?></option>");
//
//            if (courseId == '0') {
//                $('#showTermEvent').html('');
//                return false;
//            }
//            
//            var options = {
//                closeButton: true,
//                debug: false,
//                positionClass: "toast-bottom-right",
//                onclick: null
//            };
//
//            $.ajax({
//                url: "<?php echo e(URL::to('apptToCm/getTermEvent')); ?>",
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
//                    $('#showTermEvent').html(res.html);
//                    $('.js-source-states').select2();
//                    App.unblockUI();
//                },
//                error: function (jqXhr, ajaxOptions, thrownError) {
//                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
//                    App.unblockUI();
//                }
//            });//ajax
//        });

        $(document).on("change", "#eventId", function () {

            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();

            $('#showSubEventCmAppt').html('');
            $('#showSubSubEventCmAppt').html('');
            $('#showSubSubSubEventCmAppt').html('');
            $('#showCmAppt').html('');
            $('#showAppt').html('');
            $('#subEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_EVENT_OPT'); ?></option>");
            $('#subSubEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_EVENT_OPT'); ?></option>");
            $('#subSubSubEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_SUB_EVENT_OPT'); ?></option>");

            if (eventId == '0') {
                $('#showCmAppt').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "<?php echo e(URL::to('apptToCm/getSubEventCmAppt')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubEventCmAppt').html(res.html);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subEventId", function () {

            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();

            $('#showSubSubEventCmAppt').html('');
            $('#showSubSubSubEventCmAppt').html('');
            $('#showCmAppt').html('');
            $('#showAppt').html('');
            $('#subSubEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_EVENT_OPT'); ?></option>");
            $('#subSubSubEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_SUB_EVENT_OPT'); ?></option>");

            if (subEventId == '0') {
                $('#showCmAppt').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "<?php echo e(URL::to('apptToCm/getSubSubEventCmAppt')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubEventCmAppt').html(res.html);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subSubEventId", function () {

            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();

            $('#showSubSubSubEventCmAppt').html('');
            $('#showCmAppt').html('');
            $('#showAppt').html('');
            $('#subSubSubEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_SUB_EVENT_OPT'); ?></option>");

            if (subSubEventId == '0') {
                $('#showCmAppt').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "<?php echo e(URL::to('apptToCm/getSubSubSubEventCmAppt')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubSubEventCmAppt').html(res.html);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subSubSubEventId", function () {

            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();

            if (subSubSubEventId == '0') {
                $('#showCmAppt').html('');
                return false;
            }

            $('#showAppt').html('');

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "<?php echo e(URL::to('apptToCm/getCmAppt')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmAppt').html(res.html);
                    $('.js-source-states').select2();
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
                        url: "<?php echo e(URL::to('apptToCm/saveApptToCm')); ?>",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.button-submit').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success('<?php echo app('translator')->get("label.APPT_HAS_BEEN_ASSIGNED_TO_THIS_CM"); ?>', res, options);
                            $('.button-submit').prop('disabled', false);
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
                                toastr.error(jqXhr.responseJSON.message, '', options);
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            $('.button-submit').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }
            });
        });

        // Start Show Assigned Appt
        $(document).on("click", "#assignedAppt", function (e) {
            e.preventDefault();

            var courseId = $(this).attr('data-course-id');
            var termId = $(this).attr('data-term-id');
            var eventId = $(this).attr('data-event-id');
            var subEventId = $(this).attr('data-sub-event-id');
            var subSubEventId = $(this).attr('data-sub-sub-event-id');
            var subSubSubEventId = $(this).attr('data-sub-sub-sub-event-id');

            $.ajax({
                url: "<?php echo e(URL::to('apptToCm/getAssignedAppt')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                success: function (res) {
                    $("#showAssignedAppt").html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });
        // End Show Assigned Appt
    });

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/apptToCm/index.blade.php ENDPATH**/ ?>