<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="eventGroupId"><?php echo app('translator')->get('label.EVENT_GROUP'); ?> :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            <?php echo Form::select('event_group_id', $eventGroupList, Request::get('event_group_id'), ['class' => 'form-control js-source-states', 'id' => 'eventGroupId']); ?>

            <span class="text-danger"><?php echo e($errors->first('event_group_id')); ?></span>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afwc\resources\views/reportCrnt/mutualAssessmentDetailed/showEventGroup.blade.php ENDPATH**/ ?>