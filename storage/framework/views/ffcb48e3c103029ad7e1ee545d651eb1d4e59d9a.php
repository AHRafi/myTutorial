<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.CONTENT_CATEGORY_MANAGEMENT'); ?>
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="<?php echo e(URL::to('contentCategory/create'.Helper::queryPageStr($qpArr))); ?>"> <?php echo app('translator')->get('label.CREATE_NEW_CONTENT_CATEGORY'); ?>
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <!-- Begin Filter-->
                <?php echo Form::open(array('group' => 'form', 'url' => 'contentCategory/filter','class' => 'form-horizontal')); ?>

                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="search"><?php echo app('translator')->get('label.SEARCH'); ?></label>
                            <div class="col-md-8">
                                <?php echo Form::text('search',  Request::get('search'), ['class' => 'form-control tooltips', 'id' => 'search', 'title' => 'Name', 'placeholder' => 'Name', 'list' => 'contentCategoryName', 'autocomplete' => 'off']); ?> 
                                <datalist id="contentCategoryName">
                                    <?php if(!$nameArr->isEmpty()): ?>
                                    <?php $__currentLoopData = $nameArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->name); ?>" />
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.NAME'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.SHORT_DESCRIPTION'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.PARENT_CATEGORY'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.RELATE_TO_THE_COMPARTMENTS'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.ORDER'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.STATUS'); ?></th>
                            <th class="vcenter text-center"><?php echo app('translator')->get('label.ACTION'); ?></th>
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
                            <td class="vcenter"><?php echo e($target->name); ?></td>
                            <td class="vcenter"><?php echo e($target->short_description); ?></td>
                            <td class="vcenter">
                                <?php
                                if (isset($parentArr[$target->id])) {
                                    echo $parentArr[$target->id];
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                            <td class="text-center vcenter">
                                <?php $comptArr = !empty($target->related_compartment) ? explode(',', $target->related_compartment) : []; ?>
                                <?php if(!empty($comptArr)): ?>
                                <?php $__currentLoopData = $comptArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comptId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                $compt = !empty($comptId) && !empty($compartmentList[$comptId]) ? $compartmentList[$comptId] : __('label.N_A');
                                $comptColor = empty($comptId) ? 'grey-mint' : ($comptId == '1' ? 'purple-sharp' : ($comptId == '2' ? 'blue-steel' : ($comptId == '3' ? 'yellow' : 'grey-mint')));
                                ?>
                                <span class="label label-sm label-<?php echo e($comptColor); ?>"><?php echo $compt; ?></span>&nbsp; 
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center vcenter"><?php echo e($target->order); ?></td>
                            <td class="vcenter text-center">
                                <?php if($target->status == '1'): ?>
                                <span class="label label-sm label-success"><?php echo app('translator')->get('label.ACTIVE'); ?></span>
                                <?php else: ?>
                                <span class="label label-sm label-warning"><?php echo app('translator')->get('label.INACTIVE'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">  
                                    <?php echo e(Form::open(array('url' => 'contentCategory/' . $target->id.'/'.Helper::queryPageStr($qpArr), 'class' => 'delete-form-inline'))); ?>

                                    <a class="btn btn-xs btn-primary tooltips vcenter" title="Edit" href="<?php echo e(URL::to('contentCategory/' . $target->id . '/edit'.Helper::queryPageStr($qpArr))); ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <?php echo e(Form::hidden('_method', 'DELETE')); ?>

                                    <button class="btn btn-xs btn-danger delete tooltips vcenter" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <?php echo e(Form::close()); ?>


                                </div>
                            </td>

                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="3" class="vcenter"><?php echo app('translator')->get('label.CONTENT_CATEGORY_NOT_FOUND'); ?></td>
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
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/contentCategory/index.blade.php ENDPATH**/ ?>