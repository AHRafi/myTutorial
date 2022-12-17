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
                        <span class="header">@lang('label.MUTUAL_ASSESSMENT_DETAILED_REPORT')</span>
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
                            @if($maProcess == '1')
                            {{__('label.SYNDICATE')}} : <strong>{{ !empty($synList[Request::get('syn_id')]) && Request::get('syn_id') != 0 ? $synList[Request::get('syn_id')] : __('label.N_A') }} |</strong>
                            @elseif($maProcess == '2')
                            {{__('label.SUB_SYNDICATE')}} : <strong>{{ !empty($subSynList[Request::get('sub_syn_id')]) && Request::get('sub_syn_id') != 0 ? $subSynList[Request::get('sub_syn_id')] : __('label.N_A') }} |</strong>
                            @elseif($maProcess == '3')
                            {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }} |</strong>
                            @if(!empty(Request::get('sub_event_id')))
                            {{__('label.SUB_EVENT')}} : <strong>{{ !empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0 ? $subEventList[Request::get('sub_event_id')] : __('label.N_A') }} |</strong>
                            @endif
                            @if(!empty(Request::get('sub_sub_event_id')))
                            {{__('label.SUB_SUB_EVENT')}} : <strong>{{ !empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0 ? $subSubEventList[Request::get('sub_sub_event_id')] : __('label.N_A') }} |</strong>
                            @endif
                            @if(!empty(Request::get('sub_sub_sub_event_id')))
                            {{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ !empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0 ? $subSubSubEventList[Request::get('sub_sub_sub_event_id')] : __('label.N_A') }} |</strong>
                            @endif
                            {{__('label.EVENT_GROUP')}} : <strong>{{ !empty($eventGroupList[Request::get('event_group_id')]) && Request::get('event_group_id') != 0 ? $eventGroupList[Request::get('event_group_id')] : __('label.N_A') }} |</strong>
                            @endif

                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($cmArr) ? sizeof($cmArr) : 0 }}</strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter text-center" rowspan="3">@lang('label.SL')</th>
                                <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                                <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                                <th class="vcenter text-center" colspan="{{!empty($cmArr) && !empty($factorList) ? sizeof($cmArr)*sizeof($factorList) : 1}}">@lang('label.CM_MARKING')</th>
                                <th class="vcenter text-center" rowspan="2" colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">@lang('label.AVG')</th>
                                <th class="vcenter text-center" rowspan="2"colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">@lang('label.POSITION')</th>
                            </tr>
                            <tr>
                                @if(!empty($markingCmArr))
                                @foreach($markingCmArr as $cmId => $cm)
                                <?php
                                $cmName = (!empty($cm['rank_name']) ? $cm['rank_name'] : '') . ' ' . (!empty($cm['official_name']) ? $cm['official_name'] : '') . ' (' . (!empty($cm['personal_no']) ? $cm['personal_no'] : '') . ')';
                                ?>
                                <th class="vcenter text-center"colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">{!! $cmName ?? '' !!}</th>
                                @endforeach
                                @endif
                            </tr>
                            <tr>
                                @if(!empty($markingCmArr))
                                @foreach($markingCmArr as $cmId => $cm)
                                @if(!empty($factorList))
                                @foreach($factorList as $factorId => $factor)
                                <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                                @endforeach
                                @endif
                                @endforeach
                                @endif
                                @if(!empty($factorList))
                                @foreach($factorList as $factorId => $factor)
                                <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                                @endforeach
                                @endif
                                @if(!empty($factorList))
                                @foreach($factorList as $factorId => $factor)
                                <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                                @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($cmArr) && !empty($markingCmArr))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($cmArr as $cmId => $cm)
                            <?php
                            $cmId = !empty($cm['id']) ? $cm['id'] : 0;
                            $cmName = (!empty($cm['rank_name']) ? $cm['rank_name'] : '') . ' ' . (!empty($cm['official_name']) ? $cm['official_name'] : '') . ' (' . (!empty($cm['personal_no']) ? $cm['personal_no'] : '') . ')';
                            ?>
                            <tr>
                                <td class="vcenter text-center">{!! ++$sl !!}</td>
                                <td class="vcenter text-center">
                                    <div class="width-inherit">{!! $cm['personal_no'] ?? '' !!}</div>
                                </td>
                                <td class="vcenter text-center">
                                    <div class="width-inherit">{!! $cm['rank_name'] ?? '' !!}</div>            
                                </td>
                                <td class="vcenter text-center width-180">
                                    <div class="width-inherit text-left">{!! $cm['full_name'] ?? '' !!}</div>
                                </td>
                                @foreach($markingCmArr as $markingCmId => $markingCm)
                                @if(!empty($factorList))
                                @foreach($factorList as $factorId => $factor)
                                <?php
                                $alignment = !empty($markingPositionArr[$markingCmId][$cmId][$factorId]['pos']) ? 'right' : 'center';
                                ?>
                                <td class="vcenter text-{{$alignment}}">{!! $markingPositionArr[$markingCmId][$cmId][$factorId]['pos'] ?? '--' !!}</td>
                                @endforeach
                                @endif
                                @endforeach
                                @if(!empty($factorList))
                                @foreach($factorList as $factorId => $factor)
                                <td class="vcenter text-{{!empty($cm['avg_'.$factorId]) ? 'right' : 'center'}}">
                                    {!! !empty($cm['avg_'.$factorId]) ? Helper::numberFormat2Digit($cm['avg_'.$factorId]) : '--' !!}
                                </td>
                                @endforeach
                                @endif
                                @if(!empty($factorList))
                                @foreach($factorList as $factorId => $factor)
                                <td class="vcenter text-center">{!! $cm['position_'.$factorId] ?? '' !!}</td>
                                @endforeach
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5"><strong>
                                        @lang('label.NO_CM_IS_ASSIGNED_TO_THIS_SYN')
                                    </strong>
                                </td>
                            </tr>
                            @endif
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
                    <strong>{!!__('label.MUTUAL_ASSESSMENT_DETAILED_REPORT')!!}</strong>
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
                        @if($maProcess == '1')
                        {{__('label.SYNDICATE')}} : <strong>{{ !empty($synList[Request::get('syn_id')]) && Request::get('syn_id') != 0 ? $synList[Request::get('syn_id')] : __('label.N_A') }} |</strong>
                        @elseif($maProcess == '2')
                        {{__('label.SUB_SYNDICATE')}} : <strong>{{ !empty($subSynList[Request::get('sub_syn_id')]) && Request::get('sub_syn_id') != 0 ? $subSynList[Request::get('sub_syn_id')] : __('label.N_A') }} |</strong>
                        @elseif($maProcess == '3')
                        {{__('label.EVENT')}} : <strong>{{ !empty($eventList[Request::get('event_id')]) && Request::get('event_id') != 0 ? $eventList[Request::get('event_id')] : __('label.N_A') }} |</strong>
                        @if(!empty(Request::get('sub_event_id')))
                        {{__('label.SUB_EVENT')}} : <strong>{{ !empty($subEventList[Request::get('sub_event_id')]) && Request::get('sub_event_id') != 0 ? $subEventList[Request::get('sub_event_id')] : __('label.N_A') }} |</strong>
                        @endif
                        @if(!empty(Request::get('sub_sub_event_id')))
                        {{__('label.SUB_SUB_EVENT')}} : <strong>{{ !empty($subSubEventList[Request::get('sub_sub_event_id')]) && Request::get('sub_sub_event_id') != 0 ? $subSubEventList[Request::get('sub_sub_event_id')] : __('label.N_A') }} |</strong>
                        @endif
                        @if(!empty(Request::get('sub_sub_sub_event_id')))
                        {{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ !empty($subSubSubEventList[Request::get('sub_sub_sub_event_id')]) && Request::get('sub_sub_sub_event_id') != 0 ? $subSubSubEventList[Request::get('sub_sub_sub_event_id')] : __('label.N_A') }} |</strong>
                        @endif
                        {{__('label.EVENT_GROUP')}} : <strong>{{ !empty($eventGroupList[Request::get('event_group_id')]) && Request::get('event_group_id') != 0 ? $eventGroupList[Request::get('event_group_id')] : __('label.N_A') }} |</strong>
                        @endif

                        {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($cmArr) ? sizeof($cmArr) : 0 }}</strong>

                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color">
            <thead>
                <tr>
                    <th class="vcenter text-center" rowspan="3">@lang('label.SL')</th>
                    <th class="vcenter" rowspan="3">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter" rowspan="3">@lang('label.RANK')</th>
                    <th class="vcenter" rowspan="3">@lang('label.CM')</th>
                    <th class="vcenter text-center" colspan="{{!empty($cmArr) && !empty($factorList) ? sizeof($cmArr)*sizeof($factorList) : 1}}">@lang('label.CM_MARKING')</th>
                    <th class="vcenter text-center" rowspan="2" colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">@lang('label.AVG')</th>
                    <th class="vcenter text-center" rowspan="2"colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">@lang('label.POSITION')</th>
                </tr>
                <tr>
                    @if(!empty($markingCmArr))
                    @foreach($markingCmArr as $cmId => $cm)
                    <?php
                    $cmName = (!empty($cm['rank_name']) ? $cm['rank_name'] : '') . ' ' . (!empty($cm['official_name']) ? $cm['official_name'] : '') . ' (' . (!empty($cm['personal_no']) ? $cm['personal_no'] : '') . ')';
                    ?>
                    <th class="vcenter text-center"colspan="{{!empty($factorList) ? sizeof($factorList) : 1}}">{!! $cmName ?? '' !!}</th>
                    @endforeach
                    @endif
                </tr>
                <tr>
                    @if(!empty($markingCmArr))
                    @foreach($markingCmArr as $cmId => $cm)
                    @if(!empty($factorList))
                    @foreach($factorList as $factorId => $factor)
                    <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                    @endforeach
                    @endif
                    @endforeach
                    @endif
                    @if(!empty($factorList))
                    @foreach($factorList as $factorId => $factor)
                    <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                    @endforeach
                    @endif
                    @if(!empty($factorList))
                    @foreach($factorList as $factorId => $factor)
                    <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                    @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
                @if(!empty($cmArr) && !empty($markingCmArr))
                <?php
                $sl = 0;
                ?>
                @foreach($cmArr as $cmId => $cm)
                <?php
                $cmId = !empty($cm['id']) ? $cm['id'] : 0;
                $cmName = (!empty($cm['rank_name']) ? $cm['rank_name'] : '') . ' ' . (!empty($cm['official_name']) ? $cm['official_name'] : '') . ' (' . (!empty($cm['personal_no']) ? $cm['personal_no'] : '') . ')';
                ?>
                <tr>
                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                    <td class="vcenter text-center">
                        <div class="width-inherit">{!! $cm['personal_no'] ?? '' !!}</div>
                    </td>
                    <td class="vcenter text-center">
                        <div class="width-inherit">{!! $cm['rank_name'] ?? '' !!}</div>            
                    </td>
                    <td class="vcenter text-center width-180">
                        <div class="width-inherit text-left">{!! $cm['full_name'] ?? '' !!}</div>
                    </td>
                    @foreach($markingCmArr as $markingCmId => $markingCm)
                    @if(!empty($factorList))
                    @foreach($factorList as $factorId => $factor)
                    <?php
                    $alignment = !empty($markingPositionArr[$markingCmId][$cmId][$factorId]['pos']) ? 'right' : 'center';
                    ?>
                    <td class="vcenter text-{{$alignment}}">{!! $markingPositionArr[$markingCmId][$cmId][$factorId]['pos'] ?? '--' !!}</td>
                    @endforeach
                    @endif
                    @endforeach
                    @if(!empty($factorList))
                    @foreach($factorList as $factorId => $factor)
                    <td class="vcenter text-{{!empty($cm['avg_'.$factorId]) ? 'right' : 'center'}}">
                        {!! !empty($cm['avg_'.$factorId]) ? Helper::numberFormat2Digit($cm['avg_'.$factorId]) : '--' !!}
                    </td>
                    @endforeach
                    @endif
                    @if(!empty($factorList))
                    @foreach($factorList as $factorId => $factor)
                    <td class="vcenter text-center">{!! $cm['position_'.$factorId] ?? '' !!}</td>
                    @endforeach
                    @endif
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="5"><strong>@lang('label.NO_CM_IS_ASSIGNED_TO_THIS_SYN_OR_SUB_SYN')</strong></td>
                </tr>
                @endif
            </tbody>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border text-left" colspan="5">
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
