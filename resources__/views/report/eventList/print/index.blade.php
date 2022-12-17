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
        <title>@lang('label.SINT_AMS_TITLE')</title>
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
            /*@page { size: landscape; }*/
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
                        <span class="header">@lang('label.EVENT_MKS_WT')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearInfo[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearInfo[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} </strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                <th class="vcenter">@lang('label.EVENT')</th>
                                <th class="vcenter">@lang('label.SUB_EVENT')</th>
                                <th class="vcenter">@lang('label.SUB_SUB_EVENT')</th>
                                <th class="vcenter">@lang('label.SUB_SUB_SUB_EVENT')</th>
                                <th class="vcenter text-center">@lang('label.MKS_LIMIT')</th>
                                <th class="vcenter text-center">@lang('label.HIGHEST_MKS_LIMIT')</th>
                                <th class="vcenter text-center">@lang('label.LOWEST_MKS_LIMIT')</th>
                                <th class="vcenter text-center">@lang('label.WT')</th>
                                @if(empty(Request::get('term_id')))
                                <th class="vcenter text-center">@lang('label.EVENT_TOTAL')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($eventMksWtArr['mks_wt']))
                            <?php $sl = 0; ?>
                            @foreach($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                            <?php $eventId = !empty($evInfo['event_id']) ? $evInfo['event_id'] : 0; ?>
                            <tr>
                                <td class="vcenter text-center" rowspan="{!! !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 !!}">{!! ++$sl !!}</td>
                                <td class="vcenter"  rowspan="{!! !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 !!}">
                                    {!! !empty($eventMksWtArr['event'][$eventId]['name']) ? $eventMksWtArr['event'][$eventId]['name'] : '' !!}
                                </td>

                                @if(!empty($evInfo) && !is_numeric($evInfo))
                                <?php $i = 0; ?>
                                @foreach($evInfo as $subEventId => $subEvInfo)
                                @if(is_numeric($subEventId))
                                <?php
                                if ($i > 0) {
                                    echo '<tr>';
                                }
                                ?>

                                <td class="vcenter"  rowspan="{!! !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] : 1 !!}">
                                    {!! !empty($eventMksWtArr['event'][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId]['name'] : '' !!}
                                </td>

                                @if(!empty($subEvInfo) && !is_numeric($subEvInfo))
                                <?php $j = 0; ?>
                                @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                <?php
                                if ($j > 0) {
                                    echo '<tr>';
                                }
                                ?>
                                <td class="vcenter"  rowspan="{!! !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] : 1 !!}">
                                    {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] : '' !!}
                                </td>

                                @if(!empty($subSubEvInfo) && !is_numeric($subSubEvInfo))
                                <?php $k = 0; ?>
                                @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                                <?php
                                if ($k > 0) {
                                    echo '<tr>';
                                }
                                ?>
                                <td class="vcenter">
                                    {!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' !!}
                                </td>
                                <?php
                                $eventMkslimit = !empty($subSubSubEvInfo['mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['mks_limit']) : '--';
                                $eventHighestMkslimit = !empty($subSubSubEvInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['highest_mks_limit']) : '--';
                                $eventLowestMkslimit = !empty($subSubSubEvInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['lowest_mks_limit']) : '--';
                                $eventWt = !empty($subSubSubEvInfo['wt']) ? Helper::numberFormat2Digit($subSubSubEvInfo['wt']) : '--';
                                $eventWiseTotalWt = !empty($eventMksWtArr['event_total_wt'][$eventId]) ? Helper::numberFormat2Digit($eventMksWtArr['event_total_wt'][$eventId]) : '--';

                                $eventMkslimitTextAlign = !empty($subSubSubEvInfo['mks_limit']) ? 'right' : 'center';
                                $eventHighestMkslimitTextAlign = !empty($subSubSubEvInfo['highest_mks_limit']) ? 'right' : 'center';
                                $eventLowestMkslimitTextAlign = !empty($subSubSubEvInfo['lowest_mks_limit']) ? 'right' : 'center';
                                $eventWtTextAlign = !empty($subSubSubEvInfo['wt']) ? 'right' : 'center';
                                $eventWiseTotalWtTextAlign = !empty($eventMksWtArr['event_total_wt'][$eventId]) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$eventMkslimitTextAlign}} width-80">
                                    <span class="width-inherit">{!! $eventMkslimit !!}</span>
                                </td>
                                <td class="text-{{$eventHighestMkslimitTextAlign}} width-80">
                                    <span class="width-inherit">{!! $eventHighestMkslimit !!}</span>
                                </td>
                                <td class="text-{{$eventLowestMkslimitTextAlign}} width-80">
                                    <span class="width-inherit">{!! $eventLowestMkslimit !!}</span>
                                </td>
                                <td class="text-{{$eventWtTextAlign}} width-80">
                                    <span class="width-inherit">{!! $eventWt !!}</span>
                                </td>
                                @if(empty(Request::get('term_id')))
                                @if($i == 0 && $j == 0 && $k == 0)
                                <td class="vcenter text-{{$eventWiseTotalWtTextAlign}} width-80" rowspan="{!! !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 !!}">
                                    <span class="width-inherit">{!! $eventWiseTotalWt !!}</span>
                                </td>
                                @endif
                                @endif
                                <?php
                                if ($i < ($rowSpanArr['event'][$eventId] - 1)) {
                                    if ($j < ($rowSpanArr['sub_event'][$eventId][$subEventId] - 1)) {
                                        if ($k < ($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] - 1)) {
                                            echo '</tr>';
                                        }
                                    }
                                }
                                $k++;
                                ?>
                                @endforeach
                                @endif

                                <?php
                                $j++;
                                ?>
                                @endforeach
                                @endif

                                <?php
                                $i++;
                                ?>
                                @endif
                                @endforeach
                                @endif

                            </tr>
                            @endforeach
                            <tr class="info">

                                <th class="text-right" colspan="{{ (empty(Request::get('term_id')) ? 3 : 2)+6 }}">@lang('label.TOTAL')</th>
                                <th class="text-right width-80">
                                    <span class="width-inherit bold">{!! !empty($eventMksWtArr['total_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['total_wt']) : '0.00' !!}</span>
                                </th>
                            </tr>
                            @else
                            <tr>
                                <td colspan="9">@lang('label.NO_EVENT_IS_ASSIGNED_TO_THIS_TERM')</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if(empty($request->term_id))
            <div class="row margin-top-10">
                <div class="col-md-8">
                    @if(!$dsObsnMksWtInfo->isEmpty())
                    <div class="table-responsive max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.DS_OBSN')</th>
                                    <th class="vcenter text-center">@lang('label.MKS_LIMIT')</th>
                                    <th class="vcenter text-center">@lang('label.LIMIT_PERCENT')</th>
                                    <th class="vcenter text-center">@lang('label.WT')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sl = 0; ?>
                                @foreach($dsObsnMksWtInfo as $dsObsn)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">{!! $dsObsn->term ?? '' !!}</td>
                                    <td class="vcenter text-right">{!! !empty($dsObsn->mks_limit) ? Helper::numberFormat2Digit($dsObsn->mks_limit) : '--' !!}</td>
                                    <td class="vcenter text-right">{!! !empty($dsObsn->limit_percent) ? Helper::numberFormat2Digit($dsObsn->limit_percent) : '--' !!}</td>
                                    <td class="vcenter text-right">{!! !empty($dsObsn->obsn) ? Helper::numberFormat2Digit($dsObsn->obsn) : '--' !!}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th class="text-right bold" colspan="2"> @lang('label.TOTAL') </th>
                                    <th class="text-right width-80">
                                        <span class="width-inherit bold">{!! !empty($dsObsnMksWtArr['total_mks']) ? Helper::numberFormat2Digit($dsObsnMksWtArr['total_mks']) : '0.00' !!}</span>
                                    </th>
                                    <th></th>
                                    <th class="text-right width-80">
                                        <span class="width-inherit bold">{!! !empty($dsObsnMksWtArr['total_wt']) ? Helper::numberFormat2Digit($dsObsnMksWtArr['total_wt']) : '0.00' !!}</span>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                <div class="col-md-4">
                    @if(!empty($courseWtInfo))
                    <div class="table-responsive max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.CRITERIA')</th>
                                    <th class="vcenter text-center">@lang('label.WT')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="vcenter text-center">1</td>
                                    <td class="vcenter">@lang('label.TOTAL_EVENT_WT')</td>
                                    <td class="vcenter text-right">{!! !empty($courseWtInfo->total_event_wt) ? Helper::numberFormat2Digit($courseWtInfo->total_event_wt) : '--' !!}</td>
                                </tr>
                                <tr>
                                    <td class="vcenter text-center">2</td>
                                    <td class="vcenter">@lang('label.DS_OBSN_WT')</td>
                                    <td class="vcenter text-right">{!! !empty($courseWtInfo->ds_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->ds_obsn_wt) : '--' !!}</td>
                                </tr>
                                <tr>
                                    <td class="vcenter text-center">3</td>
                                    <td class="vcenter">@lang('label.CI_OBSN_WT')</td>
                                    <td class="vcenter text-right">{!! !empty($courseWtInfo->ci_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->ci_obsn_wt) : '--' !!}</td>
                                </tr>
                                <tr>
                                    <td class="vcenter text-center">4</td>
                                    <td class="vcenter">@lang('label.COMDT_OBSN_WT')</td>
                                    <td class="vcenter text-right">{!! !empty($courseWtInfo->comdt_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->comdt_obsn_wt) : '--' !!}</td>
                                </tr>
                                <tr>
                                    <th class="vcenter bold text-right" colspan="2">@lang('label.TOTAL')</th>
                                    <th class="vcenter text-right bold">{!! !empty($courseWtInfo->total_wt) ? Helper::numberFormat2Digit($courseWtInfo->total_wt) : '--' !!}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        <!--footer-->
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border text-left">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border text-right">
                    <strong>@lang('label.GENERATED_FROM_SINT')</strong>
                </td>
            </tr>
        </table>


        <!--//end of footer-->
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
                <td class="" colspan="9">
                    <img width="500" height="auto" src="public/img/sint_ams_logo.jpg" alt=""/>
                </td>
            </tr>
            <tr>
                <td class="no-border text-center" colspan="9">
                    <strong>{!!__('label.EVENT_MKS_WT')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="9">
                    <h5 style="padding: 10px;">
                        {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearInfo[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearInfo[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                        {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                        {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} </strong>
                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                    <th class="vcenter">@lang('label.EVENT')</th>
                    <th class="vcenter">@lang('label.SUB_EVENT')</th>
                    <th class="vcenter">@lang('label.SUB_SUB_EVENT')</th>
                    <th class="vcenter">@lang('label.SUB_SUB_SUB_EVENT')</th>
                    <th class="vcenter text-center">@lang('label.MKS_LIMIT')</th>
                    <th class="vcenter text-center">@lang('label.HIGHEST_MKS_LIMIT')</th>
                    <th class="vcenter text-center">@lang('label.LOWEST_MKS_LIMIT')</th>
                    <th class="vcenter text-center">@lang('label.WT')</th>
                    @if(empty(Request::get('term_id')))
                    <th class="vcenter text-center">@lang('label.EVENT_TOTAL')</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if(!empty($eventMksWtArr['mks_wt']))
                <?php $sl = 0; ?>
                @foreach($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                <?php $eventId = !empty($evInfo['event_id']) ? $evInfo['event_id'] : 0; ?>
                <tr>
                    <td class="vcenter text-center" rowspan="{{ !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 }}">{{ ++$sl }}</td>
                    <td class="vcenter" rowspan="{{ !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 }}">
                        {{ !empty($eventMksWtArr['event'][$eventId]['name']) ? $eventMksWtArr['event'][$eventId]['name'] : '' }}
                    </td>

                    @if(!empty($evInfo) && !is_numeric($evInfo))
                    <?php $i = 0; ?>
                    @foreach($evInfo as $subEventId => $subEvInfo)
                    @if(is_numeric($subEventId))
                    <?php
                    if ($i > 0) {
                        echo '<tr>';
                    }
                    ?>

                    <td class="vcenter"  rowspan="{{ !empty($rowSpanArr['sub_event'][$eventId][$subEventId]) ? $rowSpanArr['sub_event'][$eventId][$subEventId] : 1 }}">
                        {{ !empty($eventMksWtArr['event'][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId]['name'] : '' }}
                    </td>

                    @if(!empty($subEvInfo) && !is_numeric($subEvInfo))
                    <?php $j = 0; ?>
                    @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                    <?php
                    if ($j > 0) {
                        echo '<tr>';
                    }
                    ?>
                    <td class="vcenter"  rowspan="{{ !empty($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId]) ? $rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] : 1 }}">
                        {{ !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] : '' }}
                    </td>

                    @if(!empty($subSubEvInfo) && !is_numeric($subSubEvInfo))
                    <?php $k = 0; ?>
                    @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                    <?php
                    if ($k > 0) {
                        echo '<tr>';
                    }
                    ?>
                    <td class="vcenter">
                        {{ !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' }}
                    </td>
                    <?php
                    $eventMkslimit = !empty($subSubSubEvInfo['mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['mks_limit']) : '--';
                    $eventHighestMkslimit = !empty($subSubSubEvInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['highest_mks_limit']) : '--';
                    $eventLowestMkslimit = !empty($subSubSubEvInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($subSubSubEvInfo['lowest_mks_limit']) : '--';
                    $eventWt = !empty($subSubSubEvInfo['wt']) ? Helper::numberFormat2Digit($subSubSubEvInfo['wt']) : '--';
                    $eventWiseTotalWt = !empty($eventMksWtArr['event_total_wt'][$eventId]) ? Helper::numberFormat2Digit($eventMksWtArr['event_total_wt'][$eventId]) : '--';

                    $eventMkslimitTextAlign = !empty($subSubSubEvInfo['mks_limit']) ? 'right' : 'center';
                    $eventHighestMkslimitTextAlign = !empty($subSubSubEvInfo['highest_mks_limit']) ? 'right' : 'center';
                    $eventLowestMkslimitTextAlign = !empty($subSubSubEvInfo['lowest_mks_limit']) ? 'right' : 'center';
                    $eventWtTextAlign = !empty($subSubSubEvInfo['wt']) ? 'right' : 'center';
                    $eventWiseTotalWtTextAlign = !empty($eventMksWtArr['event_total_wt'][$eventId]) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$eventMkslimitTextAlign}}">
                        <span class="width-inherit">{{ $eventMkslimit }}</span>
                    </td>
                    <td class="text-{{$eventHighestMkslimitTextAlign}}">
                        <span class="width-inherit">{{ $eventHighestMkslimit }}</span>
                    </td>
                    <td class="text-{{$eventLowestMkslimitTextAlign}}">
                        <span class="width-inherit">{{ $eventLowestMkslimit }}</span>
                    </td>
                    <td class="text-{{$eventWtTextAlign}}">
                        <span class="width-inherit">{{ $eventWt }}</span>
                    </td>
                    @if(empty(Request::get('term_id')))
                    @if($i == 0 && $j == 0 && $k == 0)
                    <td class="vcenter text-{{$eventWiseTotalWtTextAlign}}" rowspan="{{ !empty($rowSpanArr['event'][$eventId]) ? $rowSpanArr['event'][$eventId] : 1 }}">
                        <span class="width-inherit">{{ $eventWiseTotalWt }}</span>
                    </td>
                    @endif
                    @endif
                    <?php
                    if ($i < ($rowSpanArr['event'][$eventId] - 1)) {
                        if ($j < ($rowSpanArr['sub_event'][$eventId][$subEventId] - 1)) {
                            if ($k < ($rowSpanArr['sub_sub_event'][$eventId][$subEventId][$subSubEventId] - 1)) {
                                echo '</tr>';
                            }
                        }
                    }
                    $k++;
                    ?>
                    @endforeach
                    @endif

                    <?php
                    $j++;
                    ?>
                    @endforeach
                    @endif

                    <?php
                    $i++;
                    ?>
                    @endif
                    @endforeach
                    @endif

                </tr>
                @endforeach
                <tr class="info">
                    <th class="text-right" colspan="{{ (empty(Request::get('term_id')) ? 3 : 2) +6 }}">@lang('label.TOTAL')</th>
                    <th class="text-right">
                        <span class="width-inherit bold">{{ !empty($eventMksWtArr['total_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['total_wt']) : '0.00' }}</span>
                    </th>
                </tr>
                @else
                <tr>
                    <td colspan="9">@lang('label.NO_EVENT_IS_ASSIGNED_TO_THIS_TERM')</td>
                </tr>
                @endif
            </tbody>
        </table>
        <table class="table table-bordered">
            <tr>
                <td width="67%">
                    <table class="table table-bordered table-hover table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                <th class="vcenter">@lang('label.DS_OBSN')</th>
                                <th class="vcenter text-center">@lang('label.MKS_LIMIT')</th>
                                <th class="vcenter text-center">@lang('label.LIMIT_PERCENT')</th>
                                <th class="vcenter text-center">@lang('label.WT')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sl = 0; ?>
                            @foreach($dsObsnMksWtInfo as $dsObsn)
                            <tr>
                                <td class="vcenter text-center">{{ ++$sl }}</td>
                                <td class="vcenter">{{ $dsObsn->term ?? '' }}</td>
                                <td class="vcenter text-right">{{ !empty($dsObsn->mks_limit) ? Helper::numberFormat2Digit($dsObsn->mks_limit) : '--' }}</td>
                                <td class="vcenter text-right">{{ !empty($dsObsn->limit_percent) ? Helper::numberFormat2Digit($dsObsn->limit_percent) : '--' }}</td>
                                <td class="vcenter text-right">{{ !empty($dsObsn->obsn) ? Helper::numberFormat2Digit($dsObsn->obsn) : '--' }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th class="text-right bold" colspan="2"> @lang('label.TOTAL') </th>
                                <th class="text-right">
                                    <span class="width-inherit bold">{{ !empty($dsObsnMksWtArr['total_mks']) ? Helper::numberFormat2Digit($dsObsnMksWtArr['total_mks']) : '0.00' }}</span>
                                </th>
                                <th></th>
                                <th class="text-right">
                                    <span class="width-inherit bold">{{ !empty($dsObsnMksWtArr['total_wt']) ? Helper::numberFormat2Digit($dsObsnMksWtArr['total_wt']) : '0.00' }}</span>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td width="33%">
                    <table class="table table-bordered table-hover table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                <th class="vcenter">@lang('label.CRITERIA')</th>
                                <th class="vcenter text-center">@lang('label.WT')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="vcenter text-center">1</td>
                                <td class="vcenter">@lang('label.TOTAL_EVENT_WT')</td>
                                <td class="vcenter text-right">{{ !empty($courseWtInfo->total_event_wt) ? Helper::numberFormat2Digit($courseWtInfo->total_event_wt) : '--' }}</td>
                            </tr>
                            <tr>
                                <td class="vcenter text-center">2</td>
                                <td class="vcenter">@lang('label.DS_OBSN_WT')</td>
                                <td class="vcenter text-right">{{ !empty($courseWtInfo->ds_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->ds_obsn_wt) : '--' }}</td>
                            </tr>
                            <tr>
                                <td class="vcenter text-center">3</td>
                                <td class="vcenter">@lang('label.CI_OBSN_WT')</td>
                                <td class="vcenter text-right">{{ !empty($courseWtInfo->ci_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->ci_obsn_wt) : '--' }}</td>
                            </tr>
                            <tr>
                                <td class="vcenter text-center">4</td>
                                <td class="vcenter">@lang('label.COMDT_OBSN_WT')</td>
                                <td class="vcenter text-right">{{ !empty($courseWtInfo->comdt_obsn_wt) ? Helper::numberFormat2Digit($courseWtInfo->comdt_obsn_wt) : '--' }}</td>
                            </tr>
                            <tr>
                                <th class="vcenter bold text-right" colspan="2">@lang('label.TOTAL')</th>
                                <th class="vcenter text-right bold">{{ !empty($courseWtInfo->total_wt) ? Helper::numberFormat2Digit($courseWtInfo->total_wt) : '--' }}</th>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <!--footer-->
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border text-left" colspan="5">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border text-right">
                    <strong>@lang('label.GENERATED_FROM_SINT')</strong>
                </td>
            </tr>
        </table>
    </body>
</html>
@endif