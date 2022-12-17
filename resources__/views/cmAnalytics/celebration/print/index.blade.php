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
            @page { 
                margin: 67px 48px 67px 96px;
                size: auto; 
            }
            body {
                font-size: 10px;
                font-family: Arial;
            }
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
                        <span class="header">@lang('label.CELEBRATION_REPORT')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.CEL_EVENT')}} : <strong>{{ !empty($celEventList[Request::get('cel_event')]) && Request::get('cel_event') != 0 ? $celEventList[Request::get('cel_event')] : __('label.N_A') }} |</strong>
                            {{__('label.MONTH')}} : <strong>{{ !empty($monthList[Request::get('month')]) && Request::get('month') != '00' ? $monthList[Request::get('month')] : __('label.N_A') }} |</strong>
                            {{__('label.DAY')}} : <strong>{{ __('label.FROM') . ' ' . Request::get('day_from') . ' ' . __('label.TO') . ' ' . Request::get('day_to') }} </strong>
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
                                <th class="vcenter width-100">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter">@lang('label.RANK')</th>
                                <th class="vcenter">@lang('label.FULL_NAME')</th>
                                <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                @if(Request::get('cel_event') == '1')
                                <th class="vcenter text-center">@lang('label.SELF_BIRTH_DATE')</th>
                                @elseif(Request::get('cel_event') == '2')
                                <th class="vcenter text-center">@lang('label.SELF_BIRTH_DATE')</th>
                                <th class="vcenter text-center">@lang('label.SPOUSE_BIRTH_DATE')</th>
                                @elseif(Request::get('cel_event') == '3')
                                <th class="vcenter text-center">@lang('label.MARRIAGE_DATE')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty(Request::get('month')) && Request::get('month') != '00')
                            <tr class="active">
                                <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.THIS_MONTH')</td>
                            </tr>
                            @if (!empty($targetArr['this']))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($targetArr['this'] as $date => $target)

                            <tr>
                                <td class="vcenter text-center">{!! ++$sl !!}</td>

                                <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                                <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                                <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                                <td class="vcenter text-center" width="50px">
                                    @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                    <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                    @else
                                    <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                    @endif
                                </td>
                                @if(Request::get('cel_event') == '1')
                                <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                @elseif(Request::get('cel_event') == '2')
                                <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                @elseif(Request::get('cel_event') == '3')
                                <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NO_CM_FOUND')</td>
                            </tr>
                            @endif
                            <tr class="active">
                                <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NEXT_MONTH')</td>
                            </tr>
                            @if (!empty($targetArr['coming']))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($targetArr['coming'] as $date => $target)

                            <tr>
                                <td class="vcenter text-center">{!! ++$sl !!}</td>

                                <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                                <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                                <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                                <td class="vcenter text-center" width="50px">
                                    @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                    <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                    @else
                                    <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                    @endif
                                </td>
                                @if(Request::get('cel_event') == '1')
                                <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                @elseif(Request::get('cel_event') == '2')
                                <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                @elseif(Request::get('cel_event') == '3')
                                <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NO_CM_FOUND')</td>
                            </tr>
                            @endif
                            <tr class="active">
                                <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.PREVIOUS_MONTH')</td>
                            </tr>
                            @if (!empty($targetArr['prev']))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($targetArr['prev'] as $date => $target)

                            <tr>
                                <td class="vcenter text-center">{!! ++$sl !!}</td>

                                <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                                <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                                <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                                <td class="vcenter text-center" width="50px">
                                    @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                    <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                    @else
                                    <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                    @endif
                                </td>
                                @if(Request::get('cel_event') == '1')
                                <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                @elseif(Request::get('cel_event') == '2')
                                <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                @elseif(Request::get('cel_event') == '3')
                                <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NO_CM_FOUND')</td>
                            </tr>
                            @endif
                                @else
                                @if (!empty($targetArr['all']))
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($targetArr['all'] as $date => $target)

                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    @if(Request::get('cel_event') == '1')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '2')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '3')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                    @endif
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NO_CM_FOUND')</td>
                                </tr>
                                @endif
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
                    <strong>{!!__('label.CELEBRATION_REPORT')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.CEL_EVENT')}} : <strong>{{ !empty($celEventList[Request::get('cel_event')]) && Request::get('cel_event') != 0 ? $celEventList[Request::get('cel_event')] : __('label.N_A') }} |</strong>
                        {{__('label.MONTH')}} : <strong>{{ !empty($monthList[Request::get('month')]) && Request::get('month') != '00' ? $monthList[Request::get('month')] : __('label.N_A') }} |</strong>
                        {{__('label.DAY')}} : <strong>{{ __('label.FROM') . ' ' . Request::get('day_from') . ' ' . __('label.TO') . ' ' . Request::get('day_to') }} </strong>
                    </h5>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
            <thead>
                <tr>
                    <th class="vcenter text-center">@lang('label.SERIAL')</th>
                    <th class="vcenter width-100">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter">@lang('label.RANK')</th>
                    <th class="vcenter">@lang('label.FULL_NAME')</th>
                    @if(Request::get('cel_event') == '1')
                    <th class="vcenter text-center">@lang('label.SELF_BIRTH_DATE')</th>
                    @elseif(Request::get('cel_event') == '2')
                    <th class="vcenter text-center">@lang('label.SELF_BIRTH_DATE')</th>
                    <th class="vcenter text-center">@lang('label.SPOUSE_BIRTH_DATE')</th>
                    @elseif(Request::get('cel_event') == '3')
                    <th class="vcenter text-center">@lang('label.MARRIAGE_DATE')</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if(!empty(Request::get('month')) && Request::get('month') != '00')
                <tr class="active">
                    <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.THIS_MONTH')</td>
                </tr>
                @if (!empty($targetArr['this']))
                <?php
                $sl = 0;
                ?>
                @foreach($targetArr['this'] as $date => $target)

                <tr>
                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                    @if(Request::get('cel_event') == '1')
                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                    @elseif(Request::get('cel_event') == '2')
                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                    @elseif(Request::get('cel_event') == '3')
                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                    @endif
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 5 : (Request::get('cel_event') == '2' ? 6 : 4) !!}">@lang('label.NO_CM_FOUND')</td>
                </tr>
                @endif
                <tr class="active">
                    <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NEXT_MONTH')</td>
                </tr>
                @if (!empty($targetArr['coming']))
                <?php
                $sl = 0;
                ?>
                @foreach($targetArr['coming'] as $date => $target)

                <tr>
                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                    @if(Request::get('cel_event') == '1')
                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                    @elseif(Request::get('cel_event') == '2')
                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                    @elseif(Request::get('cel_event') == '3')
                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                    @endif
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 5 : (Request::get('cel_event') == '2' ? 6 : 4) !!}">@lang('label.NO_CM_FOUND')</td>
                </tr>
                @endif
                <tr class="active">
                    <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 5 : (Request::get('cel_event') == '2' ? 6 : 4) !!}">@lang('label.PREVIOUS_MONTH')</td>
                </tr>
                @if (!empty($targetArr['prev']))
                <?php
                $sl = 0;
                ?>
                @foreach($targetArr['prev'] as $date => $target)

                <tr>
                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                    @if(Request::get('cel_event') == '1')
                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                    @elseif(Request::get('cel_event') == '2')
                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                    @elseif(Request::get('cel_event') == '3')
                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                    @endif
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 5 : (Request::get('cel_event') == '2' ? 6 : 4) !!}">@lang('label.NO_CM_FOUND')</td>
                </tr>
                @endif
                @else
                @if (!empty($targetArr['all']))
                <?php
                $sl = 0;
                ?>
                @foreach($targetArr['all'] as $date => $target)

                <tr>
                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                    @if(Request::get('cel_event') == '1')
                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                    @elseif(Request::get('cel_event') == '2')
                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                    @elseif(Request::get('cel_event') == '3')
                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                    @endif
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 5 : (Request::get('cel_event') == '2' ? 6 : 4) !!}">@lang('label.NO_CM_FOUND')</td>
                </tr>
                @endif
                @endif
            </tbody>

        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border" colspan="4">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border" colspan="4">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>
    </body>
</html>
@endif