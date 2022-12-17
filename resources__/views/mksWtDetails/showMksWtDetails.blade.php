<div class="row">
    <div class="col-md-12">
        <h5 class="bold">@lang('label.CRITERIA_WISE_WT')</h5>
        @if(!empty($criteriaWiseWtArr))
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
                    <td class="text-left"> {!! $eventCriteriaArr[$sl] !!}</td>
                    <td class="text-right">
                        {!! !empty($criteriaWiseWtArr['written_ass_wt']) ? $criteriaWiseWtArr['written_ass_wt'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left"> {!! $eventCriteriaArr[$sl] !!}</td>
                    <td class="text-right">
                        {!! !empty($criteriaWiseWtArr['outdoor_wt']) ? $criteriaWiseWtArr['outdoor_wt'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left"> {!! $eventCriteriaArr[$sl] !!}</td>
                    <td class="text-right">
                        {!! !empty($criteriaWiseWtArr['obsn_wt']) ? $criteriaWiseWtArr['obsn_wt'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left"> {!! $eventCriteriaArr[$sl] !!}</td>
                    <td class="text-right">
                        {!! !empty($criteriaWiseWtArr['misc_wt']) ? $criteriaWiseWtArr['misc_wt'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($criteriaWiseWtArr['total_wt']) ? $criteriaWiseWtArr['total_wt'] : '' !!}</span>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CRITERIA_WISE_WT_FOUND') !!}</strong></p>
        </div>
    </div>
    @endif
    <div class="col-md-12">
        <h5 class="bold">{!! $eventCriteriaArr[1] !!} @lang('label.WT')</h5>
        @if(!empty($criteriaWiseWtArr))
        <?php
        $totalWrittenWt = !empty($criteriaWiseWtArr->written_ass_wt) ? $criteriaWiseWtArr->written_ass_wt : 0;
        $totalOutdoorWt = !empty($criteriaWiseWtArr->outdoor_wt) ? $criteriaWiseWtArr->outdoor_wt : 0;
        ?>
        {!! Form::hidden('written_assign_wt',$totalWrittenWt) !!}
        {!! Form::hidden('outdoor_event_wt',$totalOutdoorWt) !!}
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="text-center vcenter">@lang('label.EVENT')</th>
                    <th class="text-center vcenter">@lang('label.MKS')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $wrritenAssId = 1;
                $outdoorEventId = 2;
                $sl = 0;
                $totalWrittenAssignmentWt = 0;
                $totalOutdoorEventWt = 0;
                ?>

                @if(!empty($eventArr))

                @foreach($eventArr as $event)
                @if($wrritenAssId == $event->event_criteria_id)
                <?php
                $totalWrittenAssignmentWt += !empty($eventMksWtInfoWtArr[$event->id]) ? $eventMksWtInfoWtArr[$event->id] : 0;
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>

                    <td class="text-left"> {!! $event->name !!}</td>

                    <td class="text-right">
                        {!! !empty($eventMksWtInfoMksArr[$event->id]) ? $eventMksWtInfoMksArr[$event->id] : __('label.N_A') !!}
                    </td>
                    @if( $event->for_entrance == '1' )
                    <td class="text-center">---</td>
                    @else
                    <td class="text-right">
                        {!! !empty($eventMksWtInfoWtArr[$event->id]) ? $eventMksWtInfoWtArr[$event->id] : __('label.N_A') !!}
                    </td>
                    @endif
                </tr>
                @endif
                @endforeach
                <tr>
                    <td class="text-right bold" colspan="3"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($totalWrittenAssignmentWt) ? Helper::numberFormat2Digit($totalWrittenAssignmentWt) : __('label.N_A') !!}</span>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
    </div>
    <div class="col-md-12">
        <h5 class="bold">{!! $eventCriteriaArr[2] !!} @lang('label.WT')</h5>
        @if(!empty($criteriaWiseWtArr))
        <?php
        $totalWrittenWt = !empty($criteriaWiseWtArr->written_ass_wt) ? $criteriaWiseWtArr->written_ass_wt : 0;
        $totalOutdoorWt = !empty($criteriaWiseWtArr->outdoor_wt) ? $criteriaWiseWtArr->outdoor_wt : 0;
        $sl = 0;
        ?>
        {!! Form::hidden('written_assign_wt',$totalWrittenWt) !!}
        {!! Form::hidden('outdoor_event_wt',$totalOutdoorWt) !!}
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="text-center vcenter">@lang('label.EVENT')</th>
                    <th class="text-center vcenter">@lang('label.MKS')</th>
                    <th class="text-center vcenter">@lang('label.WT')</th>
                </tr>
            </thead>

            <tbody>

                @if(!empty($eventArr))
                @foreach($eventArr as $event)
                @if($outdoorEventId == $event->event_criteria_id)
                <?php
                $totalOutdoorEventWt += !empty($eventMksWtInfoWtArr[$event->id]) ? $eventMksWtInfoWtArr[$event->id] : 0;
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>

                    <td class="text-left"> {!! $event->name !!}</td>

                    <td class="text-right">
                        {!! !empty($eventMksWtInfoMksArr[$event->id]) ? $eventMksWtInfoMksArr[$event->id] : __('label.N_A') !!}
                    </td>
                    <td class="text-right">
                        {!! !empty($eventMksWtInfoWtArr[$event->id]) ? $eventMksWtInfoWtArr[$event->id] : __('label.N_A') !!}
                    </td>
                </tr>
                @endif
                @endforeach

                <tr>
                    <td class="text-right bold" colspan="3"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($totalOutdoorEventWt) ? Helper::numberFormat2Digit($totalOutdoorEventWt) : '' !!}</span>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_MKS_WT_FOUND') !!}</strong></p>
        </div>
    </div>
    @endif
    <div class="col-md-12">
        <h5 class="bold">@lang('label.OBSN_WT')</h5>
        @if(!empty($criteriaWiseWtArr))
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
                    <td class="text-right">
                        {!! !empty($obsnWtArr['ds_obsn']) ? $obsnWtArr['ds_obsn'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.OIC_OBSN')</td>
                    <td class="text-right">
                        {!! !empty($obsnWtArr['oic_obsn']) ? $obsnWtArr['oic_obsn'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.CI_OBSN')</td>
                    <td class="text-right">
                        {!! !empty($obsnWtArr['ci_obsn']) ? $obsnWtArr['ci_obsn'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.COMDT_OBSN')</td>
                    <td class="text-right">
                        {!! !empty($obsnWtArr['comdt_obsn']) ? $obsnWtArr['comdt_obsn'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($obsnWtArr['total_wt']) ? $obsnWtArr['total_wt'] : '' !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_OBSN_WT_FOUND') !!}</strong></p>
        </div>
    </div>
    @endif
    <div class="col-md-12">
        <h5 class="bold">@lang('label.MISC_WT')</h5>
        @if(!empty($criteriaWiseWtArr))
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
                    <td class="text-right">
                        {!! !empty($miscWtArr['mutual_assign']) ? $miscWtArr['mutual_assign'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.IPFT_1')</td>
                    <td class="text-center">---</td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.IPFT_2')</td>
                    <td class="text-right">
                        {!! !empty($miscWtArr['ipft_2']) ? $miscWtArr['ipft_2'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.IPFT_3')</td>
                    <td class="text-right">
                        {!! !empty($miscWtArr['ipft_3']) ? $miscWtArr['ipft_3'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($miscWtArr['total_wt']) ? $miscWtArr['total_wt'] : '' !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_MISC_WT_FOUND') !!}</strong></p>
        </div>
    </div>
    @endif

    <div class="col-md-12">
        <h5 class="bold">@lang('label.WARNING_WT')</h5>
        @if(!empty($warningWtArr))
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
                    <td class="text-right">
                        {!! !empty($warningWtArr['oic_warning']) ? $warningWtArr['oic_warning'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.CI_WARNING')</td>
                    <td class="text-right">
                        {!! !empty($warningWtArr['ci_warning']) ? $warningWtArr['ci_warning'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">@lang('label.COMDT_WARNING')</td>
                    <td class="text-right">
                        {!! !empty($warningWtArr['comdt_warning']) ? $warningWtArr['comdt_warning'] : __('label.N_A') !!}
                    </td>
                </tr>
                <tr>
                    <td class=" text-right bold" colspan="2"> @lang('label.TOTAL') </td>
                    <td class="text-right width-200">
                        <span class="total-wt bold">{!! !empty($warningWtArr['total_wt']) ? $warningWtArr['total_wt'] : '' !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_WARNING_WT_FOUND') !!}</strong></p>
        </div>
    </div>
    @endif
</div>

<script>
    $(document).ready(function () {
        $(document).on('keyup', '#dsObsnId', function () {
            var dsObsn = $(this).val();
            if (isNaN(dsObsn)) {
                $("#dsObsnId").val('');
                $("#dsObsnId").focus();
                return false;
            }
            total();
        });
        $(document).on('keyup', '#oicObsnId', function () {
            var oicObsn = $(this).val();
            if (isNaN(oicObsn)) {
                $("#oicObsnId").val('');
                $("#oicObsnId").focus();
                return false;
            }
            total();
        });
        $(document).on('keyup', '#ciObsnId', function () {
            var ciObsn = $(this).val();
            if (isNaN(ciObsn)) {
                $("#ciObsnId").val('');
                $("#ciObsnId").focus();
                return false;
            }
            total();
        });
        $(document).on('keyup', '#comdtObsnId', function () {
            var comdtObsn = $(this).val();
            if (isNaN(comdtObsn)) {
                $("#comdtObsnId").val('');
                $("#comdtObsnId").focus();
                return false;
            }
            total();
        });

        function total() {
            var dsObsn = $('#dsObsnId').val();
            var oicObsn = $('#oicObsnId').val();
            var ciObsn = $('#ciObsnId').val();
            var comdtObsn = $('#comdtObsnId').val();
            //var total = 0;
            var total = parseFloat(Number(dsObsn) + Number(oicObsn) + Number(ciObsn) + Number(comdtObsn)).toFixed(2);
            $(".total-wt").text(total);
            $(".total-wt").val(total);
        }
    });
</script>


