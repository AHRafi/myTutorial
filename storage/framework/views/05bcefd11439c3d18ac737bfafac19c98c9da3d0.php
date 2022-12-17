<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.SUBJECT_TO_LESSON'); ?>
            </div>
        </div>

        <div class="portlet-body">
            <div class="row">
                <div class="col-md-12">


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
                            <label class="control-label col-md-3" for="subjectId"><?php echo app('translator')->get('label.SUBJECT'); ?></label>
                            <div class="col-md-9">
                                <?php echo Form::select('subject_id', $subjectList, null, ['class' => 'form-control js-source-states', 'id' => 'subjectId']); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <div id="lessonList">
                        </div>
                    </div>
                </div>



            </div>


        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(document).on("change", "#subjectId", function () {
            var subjectId = $("#subjectId").val();
            var courseId = $("#courseId").val();
            
            $('#lessonList').html('');
            if(subjectId == '0'){
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "<?php echo e(URL::to('subjectToLesson/getLessonList')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    subject_Id: subjectId,
                    course_id: courseId,
                },
                beforeSend: function () {
                    App.blockUI({
                        boxed: true
                    });
                },
                success: function (res) {
                    $('#lessonList').html(res.html);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('<?php echo app('translator')->get('label.SOMETHING_WENT_WRONG'); ?>', 'Error', options);
                            App.unblockUI();
                }
            }
            ); //ajax
        });

        $(document).on('click', '#lessonBtn', function (e) {
            e.preventDefault();
            var oTable = $('#dataTable').dataTable();
            var x = oTable.$('input,select,textarea').serializeArray();
            $.each(x, function (i, field) {

                $("#submitLessonForm").append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', field.name)
                        .val(field.value));

            });
            var form_data = new FormData($('#submitLessonForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            swal({
                title: 'Are you sure?',
                text: "<?php echo app('translator')->get('label.YOU_WANT_TO_ADD_MODULE'); ?>",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "<?php echo e(route('subjectToLesson.saveLesson')); ?>",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            $("#assignedLesson").html("<?php echo app('translator')->get('label.LESSONS_RELATED_TO_THIS_SUBJECT'); ?>: " + res.prevLessonNumber);
                            $(".total-related-lessons").html("<?php echo app('translator')->get('label.LESSONS_RELATED_TO_THIS_SUBJECT'); ?>: " + res.prevAllLessonNumber);
                            toastr.success(res.message, res.heading, options);
                        },
                        error: function (jqXhr, ajaxOptions, thrownError) {
                            if (jqXhr.status == 400) {
                                var errorsHtml = '';
                                var errors = jqXhr.responseJSON.message;
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value[0] +
                                            '</li>';
                                });
                                toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                        options);
                            } else if (jqXhr.status == 401) {
                                toastr.error(jqXhr.responseJSON.message, 'Error',
                                        options);
                            } else {
                                toastr.error('Something went wrong', 'Error',
                                        options);
                            }
                            App.unblockUI();
                        }
                    });
                }

            });

        });

        $(document).on('click', '#assignedLesson', function (e) {
            e.preventDefault();

            var subjectId = $("#subjectId").val();
            var courseId = $("#courseId").val();

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "<?php echo e(route('subjectToLesson.getAssignedLesson')); ?>",
                type: "POST",
                datatype: 'json',
                data: {
                    subject_id: subjectId,
                    course_id: courseId,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('#placeAssignedLesson').html(res.html);
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
                        toastr.error('Something went wrong', 'Error', options);
                    }
                    App.unblockUI();
                }
            });

        });

    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/subjectToLesson/index.blade.php ENDPATH**/ ?>