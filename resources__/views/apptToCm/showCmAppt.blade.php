
<div class="col-md-12">
    <div class="row">
        @if(!$targetArr->isEmpty())
        @if(!empty($apptList))

        <div class="col-md-12 margin-top-10">
            <span class="label label-success">
                @lang('label.TOTAL_NO_OF_CM'):&nbsp;{!! !empty($targetArr)?sizeof($targetArr):0 !!}
            </span>&nbsp;
            <button class="label label-primary btn-label-groove tooltips" href="#modalAssignedAppt" id="assignedAppt" 
                    data-course-id="{!! !empty($request->course_id) ? $request->course_id : 0 !!}" 
                    data-term-id="{!! !empty($request->term_id) ? $request->term_id : 0 !!}" 
                    data-event-id="{!! !empty($request->event_id) ? $request->event_id : 0 !!}" 
                    data-sub-event-id="{!! !empty($request->sub_event_id) ? $request->sub_event_id : 0 !!}" 
                    data-sub-sub-event-id="{!! !empty($request->sub_sub_event_id) ? $request->sub_sub_event_id : 0 !!}" 
                    data-sub-sub-sub-event-id="{!! !empty($request->sub_sub_sub_event_id) ? $request->sub_sub_sub_event_id : 0 !!}" 
                    data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_APPT')">
                @lang('label.TOTAL_NO_OF_ASSIGNED_APPT'): &nbsp;{!! !empty($apptList)?sizeof($apptList):0 !!}&nbsp; <i class="fa fa-search-plus"></i>
            </button>
        </div>

        <div class="col-md-12 margin-top-10">
            <div class="table-responsive webkit-scrollbar">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th class="vcenter text-center">@lang('label.SL_NO')</th>

<!--                        <th class="vcenter" width="15%">
    <div class="md-checkbox has-success">
        {!! Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check']) !!}
        <label for="checkAll">
            <span class="inc"></span>
            <span class="check mark-caheck"></span>
            <span class="box mark-caheck"></span>
        </label>&nbsp;&nbsp;
        <span class="bold">@lang('label.CHECK_ALL')</span>
    </div>
</th>-->

                            <th class="text-center vcenter">@lang('label.PHOTO')</th>
                            <th class=" vcenter">@lang('label.PERSONAL_NO')</th>
                            <th class="vcenter">@lang('label.RANK')</th>
                            <th class=" vcenter">@lang('label.FULL_NAME')</th>
                            <th class=" vcenter">@lang('label.ASSIGNED_SYN')</th>
                            <th class=" vcenter">@lang('label.APPT')</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php $sl = 0; ?>
                        @foreach($targetArr as $target)


                        <?php
                        $checked = '';
                        $selectedAppt = null;
                        $disabled = 'disabled';
                        $title = __('label.CHECK');
                        if (!empty($previousDataList) && array_key_exists($target->id, $previousDataList)) {
                            $selectedAppt = $previousDataList[$target->id];
                            $checked = 'checked';
                            $disabled = '';
                            $title = __('label.UNCHECK');
                        }
                        ?>

                        <tr>
                            <td class="vcenter text-center">{!! ++$sl !!}</td>
    <!--                        <td class="vcenter">
                                <div class="md-checkbox has-success tooltips" title="" >
                                    {!! Form::checkbox('cm_id['.$target->id.']',$target->id, $checked, ['id' => $target->id, 'data-id'=>$target->id, 'class'=> 'md-check appt-to-cm']) !!}
                                    <label for="{!! $target->id !!}">
                                        <span class="inc"></span>
                                        <span class="check mark-caheck tooltips" title="{{$title}}"></span>
                                        <span class="box mark-caheck tooltips" title="{{$title}}"></span>
                                    </label>
                                </div>
                            </td>-->
                            <td class="text-center vcenter" width="50px">
                                <?php if (!empty($target->photo && File::exists('public/uploads/cm/' . $target->photo))) { ?>
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target->photo}}" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                                <?php } else { ?>
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                                <?php } ?>
                            </td>
                            <td class="vcenter">{!! $target->personal_no !!}</td>
                            <td class="vcenter">{!! !empty($target->rank_code) ? $target->rank_code : '' !!} </td>
                            <td class="vcenter">{!! Common::getFurnishedCmName($target->full_name)!!}</td>
                            <td class="vcenter">{!! !empty($target->syn_name) ? $target->syn_name : '' !!}</td>
                            {!! Form::hidden('syn_id['.$target->id.']', $target->syn_id) !!}
                            <td class="vcenter width-200">
                                <select class="form-control width-inherit js-source-states  appt-select appt-select-syn-{{$target->syn_id}}  appt-select-{{$target->id}}" 
                                        name="appt_id[{!! $target->id !!}]" id="apptId_{!! $target->id !!}" data-syn-id="{!! $target->syn_id !!}" data-id="{{$target->id}}">
                                    @foreach($apptList as $apptId => $info)
                                    <?php $optionDisabled = !empty($previousDataOptionDisabledList[$target->syn_id][$target->id]) && in_array($apptId, $previousDataOptionDisabledList[$target->syn_id][$target->id]) ? 'disabled' : ''; ?>
                                    <option value="{!! $apptId !!}" id="{!! $target->id.'_'.$apptId !!}" {{$optionDisabled}}
                                            data-cm-id="{!! $target->id !!}"  data-option-id="{!! $apptId !!}" 
                                            data-unique="{!! $info['is_unique'] !!}"  data-syn-id="{!! $target->syn_id !!}"
                                            <?php
                                            if ($selectedAppt == $apptId) {
                                                echo 'selected="selected"';
                                            } else {
                                                echo '';
                                            }
                                            ?>>{!! $info['name'] !!} 
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href="{{ URL::to('apptToCm') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.APPT_MATRIX_IS_NOT_SET_YET') !!}</strong></p>
            </div>
        </div>
        @endif
        @else
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_ASSIGNED_TO_THIS_GROUP') !!}</strong></p>
            </div>
        </div>
        @endif
    </div>
