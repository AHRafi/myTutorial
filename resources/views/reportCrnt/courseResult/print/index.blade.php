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
                        <span class="header">@lang('label.COURSE_RESULT')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                            {{__('label.COURSE')}} : <strong>{{$courseList->name}}</strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
                                <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
                                <th class="vcenter" rowspan="2">@lang('label.CM')</th>
                                <th class="vcenter" rowspan="2">@lang('label.PHOTO')</th>
                                @if(!empty($termDataArr))
                                @foreach($termDataArr as $termId => $termName)
                                <th class="text-center vcenter" colspan="5">
                                    {!! !empty($termName) ? $termName : '' !!} (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['total_wt'][$termId]) ? Helper::numberFormat2Digit($eventMksWtArr['total_wt'][$termId]) : '0.00'}})
                                </th>
                                @endforeach
                                @endif
                                <th class="vcenter text-center" colspan="5">
                                    @lang('label.TERM_AGGREGATED_RESULT') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['agg_total_wt_limit']) ? Helper::numberFormat2Digit($eventMksWtArr['agg_total_wt_limit']) : '0.00'}})
                                </th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.CI_OBSN')&nbsp;({!! !empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : '0.00' !!})</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.COMDT_OBSN')&nbsp;({!! !empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : '0.00' !!})</th>
                                <th class="vcenter text-center" colspan="5">
                                    @lang('label.FINAL') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['final_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['final_wt']) : '0.00'}})
                                </th>
                                <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
                                <th class="vcenter" rowspan="2">@lang('label.CM')</th>
                                <th class="vcenter" rowspan="2">@lang('label.PHOTO')</th>
                            </tr>
                            <tr>
                                @if(!empty($termDataArr))
                                @foreach($termDataArr as $termId => $termName)
                                <?php
                                $termAggWtTotal = !empty($termAggWtTotal) ? $termAggWtTotal : 0;
                                $termAggWtTotal += $eventMksWtArr['total_wt'][$termId];
                                $finalWtLimit = $termAggWtTotal + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : '0.00') + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : '0.00');
                                ?>
                                <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
                                <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
                                <th class="vcenter text-center">@lang('label.PERCENT')</th>
                                <th class="vcenter text-center">@lang('label.GRADE')</th>
                                <th class="vcenter text-center">@lang('label.POSITION')</th>
                                @endforeach
                                @endif
                                <!--term aggregated total-->
                                <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
                                <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
                                <th class="vcenter text-center">@lang('label.PERCENT')</th>
                                <th class="vcenter text-center">@lang('label.GRADE')</th>
                                <th class="vcenter text-center">@lang('label.POSITION')</th>

                                <!--final-->
                                <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
                                <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
                                <th class="vcenter text-center">@lang('label.PERCENT')</th>
                                <th class="vcenter text-center">@lang('label.GRADE')</th>
                                <th class="vcenter text-center">@lang('label.POSITION')</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $sl = 0;
                            $readonly = !empty($comdtObsnLockInfo) ? 'readonly' : '';
                            $givenWt = !empty($comdtObsnLockInfo) ? 'given-wt' : '';
                            ?>
                            @foreach($cmArr as $cmId => $cmInfo)
                            <?php
                            $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');
                            $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');
                            ?>
                            <tr>
                                <td class="text-center vcenter">{!! ++$sl !!}</td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-150">
                                    <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                                </td>
                                <td class="vcenter" width="50px">
                                    @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                                    <img width="50" height="60" src="{{$basePath}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                    @else
                                    <img width="50" height="60" src="{{$basePath}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                    @endif
                                </td>

                                @if(!empty($termDataArr))
                                @foreach($termDataArr as $termId => $termName)
                                <?php
                                $totalAssignedWtTextAlign = !empty($cmInfo['term_total'][$termId]['total_assigned_wt']) ? 'right' : 'center';
                                $totalWtTextAlign = !empty($cmInfo['term_total'][$termId]['total_wt']) ? 'right' : 'center';
                                $totalPercentageTextAlign = !empty($cmInfo['term_total'][$termId]['percentage']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_assigned_wt']) ? Helper::numberFormat2Digit($cmInfo['term_total'][$termId]['total_assigned_wt']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_wt']) ? Helper::numberFormat3Digit($cmInfo['term_total'][$termId]['total_wt']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['percentage']) ? Helper::numberFormat2Digit($cmInfo['term_total'][$termId]['percentage']) : '--' !!}</span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_grade']) ? $cmInfo['term_total'][$termId]['total_grade'] : '' !!} </span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['position']) ? $cmInfo['term_total'][$termId]['position'] : '' !!} </span>
                                </td>
                                @endforeach
                                @endif

                                <?php
                                $totalAssignedWtTextAlign = !empty($cmInfo['agg_total_wt_limit']) ? 'right' : 'center';
                                $totalWtTextAlign = !empty($cmInfo['term_agg_total_wt']) ? 'right' : 'center';
                                $totalPercentageTextAlign = !empty($cmInfo['term_agg_percentage']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['agg_total_wt_limit']) ? Helper::numberFormat2Digit($cmInfo['agg_total_wt_limit']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_total_wt']) ? Helper::numberFormat2Digit($cmInfo['term_agg_total_wt']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_percentage']) ? Helper::numberFormat2Digit($cmInfo['term_agg_percentage']) : '--' !!}</span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_total_grade']) ? $cmInfo['term_agg_total_grade'] : '' !!} </span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['total_term_agg_position']) ? $cmInfo['total_term_agg_position'] : '' !!} </span>
                                </td>

                                <!--ci comdt obsn-->
                                <?php
                                $ciObsnTextAlign = !empty($cmInfo['ci_obsn']) ? 'right' : 'center';
                                $comdtObsnTextAlign = !empty($cmInfo['comdt_obsn']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$ciObsnTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['ci_obsn']) ? Helper::numberFormat2Digit($cmInfo['ci_obsn']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$comdtObsnTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['comdt_obsn']) ? Helper::numberFormat2Digit($cmInfo['comdt_obsn']) : '--' !!}</span>
                                </td>

                                <!--final-->
                                <?php
                                $finalAssignedWtTextAlign = !empty($cmInfo['final_assigned_wt']) ? 'right' : 'center';
                                $finalWtTextAlign = !empty($cmInfo['final_wt']) ? 'right' : 'center';
                                $finalPerTextAlign = !empty($cmInfo['final_percentage']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$finalAssignedWtTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['final_assigned_wt']) ? Helper::numberFormat3Digit($cmInfo['final_assigned_wt']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$finalWtTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['final_wt']) ? Helper::numberFormat2Digit($cmInfo['final_wt']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$finalPerTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['final_percentage']) ? Helper::numberFormat2Digit($cmInfo['final_percentage']) : '--' !!}</span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['final_grade']) ? $cmInfo['final_grade'] : '' !!} </span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['final_position']) ? $cmInfo['final_position'] : '' !!} </span>
                                </td>

                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-150">
                                    <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                                </td>
                                <td class="vcenter" width="50px">
                                    @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                                    <img width="50" height="60" src="{{$basePath}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                    @else
                                    <img width="50" height="60" src="{{$basePath}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                    @endif
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
                    <strong>{!!__('label.COURSE_RESULT')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.TRAINING_YEAR')}} : <strong>{{$activeTrainingYearList->name}} |</strong>
                        {{__('label.COURSE')}} : <strong>{{$courseList->name}}</strong>
                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color">
            <thead>
                <tr>
                    <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
                    <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
                    <th class="vcenter" rowspan="2">@lang('label.CM')</th>
                    @if(!empty($termDataArr))
                    @foreach($termDataArr as $termId => $termName)
                    <th class="text-center vcenter" colspan="5">
                        {!! !empty($termName) ? $termName : '' !!} (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['total_wt'][$termId]) ? Helper::numberFormat2Digit($eventMksWtArr['total_wt'][$termId]) : '0.00'}})
                    </th>
                    @endforeach
                    @endif
                    <th class="vcenter text-center" colspan="5">
                        @lang('label.TERM_AGGREGATED_RESULT') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['agg_total_wt_limit']) ? Helper::numberFormat2Digit($eventMksWtArr['agg_total_wt_limit']) : '0.00'}})
                    </th>
                    <th class="vcenter text-center" rowspan="2">@lang('label.CI_OBSN')&nbsp;({!! !empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : '0.00' !!})</th>
                    <th class="vcenter text-center" rowspan="2">@lang('label.COMDT_OBSN')&nbsp;({!! !empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : '0.00' !!})</th>
                    <th class="vcenter text-center" colspan="5">
                        @lang('label.FINAL') (@lang('label.TOTAL_WT'): {{!empty($eventMksWtArr['final_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['final_wt']) : '0.00'}})
                    </th>
                    <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
                    <th class="vcenter" rowspan="2">@lang('label.CM')</th>
                </tr>
                <tr>
                    @if(!empty($termDataArr))
                    @foreach($termDataArr as $termId => $termName)
                    <?php
                    $termAggWtTotal = !empty($termAggWtTotal) ? $termAggWtTotal : 0;
                    $termAggWtTotal += $eventMksWtArr['total_wt'][$termId];
                    $finalWtLimit = $termAggWtTotal + (!empty($assignedObsnInfo->ci_obsn_wt) ? $assignedObsnInfo->ci_obsn_wt : '0.00') + (!empty($assignedObsnInfo->comdt_obsn_wt) ? $assignedObsnInfo->comdt_obsn_wt : '0.00');
                    ?>
                    <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
                    <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
                    <th class="vcenter text-center">@lang('label.PERCENT')</th>
                    <th class="vcenter text-center">@lang('label.GRADE')</th>
                    <th class="vcenter text-center">@lang('label.POSITION')</th>
                    @endforeach
                    @endif
                    <!--term aggregated total-->
                    <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
                    <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
                    <th class="vcenter text-center">@lang('label.PERCENT')</th>
                    <th class="vcenter text-center">@lang('label.GRADE')</th>
                    <th class="vcenter text-center">@lang('label.POSITION')</th>

                    <!--final-->
                    <th class="vcenter text-center">@lang('label.ASSIGNED_WT')</th>
                    <th class="vcenter text-center">@lang('label.ACHIEVED_WT')</th>
                    <th class="vcenter text-center">@lang('label.PERCENT')</th>
                    <th class="vcenter text-center">@lang('label.GRADE')</th>
                    <th class="vcenter text-center">@lang('label.POSITION')</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $sl = 0;
                $readonly = !empty($comdtObsnLockInfo) ? 'readonly' : '';
                $givenWt = !empty($comdtObsnLockInfo) ? 'given-wt' : '';
                ?>
                @foreach($cmArr as $cmId => $cmInfo)
                <?php
                $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');
                $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                    </td>
                    <td class="vcenter width-150">
                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                    </td>

                    @if(!empty($termDataArr))
                    @foreach($termDataArr as $termId => $termName)
                    <?php
                    $totalAssignedWtTextAlign = !empty($cmInfo['term_total'][$termId]['total_assigned_wt']) ? 'right' : 'center';
                    $totalWtTextAlign = !empty($cmInfo['term_total'][$termId]['total_wt']) ? 'right' : 'center';
                    $totalPercentageTextAlign = !empty($cmInfo['term_total'][$termId]['percentage']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_assigned_wt']) ? Helper::numberFormat2Digit($cmInfo['term_total'][$termId]['total_assigned_wt']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_wt']) ? Helper::numberFormat3Digit($cmInfo['term_total'][$termId]['total_wt']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['percentage']) ? Helper::numberFormat2Digit($cmInfo['term_total'][$termId]['percentage']) : '--' !!}</span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['total_grade']) ? $cmInfo['term_total'][$termId]['total_grade'] : '' !!} </span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['term_total'][$termId]['position']) ? $cmInfo['term_total'][$termId]['position'] : '' !!} </span>
                    </td>
                    @endforeach
                    @endif

                    <?php
                    $totalAssignedWtTextAlign = !empty($cmInfo['agg_total_wt_limit']) ? 'right' : 'center';
                    $totalWtTextAlign = !empty($cmInfo['term_agg_total_wt']) ? 'right' : 'center';
                    $totalPercentageTextAlign = !empty($cmInfo['term_agg_percentage']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$totalAssignedWtTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['agg_total_wt_limit']) ? Helper::numberFormat2Digit($cmInfo['agg_total_wt_limit']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$totalWtTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_total_wt']) ? Helper::numberFormat2Digit($cmInfo['term_agg_total_wt']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_percentage']) ? Helper::numberFormat2Digit($cmInfo['term_agg_percentage']) : '--' !!}</span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['term_agg_total_grade']) ? $cmInfo['term_agg_total_grade'] : '' !!} </span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['total_term_agg_position']) ? $cmInfo['total_term_agg_position'] : '' !!} </span>
                    </td>

                    <!--ci comdt obsn-->
                    <?php
                    $ciObsnTextAlign = !empty($cmInfo['ci_obsn']) ? 'right' : 'center';
                    $comdtObsnTextAlign = !empty($cmInfo['comdt_obsn']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$ciObsnTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['ci_obsn']) ? Helper::numberFormat2Digit($cmInfo['ci_obsn']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$comdtObsnTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['comdt_obsn']) ? Helper::numberFormat2Digit($cmInfo['comdt_obsn']) : '--' !!}</span>
                    </td>

                    <!--final-->
                    <?php
                    $finalAssignedWtTextAlign = !empty($cmInfo['final_assigned_wt']) ? 'right' : 'center';
                    $finalWtTextAlign = !empty($cmInfo['final_wt']) ? 'right' : 'center';
                    $finalPerTextAlign = !empty($cmInfo['final_percentage']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$finalAssignedWtTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['final_assigned_wt']) ? Helper::numberFormat3Digit($cmInfo['final_assigned_wt']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$finalWtTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['final_wt']) ? Helper::numberFormat3Digit($cmInfo['final_wt']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$finalPerTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['final_percentage']) ? Helper::numberFormat2Digit($cmInfo['final_percentage']) : '--' !!}</span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['final_grade']) ? $cmInfo['final_grade'] : '' !!} </span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['final_position']) ? $cmInfo['final_position'] : '' !!} </span>
                    </td>


                    <td class="vcenter width-80">
                        <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{!! $cmInfo['rank_name'] ?? '' !!}</div>
                    </td>
                    <td class="vcenter width-150">
                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border text-left" colspan="4">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border text-right" colspan="4">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>
    </body>
</html>
@endif
