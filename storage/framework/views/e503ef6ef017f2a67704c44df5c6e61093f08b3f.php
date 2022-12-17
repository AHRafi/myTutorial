<?php $__env->startSection('data_count'); ?>
    <div class="col-md-12">
        <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.LESSON_WISE_GS_FEEDBACK_FROM_DS'); ?>
                </div>
            </div>
            <div class="portlet-body">
                <?php echo Form::open([
                    'group' => 'form',
                    'url' => 'lessonWiseGsFeedbackFromDs/filter',
                    'class' => 'form-horizontal',
                    'id' => 'submitForm',
                ]); ?>


                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <?php echo Form::select('training_year_id', $activeTrainingYearList, Request::get('training_year_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'trainingYearId',
                                ]); ?>

                                <span class="text-danger"><?php echo e($errors->first('training_year_id')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <?php echo Form::select('course_id', $courseList, Request::get('course_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'courseId',
                                ]); ?>

                                <span class="text-danger"><?php echo e($errors->first('course_id')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="gsId"><?php echo app('translator')->get('label.GS'); ?> :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::select('gs_id', $gsList, Request::get('gs_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'gsId',
                                ]); ?>

                                <span class="text-danger"><?php echo e($errors->first('gs_id')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="lessonId"><?php echo app('translator')->get('label.LESSON'); ?> :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::select('lesson_id', $lessonList, Request::get('lesson_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'lessonId',
                                ]); ?>

                                <span class="text-danger"><?php echo e($errors->first('lesson_id')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="form-group">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn"
                                id="generateReport" value="Show Filter Info" data-id="1">
                                <i class="fa fa-search"></i> <?php echo app('translator')->get('label.GENERATE'); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <?php if(Request::get('generate') == 'true'): ?>
                    <?php if(!empty($dsDataList)): ?>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-md btn-primary vcenter" target="_blank" href="<?php echo URL::full() . '&view=print'; ?>">
                                    <span class="tooltips" title="<?php echo app('translator')->get('label.PRINT'); ?>"><i class="fa fa-print"></i> </span>
                                </a>
                                <!--                    <a class="btn btn-success vcenter" href="<?php echo URL::full() . '&view=pdf'; ?>">
                                                                <span class="tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_PDF'); ?>"><i class="fa fa-file-pdf-o"></i></span>
                                                            </a>-->
                                <a class="btn btn-warning vcenter" href="<?php echo URL::full() . '&view=excel'; ?>">
                                    <span class="tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_EXCEL'); ?>"><i class="fa fa-file-excel-o"></i>
                                    </span>
                                </a>

                                <label class="control-label" for="sortBy"><?php echo app('translator')->get('label.SORT_BY'); ?> :</label>&nbsp;

                                <label class="control-label" for="sortBy">
                                    <?php echo Form::select('sort', $sortByList, Request::get('sort'), ['class' => 'form-control', 'id' => 'sortBy']); ?>

                                </label>

                                <button class="btn green-jungle filter-btn" id="sortByHref" type="submit">
                                    <i class="fa fa-arrow-right"></i> <?php echo app('translator')->get('label.GO'); ?>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-blue-hoki bg-font-blue-hoki">
                                <h5 style="padding: 10px;">
                                    <?php echo e(__('label.TRAINING_YEAR')); ?> :
                                    <strong><?php echo e(!empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A')); ?>

                                        |</strong>
                                    <?php echo e(__('label.COURSE')); ?> :
                                    <strong><?php echo e(!empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A')); ?>

                                        |</strong>
                                    <?php echo e(__('label.GS')); ?> :
                                    <strong><?php echo e(!empty($gsList[Request::get('gs_id')]) && Request::get('gs_id') != 0 ? $gsList[Request::get('gs_id')] : __('label.N_A')); ?>

                                        |</strong>
                                    <?php echo e(__('label.LESSON')); ?> :
                                    <strong><?php echo e(!empty($lessonList[Request::get('lesson_id')]) && Request::get('lesson_id') != 0 ? $lessonList[Request::get('lesson_id')] : __('label.N_A')); ?>

                                    </strong>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <div class="webkit-scrollbar max-height-500">
                                <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.PERSONAL_NO'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.RANK'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.FULL_NAME'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.OFFICIAL_NAME'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.WING'); ?></th>
                                            <th class="vcenter"><?php echo app('translator')->get('label.PHOTO'); ?></th>

                                            <th class="vcenter text-center"><?php echo app('translator')->get('label.GRADING'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($dsDataList)): ?>
                                            <?php
                                            $sl = 0;
                                            ?>

                                            <?php $__currentLoopData = $dsDataList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="vcenter text-center"><?php echo ++$sl; ?></td>
                                                    <td class="vcenter"><?php echo !empty($target['personal_no']) ? $target['personal_no'] : ''; ?></td>
                                                    <td class="vcenter"><?php echo $target['rank'] ?? ''; ?></td>
                                                    <td class="vcenter"><?php echo $target['ds_name'] ?? ''; ?></td>
                                                    <td class="vcenter"><?php echo $target['official_name'] ?? ''; ?></td>
                                                    <td class="vcenter"><?php echo $target['wing'] ?? ''; ?></td>
                                                    <td class="vcenter text-center" width="50px">
                                                        <?php if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo'])): ?>
                                                            <img width="50" height="60"
                                                                src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($target['photo']); ?>"
                                                                alt="<?php echo e($target['official_name'] ?? ''); ?>" />
                                                        <?php else: ?>
                                                            <img width="50" height="60"
                                                                src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png"
                                                                alt="<?php echo e($target['official_name'] ?? ''); ?>" />
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="vcenter text-center"><?php echo !empty($target['grading']) && $target['grading'] != 0 ? $target['grading'] : ''; ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="11"><?php echo app('translator')->get('label.NO_DS_IS_ASSIGNED_TO_THIS_LESSON'); ?></td>
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
        $(function() {
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            //table header fix
            $(".table-head-fixer-color").tableHeadFixer({
                left: 5
            });

            $(document).on("change", "#trainingYearId", function() {
                var trainingYearId = $("#trainingYearId").val();
                if (trainingYearId == '0') {
                    $("#courseId").html("<option value='0'><?php echo app('translator')->get('label.SELECT_COURSE_OPT'); ?></option>");
                    $("#gsId").html("<option value='0'><?php echo app('translator')->get('label.SELECT_GS_OPT'); ?></option>");
                    return false;
                }
                $.ajax({
                    url: "<?php echo e(URL::to('lessonWiseGsFeedbackFromDs/getCourse')); ?>",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        training_year_id: trainingYearId
                    },
                    beforeSend: function() {
                        $("#gsId").html("<option value='0'><?php echo app('translator')->get('label.SELECT_GS_OPT'); ?></option>");
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#courseId').html(res.html);
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {}
                }); //ajax

            });
            //End::Get Course

            //Start::Get GS
            $(document).on("change", "#courseId", function() {


                var courseId = $("#courseId").val();
                if (courseId == '0') {
                    $("#gsId").html("<option value='0'><?php echo app('translator')->get('label.SELECT_GS_OPT'); ?></option>");
                    return false;
                }

                $.ajax({
                    url: "<?php echo e(URL::to('lessonWiseGsFeedbackFromDs/getGs')); ?>",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#gsId').html(res.html);
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {}
                }); //ajax

            });
            //End::Get Gs

            //Start::Get lesson
            $(document).on("change", "#gsId", function() {

                var gsId = $("#gsId").val();
                if (gsId == '0') {
                    $("#lessonId").html("<option value='0'><?php echo app('translator')->get('label.SELECT_LESSON_OPT'); ?></option>");
                    return false;
                }

                $.ajax({
                    url: "<?php echo e(URL::to('lessonWiseGsFeedbackFromDs/getLesson')); ?>",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        gs_id: gsId
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#lessonId').html(res.html);
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {}
                }); //ajax

            });
            //End::Get lesson
        });
    </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/lessonWiseGsFeedback/fromDs/index.blade.php ENDPATH**/ ?>