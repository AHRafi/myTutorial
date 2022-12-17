<?php
$basePath = URL::to('/');
?>
@if (Request::get('view') == 'pdf' || Request::get('view') == 'print') 
<?php
if (Request::get('view') == 'pdf') {
    $basePath = base_path();
}
?>
<html>
    <head>
        <title>@lang('label.NDC_AMS_TITLE')</title>
        <link rel="shortcut icon" href="{{$basePath}}/public/img/favicon_sint.png" />
        <meta charset="UTF-8">
        <link href="{{asset('public/fonts/css.css?family=Open Sans')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('public/assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/css/components.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/morris/morris.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/jqvmap/jqvmap/jqvmap.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/css/plugins.min.css')}}" rel="stylesheet" type="text/css" />


        <!--BEGIN THEME LAYOUT STYLES--> 
        <!--<link href="{{asset('public/assets/layouts/layout/css/layout.min.css')}}" rel="stylesheet" type="text/css" />-->
        <link href="{{asset('public/assets/layouts/layout/css/custom.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/css/custom.css')}}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link href="{{asset('public/assets/layouts/layout/css/downloadPdfPrint/print.css')}}" rel="stylesheet" type="text/css" /> 
        <link href="{{asset('public/assets/layouts/layout/css/downloadPdfPrint/printInvoice.css')}}" rel="stylesheet" type="text/css" /> 

        <style type="text/css" media="print">
            /*            @page { size: landscape; }*/
            * {
                -webkit-print-color-adjust: exact !important; 
                color-adjust: exact !important; 
            }
        </style>

        <script src="{{asset('public/assets/global/plugins/jquery.min.js')}}" type="text/javascript"></script>
    </head>
    <body>


        <div class="portlet-body">
            <div class="row text-center">
                <div class="col-md-12 text-center">
                    <img width="500" height="auto" src="{{$basePath}}/public/img/sint_ams_logo.png" alt=""/>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="text-center bold uppercase">
                        <span class="header">@lang('label.COURSE_WISE_DOC_REPORT')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">

                            {{__('label.COURSE')}} : <strong>{{ !empty(Request::get('course_id')) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.TOTAL_NO_OF_DOCUMENT')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12 table-responsive">
                    <div class="max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.TITLE')</th>
                                    <th class="vcenter">@lang('label.MODULE')</th>
                                    <th class="vcenter">@lang('label.CONTENT_CATEGORY')</th>
                                    <th class="vcenter">@lang('label.ORIGINATOR')</th>
                                    <th class="text-center vcenter">@lang('label.DATE_OF_UPLOAD')</th>
                                    <th class="text-center vcenter">@lang('label.NO_OF_CONTENT')</th>
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

                                    <td class="vcenter text-center">
                                        {!! !empty($target['content_details']) ? sizeof($target['content_details']) : 0 !!}
                                    </td>
                                    <td class="text-center vcenter">{{ $target['short_description'] ?? '' }}</td>


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
        </div>
        <!--footer-->
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border text-left">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border text-right">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>

        <script src="{{asset('public/assets/global/plugins/bootstrap/js/bootstrap.min.js')}}"  type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->


        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{asset('public/assets/global/scripts/app.min.js')}}" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!--<script src="{{asset('public/assets/layouts/layout/scripts/layout.min.js')}}" type="text/javascript"></script>-->



        <!--<script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>-->


        <script>
document.addEventListener("DOMContentLoaded", function (event) {
    window.print();
});
        </script>

    </body>
</html>
@else
<html>
    <head>
        <link href="{{asset('public/fonts/css.css?family=Open Sans')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('public/assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/css/components.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/morris/morris.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/jqvmap/jqvmap/jqvmap.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/css/plugins.min.css')}}" rel="stylesheet" type="text/css" />


        <!--BEGIN THEME LAYOUT STYLES--> 
        <!--<link href="{{asset('public/assets/layouts/layout/css/layout.min.css')}}" rel="stylesheet" type="text/css" />-->
        <link href="{{asset('public/assets/layouts/layout/css/custom.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/css/custom.css')}}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link href="{{asset('public/assets/layouts/layout/css/downloadPdfPrint/print.css')}}" rel="stylesheet" type="text/css" /> 
        <link href="{{asset('public/assets/layouts/layout/css/downloadPdfPrint/printInvoice.css')}}" rel="stylesheet" type="text/css" /> 
    </head>
    <body>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <img width="500" height="auto" src="public/img/sint_ams_logo.jpg" alt=""/>
                </td>
            </tr>
            <tr>
                <td class="no-border text-center" colspan="8">
                    <strong>{!!__('label.COURSE_WISE_DOC_REPORT')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.COURSE')}} : <strong>{{ !empty(Request::get('course_id')) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                        {{__('label.TOTAL_NO_OF_DOCUMENT')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="vcenter">@lang('label.TITLE')</th>
                    <th class="vcenter">@lang('label.MODULE')</th>
                    <th class="vcenter">@lang('label.CONTENT_CATEGORY')</th>
                    <th class="vcenter text-center">@lang('label.ORIGINATOR')</th>
                    <th class="text-center vcenter">@lang('label.DATE_OF_UPLOAD')</th>
                    <th class="text-center vcenter">@lang('label.CONTENT')</th>
                    <th class="text-center vcenter">@lang('label.SHORT_DESCRIPTION')</th>
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

                    <td class="vcenter text-center">
                        {!! !empty($target['content_details']) ? sizeof($target['content_details']) : 0 !!}
                    </td>
                    <td class="text-center vcenter">{{ $target['short_description'] ?? '' }}</td>


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
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border" colspan="4">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border" colspan="3">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>
    </body>
</html>
@endif