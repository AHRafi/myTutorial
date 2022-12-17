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
                        <span class="header">@lang('label.EVENT_MARKING_STATE')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong>{{$courseList->name}}</strong>
                            @if(!empty($cmArr))
                            <strong> | </strong>{{__('label.TOTAL_CM_WITH_NO_PARTICIPATION')}} : <strong>{{!empty($eventMksWtArr['total_none_pct_cm']) ? $eventMksWtArr['total_none_pct_cm'] : 0}}</strong>
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
                                <th class="text-center vcenter" rowspan="4">@lang('label.SL_NO')</th>
                                <th class="vcenter" rowspan="4">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter" rowspan="4">@lang('label.RANK')</th>
                                <th class="vcenter" rowspan="4">@lang('label.CM')</th>
                                @if (!empty($eventMksWtArr['mks_wt']))
                                @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId]) && sizeof($eventMksWtArr['event'][$eventId]) > 1 ? 1 : 4 !!}"
                                    colspan="{!! !empty($rowSpanArr['event'][$eventId]) && $rowSpanArr['event'][$eventId] > 1 ? $rowSpanArr['event'][$eventId] : 1 !!}">
                                    {!! !empty($eventMksWtArr['event'][$eventId]['name']) ? $eventMksWtArr['event'][$eventId]['name'] : '' !!}
                                </th>
                                @endforeach
                                @endif
                                <th class="vcenter text-center" rowspan="4">@lang('label.TOTAL_MARKED_EVENT')</th>
                                <th class="vcenter" rowspan="4">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter" rowspan="4">@lang('label.RANK')</th>
                                <th class="vcenter" rowspan="4">@lang('label.CM')</th>

                            </tr>
                            <tr>
                                @if (!empty($eventMksWtArr['mks_wt']))
                                @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                @foreach($evInfo as $subEventId => $subEvInfo)
                                @if(!empty($subEventId))
                                <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId][$subEventId]) && sizeof($eventMksWtArr['event'][$eventId][$subEventId]) > 1 ? 1 : 3 !!}"
                                    colspan="{!! !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] : 1 !!}">
                                    {!! !empty($eventMksWtArr['event'][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId]['name'] : '' !!}
                                </th>                                 
                                @endif
                                @endforeach
                                @endforeach
                                @endif

                            </tr>
                            <tr>
                                @if (!empty($eventMksWtArr['mks_wt']))
                                @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                @foreach($evInfo as $subEventId => $subEvInfo)
                                @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                @if(!empty($subSubEventId))
                                <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]) && sizeof($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]) > 1 ? 1 : 2 !!}"
                                    colspan="{!! !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] : 1 !!}">
                                    {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] : '' !!}
                                </th>
                                @endif
                                @endforeach
                                @endforeach
                                @endforeach
                                @endif
                            </tr>
                            <tr>
                                @if (!empty($eventMksWtArr['mks_wt']))
                                @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                @foreach($evInfo as $subEventId => $subEvInfo)
                                @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                                @if(!empty($subSubSubEventId))
                                <th class="vcenter text-center">
                                    {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' !!}
                                </th>
                                @endif
                                @endforeach
                                @endforeach
                                @endforeach
                                @endforeach
                                @endif
                            </tr>


                        </thead>

                        <tbody>
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($cmArr as $cmId => $cmInfo)
                            <?php
                            $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
                            $totalMked = !empty($cmInfo['total_mked']) ? $cmInfo['total_mked'] : 0;
                            $totalMkedSign = !empty($cmInfo['total_mked']) ? '' : 'warning';
                            $totalMkedColor = !empty($cmInfo['total_mked']) ? 'green-steel' : 'red-intense';
                            ?>
                            <tr>
                                <td class="text-center vcenter">{!! ++$sl !!}</td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}&nbsp;<span class=" bold text-red-intense"><i class="fa fa-{{$totalMkedSign}}"></i></span></div>
                                </td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-180">
                                    <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                                </td>
                                <?php
                                $totalMks = 0;
                                $totalWt = 0;
                                ?>
                                @if (!empty($eventMksWtArr['mks_wt']))
                                @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                                @foreach($evInfo as $subEventId => $subEvInfo)
                                @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)

                                <?php
                                $mksSign = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) ? 'check' : 'close';
                                $mksColor = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) ? 'green-steel' : 'red-intense';
                                ?>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold text-{{$mksColor}}"><i class="fa fa-{{$mksSign}}"></i></span>
                                </td>

                                @endforeach
                                @endforeach
                                @endforeach
                                @endforeach
                                @endif

                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold text-{{$totalMkedColor}}">{!! $totalMked !!}</span>
                                </td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}&nbsp;<span class=" bold text-red-intense"><i class="fa fa-{{$totalMkedSign}}"></i></span></div>
                                </td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-180">
                                    <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
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
                    <strong>{!!__('label.EVENT_MARKING_STATE')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                        {{__('label.COURSE')}} : <strong>{{$courseList->name}}</strong>
                        @if(!empty($cmArr))
                        <strong> | </strong>{{__('label.TOTAL_CM_WITH_NO_PARTICIPATION')}} : <strong>{{!empty($eventMksWtArr['total_none_pct_cm']) ? $eventMksWtArr['total_none_pct_cm'] : 0}}</strong>
                        @endif
                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color">
            <thead>
                <tr>
                    <th class="text-center vcenter" rowspan="4">@lang('label.SL_NO')</th>
                    <th class="vcenter" rowspan="4">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter" rowspan="4">@lang('label.RANK')</th>
                    <th class="vcenter" rowspan="4">@lang('label.CM')</th>
                    @if (!empty($eventMksWtArr['mks_wt']))
                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                    <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId]) && sizeof($eventMksWtArr['event'][$eventId]) > 1 ? 1 : 4 !!}"
                        colspan="{!! !empty($rowSpanArr['event'][$eventId]) && $rowSpanArr['event'][$eventId] > 1 ? $rowSpanArr['event'][$eventId] : 1 !!}">
                        {{ !empty($eventMksWtArr['event'][$eventId]['name']) ? $eventMksWtArr['event'][$eventId]['name'] : '' }}
                    </th>
                    @endforeach
                    @endif

                    <th class="vcenter text-center" rowspan="4">@lang('label.TOTAL_MARKED_EVENT')</th>
                    <th class="vcenter" rowspan="4">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter" rowspan="4">@lang('label.RANK')</th>
                    <th class="vcenter" rowspan="4">@lang('label.CM')</th>

                </tr>
                <tr>
                    @if (!empty($eventMksWtArr['mks_wt']))
                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                    @foreach($evInfo as $subEventId => $subEvInfo)
                    @if(!empty($subEventId))
                    <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId][$subEventId]) && sizeof($eventMksWtArr['event'][$eventId][$subEventId]) > 1 ? 1 : 3 !!}"
                        colspan="{!! !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] : 1 !!}">
                        {{!empty($eventMksWtArr['event'][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId]['name'] : ''}}
                    </th>                                 
                    @endif
                    @endforeach
                    @endforeach
                    @endif

                </tr>
                <tr>
                    @if (!empty($eventMksWtArr['mks_wt']))
                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                    @foreach($evInfo as $subEventId => $subEvInfo)
                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                    @if(!empty($subSubEventId))
                    <th class="vcenter text-center" rowspan="{!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]) && sizeof($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]) > 1 ? 1 : 2 !!}"
                        colspan="{!! !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] : 1 !!}">
                        {{!empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] : '' }}
                    </th>
                    @endif
                    @endforeach
                    @endforeach
                    @endforeach
                    @endif
                </tr>
                <tr>
                    @if (!empty($eventMksWtArr['mks_wt']))
                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                    @foreach($evInfo as $subEventId => $subEvInfo)
                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                    @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                    @if(!empty($subSubSubEventId))
                    <th class="vcenter text-center">
                        {{!empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' }}
                    </th>
                    @endif
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endif
                </tr>
            </thead>

            <tbody>
                <?php
                $sl = 0;
                ?>
                @foreach($cmArr as $cmId => $cmInfo)
                <?php
                $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
                $totalMked = !empty($cmInfo['total_mked']) ? $cmInfo['total_mked'] : 0;
                $totalMkedSign = !empty($cmInfo['total_mked']) ? '' : '*';
                $totalMkedColor = !empty($cmInfo['total_mked']) ? 'green-steel' : 'red-intense';
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}&nbsp;<span class=" bold text-red-intense">{{$totalMkedSign}}</span></div>
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                    </td>
                    <td class="vcenter width-180">
                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                    </td>
                    <?php
                    $totalMks = 0;
                    $totalWt = 0;
                    ?>
                    @if (!empty($eventMksWtArr['mks_wt']))
                    @foreach ($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                    @foreach($evInfo as $subEventId => $subEvInfo)
                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                    @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)

                    <?php
                    $mksSign = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) ? '' : 'X';
                    $mksColor = !empty($achieveMksWtArr[$cmId][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['mks']) ? 'green-steel' : 'red-intense';
                    ?>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{{$mksSign}}</span>
                    </td>

                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endif

                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold text-{{$totalMkedColor}}">{!! $totalMked !!}</span>
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}&nbsp;<span class=" bold text-red-intense"><i class="fa fa-{{$totalMkedSign}}"></i></span></div>
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                    </td>
                    <td class="vcenter width-180">
                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
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
