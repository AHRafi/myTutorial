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
            @page { size: landscape; }
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
                    <img width="500" height="auto" src="{{$basePath}}/public/img/sint_ams_logo.jpg" alt=""/>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="text-center bold uppercase">
                        <span class="header">@lang('label.APPT_TO_CM')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong>{{$courseList->name}} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} |</strong>
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }}</strong>
                            @if(!empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0)
                            <strong>| </strong>{{__('label.SUB_EVENT')}} : <strong>{{ $subEventList[Request::get('sub_event_id')] }}</strong>
                            @endif
                            @if(!empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0)
                            <strong>| </strong>{{__('label.SUB_SUB_EVENT')}} : <strong>{{ $subSubEventList[Request::get('sub_sub_event_id')] }}</strong>
                            @endif
                            @if(!empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0)
                            <strong>| </strong>{{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ $subSubSubEventList[Request::get('sub_sub_sub_event_id')] }}</strong>
                            @endif
                            @if(!empty($targetArr))
                            <strong>| </strong>{{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }} </strong>
                            @endif
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter">@lang('label.RANK')</th>
                                <th class="vcenter">@lang('label.CM')</th>
                                <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                <th class="vcenter">@lang('label.SYN')</th>
                                <th class="vcenter">@lang('label.APPT')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sl = 0; ?>
                            @foreach($targetArr as $cmInfo)
                            <tr>
                                <td class="vcenter tex-center width-80">
                                    <div class="width-inherit text-center">{!! ++$sl !!}</div>
                                </td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo->personal_no ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-50">
                                    <div class="width-inherit">{!! $cmInfo->rank_code ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-200">
                                    <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo->full_name) !!}</div>
                                </td>
                                <td class="vcenter text-center" width="30px">
                                    @if(!empty($cmInfo->photo) && File::exists('public/uploads/cm/' . $cmInfo->photo))
                                    <img width="30" height="33" src="{{$basePath}}/public/uploads/cm/{{$cmInfo->photo}}" alt="{!! Common::getFurnishedCmName($cmInfo->full_name) !!}">
                                    @else
                                    <img width="30" height="33" src="{{$basePath}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo->full_name) !!}">
                                    @endif
                                </td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo->syn ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo->appt ?? '' !!}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
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
                    <strong>{!!__('label.APPT_TO_CM')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                        {{__('label.COURSE')}} : <strong>{{$courseList->name}} |</strong>
                        {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} |</strong>
                        {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }}</strong>
                        @if(!empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0)
                        <strong>| </strong>{{__('label.SUB_EVENT')}} : <strong>{{ $subEventList[Request::get('sub_event_id')] }}</strong>
                        @endif
                        @if(!empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0)
                        <strong>| </strong>{{__('label.SUB_SUB_EVENT')}} : <strong>{{ $subSubEventList[Request::get('sub_sub_event_id')] }}</strong>
                        @endif
                        @if(!empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0)
                        <strong>| </strong>{{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ $subSubSubEventList[Request::get('sub_sub_sub_event_id')] }}</strong>
                        @endif
                        @if(!empty($targetArr))
                        <strong>| </strong>{{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }} </strong>
                        @endif
                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter">@lang('label.RANK')</th>
                    <th class="vcenter">@lang('label.CM')</th>
                    <th class="vcenter text-center">@lang('label.PHOTO')</th>
                    <th class="vcenter">@lang('label.SYN')</th>
                    <th class="vcenter">@lang('label.APPT')</th>
                </tr>
            </thead>
            <tbody>
                <?php $sl = 0; ?>
                @foreach($targetArr as $cmInfo)
                <tr>
                    <td class="vcenter tex-center width-80">
                        <div class="width-inherit text-center">{{ ++$sl }}</div>
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{{ $cmInfo->personal_no ?? '' }}</div>
                    </td>
                    <td class="vcenter width-50">
                        <div class="width-inherit">{{ $cmInfo->rank_code ?? '' }}</div>
                    </td>
                    <td class="vcenter width-200">
                        <div class="width-inherit">{{ Common::getFurnishedCmName($cmInfo->full_name) }}</div>
                    </td>
                    <td class="vcenter text-center">
                        @if(!empty($cmInfo->photo) && File::exists('public/uploads/cm/' . $cmInfo->photo))
                        <img width="30" height="33" src="public/uploads/cm/{{$cmInfo->photo}}" alt="{!! Common::getFurnishedCmName($cmInfo->full_name) !!}">
                        @else
                        <img width="30" height="33" src="public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo->full_name) !!}">
                        @endif
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{{ $cmInfo->syn ?? '' }}</div>
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{{ $cmInfo->appt ?? '' }}</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border text-left" colspan="5">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border text-right" colspan="2">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>
    </body>
</html>
@endif
