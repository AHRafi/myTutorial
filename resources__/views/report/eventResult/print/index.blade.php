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
                        <span class="header">@lang('label.EVENT_RESULT')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} |</strong>
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }} |</strong>
                            @if(!empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0)
                            {{__('label.SUB_EVENT')}} : <strong>{{ $subEventList[Request::get('sub_event_id')] }} |</strong>
                            @endif
                            @if(!empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0)
                            {{__('label.SUB_SUB_EVENT')}} : <strong>{{ $subSubEventList[Request::get('sub_sub_event_id')] }} |</strong>
                            @endif
                            @if(!empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0)
                            {{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ $subSubSubEventList[Request::get('sub_sub_sub_event_id')] }} |</strong>
                            @endif
                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($cmArr) ? sizeof($cmArr) : 0 }} </strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="text-center vcenter" colspan="4">@lang('label.MKS_WT_INFO')</th>
                            </tr>
                            <tr>
                                <th class="text-center vcenter">@lang('label.MKS_LIMIT')</th>
                                <th class="text-center vcenter">@lang('label.HIGHEST_MKS_LIMIT')</th>
                                <th class="text-center vcenter">@lang('label.LOWEST_MKS_LIMIT')</th>
                                <th class="text-center vcenter">@lang('label.WT')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                $mksLimitTextAlign = !empty($assingedMksWtInfo['mks_limit']) ? 'right' : 'center';
                                $mksLimitHighTextAlign = !empty($assingedMksWtInfo['highest_mks_limit']) ? 'right' : 'center';
                                $mksLimitLowTextAlign = !empty($assingedMksWtInfo['lowest_mks_limit']) ? 'right' : 'center';
                                $mksLimitWtTextAlign = !empty($assingedMksWtInfo['wt']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$mksLimitTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['mks_limit']) : '0.00' !!}</td>
                                <td class="text-{{$mksLimitHighTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['highest_mks_limit']) : '0.00' !!}</td>
                                <td class="text-{{$mksLimitLowTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['lowest_mks_limit']) : '0.00' !!}</td>
                                <td class="text-{{$mksLimitWtTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['wt']) ? Helper::numberFormat2Digit($assingedMksWtInfo['wt']) : '0.00' !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 margin-top-10">
                    <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                        <thead>
                            <tr>
                                <th class="text-center vcenter" rowspan="3">@lang('label.SL_NO')</th>
                                <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                                <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                                <th class="vcenter" rowspan="3">@lang('label.PHOTO')</th>
                                <!--<th class="vcenter" rowspan="3">@lang('label.SYNDICATE')</th>-->
                                <th class="text-center vcenter" colspan="{{ (!empty($dsDataList) ? sizeof($dsDataList) : 1)*4 }}">@lang('label.DS_MARKING')</th>
                                <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AVERAGE')</th>
                                <th class="text-center vcenter" rowspan="3">@lang('label.CI_MODERATION')</th>
                                <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AFTER_CI_MODERATION')</th>
