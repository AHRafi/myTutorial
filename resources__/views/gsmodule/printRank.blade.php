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
            <p>@lang('label.RANK_LIST')</p>
        </div>

        <table class="table table-striped table-bordered">
            <thead>        
                <tr>
                    <th>@lang('label.SL_NO')</th>
                    <th class="vcenter">@lang('label.RANK_NAME')</th>
                    <th>@lang('label.CODE')</th>
                    <th class="text-center">@lang('label.ORDER')</th>
                    <th class="text-center">@lang('label.STATUS')</th>
                </tr>
            </thead>
            <tbody>
                @if (!$targetArr->isEmpty())
                <?php
                $sl = 0;
                ?>
                @foreach($targetArr as $target)
                <tr>
                    <td>{{ ++$sl }}</td>
                    <td class="vcenter">{{ $target->rank_name }}</td>
                    <td>{{ $target->code }}</td>
                    <td class="text-center">{{ $target->order }}</td>
                    <td class="text-center">
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
                    <td colspan="6">@lang('label.NO_RANK_FOUND')</td>
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