
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-book"></i><?php echo app('translator')->get('label.EVENT_ASSESSMENT'); ?>
            </div>
        </div>

        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')); ?>

            <?php echo Form::hidden('auto_save', 0, ['id' => 'autoSave']); ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-6" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?> :</label>
                                <div class="col-md-6">
                                    <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYearInfo->name); ?> </strong></div>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($activeCourse)): ?>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?> :</label>
                                <div class="col-md-7"> <div class="control-label pull-left"> <strong> <?php echo e($activeCourse->name); ?> </strong></div>
                                    <?php echo Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']); ?>

                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12 ">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.DS_IS_NOT_ASSIGNED_TO_ANY_MARKING_GROUP_YET'); ?></strong></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div id="showTermEvent">
                            <?php if(!empty($termList)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-6" for="termId"><?php echo app('translator')->get('label.TERM'); ?> :<span class="text-danger"> *</span></label>
                                    <div class="col-md-6">
                                        <div class="control-label pull-left"> <strong> <?php echo e($termList->name); ?> </strong></div>
                                    </div>
                                </div>
                            </div>
                            <?php echo Form::hidden('term_id',$termList->id,['id'=>'termId']); ?>

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
                            <div class="row">
                                <div class="col-md-offset-2 col-md-7">
                                    <div class="alert alert-danger alert-dismissable">
                                        <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_TERM'); ?></strong></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php else: ?>
                            <div class="row">
                                <div class="col-md-offset-2 col-md-7">
                                    <div class="alert alert-danger alert-dismissable">
                                        <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_ACTIVE_TERM_FOUND'); ?></strong></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>    

                    <div class="row">
                        <div id="showSubEventOrCmList"></div>

                        <div id="showSubSubEventOrCmList"></div>

                        <div id="showSubSubSubEventOrCmList"></div>
                    </div>

                    <div id="showCmList">

                    </div>
                    <!-- Unlock message modal -->
                    <div class="modal fade test" id="modalUnlockMessage" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div id="showMessage"></div>
                        </div>
                    </div>
                    <!-- End Unlock message modal -->
                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>

</div>

<script src="<?php echo e(asset('public/assets/global/plugins/sweetalert/lib/sweetalert2.min.js')); ?>" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {

        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            timeOut: 1000,
            onclick: null
        };

        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == '0') {
                $('#showTermEvent').html('');
                $('#showSubEventOrCmList').html('');
                $('#showSubSubEventOrCmList').html('');
                $('#showSubSubSubEventOrCmList').html('');
                $('#showCmList').html('');
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('eventAssessmentMarking/getTermEvent')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    $('#showSubEventOrCmList').html('');
                    $('#showSubSubEventOrCmList').html('');
                    $('#showSubSubSubEventOrCmList').html('');
                    $('#showCmList').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showTermEvent').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();

                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, 'Error', options);
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#eventId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            if (eventId == '0') {
                $('#subEventId').html("<select><option value='0'><?php echo app('translator')->get('label.SELECT_SUB_EVENT_OPT'); ?></option></select>");
                $('#showSubEventOrCmList').html('');
                $('#showSubSubEventOrCmList').html('');
                $('#showSubSubSubEventOrCmList').html('');
                $('#showCmList').html('');
                return false;
            }

            $.ajax({
                url: "<?php echo e(URL::to('eventAssessmentMarking/getSubEvent')); ?>",
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
                    $('#showSubEventOrCmList').html('');
                    $('#showSubSubEventOrCmList').html('');
                    $('#showSubSubSubEventOrCmList').html('');
                    $('#showCmList').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubEventOrCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
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
            if (subEventId == '0') {
                $('#subSubEventId').html("<select><option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_EVENT_OPT'); ?></option></select>");
                $('#showSubSubSubEventOrCmList').html('');
                $('#showSubSubEventOrCmList').html('');
                $('#showCmList').html('');
                return false;
            }

            $.ajax({
                url: "<?php echo e(URL::to('eventAssessmentMarking/getSubSubEvent')); ?>",
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
                    $('#showSubSubSubEventOrCmList').html('');
                    $('#showSubSubEventOrCmList').html('');
                    $('#showCmList').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubEventOrCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
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
            if (subSubEventId == '0') {
                $('#subSubSubEventId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SUB_SUB_EVENT_OPT'); ?></option>");
                $('#showCmList').html('');
                $('#showSubSubSubEventOrCmList').html('');
                return false;
            }

            $.ajax({
                url: "<?php echo e(URL::to('eventAssessmentMarking/getSubSubSubEvent')); ?>",
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
                    $('#showSubSubSubEventOrCmList').html('');
                    $('#showCmList').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubSubEventOrCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
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
                $('#showCmList').html('');
                return false;
            }

            $.ajax({
                url: "<?php echo e(URL::to('eventAssessmentMarking/showMarkingCmList')); ?>",
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
                    $('#showCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });
        // Start::Sort
        $(document).on("change", "#sortBy", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var sortBy = $("#sortBy").val();

            $.ajax({
                url: "<?php echo e(URL::to('eventAssessmentMarking/showMarkingCmList')); ?>",
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
                    sort_by: sortBy,
                },
                beforeSend: function () {
                    $('.marking-cm-list').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmList').html(res.html);
                    $('#autoSave').val(res.autoSave);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });
        //End::Sorty

//form submit
        $(document).on('click', '.button-submit', function (e) {
            e.preventDefault();
            var dataId = $(this).attr('data-id');
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
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                            url: "<?php echo e(URL::to('eventAssessmentMarking/saveEventAssessmentMarking')); ?>",
                            type: "POST",
                            datatype: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            beforeSend: function () {
                                $('.button-submit').prop('disabled', true);
                                if (dataId == 2) {
                                    $('#autoSave').val(0);
                                }
                                App.blockUI({boxed: true});
                            },
                            success: function (res) {
                                $('.button-submit').prop('disabled', false);
                                if (dataId == 2) {
                                    $('#autoSave').val(0);
                                }
                                toastr.success(res.message, res.heading, options);
                                var courseId = res.loadData.course_id;
                                var termId = res.loadData.term_id;
                                var eventId = res.loadData.event_id;
                                var subEventId = res.loadData.sub_event_id;
                                var subSubEventId = res.loadData.sub_sub_event_id;
                                var subSubSubEventId = res.loadData.sub_sub_sub_event_id;
                                $.ajax({
                                    url: "<?php echo e(URL::to('eventAssessmentMarking/showMarkingCmList')); ?>",
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
                                        if (subEventId == '0') {
                                            $('#showSubEventOrCmList').html('');
                                        }
                                        if (subSubEventId == '0') {
                                            $('#showSubSubEventOrCmList').html('');
                                        }
                                        if (subSubSubEventId == '0') {
                                            $('#showSubSubSubEventOrCmList').html('');
                                        }
                                        $('#showCmList').html('');
                                        $('#autoSave').val(0);
                                        App.blockUI({boxed: true});
                                    },
                                    success: function (res) {
                                        $('#showCmList').html(res.html);
                                        if (dataId == 2) {
                                            $('#autoSave').val(0);
                                        }
                                        $('#autoSave').val(res.autoSave);
                                        $('.js-source-states').select2();
                                        App.unblockUI();
                                    },
                                    error: function (jqXhr, ajaxOptions, thrownError) {
                                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                                        App.unblockUI();
                                    }
                                }); //ajax
                                App.unblockUI();
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
                                    toastr.error(jqXhr.responseJSON.message, 'Error', options);
                                } else {
                                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                                }
                                $('.button-submit').prop('disabled', false);
                                App.unblockUI();
                            }

                        });
                }
            });
        });

        //start :: auto save
        setInterval(function () {
            if ($('#autoSave').val() == 1) {
                var dataId = 1;
                var form_data = new FormData($('#submitForm')[0]);
                form_data.append('data_id', dataId);
                form_data.append('auto_saving', 1);
                $.ajax({
                    url: "<?php echo e(URL::to('eventAssessmentMarking/saveEventAssessmentMarking')); ?>",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    beforeSend: function () {
                        $('.button-submit').prop('disabled', true);
                        if (dataId == 2) {
                            $('#autoSave').val(0);
                        }
                        toastr.info("<?php echo app('translator')->get('label.SAVING'); ?>", "", options);
                    },
                    success: function (res) {
                        $('.button-submit').prop('disabled', false);
                        if (dataId == 2) {
                            $('#autoSave').val(0);
                        }
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
                            toastr.error(jqXhr.responseJSON.message, 'Error', options);
                        } else {
                            toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                        }
                        $('.button-submit').prop('disabled', false);
                        App.unblockUI();
                    }

                });
            }

        }, 30000);
        //end :: auto save


//Rquest for unlock
        $(document).on('click', '.request-for-unlock', function (e) {
            e.preventDefault();

            var form_data = new FormData($('#submitForm')[0]);

            $.ajax({
                url: "<?php echo e(URL::to('eventAssessmentMarking/getRequestForUnlockModal')); ?>",
                type: "POST",
                datatype: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                beforeSend: function () {
                    $('#showMessage').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showMessage').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
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
                        toastr.error(jqXhr.responseJSON.message, 'Error', options);
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }
                    App.unblockUI();
                }

            });
        });
