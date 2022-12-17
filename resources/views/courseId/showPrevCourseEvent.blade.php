<div class="row margin-top-10">
    @if (!$eventInfo->isEmpty())
    <div class="col-md-12">
        <span class="label label-sm label-blue-steel">
            @lang('label.TOTAL_NO_OF_EVENTS'):&nbsp;{!! !$eventInfo->isEmpty() ? sizeOf($eventInfo) : 0 !!}
        </span>
    </div>
    <div class="col-md-12 margin-top-10">
        <div class="table-responsive max-height-500 webkit-scrollbar">
            <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                <thead>
                    <tr>
                        <th class="text-center vcenter">@lang('label.SL_NO')</th>
                        <th class="vcenter" width="15%">

                            <div class="md-checkbox has-success">
                                {!! Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check']) !!}
                                <label for="checkAll">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>&nbsp;&nbsp;
                                <span class="bold">@lang('label.CHECK_ALL')</span>
                            </div>
                        </th>
                        <th class="text-center vcenter">@lang('label.EVENT_CODE')</th>
                        <th class="text-center vcenter">@lang('label.HAS_SUB_EVENT')</th>
                        <th class="text-center vcenter">@lang('label.HAS_DS_ASSESMENT')</th>
                        <th class="text-center vcenter">@lang('label.HAS_GROUP_CLONING')</th>
                        <th class="text-center vcenter">@lang('label.FOR_MA_GROUPING')</th>
                    </tr>
                </thead>
                <tbody>

                    @php $sl = 0; @endphp
                    @foreach($eventInfo as $eInfo)
                    <tr>
                        <td class="text-center vcenter">{!! ++$sl !!}</td>
                        <td class="vcenter">
                            <div class="md-checkbox has-success tooltips" >
                                {!! Form::checkbox('event['.$eInfo->id.']',$eInfo->id, null, ['id' => $eInfo->id, 'data-id'=>$eInfo->id, 'class'=> 'md-check event']) !!}  
                                <label for="{!! $eInfo->id !!}">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck tooltips" title=""></span>
                                    <span class="box mark-caheck tooltips" title=""></span>
                                </label>
                            </div>
                        </td>
                        <td class="vcenter">{!! $eInfo->event_code!!}</td>
                        <td class="text-center vcenter">
                            @if($eInfo->has_sub_event == '1')
                            <span class="label label-sm label-success">@lang('label.YES')</span>
                            @else
                            <span class="label label-sm label-warning">@lang('label.NO')</span>
                            @endif
                        </td>
                        <td class="text-center vcenter">
                            @if($eInfo->has_ds_assesment == '1')
                            <span class="label label-sm label-success">@lang('label.YES')</span>
                            @else
                            <span class="label label-sm label-warning">@lang('label.NO')</span>
                            @endif
                        </td>
                        <td class="text-center vcenter">
                            @if($eInfo->has_group_cloning == '1')
                            <span class="label label-sm label-success">@lang('label.YES')</span>
                            @else
                            <span class="label label-sm label-warning">@lang('label.NO')</span>
                            @endif
                        </td>
                        <td class="text-center vcenter">
                            @if($eInfo->for_ma_grouping == '1')
                            <span class="label label-sm label-success">@lang('label.YES')</span>
                            @else
                            <span class="label label-sm label-warning">@lang('label.NO')</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_EVENT_FOUND')</p>
        </div>
    </div>
    @endif
</div>
<!-- if submit wt chack End -->

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
//   Start: CHECK ALL
    $(document).ready(function () {
        $("#dataTable").tableHeadFixer();
        //'check all' change
        $(document).on('click', '#checkAll', function () {
            if ($('#checkAll').is(':checked')) {
                $('.event').each(function () {
                    if (this.checked == false) {
                        var key = $(this).attr('data-id');
                        $(this).prop('checked', true);
                    }
                });
            } else {
                $(".event").removeAttr('checked');
            }
        });

        $(document).on('click', '.event', function () {
            allCheck();
        });
<?php if (!$eventInfo->isEmpty()) { ?>
            allCheck();
<?php } ?>
    });

    function allCheck() {

        if ($('.event:checked').length == $('.event').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
// End:  CHECK ALL

</script>