@if (!$targetArr->isEmpty())
<div class="row">
    <div class="col-md-12">
        <span class="label label-sm label-blue-steel">
            @lang('label.TOTAL_NO_OF_SUB_SUB_EVENTS'):&nbsp;{!! !$targetArr->isEmpty() ? sizeOf($targetArr) : 0 !!}
        </span>&nbsp;
        <button class="label label-sm label-green-seagreen btn-label-groove tooltips" href="#modalAssignedSubSubEvent" id="assignedSubSubEvent"  data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_SUB_SUB_EVENT')">
            @lang('label.SUB_SUB_EVENT_ASSIGNED_TO_THIS_EVENT'):&nbsp;{!! !$prevDataArr->isEmpty() ? sizeOf($prevDataArr) : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>
        </button>
    </div>
</div>

<div class="row margin-top-10">
    <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-hover" id="dataTable">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="vcenter" width="15%">
                        <?php
                        //disable
                        $disabledCAll = '';
                        if (!empty($termToSubSubEventDataArr)) {
                            $disabledCAll = 'disabled';
                        }
                        if (!empty($markingGroupDataArr)) {
                            $disabledCAll = 'disabled';
                        }
                        ?>
                        <div class="md-checkbox has-success">
                            {!! Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check', $disabledCAll]) !!}
                            <label for="checkAll">
                                <span class="inc"></span>
                                <span class="check mark-caheck"></span>
                                <span class="box mark-caheck"></span>
                            </label>&nbsp;&nbsp;
                            <span class="bold">@lang('label.CHECK_ALL')</span>
                        </div>
                    </th>
                    <th class="vcenter">@lang('label.SUB_SUB_EVENT')</th>
                    <th class="vcenter">@lang('label.HAS_SUB_SUB_SUB_EVENT')</th>
                    <th class="vcenter {{ (!empty($prevDsAssesment->has_ds_assesment)) || (!empty($prevDsAssesment1->has_ds_assesment)) ? 'display-none' : '' }}">
                        @lang('label.HAS_DS_ASSESMENT')
                    </th>
                </tr>
            </thead>
            <tbody>
                @php $sl = 0; @endphp
                @foreach($targetArr as $target)
                <?php
                $disabled = '';
                if (!empty($termToSubSubEventDataArr)) {
                    if (in_array($target->id, $termToSubSubEventDataArr)) {
                        $disabled = 'disabled';
                    }
                }
                if (!empty($markingGroupDataArr)) {
                    if (in_array($target->id, $markingGroupDataArr)) {
                        $disabled = 'disabled';
                    }
                }
                
                $checked = '';
                $title = __('label.CHECK');
                $disabledHasSubSubSubEvent = 'disabled';
                $disabledHasDsAssesment = 'disabled';
                
                if (!empty($prevEventToSubSubEventList)) {
                    if (array_key_exists($target->id, $prevEventToSubSubEventList)) {
                        $checked = 'checked';
                        $eventName = $target->event_code;
                        if (in_array($target->id, $termToSubSubEventDataArr)) {
                            $title = __('label.EVENT_IS_ALREADY_ASSIGNED_TO_TERM', ['eventName' => $eventName]);
                        } elseif (in_array($target->id, $markingGroupDataArr)) {
                            $title = __('label.EVENT_IS_ALREADY_ASSIGNED_TO_MARKING_GROUP', ['eventName' => $eventName]);
                        } else {
                            $title = __('label.UNCHECK');
                        }
                    }
                    
                    $disabledHasSubSubSubEvent = array_key_exists($target->id, $prevEventToSubSubEventList) ? '' : 'disabled';
                    $disabledHasDsAssesment = array_key_exists($target->id, $prevEventToSubSubEventList) ? '' : 'disabled';
                }
                
                $checkedHasSubSubSubEvent = '';
                $checkedHasDsAssesment = '';
                
                if(in_array($target->id, $checkHasDsAssesment)) {
                    $checkedHasDsAssesment = 'checked';
                }
                
                if(in_array($target->id, $checkHasSubSubSubEvent)) {
                    $checkedHasSubSubSubEvent = 'checked';
//                    $disabledHasDsAssesment= 'disabled';
//                    $checkedHasDsAssesment = '';
                }
                
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="vcenter">
                        <div class="md-checkbox has-success tooltips" >
                            {!! Form::checkbox('sub_sub_event_id['.$target->id.']',$target->id, $checked, ['id' => $target->id, 'data-id'=>$target->id,'class'=> 'md-check event-to-sub-sub-event', $disabled]) !!}
                            @if(!empty($disabled))
                            {!! Form::hidden('sub_sub_event_id['.$target->id.']', $target->id) !!}
                            @endif
                            <label for="{!! $target->id !!}">
                                <span class="inc"></span>
                                <span class="check mark-caheck tooltips" title="{{$title}}"></span>
                                <span class="box mark-caheck tooltips" title="{{$title}}"></span>
                            </label>
                        </div>
                    </td>
                    <td class="vcenter">{!! $target->event_code!!}</td>
                    <td class="vcenter">
                        <div class="md-checkbox" >
                            {!! Form::checkbox('has_sub_sub_sub_event['.$target->id.']', 1
                            , $checkedHasSubSubSubEvent
                            , ['id' => 'hasSubSubSubEvent'.$target->id, 'data-id'=>$target->id
                            ,'class'=> 'md-check has-checked has-sub-sub-sub-event has-sub-sub-sub-event-'.$target->id, $disabledHasSubSubSubEvent, $disabled]) !!}
                            @if(!empty($disabled))
                            {!! Form::hidden('has_sub_sub_sub_event['.$target->id.']', (!empty($checkedHasSubSubSubEvent) ? 1 : 0)) !!}
                            @endif
                            <label for="hasSubSubSubEvent{!! $target->id !!}">
                                <span class="inc"></span>
                                <span class="check mark-caheck tooltips"></span>
                                <span class="box mark-caheck tooltips"></span>
                            </label>
                        </div>
                    </td>
                    <td class="vcenter {{ (!empty($prevDsAssesment->has_ds_assesment)) || (!empty($prevDsAssesment1->has_ds_assesment)) ? 'display-none' : '' }}">
                        <div class="md-checkbox" >
                            {!! Form::checkbox('has_ds_assesment['.$target->id.']', 1
                            , $checkedHasDsAssesment
                            , ['id' => 'hasDsAssesment'.$target->id, 'data-id'=>$target->id
                            ,'class'=> 'md-check has-checked has-ds-assesment has-ds-assesment-'.$target->id, $disabledHasDsAssesment]) !!}
                            <label for="hasDsAssesment{!! $target->id !!}">
                                <span class="inc"></span>
                                <span class="check mark-caheck tooltips"></span>
                                <span class="box mark-caheck tooltips"></span>
                            </label>
                        </div>
                    </td>
                    
                    
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- if submit wt chack Start -->
<div class = "form-actions">
    <div class = "col-md-offset-4 col-md-8">
        <button class = "button-submit btn btn-circle green" type="button">
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href = "{{ URL::to('eventToSubSubEvent') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>

@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_SUB_SUB_EVENT_FOUND_FOR_THIS_EVENT')</p>
    </div>
</div>
@endif
<!-- if submit wt chack End -->

<script type="text/javascript">
//   Start: CHECK ALL
    $(document).ready(function () {
        //'check all' change
        $(document).on('click', '#checkAll', function () {
            if ($('#checkAll').is(':checked')) {
                $('.event-to-sub-sub-event').each(function () {
                    if (this.checked == false) {
                        var key = $(this).attr('data-id');
                        $(this).prop('checked', true);
                        $(".has-sub-sub-sub-event-" + key).prop('disabled', false);
                        $(".has-ds-assesment-" + key).prop('disabled', false);
                        $(".has-ds-assesment-" + key).prop('checked', true);
                    }
                });
            } else {
                $(".event-to-sub-sub-event").removeAttr('checked');
                $(".has-checked").attr('disabled', 'disabled');
                $(".has-checked").removeAttr('checked');
            }
        });

        $(document).on('click', '.event-to-sub-sub-event', function () {
            allCheck();
        });
<?php if (!$targetArr->isEmpty()) { ?>
            allCheck();
<?php } ?>
    });

    function allCheck() {

        if ($('.event-to-sub-sub-event:checked').length == $('.event-to-sub-sub-event').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
//    End: CHECK ALL


//  Start: checked & disable

    $(document).on('click', '.event-to-sub-sub-event', function () {
        var key = $(this).attr('data-id');
        if (this.checked) {
            $(".has-sub-sub-sub-event-" + key).removeAttr('disabled');
            $(".has-ds-assesment-" + key).removeAttr('disabled');
            $(".has-ds-assesment-" + key).prop('checked', true);
        } else {
            $(".has-sub-sub-sub-event-" + key).attr('disabled', 'disabled');
            $(".has-sub-sub-sub-event-" + key).removeAttr('checked');
            $(".has-ds-assesment-" + key).attr('disabled', 'disabled');
            $(".has-ds-assesment-" + key).removeAttr('checked');
        }
    });
    
    $(document).on('click', '.has-sub-sub-sub-event', function () {
        var key = $(this).attr('data-id');
        if (this.checked == false) {
            $(".has-ds-assesment-" + key).prop('checked', true);
        }
    });
    
    $(document).on('click', '.has-ds-assesment', function () {
        var key = $(this).attr('data-id');
        if (this.checked == false && $(".has-sub-sub-sub-event-" + key).prop('checked') == false) {
            swal("@lang('label.SUB_SUB_EVENT_WITHOUT_CHILD_MUST_HAVE_DS_ASSESSMENT_GROUP')");
            return false;
        }
    });
//  End: checked & disable

</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>