</div>
<!-- Modal end-->
<script type="text/javascript">
    //    CHECK ALL
    $(document).ready(function () {

<?php
if (!$targetArr->isEmpty()) {
    if (!empty($apptList)) {
        ?>

                //            $('#dataTable').dataTable({
                //                "paging": true,
                //                "info": false,
                //                "order": false
                //            });

                //                $('#checkAll').change(function () {  //'check all' change
                //                    $('.appt-to-cm').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                //                    $('.appt-select').prop('disabled', !$(this).prop('checked')); //change all 'checkbox' checked status
                //                });
                //
                //                $('.appt-to-cm').change(function () {
                //                    var key = $(this).attr('data-id');
                //                    if (this.checked == false) { //if this item is unchecked
                //                        $('#checkAll').prop('checked', false); //change 'check all' checked status to false
                //                        $('.appt-select-' + key).prop('disabled', true);
                //                    } else {
                //                        $('.appt-select-' + key).prop('disabled', false);
                //                    }
                //                    //check 'check all' if all checkbox items are checked
                //                    allCheck();
                //                });
                //                allCheck();

                $(document).on('change', '.appt-select', function () {
                    var selections = [];
                    var synId = $(this).attr('data-syn-id');

                    $('select.appt-select-syn-' + synId + ' option:selected').each(function () {
                        var optionId = $(this).attr('data-option-id');
                        selections.push(optionId);
                    });

                    $('select.appt-select-syn-' + synId + ' option').each(function () {
                        var isUnique = $(this).attr('data-unique');
                        $(this).prop('disabled', $.inArray($(this).val(), selections) > -1 && !$(this).is(":selected") && isUnique == '1');

                    });
                    $('select.js-source-states').select2();


                });
        <?php
    }
}
?>
    });


    function allCheck() {

        if ($('.appt-to-cm:checked').length == $('.appt-to-cm').length) {
            $('#checkAll').prop('checked', true); //change 'check all' checked status to true
        } else {
            $('#checkAll').prop('checked', false);
        }
    }

</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

