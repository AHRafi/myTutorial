@extends('layouts.default.master')
@section('data_count')	
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-clipboard"></i>@lang('label.EDIT_NEW_CONTENT')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::model($target, array('group' => 'form', 'url' => '#', 'files'=> true, 'class' => 'form-horizontal', 'id' => 'updateContentForm' )) !!}
            {!! Form::hidden('filter', Helper::queryPageStr($qpArr)) !!}
            {!! Form::hidden('id', $target->id) !!}
            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-4">

                        <div class="form-group">
                            <label class="control-label col-md-4" for="originator">@lang('label.ORIGINATOR') :</label>
                            <div class="col-md-4">
                                <div class="control-label pull-left"> 
                                    <strong> {{!empty($originatorInfo->name) ? $originatorInfo->name : Auth::user()->official_name}} </strong>
                                </div>
                                {!! Form::hidden('originator',  !empty($target->originator) ? $target->originator : Auth::user()->id, ['id'=> 'originator']) !!} 
                                {!! Form::hidden('origin',  !empty($target->origin) ? $target->origin : '1', ['id'=> 'origin']) !!} 
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :</label>
                            <div class="col-md-4">
                                <div class="control-label pull-left"> 
                                    <strong> {{$activeCourse->name}} </strong>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="title">@lang('label.TITLE') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('title', $target->title, ['id'=> 'title', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('title') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="dateOfUpload">@lang('label.DATE_OF_UPLOAD') :<span class="text-danger"> *</span></label>

                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('date_upload', !empty($target->date_upload) ? Helper::formatDate($target->date_upload) : date('d F Y'), ['id'=> 'dateOfUpload', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="dateOfUpload">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="shortDescription">@lang('label.SHORT_DESCRIPTION') :</label>
                            <div class="col-md-8">
                                {!! Form::textarea('short_description', $target->short_description, ['id'=> 'shortDescription', 'class' => 'form-control', 'autocomplete' => 'off', 'size' => '10x1']) !!} 
                                <span class="text-danger">{{ $errors->first('short_description') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="contentClassificationId">@lang('label.CONTENT_CLASSIFICATION') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group bootstrap-touchspin width-full">
                                    {!! Form::select('content_classification_id', $contentClassificationArr , null, ['id'=> 'contentClassificationId', 'class' => 'form-control js-source-states']) !!} 
                                    <span class="input-group-addon bootstrap-touchspin-postfix bold cnt-cls-postfix">
                                        <?php $cntClsArr = !empty($contentClassArr[$target->content_classification_id]) ? $contentClassArr[$target->content_classification_id] : []; ?>
                                        @if(!empty($cntClsArr))
                                        <span class="bold tooltips" title="{{$cntClsArr['name']}}"><i class="{{$cntClsArr['icon']}} font-{{$cntClsArr['color']}}"></i></span>
                                        @endif
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('content_classification_id') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="moduleId">@lang('label.MODULE') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('module_id', $moduleArr, null, ['class' => 'form-control js-source-states', 'id' => 'moduleId']) !!}
                                <span class="text-danger">{{ $errors->first('module_id') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="contentCategory">@lang('label.CONTENT_CATEGORY') :<span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                {!! Form::select('content_category_id',$categoryArr, null, ['class' => 'form-control js-source-states', 'id' => 'contentCategoryId']) !!}
                                <span class="text-danger">{{ $errors->first('content_category_id') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="outputAccess">@lang('label.OUTPUT_ACCESS') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php
                                $outputAccess = !empty($target->output_access) ? explode(",", $target->output_access) : [];
                                ?>
                                {!! Form::select('output_access[]', $compartmentList, $outputAccess, ['id' => 'outputAccess', 'class' => 'form-control mt-multiselect btn btn-default', 'multiple']) !!}
                                <span class="text-danger">{{ $errors->first('output_access') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="status">@lang('label.STATUS') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('status', ['1' => __('label.ACTIVE'), '2' => __('label.INACTIVE')], null, ['class' => 'form-control', 'id' => 'status']) !!}
                                <span class="text-danger">{{ $errors->first('status') }}</span>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tbody>
                                <?php
                                $contentKey = 'mn0';
                                $multiContentTypeArr = $contentTypeArr;
                                unset($multiContentTypeArr[4]);
                                ?>
                                <tr>
                                    <td class="text-center vcenter bold">@lang('label.MULTIPLE_CONTENT_UPLOAD')</td>
                                    <td class="text-center vcenter">
                                        {!! Form::select('multi_content['. $contentKey .'][content_type]', $multiContentTypeArr , null, ['id' => 'contentType_'.$contentKey, 'class' => 'form-control js-source-states multi-content-type content-type width-full', 'data-key' => $contentKey]) !!}
                                    </td>
                                    <td class="vcenter">

                                        <div class="form-group margin-bottom-0">

                                            <div class="col-md-12 upload-doc-{{$contentKey}}">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <span class="btn green-seagreen btn-file">
                                                        <span class="fileinput-new"> @lang('label.SELECT_DOC') </span>
                                                        <span class="fileinput-exists">@lang('label.CHANGE')</span>

                                                        {!! Form::file('multi_content['. $contentKey .'][doc][]',['id'=> 'contentDoc_'.$contentKey, 'class' => 'multi-content', 'multiple'=> true]) !!}
                                                    </span>
                                                    <br>
                                                    <span class="fileinput-filename width-250"></span>&nbsp;
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
                                            <div class="col-md-12 upload-photo-{{$contentKey}} display-none">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 50px; height: 60px;"> </div>
                                                    <div>
                                                        <span class="btn green-seagreen btn-outline btn-file">
                                                            <span class="fileinput-new"> @lang('label.SELECT_IMAGE') </span>
                                                            <span class="fileinput-exists">@lang('label.CHANGE')</span>

                                                            {!! Form::file('multi_content['. $contentKey .'][photo][]', ['id'=> 'contentPhoto_'.$contentKey, 'class' => 'form-control multi-content', 'multiple'=> true]) !!} 
                                                        </span>
                                                        <a href="javascript:;" class="btn green-seagreen fileinput-exists" data-dismiss="fileinput"> Remove </a>
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
                                            <div class="col-md-12 upload-video-{{$contentKey}} display-none">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <span class="btn green-seagreen btn-file">
                                                        <span class="fileinput-new"> @lang('label.SELECT_VIDEO') </span>
                                                        <span class="fileinput-exists">@lang('label.CHANGE')</span>

                                                        {!! Form::file('multi_content['. $contentKey .'][video][]', ['id'=> 'contentVideo_'.$contentKey, 'class' => 'form-control multi-content', 'multiple'=> true]) !!} 
                                                    </span>
                                                    <span class="fileinput-filename width-250"></span>&nbsp;
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
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="active">
                                    <th class="text-center vcenter width-80">@lang('label.SL')</th>
                                    <th class="text-center vcenter width-120">@lang('label.CONTENT_TYPE')<span class="text-danger"> *</span></th>
                                    <th class="text-center vcenter">@lang('label.CONTENT')<span class="text-danger"> *</span></th>
                                    <th class="text-center vcenter width-50">@lang('label.REARRANGE')<span class="text-danger"></span></th>
                                    <th class="text-center vcenter  width-50">
                                        <button class="btn btn-inline btn-success add-new-content tooltips btn-xs" data-placement="right" title="@lang('label.ADD_CONTENT')" type="button">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </th>         
                                </tr>
                            </thead>
                            <tbody  id="contentRow">
                                @if(!empty($contentDetailsArr))
                                <?php $cSl = 0; ?>
                                @foreach($contentDetailsArr as $contentKey => $cInfo)
                                <tr>
                                    {!! Form::hidden('content['. $contentKey .'][id]', $cInfo['id'] ?? 0) !!}
                                    <td class="text-center vcenter content-sl" data-key="{{$contentKey}}">{!! ++$cSl !!}</td>
                                    {!! Form::hidden('content['. $contentKey .'][content_order]', $cInfo['content_order'] ?? null, ['id' => 'contentOrder_'.$contentKey, 'class' => 'content-order', 'data-key' => $contentKey]) !!}
    
                                    <td class="text-center vcenter width-100">
                                        <div class="width-full">
                                            {!! Form::select('content['. $contentKey .'][content_type]', $contentTypeArr, $cInfo['content_type_id'] ?? null, ['id' => 'contentType_'.$contentKey, 'class' => 'form-control js-source-states content-type width-full', 'data-key' => $contentKey]) !!}
                                        </div>
                                    </td>

                                    <td class="vcenter">
                                        <div class="form-group margin-bottom-0">
                                            <?php
                                            $display = [];
                                            if (!empty($contentTypeArr)) {
                                                foreach ($contentTypeArr as $typeId => $type) {
                                                    $display[$typeId] = !empty($cInfo['content_type_id']) && $cInfo['content_type_id'] == $typeId ? '' : 'display-none';
                                                }
                                            }
                                            ?> 
                                            <div class="col-md-12 upload-doc-{{$contentKey}} {{$display[1] ?? 'display-none'}}">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <span class="btn green-seagreen btn-file">
                                                        <span class="fileinput-new"> @lang('label.SELECT_DOC') </span>
                                                        <span class="fileinput-exists">@lang('label.CHANGE')</span>
                                                        {!! Form::file('content['. $contentKey .'][doc]',['id'=> 'contentDoc_'.$contentKey, 'data-key' => $contentKey]) !!}
                                                        @if(!empty($cInfo['content']) && $cInfo['content_type_id'] == 1)
                                                        {!! Form::hidden('content['.$contentKey .'][prev_doc]', $cInfo['content']) !!}
                                                        @endif
                                                    </span>
                                                    @if(!empty($cInfo['content']) && $cInfo['content_type_id'] == 1)
                                                    <a href="{{URL::to('public/uploads/content/file/'.$cInfo['content'])}}"
                                                       class="btn green-jungle btn-md tooltips" title="@lang('label.UPOLADED_DOC_PREVIEW')" target="_blank">
                                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                    </a>
                                                    @endif
                                                    <span class="fileinput-filename width-250">{!! !empty($cInfo['content_original']) && $cInfo['content_type_id'] == 1 ? $cInfo['content_original'] : '' !!}</span>&nbsp;
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
                                                        @if(!empty($cInfo['content']) && $cInfo['content_type_id'] == 2)
                                                        <img src="{{URL::to('/')}}/public/uploads/content/photo/{{$cInfo['content']}}" alt="{{ $cInfo['content_original']}}"/>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="btn green-seagreen btn-outline btn-file">
                                                            <span class="fileinput-new"> @lang('label.SELECT_IMAGE') </span>
                                                            <span class="fileinput-exists">@lang('label.CHANGE')</span>

                                                            {!! Form::file('content['. $contentKey .'][photo]', ['id'=> 'contentPhoto_'.$contentKey, 'class' => 'form-control', 'data-key' => $contentKey]) !!} 
                                                            @if(!empty($cInfo['content']) && $cInfo['content_type_id'] == 2)
                                                            {!! Form::hidden('content['.$contentKey .'][prev_photo]', $cInfo['content']) !!}
                                                            @endif
                                                        </span>

                                                        @if(!empty($cInfo['content']) && $cInfo['content_type_id'] == 2)
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
                                                        @if(!empty($cInfo['content']) && $cInfo['content_type_id'] == 3)
                                                        {!! Form::hidden('content['.$contentKey .'][prev_video]', $cInfo['content']) !!}
                                                        @endif
                                                    </span>
                                                    @if(!empty($cInfo['content']) && $cInfo['content_type_id'] == 3)
                                                    <a href="{{URL::to('public/uploads/content/video/'.$cInfo['content'])}}"
                                                       class="btn yellow-casablanca btn-md tooltips" title="@lang('label.UPOLADED_VIDEO_PREVIEW')" target="_blank">
                                                        <i class="fa fa-file-movie-o" aria-hidden="true"></i>
                                                    </a>
                                                    @endif
                                                    <span class="fileinput-filename width-250">{!! !empty($cInfo['content_original']) && $cInfo['content_type_id'] == 3 ? $cInfo['content_original'] : '' !!}</span>&nbsp;
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

                                                {!! Form::text('content['. $contentKey .'][url]',  !empty($cInfo['content']) && $cInfo['content_type_id'] == 4 ? $cInfo['content'] : null, ['id'=> 'contentUrl_'.$contentKey, 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => $urlExample, 'data-key' => $contentKey]) !!}  
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

                                    <td class="text-center vcenter  width-50">
                                        <button class="btn btn-inline btn-danger remove-content-row tooltips btn-xs" data-key="{{$contentKey}}" data-placement="right" title="@lang('label.REMOVE_CONTENT')" type="button">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn btn-circle green" type="button" id="updateContent">
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href="{{ URL::to('/content'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>	
    </div>
</div>
{!! Form::open(array('group' => 'form', 'url' => '', 'files'=> true, 'class' => 'form-horizontal', 'id' => 'multiContentForm')) !!}

{!! Form::close() !!}
<script>
    $(function () {
        //add new contact row
        var options = {
            tapToDismiss: true,
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null,
        };

        $(".tooltips").tooltip();

        var contentClassArr = [];
<?php
if (!empty($contentClassArr)) {
    foreach ($contentClassArr as $clsId => $clsInfo) {
        ?>
                var id = "<?php echo!empty($clsId) ? $clsId : ''; ?>";
                var name = "<?php echo!empty($clsInfo['name']) ? $clsInfo['name'] : ''; ?>";
                var icon = "<?php echo!empty($clsInfo['icon']) ? $clsInfo['icon'] : ''; ?>";
                var color = "<?php echo!empty($clsInfo['color']) ? $clsInfo['color'] : ''; ?>";

                contentClassArr[id] = [];
                contentClassArr[id]['name'] = name;
                contentClassArr[id]['icon'] = icon;
                contentClassArr[id]['color'] = color;
        <?php
    }
}
?>
        $(document).on("change", '#contentClassificationId', function () {
            var cls = $(this).val();
            var name = contentClassArr[cls]['name'] != '' ? contentClassArr[cls]['name'] : '';
            var icon = contentClassArr[cls]['icon'] != '' ? contentClassArr[cls]['icon'] : '';
            var color = contentClassArr[cls]['color'] != '' ? contentClassArr[cls]['color'] : '';
            $(".cnt-cls-postfix").html('<span class="bold tooltips" title="' + name + '"><i class="' + icon + ' font-' + color + '"></i></span>');

            return false;
        });

        $(document).on('change', ".content-type", function () {
            var contentType = $(this).val();
            var contentKey = $(this).attr("data-key");

            var uploadDoc = ".upload-doc-" + contentKey;
            var uploadPhoto = ".upload-photo-" + contentKey;
            var uploadVideo = ".upload-video-" + contentKey;
            var uploadUrl = ".upload-url-" + contentKey;

            if (contentType == '1') {
                contentTypeWiseUpload(uploadDoc, uploadPhoto, uploadVideo, uploadUrl);
            } else if (contentType == '2') {
                contentTypeWiseUpload(uploadPhoto, uploadDoc, uploadVideo, uploadUrl);
            } else if (contentType == '3') {
                contentTypeWiseUpload(uploadVideo, uploadDoc, uploadPhoto, uploadUrl);
            } else if (contentType == '4') {
                contentTypeWiseUpload(uploadUrl, uploadDoc, uploadPhoto, uploadVideo);
            }
        });


        //remove product row
        $(document).on('click', '.remove-content-row', function () {
            $(this).parent().parent().remove();
            rearrangeSL('content');
            return false;
        });

        $(document).on("click", ".add-new-content", function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({

                url: "{{ route('content.addContentRow') }}",
                type: "POST",
                dataType: 'json', // what to expect back from the PHP script, if anything
                data: {},

                success: function (res) {
                    $("#contentRow").append(res.html);
                    $(".tooltips").tooltip();
                    rearrangeSL('content');
                },
            });
        });

        $(document).on("click", "#updateContent", function (e) {
            e.preventDefault();
            var formData = new FormData($('#updateContentForm')[0]);
            //validate max post size
            var contentLength = getContentLength(formData);
            var limit = "<?php echo ((int) ini_get('post_max_size') * 1024 * 1024); ?>";
            if (contentLength != 0 && (contentLength > limit)) {
                toastr.error("@lang('label.THE_UPLOADED_FILE_SIZE_EXCEEDED_THE_SERVER_LIMIT')", 'Error', options);
                return false;
            }

            swal({
                title: 'Are you sure?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Confirm',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{ route('content.update')}}",
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function () {
                            $('#updateContent').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success(res.message, res.heading, options);
                            location.replace("{{ route('content.index')}}");

                        },
                        error: function (jqXhr, ajaxOptions, thrownError) {
                            if (jqXhr.status == 400) {
                                var errorsHtml = '';
                                var errors = jqXhr.responseJSON.message;
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value + '</li>';
                                });
                                toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                            } else if (jqXhr.status == 401) {
                                toastr.error(jqXhr.responseJSON.message, '', options);
                            } else if (jqXhr.status == 413) {
                                toastr.error("@lang('label.THE_UPLOADED_FILE_SIZE_EXCEEDED_THE_SERVER_LIMIT')", 'Error', options);
                            } else {
                                toastr.error('Error', "@lang('label.SOMETHING_WENT_WRONG')", options);
                            }
                            $('#updateContent').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }
            });
        });

        var authorityAllSelected = false;
        $('#outputAccess').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            nonSelectedText: "@lang('label.SELECT_OUTPUT_ACCESS')",
//        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                authorityAllSelected = true;
            },
            onChange: function () {
                authorityAllSelected = false;
            }
        });

        $(document).on("click", ".content-up,.content-down", function (e) {
            e.stopPropagation();
            var row = $(this).parent().parent();
            if ($(this).is('.content-up')) {
                row.insertBefore(row.prev());
            } else {
                row.insertAfter(row.next());
            }
            rearrangeSL('content');
            return false;
        });

        $(".multi-content").on("change", function (e) {

            var contentType = $('.multi-content-type').val();
            var files = e.target.files;
            var formData2 = new FormData($('#createContentForm')[0]);

            var contentLength = getContentLength(formData2);
            var limit = "<?php echo ((int) ini_get('post_max_size') * 1024 * 1024); ?>";
            if (contentLength != 0 && (contentLength > limit)) {
                toastr.error("@lang('label.THE_UPLOADED_FILE_SIZE_EXCEEDED_THE_SERVER_LIMIT')", 'Error', options);
                return false;
            }

            for (var i = 0; i < files.length; i++) {
                var formData = new FormData($('#multiContentForm')[0]);
                formData.append('content_type', contentType);
                formData.append('file', files[i]);
                formData.append('sl', i);
                $.ajax({

                    url: "{{ route('content.addContentRow') }}",
                    type: "POST",
                    dataType: 'json', // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    success: function (res) {
                        $("#contentRow").append(res.html);
                        $(".tooltips").tooltip();
                        rearrangeSL('content');
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 400) {
                            var errorsHtml = '';
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else if (jqXhr.status == 413) {
                            toastr.error("@lang('label.THE_UPLOADED_FILE_SIZE_EXCEEDED_THE_SERVER_LIMIT')", 'Error', options);
                        } else {
                            toastr.error('Error', "@lang('label.SOMETHING_WENT_WRONG')", options);
                        }
                    }
                });
            }
            $(this).val(null);
        });

    });

    function getContentLength(formData) {
        const formDataEntries = [...formData.entries()]

        const contentLength = formDataEntries.reduce((acc, [key, value]) => {
            if (typeof value === 'string')
                return acc + value.length;
            if (typeof value === 'object')
                return acc + value.size;

            return acc;
        }, 0);

        return contentLength;
    }


    function contentTypeWiseUpload(upload1, upload2, upload3, upload4) {
        if ($(upload1).hasClass('display-none')) {
            $(upload1).removeClass('display-none');
        }

        if (!$(upload2).hasClass('display-none')) {
            $(upload2).addClass('display-none');
        }
        if (!$(upload3).hasClass('display-none')) {
            $(upload3).addClass('display-none');
        }
        if (!$(upload4).hasClass('display-none')) {
            $(upload4).addClass('display-none');
        }
    }

    function rearrangeSL(type) {
        var sl = 0;
        $('.' + type + '-sl').each(function () {
            var key = $(this).attr('data-key');
            sl = sl + 1;
            $(this).text(sl);
            $('#contentOrder_' + key).val(sl);
        });
    }

</script>

@stop