<!--                                <th class="text-center vcenter" rowspan="3">@lang('label.COMDT_MODERATION')</th>
                                <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AFTER_COMDT_MODERATION')</th>-->
                                <th class="text-center vcenter" rowspan="2" colspan="5">@lang('label.FINAL')</th>
                                <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                                <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                                <th class="vcenter" rowspan="3">@lang('label.PHOTO')</th>
                                <!--<th class="vcenter" rowspan="3">@lang('label.SYNDICATE')</th>-->
                            </tr>
                            <tr>
                                @if(!empty($dsDataList))
                                @foreach($dsDataList as $dsId => $dsInfo)
                                <?php
                                $src = URL::to('/') . '/public/img/unknown.png';
                                $alt = $dsInfo['ds_name'] ?? '';
                                $personalNo = !empty($dsInfo['personal_no']) ? '(' . $dsInfo['personal_no'] . ')' : '';
                                if (!empty($dsInfo['photo']) && File::exists('public/uploads/user/' . $dsInfo['photo'])) {
                                    $src = URL::to('/') . '/public/uploads/user/' . $dsInfo['photo'];
                                }
                                ?>
                                <th class="text-center vcenter" colspan="4">
                                    <span class="tooltips" data-html="true" data-placement="bottom" title="
                                          <div class='text-center'>
                                          <img width='50' height='60' src='{!! $src !!}' alt='{!! $alt !!}'/><br/>
                                          <strong>{!! $alt !!}<br/>
                                          {!! $personalNo !!} </strong>
                                          </div>
                                          ">
                                        {{ $dsInfo['appt'] ?? '' }}
                                    </span>

                                </th>
                                @endforeach
                                @endif
                            </tr>
                            <tr>
                                <!--DS Marking-->
                                @if(!empty($dsDataList))
                                @foreach($dsDataList as $dsId => $dsInfo)
                                <th class="vcenter text-center">
                                    <span class="tooltips" data-html="true">
                                        @lang('label.MKS')
                                    </span>
                                </th>
                                <th class="text-center vcenter">@lang('label.WT')</th>
                                <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                <th class="text-center vcenter">@lang('label.GRADE') </th>
                                @endforeach
                                @endif

                                <!--Average-->
                                <th class="vcenter text-center">@lang('label.MKS')</th>
                                <th class="text-center vcenter">@lang('label.WT')</th>
                                <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                <th class="text-center vcenter">@lang('label.GRADE') </th>

                                <!--After CI Moderation-->
                                <th class="vcenter text-center">@lang('label.MKS')</th>
                                <th class="text-center vcenter">@lang('label.WT')</th>
                                <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                <th class="text-center vcenter">@lang('label.GRADE') </th>

                                <!--After Comdt Moderation-->
                                <th class="vcenter text-center">@lang('label.MKS')</th>
                                <th class="text-center vcenter">@lang('label.WT')</th>
                                <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                <th class="text-center vcenter">@lang('label.GRADE') </th>

                                <!--Final Marking-->
                                <th class="vcenter text-center">@lang('label.MKS')</th>
                                <th class="text-center vcenter">@lang('label.WT')</th>
                                <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                <th class="text-center vcenter">@lang('label.GRADE') </th>
                                <th class="text-center vcenter">@lang('label.POSITION') </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sl = 0; ?>
                            @foreach($cmArr as $cmId => $cmInfo)
                            <?php
                            $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
                            $readonly = !empty($comdtModerationMarkingLockInfo) ? 'readonly' : '';
                            $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');

                            $avgDsMark = !empty($avgDsMksWtArr['mks'][$cmId]) ? $avgDsMksWtArr['mks'][$cmId] : 0;
                            $modLimit = !empty($comdtMksInfo->comdt_mod) ? $comdtMksInfo->comdt_mod : 0;
                            $modMark = (($avgDsMark * $modLimit) / 100);
                            $title = __('label.RECOMMENDED_MAX_MIN_VALUE', ['mod_mark' => $modMark]);
//                            $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');
                            ?>
                            <tr>
                                <td class="text-center vcenter">{!! ++$sl !!}</td>
                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-50">
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

                                <!--DS Marking-->
                                @if(!empty($dsDataList))
                                @foreach($dsDataList as $dsId => $dsInfo)
                                <?php
                                $dsMarkingTextAlign = !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? $dsMksWtArr[$dsId][$cmId]['mks'] : '--' !!}</span>
                                </td>
                                <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['wt']) ? $dsMksWtArr[$dsId][$cmId]['wt'] : '--' !!}</span>
                                </td>
                                <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['percentage']) ? $dsMksWtArr[$dsId][$cmId]['percentage'] : '--' !!}</span>
                                </td>
                                <td class="text-center  vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['grade_name']) ? $dsMksWtArr[$dsId][$cmId]['grade_name'] : '--' !!}</span>
                                </td>

                                @endforeach
                                @endif

                                <!--Average-->
                                <?php
                                $dsAvgMarkingTextAlign = !empty($avgDsMksWtArr['mks'][$cmId]) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$dsAvgMarkingTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['mks'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['mks'][$cmId]) : '--' !!}</span>
                                </td>
                                <td class="text-{{$dsAvgMarkingTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['wt'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['wt'][$cmId]) : '--' !!}</span>
                                </td>
                                <td class="text-{{$dsAvgMarkingTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['percentage'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['percentage'][$cmId]) : '--' !!}</span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['grade'][$cmId]) ? $avgDsMksWtArr['grade'][$cmId] : '--' !!}</span>
                                </td>

                                <!--CI Moderation-->
                                <?php
                                $ciModTextAlign = !empty($ciMksWtArr[$cmId]['ci_moderation']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$ciModTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['ci_moderation']) ? Helper::numberFormat2Digit($ciMksWtArr[$cmId]['ci_moderation']) : '--' !!}</span>
                                </td>

                                <!--After CI Moderation-->
                                <?php
                                $ciModerationTextAlign = !empty($ciMksWtArr[$cmId]['mks']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$ciModTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['mks']) ? $ciMksWtArr[$cmId]['mks'] : '--' !!}</span>
                                </td>
                                <td class="text-{{$ciModTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['wt']) ? $ciMksWtArr[$cmId]['wt'] : '--' !!}</span>
                                </td>
                                <td class="text-{{$ciModTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['percentage']) ? $ciMksWtArr[$cmId]['percentage'] : '--' !!}</span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['grade_name']) ? $ciMksWtArr[$cmId]['grade_name'] : '--' !!}</span>
                                </td>

                                <!--Comdt Moderation-->
                                <?php
                                $comdtModTextAlign = !empty($prevMksWtArr[$cmId]['comdt_moderation']) ? 'right' : 'center';
                                ?>
