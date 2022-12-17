<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="text-center vcenter">@lang('label.AUTHORITY')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $sl = 0;
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.OIC_WARNING')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('oic_warning',!empty($warningWtArr['oic_warning']) ? $warningWtArr['oic_warning'] : null, ['id'=> 'oicWarningId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.CI_WARNING')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('ci_warning',!empty($warningWtArr['ci_warning']) ? $warningWtArr['ci_warning'] : null, ['id'=> 'ciWarningId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.COMDT_WARNING')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('comdt_warning',!empty($warningWtArr['comdt_warning']) ? $warningWtArr['comdt_warning'] : null, ['id'=> 'comdtWarningId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($warningWtArr['total_wt']) ? $warningWtArr['total_wt'] : '' !!}</span>
                        {!! Form::hidden('total',!empty($warningWtArr['total_wt']) ? $warningWtArr['total_wt'] : null ,['id' => 'totalWt']) !!}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-5 col-md-5">
                    <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                    </button>
                    <a href="{{ URL::to('warningWt') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('public/js/custom.js')}}"></script>
<script>
    $(document).ready(function () {
        $(document).on('keyup', '#oicWarningId', function () {
            total();
        });
        $(document).on('keyup', '#ciWarningId', function () {
            total();
        });
        $(document).on('keyup', '#comdtWarningId', function () {
            total();
        });

        function total() {
            var oicWarning = $('#oicWarningId').val();
            if (isNaN(oicWarning)) {
                oicWarning = 0;
            }
            var ciWarning = $('#ciWarningId').val();
            if (isNaN(ciWarning)) {
                ciWarning = 0;
            }
            var comdtWarning = $('#comdtWarningId').val();
            if (isNaN(comdtWarning)) {
                comdtWarning = 0;
            }
            var total = parseFloat(Number(oicWarning) + Number(ciWarning) + Number(comdtWarning)).toFixed(2);
            $(".total-wt").text(total);
            $("#totalWt").val(total);
        }
    });
</script>


