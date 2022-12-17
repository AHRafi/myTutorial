<?php if(!empty($cmList)): ?>
<div class="cm-list col-md-12">
    <?php echo Form::open(array('group' => 'form', 'url' => 'mutualAssessment/generate','class' => 'form-horizontal','id' => 'submitForm')); ?>   

    <div class="row">
        <div class="col-md-10 mb-10">
            <span class="label label-sm label-green-seagreen">
                <?php echo app('translator')->get('label.TOTAL_NUMBER_OF_CM'); ?> : <strong><?php echo e(!empty($cmList) ? sizeof($cmList) : 0); ?></strong> 
            </span> &nbsp;
            <span class="label label-sm label-blue-steel">
                <?php echo app('translator')->get('label.TOTAL_EXPORTED_MARK_SHEET'); ?> : <strong><?php echo e(!empty($exportCmIdArr) ? sizeof($exportCmIdArr) : 0); ?></strong> 
            </span> 
        </div>
        <div class="col-md-2 text-right">
            <button  type="button"  class=" btn green-steel btn-sm previewMarkingSheet">
                <i class="fa fa-download" aria-hidden="true"></i>&nbsp;<?php echo app('translator')->get('label.PREVIEW_MARKING_SHEET'); ?>
            </button>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-md-12 table-responsive">
            <div class="webkit-scrollbar my-datatable">
                <table class="table table-bordered table-hover relation-view-2" id="cmListTable">
                    <thead>
                        <tr>
                            <th class="vcenter text-center"><?php echo app('translator')->get('label.SL'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.PERSONAL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.RANK'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.NAME'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.PHOTO'); ?></th>
                            <th class="vcenter text-center" width="50"><?php echo app('translator')->get('label.EXPORT_STATUS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl = 1; ?> 
                        <?php $__currentLoopData = $cmList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                        <tr>
                            <td class="vcenter text-center"><strong><?php echo e($sl++); ?></strong></td>
                            <td class="vcenter"><?php echo e($cm['personal_no']); ?></td>
                            <td class="vcenter"><?php echo e($cm['rank']); ?></td>
                            <td class="vcenter"><?php echo Common::getFurnishedCmName($cm['full_name']); ?></td>
                            <td class="vcenter" width="50px">
                                <?php if(!empty($cm['photo']) && File::exists('public/uploads/cm/' . $cm['photo'])): ?>
                                <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($cm['photo']); ?>" alt="<?php echo Common::getFurnishedCmName($cm['full_name']); ?>"/>
                                <?php else: ?>
                                <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo Common::getFurnishedCmName($cm['full_name']); ?>"/>
                                <?php endif; ?>
                            </td>
                            <td class="vcenter text-center" width="50">
                                <?php if(in_array($cm['cm_id'],$exportCmIdArr)): ?>
                                <i class="fa fa-check font-green-jungle" aria-hidden="true"></i>
                                <?php else: ?>
                                <i class="fa fa-times font-red-thunderbird" aria-hidden="true"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <?php echo Form::hidden('course_id', $courseId); ?> 
    <?php echo Form::hidden('term_id', $termId); ?> 
    <?php echo Form::hidden('syn_id', $synId); ?> 
    <?php echo Form::hidden('sub_syn_id', $subSynId); ?> 
    <?php echo Form::hidden('event_id', $eventId); ?> 
    <?php echo Form::hidden('cm_id', null, ['class' => 'cm-id']); ?>   

    <?php echo Form::close(); ?>  
</div>
<?php else: ?>
<div class="col-md-12  margin-top-10">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> <?php echo __('label.NO_CM_FOUND'); ?></strong></p>
    </div>
</div>
<?php endif; ?>

<style>
    .mb-10{
        margin-bottom: 10px;
    }
    .p-5{padding:5px;}
    .infos span{
        margin-right: 10px;
    }
</style>
<script>
    $(document).ready(function () {
        $('.relation-view-2').tableHeadFixer();
        $('#cmListTable').DataTable();
    });
</script>



<?php /**PATH C:\xampp\htdocs\afwc\resources\views/mutualAssessment/showCmList.blade.php ENDPATH**/ ?>