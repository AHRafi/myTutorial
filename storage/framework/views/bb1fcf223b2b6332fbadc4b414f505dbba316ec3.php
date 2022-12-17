
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.CONSIDERATION_LIST'); ?>
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="<?php echo e(URL::to('considerations/create'.Helper::queryPageStr($qpArr))); ?>"> <?php echo app('translator')->get('label.CREATE_NEW_CONSIDERATION'); ?>
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <!-- Begin Filter-->
                <?php echo Form::open(array('group' => 'form', 'url' => 'considerations/filter','class' => 'form-horizontal')); ?>

                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="filSearch"><?php echo app('translator')->get('label.SEARCH'); ?></label>
                            <div class="col-md-8">
                                <?php echo Form::text('fil_search',  Request::get('fil_search'), ['class' => 'form-control tooltips', 'id' => 'filSearch', 'title' => __('label.TITLE') , 'placeholder' => __('label.TITLE'), 'list' => 'considerationsName', 'autocomplete' => 'off']); ?> 
                                <datalist id="considerationsName">
                                    <?php if(!$nameArr->isEmpty()): ?>
                                    <?php $__currentLoopData = $nameArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->title); ?>" />
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </datalist>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="form">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                <i class="fa fa-search"></i> <?php echo app('translator')->get('label.FILTER'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <?php echo Form::close(); ?>

                <!-- End Filter -->
            </div>

            <!--            <div class="row">
                            <div class="col-md-offset-8 col-md-4" id="manageEvDiv">
                                <a class="btn btn-icon-only btn-warning tooltips vcenter" title="Download PDF" 
                                   href="<?php echo e(action('RankController@index', ['download'=>'pdf', 'fil_search' => Request::get('fil_search'), 'fil_service_id' => Request::get('fil_service_id') ])); ?>">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>-->

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.TITLE'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.ORDER'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.STATUS'); ?></th>
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
                        <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center vcenter"><?php echo e(++$sl); ?></td>
                            <td class="vcenter"><?php echo e($target->title); ?></td>
                            <td class="text-center vcenter"><?php echo e($target->order); ?></td>

                            <td class="text-center vcenter">
                                <?php if($target->status == '1'): ?>
                                <span class="label label-sm label-success"><?php echo app('translator')->get('label.ACTIVE'); ?></span>
                                <?php else: ?>
                                <span class="label label-sm label-warning"><?php echo app('translator')->get('label.INACTIVE'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">
                                    <?php echo e(Form::open(array('url' => 'considerations/' . $target->id.Helper::queryPageStr($qpArr)))); ?>

                                    <?php echo e(Form::hidden('_method', 'DELETE')); ?>


                                    <a class="btn btn-xs btn-primary tooltips " title="Edit" href="<?php echo e(URL::to('considerations/' . $target->id . '/edit'.Helper::queryPageStr($qpArr))); ?>">
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
                            <td colspan="8" class="vcenter"><?php echo app('translator')->get('label.NO_CONSIDERATION_FOUND'); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
            <?php echo $__env->make('layouts.paginator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>     
        </div>	
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/considerations/index.blade.php ENDPATH**/ ?>