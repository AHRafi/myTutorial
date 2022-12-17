<!--<div class="form-group">
    <label class="control-label col-md-4" for="hasApptId">@lang('label.APPT_EXISTS') :<span class="text-danger"> *</span></label>
    <div class = "col-md-4">

<?php
$checked = '';
if (!empty($previousDataArr['appt_exist']) && $previousDataArr['appt_exist'] == '1') {
    $checked = 'checked';
}
?>

        <div class="md-checkbox">
            {!! Form::checkbox('appt_exist', 1 ,false, ['id' => 'hasApptId', 'class'=> 'check', $checked]) !!}
            <label for="hasApptId">
                <span></span>
                <span class="check"></span>
                <span class="box"></span>
            </label>
            <span class="">@lang('label.PUT_TICK_IF_HAS_APPT_MATRIX')</span>
        </div>
    </div>
</div>-->

<div class="col-md-12">
    <!--get  Appt  -->
    <div id="showAppt">
        @if (!$targetArr->isEmpty())
        <div class = "form-group">
            <label class = "control-label col-md-4" for = "moduleId">@lang('label.CHOOSE_APPT') :<span class = "text-danger"> *</span></label>
            <div class = "col-md-8 margin-top-8">
                <?php
                //disable
                $disabledCAll = '';
                if (!empty($apptToCmDataArr)) {
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
                <div class="form-group form-md-line-input">
                    <div class="col-md-10">
                        <div class="md-checkbox-list">
                            @foreach($targetArr as $key=>$item)

                            <?php
                            $checked = '';
                            $disabled = '';
                            $title = __('label.CHECK');
                            if (!empty($apptStringToArr)) {
                                if (in_array($item->id, $apptStringToArr)) {
                                    $checked = 'checked';
                                    $apptName = $item->name;
                                    if (in_array($item->id, $apptToCmDataArr)) {
                                        $title = __('label.APPT_IS_ALREADY_ASSIGNED_TO_APPT_TO_CM', ['apptName' => $apptName]);
                                        $disabled = 'disabled';
                                    } else {
                                        $title = __('label.UNCHECK');
                                    }
                                }
                            }
                            ?>

                            <div class="md-checkbox">
                                {!! Form::checkbox('appt_id['.$item->id.']',$item->id, $checked, ['id' => $item->id, 'data-id'=>$item->id,'class'=> 'md-check event-to-appt-check', $disabled]) !!}
                                @if(!empty($disabled))
                                {!! Form::hidden('appt_id['.$item->id.']', $item->id) !!}
                                @endif
                                <span class = "text-danger">{{ $errors->first('appt_id') }}</span>
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
                <a href = "{{ URL::to('eventToApptMatrix') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
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
<?php if (!$targetArr->isEmpty()) { ?>
                    // this code for  database 'check all' if all checkbox items are checked
                    if ($('.event-to-appt-check:checked').length == $('.event-to-appt-check').length) {

                        $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
                    }
    <?php
}
?>
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

                $('.event-to-appt-check').change(function () {
                    if (this.checked == false) { //if this item is unchecked
                        $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
                    }

                    //check 'check all' if all checkbox items are checked
                    if ($('.event-to-appt-check:checked').length == $('.event-to-appt-check').length) {
                        $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
                    }
                });

            });
            //    CHECK ALL
        </script>
        <script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
    </div>
</div>
