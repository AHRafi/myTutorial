<?php
$contentKey = 'nc' . uniqid();
$display = [];
$cType = !empty($request->content_type) ? $request->content_type : 1;
if (!empty($contentTypeArr)) {
    foreach ($contentTypeArr as $typeId => $type) {
        $display[$typeId] = !empty($cType) && $cType == $typeId ? '' : 'display-none';
    }
}
?>
<tr>
    <td class="text-center vcenter content-sl" data-key="<?php echo e($contentKey); ?>"></td>
    <?php echo Form::hidden('content['. $contentKey .'][content_order]', null, ['id' => 'contentOrder_'.$contentKey, 'class' => 'content-order', 'data-key' => $contentKey]); ?>

    <td class="text-center vcenter">
        <?php echo Form::select('content['. $contentKey .'][content_type]', $contentTypeArr , $cType, ['id' => 'contentType_'.$contentKey, 'class' => 'form-control js-source-states content-type width-full', 'data-key' => $contentKey]); ?>

    </td>
    <td class="vcenter">
        <div class="form-group margin-bottom-0">

            <div class="col-md-12 upload-doc-<?php echo e($contentKey); ?> <?php echo e($display[1] ?? 'display-none'); ?>">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn green-seagreen btn-file">
                        <span class="fileinput-new"> <?php echo app('translator')->get('label.SELECT_DOC'); ?> </span>
                        <span class="fileinput-exists"><?php echo app('translator')->get('label.CHANGE'); ?></span>
                        <?php echo Form::file('content['. $contentKey .'][doc]',['id'=> 'contentDoc_'.$contentKey, 'data-key' => $contentKey]); ?>

                        <?php if(!empty($fileArr['file_name']) && $request->content_type == 1): ?>
                        <?php echo Form::hidden('content['.$contentKey .'][prev_doc]', $fileArr['file_name']); ?>

                        <?php echo Form::hidden('content['.$contentKey .'][prev_doc_original]', $fileArr['file_original_name']); ?>

                        <?php endif; ?>
                    </span>
                    <?php if(!empty($fileArr['file_name']) && $request->content_type == 1): ?>
                    <a href="<?php echo e(URL::to('public/uploads/content/file/'.$fileArr['file_name'])); ?>"
                       class="btn green-jungle btn-md tooltips" title="<?php echo app('translator')->get('label.UPOLADED_DOC_PREVIEW'); ?>" target="_blank">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                    <span class="fileinput-filename width-250"><?php echo !empty($fileArr['file_original_name']) && $request->content_type == 1 ? $fileArr['file_original_name'] : ''; ?></span>&nbsp;
                    <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>
                </div>
                <div class="clearfix">
                    <?php
                    $fileFormat = !empty($contentTypeDataArr[1]['description']) ? $contentTypeDataArr[1]['description'] : '';
                    $fileSize = !empty($contentTypeDataArr[1]['file_size']) ? $contentTypeDataArr[1]['file_size'] : '';
                    ?>
                    <span class="label label-danger"><?php echo app('translator')->get('label.NOTE'); ?></span><br> <?php echo app('translator')->get('label.CONTENT_FORMAT_DESCRIPTION', ['file_format'=> $fileFormat, 'file_size' =>$fileSize]); ?>
                </div>
            </div>
            <div class="col-md-12 upload-photo-<?php echo e($contentKey); ?> <?php echo e($display[2] ?? 'display-none'); ?>">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 50px; height: 60px;"> 
                        <?php if(!empty($fileArr['file_name']) && $request->content_type == 2): ?>
                        <img src="<?php echo e(URL::to('/')); ?>/public/uploads/content/photo/<?php echo e($fileArr['file_name']); ?>" alt="<?php echo e($fileArr['file_original_name']); ?>"/>
                        <?php endif; ?>
                    </div>
                    <div>
                        <span class="btn green-seagreen btn-outline btn-file">
                            <span class="fileinput-new"> <?php echo app('translator')->get('label.SELECT_IMAGE'); ?> </span>
                            <span class="fileinput-exists"><?php echo app('translator')->get('label.CHANGE'); ?></span>

                            <?php echo Form::file('content['. $contentKey .'][photo]', ['id'=> 'contentPhoto_'.$contentKey, 'class' => 'form-control', 'data-key' => $contentKey]); ?> 
                            <?php if(!empty($fileArr['file_name']) && $request->content_type == 2): ?>
                            <?php echo Form::hidden('content['.$contentKey .'][prev_photo]', $fileArr['file_name']); ?>

                            <?php echo Form::hidden('content['.$contentKey .'][prev_photo_original]', $fileArr['file_original_name']); ?>

                            <?php endif; ?>
                        </span>
                        <?php if(!empty($fileArr['file_name']) && $request->content_type == 2): ?>
                        <a href="javascript:;" class="btn green-seagreen" data-dismiss="fileinput"> Remove </a>
                        <?php else: ?>
                        <a href="javascript:;" class="btn green-seagreen fileinput-exists" data-dismiss="fileinput"> Remove </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="clearfix margin-top-10">
                    <?php
                    $fileFormat = !empty($contentTypeDataArr[2]['description']) ? $contentTypeDataArr[2]['description'] : '';
                    $fileSize = !empty($contentTypeDataArr[2]['file_size']) ? $contentTypeDataArr[2]['file_size'] : '';
                    ?>
                    <span class="label label-danger"><?php echo app('translator')->get('label.NOTE'); ?></span><br> <?php echo app('translator')->get('label.CONTENT_FORMAT_DESCRIPTION', ['file_format'=> $fileFormat, 'file_size' =>$fileSize]); ?>
                </div>
            </div>
            <div class="col-md-12 upload-video-<?php echo e($contentKey); ?> <?php echo e($display[3] ?? 'display-none'); ?>">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn green-seagreen btn-file">
                        <span class="fileinput-new"> <?php echo app('translator')->get('label.SELECT_VIDEO'); ?> </span>
                        <span class="fileinput-exists"><?php echo app('translator')->get('label.CHANGE'); ?></span>

                        <?php echo Form::file('content['. $contentKey .'][video]', ['id'=> 'contentVideo_'.$contentKey, 'class' => 'form-control', 'autocomplete' => 'off', 'data-key' => $contentKey]); ?> 
                        <?php if(!empty($fileArr['file_name']) && $request->content_type == 3): ?>
                        <?php echo Form::hidden('content['.$contentKey .'][prev_video]', $fileArr['file_name']); ?>

                        <?php echo Form::hidden('content['.$contentKey .'][prev_video_original]', $fileArr['file_original_name']); ?>

                        <?php endif; ?>
                    </span>
                    <?php if(!empty($fileArr['file_name']) && $request->content_type == 3): ?>
                    <a href="<?php echo e(URL::to('public/uploads/content/video/'.$fileArr['file_name'])); ?>"
                       class="btn yellow-casablanca btn-md tooltips" title="<?php echo app('translator')->get('label.UPOLADED_VIDEO_PREVIEW'); ?>" target="_blank">
                        <i class="fa fa-file-movie-o" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                    <span class="fileinput-filename width-250"><?php echo !empty($fileArr['file_original_name']) && $request->content_type == 1 ? $fileArr['file_original_name'] : ''; ?></span>&nbsp;
                    <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>
                </div>
                <div class="clearfix margin-top-10">
                    <?php
                    $fileFormat = !empty($contentTypeDataArr[3]['description']) ? $contentTypeDataArr[3]['description'] : '';
                    $fileSize = !empty($contentTypeDataArr[3]['file_size']) ? $contentTypeDataArr[3]['file_size'] : '';
                    ?>
                    <span class="label label-danger"><?php echo app('translator')->get('label.NOTE'); ?></span><br> <?php echo app('translator')->get('label.CONTENT_FORMAT_DESCRIPTION', ['file_format'=> $fileFormat, 'file_size' =>$fileSize]); ?>
                </div>
            </div>
            <div class="col-md-12 upload-url-<?php echo e($contentKey); ?> <?php echo e($display[4] ?? 'display-none'); ?>">
                <?php
                $urlExample = !empty($contentTypeDataArr[4]['description']) ? $contentTypeDataArr[4]['description'] : '';
                ?>

                <?php echo Form::text('content['. $contentKey .'][url]', null, ['id'=> 'contentUrl_'.$contentKey, 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => $urlExample, 'data-key' => $contentKey]); ?>  
            </div>
        </div>
    </td>

    <td class="text-center vcenter width-50">
        <button class="btn btn-inline purple-soft content-up tooltips btn-xs" data-key="<?php echo e($contentKey); ?>" data-placement="top" title="<?php echo app('translator')->get('label.MOVE_CONTENT_UP'); ?>" type="button">
            <i class="fa fa-long-arrow-up bold"></i>
        </button>
        <button class="btn btn-inline purple-soft content-down tooltips btn-xs" data-key="<?php echo e($contentKey); ?>" data-placement="top" title="<?php echo app('translator')->get('label.MOVE_CONTENT_DOWN'); ?>" type="button">
            <i class="fa fa-long-arrow-down bold"></i>
        </button>
    </td>
    <td class="text-center vcenter width-50">
        <button class="btn btn-inline btn-danger remove-content-row tooltips btn-xs" data-key="<?php echo e($contentKey); ?>" data-placement="right" title="<?php echo app('translator')->get('label.REMOVE_CONTENT'); ?>" type="button">
            <i class="fa fa-times"></i>
        </button>
    </td>
<script src="<?php echo e(asset('public/js/custom.js')); ?>" type="text/javascript"></script>
</tr>

<?php /**PATH C:\xampp\htdocs\afwc\resources\views/content/addContentRow.blade.php ENDPATH**/ ?>