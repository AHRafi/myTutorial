
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="subEventId"><?php echo app('translator')->get('label.SUB_EVENT'); ?> :<span class="text-danger"> <?php echo e(sizeof($subEventList) > 1 ? '*' : ''); ?></span></label>
        <div class="col-md-7">
            <?php echo Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subEventId']); ?>

            <span class="text-danger"><?php echo e($errors->first('sub_event_id')); ?></span>
        </div>
    </div>
</div>
<?php echo Form::hidden('has_sub_event', sizeof($subEventList) > 1 ? 1 : 0); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/reportCrnt/mutualAssessmentDetailed/showSubEvent.blade.php ENDPATH**/ ?>