
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-power-off"></i><?php echo app('translator')->get('label.MKS_SUBMISSION_STATE'); ?>
            </div>
        </div>

        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'assessmentActDeactForm')); ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId"><?php echo app('translator')->get('label.TRAINING_YEAR'); ?>:</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> <?php echo e($activeTrainingYearInfo->name); ?> </strong></div>
                                    <?php echo Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                                <label class="control-label col-md-6" for="termId"><?php echo app('translator')->get('label.TERM'); ?> :</label>
                                <div class="col-md-6">
                                    <div class="control-label pull-left"> <strong> <?php echo e($activeTermInfo->name); ?> </strong></div>
                                    <?php echo Form::hidden('term_id',$activeTermInfo->id,['id'=>'termId']); ?>

                                </div>
                            </div>
                        </div>

                    </div><!--Start::Event assessment summary -->
                    <div class="row margin-top-10">
                        <div class="col-md-12">
                            <div class=" table-responsive webkit-scrollbar">
                                <table class="table table-bordered table-head-fixer-color">
                                    <thead>
                                        <tr>
                                            <th class="vcenter" colspan="<?php echo e(Auth::user()->group_id == 4 ? 10 : 9); ?>"><?php echo app('translator')->get('label.EVENT_ASSESSMENT'); ?> <?php echo app('translator')->get('label.PROGRESS'); ?></th>
                                        </tr>
                                        <tr>
                                            <th class="vcenter text-center" rowspan="2"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                                            <th class="vcenter" rowspan="2"><?php echo app('translator')->get('label.EVENT'); ?></th>
                                            <th class="vcenter" rowspan="2"><?php echo app('translator')->get('label.SUB_EVENT'); ?></th>
                                            <th class="vcenter" rowspan="2"><?php echo app('translator')->get('label.SUB_SUB_EVENT'); ?></th>
                                            <th class="vcenter" rowspan="2"><?php echo app('translator')->get('label.SUB_SUB_SUB_EVENT'); ?></th>
                                            <th class="vcenter text-center" rowspan="2"><?php echo app('translator')->get('label.ACTIVATION_STATUS'); ?></th>
											
                                            <?php if(Auth::user()->group_id == 4): ?>
                                            <th class="vcenter text-center" rowspan="2"><?php echo app('translator')->get('label.MY_MKS_SUBMISSION_STATE'); ?></th>
                                            <?php endif; ?>
                                            <th class="vcenter text-center" colspan="2"><?php echo app('translator')->get('label.DS_MARKING'); ?></th>
                                            <th class="vcenter text-center" rowspan="2"><?php echo app('translator')->get('label.CI_MODERATION_MARKING'); ?></th>
                                            <!--<th class="vcenter text-center" rowspan="2"><?php echo app('translator')->get('label.COMDT_MODERATION_MARKING'); ?></th>-->
                                        </tr>
                                        <tr>
                                            <th class="vcenter text-center"><?php echo app('translator')->get('label.FORWARDED'); ?></th>
                                            <th class="vcenter text-center"><?php echo app('translator')->get('label.NOT_FORWARDED'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($eventMksWtArr['mks_wt'])): ?>

                                        <?php $__currentLoopData = $eventMksWtArr['mks_wt']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $termId => $evMksInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <!--                            <tr>
                                            <td class="vcenter text-center" colspan="9"><?php echo !empty($eventMksWtArr['event'][$termId]['name']) ? $eventMksWtArr['event'][$termId]['name'] : ''; ?></td>
                                        </tr>-->
                                        <?php $sl = 0; ?>
                                        <?php $__currentLoopData = $evMksInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventId => $evInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-center" rowspan="<?php echo !empty($rowSpanArr['event'][$termId][$eventId]) ? $rowSpanArr['event'][$termId][$eventId] : 1; ?>"><?php echo ++$sl; ?></td>
                                            <td rowspan="<?php echo !empty($rowSpanArr['event'][$termId][$eventId]) ? $rowSpanArr['event'][$termId][$eventId] : 1; ?>">
                                                <?php echo !empty($eventMksWtArr['event'][$termId][$eventId]['name']) ? $eventMksWtArr['event'][$termId][$eventId]['name'] : ''; ?>

                                            </td>

                                            <?php if(!empty($evInfo)): ?>
                                            <?php $i = 0; ?>
                                            <?php $__currentLoopData = $evInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subEventId => $subEvInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                            if ($i > 0) {
                                                echo '<tr>';
                                            }
                                            ?>
                                            <td class="vcenter"  rowspan="<?php echo !empty($rowSpanArr['sub_event'][$termId][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$termId][$eventId][$subEventId] : 1; ?>">
                                                <?php echo !empty($eventMksWtArr['event'][$termId][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$termId][$eventId][$subEventId]['name'] : ''; ?>

                                            </td>

                                            <?php if(!empty($subEvInfo)): ?>
                                            <?php $j = 0; ?>
                                            <?php $__currentLoopData = $subEvInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subSubEventId => $subSubEvInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                            if ($j > 0) {
                                                echo '<tr>';
                                            }
                                            ?>
                                            <td class="vcenter"  rowspan="<?php echo !empty($rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] : 1; ?>">
                                                <?php echo !empty($eventMksWtArr['event'][$termId][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$termId][$eventId][$subEventId][$subSubEventId]['name'] : ''; ?>

                                            </td>

                                            <?php if(!empty($subSubEvInfo)): ?>
                                            <?php $k = 0; ?>
                                            <?php $__currentLoopData = $subSubEvInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subSubSubEventId => $subSubSubEvInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                            if ($k > 0) {
                                                echo '<tr>';
                                            }
                                            ?>
                                            <td class="vcenter">
                                                <?php echo !empty($eventMksWtArr['event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$termId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : ''; ?>

                                            </td>
                                            <td class="vcenter text-center">
                                                <?php
                                                $color = !empty($assessmentActDeactArr[1][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? 'green-steel' : 'red-intense';
                                                $title = !empty($assessmentActDeactArr[1][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? __('label.ACTIVATED') : __('label.DEACTIVATED');
                                                ?>
                                                <i class="fa fa-power-off bold text-<?php echo e($color); ?> tooltips" title="<?php echo e($title); ?>"></i>
                                            </td>
                                            <?php if(Auth::user()->group_id == 4): ?>
                                            <td class="vcenter text-center">
                                                <?php
                                                $state = __('label.N_A');
                                                $color = 'grey-mint';

                                                if (!empty($dsOwnMksSubmissionArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId])) {
                                                    $dsOwnMksSubmission = $dsOwnMksSubmissionArr[$eventId][$subEventId][$subSubEventId][$subSubSubEventId];
                                                    if (!empty($dsOwnMksSubmission['submitted'])) {
                                                        $state = __('label.SUBMITTED');
                                                        $color = 'purple';
                                                    } else if (!empty($dsOwnMksSubmission['drafted'])) {
                                                        $state = __('label.DRAFTED');
                                                        $color = 'blue-steel';
                                                    } else if (!empty($dsOwnMksSubmission['to_be_put'])) {
                                                        $state = __('label.NOT_SUBMITTED_YET');
                                                        $color = 'yellow';
                                                    } 
                                                }

                                                ?>
                                                <span class="label label-sm label-<?php echo e($color); ?>"><?php echo $state; ?></span>
                                            </td>
                                            <?php endif; ?>
                                            <td class="vcenter text-center">
                                                <?php
                                                $forwardedClass = '';
                                                $forwardedtype = '';
                                                $forwardedHref = '';
                                                if (!empty($subSubSubEvInfo['forwarded'])) {
                                                    $forwardedClass = 'ds-marking-status';
                                                    $forwardedtype = 'type=button';
                                                    $forwardedHref = '#dsMarkingSummaryModal';
                                                }
                                                ?>
                                                <a <?php echo e($forwardedtype); ?> class = "btn btn-xs bold <?php echo e($forwardedClass); ?> green-steel tooltips" term-id='<?php echo e($termId); ?>' 
                                                    event-id='<?php echo e($eventId); ?>' sub-event-id='<?php echo e($subEventId); ?>' sub-sub-event-id='<?php echo e($subSubEventId); ?>' 
                                                    sub-sub-sub-event-id='<?php echo e($subSubSubEventId); ?>'
                                                    data-id="1" title="<?php echo app('translator')->get('label.FORWARDED'); ?>" href="<?php echo e($forwardedHref); ?>" data-toggle="modal">
                                                    <?php echo e(!empty($subSubSubEvInfo['forwarded']) ? $subSubSubEvInfo['forwarded'] : '0'); ?>

                                                </a>
                                            </td>
                                            <td class="vcenter text-center">
                                                <?php
                                                $notForwardedClass = '';
                                                $notForwardedtype = '';
                                                $notForwardedHref = '';
                                                if (!empty($subSubSubEvInfo['not_forwarded'])) {
                                                    $notForwardedClass = 'ds-marking-status';
                                                    $notForwardedtype = 'type=button';
                                                    $notForwardedHref = '#dsMarkingSummaryModal';
                                                }
                                                ?>
                                                <a  <?php echo e($notForwardedtype); ?> class = "btn btn-xs bold <?php echo e($notForwardedClass); ?> red-mint tooltips"  term-id='<?php echo e($termId); ?>' 
                                                    event-id='<?php echo e($eventId); ?>' sub-event-id='<?php echo e($subEventId); ?>' sub-sub-event-id='<?php echo e($subSubEventId); ?>' 
                                                    sub-sub-sub-event-id='<?php echo e($subSubSubEventId); ?>' 
                                                    data-id="2" title="<?php echo app('translator')->get('label.NOT_FORWARDED'); ?>" href="<?php echo e($notForwardedHref); ?>" data-toggle="modal">
                                                    <?php echo e(!empty($subSubSubEvInfo['not_forwarded']) ? $subSubSubEvInfo['not_forwarded'] : '0'); ?>

                                                </a>
                                            </td> 

                                            <td class="text-center vcenter">
                                                <?php if(!empty($subSubSubEvInfo['ci_mod_lock'])): ?>
                                                <span class="label label-sm label-purple"><?php echo app('translator')->get('label.FORWORDED'); ?></span>
                                                <?php elseif(!empty($subSubSubEvInfo['ci_mod'])): ?>
                                                <span class="label label-sm label-blue-steel"><?php echo app('translator')->get('label.DRAFTED'); ?></span>
                                                <?php else: ?>
                                                <span class="label label-sm label-grey-mint"><?php echo app('translator')->get('label.NO_MARKING_PUT_YET'); ?></span>
                                                <?php endif; ?>
                                            </td>
            <!--                                <td class="text-center vcenter">
                                                <?php if(!empty($subSubSubEvInfo['comdt_mod_lock'])): ?>
                                                <span class="label label-sm label-purple"><?php echo app('translator')->get('label.FORWORDED'); ?></span>
                                                <?php elseif(!empty($subSubSubEvInfo['comdt_mod'])): ?>
                                                <span class="label label-sm label-blue-steel"><?php echo app('translator')->get('label.DRAFTED'); ?></span>
                                                <?php else: ?>
                                                <span class="label label-sm label-grey-mint"><?php echo app('translator')->get('label.NO_MARKING_PUT_YET'); ?></span>
                                                <?php endif; ?>
                                            </td>-->

                                            <?php
                                            if ($i < ($rowSpanArr['event'][$termId][$eventId] - 1)) {
                                                if ($j < ($rowSpanArr['sub_event'][$termId][$eventId][$subEventId] - 1)) {
                                                    if ($k < ($rowSpanArr['sub_sub_event'][$termId][$eventId][$subEventId][$subSubEventId] - 1)) {
                                                        echo '</tr>';
                                                    }
                                                }
                                            }
                                            $k++;
                                            ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                            <?php
                                            $j++;
                                            ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                            <?php
                                            $i++;
                                            ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="9"><?php echo app('translator')->get('label.NO_MARKING_GROUP_IS_ASSIGNED_YET'); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--            ds observation marking Status-->
                    <div class="row margin-top-30">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover table-head-fixer-color">
                                <thead>
                                    <tr>
                                        <th class="vcenter" colspan="2"><?php echo app('translator')->get('label.DS_OBSN'); ?> <?php echo app('translator')->get('label.PROGRESS'); ?></th>
                                        <th class="vcenter text-center">
                                            <?php
                                            $color = !empty($assessmentActDeactArr[3][0][0][0][0]) ? 'green-steel' : 'red-intense';
                                            $title = !empty($assessmentActDeactArr[3][0][0][0][0]) ? __('label.ACTIVATED') : __('label.DEACTIVATED');
                                            ?>
                                            <i class="fa fa-power-off bold text-<?php echo e($color); ?> tooltips" title="<?php echo e($title); ?>"></i>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                                        <th class="vcenter text-center"><?php echo app('translator')->get('label.DS'); ?></th>
                                        <th class="vcenter text-center"><?php echo app('translator')->get('label.MARKING_STATUS'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($dsDataList)): ?>
                                    <?php $sl = 0; ?>
                                    <?php $__currentLoopData = $dsDataList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dsId => $dsInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $src = URL::to('/') . '/public/img/unknown.png';
                                    $alt = $dsInfo['ds_name'] ?? '';
                                    $personalNo = !empty($dsInfo['personal_no']) ? '(' . $dsInfo['personal_no'] . ')' : '';
                                    if (!empty($dsInfo['photo']) && File::exists('public/uploads/user/' . $dsInfo['photo'])) {
                                        $src = URL::to('/') . '/public/uploads/user/' . $dsInfo['photo'];
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center vcenter"><?php echo ++$sl; ?></td>
                                        <td class="text-center vcenter">
                                            <span class="tooltips" data-html="true" data-placement="bottom" title="
                                                  <div class='text-center'>
                                                  <img width='50' height='60' src='<?php echo $src; ?>' alt='<?php echo $alt; ?>'/><br/>
                                                  <strong><?php echo $alt; ?><br/>
                                                  <?php echo $personalNo; ?> </strong>
                                                  </div>
                                                  ">
                                                <?php echo e($dsInfo['official_name'] ?? ''); ?>

                                            </span>
                                        </td>

                                        <td class="text-center vcenter width-160">
                                            <?php if(!empty($dsObservationMarkingArr) && array_key_exists($dsId, $dsObservationMarkingArr)): ?>
                                            <?php if(!empty($dsObservationMarkingLockArr) && array_key_exists($dsId, $dsObservationMarkingLockArr)): ?>
                                            <span class="label label-sm label-purple"><?php echo app('translator')->get('label.FORWORDED'); ?></span>
                                            <?php else: ?>
                                            <span class="label label-sm label-blue-steel"><?php echo app('translator')->get('label.DRAFTED'); ?></span>
                                            <?php endif; ?>
                                            <?php else: ?>
                                            <span class="label label-sm label-grey-mint"><?php echo app('translator')->get('label.NO_MARKING_PUT_YET'); ?></span>
                                            <?php endif; ?>

                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="3"><?php echo app('translator')->get('label.NO_DATA_FOUND'); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Start Course Status Summary modal -->
<div class="modal fade" id="modalInfo" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showCourseStatus"></div>
    </div>
</div>
<!--End Start Course Status Summary modal -->


<!-- DS Marking Summary modal -->
<div class="modal fade test" id="dsMarkingSummaryModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showDsMarkingSummary"></div>
    </div>
</div>
<!-- End DS Marking Summary modal -->


<script type="text/javascript">
    $(function () {
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        $(".table-head-fixer-color").tableHeadFixer();

        //DS Marking Summary Modal
        $(document).on('click', '.ds-marking-status', function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var dataId = $(this).attr('data-id');
            var termId = $(this).attr('term-id');
            var eventId = $(this).attr('event-id');
            var subEventId = $(this).attr('sub-event-id');
            var subSubEventId = $(this).attr('sub-sub-event-id');
            var subSubSubEventId = $(this).attr('sub-sub-sub-event-id');
            $.ajax({
                url: "<?php echo e(URL::to('mksSubmissionState/getDsMarkingSummary')); ?>",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    data_id: dataId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    $('#showDsMarkingSummary').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showDsMarkingSummary').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    if (jqXhr.status == 400) {
                        var errorsHtml = '';
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, 'Error', options);
                    } else {
                        toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
                    }
                    App.unblockUI();
                }

            });
        });


    });
</script>
<script src="<?php echo e(asset('public/js/custom.js')); ?>" type="text/javascript"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/mksSubmissionState/index.blade.php ENDPATH**/ ?>