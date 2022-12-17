<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.MUTUAL_ASSESSMENT_DETAILED_REPORT'); ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => 'mutualAssessmentDetailedReportCrnt/filter','class' => 'form-horizontal', 'id' => 'submitForm')); ?>

            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?>:</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYearList->name); ?> </strong></div>
                            <?php echo Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?>:</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> <?php echo e($courseList->name); ?> </strong></div>
                            <?php echo Form::hidden('course_id', $courseList->id, ['id' => 'courseId']); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId"><?php echo app('translator')->get('label.TERM'); ?> :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            <?php echo Form::select('term_id', $termList, Request::get('term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']); ?>

                            <span class="text-danger"><?php echo e($errors->first('term_id')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" id="synOrEventGroup">
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
                    <?php echo Form::hidden('ma_process', !empty($maProcess) ? $maProcess : 0, ['id' => 'maProcess']); ?>

                </div>

            </div>

            <div class="row">
                <div id="showSubEvent">
                    <?php if($maProcess == '3'): ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="subEventId"><?php echo app('translator')->get('label.SUB_EVENT'); ?> :<span class="text-danger"> <?php echo e(sizeof($subEventList) > 1 ? '*' : ''); ?></span></label>
                            <div class="col-md-7">
                                <?php echo Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subEventId']); ?>

                                <span class="text-danger"><?php echo e($errors->first('sub_event_id')); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::hidden('has_sub_event', sizeof($subEventList) > 1 ? 1 : 0); ?>

                    <?php endif; ?>
                </div>
                <div id="showSubSubEvent">
                    <?php if($maProcess == '3'): ?>
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

                    <?php endif; ?>
                </div>
                <div id="showSubSubSubEvent">
                    <?php if($maProcess == '3'): ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="subSubSubEventId"><?php echo app('translator')->get('label.SUB_SUB_SUB_EVENT'); ?> :<span class="text-danger"> <?php echo e(sizeof($subSubSubEventList) > 1 ? '*' : ''); ?></span></label>
                            <div class="col-md-7">
                                <?php echo Form::select('sub_sub_sub_event_id', $subSubSubEventList, Request::get('sub_sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId']); ?>

                                <span class="text-danger"><?php echo e($errors->first('sub_sub_sub_event_id')); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::hidden('has_sub_sub_sub_event', sizeof($subSubSubEventList) > 1 ? 1 : 0); ?>


                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div id="showEventGroup">
                    <?php if($maProcess == '3'): ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="eventGroupId"><?php echo app('translator')->get('label.EVENT_GROUP'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-7">
                                <?php echo Form::select('event_group_id', $eventGroupList, Request::get('event_group_id'), ['class' => 'form-control js-source-states', 'id' => 'eventGroupId']); ?>

                                <span class="text-danger"><?php echo e($errors->first('event_group_id')); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-center">
                    <div class="form-group">
                        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="eventCombineReportId" value="Show Filter Info" data-id="1">
                            <i class="fa fa-search"></i> <?php echo app('translator')->get('label.GENERATE'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php if(Request::get('generate') == 'true'): ?>
            <?php if(!empty($cmArr)): ?>
            <div class="row">
                <div class="col-md-12 text-right">
                    <!--                    <a class="btn btn-md btn-primary vcenter tooltips" title="<?php echo app('translator')->get('label.PRINT'); ?>" target="_blank"  href="<?php echo URL::full().'&view=print'; ?>">
                                            <span class=""><i class="fa fa-print"></i> </span>
                                        </a>
                                        <a class="btn btn-success vcenter tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_PDF'); ?>" href="<?php echo URL::full().'&view=pdf'; ?>">
                                            <span class=""><i class="fa fa-file-pdf-o"></i></span>
                                        </a>-->
                    <a class="btn btn-warning vcenter tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_EXCEL'); ?>" href="<?php echo URL::full().'&view=excel'; ?>">
                        <span class=""><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                    <label class="control-label" for="sortBy"><?php echo app('translator')->get('label.SORT_BY'); ?> :</label>&nbsp;

                    <label class="control-label" for="sortBy">
                        <?php echo Form::select('sort', $sortByList, Request::get('sort'),['class' => 'form-control','id'=>'sortBy']); ?>

                    </label>

                    <button class="btn green-jungle filter-btn"  id="sortByHref" type="submit">
                        <i class="fa fa-arrow-right"></i>  <?php echo app('translator')->get('label.GO'); ?>
                    </button>


                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            <?php echo e(__('label.TRAINING_YEAR')); ?> : <strong><?php echo e($activeTrainingYearList->name); ?> |</strong>
                            <?php echo e(__('label.COURSE')); ?> : <strong><?php echo e($courseList->name); ?> |</strong>
                            <?php echo e(__('label.TERM')); ?> : <strong><?php echo e(!empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A')); ?> |</strong>
                            <?php if($maProcess == '1'): ?>
                            <?php echo e(__('label.SYNDICATE')); ?> : <strong><?php echo e(!empty($synList[Request::get('syn_id')]) && Request::get('syn_id') != 0 ? $synList[Request::get('syn_id')] : __('label.N_A')); ?> |</strong>
                            <?php elseif($maProcess == '2'): ?>
                            <?php echo e(__('label.SUB_SYNDICATE')); ?> : <strong><?php echo e(!empty($subSynList[Request::get('sub_syn_id')]) && Request::get('sub_syn_id') != 0 ? $subSynList[Request::get('sub_syn_id')] : __('label.N_A')); ?> |</strong>
                            <?php elseif($maProcess == '3'): ?>
                            <?php echo e(__('label.EVENT')); ?> : <strong><?php echo e(!empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A')); ?> |</strong>
                            <?php if(!empty(Request::get('sub_event_id'))): ?>
                            <?php echo e(__('label.SUB_EVENT')); ?> : <strong><?php echo e(!empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0 ? $subEventList[Request::get('sub_event_id')] : __('label.N_A')); ?> |</strong>
                            <?php endif; ?>
                            <?php if(!empty(Request::get('sub_sub_event_id'))): ?>
                            <?php echo e(__('label.SUB_SUB_EVENT')); ?> : <strong><?php echo e(!empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0 ? $subSubEventList[Request::get('sub_sub_event_id')] : __('label.N_A')); ?> |</strong>
                            <?php endif; ?>
                            <?php if(!empty(Request::get('sub_sub_sub_event_id'))): ?>
                            <?php echo e(__('label.SUB_SUB_SUB_EVENT')); ?> : <strong><?php echo e(!empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0 ? $subSubSubEventList[Request::get('sub_sub_sub_event_id')] : __('label.N_A')); ?> |</strong>
                            <?php endif; ?>
                            <?php echo e(__('label.EVENT_GROUP')); ?> : <strong><?php echo e(!empty($eventGroupList[Request::get('event_group_id')]) && Request::get('event_group_id') != 0 ? $eventGroupList[Request::get('event_group_id')] : __('label.N_A')); ?> |</strong>
                            <?php endif; ?>

                            <?php echo e(__('label.TOTAL_NO_OF_CM')); ?> : <strong><?php echo e(!empty($cmArr) ? sizeof($cmArr) : 0); ?></strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="max-height-500 table-responsive webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center" rowspan="3"><?php echo app('translator')->get('label.SL'); ?></th>
                                    <th class="vcenter" rowspan="3"><?php echo app('translator')->get('label.PERSONAL_NO'); ?></th>
                                    <th class="vcenter" rowspan="3"><?php echo app('translator')->get('label.RANK'); ?></th>
                                    <th class="vcenter" rowspan="3"><?php echo app('translator')->get('label.CM'); ?></th>
                                    <th class="vcenter" rowspan="3"><?php echo app('translator')->get('label.PHOTO'); ?></th>
                                    <th class="vcenter text-center" colspan="<?php echo e(!empty($cmArr) && !empty($factorList) ? sizeof($cmArr)*sizeof($factorList) : 1); ?>"><?php echo app('translator')->get('label.CM_MARKING'); ?></th>
                                    <th class="vcenter text-center" rowspan="2" colspan="<?php echo e(!empty($factorList) ? sizeof($factorList) : 1); ?>"><?php echo app('translator')->get('label.AVG'); ?></th>
                                    <th class="vcenter text-center" rowspan="2"colspan="<?php echo e(!empty($factorList) ? sizeof($factorList) : 1); ?>"><?php echo app('translator')->get('label.POSITION'); ?></th>
                                </tr>
                                <tr>
                                    <?php if(!empty($markingCmArr)): ?>
                                    <?php $__currentLoopData = $markingCmArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cmId => $cm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $cmName = (!empty($cm['rank_name']) ? $cm['rank_name'] : '') . ' ' . (!empty($cm['official_name']) ? $cm['official_name'] : '') . ' (' . (!empty($cm['personal_no']) ? $cm['personal_no'] : '') . ')';
                                    ?>
                                    <th class="vcenter text-center"colspan="<?php echo e(!empty($factorList) ? sizeof($factorList) : 1); ?>"><?php echo $cmName ?? ''; ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <?php if(!empty($markingCmArr)): ?>
                                    <?php $__currentLoopData = $markingCmArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cmId => $cm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!empty($factorList)): ?>
                                    <?php $__currentLoopData = $factorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factorId => $factor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="vcenter text-center"><?php echo $factor ?? ''; ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <?php if(!empty($factorList)): ?>
                                    <?php $__currentLoopData = $factorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factorId => $factor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="vcenter text-center"><?php echo $factor ?? ''; ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <?php if(!empty($factorList)): ?>
                                    <?php $__currentLoopData = $factorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factorId => $factor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="vcenter text-center"><?php echo $factor ?? ''; ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($cmArr) && !empty($markingCmArr)): ?>
                                <?php
                                $sl = 0;
                                ?>
                                <?php $__currentLoopData = $cmArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cmId => $cm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                $cmId = !empty($cm['id']) ? $cm['id'] : 0;
                                $cmName = (!empty($cm['rank_name']) ? $cm['rank_name'] : '') . ' ' . (!empty($cm['official_name']) ? $cm['official_name'] : '') . ' (' . (!empty($cm['personal_no']) ? $cm['personal_no'] : '') . ')';
                                ?>
                                <tr>
                                    <td class="vcenter text-center"><?php echo ++$sl; ?></td>
                                    <td class="vcenter">
                                        <div class="width-inherit"><?php echo $cm['personal_no'] ?? ''; ?></div>
                                    </td>
                                    <td class="vcenter">
                                        <div class="width-inherit"><?php echo $cm['rank_name'] ?? ''; ?></div>
                                    </td>
                                    <td class="vcenter width-180">
                                        <div class="width-inherit text-left"><?php echo $cm['full_name'] ?? ''; ?></div>
                                    </td>
                                    <td class="vcenter" width="50px">
                                        <?php if(!empty($cm['photo']) && File::exists('public/uploads/cm/' . $cm['photo'])): ?>
                                        <img class="profile-zoom" width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($cm['photo']); ?>" alt="<?php echo Common::getFurnishedCmName($cm['full_name']); ?>">
                                        <?php else: ?>
                                        <img class="profile-zoom" width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo Common::getFurnishedCmName($cm['full_name']); ?>">
                                        <?php endif; ?>
                                    </td>
                                    <?php $__currentLoopData = $markingCmArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $markingCmId => $markingCm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!empty($factorList)): ?>
                                    <?php $__currentLoopData = $factorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factorId => $factor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $alignment = !empty($markingPositionArr[$markingCmId][$cmId][$factorId]['pos']) ? 'right' : 'center';
                                    ?>
                                    <td class="vcenter text-<?php echo e($alignment); ?>"><?php echo $markingPositionArr[$markingCmId][$cmId][$factorId]['pos'] ?? '--'; ?></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!empty($factorList)): ?>
                                    <?php $__currentLoopData = $factorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factorId => $factor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="vcenter text-<?php echo e(!empty($cm['avg_'.$factorId]) ? 'right' : 'center'); ?>">
                                        <?php echo !empty($cm['avg_'.$factorId]) ? Helper::numberFormat2Digit($cm['avg_'.$factorId]) : '--'; ?>

                                    </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <?php if(!empty($factorList)): ?>
                                    <?php $__currentLoopData = $factorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factorId => $factor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="vcenter text-center"><?php echo $cm['position_'.$factorId] ?? ''; ?></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5">
                                        <strong>
                                            <?php if(Request::get('ma_process') == '1'): ?>
                                            <?php echo app('translator')->get('label.NO_CM_IS_ASSIGNED_TO_THIS_SYNDICATE'); ?>
                                            <?php elseif(Request::get('ma_process') == '2'): ?>
                                            <?php echo app('translator')->get('label.NO_CM_IS_ASSIGNED_TO_THIS_SUB_SYNDICATE'); ?>
                                            <?php elseif(Request::get('ma_process') == '3'): ?>
                                            <?php echo app('translator')->get('label.NO_CM_IS_ASSIGNED_TO_THIS_EVENT_GROUP'); ?>
                                            <?php else: ?>
                                            <?php echo app('translator')->get('label.NO_CM_IS_ASSIGNED_TO_THIS_SYNDICATE'); ?>
                                            <?php endif; ?>
                                        </strong>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <?php endif; ?>
            <?php echo Form::close(); ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        //table header fix
        $(".table-head-fixer-color").tableHeadFixer({left: 5});

        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            var maProcess = $("#maProcess").val();
            if (trainingYearId == 0) {
                $('#courseId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_COURSE_OPT'); ?></option>");
                $('#termId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_TERM_OPT'); ?></option>");
                $('#factorId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_OPT'); ?></option>");
                if (maProcess == 0) {
                    $('#synId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SYN_OPT'); ?></option>");
                } else {
                    $('#eventGroupId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_GROUP_OPT'); ?></option>");
                }
                $('#subSynId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SYN_OPT'); ?></option>");
                $('.required-show').text('');
                $('#hasSubSyn').val(0);
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessmentDetailedReportCrnt/getCourse')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    $('#termId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_TERM_OPT'); ?></option>");
                    $('#factorId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_OPT'); ?></option>");
                    if (maProcess == 0) {
                        $('#synId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SYN_OPT'); ?></option>");
                    } else {
                        $('#eventGroupId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_GROUP_OPT'); ?></option>");
                    }
                    $('#subSynId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SYN_OPT'); ?></option>");
                    $('.required-show').text('');
                    $('#hasSubSyn').val(0);
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#courseId').html(res.html);
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            });//ajax

        });
        //End::Get Course

        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            var maProcess = $("#maProcess").val();
            if (courseId == 0) {
                $('#termId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_TERM_OPT'); ?></option>");
                $('#factorId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_OPT'); ?></option>");
                if (maProcess == 0) {
                    $('#synId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SYN_OPT'); ?></option>");
                } else {
                    $('#eventGroupId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_GROUP_OPT'); ?></option>");
                }
                $('#subSynId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SYN_OPT'); ?></option>");
                $('.required-show').text('');
                $('#hasSubSyn').val(0);
                return false;
            }
            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessmentDetailedReportCrnt/getTerm')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    $('#factorId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_EVENT_OPT'); ?></option>");
                    $('#subSynId').html("<option value='0'><?php echo app('translator')->get('label.SELECT_SUB_SYN_OPT'); ?></option>");
                    $('.required-show').text('');
                    $('#hasSubSyn').val(0);
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#termId').html(res.html);
                    $('#synId').html(res.html1);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }

                }
            });//ajax
        });

        $(document).on("change", "#termId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();

            $('#synOrEventGroup').html('');
            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessmentDetailedReportCrnt/getSynOrGp')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#synOrEventGroup').html(res.html);
                    if (res.maProcess == '3') {
                        $('#showSubEvent').html(res.html1);
                        $('#showSubSubEvent').html(res.html2);
                        $('#showSubSubSubEvent').html(res.html3);
                        $('#showEventGroup').html(res.html4);
                    }
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }

                }
            });//ajax
        });

        $(document).on("change", "#eventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();

            $('#showSubEvent').html('');
            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessmentDetailedReportCrnt/getSubEvent')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                    event_id: eventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubEvent').html(res.html);
                    $('#showSubSubEvent').html(res.html2);
                    $('#showSubSubSubEvent').html(res.html3);
                    $('#showEventGroup').html(res.html1);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }

                }
            });//ajax
        });

        $(document).on("change", "#subEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();

            $('#showSubSubEvent').html('');
            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessmentDetailedReportCrnt/getSubSubEvent')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubEvent').html(res.html);
                    $('#showSubSubSubEvent').html(res.html2);
                    $('#showEventGroup').html(res.html1);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }

                }
            });//ajax
        });

        $(document).on("change", "#subSubEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();

            $('#showSubSubSubEvent').html('');
            $('#showEventGroup').html('');

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessmentDetailedReportCrnt/getSubSubSubEvent')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubSubEvent').html(res.html);
                    $('#showEventGroup').html(res.html1);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }

                }
            });//ajax
        });

        $(document).on("change", "#subSubSubEventId", function () {
            var termId = $("#termId").val();
            var courseId = $("#courseId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();

            $('#showEventGroup').html('');

            $.ajax({
                url: "<?php echo e(URL::to('mutualAssessmentDetailedReportCrnt/getEventGroup')); ?>",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    term_id: termId,
                    course_id: courseId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showEventGroup').html(res.html);
                    $('.js-source-states').select2();

                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }

                }
            });//ajax
        });


    });
</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/reportCrnt/mutualAssessmentDetailed/index.blade.php ENDPATH**/ ?>