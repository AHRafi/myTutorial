<html>
    <head>
        <title>@lang('label.ATMS_TITLE')</title>
        @if(Request::get('download') == 'print')
        <link rel="shortcut icon" href="{{URL::to('/')}}/public/img/favicon.ico" />
        <link href="{{asset('public/assets/layouts/layout/css/downloadPdfPrint/print.css')}}" rel="stylesheet" type="text/css" />
        @elseif(Request::get('download') == 'pdf')
        <link rel="shortcut icon" href="{!! base_path() !!}/public/img/favicon.ico" />
        <link href="{{ base_path().'/public/assets/layouts/layout/css/downloadPdfPrint/print.css'}}" rel="stylesheet" type="text/css" />
        <link href="{{ base_path().'/public/assets/layouts/layout/css/downloadPdfPrint/pdf.css'}}" rel="stylesheet" type="text/css" />
        @endif
    </head>
    <body>

        <div class="header">
            <p>@lang('label.USER_LIST')</p>
        </div>

        <table class="table table-striped table-bordered">
            <thead>        
                <tr>
                    <th class="vcenter">@lang('label.SL_NO')</th>
                    <th class="vcenter">@lang('label.ORGANISATION')</th>
                    <th class="vcenter">@lang('label.USER_GROUP')</th>
                    <th class="text-center vcenter">@lang('label.SERVICE')</th>
                    <th class="text-center vcenter">@lang('label.RANK')</th>
                    <th class="text-center vcenter">@lang('label.APPT')</th>
                    <th class="vcenter">@lang('label.NAME')</th>
                    <th class="vcenter">@lang('label.USERNAME')</th>
                    <th class=" text-center vcenter">@lang('label.PERSONAL_SERVICE_NO')</th>
                    <th class="text-center vcenter">@lang('label.STATUS')</th>
                </tr>
            </thead>
            <tbody>
                @if (!$targetArr->isEmpty())
                <?php
                $sl = 0;
                ?>
                @foreach($targetArr as $target)
                <tr>
                    <td class="vcenter">{{ ++$sl }}</td>
                    <td class="vcenter">
                        @if($target->category_id == '1')
                        <span>@lang('label.HQ_ARTDOC')</span>
                        @elseif($target->category_id == '2')
                        <span>{{ $target->institute_code }}</span>
                        @else
                        <span>{{ $target->dtebr_code }}</span>	
                        @endif
                    </td>
                    <td class="vcenter">{{ $target->group_name }}</td>
                    <td class="text-center vcenter">{{ $target->service_code }}</td>
                    <td class="text-center vcenter">{{ $target->rank_code }}</td>
                    <td class="text-center vcenter">{{ $target->appt_code }}</td>
                    <td class="vcenter">{{ $target->full_name }}</td>
                    <td class="vcenter">{{ $target->username }}</td>
                    <td class="text-center vcenter">{{ $target->personnel_no_prefix_name . $target->personal_service_no }}</td>
                    <td class="text-center vcenter">
                        @if($target->status == '1')
                        <span>@lang('label.ACTIVE')</span>
                        @else
                        <span>@lang('label.INACTIVE')</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="10" class="vcenter">@lang('label.NO_USER_FOUND')</td>
                </tr>
                @endif
            </tbody>
        </table>
        <table class="no-border">
            <tr>
                <td class="no-border text-right">@lang('label.REPORT_GENERATED_ON') {{ Helper::printDateTime(date('Y-m-d H:i:s')).' by '.$rankCode->code.' '.Auth::user()->full_name }}</td>
            </tr>
        </table>
        <script>
            document.addEventListener("DOMContentLoaded", function (event) {
                window.print();
                //window.close();
            });
        </script>
    </body>
</html>