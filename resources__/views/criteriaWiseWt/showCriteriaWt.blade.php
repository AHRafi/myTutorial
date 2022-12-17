<div class="row">
    @if(!empty($totalCourseWt))
    <div class="col-md-12 margin-top-10">
        <span class="label label-md bold label-blue-steel">
            @lang('label.TOTAL_COURSE_WT'):&nbsp;{!! $totalCourseWt->total_course_wt ?? '' !!}
        </span>
        {!! Form::hidden('total_course_wt',!empty($totalCourseWt->total_course_wt) ? $totalCourseWt->total_course_wt : null,['id' => 'totalCourseWtId']) !!}
    </div>
    @endif
    <div class="col-md-12 margin-top-10">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>

                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="text-center vcenter">@lang('label.CRITERIA')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>

                </tr>
            </thead>

            <tbody>

                <?php
                $sl = 0;
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.TOTAL_EVENT_WT')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('total_event_wt',!empty($criteriaWtArr['total_event_wt']) ? $criteriaWtArr['total_event_wt'] : null, ['id'=> 'totalEventWtId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left"> @lang('label.CI_OBSN_WT')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('ci_obsn_wt',!empty($criteriaWtArr['ci_obsn_wt']) ? $criteriaWtArr['ci_obsn_wt'] : null, ['id'=> 'ciObsnWtId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left"> @lang('label.COMDT_OBSN_WT')</td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('comdt_obsn_wt',!empty($criteriaWtArr['comdt_obsn_wt']) ? $criteriaWtArr['comdt_obsn_wt'] : null, ['id'=> 'comdtObsnWtId', 'class' => 'form-control integer-decimal-only text-inherit text-right','autocomplete' => 'off']) !!}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($criteriaWtArr['total_wt']) ? $criteriaWtArr['total_wt'] : '' !!}</span>
                        {!! Form::hidden('total',!empty($criteriaWtArr['total_wt']) ? $criteriaWtArr['total_wt'] : null ,['class' => 'total-wt']) !!}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-12 text-center">
        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
            <i class="fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href="{{ URL::to('criteriaWiseWt') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>
<script src="{{asset('public/js/custom.js')}}"></script>
<script>
$(document).ready(function () {
    $(document).on('keyup', '#totalEventWtId', function () {
        var totalEventWt = parseFloat($(this).val());
        var totalCourseWt = parseFloat($("#totalCourseWtId").val());
        total();
        var totalWt = parseFloat($(".total-wt").val());
        if (totalEventWt > totalCourseWt) {
            swal({
                title: '@lang("label.YOUR_GIVEN_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                   
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#totalEventWtId").val('');
                $("#totalEventWtId").focus('');
                total();
                return false;
            });
        } else if (totalWt > totalCourseWt) {
            swal({
                title: '@lang("label.TOTAL_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                   
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#totalEventWtId").val('');
                $("#totalEventWtId").focus('');
                total();
                return false;
            });
        }
    });
    $(document).on('keyup', '#ciObsnWtId', function () {
        var ciObsnWt = parseFloat($(this).val());
        var totalCourseWt = parseFloat($("#totalCourseWtId").val());
        total();
        var totalWt = parseFloat($(".total-wt").val());
        if (ciObsnWt > totalCourseWt) {
            swal({
                title: '@lang("label.YOUR_GIVEN_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                   
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#ciObsnWtId").val('');
                $("#ciObsnWtId").focus('');
                total();
                return false;
            });
        } else if (totalWt > totalCourseWt) {
            swal({
                title: '@lang("label.TOTAL_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                   
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#ciObsnWtId").val('');
                $("#ciObsnWtId").focus('');
                total();
                return false;
            });
        }
    });
    $(document).on('keyup', '#comdtObsnWtId', function () {
        var comdtObsnWt = parseFloat($(this).val());
        var totalCourseWt = parseFloat($("#totalCourseWtId").val());
        total();
        var totalWt = parseFloat($(".total-wt").val());
        if (comdtObsnWt > totalCourseWt) {
            swal({
                title: '@lang("label.YOUR_GIVEN_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                   
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#comdtObsnWtId").val('');
                $("#comdtObsnWtId").focus('');
                total();
                return false;
            });
        } else if (totalWt > totalCourseWt) {
            swal({
                title: '@lang("label.TOTAL_WT_EXCEEDED_FROM_TOTAL_COURSE_WT")',
                   
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $("#comdtObsnWtId").val('');
                $("#comdtObsnWtId").focus('');
                total();
                return false;
            });
        }
    });


    function total() {
        var totalEventWt = $('#totalEventWtId').val();
        if (isNaN(totalEventWt)) {
            totalEventWt = 0;
        }
        var ciObsnWt = $('#ciObsnWtId').val();
        if (isNaN(ciObsnWt)) {
            ciObsnWt = 0;
        }
        var comdtObsnWt = $('#comdtObsnWtId').val();
        if (isNaN(comdtObsnWt)) {
            comdtObsnWt = 0;
        }
        //var total = 0;
        var total = parseFloat(Number(totalEventWt) + Number(ciObsnWt) + Number(comdtObsnWt)).toFixed(2);
        $(".total-wt").text(total);
        $(".total-wt").val(total);
    }
});
</script>


