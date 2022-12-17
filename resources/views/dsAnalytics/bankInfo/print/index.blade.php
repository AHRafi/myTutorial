<?php
$basePath = URL::to('/');
$columns = !empty(Request::get('columns')) ? explode(',', Request::get('columns')) : [];
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
                        <span class="header">@lang('label.BANK_INFORMATION_WISE_DS_ANALYTICS')</span>
                    </div>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.NAME')}} : <strong>{{ !empty(Request::get('name')) && Request::get('name') != '' ? Request::get('name') : __('label.N_A') }} |</strong>
                            {{__('label.SERVICE')}} : <strong>{{ !empty($wingList[Request::get('wing_id')]) && Request::get('wing_id') != 0 ? $wingList[Request::get('wing_id')] : __('label.ALL') }} |</strong>
                            {{__('label.RANK')}} : <strong>{{ !empty($rankList[Request::get('rank')]) && Request::get('rank_id') != 0 ? $rankList[Request::get('rank')] : __('label.ALL') }} |</strong>
                            {{__('label.ARMS_SERVICE')}} : <strong>{{ !empty($armsServiceList[Request::get('arms_service')]) && Request::get('arms_service') != 0 ? $armsServiceList[Request::get('arms_service')] : __('label.ALL') }} |</strong>
                            {{__('label.BANK_NAME')}} : <strong>{{ !empty(Request::get('bank')) && Request::get('bank') != '' ? Request::get('bank') : __('label.N_A') }} |</strong>
                            {{__('label.BRANCH')}} : <strong>{{ !empty(Request::get('branch')) && Request::get('branch') != '' ? Request::get('branch') : __('label.N_A') }} |</strong>
                            {{__('label.ONLINE')}} : <strong>{{ !empty(Request::get('online_check')) && Request::get('online_check') == 1 ? __('label.YES') : __('label.N_A') }} |</strong>
                            {{__('label.TOTAL_NO_OF_DS')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

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
                                @if(empty($columns) || in_array('1', $columns))
                                <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                @endif
                                <th class="vcenter">@lang('label.ARMS_SERVICE')</th>
                                @if(empty($columns) || in_array('2', $columns))
                                <th class="vcenter">@lang('label.APPT_AFWC')</th>
                                @endif
                                @if(empty($columns) || in_array('3', $columns))
                                <th class="vcenter">@lang('label.BANK_NAME')</th>
                                @endif
                                @if(empty($columns) || in_array('4', $columns))
                                <th class="vcenter">@lang('label.BANK_ACCOUNT_NO')</th>
                                @endif
                                @if(empty($columns) || in_array('5', $columns))
                                <th class="vcenter">@lang('label.BRANCH')</th>
                                @endif
                                @if(empty($columns) || in_array('6', $columns))
                                <th class="vcenter text-center">@lang('label.ONLINE')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($targetArr))
                            <?php
                            $sl = 0;
                            ?>

                            @foreach($targetArr as $id => $target)

                            <tr>
                                <td class="vcenter text-center" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                    {!! ++$sl !!}
                                </td>

                                <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                    {!! Common::getFurnishedCmName($target['personal_no']) !!}
                                </td>
                                <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                    {!! Common::getFurnishedCmName($target['rank']) !!}
                                </td>
                                <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                    {!! Common::getFurnishedCmName($target['full_name']) !!}
                                </td>
                                @if(empty($columns) || in_array('1', $columns))
                                <td class="vcenter text-center" width="50px" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                    @if(!empty($target['photo']) && File::exists('public/uploads/user/' . $target['photo']))
                                    <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                    @else
                                    <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                    @endif
                                </td>
                                @endif
                                <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                    {!! $target['arms_service_name'] ?? '' !!}
                                </td>
                                @if(empty($columns) || in_array('2', $columns))
                                <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                    {!! $target['appointment_name']?? '' !!}
                                </td>
                                @endif
                                @if(!empty($target['rec_svc']))
                                <?php $i = 0; ?>
                                @foreach($target['rec_svc'] as $rsKey => $rsInfo)
                                <?php
                                if ($i > 0) {
                                    echo '<tr>';
                                }
                                ?>
                                @if(empty($columns) || in_array('3', $columns))
                                <td class="vcenter">{!! $rsInfo['name'] ?? '' !!}</td>
                                @endif
                                @if(empty($columns) || in_array('4', $columns))
                                <td class="vcenter">{!! $rsInfo['account'] ?? '' !!}</td>
                                @endif
                                @if(empty($columns) || in_array('5', $columns))
                                <td class="vcenter">{!! $rsInfo['branch'] ?? '' !!}</td>
                                @endif
                                @if(empty($columns) || in_array('6', $columns))
                                <td class="vcenter text-center">{!! !empty($rsInfo['is_online']) && $rsInfo['is_online']==1 ? 'Yes' : 'No' ?? '' !!}</td>
                                @endif
                                <?php
                                if ($i < ($target['rec_svc_span'] - 1)) {
                                    echo '</tr>';
                                }
                                $i++;
                                ?>
                                @endforeach
                                @else 
                                @if(empty($columns) || in_array('3', $columns))
                                <td class="vcenter"></td>
                                @endif
                                @if(empty($columns) || in_array('4', $columns))
                                <td class="vcenter"></td>
                                @endif
                                @if(empty($columns) || in_array('5', $columns))
                                <td class="vcenter"></td>
                                @endif
                                @if(empty($columns) || in_array('6', $columns))
                                <td class="vcenter text-center"></td>
                                @endif

                                @endif


                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="11">@lang('label.NO_DS_FOUND')</td>
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
                    <strong>{!!__('label.BANK_INFORMATION_WISE_DS_ANALYTICS')!!}</strong>
                </td>
            </tr>
        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="" colspan="8">
                    <h5 style="padding: 10px;">
                        {{__('label.NAME')}} : <strong>{{ !empty(Request::get('name')) && Request::get('name') != '' ? Request::get('name') : __('label.N_A') }} |</strong>
                        {{__('label.SERVICE')}} : <strong>{{ !empty($wingList[Request::get('wing_id')]) && Request::get('wing_id') != 0 ? $wingList[Request::get('wing_id')] : __('label.ALL') }} |</strong>
                        {{__('label.RANK')}} : <strong>{{ !empty($rankList[Request::get('rank')]) && Request::get('rank_id') != 0 ? $rankList[Request::get('rank')] : __('label.ALL') }} |</strong>
                        {{__('label.ARMS_SERVICE')}} : <strong>{{ !empty($armsServiceList[Request::get('arms_service')]) && Request::get('arms_service') != 0 ? $armsServiceList[Request::get('arms_service')] : __('label.ALL') }} |</strong>
                        {{__('label.BANK_NAME')}} : <strong>{{ !empty(Request::get('bank')) && Request::get('bank') != '' ? Request::get('bank') : __('label.N_A') }} |</strong>
                        {{__('label.BRANCH')}} : <strong>{{ !empty(Request::get('branch')) && Request::get('branch') != '' ? Request::get('branch') : __('label.N_A') }} |</strong>
                        {{__('label.ONLINE')}} : <strong>{{ !empty(Request::get('online_check')) && Request::get('online_check') == 1 ? __('label.YES') : __('label.N_A') }} |</strong>
                        {{__('label.TOTAL_NO_OF_DS')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

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

                    <th class="vcenter">@lang('label.ARMS_SERVICE')</th>
                    @if(empty($columns) || in_array('2', $columns))
                    <th class="vcenter">@lang('label.APPT_AFWC')</th>
                    @endif
                    @if(empty($columns) || in_array('3', $columns))
                    <th class="vcenter">@lang('label.BANK_NAME')</th>
                    @endif
                    @if(empty($columns) || in_array('4', $columns))
                    <th class="vcenter">@lang('label.BANK_ACCOUNT_NO')</th>
                    @endif
                    @if(empty($columns) || in_array('5', $columns))
                    <th class="vcenter">@lang('label.BRANCH')</th>
                    @endif
                    @if(empty($columns) || in_array('6', $columns))
                    <th class="vcenter text-center">@lang('label.ONLINE')</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if (!empty($targetArr))
                <?php
                $sl = 0;
                ?>

                @foreach($targetArr as $id => $target)

                <tr>
                    <td class="vcenter text-center" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                        {!! ++$sl !!}
                    </td>

                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                        {!! Common::getFurnishedCmName($target['personal_no']) !!}
                    </td>
                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                        {!! Common::getFurnishedCmName($target['rank']) !!}
                    </td>
                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                        {!! Common::getFurnishedCmName($target['full_name']) !!}
                    </td>

                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                        {!! $target['arms_service_name'] ?? '' !!}
                    </td>
                    @if(empty($columns) || in_array('2', $columns))
                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                        {!! $target['appointment_name']?? '' !!}
                    </td>
                    @endif
                    @if(!empty($target['rec_svc']))
                    <?php $i = 0; ?>
                    @foreach($target['rec_svc'] as $rsKey => $rsInfo)
                    <?php
                    if ($i > 0) {
                        echo '<tr>';
                    }
                    ?>
                    @if(empty($columns) || in_array('3', $columns))
                    <td class="vcenter">{!! $rsInfo['name'] ?? '' !!}</td>
                    @endif
                    @if(empty($columns) || in_array('4', $columns))
                    <td class="vcenter">{!! $rsInfo['account'] ?? '' !!}</td>
                    @endif
                    @if(empty($columns) || in_array('5', $columns))
                    <td class="vcenter">{!! $rsInfo['branch'] ?? '' !!}</td>
                    @endif
                    @if(empty($columns) || in_array('6', $columns))
                    <td class="vcenter text-center">{!! !empty($rsInfo['is_online']) && $rsInfo['is_online']==1 ? 'Yes' : 'No' ?? '' !!}</td>
                    @endif
                    <?php
                    if ($i < ($target['rec_svc_span'] - 1)) {
                        echo '</tr>';
                    }
                    $i++;
                    ?>
                    @endforeach
                    @else 
                    @if(empty($columns) || in_array('3', $columns))
                    <td class="vcenter"></td>
                    @endif
                    @if(empty($columns) || in_array('4', $columns))
                    <td class="vcenter"></td>
                    @endif
                    @if(empty($columns) || in_array('5', $columns))
                    <td class="vcenter"></td>
                    @endif
                    @if(empty($columns) || in_array('6', $columns))
                    <td class="vcenter text-center"></td>
                    @endif

                    @endif


                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="11">@lang('label.NO_DS_FOUND')</td>
                </tr>
                @endif
            </tbody>

        </table>
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border" colspan="4">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border" colspan="5">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>
    </body>
</html>
@endif