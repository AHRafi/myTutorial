
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.CLASSIFICATION_WISE_DOC_REPORT'); ?>
            </div>
        </div>

        <div class="portlet-body">

            <?php echo Form::open(array('group' => 'form', 'url' => 'classificationWiseDocReport/filter','class' => 'form-horizontal')); ?>

            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="control-label col-md-5"><?php echo app('translator')->get('label.CONTENT_CLASSIFICATION'); ?> </label>
                        <div class="col-md-7">
                            <div class="input-group">
                                <?php echo Form::select('classification_id', $contentClassificationList ,Request::get('classification_id'), ['class' => 'form-control js-source-states', 'readonly' => '']); ?> 
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-5 pull-left">
                    <div class="form-group">
                        <!--                        <label class="control-label col-md-4" for="">&nbsp;</label>-->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> <?php echo app('translator')->get('label.GENERATE'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
			
            <?php echo Form::close(); ?>

            <?php if($request->generate == 'true'): ?>

            <?php if(!empty($targetArr)): ?>
            <div class="row">

                <div class="col-md-12 text-right">


                    <a class="btn btn-md print btn-primary vcenter" target="_blank"  href="<?php echo URL::full().'&view=print&print_option=1'; ?>">
                        <span class="tooltips" title="<?php echo app('translator')->get('label.PRINT'); ?>"><i class="fa fa-print"></i> </span> 
                    </a>



                    <!--                                        <a class="btn btn-success vcenter" href="<?php echo URL::full().'&view=pdf'; ?>">
                                                                <span class="tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_PDF'); ?>"><i class="fa fa-file-pdf-o"></i></span>
                                                            </a>-->
                    <a class="btn btn-warning vcenter" href="<?php echo URL::full().'&view=excel'; ?>">
                        <span class="tooltips" title="<?php echo app('translator')->get('label.DOWNLOAD_EXCEL'); ?>"><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">

                            <?php echo e(__('label.CONTENT_CLASSIFICATION')); ?> : <strong><?php echo e(!empty(Request::get('classification_id')) && Request::get('classification_id') != 0 ? $contentClassificationList[Request::get('classification_id')] : __('label.N_A')); ?> |</strong>
                            <?php echo e(__('label.TOTAL_NO_OF_DOCUMENT')); ?> : <strong><?php echo e(!empty($targetArr) ? sizeof($targetArr) : 0); ?></strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <div class="table-responsive max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.TITLE'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.MODULE'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.CONTENT_CATEGORY'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.ORIGINATOR'); ?></th>
                                    <th class="text-center vcenter"><?php echo app('translator')->get('label.DATE_OF_UPLOAD'); ?></th>
                                    <th class="text-center vcenter"><?php echo app('translator')->get('label.CONTENT'); ?></th>
                                    <th class="vcenter"><?php echo app('translator')->get('label.SHORT_DESCRIPTION'); ?></th>
                                    <th class="text-center vcenter"><?php echo app('translator')->get('label.STATUS'); ?></th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($targetArr)): ?>
                                <?php
                                $sl = 0;
                                ?>
                                <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center vcenter">
                                        <?php echo e(++$sl); ?>

                                    </td>
                                    <td class="vcenter width-200">
                                        <div class="width-inherit">
                                            <?php echo e($target['title'] ?? ''); ?>&nbsp;
                                            <?php if(!empty($target['content_classification_id'])): ?>
                                            <span class="bold tooltips" title="<?php echo e($target['content_classification_name']); ?>"><i class="<?php echo e($target['content_classification_icon']); ?> font-<?php echo e($target['content_classification_color']); ?>"></i></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class=" vcenter"><?php echo e($target['module_name'] ?? ''); ?></td>
                                    <td class=" vcenter"><?php echo e($target['content_cat'] ?? ''); ?></td> 
                                    <td class="vcenter">
                                        <?php if(!empty($target['origin'])): ?>
                                        <?php if($target['origin'] == '1' ): ?>
                                        <?php echo e($target['user_official_name'] ?? ''); ?>

                                        <?php elseif($target['origin'] == '2' ): ?>
                                        <?php echo e($target['cm_official_name'] ?? ''); ?>

                                        <?php elseif($target['origin'] == '3' ): ?>
                                        <?php echo e($target['staff_official_name'] ?? ''); ?>

                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center vcenter"><?php echo e(!empty($target['date_upload']) ? Helper::formatDate($target['date_upload']) : ''); ?></td>

                                    <td class="td-actions vcenter text-center">
                                        <div class="width-inherit">
                                            <?php if(!empty($target['content_details'])): ?>

                                            <?php echo e(Form::open(array('url' => 'classificationWiseDocReport/downloadFile', 'class' => 'download-file-form'))); ?>

                                            <?php echo e(Form::hidden('_method', 'POST')); ?>

                                            <?php $__currentLoopData = $target['content_details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailsId => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                            $color = 'grey-mint';
                                            $icon = 'times-circle';
                                            $original = '';
                                            if (!empty($detail['content_type'])) {
                                                if ($detail['content_type'] == 1) {
                                                    $color = 'green-jungle';
                                                    $icon = 'file-pdf-o';
                                                    $original = $detail['content_original'] ?? '';
                                                } elseif ($detail['content_type'] == 2) {
                                                    $color = 'blue-steel';
                                                    $icon = 'file-image-o';
                                                    $original = $detail['content_original'] ?? '';
                                                } elseif ($detail['content_type'] == 3) {
                                                    $color = 'yellow-casablanca';
                                                    $icon = 'file-movie-o';
                                                    $original = $detail['content_original'] ?? '';
                                                } elseif ($detail['content_type'] == 4) {
                                                    $color = 'purple-sharp';
                                                    $icon = 'link';
                                                    $original = $detail['content'] ?? '';
                                                }
                                            }
                                            ?>
                                            <?php if($detail['content_type'] == 4): ?>
                                            <a class="btn btn-xs <?php echo e($color); ?> download-content tooltips" title="<?php echo e($original); ?>"
                                               data-content="<?php echo e($detail['content']); ?>" data-original="<?php echo e($detail['content_original']); ?>" data-content-type="<?php echo e($detail['content_type']); ?>">
                                                <i class="fa fa-<?php echo e($icon); ?>"> </i>
                                            </a>
                                            <?php else: ?>
                                            <button class="btn btn-xs <?php echo e($color); ?> download-content tooltips" title="<?php echo e($original); ?>"
                                                    data-content="<?php echo e($detail['content']); ?>" data-original="<?php echo e($detail['content_original']); ?>" data-content-type="<?php echo e($detail['content_type']); ?>">
                                                <i class="fa fa-<?php echo e($icon); ?>"> </i>
                                            </button>
                                            <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e(Form::close()); ?>

                                            <?php else: ?>
                                            <span class="label label-sm label-grey-mint tooltips" title="<?php echo app('translator')->get('label.NO_CONTENT_UPLOADED'); ?>">
                                                <i class="fa fa-times-circle"></i>
                                            </span>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                    <td class="vcenter"><?php echo e($target['short_description'] ?? ''); ?></td>


                                    <td class="text-center vcenter">
                                        <?php if($target['status'] == '1'): ?>
                                        <span class="label label-sm label-success"><?php echo app('translator')->get('label.ACTIVE'); ?></span>
                                        <?php else: ?>
                                        <span class="label label-sm label-warning"><?php echo app('translator')->get('label.INACTIVE'); ?></span>
                                        <?php endif; ?>
                                    </td>


                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="9" class="vcenter"><?php echo app('translator')->get('label.NO_CONTENT_FOUND'); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <?php endif; ?>

        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $(document).on("click", '.download-content', function (e) {
            var content = $(this).attr("data-content");
            var contentOriginal = $(this).attr("data-original");
            var contentType = $(this).attr("data-content-type");

            if (contentType == 4) {
                var a = document.createElement("a");
                a.href = content;
                a.setAttribute("download", content);
//                a.click();
                window.open(content, '_blank');
            } else {
                var form = $(this).parents('form');
                $('<input>').attr('type', 'hidden')
                        .attr('name', 'content')
                        .attr('value', content)
                        .appendTo(form);
                $('<input>').attr('type', 'hidden')
                        .attr('name', 'content_original')
                        .attr('value', contentOriginal)
                        .appendTo(form);
                $('<input>').attr('type', 'hidden')
                        .attr('name', 'content_type')
                        .attr('value', contentType)
                        .appendTo(form);
//                form.put('content', content);
//                form.put('content_original', contentOriginal);
//                form.put('content_path', path);
                form.submit();
            }


        });
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/referenceArchive/classificationWiseDocReport/index.blade.php ENDPATH**/ ?>