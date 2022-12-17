<?php if($maProcess == '1'): ?>
<div class="form-group">
    <label class="control-label col-md-4" for="synId"><?php echo app('translator')->get('label.SYN'); ?> :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        <?php echo Form::select('syn_id', $synList, Request::get('syn_id'), ['class' => 'form-control js-source-states', 'id' => 'synId']); ?>

        <span class="text-danger"><?php echo e($errors->first('syn_id')); ?></span>
    </div>
</div>
<?php elseif($maProcess == '2'): ?>
<div class="form-group">
    <label class="control-label col-md-4" for="subSynId"><?php echo app('translator')->get('label.SUB_SYN'); ?> :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        <?php echo Form::select('sub_syn_id', $subSynList, Request::get('sub_syn_id'), ['class' => 'form-control js-source-states', 'id' => 'subSynId']); ?>

        <span class="text-danger"><?php echo e($errors->first('sub_syn_id')); ?></span>
    </div>
</div>
<?php elseif($maProcess == '3'): ?>
<div class="form-group">
    <label class="control-label col-md-4" for="eventId"><?php echo app('translator')->get('label.EVENT'); ?> :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        <?php echo Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']); ?>

        <span class="text-danger"><?php echo e($errors->first('event_id')); ?></span>
    </div>
</div>
<?php else: ?>
<div class="form-group">
    <label class="control-label col-md-4" for="synId"><?php echo app('translator')->get('label.SYN'); ?> :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        <?php echo Form::select('syn_id', $synList, Request::get('syn_id'), ['class' => 'form-control js-source-states', 'id' => 'synId']); ?>

        <span class="text-danger"><?php echo e($errors->first('syn_id')); ?></span>
    </div>
</div>
<?php endif; ?>
<?php echo Form::hidden('ma_process', !empty($maProcess) ? $maProcess : 0, ['id' => 'maProcess']); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/reportCrnt/mutualAssessmentDetailed/getSyn.blade.php ENDPATH**/ ?>