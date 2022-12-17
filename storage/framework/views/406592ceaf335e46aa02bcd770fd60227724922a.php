<div class="col-md-12">
    <div class="row">
        <div class="col-md-10">
            <span class="label label-sm label-green-seagreen">
                <?php echo app('translator')->get('label.TOTAL_NUMBER_OF_CM'); ?> : <strong><?php echo e(!$cmList->isEmpty() ? $cmList->count() : 0); ?></strong>
            </span>
        </div>
        <div class="col-md-2 text-right mb-10">
            <button class="btn purple-sharp btn-sm" type="button" id="import">
                <i class="fa fa-arrow-down"></i> <?php echo app('translator')->get('label.IMPORT'); ?>
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-header-fixed">
                <thead>
                    <tr>
                        <th class="vcenter text-center"><?php echo app('translator')->get('label.SL'); ?></th>
                        <th class="vcenter"><?php echo app('translator')->get('label.PERSONAL_NO'); ?></th>
                        <th class="vcenter"><?php echo app('translator')->get('label.RANK'); ?></th>
                        <th class="vcenter"><?php echo app('translator')->get('label.NAME'); ?></th>
                        <th class="vcenter"><?php echo app('translator')->get('label.PHOTO'); ?></th>
                        <?php if(!empty($factorList)): ?>
                        <?php $__currentLoopData = $factorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factorId => $factor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="vcenter text-center width-80"><?php echo e($factor); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $sl = 1; ?> 
                    <?php if(!$cmList->isEmpty()): ?>
                    <?php $__currentLoopData = $cmList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                    <tr>
                        <td class="vcenter text-center width-80"><strong><?php echo e($sl++); ?></strong></td>
                        <td class="vcenter width-80"><?php echo e($cm->personal_no); ?></td>
                        <td class="vcenter width-80"><?php echo e($cm->rank); ?></td>
                        <td class="vcenter"><?php echo Common::getFurnishedCmName($cm->full_name); ?></td>
                        <td class="vcenter" width="50px">
                            <?php if(!empty($cm->photo) && File::exists('public/uploads/cm/' . $cm->photo)): ?>
                            <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($cm->photo); ?>" alt="<?php echo Common::getFurnishedCmName($cm['full_name']); ?>"/>
                            <?php else: ?>
                            <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo Common::getFurnishedCmName($cm['full_name']); ?>"/>
                            <?php endif; ?>
                        </td>
                        <?php if(!empty($factorList)): ?>
                        <?php $__currentLoopData = $factorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factorId => $factor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="vcenter text-center width-80">
                            <?php echo Form::text('position['.$cm->cm_id.']['.$factorId.']', !empty($prevMarkingArr[$cm->cm_id][$factorId])? $prevMarkingArr[$cm->cm_id][$factorId] : null, ['class' => 'form-control text-center width-inherit', 'readonly']); ?>

                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="vcenter"><strong><?php echo app('translator')->get('label.CM_NOT_AVAILABLE'); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .borderless td, .borderless th {
        border: none;
    } 
    .custom-padding-3-10 td{
        padding:3px 10px !important;
    }
</style>

<script>
$(function(){
    $('.table-header-fixed').tableHeadFixer({left: 5});
});

</script><?php /**PATH C:\xampp\htdocs\afwc\resources\views/mutualAssessment/showMarkingSheet.blade.php ENDPATH**/ ?>