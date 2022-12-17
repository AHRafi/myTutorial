
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-clipboard"></i><?php echo app('translator')->get('label.CONTENT_LIST'); ?>
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="<?php echo e(URL::to('content/create'.Helper::queryPageStr($qpArr))); ?>"> <?php echo app('translator')->get('label.CREATE_NEW_CONTENT'); ?>
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <div class="col-md-12">
                    <!-- Begin Filter-->
                    <?php echo Form::open(array('group' => 'form', 'url' => 'content/filter','class' => 'form-horizontal')); ?>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="filSearch"><?php echo app('translator')->get('label.SEARCH'); ?></label>
                                <div class="col-md-8">
                                    <?php echo Form::text('fil_search',  Request::get('fil_search'), ['class' => 'form-control tooltips', 'id' => 'filSearch', 'title' => 'Title', 'placeholder' => 'Title', 'list' => 'contentTitle', 'autocomplete' => 'off']); ?> 
                                    <datalist id="contentTitle">
                                        <?php if(!$nameArr->isEmpty()): ?>
                                        <?php $__currentLoopData = $nameArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->title); ?>" />
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="form">
                                <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                    <i class="fa fa-search"></i> <?php echo app('translator')->get('label.FILTER'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                    </div>
                    <?php echo Form::close(); ?>

                    <!-- End Filter -->
                </div>
            </div>

            <div class="table-responsive max-height-500 webkit-scrollbar">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.TITLE'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.MODULE'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.CONTENT_CATEGORY'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.ORIGINATOR'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.COURSE'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.OUTPUT_ACCESS'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.DATE_OF_UPLOAD'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.CONTENT'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.SHORT_DESCRIPTION'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.STATUS'); ?></th>
                            <th class="td-actions text-center vcenter"><?php echo app('translator')->get('label.ACTION'); ?></th>
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
                            <td class="vcenter"><?php echo e($target['module_name'] ?? ''); ?></td>
                            <td class="vcenter"><?php echo e($target['content_cat'] ?? ''); ?></td> 
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
                            <td class="vcenter"><?php echo e($target['course_name'] ?? ''); ?></td> 
                            <td class="text-center vcenter width-120">
                                <div class="width-inherit">
                                    <?php $comptArr = !empty($target['output_access']) ? explode(',', $target['output_access']) : []; ?>
                                    <?php if(!empty($comptArr)): ?>
                                    <?php $__currentLoopData = $comptArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comptId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $compt = !empty($comptId) && !empty($compartmentList[$comptId]) ? $compartmentList[$comptId] : __('label.N_A');
                                    $comptColor = empty($comptId) ? 'grey-mint' : ($comptId == '1' ? 'purple-sharp' : ($comptId == '2' ? 'blue-steel' : ($comptId == '3' ? 'yellow' : 'grey-mint')));
                                    ?>
                                    <span class="label label-sm label-<?php echo e($comptColor); ?>"><?php echo $compt; ?></span>&nbsp; 
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center vcenter"><?php echo e(!empty($target['date_upload']) ? Helper::formatDate($target['date_upload']) : ''); ?></td>

                            <td class="td-actions vcenter text-center">
                                <div class="width-inherit">
                                    <?php if(!empty($target['content_details'])): ?>

                                    <?php echo e(Form::open(array('url' => 'content/downloadFile', 'class' => 'download-file-form'))); ?>

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
                            <td class="td-actions vcenter text-center">
                                <div class="width-inherit">
                                    <?php echo e(Form::open(array('url' => 'content/' . $target['id'].Helper::queryPageStr($qpArr)))); ?>

                                    <?php echo e(Form::hidden('_method', 'DELETE')); ?>


                                    <a class="btn btn-xs btn-primary tooltips " title="Edit" href="<?php echo e(URL::to('content/' . $target['id'] . '/edit'.Helper::queryPageStr($qpArr))); ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <?php echo e(Form::close()); ?>

                                </div>
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
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/content/index.blade.php ENDPATH**/ ?>