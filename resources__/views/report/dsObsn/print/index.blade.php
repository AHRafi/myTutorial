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
                        <span class="header">@lang('label.DS_OBSN')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} </strong>
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
                                <th class="vcenter text-center" colspan="5">@lang('label.TOTAL')</th>
                                <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
                                <th class="vcenter" rowspan="2">@lang('label.CM')</th>
                                <th class="vcenter" rowspan="2">@lang('label.PHOTO')</th>
                            </tr>
                            <tr>
                                @if(!empty($dsDataList))
                                @foreach($dsDataList as $dsId => $dsInfo)
                                <th class="vcenter text-center">
                                    @lang('label.MKS')&nbsp;({!! !empty($assignedDsObsnInfo->mks_limit) ? Helper::numberFormat2Digit($assignedDsObsnInfo->mks_limit) : '0.00' !!})
                                </th>
                                <th class="vcenter text-center">
                                    @lang('label.WT')&nbsp;({!! !empty($assignedDsObsnInfo->obsn) ? Helper::numberFormat2Digit($assignedDsObsnInfo->obsn) : '0.00' !!})
                                </th>
                                @endforeach
                                @endif
                                <th class="vcenter text-center">
                                    @lang('label.MKS')&nbsp;({!! !empty($assignedDsObsnInfo->mks_limit) ? Helper::numberFormat2Digit($assignedDsObsnInfo->mks_limit) : '0.00' !!})
                                </th>
                                <th class="vcenter text-center">
                                    @lang('label.WT')&nbsp;({!! !empty($assignedDsObsnInfo->obsn) ? Helper::numberFormat2Digit($assignedDsObsnInfo->obsn) : '0.00' !!})
                                </th>
                                <th class="vcenter text-center">@lang('label.PERCENT')</th>
                                <th class="vcenter text-center">@lang('label.GRADE')</th>
                                <th class="vcenter text-center">@lang('label.POSITION')</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($cmArr as $cmId => $cmInfo)
                            <?php
                            $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
                            $cmName = (!empty($cmInfo['rank_name']) ? $cmInfo['rank_name'] . ' ' : '') . (!empty($cmInfo['full_name']) ? $cmInfo['full_name'] : '');
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
                                    {!! Form::hidden('cm_name['.$cmId.']',Common::getFurnishedCmName($cmName),['id' => 'cmId'])!!}
                                </td>
                                <td class="vcenter" width="50px">
                                    @if(!empty($cmInfo['photo']) && File::exists('public/uploads/cm/' . $cmInfo['photo']))
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                    @else
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                    @endif
                                </td>
                                <!--DS Marking-->
                                @if(!empty($dsDataList))
                                @foreach($dsDataList as $dsId => $dsInfo)
                                <?php
                                $dsMarkingTextAlign = !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? 'right' : 'center';
                                $dsMarkingRemarks = !empty($dsMksWtArr[$dsId][$cmId]['remarks']) ? $dsMksWtArr[$dsId][$cmId]['remarks'] : '';
                                $dsMarkingRemarksColor = !empty($dsMksWtArr[$dsId][$cmId]['remarks']) ? 'text-danger' : '';
                                ?>
                                <td class="text-{{$dsMarkingTextAlign}} vcenter width-80 tooltips" title="{{$dsMarkingRemarks}}">
                                    <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? Helper::numberFormat2Digit($dsMksWtArr[$dsId][$cmId]['mks']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['wt']) ? Helper::numberFormat2Digit($dsMksWtArr[$dsId][$cmId]['wt']) : '--' !!}</span>
                                </td>
