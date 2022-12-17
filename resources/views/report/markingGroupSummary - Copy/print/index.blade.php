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
                        <span class="header">@lang('label.MARKING_GROUP_SUMMARY')</span>
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
                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($cmArr) ? sizeof($cmArr) : 0 }} |</strong>
                            {{__('label.TOTAL_NO_OF_DS')}} : <strong>{{ !empty($dsArr) ? sizeof($dsArr) : 0 }}</strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                        <thead>
                            <tr>
                                <th class="vcenter text-center">@lang('label.SL')</th>
                                <th class="vcenter">@lang('label.MARKING_GROUP')</th>
                                <th class="vcenter">@lang('label.ASSIGNED_CM')</th>
                                <th class="vcenter">@lang('label.ASSIGNED_DS')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($markingGroupArr))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($markingGroupArr as $markingGroupId => $markingGroup)

                            <tr>
                                <td class="text-center">{!! ++$sl !!}</td>
                                <td class="">{!! $markingGroup !!}</td>
                                <td class="vcenter">
                                    @if(!empty($cmArr[$markingGroupId]))
                                    <?php
                                    $cmSl = 0;
                                    ?>
                                    @foreach($cmArr[$markingGroupId] as $cmId => $cm)
                                    <?php
                                    $cmName = $cm['cm_name'] ?? null;
                                    $cmPhoto = $cm['photo'] ?? null;
                                    ?>

                                    <div class="margin-bottom-2">
                                        <span>{{ ++$cmSl }}. </span>
                                        <?php
                                        if (!empty($cmPhoto && File::exists('public/uploads/cm/' . $cmPhoto))) {
                                            ?>
                                            <img width="22" height="25" src="{{$basePath}}/public/uploads/cm/{{$cmPhoto}}" alt="{{ $cmName }}"/>&nbsp;&nbsp;
                                        <?php } else { ?>
                                            <img width="22" height="25" src="{{$basePath}}/public/img/unknown.png" alt="{{ $cmName }}"/>&nbsp;&nbsp;
                                            <?php
                                        }
                                        ?>  
                                        {!!$cm['cm_name'] ?? ''!!}
                                    </div>

                                    @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($dsArr[$markingGroupId]))
                                    <?php
                                    $dsSl = 0;
                                    ?>
                                    @foreach($dsArr[$markingGroupId] as $dsId => $ds)

                                    <?php
                                    $dsName = $ds['ds_name'] ?? null;
                                    $dsPhoto = $ds['photo'] ?? null;
                                    ?>
                                    <div class="margin-bottom-2">
                                        <span>{{ ++$dsSl }}. </span>
                                        <?php
                                        if (!empty($dsPhoto && File::exists('public/uploads/user/' . $dsPhoto))) {
                                            ?>
                                            <img width="22" height="25" src="{{$basePath}}/public/uploads/user/{{$dsPhoto}}" alt="{{ $dsName }}"/>&nbsp;&nbsp;
                                        <?php } else { ?>
                                            <img width="22" height="25" src="{{$basePath}}/public/img/unknown.png" alt="{{ $dsName }}"/>&nbsp;&nbsp;
                                            <?php
                                        }
                                        ?>
                                        {!!$ds['ds_name'] ?? ''!!}
                                    </div>

                                    @endforeach
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4"><strong>@lang('label.NO_MARKING_GROUP_IS_ASSIGNED_TO_THIS_EVENT')</strong></td>
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
                    <strong>{!!__('label.MARKING_GROUP_SUMMARY')!!}</strong>
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
                        </br>
                        {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($cmArr) ? sizeof($cmArr) : 0 }} |</strong>
                        {{__('label.TOTAL_NO_OF_DS')}} : <strong>{{ !empty($dsArr) ? sizeof($dsArr) : 0 }}</strong>

                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
            <thead>
                <tr>
                    <th class="vcenter text-center">@lang('label.SL')</th>
                    <th class="vcenter">@lang('label.MARKING_GROUP')</th>
                    <th class="vcenter">@lang('label.CM')</th>
                    <th class="vcenter">@lang('label.DS')</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($markingGroupArr))
                <?php
                $sl = 0;
                ?>
                @foreach($markingGroupArr as $markingGroupId => $markingGroup)

                <tr>
                    <td class="text-center">{!! ++$sl !!}</td>
                    <td class="">{!! $markingGroup !!}</td>
                    <td class="vcenter">
                        @if(!empty($cmArr[$markingGroupId]))
                        <?php
                        $cmSl = 0;
                        ?>
                        @foreach($cmArr[$markingGroupId] as $cmId => $cm)
                        <?php
                        $cmName = $cm['cm_name'] ?? null;
                        $cmPhoto = $cm['photo'] ?? null;
                        ?>

                        <div class="margin-bottom-2">
                            <span>{{ ++$cmSl }}. </span>
                            <?php
                            if (!empty($cmPhoto && File::exists('public/uploads/cm/' . $cmPhoto))) {
                                ?>
                                <img width="22" height="25" src="{{URL::to('/')}}/public/uploads/cm/{{$cmPhoto}}" alt="{{ $cmName }}"/>&nbsp;&nbsp;
                            <?php } else { ?>
                                <img width="22" height="25" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $cmName }}"/>&nbsp;&nbsp;
                                <?php
                            }
                            ?>  
                            {!!$cm['cm_name'] ?? ''!!}
                        </div>

                        @endforeach
                        @endif
                    </td>
                    <td>
                        @if(!empty($dsArr[$markingGroupId]))
                        <?php
                        $dsSl = 0;
                        ?>
                        @foreach($dsArr[$markingGroupId] as $dsId => $ds)

                        <?php
                        $dsName = $ds['ds_name'] ?? null;
                        $dsPhoto = $ds['photo'] ?? null;
                        ?>
                        <div class="margin-bottom-2">
                            <span>{{ ++$dsSl }}. </span>
                            <?php
                            if (!empty($dsPhoto && File::exists('public/uploads/user/' . $dsPhoto))) {
                                ?>
                                <img width="22" height="25" src="{{URL::to('/')}}/public/uploads/user/{{$dsPhoto}}" alt="{{ $dsName }}"/>&nbsp;&nbsp;
                            <?php } else { ?>
                                <img width="22" height="25" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $dsName }}"/>&nbsp;&nbsp;
                                <?php
                            }
                            ?>
                            {!!$ds['ds_name'] ?? ''!!}
                        </div>

                        @endforeach
                        @endif
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4"><strong>@lang('label.NO_MARKING_GROUP_IS_ASSIGNED_TO_THIS_EVENT')</strong></td>
                </tr>
                @endif
            </tbody>
        </table>
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
    </body>
</html>
@endif
