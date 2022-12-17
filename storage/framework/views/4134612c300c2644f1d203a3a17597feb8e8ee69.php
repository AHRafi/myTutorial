<?php if(sizeof($cmList) > 1): ?>
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="cmId"><?php echo app('translator')->get('label.CM'); ?> :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            <?php echo Form::select('cm_id', $cmList, null, ['class' => 'form-control js-source-states', 'id' => 'cmId']); ?>

        </div>
    </div>
</div>
<?php else: ?>
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_CM_FOUND'); ?></strong></p>
    </div>
</div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/mutualAssessment/showCmOptions.blade.php ENDPATH**/ ?>