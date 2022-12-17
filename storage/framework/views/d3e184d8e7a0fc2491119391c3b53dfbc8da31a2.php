<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.BASIC_INFORMATION_WISE_CM_ANALYTICS'); ?>
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            <?php echo Form::open(array('group' => 'form', 'url' => 'basicInfoWiseCmAnalytics/filter','class' => 'form-horizontal')); ?>

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
                        <label class="control-label col-md-4" for="bloodGroup"><?php echo app('translator')->get('label.BLOOD_GROUP'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('blood_group', $bloodGroupList,  Request::get('blood_group'), ['class' => 'form-control js-source-states', 'id' => 'bloodGroup']); ?>

                            <span class="text-danger"><?php echo e($errors->first('blood_group')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4"><?php echo app('translator')->get('label.BIRTH_DATE_FROM'); ?> </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                <?php echo Form::text('birth_date_from',Request::get('birth_date_from') ?? null, ['class' => 'form-control', 'id' => 'dobFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']); ?>

                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="dobFrom">
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
                        <label class="control-label col-md-4"><?php echo app('translator')->get('label.BIRTH_DATE_TO'); ?> </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2 ">
                                <?php echo Form::text('birth_date_to',Request::get('birth_date_to') ?? null, ['class' => 'form-control', 'id' => 'dobTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']); ?>

                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="dobTo">
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
                        <label class="control-label col-md-4" for="religion"><?php echo app('translator')->get('label.RELIGION'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('religion', $religionList,  Request::get('religion'), ['class' => 'form-control js-source-states', 'id' => 'religion']); ?>

                            <span class="text-danger"><?php echo e($errors->first('religion')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 ">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="gender"><?php echo app('translator')->get('label.GENDER'); ?> </label>
                        <div class="col-md-8">
                            <?php echo Form::select('gender', $genderList,  Request::get('gender'), ['class' => 'form-control js-source-states', 'id' => 'gender']); ?>

                            <span class="text-danger"><?php echo e($errors->first('gender')); ?></span>
                        </div>
                    </div>
                </div>
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
                    <!--<label class="control-label" for="printOption">
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
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            <?php echo e(__('label.NAME')); ?> : <strong><?php echo e(!empty(Request::get('name')) && Request::get('name') != '' ? Request::get('name') : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.SERVICE')); ?> : <strong><?php echo e(!empty($wingList[Request::get('wing_id')]) && Request::get('wing_id') != 0 ? $wingList[Request::get('wing_id')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.RANK')); ?> : <strong><?php echo e(!empty($rankList[Request::get('rank')]) && Request::get('rank_id') != 0 ? $rankList[Request::get('rank')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.ARMS_SERVICE')); ?> : <strong><?php echo e(!empty($armsServiceList[Request::get('arms_service')]) && Request::get('arms_service') != 0 ? $armsServiceList[Request::get('arms_service')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.BLOOD_GROUP')); ?> : <strong><?php echo e(!empty($bloodGroupList[Request::get('blood_group')]) && Request::get('blood_group') != 0 ? $bloodGroupList[Request::get('blood_group')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.BIRTH_DATE_FROM')); ?> : <strong><?php echo e(!empty(Request::get('birth_date_from')) && Request::get('birth_date_from') != '' ? Request::get('birth_date_from') : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.BIRTH_DATE_TO')); ?> : <strong><?php echo e(!empty(Request::get('birth_date_to')) && Request::get('birth_date_to') != '' ? Request::get('birth_date_to') : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.RELIGION')); ?> : <strong><?php echo e(!empty($religionList[Request::get('religion')]) && Request::get('religion') != 0 ? $religionList[Request::get('religion')] : __('label.ALL')); ?> |</strong>
                            <?php echo e(__('label.GENDER')); ?> : <strong><?php echo e(!empty($genderList[Request::get('gender')]) && Request::get('gender') != 0 ? $genderList[Request::get('gender')] : __('label.ALL')); ?> |</strong>
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
                                    <th class="vcenter"><?php echo app('translator')->get('label.FULL_NAME_BANGLA'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.PHOTO'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.ARMS_SERVICE'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.AFWC_COURSE_NAME'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.EMAIL'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.MOBILE'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.BLOOD_GROUP'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.DATE_OF_BIRTH'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.RELIGION'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.GENDER'); ?></th>

<!--                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.PROFILE_DETAILS'); ?></th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($targetArr)): ?>
                                <?php
                                $sl = 0;
                                $cmGroupId = null;
                                ?>

                                <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <tr>
                                    <td class="vcenter text-center"><?php echo ++$sl; ?></td>

                                    <td class="vcenter"><?php echo Common::getFurnishedCmName($target['personal_no']); ?></td>
                                    <td class="vcenter"><?php echo Common::getFurnishedCmName($target['rank']); ?></td>
                                    <td class="vcenter"><?php echo Common::getFurnishedCmName($target['full_name']); ?></td>
                                    <td class="vcenter"><?php echo $target['bn_name']; ?></td>

                                    <td class="vcenter text-center" width="50px">
                                        <?php if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo'])): ?>
                                        <img class="profile-zoom" width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($target['photo']); ?>" alt="<?php echo e($target['official_name']?? ''); ?>"/>
                                        <?php else: ?>
                                        <img class="profile-zoom" width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo e($target['official_name']?? ''); ?>"/>
                                        <?php endif; ?>
                                    </td>
                                    <td class="vcenter"><?php echo $target['arms_service_name'] ?? ''; ?></td>
                                    <td class="vcenter"><?php echo $target['course_name']?? ''; ?></td>
                                    <td class="vcenter"><?php echo $target['email'] ?? ''; ?></td>
                                    <td class="vcenter"><?php echo $target['number'] ?? ''; ?></td>
                                    <td class="vcenter text-center"><?php echo !empty($target['blood_group']) && !empty($bloodGroupList[$target['blood_group']]) ? $bloodGroupList[$target['blood_group']] : ''; ?></td>
                                    <td class="vcenter text-center"><?php echo !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : ''; ?></td>
                                    <td class="vcenter text-center"><?php echo !empty($target['religion_id']) && !empty($religionList[$target['religion_id']]) ? $religionList[$target['religion_id']] : ''; ?></td>
                                    <td class="vcenter text-center" ><?php echo !empty($target['gender']) && !empty($genderList[$target['gender']]) ? $genderList[$target['gender']] : ''; ?></td>

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

            <?php echo Form::close(); ?>

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

<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/cmAnalytics/basicInfo/index.blade.php ENDPATH**/ ?>