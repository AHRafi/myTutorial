
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

    <!--END :    : TERM TO COURSE CARD-->
    <!--TERM SCHEDULING CARD-->
    <div class="row margin-top-20">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat yellow-casablanca tooltips" href="<?php echo URL::to('/termToCourse/activationOrClosing'); ?>" title="<?php echo app('translator')->get('label.TERM_SCHEDULING_ACTIVATION_CLOSING'); ?>">
                <div class="visual">
                    <i class="fa fa-sliders"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-sliders"></i>
                    </div>
                    <div class="desc"><?php echo app('translator')->get('label.TERM_SCHEDULING_ACTIVATION_CLOSING'); ?></div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat purple-soft tooltips" href="<?php echo URL::to('/termToEvent'); ?>" title="<?php echo app('translator')->get('label.TERM_TO_EVENT'); ?>">
                <div class="visual">
                    <i class="fa fa-cubes"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-cubes"></i>
                    </div>
                    <div class="desc"><?php echo app('translator')->get('label.TERM_TO_EVENT'); ?></div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat green-sharp tooltips" href="<?php echo URL::to('/termToMAEvent'); ?>" title="<?php echo app('translator')->get('label.TERM_TO_MA_EVENT'); ?>">
                <div class="visual">
                    <i class="fa fa-puzzle-piece"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-puzzle-piece"></i>
                    </div>
                    <div class="desc"><?php echo app('translator')->get('label.TERM_TO_MA_EVENT'); ?></div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat blue-hoki tooltips" href="<?php echo URL::to('/markingGroup'); ?>" title="<?php echo app('translator')->get('label.ASSIGN_MARKING_GROUP'); ?>">
                <div class="visual">
                    <i class="fa fa-pencil"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <div class="desc"><?php echo app('translator')->get('label.ASSIGN_MARKING_GROUP'); ?></div>
                </div>
            </a>
        </div> 
    </div>
    <div class="row margin-top-20">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat yellow tooltips" href="<?php echo URL::to('/termToCourse'); ?>" title="<?php echo app('translator')->get('label.TERM_SCHEDULING'); ?>">
                <div class="visual">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div class="desc"><?php echo app('translator')->get('label.TERM_SCHEDULING'); ?></div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat blue-steel tooltips" href="<?php echo URL::to('/synToCourse'); ?>" title="<?php echo app('translator')->get('label.SYN_TO_COURSE'); ?>">
                <div class="visual">
                    <i class="fa fa-sitemap"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-sitemap"></i>
                    </div>
                    <div class="desc"><?php echo app('translator')->get('label.SYN_TO_COURSE'); ?></div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat purple-studio tooltips" href="<?php echo URL::to('/cmGroupMemberTemplate'); ?>" title="<?php echo app('translator')->get('label.CM_GROUP_MEMBER_TEMPLATE'); ?>">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="desc"><?php echo app('translator')->get('label.CM_GROUP_MEMBER_TEMPLATE'); ?></div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat green-jungle tooltips" href="<?php echo URL::to('/dsGroupMemberTemplate'); ?>" title="<?php echo app('translator')->get('label.DS_GROUP_MEMBER_TEMPLATE'); ?>">
                <div class="visual">
                    <i class="fa fa-book"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-book"></i>
                    </div>
                    <div class="desc"><?php echo app('translator')->get('label.DS_GROUP_MEMBER_TEMPLATE'); ?></div>
                </div>
            </a>
        </div> 
    </div>
    <!--END :: TERM SCHEDULING CARD-->
</div>
<script src="<?php echo e(asset('public/js/apexcharts.min.js')); ?>" type="text/javascript"></script>
<script type="text/javascript">

</script>

<!--EOF SHORT ICON-->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/admin/superAdmin/dashboard.blade.php ENDPATH**/ ?>