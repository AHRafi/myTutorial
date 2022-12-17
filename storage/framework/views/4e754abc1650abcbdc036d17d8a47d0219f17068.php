 
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.DS_REMARKS'); ?>
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="<?php echo e(URL::to('dsRemarks/create'.Helper::queryPageStr($qpArr))); ?>"> <?php echo app('translator')->get('label.CREATE_NEW_DS_REMARKS'); ?>
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => 'dsRemarks/filter','class' => 'form-horizontal')); ?>

            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?>:</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYearList->name); ?> </strong></div>
                            <?php echo Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?>:</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> <?php echo e($courseList->name); ?> </strong></div>
                            <?php echo Form::hidden('course_id', $courseList->id, ['id' => 'courseId']); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId"><?php echo app('translator')->get('label.TERM'); ?> :<span class="text-danger"> </span></label>
                        <div class="col-md-8">
                            <?php echo Form::select('term_id', $termList, Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('term_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cmId"><?php echo app('translator')->get('label.CM'); ?> :</label>
                        <div class="col-md-8">
                            <?php echo Form::select('cm_id', $cmList, Request::get('cm_id'), ['class' => 'form-control js-source-states', 'id' => 'cmId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('cm_id')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="maEventId"><?php echo app('translator')->get('label.EVENT'); ?> :</label>
                        <div class="col-md-8">
                            <?php echo Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('event_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="form">
                        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                            <i class="fa fa-search"></i> <?php echo app('translator')->get('label.FILTER'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <?php echo Form::close(); ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="max-height-500 table-responsive webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.SL'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.DATE'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.TERM'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.CM'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.EVENT'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.RMKS'); ?></th>
                                    <th class="vcenter text-center"><?php echo app('translator')->get('label.REMARKED_BY'); ?></th>
                                    <th class="td-actions text-center vcenter"><?php echo app('translator')->get('label.ACTION'); ?></th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!$targetArr->isEmpty()): ?>
                                <?php
                                $page = Request::get('page');
                                $page = empty($page) ? 1 : $page;
                                $sl = ($page - 1) * Session::get('paginatorCount');
                                ?>
                                <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $remarks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="vcenter text-center"><?php echo ++$sl; ?></td>
                                    <td class="vcenter"><?php echo e(!empty($remarks->date) ? Helper::formatDate($remarks->date) : ''); ?></td>
                                    <td class="vcenter"><?php echo $remarks->term; ?></td>
                                    <td class="vcenter"><?php echo $remarks->cm; ?></td>
                                    <td class="vcenter"><?php echo e($remarks->event); ?></td>
                                    <td class="vcenter"><?php echo e($remarks->remarks ?? ''); ?></td>
                                    <td class="vcenter text-center"><?php echo e($remarks->official_name); ?></td>
                                    <td class="td-actions text-center vcenter">
                                        <div class="width-inherit">
                                            <?php echo e(Form::open(array('url' => 'dsRemarks/' . $remarks->id.Helper::queryPageStr($qpArr)))); ?>

                                            <?php echo e(Form::hidden('_method', 'DELETE')); ?>


                                            <a class="btn btn-xs btn-primary tooltips " title="Edit" href="<?php echo e(URL::to('dsRemarks/' . $remarks->id . '/edit'.Helper::queryPageStr($qpArr))); ?>">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-xs btn-danger delete tooltips" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <?php echo e(Form::close()); ?>

                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="9"><strong><?php echo app('translator')->get('label.NO_DS_REMARKS_FOUND'); ?></strong></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php echo $__env->make('layouts.paginator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/dsRemarks/index.blade.php ENDPATH**/ ?>