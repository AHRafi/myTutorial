@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DOCUMENT_SEARCH_LIST')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'documentSearch/filter','class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.DATE_OF_UPLOAD_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('date_of_upload_from',Request::get('date_of_upload_from') ?? null, ['class' => 'form-control', 'id' => 'dateOfUploadFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="dateOfUploadFrom">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.DATE_OF_UPLOAD_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('date_of_upload_to',Request::get('date_of_upload_to') ?? null, ['class' => 'form-control', 'id' => 'dateOfUploadTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="dateOfUploadTo">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="title">@lang('label.TITLE') </label>
                        <div class="col-md-8">
                            {!! Form::text('title',  Request::get('title'), ['class' => 'form-control tooltips', 'id' => 'title', 'placeholder' => 'Title'  , 'list' => 'conTitle' ,'autocomplete' => 'off']) !!}
                            <datalist id="conTitle">
                                @if (!$titleArr->isEmpty())
                                @foreach($titleArr as $item)
                                <option value="{!! $item->title !!}" />
                                @endforeach
                                @endif
                            </datalist>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @if(Auth::user()->group_id != '4')
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') </label>
                        <div class="col-md-8">
                            {!! Form::select('course_id',  $courseList, Request::get('course_id'), ['class' => 'form-control js-source-states', 'autocomplete' => 'off']) !!} 
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="originatorId">@lang('label.ORIGINATOR') </label>
                        <div class="col-md-8">
                            {!! Form::select('originator_id',  $originatorList, Request::get('originator_id'), ['class' => 'form-control js-source-states']) !!} 
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="shortDes">@lang('label.SHORT_DES') </label>
                        <div class="col-md-8">
                            {!! Form::text('short_des',  Request::get('short_des'), ['class' => 'form-control tooltips', 'id' => 'shortDes', 'title' => 'Short Description', 'placeholder' => 'Short Description','autocomplete' => 'off']) !!} 
                            <span class="text-danger">{{ $errors->first('short_des') }}</span>
                        </div>
                    </div>
                </div>
                @if(Auth::user()->group_id == '4')
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="contentClassification">@lang('label.CONTENT_CLASSIFICATION') </label>
                        <div class="col-md-8">
                            {!! Form::select('con_classification', $contentClassificationList, Request::get('con_classification'), ['class' => 'form-control  js-source-states ','autocomplete' => 'off']) !!} 
                            <span class="text-danger">{{ $errors->first('con_classification') }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="row">
                @if(Auth::user()->group_id != '4')
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="contentClassification">@lang('label.CONTENT_CLASSIFICATION') </label>
                        <div class="col-md-8">
                            {!! Form::select('con_classification', $contentClassificationList, Request::get('con_classification'), ['class' => 'form-control  js-source-states ','autocomplete' => 'off']) !!} 
                            <span class="text-danger">{{ $errors->first('con_classification') }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="contentmodule">@lang('label.MODULE') </label>
                        <div class="col-md-8">
                            {!! Form::select('con_module', $contentModuleList, Request::get('con_module'), ['class' => 'form-control  js-source-states ','autocomplete' => 'off']) !!} 
                            <span class="text-danger">{{ $errors->first('con_module') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="contentCategory">@lang('label.CONTENT_CATEGORY') </label>
                        <div class="col-md-8">
                            {!! Form::select('con_category',  $contentCategoryList, Request::get('con_category'), ['class' => 'form-control js-source-states', 'autocomplete' => 'off']) !!} 
                            <span class="text-danger">{{ $errors->first('con_category') }}</span>
                        </div>
                    </div>
                </div>
                @if(Auth::user()->group_id == '4')
                <div class="col-md-4 text-center">
                    <div class="form-group">
                        <!--<label class="control-label col-md-4" for="">&nbsp;</label>-->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> @lang('label.GENERATE')
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @if(Auth::user()->group_id != '4')
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="form-group">
                        <!--<label class="control-label col-md-4" for="">&nbsp;</label>-->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> @lang('label.GENERATE')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            
			@if($request->generate == 'true')
            @if (!empty($targetArr))
            <div class="row">

                <div class="col-md-12 text-right">


                    <a class="btn btn-md print btn-primary vcenter" target="_blank"  href="{!! URL::full().'&view=print&print_option=1' !!}">
                        <span class="tooltips" title="@lang('label.PRINT')"><i class="fa fa-print"></i> </span> 
                    </a>



                    <!--                                        <a class="btn btn-success vcenter" href="{!! URL::full().'&view=pdf' !!}">
                                                                <span class="tooltips" title="@lang('label.DOWNLOAD_PDF')"><i class="fa fa-file-pdf-o"></i></span>
                                                            </a>-->
                    <a class="btn btn-warning vcenter" href="{!! URL::full().'&view=excel' !!}">
                        <span class="tooltips" title="@lang('label.DOWNLOAD_EXCEL')"><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                    <label class="control-label" for="sortBy">@lang('label.SORT_BY') :</label>&nbsp;
                    <label class="control-label" for="sortBy">
                        {!! Form::select('sort', $sortByList, Request::get('sort'),['class' => 'form-control','id'=>'sortBy']) !!}
                    </label>


                    <button class="btn green-jungle filter-btn"  id="sortByHref" type="submit">
                        <i class="fa fa-arrow-right"></i>  @lang('label.GO')
                    </button>
                </div>
            </div>
            @endif
            @endif
			
            {!! Form::close() !!}
			
			@if($request->generate == 'true')
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.ORIGINATOR')}} : <strong>{{ !empty($originatorList[Request::get('originator_id')]) && Request::get('originator_id') != 0 ? $originatorList[Request::get('originator_id')] : __('label.ALL') }} |</strong>
                            {{__('label.TITLE')}} : <strong>{{ !empty(Request::get('title')) && Request::get('title') != '' ? Request::get('title') : __('label.N_A') }} |</strong>
                            {{__('label.CONTENT_CLASSIFICATION')}} : <strong>{{ !empty($contentClassificationList[Request::get('con_classification')]) && Request::get('con_classification') != 0 ? $contentClassificationList[Request::get('con_classification')] : __('label.ALL') }} |</strong>
                            {{__('label.MODULE')}} : <strong>{{ !empty($contentModuleList[Request::get('con_module')]) && Request::get('con_module') != 0 ? $contentModuleList[Request::get('con_module')] : __('label.ALL') }} |</strong>
                            {{__('label.CONTENT_CATEGORY')}} : <strong>{{ !empty($contentCategoryList[Request::get('con_category')]) && Request::get('con_category') != 0 ? $contentCategoryList[Request::get('con_category')] : __('label.ALL') }} |</strong>
                            {{__('label.SHORT_DES')}} : <strong>{{ !empty(Request::get('short_des')) && Request::get('short_des') != '' ? Request::get('short_des') : __('label.N_A') }} |</strong>
                            {{__('label.DATE_OF_UPLOAD_FROM')}} : <strong>{{ !empty(Request::get('date_of_upload_from')) && Request::get('date_of_upload_from') != '' ? Request::get('date_of_upload_from') : __('label.N_A') }} |</strong>
                            {{__('label.DATE_OF_UPLOAD_TO')}} : <strong>{{ !empty(Request::get('date_of_upload_to')) && Request::get('date_of_upload_to') != '' ? Request::get('date_of_upload_to') : __('label.N_A') }} |</strong>
                            {{__('label.TOTAL_NO_OF_DOCUMENT')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

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
                                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.TITLE')</th>
                                    <th class="vcenter">@lang('label.MODULE')</th>
                                    <th class="vcenter">@lang('label.CONTENT_CATEGORY')</th>
                                    <th class="vcenter">@lang('label.ORIGINATOR')</th>
                                    <th class="text-center vcenter">@lang('label.DATE_OF_UPLOAD')</th>
                                    <th class="text-center vcenter">@lang('label.CONTENT')</th>
                                    <th class="vcenter">@lang('label.SHORT_DESCRIPTION')</th>
                                    <th class="text-center vcenter">@lang('label.STATUS')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($targetArr))
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($targetArr as $id => $target)
                                <tr>
                                    <td class="text-center vcenter">
                                        {{ ++$sl }}
                                    </td>
                                    <td class="vcenter width-200">
                                        <div class="width-inherit">
                                            {{ $target['title'] ?? '' }}&nbsp;
                                            @if(!empty($target['content_classification_id']))
                                            <span class="bold tooltips" title="{{$target['content_classification_name']}}"><i class="{{$target['content_classification_icon']}} font-{{$target['content_classification_color']}}"></i></span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class=" vcenter">{{ $target['module_name'] ?? '' }}</td>
                                    <td class=" vcenter">{{ $target['content_cat'] ?? '' }}</td> 
                                    <td class="vcenter">
                                        @if(!empty($target['origin']))
                                        @if($target['origin'] == '1' )
                                        {{ $target['user_official_name'] ?? ''  }}
                                        @elseif($target['origin'] == '2' )
                                        {{ $target['cm_official_name'] ?? ''  }}
                                        @elseif($target['origin'] == '3' )
                                        {{ $target['staff_official_name'] ?? ''  }}
                                        @endif
                                        @endif
                                    </td>
                                    <td class="text-center vcenter">{{ !empty($target['date_upload']) ? Helper::formatDate($target['date_upload']) : '' }}</td>

                                    <td class="td-actions vcenter text-center">
                                        <div class="width-inherit">
                                            @if(!empty($target['content_details']))

                                            {{ Form::open(array('url' => 'documentSearch/downloadFile', 'class' => 'download-file-form')) }}
                                            {{ Form::hidden('_method', 'POST') }}
                                            @foreach($target['content_details'] as $detailsId => $detail)
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
                                            @if($detail['content_type'] == 4)
                                            <a class="btn btn-xs {{$color}} download-content tooltips" title="{{$original}}"
                                               data-content="{{$detail['content']}}" data-original="{{$detail['content_original']}}" data-content-type="{{$detail['content_type']}}">
                                                <i class="fa fa-{{$icon}}"> </i>
                                            </a>
                                            @else
                                            <button class="btn btn-xs {{$color}} download-content tooltips" title="{{$original}}"
                                                    data-content="{{$detail['content']}}" data-original="{{$detail['content_original']}}" data-content-type="{{$detail['content_type']}}">
                                                <i class="fa fa-{{$icon}}"> </i>
                                            </button>
                                            @endif
                                            @endforeach
                                            {{ Form::close() }}
                                            @else
                                            <span class="label label-sm label-grey-mint tooltips" title="@lang('label.NO_CONTENT_UPLOADED')">
                                                <i class="fa fa-times-circle"></i>
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="vcenter">{{ $target['short_description'] ?? '' }}</td>


                                    <td class="text-center vcenter">
                                        @if($target['status'] == '1')
                                        <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                        @else
                                        <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                        @endif
                                    </td>


                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="9" class="vcenter">@lang('label.NO_CONTENT_FOUND')</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            @endif



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
    @stop