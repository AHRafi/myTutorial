@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CAT_WISE_DOC_REPORT')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => 'catWiseDocReport/filter','class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.CATEGORY') </label>
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::select('category', $contentCategoryList ,Request::get('category'), ['class' => 'form-control js-source-states', 'id' => 'month', 'readonly' => '']) !!} 
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center pull-left">
                    <div class="form-group">
                        <!--                        <label class="control-label col-md-4" for="">&nbsp;</label>-->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> @lang('label.GENERATE')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
			
            {!! Form::close() !!}
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
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">

                            {{__('label.CATEGORY')}} : <strong>{{ !empty(Request::get('category')) && Request::get('category') != 0 ? $contentCategoryList[Request::get('category')] : __('label.N_A') }} |</strong>
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

                                            {{ Form::open(array('url' => 'catWiseDocReport/downloadFile', 'class' => 'download-file-form')) }}
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