<div class="row">
    @if(!empty($criteriaWiseWtArr))
    <div class="col-md-12 margin-top-10">
        <span class="label label-md bold label-blue-steel">
            @lang('label.TOTAL_OBSN_WT'):&nbsp;{!! !empty($criteriaWiseWtArr->obsn_wt) ? $criteriaWiseWtArr->obsn_wt : 0 !!}
        </span>
    </div>
    <div class="col-md-12 margin-top-10">
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
                    <td class="text-left">@lang('label.DS_OBSN')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('ds_obsn',!empty($obsnWtArr['ds_obsn']) ? $obsnWtArr['ds_obsn'] : null, ['id'=> 'dsObsnId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.OIC_OBSN')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('oic_obsn',!empty($obsnWtArr['oic_obsn']) ? $obsnWtArr['oic_obsn'] : null, ['id'=> 'oicObsnId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.CI_OBSN')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('ci_obsn',!empty($obsnWtArr['ci_obsn']) ? $obsnWtArr['ci_obsn'] : null, ['id'=> 'ciObsnId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.COMDT_OBSN')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('comdt_obsn',!empty($obsnWtArr['comdt_obsn']) ? $obsnWtArr['comdt_obsn'] : null, ['id'=> 'comdtObsnId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($obsnWtArr['total_wt']) ? $obsnWtArr['total_wt'] : '' !!}</span>
                        {!! Form::hidden('total',!empty($obsnWtArr['total_wt']) ? $obsnWtArr['total_wt'] : null,['class' => 'total-wt']) !!}
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
                    <a href="{{ URL::to('obsnWt') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_OBSN_WT_FOUND') !!}</strong></p>
        </div>
    </div>
    @endif
</div>
<script src="{{asset('public/js/custom.js')}}"></script>
<script>
    $(document).ready(function () {
        $(document).on('keyup', '#dsObsnId', function () {
            total();
        });
        $(document).on('keyup', '#oicObsnId', function () {
            total();
        });
        $(document).on('keyup', '#ciObsnId', function () {
            total();
        });
        $(document).on('keyup', '#comdtObsnId', function () {
            total();
        });

        function total() {
            var dsObsn = $('#dsObsnId').val();
            if (isNaN(dsObsn)) {
                dsObsn = 0;
            }
            var oicObsn = $('#oicObsnId').val();
            if (isNaN(oicObsn)) {
                oicObsn = 0;
            }
            var ciObsn = $('#ciObsnId').val();
            if (isNaN(ciObsn)) {
                ciObsn = 0;
            }
            var comdtObsn = $('#comdtObsnId').val();
            if (isNaN(comdtObsn)) {
                comdtObsn = 0;
            }
            //var total = 0;
            var total = parseFloat(Number(dsObsn) + Number(oicObsn) + Number(ciObsn) + Number(comdtObsn)).toFixed(2);
            $(".total-wt").text(total);
            $(".total-wt").val(total);
        }
    });
</script>


