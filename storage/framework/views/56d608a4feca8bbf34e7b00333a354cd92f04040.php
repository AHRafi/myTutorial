<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="subEventId"><?php echo app('translator')->get('label.SUB_EVENT'); ?> :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            <?php echo Form::select('sub_event_id', $subEventList, null, ['class' => 'form-control js-source-states', 'id' => 'subEventId']); ?>

        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afwc\resources\views/eventAssessmentMarking/getSubEvent.blade.php ENDPATH**/ ?>