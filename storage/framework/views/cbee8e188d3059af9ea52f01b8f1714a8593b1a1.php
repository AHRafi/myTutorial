
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.COMMISSIONING_COURSE_WISE_CM_ANALYTICS'); ?>
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            <?php echo Form::open(array('group' => 'form', 'url' => 'comCourseWiseCmAnalytics/filter','class' => 'form-horizontal')); ?>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('course_id[]', $courseList, !empty(Request::get('course_id'))?explode(',',Request::get('course_id')):'' , [ 'id' => 'courseId', 'class' => 'form-control mt-multiselect btn btn-default',  'multiple' , 'data-select-all'=>"true", 'data-width' => '100%']); ?>

                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="name"><?php echo app('translator')->get('label.NAME'); ?></label>
                        <div class="col-md-8">
                            <?php echo Form::text('name',  Request::get('name'), ['class' => 'form-control tooltips', 'id' => 'name', 'title' => 'Full Name/Official Name', 'placeholder' => 'Full Name/Official Name', 'list' => 'cmName', 'autocomplete' => 'off']); ?> 
                            <datalist id="cmName">
                                <?php if(!$nameArr->isEmpty()): ?>
                                <?php $__currentLoopData = $nameArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo $item->official_name; ?>" />
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
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="rank"><?php echo app('translator')->get('label.RANK'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('rank_id', $rankList,  Request::get('rank_id'), ['class' => 'form-control js-source-states', 'id' => 'rank']); ?>

                            <span class="text-danger"><?php echo e($errors->first('rank_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="armsService"><?php echo app('translator')->get('label.ARMS_SERVICE'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('arms_service_id', $armsServiceList,  Request::get('arms_service_id'), ['class' => 'form-control js-source-states', 'id' => 'armsService']); ?>

                            <span class="text-danger"><?php echo e($errors->first('arms_service_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="comCourse"><?php echo app('translator')->get('label.COMMISSIONING_COURSE'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('com_course_id', $comCourseList,  Request::get('com_course_id'), ['class' => 'form-control js-source-states', 'id' => 'comCourse']); ?>

                            <span class="text-danger"><?php echo e($errors->first('com_course_id')); ?></span>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4"><?php echo app('translator')->get('label.COMMISSIONING_DATE_FROM'); ?> </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                <?php echo Form::text('commissioning_date_from',Request::get('commissioning_date_from') ?? null, ['class' => 'form-control', 'id' => 'docmFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']); ?> 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="docmFrom">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4"><?php echo app('translator')->get('label.COMMISSIONING_DATE_TO'); ?> </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2 ">
                                <?php echo Form::text('commissioning_date_to',Request::get('commissioning_date_to') ?? null, ['class' => 'form-control', 'id' => 'docmTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']); ?> 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="docmTo">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commissionType"><?php echo app('translator')->get('label.COMMISSIONING_TYPE'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('com_type_id', $commissionTypeList,  Request::get('com_type_id'), ['class' => 'form-control js-source-states', 'id' => 'commissionType']); ?>

                            <span class="text-danger"><?php echo e($errors->first('com_type_id')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-12 text-center">
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

            <!--filter form close-->

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
                            <?php echo e(__('label.RANK')); ?> : <strong><?php echo e(!empty($rankList[Request::get('rank')]) && Request::get('rank_id') != 0 ? $rankList[Request::get('rank')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.ARMS_SERVICE')); ?> : <strong><?php echo e(!empty($armsServiceList[Request::get('arms_service')]) && Request::get('arms_service') != 0 ? $armsServiceList[Request::get('arms_service')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.COMMISSIONING_COURSE')); ?> : <strong><?php echo e(!empty($comCourseList[Request::get('com_course_id')]) && Request::get('com_course_id') != 0 ? $comCourseList[Request::get('com_course_id')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.COMMISSIONING_DATE_FROM')); ?> : <strong><?php echo e(!empty(Request::get('commissioning_date_from')) && Request::get('commissioning_date_from') != '' ? Request::get('commissioning_date_from') : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.COMMISSIONING_DATE_TO')); ?> : <strong><?php echo e(!empty(Request::get('commissioning_date_to')) && Request::get('commissioning_date_to') != '' ? Request::get('commissioning_date_to') : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.COMMISSIONING_TYPE')); ?> : <strong><?php echo e(!empty($commissionTypeList[Request::get('com_type_id')]) && Request::get('com_type_id') != 0 ? $commissionTypeList[Request::get('com_type_id')] : __('label.ALL')); ?> |</strong>

                            <?php echo e(__('label.TOTAL_NO_OF_CM')); ?> : <strong><?php echo e(!empty($targetArr) ? sizeof($targetArr) : 0); ?></strong>

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
                                    <th class="vcenter"><?php echo app('translator')->get('label.AFWC_COURSE_NAME'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.COMMISSIONING_COURSE'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.COMMISSIONING_DATE'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.COMMISSIONING_TYPE'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($targetArr)): ?>
                                <?php
                                $sl = 0;
                                ?>

                                <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="vcenter text-center"><?php echo ++$sl; ?></td>

                                    <td class="vcenter"><?php echo Common::getFurnishedCmName($target['personal_no']); ?></td>
                                    <td class="vcenter"><?php echo Common::getFurnishedCmName($target['rank']); ?></td>
                                    <td class="vcenter"><?php echo Common::getFurnishedCmName($target['full_name']); ?></td>

                                    <td class="vcenter text-center" width="50px">
                                        <?php if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo'])): ?>
                                        <img class="profile-zoom" width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($target['photo']); ?>" alt="<?php echo e($target['official_name']?? ''); ?>"/>
                                        <?php else: ?>
                                        <img class="profile-zoom" width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo e($target['official_name']?? ''); ?>"/>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vcenter"><?php echo $target['arms_service_name'] ?? ''; ?></td>
                                    <td class="vcenter"><?php echo $target['course_name']?? ''; ?></td>
                                    <td class="vcenter"><?php echo !empty($target['commissioning_course_id']) && !empty($comCourseList[$target['commissioning_course_id']]) ? $comCourseList[$target['commissioning_course_id']]: ''; ?></td>
                                    <td class="vcenter text-center"><?php echo !empty($target['commisioning_date']) ? Helper::formatDate($target['commisioning_date']): ''; ?></td>
                                    <td class="vcenter text-center"><?php echo !empty($target['commission_type']) && !empty($commissionTypeList[$target['commission_type']]) ? $commissionTypeList[$target['commission_type']] : ''; ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="11"><?php echo app('translator')->get('label.NO_CM_FOUND'); ?></td>
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
        var courseAllSelected = false;
        $('#courseId').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            nonSelectedText: "<?php echo app('translator')->get('label.SELECT_COURSE'); ?>",
//        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                courseAllSelected = true;
            },
            onChange: function () {
                courseAllSelected = false;
            }
        });
        
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
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/cmAnalytics/comCourseInfo/index.blade.php ENDPATH**/ ?>