<!--                                <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['comdt_moderation']) ? $prevMksWtArr[$cmId]['comdt_moderation'] : '--' !!}</span>
                                </td>-->

                                <!--After Comdt Moderation-->
                                <?php
                                $comdtModerationTextAlign = !empty($prevMksWtArr[$cmId]['mks']) ? 'right' : 'center';
                                ?>
<!--                                <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['mks']) ? $prevMksWtArr[$cmId]['mks'] : '--' !!}</span>
                                </td>
                                <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['wt']) ? $prevMksWtArr[$cmId]['wt'] : '--' !!}</span>
                                </td>
                                <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['percentage']) ? $prevMksWtArr[$cmId]['percentage'] : '--' !!}</span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['grade_name']) ? $prevMksWtArr[$cmId]['grade_name'] : '--' !!}</span>
                                </td>-->

                                <!--final Marking-->
                                <?php
                                $finalMks = !empty($cmInfo['final_mks']) ? $cmInfo['final_mks'] : '--';
                                $finalWt = !empty($cmInfo['final_wt']) ? $cmInfo['final_wt'] : '--';
                                $finalPercentage = !empty($cmInfo['final_percentage']) ? $cmInfo['final_percentage'] : '--';
                                $finalGrade = !empty($cmInfo['final_grade_name']) ? $cmInfo['final_grade_name'] : '--';
                                $finalPosition = !empty($cmInfo['position']) ? $cmInfo['position'] : '--';
                                ?>
                                <td class="text-right vcenter width-80">
                                    <span class="width-inherit bold"> {{$finalMks}} </span>
                                </td>
                                <td class="text-right vcenter width-80">
                                    <span class="width-inherit bold"> {{$finalWt}} </span>
                                </td>
                                <td class="text-right vcenter width-80">
                                    <span class="width-inherit bold"> {{$finalPercentage}} </span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold"> {{$finalGrade}} </span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold"> {{$finalPosition}} </span>
                                </td>


                                <td class="vcenter width-80">
                                    <div class="width-inherit">{!! $cmInfo['personal_no'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter width-50">
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
                    <strong>{!!__('label.EVENT_RESULT')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                        {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                        {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} |</strong>
                        {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }} |</strong>
                        @if(!empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0)
                        {{__('label.SUB_EVENT')}} : <strong>{{ $subEventList[Request::get('sub_event_id')] }} |</strong>
                        @endif
                        @if(!empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0)
                        {{__('label.SUB_SUB_EVENT')}} : <strong>{{ $subSubEventList[Request::get('sub_sub_event_id')] }} |</strong>
                        @endif
                        @if(!empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0)
                        {{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ $subSubSubEventList[Request::get('sub_sub_sub_event_id')] }} |</strong>
                        @endif
                        {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($cmArr) ? sizeof($cmArr) : 0 }} </strong>

                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color">
            <thead>
                <tr>
                    <th class="text-center vcenter" colspan="4">@lang('label.MKS_WT_INFO')</th>
                </tr>
                <tr>
                    <th class="text-center vcenter">@lang('label.MKS_LIMIT')</th>
                    <th class="text-center vcenter">@lang('label.HIGHEST_MKS_LIMIT')</th>
                    <th class="text-center vcenter">@lang('label.LOWEST_MKS_LIMIT')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    $mksLimitTextAlign = !empty($assingedMksWtInfo['mks_limit']) ? 'right' : 'center';
                    $mksLimitHighTextAlign = !empty($assingedMksWtInfo['highest_mks_limit']) ? 'right' : 'center';
                    $mksLimitLowTextAlign = !empty($assingedMksWtInfo['lowest_mks_limit']) ? 'right' : 'center';
                    $mksLimitWtTextAlign = !empty($assingedMksWtInfo['wt']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$mksLimitTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['mks_limit']) : '0.00' !!}</td>
                    <td class="text-{{$mksLimitHighTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['highest_mks_limit']) : '0.00' !!}</td>
                    <td class="text-{{$mksLimitLowTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['lowest_mks_limit']) : '0.00' !!}</td>
                    <td class="text-{{$mksLimitWtTextAlign}} vcenter width-80">{!!  !empty($assingedMksWtInfo['wt']) ? Helper::numberFormat2Digit($assingedMksWtInfo['wt']) : '0.00' !!}</td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
            <thead>
                <?php
                $eventMkslimit = !empty($assingedMksWtInfo['mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['mks_limit']) : '0.00';
                $eventHighestMkslimit = !empty($assingedMksWtInfo['highest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['highest_mks_limit']) : '0.00';
                $eventLowestMkslimit = !empty($assingedMksWtInfo['lowest_mks_limit']) ? Helper::numberFormat2Digit($assingedMksWtInfo['lowest_mks_limit']) : '0.00';
                $eventWt = !empty($assingedMksWtInfo['wt']) ? Helper::numberFormat2Digit($assingedMksWtInfo['wt']) : '0.00';
                ?>
                <tr>
                    <th class="text-center vcenter" rowspan="3">@lang('label.SL_NO')</th>
                    <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                    <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                    <th class="vcenter" rowspan="3">@lang('label.PHOTO')</th>
                    <!--<th class="vcenter" rowspan="3">@lang('label.SYNDICATE')</th>-->


                    <th class="text-center vcenter" colspan="{{ (!empty($dsDataList) ? sizeof($dsDataList) : 1)*2 }}">@lang('label.DS_MARKING')</th>
                    @if(!in_array(Auth::user()->group_id, [4]))
                    <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AVERAGE')</th>
                    <th class="text-center vcenter" rowspan="3">@lang('label.CI_MODERATION')</th>
                    <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AFTER_CI_MODERATION')</th>
<!--                                    <th class="text-center vcenter" rowspan="3">@lang('label.COMDT_MODERATION')</th>
                    <th class="text-center vcenter" rowspan="2" colspan="4">@lang('label.AFTER_COMDT_MODERATION')</th>-->
                    <th class="text-center vcenter" rowspan="2" colspan="5">@lang('label.FINAL')</th>
                    <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                    <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                    <th class="vcenter" rowspan="3">@lang('label.PHOTO')</th>
                    <!--<th class="vcenter" rowspan="3">@lang('label.SYNDICATE')</th>-->
                    @endif
                </tr>
                <tr>
                    @if(!empty($dsDataList))
                    @foreach($dsDataList as $dsId => $dsInfo)
                    <?php
                    $src = URL::to('/') . '/public/img/unknown.png';
                    $alt = $dsInfo['ds_name'] ?? '';
                    $personalNo = !empty($dsInfo['personal_no']) ? '(' . $dsInfo['personal_no'] . ')' : '';
                    if (!empty($dsInfo['photo']) && File::exists('public/uploads/user/' . $dsInfo['photo'])) {
                        $src = URL::to('/') . '/public/uploads/user/' . $dsInfo['photo'];
                    }
                    ?>
                    <th class="text-center vcenter" colspan="2">
                        <span class="tooltips" data-html="true" data-placement="bottom" title="
                              <div class='text-center'>
                              <img width='50' height='60' src='{!! $src !!}' alt='{!! $alt !!}'/><br/>
                              <strong>{!! $alt !!}<br/>
                              {!! $personalNo !!} </strong>
                              </div>
                              ">
                            {{ $dsInfo['appt'] ?? '' }}
                        </span>

                    </th>
                    @endforeach
                    @endif
                </tr>
                <tr>
                    <!--DS Marking-->
                    @if(!empty($dsDataList))
                    @foreach($dsDataList as $dsId => $dsInfo)
                    <th class="vcenter text-center">
                        <span class="tooltips" data-html="true" title="
                              <div class='text-left'>
                              @lang('label.HIGHEST_MKS_LIMIT'): &nbsp;{!! $eventHighestMkslimit !!}<br/>
                              @lang('label.LOWEST_MKS_LIMIT'): &nbsp;{!! $eventLowestMkslimit !!}<br/>
                              </div>
                              ">
                            @lang('label.MKS')
                        </span>
                    </th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
<!--                                    <th class="text-center vcenter">@lang('label.PERCENT') </th>
                    <th class="text-center vcenter">@lang('label.GRADE') </th>-->
                    @endforeach
                    @endif

                    @if(!in_array(Auth::user()->group_id, [4]))
                    <!--Average-->
                    <th class="vcenter text-center">@lang('label.MKS')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
                    <th class="text-center vcenter">@lang('label.PERCENT') </th>
                    <th class="text-center vcenter">@lang('label.GRADE') </th>

                    <!--After CI Moderation-->
                    <th class="vcenter text-center">@lang('label.MKS')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
                    <th class="text-center vcenter">@lang('label.PERCENT') </th>
                    <th class="text-center vcenter">@lang('label.GRADE') </th>

                    <!--                                    After Comdt Moderation
                                                        <th class="vcenter text-center">@lang('label.MKS')</th>
                                                        <th class="text-center vcenter">@lang('label.WT')</th>
                                                        <th class="text-center vcenter">@lang('label.PERCENT') </th>
                                                        <th class="text-center vcenter">@lang('label.GRADE') </th>-->

                    <!--Final Marking-->
                    <th class="vcenter text-center">@lang('label.MKS')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
                    <th class="text-center vcenter">@lang('label.PERCENT') </th>
                    <th class="text-center vcenter">@lang('label.GRADE') </th>
                    <th class="text-center vcenter">@lang('label.POSITION') </th>
                    @endif
                </tr>
            </thead>

            <tbody>
                <?php $sl = 0; ?>
                @foreach($cmArr as $cmId => $cmInfo)
                <?php
                $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
                $readonly = !empty($comdtModerationMarkingLockInfo) ? 'readonly' : '';
                $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');

                $avgDsMark = !empty($avgDsMksWtArr['mks'][$cmId]) ? $avgDsMksWtArr['mks'][$cmId] : 0;
                $modLimit = !empty($comdtMksInfo->comdt_mod) ? $comdtMksInfo->comdt_mod : 0;
                $modMark = (($avgDsMark * $modLimit) / 100);
                $title = __('label.RECOMMENDED_MAX_MIN_VALUE', ['mod_mark' => $modMark]);
//                                $synName = (!empty($cmInfo['syn_name']) ? $cmInfo['syn_name'] . ' ' : '') . (!empty($cmInfo['sub_syn_name']) ? '(' . $cmInfo['sub_syn_name'] . ')' : '');
                ?>
                <tr>
                    <td class="vcenter tex-center width-80">
                        <div class="width-inherit text-center">{{ ++$sl }}</div>
                    </td>
                    <td class="vcenter width-80">
                        <div class="width-inherit">{{ $cmInfo['personal_no'] ?? '' }}</div>
                    </td>
                    <td class="vcenter width-50">
                        <div class="width-inherit">{{ $cmInfo['rank_name'] ?? '' }}</div>
                    </td>
                    <td class="vcenter width-200">
                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                    </td>
                    <td class="vcenter" width="50px">
                        @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                        <img width="50" height="60" src="public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                        @else
                        <img width="50" height="60" src="public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                        @endif
                    </td>

                    <!--DS Marking-->
                    @if(!empty($dsDataList))
                    @foreach($dsDataList as $dsId => $dsInfo)
                    <?php
                    $dsMarkingTextAlign = !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? $dsMksWtArr[$dsId][$cmId]['mks'] : '--' !!}</span>
                    </td>
                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['wt']) ? $dsMksWtArr[$dsId][$cmId]['wt'] : '--' !!}</span>
                    </td>
<!--                                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['percentage']) ? $dsMksWtArr[$dsId][$cmId]['percentage'] : '--' !!}</span>
                    </td>
                    <td class="text-center  vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['grade_name']) ? $dsMksWtArr[$dsId][$cmId]['grade_name'] : '--' !!}</span>
                    </td>-->

                    @endforeach
                    @endif

                    @if(!in_array(Auth::user()->group_id, [4]))
                    <!--Average-->
                    <?php
                    $dsAvgMarkingTextAlign = !empty($avgDsMksWtArr['mks'][$cmId]) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$dsAvgMarkingTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['mks'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['mks'][$cmId]) : '--' !!}</span>
                    </td>
                    <td class="text-{{$dsAvgMarkingTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['wt'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['wt'][$cmId]) : '--' !!}</span>
                    </td>
                    <td class="text-{{$dsAvgMarkingTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['percentage'][$cmId]) ? Helper::numberFormat2Digit($avgDsMksWtArr['percentage'][$cmId]) : '--' !!}</span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($avgDsMksWtArr['grade'][$cmId]) ? $avgDsMksWtArr['grade'][$cmId] : '--' !!}</span>
                    </td>

                    <!--CI Moderation-->
                    <?php
                    $ciModTextAlign = !empty($ciMksWtArr[$cmId]['ci_moderation']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$ciModTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['ci_moderation']) ? Helper::numberFormat2Digit($ciMksWtArr[$cmId]['ci_moderation']) : '--' !!}</span>
                    </td>

                    <!--After CI Moderation-->
                    <?php
                    $ciModerationTextAlign = !empty($ciMksWtArr[$cmId]['mks']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$ciModTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['mks']) ? $ciMksWtArr[$cmId]['mks'] : '--' !!}</span>
                    </td>
                    <td class="text-{{$ciModTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['wt']) ? $ciMksWtArr[$cmId]['wt'] : '--' !!}</span>
                    </td>
                    <td class="text-{{$ciModTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['percentage']) ? $ciMksWtArr[$cmId]['percentage'] : '--' !!}</span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($ciMksWtArr[$cmId]['grade_name']) ? $ciMksWtArr[$cmId]['grade_name'] : '--' !!}</span>
                    </td>

                    <!--Comdt Moderation-->
                    <?php
                    $comdtModTextAlign = !empty($prevMksWtArr[$cmId]['comdt_moderation']) ? 'right' : 'center';
                    ?>
