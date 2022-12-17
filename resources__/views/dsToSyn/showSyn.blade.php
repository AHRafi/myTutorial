@if(!$synList->isEmpty())
<table class="table table-bordered table-hover" id="dataTable">
    <thead>
        <tr>
            <th class="vcenter">
                <div class="md-checkbox has-success">
                    {!! Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check']) !!}
                    <label for="checkAll">
                        <span class="inc"></span>
                        <span class="check mark-caheck"></span>
                        <span class="box mark-caheck"></span>
                    </label>
                    &nbsp;&nbsp;<span class="bold">@lang('label.CHECK_ALL')</span>
                </div>
            </th>
            <th class="vcenter">@lang('label.SYN')</th>
            <th class="vcenter text-center">@lang('label.SELECT_DS')</th>
        </tr>
    </thead>
    <tbody>
        @foreach($synList as $target)
        <?php
        //check and show previous value
        $checkDisabled = array_key_exists($target->id, $disableSyn) ? 'disabled' : '';
        $title = array_key_exists($target->id, $disableSyn) ? $disableSyn[$target->id] . ' ' . __('label.ASSIGNED_IN_RECRUIT_TO_SYN') : '';
        $checked = '';
        $disabled = 'disabled';
        if (!empty($prevDataList)) {
            if (array_key_exists($target->id, $prevDataList)) {
                $checked = 'checked';
                $disabled = '';
            }
        }
        ?>
        <tr>
            <td class="vcenter width-200">
                <div class="md-checkbox has-success tooltips" title="{!!$title!!}">
                    {!! Form::checkbox('syn_id['.$target->id.']', $target->id, $checked, ['id' => $target->id, 'data-id'=>$target->id,'class'=> 'md-check ds-check',$checkDisabled]) !!}
                    <label for="{!! $target->id !!}">
                        <span class="inc"></span>
                        <span class="check mark-caheck" ></span>
                        <span class="box mark-caheck"></span>
                    </label>
                </div>
            </td>
            <td class="vcenter">{!! $target->name !!}</td>
            <td class="vcenter text-center width-480">
                <select class="form-control width-inherit js-source-states ds-list" name="syn_ds_id[{!! $target->id !!}]" id="ds-{!! $target->id !!}" {!! $disabled !!}>
                    <?php $i = 0; ?>
                    @foreach($dsList as $dsKey => $dsValue)
                    <?php
                    $selectData = !empty($prevDataList[$target->id]) ? $prevDataList[$target->id] : '0';
                    $optionId = ($i == 0) ? '0' : $dsKey;
                    $i++;
                    ?>
                    <option value="{!! $dsKey !!}" id="{!! $target->id.'-'.$optionId !!}" 
                            data-syndicate-id="{!! $target->id !!}" data-option-id="{!! $optionId !!}"
                            <?php
                            if ($selectData == $dsKey) {
                                echo 'selected="selected"';
                            } else {
                                echo '';
                            }
                            ?>>{!! $dsValue !!} 
                    </option>
                    @endforeach
                </select>
            </td>
            @endforeach
    </tbody>
</table>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-4 col-md-5">
            <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                <i class="fa fa-check"></i> @lang('label.SUBMIT')
            </button>
            <a href="{{ URL::to('dsToSyn') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
        </div>
    </div>
</div>
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_SYN_FOUND') !!}</strong></p>
    </div>
</div>
@endif
<!-- Modal start -->
<div class="modal fade" id="modelAssingDs" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showAssingDs">
        </div> 
    </div> 
</div>
<!-- Modal end-->
<script type="text/javascript">
    $(function () {

        $('#checkAll').change(function () {  //'check all' change
            if (this.checked) {
                $('.ds-check').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                $(".ds-list").prop('disabled', false);
            } else {
                $(".ds-list").prop('disabled', true);
            }
        });

        $('.ds-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#checkAll')[0].checked = false; //change 'check all' checked status to false
                var dsId = $(this).data('id');
                $("#ds-" + dsId).prop('disabled', true);
            } else {
                var dsId = $(this).data('id');
                $("#ds-" + dsId).prop('disabled', false);
            }

            //check 'check all' if all checkbox items are checked
            if ($('.ds-check:checked').length == $('.ds-check').length) {
                $('#checkAll')[0].checked = true; //change 'check all' checked status to true
            }
            $("select.js-source-states").select2();
        });


        var selections = [];
        $('select.ds-list option:selected').each(function () {
            if ($(this).val() != '0') {
                var optionId = $(this).attr('data-option-id');
                selections.push(optionId);
            }
        });

        $('select.ds-list option').each(function () {
            $(this).attr('disabled', $.inArray($(this).val(), selections) > -1 && !$(this).is(":selected"));
        });

        $(document).on('change', 'select.ds-list', function () {
            var selections = [];
            $('select.ds-list option:selected').each(function () {
                if ($(this).val() != '0') {
                    var optionId = $(this).attr('data-option-id');
                    selections.push(optionId);
                }
            });

            $('select.ds-list option').each(function () {
                $(this).attr('disabled', $.inArray($(this).val(), selections) > -1 && !$(this).is(":selected"));
            });
            $("select.js-source-states").select2();
        });
    });

</script>
