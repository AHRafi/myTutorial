<div class="row">
    <?php if($subjectArr): ?>
        <div class="col-md-12">
            <div class="row margin-bottom-10">
                <div class="col-md-12">
                    <div class="col-md-9">
                        <span class="label label-success"><?php echo app('translator')->get('label.TOTAL_NUM_OF_SUBJECT'); ?>: <?php echo !empty($subjectArr) ? count($subjectArr) : 0; ?></span>
                    <span class="label label-purple"><?php echo app('translator')->get('label.TOTAL_ASSIGNED_SUBJECT'); ?>: &nbsp;<?php echo !empty($prevDataList) ? sizeof($prevDataList) : 0; ?></span>

                    <button class="label label-primary tooltips" href="#modalAssignedSubject" id="assignedSubject" data-toggle="modal"
                        data-subjectId="<?php echo e($request->module_id ?? 0); ?>" title="<?php echo app('translator')->get('label.SHOW_ASSIGNED_SUBJECT'); ?>">
                        <?php echo app('translator')->get('label.SUBJECT_ASSIGNED_TO_THIS_MODULE'); ?>: <?php echo !empty($previousCheck) ? count($previousCheck) : 0; ?>&nbsp; <i class="fa fa-search-plus"></i>
                    </button>
                    </div>
                    <div class="col-md-3 text-right">
                        <button class="btn green btn-danger tooltips" type="button" id="buttonDelete" data-moduleId="<?php echo e($request->module_id); ?>">
                            <i class="fa fa-trash"></i> &nbsp;<?php echo app('translator')->get('label.DELETE_MODULE'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th class="vcenter text-center"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                        <th class="vcenter text-center">
                            <div class="md-checkbox has-success tooltips text-center" title="<?php echo app('translator')->get('label.SELECT_ALL'); ?>">
                                <?php echo Form::checkbox('check_all', 1, false, ['id' => 'checkedAll', 'class' => 'md-check']); ?>

                                <label for="checkedAll">
                                    <span></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>
                            </div>
                        </th>
                        <th class="text-center vcenter"><?php echo app('translator')->get('label.SUBJECT'); ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?php $sl = 0; ?>
                    <?php $__currentLoopData = $subjectArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $checked = '';
                            $disabled = '';
                            $modules = '';
                            $class = 'cm-group-member-check';
                            if (!empty($previousCheck)) {
                                $checked = array_key_exists($item->id, $previousCheck) ? 'checked' : '';
                            }
                            foreach ($moduleArr as $key => $value) {
                                if($value->id == $item->id && $value->module_id != $request->module_id){
                                    $modules .= "{$value->name}, ";
                                }
                            }
                        ?>
                        <tr>
                            <td class="vcenter text-center"><?php echo ++$sl; ?></td>
                            <td class="vcenter text-center">
                                <div class="md-checkbox has-success tooltips" title="<?php echo e(!empty($modules) ? 'Releted Modules '.trim($modules,', ') : ''); ?>">
                                    <?php echo Form::checkbox('subject_id[' . $item->id . ']', $item->id, $checked, ['id' => $item->id, 'class' => 'md-check ' . $class, $disabled ]); ?>

                                    <label for="<?php echo e($item->id); ?>">
                                        <span class="inc"></span>
                                        <span class="check mark-caheck"></span>
                                        <span class="box mark-caheck"></span>
                                    </label>
                                </div>
                            </td>
                            <td class="vcenter"><?php echo e($item->title); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-5 col-md-5">
                        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit">
                            <i class="fa fa-check"></i> <?php echo app('translator')->get('label.SUBMIT'); ?>
                        </button>
                        <a href=""
                            class="btn btn-circle btn-outline grey-salsa "><?php echo app('translator')->get('label.CANCEL'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><i class="fa fa-bell-o fa-fw"></i><?php echo app('translator')->get('label.NO_SUBJECT_FOUND'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>


<script src="<?php echo e(asset('public/js/custom.js')); ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {


        var isDsEmpty = <?php echo e(!$subjectArr->isEmpty()); ?>;

        if (isDsEmpty) {
            $('#dataTable').dataTable({
                "paging": true,
                "pageLength": 100,
                "info": false,
                "order": false
            });



            // this code for  database 'check all' if all checkbox items are checked
            if ($('.cm-group-member-check:checked').length == $('.cm-group-member-check').length) {
                $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
            }

            $("#checkedAll").change(function() {
                if (this.checked) {
                    $(".md-check").each(function() {
                        if (!this.hasAttribute("disabled")) {
                            this.checked = true;
                        }
                    });
                } else {
                    $(".md-check").each(function() {
                        this.checked = false;
                    });
                }
            });

            $('.cm-group-member-check').change(function() {
                if (this.checked == false) { //if this item is unchecked
                    $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
                }

                //check 'check all' if all checkbox items are checked
                allCheck();
            });
            allCheck();
        }

    });

    function allCheck() {
        if ($('.cm-group-member-check:checked').length == $('.cm-group-member-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        } else {
            $('#checkedAll')[0].checked = false;
        }
    }
</script>
<?php /**PATH C:\xampp\htdocs\afwc\resources\views/moduleToSubject/showSubjectList.blade.php ENDPATH**/ ?>