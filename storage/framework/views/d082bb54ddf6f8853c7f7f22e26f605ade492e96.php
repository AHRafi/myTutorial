<?php $__env->startSection('data_count'); ?>	
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i><?php echo app('translator')->get('label.CREATE_CONTENT_CATEGORY'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php echo Form::open(array('group' => 'form', 'url' => 'contentCategory','class' => 'form-horizontal')); ?>


            <?php echo e(csrf_field()); ?>

            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">

                        <div class="form-group">
                            <label class="control-label col-md-4" for="parentId"><?php echo app('translator')->get('label.PARENT_CATEGORY'); ?> :</label>
                            <div class="col-md-8">
                                <?php echo Form::select('parent_id', array('0' => __('label.SELECT_CATEGORY_OPT')) + $parentArr, null, ['class' => 'form-control js-source-states', 'id' => 'parentId']); ?>

                                <span class="text-danger"><?php echo e($errors->first('parent_id')); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="name"><?php echo app('translator')->get('label.NAME'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::text('name',null, ['id'=> 'name', 'class' => 'form-control','autocomplete' => 'off']); ?> 
                                <span class="text-danger"><?php echo e($errors->first('name')); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="shortDescription"><?php echo app('translator')->get('label.SHORT_DESCRIPTION'); ?> :<span class="text-danger"> </span></label>
                            <div class="col-md-8">
                                <?php echo Form::text('short_description',null, ['id'=> 'shortDescription', 'class' => 'form-control','autocomplete' => 'off']); ?> 
                                <span class="text-danger"><?php echo e($errors->first('short_description')); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="relatedCompartment"><?php echo app('translator')->get('label.RELATE_TO_THE_COMPARTMENTS'); ?> :</label>
                            <div class="col-md-8">
                                <?php echo Form::select('related_compartment[]', $compartmentList, null, ['id' => 'relatedCompartment', 'class' => 'form-control mt-multiselect btn btn-default', 'multiple']); ?>

                                <span class="text-danger"><?php echo e($errors->first('related_compartment')); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="order"><?php echo app('translator')->get('label.ORDER'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::select('order', $orderList, null, ['class' => 'form-control js-source-states', 'id' => 'order']); ?> 
                                <span class="text-danger"><?php echo e($errors->first('order')); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="status"><?php echo app('translator')->get('label.STATUS'); ?> :</label>
                            <div class="col-md-8">
                                <?php echo Form::select('status', ['1' => __('label.ACTIVE'), '2' => __('label.INACTIVE')], '1', ['class' => 'form-control', 'id' => 'status']); ?>

                                <span class="text-danger"><?php echo e($errors->first('status')); ?></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn btn-circle green" type="submit">
                            <i class="fa fa-check"></i> <?php echo app('translator')->get('label.SUBMIT'); ?>
                        </button>
                        <a href="<?php echo e(URL::to('/contentCategory'.Helper::queryPageStr($qpArr))); ?>" class="btn btn-circle btn-outline grey-salsa"><?php echo app('translator')->get('label.CANCEL'); ?></a>
                    </div>
                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>	
    </div>
</div>

<script>

    $(function () {
        var authorityAllSelected = false;
        $('#relatedCompartment').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            nonSelectedText: "<?php echo app('translator')->get('label.SELECT_COMPARTMENT'); ?>",
//        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                authorityAllSelected = true;
            },
            onChange: function () {
                authorityAllSelected = false;
            }
        });


    });


</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/contentCategory/create.blade.php ENDPATH**/ ?>