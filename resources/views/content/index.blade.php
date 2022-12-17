@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-clipboard"></i>@lang('label.CONTENT_LIST')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="{{ URL::to('content/create'.Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_CONTENT')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <div class="col-md-12">
                    <!-- Begin Filter-->
                    {!! Form::open(array('group' => 'form', 'url' => 'content/filter','class' => 'form-horizontal')) !!}
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                                <div class="col-md-8">
                                    {!! Form::text('fil_search',  Request::get('fil_search'), ['class' => 'form-control tooltips', 'id' => 'filSearch', 'title' => 'Title', 'placeholder' => 'Title', 'list' => 'contentTitle', 'autocomplete' => 'off']) !!} 
                                    <datalist id="contentTitle">
                                        @if (!$nameArr->isEmpty())
                                        @foreach($nameArr as $item)
                                        <option value="{{$item->title}}" />
                                        @endforeach
                                        @endif
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="form">
                                <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                    <i class="fa fa-search"></i> @lang('label.FILTER')
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                    </div>
                    {!! Form::close() !!}
                    <!-- End Filter -->
                </div>
            </div>

            <div class="table-responsive max-height-500 webkit-scrollbar">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="vcenter">@lang('label.TITLE')</th>
                            <th class="vcenter">@lang('label.MODULE')</th>
                            <th class="vcenter">@lang('label.CONTENT_CATEGORY')</th>
                            <th class="vcenter">@lang('label.ORIGINATOR')</th>
                            <th class="vcenter">@lang('label.COURSE')</th>
                            <th class="text-center vcenter">@lang('label.OUTPUT_ACCESS')</th>
                            <th class="text-center vcenter">@lang('label.DATE_OF_UPLOAD')</th>
                            <th class="text-center vcenter">@lang('label.CONTENT')</th>
                            <th class="text-center vcenter">@lang('label.SHORT_DESCRIPTION')</th>
                            <th class="text-center vcenter">@lang('label.STATUS')</th>
                            <th class="td-actions text-center vcenter">@lang('label.ACTION')</th>
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
                            <td class="vcenter">{{ $target['module_name'] ?? '' }}</td>
                            <td class="vcenter">{{ $target['content_cat'] ?? '' }}</td> 
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
                            <td class="vcenter">{{ $target['course_name'] ?? '' }}</td> 
                            <td class="text-center vcenter width-120">
                                <div class="width-inherit">
                                    <?php $comptArr = !empty($target['output_access']) ? explode(',', $target['output_access']) : []; ?>
                                    @if(!empty($comptArr))
                                    @foreach($comptArr as $comptId)
                                    <?php
                                    $compt = !empty($comptId) && !empty($compartmentList[$comptId]) ? $compartmentList[$comptId] : __('label.N_A');
                                    $comptColor = empty($comptId) ? 'grey-mint' : ($comptId == '1' ? 'purple-sharp' : ($comptId == '2' ? 'blue-steel' : ($comptId == '3' ? 'yellow' : 'grey-mint')));
                                    ?>
                                    <span class="label label-sm label-{{$comptColor}}">{!! $compt !!}</span>&nbsp; 
                                    @endforeach
                                    @endif
                                </div>
                            </td>
                            <td class="text-center vcenter">{{ !empty($target['date_upload']) ? Helper::formatDate($target['date_upload']) : '' }}</td>

                            <td class="td-actions vcenter text-center">
                                <div class="width-inherit">
                                    @if(!empty($target['content_details']))

                                    {{ Form::open(array('url' => 'content/downloadFile', 'class' => 'download-file-form')) }}
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
                            <td class="td-actions vcenter text-center">
                                <div class="width-inherit">
                                    {{ Form::open(array('url' => 'content/' . $target['id'].Helper::queryPageStr($qpArr))) }}
                                    {{ Form::hidden('_method', 'DELETE') }}

                                    <a class="btn btn-xs btn-primary tooltips " title="Edit" href="{{ URL::to('content/' . $target['id'] . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    {{ Form::close() }}
                                </div>
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