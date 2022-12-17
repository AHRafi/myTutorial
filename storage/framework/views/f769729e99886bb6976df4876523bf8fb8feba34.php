 
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.IMPORT_MARKING_SHEET'); ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal', 'id' => 'submitForm', 'files' => true)); ?>

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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label col-md-6" for="termId"><?php echo app('translator')->get('label.TERM'); ?> :<span class="text-danger"> *</span></label>
                                <div class="col-md-6">
                                    <div class="control-label pull-left"><strong><?php echo e($activeTerm->name); ?></strong></div>
                                    <?php echo Form::hidden('term_id', $activeTerm->id, ['id'=>'termId']); ?>

                                </div>
                            </div>
                        </div>
                        <?php if(!empty($maProcess)): ?>
                        <?php echo Form::hidden('ma_process', $maProcess, ['id' => 'maProcess']); ?>

                        <?php if($maProcess == '1'): ?>
                        <?php if(sizeof($synList)>1): ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="synId"><?php echo app('translator')->get('label.SYN'); ?> :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    <?php echo Form::select('syn_id', $synList, null, ['class' => 'form-control js-source-states', 'id' => 'synId']); ?>

                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_SYN_IS_ASSIGNED_TO_THIS_COURSE'); ?></strong></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php elseif($maProcess == '2'): ?>
                        <?php if(sizeof($subSynList)>1): ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="subSynId"><?php echo app('translator')->get('label.SUB_SYN'); ?> :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    <?php echo Form::select('sub_syn_id', $subSynList, null, ['class' => 'form-control js-source-states', 'id' => 'subSynId']); ?>

                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_SUB_SYN_IS_ASSIGNED_TO_THIS_COURSE'); ?></strong></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php elseif($maProcess == '3'): ?>
                        <?php if(sizeof($eventsList)>1): ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="eventId"><?php echo app('translator')->get('label.EVENT'); ?> :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    <?php echo Form::select('event_id', $eventsList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']); ?>

                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_TERM'); ?></strong></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.MA_PROCESS_IS_NOT_SET_YET'); ?></strong></p>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                    <div class="row">
                        <div id="showSubEvent">

                        </div>
                        <div id="showSubSubEvent">

                        </div>
                        <div id="showSubSubSubEvent">

                        </div>
                        <div id="showEventGroup">

                        </div>
                        <div id="showFactor">

                        </div>
                        <div id="cmList">

                        </div>
                    </div>
                    <div class="row">
                        <div id="synList"></div>
                        <div id="subSyndicateList"></div>

                    </div>

                </div>

                <div id="fileUpload"></div>
                <div id="cmContainer"></div>
                <div id="prvMarkingSheet" class="mt-15"></div>

            </div>
        </div>

        <?php echo Form::close(); ?>

    </div>
</div>