<!--                                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['percentage']) ? $dsMksWtArr[$dsId][$cmId]['percentage'] : '--' !!}</span>
                                </td>
                                <td class="text-center  vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['grade_name']) ? $dsMksWtArr[$dsId][$cmId]['grade_name'] : '--' !!}</span>
                                </td>-->

                                @endforeach
                                @endif
                                <?php
                                $dsObsnMksTextAlign = !empty($cmInfo['ds_obsn_mks']) ? 'right' : 'center';
                                $dsObsnWtTextAlign = !empty($cmInfo['ds_obsn_wt']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$dsObsnMksTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['ds_obsn_mks']) ? Helper::numberFormat2Digit($cmInfo['ds_obsn_mks']) : '--' !!}</span>
                                </td>
                                <td class="text-{{$dsObsnWtTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['ds_obsn_wt']) ? Helper::numberFormat2Digit($cmInfo['ds_obsn_wt']) : '--' !!}</span>
                                </td>
                                <?php
                                $totalPercentageTextAlign = !empty($cmInfo['total_term_percent']) ? 'right' : 'center';
                                ?>
                                <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['total_term_percent']) ? Helper::numberFormat2Digit($cmInfo['total_term_percent']) : '--' !!}</span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['grade_after_term_total']) ? $cmInfo['grade_after_term_total'] : '' !!} </span>
                                </td>
                                <td class="text-center vcenter width-80">
                                    <span class="width-inherit bold">{!! !empty($cmInfo['total_term_position']) ? $cmInfo['total_term_position'] : '' !!} </span>
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
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfo['photo']}}" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
                                    @else
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cmInfo['full_name']) !!}">
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
                    <strong>{!!__('label.TERM_RESULT')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                        {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }} |</strong>
                        {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.N_A') }} </strong>
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
                    @if(!empty($dsDataList))
                    @foreach($dsDataList as $dsId => $dsInfo)
                    <th class="text-center vcenter" colspan="2">
                        <span>
                            {!! $dsInfo['appt'] ?? '' !!}
                        </span>

                    </th>
                    @endforeach
                    @endif
                    <th class="vcenter text-center" colspan="5">@lang('label.TOTAL')</th>
                    <th class="vcenter" rowspan="2">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter" rowspan="2">@lang('label.RANK')</th>
                    <th class="vcenter" rowspan="2">@lang('label.CM')</th>
                </tr>
                <tr>
                    @if(!empty($dsDataList))
                    @foreach($dsDataList as $dsId => $dsInfo)
                    <th class="vcenter text-center">
                        @lang('label.MKS')&nbsp;({!! !empty($assignedDsObsnInfo->mks_limit) ? Helper::numberFormat2Digit($assignedDsObsnInfo->mks_limit) : '0.00' !!})
                    </th>
                    <th class="vcenter text-center">
                        @lang('label.WT')&nbsp;({!! !empty($assignedDsObsnInfo->obsn) ? Helper::numberFormat2Digit($assignedDsObsnInfo->obsn) : '0.00' !!})
                    </th>
                    @endforeach
                    @endif
                    <th class="vcenter text-center">
                        @lang('label.MKS')&nbsp;({!! !empty($assignedDsObsnInfo->mks_limit) ? Helper::numberFormat2Digit($assignedDsObsnInfo->mks_limit) : '0.00' !!})
                    </th>
                    <th class="vcenter text-center">
                        @lang('label.WT')&nbsp;({!! !empty($assignedDsObsnInfo->obsn) ? Helper::numberFormat2Digit($assignedDsObsnInfo->obsn) : '0.00' !!})
                    </th>
                    <th class="vcenter text-center">@lang('label.PERCENT')</th>
                    <th class="vcenter text-center">@lang('label.GRADE')</th>
                    <th class="vcenter text-center">@lang('label.POSITION')</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $sl = 0;
                ?>
                @foreach($cmArr as $cmId => $cmInfo)
                <?php
                $cmId = !empty($cmInfo['id']) ? $cmInfo['id'] : 0;
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
                    <!--DS Marking-->
                    @if(!empty($dsDataList))
                    @foreach($dsDataList as $dsId => $dsInfo)
                    <?php
                    $dsMarkingTextAlign = !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? 'right' : 'center';
                    $dsMarkingRemarks = !empty($dsMksWtArr[$dsId][$cmId]['remarks']) ? $dsMksWtArr[$dsId][$cmId]['remarks'] : '';
                    $dsMarkingRemarksColor = !empty($dsMksWtArr[$dsId][$cmId]['remarks']) ? 'text-danger' : '';
                    ?>
                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80 tooltips" title="{{$dsMarkingRemarks}}">
                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['mks']) ? Helper::numberFormat2Digit($dsMksWtArr[$dsId][$cmId]['mks']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['wt']) ? Helper::numberFormat2Digit($dsMksWtArr[$dsId][$cmId]['wt']) : '--' !!}</span>
                    </td>
<!--                                    <td class="text-{{$dsMarkingTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['percentage']) ? $dsMksWtArr[$dsId][$cmId]['percentage'] : '--' !!}</span>
                    </td>
                    <td class="text-center  vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($dsMksWtArr[$dsId][$cmId]['grade_name']) ? $dsMksWtArr[$dsId][$cmId]['grade_name'] : '--' !!}</span>
                    </td>-->

                    @endforeach
                    @endif
                    <?php
                    $dsObsnMksTextAlign = !empty($cmInfo['ds_obsn_mks']) ? 'right' : 'center';
                    $dsObsnWtTextAlign = !empty($cmInfo['ds_obsn_wt']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$dsObsnMksTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['ds_obsn_mks']) ? Helper::numberFormat2Digit($cmInfo['ds_obsn_mks']) : '--' !!}</span>
                    </td>
                    <td class="text-{{$dsObsnWtTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['ds_obsn_wt']) ? Helper::numberFormat2Digit($cmInfo['ds_obsn_wt']) : '--' !!}</span>
                    </td>
                    <?php
                    $totalPercentageTextAlign = !empty($cmInfo['total_term_percent']) ? 'right' : 'center';
                    ?>
                    <td class="text-{{$totalPercentageTextAlign}} vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['total_term_percent']) ? Helper::numberFormat2Digit($cmInfo['total_term_percent']) : '--' !!}</span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['grade_after_term_total']) ? $cmInfo['grade_after_term_total'] : '' !!} </span>
                    </td>
                    <td class="text-center vcenter width-80">
                        <span class="width-inherit bold">{!! !empty($cmInfo['total_term_position']) ? $cmInfo['total_term_position'] : '' !!} </span>
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