//delete
        $(document).on('click', '#buttonDelete', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);

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
                        url: "<?php echo e(URL::to('eventAssessmentMarking/deleteEventAssessmentMarking')); ?>",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('#buttonDelete').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('#buttonDelete').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);

                            var courseId = res.loadData.course_id;
                            var termId = res.loadData.term_id;
                            var eventId = res.loadData.event_id;
                            var subEventId = res.loadData.sub_event_id;
                            var subSubEventId = res.loadData.sub_sub_event_id;
                            var subSubSubEventId = res.loadData.sub_sub_sub_event_id;
                            $.ajax({
                                url: "<?php echo e(URL::to('eventAssessmentMarking/showMarkingCmList')); ?>",
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
                                    if (subEventId == '0') {
                                        $('#showSubEventOrCmList').html('');
                                    }
                                    if (subSubEventId == '0') {
                                        $('#showSubSubEventOrCmList').html('');
                                    }
                                    if (subSubSubEventId == '0') {
                                        $('#showSubSubSubEventOrCmList').html('');
                                    }
                                    $('#showCmList').html('');
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showCmList').html(res.html);
                                    $('.js-source-states').select2();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
                                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                                    App.unblockUI();
                                }
                            });//ajax
                            App.unblockUI();
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
                                toastr.error(jqXhr.responseJSON.message, 'Error', options);
                            } else {
                                toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                            }
                            App.unblockUI();
                        }

                    });
                }
            });
        });
        $(document).on('click', '.save-request-for-unlock', function (e) {
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
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "<?php echo e(URL::to('eventAssessmentMarking/saveRequestForUnlock')); ?>",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            $('.modal').modal('hide');
                            toastr.success(res, '<?php echo app('translator')->get("label.REQUEST_FOR_UNLOCK_HAS_BEEN_SENT_TO_CI_SUCCESSFULLY"); ?>', options);

                            var courseId = res.loadData.course_id;
                            var termId = res.loadData.term_id;
                            var eventId = res.loadData.event_id;
                            var subEventId = res.loadData.sub_event_id;
                            var subSubEventId = res.loadData.sub_sub_event_id;
                            var subSubSubEventId = res.loadData.sub_sub_sub_event_id;
                            $.ajax({
                                url: "<?php echo e(URL::to('eventAssessmentMarking/showMarkingCmList')); ?>",
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
                                    if (subEventId == '0') {
                                        $('#showSubEventOrCmList').html('');
                                    }
                                    if (subSubEventId == '0') {
                                        $('#showSubSubEventOrCmList').html('');
                                    }
                                    if (subSubSubEventId == '0') {
                                        $('#showSubSubSubEventOrCmList').html('');
                                    }
                                    $('#showCmList').html('');
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showCmList').html(res.html);
                                    $('.js-source-states').select2();
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
                                toastr.error(jqXhr.responseJSON.message, 'Error', options);
                            } else {
                                toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                            }
                            App.unblockUI();
                        }

                    });
                }
            });
        });
    });
</script>
<script src="<?php echo e(asset('public/js/custom.js')); ?>" type="text/javascript"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/eventAssessmentMarking/index.blade.php ENDPATH**/ ?>