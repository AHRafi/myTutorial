<div class="row">
    @if(!$cmArr->isEmpty())
    <div class="col-md-12 margin-top-10">
        <span class="label label-md bold label-blue-steel">
            @lang('label.TOTAL_NO_OF_CM'):&nbsp;{!! count($cmArr) !!}
        </span>&nbsp;
        <span class="label label-md bold label-green-seagreen">
            @lang('label.TOTAL_MKS'):&nbsp;{!! !empty($eventMksWtInfo) ? $eventMksWtInfo->mks : '' !!}
        </span>
        </span>&nbsp;
        <span class="label label-md bold label-purple-sharp">
            @lang('label.TOTAL_WT'):&nbsp;{!! !empty($eventMksWtInfo) ? $eventMksWtInfo->wt : '' !!}
        </span>
    </div>
    <div class="col-md-12 margin-top-10">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="text-center vcenter">@lang('label.NAME')</th>
                    <th class="text-center vcenter">@lang('label.MKS'){!! !empty($eventMksWtInfo) ? '('.$eventMksWtInfo->mks.')' : '' !!}</th>
                    <th class="text-center vcenter">@lang('label.WT'){!! !empty($eventMksWtInfo) ? '('.$eventMksWtInfo->wt.')' : '' !!}</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $sl = 0;
                if (!$eventAssessmentMarkingArr->isEmpty()) {
                    $mks = $wt = [];
                    foreach ($eventAssessmentMarkingArr as $eamInfo) {
                        $mks[$eamInfo->cm_id] = $eamInfo->mks;
                        $wt[$eamInfo->cm_id] = $eamInfo->wt;
                    }
                }
                if (!empty($eventAssessmentMarkingLockInfo)) {
                    $readOnly = 'readonly';
                }
                ?>
                @foreach($cmArr as $stdInfo)
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="text-left">{!! Common::getFurnishedCmName($stdInfo->full_name) !!}</td>
                    {!! Form::hidden('cm_name['.$stdInfo->id.']', Common::getFurnishedCmName($stdInfo->full_name),['id' => 'cmId'])!!}
                    <td class="text-center vcenter width-200">
                        {!! Form::text('mks_wt['.$stdInfo->id.'][mks]',!empty($mks[$stdInfo->id]) ? $mks[$stdInfo->id] : null, ['id'=> 'mksId_'.$stdInfo->id, 'class' => 'form-control integer-decimal-only text-inherit text-right given-mks', 'data-key' => $stdInfo->id, 'autocomplete' => 'off',!empty($readOnly)?$readOnly:'']) !!}
                    </td>
                    <td class="text-center vcenter width-200">
                        {!! Form::text('mks_wt['.$stdInfo->id.'][wt]',!empty($wt[$stdInfo->id]) ? $wt[$stdInfo->id] : null, ['id'=> 'wtId_'.$stdInfo->id, 'class' => 'form-control integer-decimal-only text-inherit text-right given-wt', 'data-key' => $stdInfo->id, 'autocomplete' => 'off','readonly']) !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {!! Form::hidden('assigned_mks', !empty($eventMksWtInfo) ? $eventMksWtInfo->mks : null,['id' => 'assignedMksId'])!!}
        {!! Form::hidden('assigned_wt', !empty($eventMksWtInfo) ? $eventMksWtInfo->wt : null,['id' => 'assignedWtId'])!!}

        <div class="form-actions">
            <div class="row">
                @if(!empty($eventAssessmentMarkingLockInfo))
                @if($eventAssessmentMarkingLockInfo['status'] == '1')
<!--                <div class="col-md-offset-5 col-md-5">
                    <button class="btn btn-circle label-purple-sharp request-to-unlock" type="button" id="buttonSubmitLock" >
                        <i class="fa fa-unlock"></i> @lang('label.REQUEST_TO_UNLOCK')
                    </button>
                </div>-->
                @elseif($eventAssessmentMarkingLockInfo['status'] == '2')
<!--                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissable">
                        <p><strong><i class="fa fa-unlock"></i> {!! __('label.REQUESTED_TO_OIC_FOR_UNLOCK') !!}</strong></p>
                    </div>
                </div>-->
                @endif
                @else
                <div class="col-md-offset-5 col-md-5">
                    <button class="btn btn-circle label-blue-steel button-submit" data-id="1" type="button" id="buttonSubmit" >
                        <i class="fa fa-file-text-o"></i> @lang('label.SAVE_AS_DRAFT')
                    </button>
                    <button class="btn btn-circle green button-submit" data-id="2" type="button" id="buttonSubmitLock" >
                        <i class="fa fa-lock"></i> @lang('label.SAVE_LOCK')
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_FOUND') !!}</strong></p>
        </div>
    </div>
    @endif
</div>
<script src="{{asset('public/js/custom.js')}}"></script>
<script>
$(document).ready(function () {
    $(document).on('keyup', '.given-mks', function () {
        var key = $(this).attr('data-key');
        var givenMks = $("#mksId_" + key).val();
        var assignedMks = $("#assignedMksId").val();
        var assignedWt = $("#assignedWtId").val();
        if (givenMks > assignedMks) {
            swal({
                title: '@lang("label.YOUR_GIVEN_MARKS_EXCEEDED_FROM_ASSIGNED_MARKS")',
                   
                //showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ok',
                //cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                //closeOnCancel: true
            }, function (isConfirm) {
                $("#mksId_" + key).val('');
                $("#mksId_" + key).focus();
                return false;
            });
        } else {
            var wt = parseFloat((Number(assignedWt) / Number(assignedMks)) * Number(givenMks)).toFixed(2)
            $("#wtId_" + key).val(wt);
        }

    });
});
</script>


