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
    <td class="text-center vcenter content-sl" data-key="{{$contentKey}}"></td>
    {!! Form::hidden('content['. $contentKey .'][content_order]', null, ['id' => 'contentOrder_'.$contentKey, 'class' => 'content-order', 'data-key' => $contentKey]) !!}
    <td class="text-center vcenter">
        {!! Form::select('content['. $contentKey .'][content_type]', $contentTypeArr , $cType, ['id' => 'contentType_'.$contentKey, 'class' => 'form-control js-source-states content-type width-full', 'data-key' => $contentKey]) !!}
    </td>
    <td class="vcenter">
        <div class="form-group margin-bottom-0">

            <div class="col-md-12 upload-doc-{{$contentKey}} {{$display[1] ?? 'display-none'}}">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn green-seagreen btn-file">
                        <span class="fileinput-new"> @lang('label.SELECT_DOC') </span>
                        <span class="fileinput-exists">@lang('label.CHANGE')</span>
                        {!! Form::file('content['. $contentKey .'][doc]',['id'=> 'contentDoc_'.$contentKey, 'data-key' => $contentKey]) !!}
                        @if(!empty($fileArr['file_name']) && $request->content_type == 1)
                        {!! Form::hidden('content['.$contentKey .'][prev_doc]', $fileArr['file_name']) !!}
                        {!! Form::hidden('content['.$contentKey .'][prev_doc_original]', $fileArr['file_original_name']) !!}
                        @endif
                    </span>
                    @if(!empty($fileArr['file_name']) && $request->content_type == 1)
                    <a href="{{URL::to('public/uploads/content/file/'.$fileArr['file_name'])}}"
                       class="btn green-jungle btn-md tooltips" title="@lang('label.UPOLADED_DOC_PREVIEW')" target="_blank">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                    @endif
                    <span class="fileinput-filename width-250">{!! !empty($fileArr['file_original_name']) && $request->content_type == 1 ? $fileArr['file_original_name'] : '' !!}</span>&nbsp;
                    <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>
                </div>
                <div class="clearfix">
                    <?php
                    $fileFormat = !empty($contentTypeDataArr[1]['description']) ? $contentTypeDataArr[1]['description'] : '';
                    $fileSize = !empty($contentTypeDataArr[1]['file_size']) ? $contentTypeDataArr[1]['file_size'] : '';
                    ?>
                    <span class="label label-danger">@lang('label.NOTE')</span><br> @lang('label.CONTENT_FORMAT_DESCRIPTION', ['file_format'=> $fileFormat, 'file_size' =>$fileSize])
                </div>
            </div>
            <div class="col-md-12 upload-photo-{{$contentKey}} {{$display[2] ?? 'display-none'}}">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 50px; height: 60px;"> 
                        @if(!empty($fileArr['file_name']) && $request->content_type == 2)
                        <img src="{{URL::to('/')}}/public/uploads/content/photo/{{$fileArr['file_name']}}" alt="{{ $fileArr['file_original_name']}}"/>
                        @endif
                    </div>
                    <div>
                        <span class="btn green-seagreen btn-outline btn-file">
                            <span class="fileinput-new"> @lang('label.SELECT_IMAGE') </span>
                            <span class="fileinput-exists">@lang('label.CHANGE')</span>

                            {!! Form::file('content['. $contentKey .'][photo]', ['id'=> 'contentPhoto_'.$contentKey, 'class' => 'form-control', 'data-key' => $contentKey]) !!} 
                            @if(!empty($fileArr['file_name']) && $request->content_type == 2)
                            {!! Form::hidden('content['.$contentKey .'][prev_photo]', $fileArr['file_name']) !!}
                            {!! Form::hidden('content['.$contentKey .'][prev_photo_original]', $fileArr['file_original_name']) !!}
                            @endif
                        </span>
                        @if(!empty($fileArr['file_name']) && $request->content_type == 2)
                        <a href="javascript:;" class="btn green-seagreen" data-dismiss="fileinput"> Remove </a>
                        @else
                        <a href="javascript:;" class="btn green-seagreen fileinput-exists" data-dismiss="fileinput"> Remove </a>
                        @endif
                    </div>
                </div>
                <div class="clearfix margin-top-10">
                    <?php
                    $fileFormat = !empty($contentTypeDataArr[2]['description']) ? $contentTypeDataArr[2]['description'] : '';
                    $fileSize = !empty($contentTypeDataArr[2]['file_size']) ? $contentTypeDataArr[2]['file_size'] : '';
                    ?>
                    <span class="label label-danger">@lang('label.NOTE')</span><br> @lang('label.CONTENT_FORMAT_DESCRIPTION', ['file_format'=> $fileFormat, 'file_size' =>$fileSize])
                </div>
            </div>
            <div class="col-md-12 upload-video-{{$contentKey}} {{$display[3] ?? 'display-none'}}">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn green-seagreen btn-file">
                        <span class="fileinput-new"> @lang('label.SELECT_VIDEO') </span>
                        <span class="fileinput-exists">@lang('label.CHANGE')</span>

                        {!! Form::file('content['. $contentKey .'][video]', ['id'=> 'contentVideo_'.$contentKey, 'class' => 'form-control', 'autocomplete' => 'off', 'data-key' => $contentKey]) !!} 
                        @if(!empty($fileArr['file_name']) && $request->content_type == 3)
                        {!! Form::hidden('content['.$contentKey .'][prev_video]', $fileArr['file_name']) !!}
                        {!! Form::hidden('content['.$contentKey .'][prev_video_original]', $fileArr['file_original_name']) !!}
                        @endif
                    </span>
                    @if(!empty($fileArr['file_name']) && $request->content_type == 3)
                    <a href="{{URL::to('public/uploads/content/video/'.$fileArr['file_name'])}}"
                       class="btn yellow-casablanca btn-md tooltips" title="@lang('label.UPOLADED_VIDEO_PREVIEW')" target="_blank">
                        <i class="fa fa-file-movie-o" aria-hidden="true"></i>
                    </a>
                    @endif
                    <span class="fileinput-filename width-250">{!! !empty($fileArr['file_original_name']) && $request->content_type == 1 ? $fileArr['file_original_name'] : '' !!}</span>&nbsp;
                    <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>
                </div>
                <div class="clearfix margin-top-10">
                    <?php
                    $fileFormat = !empty($contentTypeDataArr[3]['description']) ? $contentTypeDataArr[3]['description'] : '';
                    $fileSize = !empty($contentTypeDataArr[3]['file_size']) ? $contentTypeDataArr[3]['file_size'] : '';
                    ?>
                    <span class="label label-danger">@lang('label.NOTE')</span><br> @lang('label.CONTENT_FORMAT_DESCRIPTION', ['file_format'=> $fileFormat, 'file_size' =>$fileSize])
                </div>
            </div>
            <div class="col-md-12 upload-url-{{$contentKey}} {{$display[4] ?? 'display-none'}}">
                <?php
                $urlExample = !empty($contentTypeDataArr[4]['description']) ? $contentTypeDataArr[4]['description'] : '';
                ?>

                {!! Form::text('content['. $contentKey .'][url]', null, ['id'=> 'contentUrl_'.$contentKey, 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => $urlExample, 'data-key' => $contentKey]) !!}  
            </div>
        </div>
    </td>

    <td class="text-center vcenter width-50">
        <button class="btn btn-inline purple-soft content-up tooltips btn-xs" data-key="{{$contentKey}}" data-placement="top" title="@lang('label.MOVE_CONTENT_UP')" type="button">
            <i class="fa fa-long-arrow-up bold"></i>
        </button>
        <button class="btn btn-inline purple-soft content-down tooltips btn-xs" data-key="{{$contentKey}}" data-placement="top" title="@lang('label.MOVE_CONTENT_DOWN')" type="button">
            <i class="fa fa-long-arrow-down bold"></i>
        </button>
    </td>
    <td class="text-center vcenter width-50">
        <button class="btn btn-inline btn-danger remove-content-row tooltips btn-xs" data-key="{{$contentKey}}" data-placement="right" title="@lang('label.REMOVE_CONTENT')" type="button">
            <i class="fa fa-times"></i>
        </button>
    </td>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
</tr>

