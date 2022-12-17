@if (!$targetArr->isEmpty())
<div class="row">
    <div class="col-md-12 margin-top-10">
        <table class="table table-bordered table-hover" id="dataTable">
            <thead>
                <tr>
                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                    <th class="vcenter" width="15%">
                        <?php
                        //disable
                        $disabledCAll = '';
                        if (!empty($markingGroupDataArr)) {
                            $disabledCAll = 'disabled';
                        }
                        ?>
                        <div class="md-checkbox">
                            <input type="checkbox" id="checkedAll" class="md-check" {{ $disabledCAll }}>
                            <label for="checkedAll">
                                <span></span>
                                <span class="check"></span>
                                <span class="box"></span>
                            </label>
                            <span class="bold">@lang('label.CHECK_ALL')</span>
                        </div>
                    </th>
                    <td class="vcenter">@lang('label.NAME')</td>
                </tr>
            </thead>
            <tbody>
                <?php $sl = 0; ?>
                @foreach($targetArr as $key=>$item)
                <?php
                //disable
                $disabled = '';
                if (!empty($markingGroupDataArr)) {
                    $disabled = in_array($item->id, $markingGroupDataArr) ? 'disabled' : '';
                }
                $checked = '';
                $title = __('label.CHECK');
                if (!empty($previousDataList[$item->id])) {
                    if (in_array($previousDataList[$item->id], $previousDataList)) {
                        $checked = 'checked';
                        $groupNmae = $item->name;
                        if (in_array($item->id, $markingGroupDataArr)) {
                            $title = __('label.GROUP_IS_ALREADY_ASSIGNED_TO_MARKING_GROUP', ['groupName' => $groupNmae]);
                        } else {
                            $title = __('label.UNCHECK');
                        }
                    }
                }
                ?>
                <tr>
                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                    <td class="vcenter">
                        <div class="md-checkbox">
                            {!! Form::checkbox('event_group_id['.$item->id.']',$item->id, $checked, ['id' => $item->id, 'data-id'=>$item->id,'class'=> 'md-check event-group-to-course-check',$disabled]) !!}
                            
                            @if(!empty($disabled))
                            {!! Form::hidden('event_group_id['.$item->id.']', $item->id) !!}
                            @endif
                            <span class = "text-danger">{{ $errors->first('event_group_id') }}</span>
                            <label for="{{$item->id}}">
                                <span></span>
                                <span class="check tooltips" title="{{$title}}"></span>
                                <span class="box tooltips" title="{{$title}}"></span>
                            </label>
                        </div>
                    </td>
                    <td class="vcenter">{!! $item->name !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class = "form-actions">
    <div class = "col-md-offset-4 col-md-8">
        <button class = "button-submit btn btn-circle green" type="button">
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href = "{{ URL::to('eventToEventGroup') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_EVENT_GROUP_FOUND')</p>
    </div>
</div>
@endif

<script src="{{ asset('public/js/custom.js') }}"></script>
<script type="text/javascript">
//    CHECK ALL
$(document).ready(function () {
<?php if (!$targetArr->isEmpty()) { ?>
        $('#dataTable').dataTable({
            "paging": true,
            "pageLength": 100,
            "info": false,
            "order": false
        });
        // this code for  database 'check all' if all checkbox items are checked
        if ($('.event-group-to-course-check:checked').length == $('.event-group-to-course-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        }

        $("#checkedAll").change(function () {
            if (this.checked) {
                $(".md-check").each(function () {
                    if (!this.hasAttribute("disabled")) {
                        this.checked = true;
                    }
                });
            } else {
                $(".md-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.event-group-to-course-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.event-group-to-course-check:checked').length == $('.event-group-to-course-check').length) {
                $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
            }
        });

<?php } ?>
});
//    CHECK ALL
</script>
