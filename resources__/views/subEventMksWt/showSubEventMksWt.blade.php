<div class="row">
    <!--    @if(!empty($eventAssessmentMarkingData))
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.MARKING_HAS_ALREADY_BEEN_STARTED') !!}</strong></p>
            </div>
        </div>
        @endif-->
    @if(!empty($assignedMksWtArr))
    @if(!empty($subEventArr))
    <div class="col-md-8 margin-top-10">
        <span class="label label-md bold label-blue-steel">
            @lang('label.TOTAL_SUB_EVENT'):&nbsp;{!! sizeof($subEventArr) !!}
        </span>&nbsp;
        <span class="label label-md bold label-green-soft">
            @lang('label.TOTAL_EVENT_WT'):&nbsp;{!! $assignedMksWtArr->wt ?? '0.00' !!}
            {!! Form::hidden('total_event_wt', !empty($assignedMksWtArr->wt) ? $assignedMksWtArr->wt : null,['id' => 'totalEventWt'] )!!}
        </span>
    </div>
    @if(empty($eventAssessmentMarkingData))
    <div class="col-md-4 margin-top-5 text-right">
        <button class="btn green btn-danger tooltips" type="button" id="buttonDelete" >
            <i class="fa fa-trash"></i> &nbsp;@lang('label.DELETE_MKS_WT')
        </button>
    </div>
    @endif

    <div class="col-md-12 margin-top-10">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>

                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="vcenter">@lang('label.SUB_EVENT')</th>
                    <th class="text-center vcenter">@lang('label.MKS')</th>
                    <th class="text-center vcenter">@lang('label.HIGHEST')</th>
                    <th class="text-center vcenter">@lang('label.LOWEST')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>

                </tr>
            </thead>

            <tbody>
                <?php
                $i = 0;
                ?>
                @foreach($subEventArr as $subEventId => $subEventName)
                <?php
                $readOnly = '';
                if (!empty($eventAssessmentMarkingData)) {
                    if (array_key_exists($subEventId, $eventAssessmentMarkingData)) {
                        $readOnly = !empty($eventAssessmentMarkingData) ? 'readonly' : '';
                    }
                }

                $eventMksLimit = !empty($subEventMksWtArr[$subEventId]['mks_limit']) ? $subEventMksWtArr[$subEventId]['mks_limit'] : (!empty($subEventMksWtArr['mks_limit']) ? $subEventMksWtArr['mks_limit'] : null);
                $eventHighestMksLimit = !empty($subEventMksWtArr[$subEventId]['highest_mks_limit']) ? $subEventMksWtArr[$subEventId]['highest_mks_limit'] : (!empty($subEventMksWtArr['highest_mks_limit']) ? $subEventMksWtArr['highest_mks_limit'] : null);
                $eventLowestMksLimit = !empty($subEventMksWtArr[$subEventId]['lowest_mks_limit']) ? $subEventMksWtArr[$subEventId]['lowest_mks_limit'] : (!empty($subEventMksWtArr['lowest_mks_limit']) ? $subEventMksWtArr['lowest_mks_limit'] : null);
                $eventWt = !empty($subEventMksWtArr[$subEventId]['wt']) ? $subEventMksWtArr[$subEventId]['wt'] : null;
                ?>
                <tr>
                    <td class="vcenter text-center">{!! ++$i !!}</td>
                    <td class="vcenter">{!! $subEventName ?? '' !!}</td>
                    <td class="vcenter width-80">
                        {!! Form::text('event_mks_wt['.$subEventId.'][mks]', $eventMksLimit,['id' => 'mksLimit_'.$subEventId, 'data-key' => $subEventId, 'class' => 'mks-limit form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readOnly]) !!}
                    </td>
                    <td class="vcenter width-80">
                        {!! Form::text('event_mks_wt['.$subEventId.'][highest]', $eventHighestMksLimit,['id' => 'highestLimit_'.$subEventId, 'data-key' => $subEventId, 'class' => 'highest-mks form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readOnly]) !!}
                    </td>
                    <td class="vcenter width-80">
                        {!! Form::text('event_mks_wt['.$subEventId.'][lowest]', $eventLowestMksLimit,['id' => 'lowestLimit_'.$subEventId, 'data-key' => $subEventId, 'class' => 'lowest-limit form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readOnly]) !!}
                    </td>
                    <td class="vcenter width-80">
                        {!! Form::text('event_mks_wt['.$subEventId.'][wt]', $eventWt,['id' => 'wt_'.$subEventId, 'data-key' => $subEventId, 'class' => 'wt-distributed form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readOnly]) !!}
                    </td>
                </tr>
                @endforeach
                {!! Form::hidden('total_wt', !empty($total) ? $total : null,['id' => 'totalWt']) !!}
                <tr>
                    <td class="vcenter text-right bold" colspan="5">@lang('label.TOTAL')</td>
                    <td class="vcenter text-right">
                        <span class="total-wt bold">{!! !empty($total) ? Helper::numberFormat2Digit($total) : '' !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-12 text-center">
        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
            <i class="fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href="{{ URL::to('subEventMksWt') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_SUB_EVENT_IS_ASSIGNED_TO_THIS_EVENT') !!}</strong></p>
        </div>
    </div>
    @endif
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.EVENT_WT_IS_NOT_ASSIGNED_YET') !!}</strong></p>
        </div>
    </div>
    @endif
</div>
<script src="{{asset('public/js/custom.js')}}"></script>
<script>
$(document).ready(function () {

    $(document).on('keyup', '.wt-distributed', function () {
        total();
        var key = $(this).attr('data-key');
        var totalAssignedWt = parseFloat($("#totalEventWt").val());
        var totalGivenWt = parseFloat($(".total-wt").text());
        if (totalGivenWt == '' || isNaN(totalGivenWt)) {
            totalGivenWt = 0;
        }
        if (totalGivenWt > totalAssignedWt) {
            swal({
                title: '@lang("label.TOTAL_EVENT_WT_EXCEEDED_FROM_ASSIGNED_TOTAL_EVENT_WT")',

                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $('#wt_' + key).val('');
                setTimeout(function () {
                    $('#wt_' + key).focus();
                }, 250);
                total();
                return false;
            });

        }
    });

    //start: highest limit can't exceed event mks limit
    $(document).on('keyup', '.mks-limit', function () {
        var key = $(this).attr('data-key');
        var eventMksLimit = parseFloat($(this).val());
        var highestMksLimit = parseFloat($("#highestLimit_" + key).val());
        if (highestMksLimit == '' || isNaN(highestMksLimit)) {
            highestMksLimit = 0;
        }

        if (eventMksLimit < highestMksLimit) {
            swal({
                title: '@lang("label.HIGHEST_LIMIT_CAN_NOT_EXCEED_EVENT_MKS_LIMIT")',

                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                closeOnConfirm: true,
            }, function (isConfirm) {
                $('#mksLimit_' + key).val('');
                $('#highestLimit_' + key).val('');
                $('#lowestLimit_' + key).val('');
                setTimeout(function () {
                    $('#mksLimit_' + key).focus();
                }, 250);
                return false;
            });

        }
    });

    $(document).on('keyup', '.highest-mks', function () {

        var key = $(this).attr('data-key');
        var eventMksLimit = parseFloat($("#mksLimit_" + key).val());
        var highestMksLimit = parseFloat($(this).val());
        var lowestMksLimit = parseFloat($('#lowestLimit_' + key).val());
        if (highestMksLimit == '' || isNaN(highestMksLimit)) {
            highestMksLimit = 0;
        }
        if (eventMksLimit < highestMksLimit) {
            swal({
                title: "@lang('label.HIGHEST_LIMIT_CAN_NOT_EXCEED_EVENT_MKS_LIMIT') ",
                text: "",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('label.OK')",
                closeOnConfirm: true,
                closeOnCancel: true,
            }, function (isConfirm) {
                $('#highestLimit_' + key).val('');
                $('#lowestLimit_' + key).val('');
                setTimeout(function () {
                    $('#highestLimit_' + key).focus();
                }, 250);
                return false;
            });
        } else if (lowestMksLimit > highestMksLimit) {
            swal({
                title: "@lang('label.LOWEST_LIMIT_CAN_NOT_EXCEED_HIGHEST_LIMIT') ",
                text: "",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('label.OK')",
                closeOnConfirm: true,
                closeOnCancel: true,
            }, function (isConfirm) {
                $('#highestLimit_' + key).val('');
                $('#lowestLimit_' + key).val('');
                setTimeout(function () {
                    $('#highestLimit_' + key).focus();
                }, 250);
                return false;
            });
        }
    });
    //end: highest limit can't exceed event mks limit

    //start: lowest limit can't exceed highest limit
    $(document).on('keyup', '.lowest-limit', function () {
        var key = $(this).attr('data-key');
        var highestMksLimit = parseFloat($("#highestLimit_" + key).val());
        var lowestMksLimit = parseFloat($(this).val());
        if (lowestMksLimit == '' || isNaN(lowestMksLimit)) {
            lowestMksLimit = 0;
        }
        if (highestMksLimit < lowestMksLimit) {
            swal({
                title: "@lang('label.LOWEST_LIMIT_CAN_NOT_EXCEED_HIGHEST_LIMIT') ",
                text: "",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('label.OK')",
                closeOnConfirm: true,
                closeOnCancel: true,
            }, function (isConfirm) {
                $('#lowestLimit_' + key).val('');
                setTimeout(function () {
                    $('#lowestLimit_' + key).focus();
                }, 250);
                return false;
            });
        }
    });
    //end: lowest limit can't exceed highest limit

    function total() {
        var sum = 0;
        $('.wt-distributed').each(function () {
            var wt = $(this).val();
            if (wt == '' || isNaN(wt)) {
                wt = 0;
            }
            sum += parseFloat(wt);
        });
        $("#totalWt").val(sum);
        $('.total-wt').text(sum.toFixed(2));
    }
});
</script>


