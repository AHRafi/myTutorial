
<div class="row">
    @if(!$targetArr->isEmpty())
    <div class="col-md-12 margin-top-10">
        <span class="label label-success">
            @lang('label.TOTAL_NO_OF_CM_ASSIGNED_TO_THIS_SYN'):&nbsp;{!! !empty($targetArr)?sizeof($targetArr):0 !!}
        </span>&nbsp;

        <button class="label label-primary tooltips" href="#modalAssignedCm" id="assignedCm"  data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_CM')">
            @lang('label.TOTAL_NO_OF_CM_ASSIGNED_TO_THIS_SUB_SYN'): &nbsp;{!! $totalNumOfAssignedCm !!}&nbsp; <i class="fa fa-search-plus"></i>
        </button>
    </div>
    <div class="col-md-12 margin-top-10">
        <table class="table table-bordered table-hover" id="dataTable">
            <thead>
                <tr>
                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                    <th class="vcenter" width="15%">
                        <div class="md-checkbox has-success tooltips" title="@lang('label.SELECT_ALL')">
                            {!! Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check']) !!}
                            <label for="checkAll">
                                <span class="inc"></span>
                                <span class="check mark-caheck"></span>
                                <span class="box mark-caheck"></span>
                            </label>&nbsp;&nbsp;
                            <span class="bold">@lang('label.CHECK_ALL')</span>
                        </div>
                    </th>
                    <th class="text-center vcenter">@lang('label.PHOTO')</th>
                    <th class=" vcenter">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter">@lang('label.RANK')</th>
                    <th class=" vcenter">@lang('label.FULL_NAME')</th>
                    <th class=" vcenter">@lang('label.WING')</th>
                    <th class=" vcenter">@lang('label.ASSIGNED_TO')</th>
                </tr>
            </thead>
            <tbody>

                <?php $sl = 0; ?>
                @foreach($targetArr as $target)
                <?php
                $checked = '';
                $disabled = '';
                $title = '';
                $class = 'cm-to-sub-syn';
                if (!empty($previousCmToSubSynList)) {
                    $checked = array_key_exists($target->id, $previousCmToSubSynList) ? 'checked' : '';

                    if (!empty($checkPreviousDataList[$target->id]) && ($request->sub_syn_id != $checkPreviousDataList[$target->id])) {
                        $disabled = 'disabled';
                        $class = '';
                        $subSyn = !empty($checkPreviousDataList[$target->id]) && !empty($subSynDataList[$checkPreviousDataList[$target->id]]) ? $subSynDataList[$checkPreviousDataList[$target->id]] : '';
                        $title = __('label.ALREADY_ASSIGNED_TO_THIS_SUB_SYN', ['subSyn' => $subSyn]);
                    }
                }
                ?>
                <tr>
                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                    <td class="vcenter">
                        <div class="md-checkbox has-success tooltips" title="{!! $title !!}" >
                            {!! Form::checkbox('cm_id['.$target->id.']',$target->id,$checked, ['id' => $target->id, 'class'=> 'md-check '.$class, $disabled]) !!}
                            <label for="{!! $target->id !!}">
                                <span class="inc"></span>
                                <span class="check mark-caheck"></span>
                                <span class="box mark-caheck"></span>
                            </label>
                        </div>
                    </td>
                    <td class="text-center vcenter" width="50px">
                        <?php if (!empty($target->photo && File::exists('public/uploads/cm/' . $target->photo))) { ?>
                            <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target->photo}}" alt="{{ $target->full_name}}"/>
                        <?php } else { ?>
                            <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $target->full_name}}"/>
                        <?php } ?>
                    </td>
                    <td class="vcenter">{!! $target->personal_no !!}</td>
                    <td class="vcenter">{!! !empty($target->rank_code) ? $target->rank_code : '' !!} </td>
                    <td class="vcenter">{!! $target->full_name!!}</td>
                    <td class="vcenter">{!! !empty($target->wing_name) ? $target->wing_name : '' !!}</td>

                    <td class="vcenter">
                        {!! !empty($checkPreviousDataList[$target->id]) && !empty($subSynDataList[$checkPreviousDataList[$target->id]]) ? $subSynDataList[$checkPreviousDataList[$target->id]] : ''!!}
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-12 text-center">
                    <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                    </button>
                    <a href="{{ URL::to('cmToSyn') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                </div>
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
<!-- Modal end-->
<script type="text/javascript">
    //    CHECK ALL
    $(document).ready(function () {
<?php if (!$targetArr->isEmpty()) { ?>
            $('#dataTable').dataTable({
                "paging": true,
                "info": false,
                "order": false
            });
            $('#checkAll').change(function () {  //'check all' change
                $('.cm-to-sub-syn').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
            });
            $('.cm-to-sub-syn').change(function () {
                if (this.checked == false) { //if this item is unchecked
                    $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
                }
                //check 'check all' if all checkbox items are checked
                if ($('.cm-to-sub-syn:checked').length == $('.cm-to-sub-syn').length) {
                    $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
                }
            });

            //'check all' change
            $(document).on('click', '#checkAll', function () {
                if (this.checked) {
                    $('.cm-to-sub-syn').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                } else {
                    $(".cm-to-sub-syn").removeAttr('checked');
                }
            });

            $(document).on('click', '.cm-to-sub-syn', function () {
                allCheck();
            });

                allCheck();
    <?php } ?>
    });


    function allCheck() {

        if ($('.cm-to-sub-syn:checked').length == $('.cm-to-sub-syn').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
//    CHECK ALL
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

