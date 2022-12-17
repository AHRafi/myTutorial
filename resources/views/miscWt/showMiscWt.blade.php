<div class="row">
    @if(!empty($criteriaWiseWtArr))
    <div class="col-md-12 margin-top-10">
        <span class="label label-md bold label-blue-steel">
            @lang('label.TOTAL_MISC_WT'):&nbsp;{!! !empty($criteriaWiseWtArr->misc_wt) ? $criteriaWiseWtArr->misc_wt : 0 !!}
        </span>
    </div>
    <div class="col-md-12 margin-top-10">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="text-center vcenter">@lang('label.EVENT')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $sl = 0;
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.MUTUAL_ASSIGNMENT')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('mutual_assignment',!empty($miscWtArr['mutual_assign']) ? $miscWtArr['mutual_assign'] : null, ['id'=> 'mutualAssignId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.IPFT_1')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('ipft_1',null, ['id'=> 'ipft1Id', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off','disabled']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.IPFT_2')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('ipft_2',!empty($miscWtArr['ipft_2']) ? $miscWtArr['ipft_2'] : null, ['id'=> 'ipft2Id', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.IPFT_3')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('ipft_3',!empty($miscWtArr['ipft_3']) ? $miscWtArr['ipft_3'] : null, ['id'=> 'ipft3Id', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($miscWtArr['total_wt']) ? $miscWtArr['total_wt'] : '' !!}</span>
                        {!! Form::hidden('total',!empty($miscWtArr['total_wt']) ? $miscWtArr['total_wt'] : null ,['id' => 'totalWt']) !!}
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
                    <a href="{{ URL::to('miscWt') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_MISC_WT_FOUND') !!}</strong></p>
        </div>
    </div>
    @endif
</div>
<script src="{{asset('public/js/custom.js')}}"></script>
<script>
    $(document).ready(function () {
        $(document).on('keyup', '#mutualAssignId', function () {
            total();
        });
        
        $(document).on('keyup', '#ipft2Id', function () {
            total();
        });
        $(document).on('keyup', '#ipft3Id', function () {
            total();
        });

        function total() {
            var mutualAssign = $('#mutualAssignId').val();
            if (isNaN(mutualAssign)) {
                mutualAssign = 0;
            }
            var ipft2 = $('#ipft2Id').val();
            if (isNaN(ipft2)) {
                ipft2 = 0;
            }
            var ipft3 = $('#ipft3Id').val();
            if (isNaN(ipft3)) {
                ipft3 = 0;
            }
            //var total = 0;
            var total = parseFloat(Number(mutualAssign) + Number(ipft2) + Number(ipft3)).toFixed(2);
            $(".total-wt").text(total);
            $("#totalWt").val(total);
        }
    });
</script>


