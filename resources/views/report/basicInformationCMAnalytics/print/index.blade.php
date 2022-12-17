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
                    <img width="500" height="auto" src="{{$basePath}}/public/img/sint_ams_logo.png" alt=""/>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="text-center bold uppercase">
                        <span class="header">@lang('label.NOMINAL_ROLL')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                            {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.ALL') }} |</strong>
                            {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} |</strong>
                            @if(Auth::user()->group_id != 6)
                            @if(Request::get('term_id') == 0)
                            {{__('label.SYN')}} : <strong>{{ __('label.ALL') }} |</strong>
                            @endif
                            @endif
                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                        <thead>
                            <tr>
                                <th class="vcenter text-center">@lang('label.SERIAL')</th>
                                <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter">@lang('label.RANK')</th>
                                <th class="vcenter">@lang('label.FULL_NAME')</th>
                                <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                                <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                <th class="vcenter">@lang('label.ARMS_SERVICE')</th>
                                <th class="vcenter">@lang('label.COMMISSIONING_COURSE')</th>
                                <th class="vcenter">@lang('label.EMAIL')</th>
                                <th class="vcenter">@lang('label.MOBILE')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($targetArr))
                            <?php
                            $sl = 0;
                            $cmGroupId = null;
                            ?>

                            @foreach($targetArr as $id => $target)

                            <?php
                            $gmGroupCondition = !empty(Request::get('term_id')) ? 1 : 0;
                            $synName = '';
                            if ($gmGroupCondition != 0) {
                                if ($target['cm_group_id'] != $cmGroupId) {
                                    $cmGroupId = $target['cm_group_id'];
                                    $gmGroupName = !empty($target['cm_group_name']) ? $target['cm_group_name'] : '';
                                    ?>
                                    <tr class="bg-grey-steel">
                                        <th colspan="11" class="text-center">{!! $gmGroupName !!}</th>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <td class="vcenter text-center">{!! ++$sl !!}</td>
                                <td class="vcenter">{!! !empty($target['personal_no']) ? $target['personal_no']:'' !!}</td>
                                <td class="vcenter">{!! $target['rank']?? '' !!}</td>
                                <td class="vcenter">{!! $target['full_name']??'' !!}</td>
                                <td class="vcenter">{!! $target['official_name']??'' !!}</td>
                                <td class="vcenter text-center" width="50px">
                                    @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                    <img width="50" height="60" src="{{$basePath}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                    @else
                                    <img width="50" height="60" src="{{$basePath}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                    @endif
                                </td>
                                <td class="vcenter">{!! $target['arms_service_name'] ?? '' !!}</td>
                                <td class="vcenter">{!! $target['comm_course_name'] ?? '' !!}</td>
                                <td class="vcenter">{!! $target['email'] ?? '' !!}</td>
                                <td class="vcenter">{!! $target['number'] ?? '' !!}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="11">@lang('label.NO_CM_FOUND')</td>
                            </tr>
                            @endif
                        </tbody>

                    </table>
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
                    <strong>{!!__('label.NOMINAL_ROLL')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.TRAINING_YEAR')}} : <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }} |</strong>
                        {{__('label.COURSE')}} : <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.ALL') }} |</strong>
                        {{__('label.TERM')}} : <strong>{{ !empty($termList[Request::get('term_id')]) && Request::get('term_id') != 0 ? $termList[Request::get('term_id')] : __('label.ALL') }} |</strong>
                        @if(Auth::user()->group_id != 4)
                        @if(Request::get('term_id') == 0)
                        {{__('label.SYN')}} : <strong>{{ __('label.ALL') }} |</strong>
                        @endif
                        @endif
                        {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
            <thead>
                <tr>
                    <th class="vcenter text-center">@lang('label.SERIAL')</th>
                    <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter">@lang('label.RANK')</th>
                    <th class="vcenter">@lang('label.FULL_NAME')</th>
                    <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                    <th class="vcenter text-center">@lang('label.PHOTO')</th>
                    <th class="vcenter">@lang('label.ARMS_SERVICE')</th>
                    <th class="vcenter">@lang('label.COMMISSIONING_COURSE')</th>
                    <th class="vcenter">@lang('label.EMAIL')</th>
                    <th class="vcenter">@lang('label.MOBILE')</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($targetArr))
                <?php
                $sl = 0;
                $cmGroupId = null;
                ?>

                @foreach($targetArr as $id => $target)

                <?php
                $gmGroupCondition = !empty(Request::get('term_id')) ? 1 : 0;
                $synName = '';
                if ($gmGroupCondition != 0) {
                    if ($target['cm_group_id'] != $cmGroupId) {
                        $cmGroupId = $target['cm_group_id'];
                        $gmGroupName = !empty($target['cm_group_name']) ? $target['cm_group_name'] : '';
                        ?>
                        <tr class="bg-grey-steel">
                            <th colspan="11" class="text-center">{!! $gmGroupName !!}</th>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <td class="vcenter text-center">{{ ++$sl }}</td>
                    <td class="vcenter">{{ !empty($target['personal_no']) ? $target['personal_no']:'' }}</td>
                    <td class="vcenter">{{ $target['rank']?? '' }}</td>
                    <td class="vcenter">{{ $target['full_name']??'' }}</td>
                    <td class="vcenter">{{ $target['official_name']??'' }}</td>
                    <td class="vcenter text-center">
                        @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                        <img width="50" height="60" src="public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                        @else
                        <img width="50" height="60" src="public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                        @endif
                    </td>
                    <td class="vcenter">{{ $target['arms_service_name'] ?? '' }}</td>
                    <td class="vcenter">{{ $target['comm_course_name'] ?? '' }}</td>
                    <td class="vcenter">{{ $target['email'] ?? '' }}</td>
                    <td class="vcenter">{{ $target['number'] ?? '' }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="11">@lang('label.NO_CM_FOUND')</td>
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