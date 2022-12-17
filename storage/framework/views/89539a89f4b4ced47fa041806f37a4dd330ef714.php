
<?php $__env->startSection('data_count'); ?>
<?php if(session('status')): ?>

<div class="alert alert-success">
    <?php echo e(session('status')); ?>

</div>
<?php endif; ?>

<div class="portlet-body">
    <?php echo $__env->make('admin.commonFeatures.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="clear-fix"></div>
    
    <!--TERM TO COURSE CARD-->
    <?php echo $__env->make('admin.commonFeatures.courseBlock', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="row margin-top-10">
        
    </div>
</div>
<!--<script src="<?php echo e(asset('public/js/apexcharts.min.js')); ?>" type="text/javascript"></script>-->
<script type="text/javascript">

</script>

<!--EOF SHORT ICON-->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/admin/ds/dashboard.blade.php ENDPATH**/ ?>