<!--                                    <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['comdt_moderation']) ? $prevMksWtArr[$cmId]['comdt_moderation'] : '--' !!}</span>
                    </td>-->

                    <!--After Comdt Moderation-->
                    <?php
                    $comdtModerationTextAlign = !empty($prevMksWtArr[$cmId]['mks']) ? 'right' : 'center';
                    ?>
<!--                                    <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['mks']) ? $prevMksWtArr[$cmId]['mks'] : '--' !!}</span>
                    </td>
                    <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['wt']) ? $prevMksWtArr[$cmId]['wt'] : '--' !!}</span>
                    </td>
                    <td class="text-{{$comdtModTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['percentage']) ? $prevMksWtArr[$cmId]['percentage'] : '--' !!}</span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($prevMksWtArr[$cmId]['grade_name']) ? $prevMksWtArr[$cmId]['grade_name'] : '--' !!}</span>
                    </td>-->

                    <!--final Marking-->
                    <?php
                    $finalMksTextAlign = !empty($cmInfo['final_mks']) ? 'right' : 'center';
                    $finalMks = !empty($cmInfo['final_mks']) ? Helper::numberFormat2Digit($cmInfo['final_mks']) : '--';
                    $finalWt = !empty($cmInfo['final_wt']) ? Helper::numberFormat3Digit($cmInfo['final_wt']) : '--';
                    $finalPercentage = !empty($cmInfo['final_percentage']) ? Helper::numberFormat2Digit($cmInfo['final_percentage']) : '--';
                    $finalGrade = !empty($cmInfo['final_grade_name']) ? $cmInfo['final_grade_name'] : '--';
                    $finalPosition = !empty($cmInfo['position']) ? $cmInfo['position'] : '--';
                    ?>
                    <td class="text-{{$finalMksTextAlign}} vcenter width-80">
                        <span class="width-inherit bold"> {{$finalMks}} </span>
                    </td>
                    <td class="text-{{$finalMksTextAlign}} vcenter width-80">
                        <span class="width-inherit bold"> {{$finalWt}} </span>
                    </td>
                    <td class="text-{{$finalMksTextAlign}} vcenter width-80">
                        <span class="width-inherit bold"> {{$finalPercentage}} </span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold"> {{$finalGrade}} </span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold"> {{$finalPosition}} </span>
                    </td>

                    <td class="vcenter width-80">
                        <div class="width-inherit">{{ $cmInfo['personal_no'] ?? '' }}</div>
                    </td>
                    <td class="vcenter width-50">
                        <div class="width-inherit">{{ $cmInfo['rank_name'] ?? '' }}</div>
                    </td>
                    <td class="vcenter width-200">
                        <div class="width-inherit">{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}</div>
                    </td>
                    <td class="vcenter" width="50px">
                        @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                        <img width="50" height="60" src="public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                        @else
                        <img width="50" height="60" src="public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                        @endif
                    </td>

                    @endif
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
