<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="subSubEventId"><?php echo app('translator')->get('label.SUB_SUB_EVENT'); ?> :<span class="text-danger"> <?php echo e(sizeof($subSubEventList) > 1 ? '*' : ''); ?></span></label>
        <div class="col-md-7">
            <?php echo Form::select('sub_sub_event_id', $subSubEventList, Request::get('sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']); ?>

            <span class="text-danger"><?php echo e($errors->first('sub_sub_event_id')); ?></span>
        </div>
    </div>
</div>
<?php echo Form::hidden('has_sub_sub_event', sizeof($subSubEventList) > 1 ? 1 : 0); ?>

<?php /**PATH C:\xampp\htdocs\afwc\resources\views/reportCrnt/mutualAssessmentDetailed/showSubSubEvent.blade.php ENDPATH**/ ?>