<!--Assigned Sub Event list-->
<div class="modal fade" id="markingSheetModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div id="markingSheetContainer">

        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null,
        };
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == '0') {
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/getTerm')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    $('#activeTerm').html('');
                    $('#cmContainer').html('');
                    $('#synList').html('');
                    $('#subSyndicateList').html('');
                    $('#fileUpload').html('');
                    $('#prvMarkingSheet').html('');
                    $('#cmList').html('');

                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#activeTerm').html(res.html);
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


        $(document).on("change", "#eventId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();

            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');
            $('#showFactor').html('');
            $('#cmList').html('');
            $('#fileUpload').html('');
            $('#cmContainer').html('');

            if (eventId == '0') {
                return false;
            }

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/getSubEvent')); ?>",
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
                success: function (res) {
                    $('#showSubEvent').html(res.html);
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

        $(document).on("change", "#subEventId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();

            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');
            $('#showFactor').html('');
            $('#cmList').html('');
            $('#fileUpload').html('');
            $('#cmContainer').html('');


            if (subEventId == '0') {
                return false;
            }

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/getSubSubEvent')); ?>",
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
                success: function (res) {
                    $('#showSubSubEvent').html(res.html);
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

        $(document).on("change", "#subSubEventId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();

            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');
            $('#showFactor').html('');
            $('#cmList').html('');
            $('#fileUpload').html('');
            $('#cmContainer').html('');


            if (subSubEventId == '0') {
                return false;
            }

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/getSubSubSubEvent')); ?>",
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
                success: function (res) {
                    $('#showSubSubSubEvent').html(res.html);
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
        $(document).on("change", "#subSubSubEventId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();

            $('#showEventGroup').html('');
            $('#showFactor').html('');
            $('#cmList').html('');
            $('#fileUpload').html('');
            $('#cmContainer').html('');


            if (subSubSubEventId == '0') {
                return false;
            }

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/getActDeact')); ?>",
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
                    $('#showEventGroup').html(res.html);
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
        
        $(document).on("change", "#eventGroupId, #synId, #subSynId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var synId = $("#synId").val();
            var subSynId = $("#subSynId").val();
            var eventGroupId = $("#eventGroupId").val();
            
            $('#cmList').html('');
            $('#fileUpload').html('');
            $('#cmContainer').html('');
            
            if (eventGroupId == '0' || synId == '0' || subSynId == '0') {
                return false;
            }


            if (typeof synId == 'undefined') {
                synId = '0';
            }
            if (typeof subSynId == 'undefined') {
                subSynId = '0';
            }
            if (typeof eventId == 'undefined') {
                eventId = '0';
            }
            if (typeof subEventId == 'undefined') {
                subEventId = '0';
            }
            if (typeof subSubEventId == 'undefined') {
                subSubEventId = '0';
            }
            if (typeof subSubSubEventId == 'undefined') {
                subSubSubEventId = '0';
            }
            if (typeof eventGroupId == 'undefined') {
                eventGroupId = '0';
            }

            var maProcess = $("#maProcess").val();
            
            var cmGroupId = 0;
            if (maProcess == '1') {
                cmGroupId = synId;
            } else if (maProcess == '2') {
                cmGroupId = subSynId;
            }


            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/getSubsynAndCmList')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    syn_id: synId,
                    sub_syn_id: subSynId,
                    cm_group_id: cmGroupId,
                    event_group_id: eventGroupId,
                    ma_process: maProcess,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#cmList').html(res.cmList);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#cmId", function () {
            var cmId = $(this).val();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var synId = $("#synId").val();
            var subSynId = $("#subSynId").val();
            var eventGroupId = $("#eventGroupId").val();

            if (typeof synId == 'undefined') {
                synId = '0';
            }
            if (typeof subSynId == 'undefined') {
                subSynId = '0';
            }
            if (typeof eventId == 'undefined') {
                eventId = '0';
            }
            if (typeof subEventId == 'undefined') {
                subEventId = '0';
            }
            if (typeof subSubEventId == 'undefined') {
                subSubEventId = '0';
            }
            if (typeof subSubSubEventId == 'undefined') {
                subSubSubEventId = '0';
            }
            if (typeof eventGroupId == 'undefined') {
                eventGroupId = '0';
            }

            var maProcess = $("#maProcess").val();
            $('#fileUpload').html('');
            $('#cmContainer').html('');
            if (cmId == 0) {
                return false;
            }

            var cmGroupId = 0;
            if (maProcess == '1') {
                cmGroupId = synId;
            } else if (maProcess == '2') {
                cmGroupId = subSynId;
            }
            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/getFileUploader')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    syn_id: synId,
                    sub_syn_id: subSynId,
                    cm_group_id: cmGroupId,
                    event_group_id: eventGroupId,
                    ma_process: maProcess,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                    cm_id: cmId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#fileUpload').html(res.fileUpload);
                    $('#cmContainer').html(res.markingSheet);
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }
                    App.unblockUI();
                }
            });//ajax
        });


        $(document).on("click", "#import", function () {
            var fromData = new FormData($('#submitForm')[0]);
            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/import')); ?>",
                type: "POST",
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fromData,
                beforeSend: function () {
                    $(this).prop("disabled", true);
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#cmContainer').html(res.markingSheet);
                    $("#import").prop("disabled", false);
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    $("#import").prop("disabled", false);
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                    } else if (jqXhr.status == 401) {
                        var errors = jqXhr.responseJSON.errormessage;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }
                    App.unblockUI();
                }
            });//ajax
        });



        $(document).on("click", ".submit-form", function () {
            var saveStatus = $(this).attr('data-id');
            var formData = new FormData($('#submitForm')[0]);
            formData.append('save_status', saveStatus);
            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/saveImportedData')); ?>",
                type: "POST",
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                beforeSend: function () {
                    $(this).prop("disabled", true);
                    App.blockUI({boxed: true});
                },
                success: function (response) {
                    $("#submitImportedData").prop("disabled", false);

//               $("#import").trigger('click');
                    if (response.saveStatus == 2) {
                        $('.buttonHide').hide();
                    }
                    App.unblockUI();
                    toastr.success(response.message, response.heading, options);
                    setInterval(function () {
                        // write statement
                    }, 8000)
//                location.reload();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                    } else if (jqXhr.status == 401) {
                        var errors = jqXhr.responseJSON.errormessage;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }
                    $("#submitImportedData").prop("disabled", false);
                    App.unblockUI();
                }
            });//ajax
        });


        function getCmList() {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var synId = $("#synId").val();
            var subSynId = $("#subSynId").val();
            var eventGroupId = $("#eventGroupId").val();
            var factorId = $("#factorId").val();

            if (typeof synId == 'undefined') {
                synId = '0';
            }
            if (typeof subSynId == 'undefined') {
                subSynId = '0';
            }
            if (typeof eventId == 'undefined') {
                eventId = '0';
            }
            if (typeof subEventId == 'undefined') {
                subEventId = '0';
            }
            if (typeof subSubEventId == 'undefined') {
                subSubEventId = '0';
            }
            if (typeof subSubSubEventId == 'undefined') {
                subSubSubEventId = '0';
            }
            if (typeof eventGroupId == 'undefined') {
                eventGroupId = '0';
            }

            var maProcess = $("#maProcess").val();
            $('#cmContainer').html('');
            if (factorId == 0) {
                return false;
            }

            var cmGroupId = 0;
            if (maProcess == '1') {
                cmGroupId = synId;
            } else if (maProcess == '2') {
                cmGroupId = subSynId;
            }


            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessment/getCmAndSubSyndicate')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    syn_id: synId,
                    sub_syn_id: subSynId,
                    cm_group_id: cmGroupId,
                    event_group_id: eventGroupId,
                    ma_process: maProcess,
                    factor_id: factorId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#cmContainer').html(res.cmList);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        }
    });

</script>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/mutualAssessment/importMarkingSheet.blade.php ENDPATH**/ ?>