
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">

    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.MIL_QUAL_WISE_DS_ANALYTICS'); ?>
            </div>
        </div>

        <div class="portlet-body">
            <!-- Begin Filter-->
            <?php echo Form::open(array('group' => 'form', 'url' => 'milQualWiseDsAnalytics/filter','class' => 'form-horizontal')); ?>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="name"><?php echo app('translator')->get('label.NAME'); ?></label>
                        <div class="col-md-8">
                            <?php echo Form::text('name',  Request::get('name'), ['class' => 'form-control tooltips', 'id' => 'name', 'title' => 'Full Name/Official Name', 'placeholder' => 'Full Name/Official Name', 'list' => 'cmName', 'autocomplete' => 'off']); ?> 
                            <datalist id="cmName">
                                <?php if(!$nameArr->isEmpty()): ?>
                                <?php $__currentLoopData = $nameArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo $item->full_name; ?>" />
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </datalist>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="wingId"><?php echo app('translator')->get('label.WING'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('wing_id', $wingList,  Request::get('wing_id'), ['class' => 'form-control js-source-states', 'id' => 'wingId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('course_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="wingId"><?php echo app('translator')->get('label.APPT_AFWC'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('appt_id', $appointmentList,  Request::get('appt_id'), ['class' => 'form-control js-source-states', 'id' => 'apptId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('appt_id')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="rank"><?php echo app('translator')->get('label.RANK'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('rank_id', $rankList,  Request::get('rank_id'), ['class' => 'form-control js-source-states', 'id' => 'rank']); ?>

                            <span class="text-danger"><?php echo e($errors->first('course_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="armsService"><?php echo app('translator')->get('label.ARMS_SERVICE'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('arms_service_id', $armsServiceList,  Request::get('arms_service_id'), ['class' => 'form-control js-source-states', 'id' => 'armsService']); ?>

                            <span class="text-danger"><?php echo e($errors->first('course_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId"><?php echo app('translator')->get('label.MIL_COURSE'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('mil_course_id[]', $milCourseList, !empty(Request::get('mil_course_id'))?explode(',',Request::get('mil_course_id')):'' , [ 'id' => 'courseId', 'class' => 'form-control mt-multiselect btn btn-default',  'multiple' , 'data-select-all'=>"true", 'data-width' => '100%']); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="knResult"><?php echo app('translator')->get('label.KNOWLEDGE_BASED_RESULT_GRADE'); ?></label>
                        <div class="col-md-8">
                            <?php echo Form::select('kn_result', $knGradeList,  Request::get('kn_result'), ['class' => 'form-control js-source-states', 'id' => 'knResult']); ?>

                            <span class="text-danger"><?php echo e($errors->first('kn_result')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="instResult"><?php echo app('translator')->get('label.INSTRUCTION_BASED_RESULT_GRADE'); ?></label>
                        <div class="col-md-8">
                            <?php echo Form::select('inst_result', $instGradeList,  Request::get('inst_result'), ['class' => 'form-control js-source-states', 'id' => 'instResult']); ?>

                            <span class="text-danger"><?php echo e($errors->first('inst_result')); ?></span>
                        </div>
                    </div>
                </div>
                <!--                <div class="col-md-4">
                                    <div class="form-group">
                                            <label class="control-label col-md-4" for="result"><?php echo app('translator')->get('label.FOREIGN_COURSE'); ?></label>
                                            <div class="col-md-8">
                                                <div class="md-checkbox cm-matrix" >
                                                    <?php echo Form::checkbox('foreign_course', '1', Request::get('foreign_course')?? 0 ,['class' => 'form-control','id' => 'foreignCourse' , 'class'=> 'md-check ']); ?>

                                                    <label for="foreignCourse">
                                                        <span class="inc"></span>
                                                        <span class="check mark-caheck tooltips"></span>
                                                        <span class="box mark-caheck tooltips"></span>
                                                    </label>
                                                    <span class="padding-left-10">   <?php echo app('translator')->get('label.FOREIGN_COURSE_CHECK'); ?></span>
                                                </div>
                                            </div>
                                        </div>  
                                </div>-->

                <div class="col-md-4 text-center">
                    <div class="form-group">
                        <!--<label class="control-label col-md-4" for="">&nbsp;</label>-->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> <?php echo app('translator')->get('label.GENERATE'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($request->generate == 'true'): ?>
            <?php if(!empty($targetArr)): ?>
            <div class="row">
                <div class="col-md-12 text-right">
<!--                    <label class="control-label" for="printOption">
                        <?php echo Form::select('print_option', $printOptionList, Request::get('print_option'),['class' => 'form-control','id'=>'printOption']); ?>

                    </label>-->
                    <label class="control-label" for="columns">
                        <?php echo Form::select('columns[]', $columnArr, !empty(Request::get('columns')) ? explode(',',Request::get('columns')) : [], [ 'id' => 'columns', 'class' => 'form-control mt-multiselect btn btn-default',  'multiple' , 'data-select-all'=>"true"]); ?>


                    </label>

                    <a class="btn btn-md print btn-primary vcenter" target="_blank"  href="<?php echo URL::full().'&view=print&columns='; ?>">
                        <span class="tooltips" title="<?php echo app('translator')->get('label.PRINT'); ?>"><i class="fa fa-print"></i> </span> 
                    </a>



                    <!--                                        <a class="btn btn-success vcenter" href="<?php echo URL::full().'&view=pdf'; ?>">
                                                                <span class="tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_PDF'); ?>"><i class="fa fa-file-pdf-o"></i></span>
                                                            </a>-->
                    <a class="btn btn-warning excel vcenter" href="<?php echo URL::full().'&view=excel&columns='; ?>">
                        <span class="tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_EXCEL'); ?>"><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                    <label class="control-label" for="sortBy"><?php echo app('translator')->get('label.SORT_BY'); ?> :</label>&nbsp;

                    <label class="control-label" for="sortBy">
                        <?php echo Form::select('sort', $sortByList, Request::get('sort'),['class' => 'form-control','id'=>'sortBy']); ?>

                    </label>

                    <button class="btn green-jungle filter-btn"  id="sortByHref" type="submit">
                        <i class="fa fa-arrow-right"></i>  <?php echo app('translator')->get('label.GO'); ?>
                    </button>
                </div>
            </div>
            <?php endif; ?>
            <?php echo Form::close(); ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            <?php echo e(__('label.NAME')); ?> : <strong><?php echo e(!empty(Request::get('name')) && Request::get('name') != '' ? Request::get('name') : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.SERVICE')); ?> : <strong><?php echo e(!empty($wingList[Request::get('wing_id')]) && Request::get('wing_id') != 0 ? $wingList[Request::get('wing_id')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.RANK')); ?> : <strong><?php echo e(!empty($rankList[Request::get('rank_id')]) && Request::get('rank_id') != 0 ? $rankList[Request::get('rank_id')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.ARMS_SERVICE')); ?> : <strong><?php echo e(!empty($armsServiceList[Request::get('arms_service_id')]) && Request::get('arms_service_id') != 0 ? $armsServiceList[Request::get('arms_service_id')] : __('label.ALL')); ?> |</strong>

                            <?php echo e(__('label.KNOWLEDGE_BASED_RESULT_GRADE')); ?> : <strong><?php echo e(!empty(Request::get('kn_result')) && Request::get('kn_result') != '' ? Request::get('kn_result') : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.INSTRUCTION_BASED_RESULT_GRADE')); ?> : <strong><?php echo e(!empty(Request::get('inst_result')) && Request::get('inst_result') != '' ? Request::get('inst_result') : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.TOTAL_NO_OF_DS')); ?> : <strong><?php echo e(!empty($targetArr) ? sizeof($targetArr) : 0); ?></strong>

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
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.SERIAL'); ?></th>

                                    <th class="vcenter"><?php echo app('translator')->get('label.PERSONAL_NO'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.RANK'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.FULL_NAME'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.PHOTO'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.ARMS_SERVICE'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.APPT_AFWC'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.INSTITUTE_NAME'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.MIL_COURSE'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.RESULT'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.FROM'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.TO'); ?></th>


<!--                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.PROFILE_DETAILS'); ?></th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($targetArr)): ?>
                                <?php
                                $sl = 0;
                                ?>

                                <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <tr>
                                    <td class="vcenter text-center" rowspan="<?php echo e(!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1); ?>">
                                        <?php echo ++$sl; ?>

                                    </td>

                                     <td class="vcenter" rowspan="<?php echo e(!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1); ?>">
                                        <?php echo Common::getFurnishedCmName($target['personal_no']); ?>

                                    </td>
                                    <td class="vcenter" rowspan="<?php echo e(!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1); ?>">
                                        <?php echo Common::getFurnishedCmName($target['rank']); ?>

                                    </td>
                                    <td class="vcenter" rowspan="<?php echo e(!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1); ?>">
                                        <?php echo Common::getFurnishedCmName($target['full_name']); ?>

                                    </td>

                                    <td class="vcenter text-center" width="50px" rowspan="<?php echo e(!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1); ?>">
                                        <?php if(!empty($target['photo']) && File::exists('public/uploads/user/' . $target['photo'])): ?>
                                        <img class="profile-zoom" width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/user/<?php echo e($target['photo']); ?>" alt="<?php echo e($target['official_name']?? ''); ?>"/>
                                        <?php else: ?>
                                        <img class="profile-zoom" width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo e($target['official_name']?? ''); ?>"/>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vcenter" rowspan="<?php echo e(!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1); ?>">
                                        <?php echo $target['arms_service_name'] ?? ''; ?>

                                    </td>
                                    <td class="vcenter" rowspan="<?php echo e(!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1); ?>">
                                        <?php echo $target['appointment_name']?? ''; ?>

                                    </td>




                                    <?php if(!empty($target['rec_svc'])): ?>
                                    <?php $i = 0; ?>
                                    <?php $__currentLoopData = $target['rec_svc']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rsKey => $rsInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    if ($i > 0) {
                                        echo '<tr>';
                                    }
                                    ?>
                                    <td class="vcenter"><?php echo $rsInfo['institute_name'] ?? ''; ?></td>
                                    <td class="vcenter"><?php echo $rsInfo['course'] ?? ''; ?></td>
                                    <td class="vcenter"><?php echo $rsInfo['result'] ?? ''; ?></td>
                                    <td class="vcenter"><?php echo $rsInfo['from'] ?? ''; ?></td>
                                    <td class="vcenter"><?php echo $rsInfo['to'] ?? ''; ?></td>

                                    <?php
                                    if ($i < ($target['rec_svc_span'] - 1)) {
                                        echo '</tr>';
                                    }
                                    $i++;
                                    ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?> 
                                    <td class="vcenter"></td>
                                    <td class="vcenter"></td>
                                    <td class="vcenter"></td>
                                    <td class="vcenter"></td>
                                    <td class="vcenter"></td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="11"><?php echo app('translator')->get('label.NO_DS_FOUND'); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>


<script type="text/javascript">

    $(function () {
        //table header fix
        $(".table-head-fixer-color").tableHeadFixer();

     $('#columns').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            nonSelectedText: "<?php echo app('translator')->get('label.SELECT_FIELD'); ?>",
//        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                courseAllSelected = true;
            },
            onChange: function () {
                courseAllSelected = false;
            }
        });

        $("#columns").on('change', function () {
            var columns = $(this).val();
            if(columns == null){
                return false;
            }
            columns = columns.toString();
            
            var hrefPrint = "<?php echo URL::full() . '&view=print&columns='; ?>" + columns;
            var hrefExcel = "<?php echo URL::full() . '&view=excel&columns='; ?>" + columns;
            $('.print').attr('href', hrefPrint);
            $('.excel').attr('href', hrefExcel);
        });

    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/dsAnalytics/milQualInfo/index.blade.php ENDPATH**/ ?>