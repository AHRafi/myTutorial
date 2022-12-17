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
        <link rel="shortcut icon" href="{{ $basePath }}/public/img/favicon_sint.png" />
        <meta charset="UTF-8">
        <link href="{{ asset('public/fonts/css.css?family=Open Sans') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('public/assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components"
            type="text/css" />
        <link href="{{ asset('public/assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('public/assets/global/plugins/jqvmap/jqvmap/jqvmap.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />


        <!--BEGIN THEME LAYOUT STYLES-->
        <!--<link href="{{ asset('public/assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css" />-->
        <link href="{{ asset('public/assets/layouts/layout/css/custom.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('public/css/custom.css') }}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link href="{{ asset('public/assets/layouts/layout/css/downloadPdfPrint/print.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/layouts/layout/css/downloadPdfPrint/printInvoice.css') }}" rel="stylesheet"
            type="text/css" />

        <style type="text/css" media="print">
            @page {
                size: landscape;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        </style>

        <script src="{{ asset('public/assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
    </head>

    <body>
        <div class="portlet-body">
            <div class="row text-center">
                <div class="col-md-12 text-center">
                    <img width="500" height="auto" src="{{ $basePath }}/public/img/sint_ams_logo.jpg"
                        alt="" />
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="text-center bold uppercase">
                        <span class="header">@lang('label.LESSON_WISE_GS_FEEDBACK_FROM_CM')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{ __('label.TRAINING_YEAR') }} :
                                    <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }}
                                        |</strong>
                                    {{ __('label.COURSE') }} :
                                    <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }}
                                        |</strong>
                                    {{ __('label.GS') }} :
                            <strong>{{ !empty($gsList[Request::get('gs_id')]) && Request::get('gs_id') != 0 ? $gsList[Request::get('gs_id')] : __('label.N_A') }}
                                |</strong>
                            {{ __('label.LESSON') }} :
                            <strong>{{ !empty($lessonList[Request::get('lesson_id')]) && Request::get('lesson_id') != 0 ? $lessonList[Request::get('lesson_id')] : __('label.N_A') }}
                            </strong>
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
                                <th class="vcenter">@lang('label.FULL_NAME')</th>
                                <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                                <th class="vcenter">@lang('label.WING')</th>
                                <th class="vcenter">@lang('label.PHOTO')</th>

                                <th class="vcenter text-center">@lang('label.GRADING')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($cmArr))
                                <?php
                                $sl = 0;
                                ?>

                                @foreach ($cmArr as $id => $target)
                                    <tr>
                                        <td class="vcenter text-center">{!! ++$sl !!}</td>
                                        <td class="vcenter">{!! !empty($target['personal_no']) ? $target['personal_no'] : '' !!}</td>
                                        <td class="vcenter">{!! $target['rank'] ?? '' !!}</td>
                                        <td class="vcenter">{!! $target['full_name'] ?? '' !!}</td>
                                        <td class="vcenter">{!! $target['official_name'] ?? '' !!}</td>
                                        <td class="vcenter">{!! $target['wing'] ?? '' !!}</td>
                                        <td class="vcenter text-center" width="50px">
                                            @if (!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                                <img width="50" height="60"
                                                    src="{{ $basePath }}/public/uploads/cm/{{ $target['photo'] }}"
                                                    alt="{{ $target['official_name'] ?? '' }}" />
                                            @else
                                                <img width="50" height="60"
                                                    src="{{ $basePath }}/public/img/unknown.png"
                                                    alt="{{ $target['official_name'] ?? '' }}" />
                                            @endif
                                        </td>
                                        <td class="vcenter text-center">{!! !empty($target['grading']) && $target['grading'] != 0 ? $target['grading'] : '' !!}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11">@lang('label.NO_CM_IS_ASSIGNED_TO_THIS_COURSE')</td>
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
                    @lang('label.GENERATED_ON') {!! '<strong>' .
                        Helper::formatDate(date('Y-m-d H:i:s')) .
                        '</strong> by <strong>' .
                        Auth::user()->full_name .
                        '</strong>' !!}.
                </td>
                <td class="no-border text-right">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>

        <script src="{{ asset('public/assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript">
        </script>
        <script src="{{ asset('public/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"
            type="text/javascript"></script>
        <!-- END CORE PLUGINS -->


        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{ asset('public/assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!--<script src="{{ asset('public/assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>-->



        <!--<script src="{{ asset('public/js/apexcharts.min.js') }}" type="text/javascript"></script>-->


        <script>
            document.addEventListener("DOMContentLoaded", function(event) {
                window.print();
            });
        </script>
    </body>

    </html>
@else
    <html>

    <head>
        <link href="{{ asset('public/fonts/css.css?family=Open Sans') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('public/assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components"
            type="text/css" />
        <link href="{{ asset('public/assets/global/plugins/morris/morris.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/global/plugins/jqvmap/jqvmap/jqvmap.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />


        <!--BEGIN THEME LAYOUT STYLES-->
        <!--<link href="{{ asset('public/assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css" />-->
        <link href="{{ asset('public/assets/layouts/layout/css/custom.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('public/css/custom.css') }}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link href="{{ asset('public/assets/layouts/layout/css/downloadPdfPrint/print.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('public/assets/layouts/layout/css/downloadPdfPrint/printInvoice.css') }}"
            rel="stylesheet" type="text/css" />
    </head>

    <body>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <img width="500" height="auto" src="public/img/sint_ams_logo.jpg" alt="" />
                </td>
            </tr>
            <tr>
                <td class="no-border text-center" colspan="8">
                    <strong>{!! __('label.LESSON_WISE_GS_FEEDBACK_FROM_CM') !!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{ __('label.TRAINING_YEAR') }} :
                        <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }}
                            |</strong>
                        {{ __('label.COURSE') }} :
                        <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }}
                            |</strong>
                        {{ __('label.GS') }} :
                        <strong>{{ !empty($gsList[Request::get('gs_id')]) && Request::get('gs_id') != 0 ? $gsList[Request::get('gs_id')] : __('label.N_A') }}
                            |</strong>
                        {{ __('label.LESSON') }} :
                        <strong>{{ !empty($lessonList[Request::get('lesson_id')]) && Request::get('lesson_id') != 0 ? $lessonList[Request::get('lesson_id')] : __('label.N_A') }}
                        </strong>
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
                    <th class="vcenter">@lang('label.FULL_NAME')</th>
                    <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                    <th class="vcenter">@lang('label.WING')</th>

                    <th class="vcenter text-center">@lang('label.GRADING')</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($cmArr))
                    <?php
                    $sl = 0;
                    ?>

                    @foreach ($cmArr as $id => $target)
                        <tr>
                            <td class="vcenter text-center">{!! ++$sl !!}</td>
                            <td class="vcenter">{!! !empty($target['personal_no']) ? $target['personal_no'] : '' !!}</td>
                            <td class="vcenter">{!! $target['rank'] ?? '' !!}</td>
                            <td class="vcenter">{!! $target['full_name'] ?? '' !!}</td>
                            <td class="vcenter">{!! $target['official_name'] ?? '' !!}</td>
                            <td class="vcenter">{!! $target['wing'] ?? '' !!}</td>
                            <td class="vcenter text-center">{!! !empty($target['grading']) && $target['grading'] != 0 ? $target['grading'] : '' !!}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">@lang('label.NO_CM_IS_ASSIGNED_TO_THIS_COURSE')</td>
                    </tr>
                @endif
            </tbody>

        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border text-left" colspan="4">
                    @lang('label.GENERATED_ON') {!! '<strong>' .
                        Helper::formatDate(date('Y-m-d H:i:s')) .
                        '</strong> by <strong>' .
                        Auth::user()->full_name .
                        '</strong>' !!}.
                </td>
                <td class="no-border text-right" colspan="4">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>
    </body>

    </html>
@endif
