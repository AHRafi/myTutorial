@if (!$targetArr->isEmpty())

<div class = "form-group">
    <label class = "control-label col-md-4" for = "moduleId">@lang('label.CHOOSE_SUB_SYN') :<span class = "text-danger"> *</span></label>
    <div class = "col-md-4 margin-top-8">
        <?php
        //disable
        $disabledCAll = '';
        if (!empty($cmToSynDataArr)) {
            $disabledCAll = 'disabled';
        }
        ?>
        <div class="md-checkbox">
            <!--{!! Form::checkbox('check_all',1,false,['id' => 'checkedAll','class'=> 'md-check', $disabledCAll]) !!}--> 
            <input type="checkbox" id="checkedAll" class="md-check" {{ $disabledCAll }}>
            <label for="checkedAll">
                <span></span>
                <span class="check"></span>
                <span class="box"></span>
            </label>
            <span class="bold">@lang('label.CHECK_ALL')</span>
        </div>
        <div class="form-group form-md-line-input table-responsive max-height-300 webkit-scrollbar">
            <div class="col-md-10">
                <div class="md-checkbox-list">
                    @foreach($targetArr as $key=>$item)
                    <?php
                    $checked = '';
                    $disabled = '';
                    if (!empty($cmToSynDataArr)) {
                        $disabled = in_array($item->id, $cmToSynDataArr) ? 'disabled' : '';
                    }
                    $title = __('label.CHECK');
                    if (!empty($previousDataList[$item->id])) {
                        if (in_array($previousDataList[$item->id], $previousDataList)) {
                            $checked = 'checked';
                            $synName = $item->name;
                            if (in_array($item->id, $cmToSynDataArr)) {
                                $title = __('label.CM_IS_ALREADY_ASSIGNED_TO_SYN', ['synName' => $synName]);
                            } else {
                                $title = __('label.UNCHECK');
                            }
                        }
                    }
                    ?>
                    <div class="md-checkbox">
                        {!! Form::checkbox('sub_syn_id['.$item->id.']',$item->id, false, ['id' => $item->id, 'data-id'=>$item->id,'class'=> 'md-check syn-to-course-check', $checked, $disabled]) !!}
                        @if(!empty($disableSubSyn))
                        @foreach($disableSubSyn as $key=>$value )
                        {!! Form::hidden('sub_syn_id['.$key.']', $key) !!}
                        @endforeach
                        @endif
                        @if(!empty($disabled))
                        {!! Form::hidden('sub_syn_id['.$item->id.']', $item->id) !!}
                        @endif
                        <span class = "text-danger">{{ $errors->first('sub_syn_id') }}</span>
                        <label for="{{$item->id}}">
                            <span></span>
                            <span class="check tooltips" title="{{$title}}"></span>
                            <span class="box tooltips" title="{{$title}}"></span>{{$item->name}}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<div class = "form-actions">
    <div class = "col-md-offset-4 col-md-8">
        <button class = "button-submit btn btn-circle green" type="button">
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href = "{{ URL::to('synToSubSyn') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_MODULE_FOUND')</p>
    </div>
</div>
@endif
<script type="text/javascript">
//    CHECK ALL
    $(document).ready(function () {
        // this code for  database 'check all' if all checkbox items are checked
        if ($('.syn-to-course-check:checked').length == $('.syn-to-course-check').length) {
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

        $('.syn-to-course-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.syn-to-course-check:checked').length == $('.syn-to-course-check').length) {
                $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
            }
        });

    });
//    CHECK ALL
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>