<div class="row">
    <div class="col-md-12 table-responsive">
        <div class="webkit-scrollbar">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center vcenter">@lang('label.SL_NO')</th>
                        <th class="w-8 vcenter">
                            <div class="md-checkbox has-success">
                                <?php
                                $allDisabled = '';

                                if (!empty($prevTermArr['active']) || !empty($prevTermArr['status']) || !empty($cmToSynArr) || !empty($termToEventArr)) {
                                    $allDisabled = 'disabled';
                                }
                                ?>
                                {!! Form::checkbox('check_all',1,false,['id' => 'checkAll','class'=> 'md-check',$allDisabled]) !!} 
                                <label for="checkAll">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>
                            </div>
                        </th>
                        <th class="vcenter">@lang('label.TERM')</th>
                        <th class="text-center vcenter">@lang('label.INITIAL_DATE')</th>
                        <th class="text-center vcenter">@lang('label.TERMINATION_DATE')</th>
                        <th class="text-center vcenter">@lang('label.NUMBER_OF_WEEK')</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($termArr))
                    <?php $sl = 0; ?>
                    @foreach($termArr as $termId => $termName)
                    <?php
                    $checked = in_array($termId, array_keys($prevData)) ? 'checked' : null;
                    $disabled = in_array($termId, array_keys($prevData)) ? null : 'disabled';
                    $title = $termDisabled = '';
                    if (!empty($prevData[$termId]['active']) && $prevData[$termId]['active'] == '1') {
                        $termDisabled = 'disabled';
                        $title = $termName . ' ' . __('label.IS_ALREADY_ACTIVE');
                    }
                    if (!empty($prevData[$termId]['status']) && $prevData[$termId]['status'] == '2') {
                        $termDisabled = 'disabled';
                        $title = $termName . ' ' . __('label.IS_ALREADY_CLOSED');
                    }
                    if (!empty($cmToSynArr) && in_array($termId, $cmToSynArr)) {
                        $termDisabled = 'disabled';
                        $title = $termName . ' ' . __('label.IS_ALREADY_RELATED_WITH_CM_TO_SYN');
                    }

                    if (!empty($termToEventArr) && in_array($termId, $termToEventArr)) {
                        $termDisabled = 'disabled';
                        $title = $termName . ' ' . __('label.IS_ALREADY_ASSIGNED_IN_EVENT');
                    }
                    ?>
                    <tr>
                        <td class="text-center vcenter">{!! ++$sl !!}</td>
                        <td class="text-center  vcenter">
                            <div class="md-checkbox has-success tooltips" title="<?php echo $title ?>">
                                {!! Form::checkbox('term_id['.$termId.']', $termId,$checked,['id' => 'term-'.$termId, 'class'=> 'md-check term tooltips ', 'data-term-id' => $termId, $termDisabled]) !!}
                                <label for="term-{{ $termId }}">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>
                            </div>
                            @if(!empty($termDisabled))
                            {!! Form::hidden('term_id['.$termId.']', $termId) !!}
                            @endif
                        </td>
                        {!! Form::hidden('status['.$termId.']', !empty($prevData[$termId]['status']) ? $prevData[$termId]['status'] : '0') !!}
                        {!! Form::hidden('active['.$termId.']', !empty($prevData[$termId]['active']) ? $prevData[$termId]['active'] : '0') !!}
                        <td class="vcenter">{{ $termName }}</td>
                        <td class="text-center vcenter">
                            <div class="input-group date datepicker2">
                                {!! Form::text('initial_date['.$termId.']', !empty($prevData[$termId]['initial_date'])? Helper::formatDate($prevData[$termId]['initial_date']):null, ['id'=> 'initialDate-'.$termId, 'class' =>
                                'form-control term-date initial-date', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '', 'readonly' => '',$disabled,'data-term-id' => $termId]) !!}
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" id="initialReset_{{$termId}}" data-term-id="{{$termId}}" type="button" remove="initialDate-{{$termId}}" {{$disabled}}>
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button"  {{$disabled}} id="initialSet_{{$termId}}">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td class="text-center vcenter">
                            <div class="input-group date datepicker2">
                                {!! Form::text('termination_date['.$termId.']', !empty($prevData[$termId]['termination_date'])? Helper::formatDate($prevData[$termId]['termination_date']):null, ['id'=> 'terminationDate-'.$termId, 'class' =>
                                'form-control term-date termination-date', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '', 'readonly' => '',$disabled,'data-term-id' => $termId]) !!}
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" id="terminationReset_{{$termId}}" data-term-id="{{$termId}}" type="button" remove="terminationDate-{{$termId}}" {{$disabled}}>
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button" {{$disabled}} id="terminationSet_{{$termId}}">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td class="text-center vcenter">
                            <div class="col-md-12">
                                {!! Form::text('number_of_week['.$termId.']', !empty($prevData[$termId]['number_of_week'])?$prevData[$termId]['number_of_week']:null, ['id'=> 'noOfWeeks-'.$termId, 'class' => 'form-control number-of-week integer-only', 'readonly',$disabled]) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('number_of_week') }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @if($sl < sizeof($termArr))
                    <tr class="active">
                        <td class="text-center vcenter" colspan="2"></td>
                        <td class="vcenter">{{ __('label.RECESS_NO', ['sl' => $sl]) }}</td>
                        <td class="text-center  vcenter">
                            <div class="input-group date datepicker2">
                                {!! Form::text('recess_initial_date['.$termId.']', !empty($prevData[$termId]['recess_initial_date'])? Helper::formatDate($prevData[$termId]['recess_initial_date']):null, ['id'=> 'recessInitialDate-'.$termId, 'class' =>
                                'form-control recess-term-date recess-initial-date', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '',$disabled,'data-term-id' => $termId]) !!}
                                <span class="input-group-btn">
                                    <button class="btn default recess-reset-date" id="recessInitialReset_{{$termId}}" data-term-id="{{$termId}}" type="button" remove="recessInitialDate-{{$termId}}" {{$disabled}}>
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default recess-date-set" type="button"  {{$disabled}} id="recessInitialSet_{{$termId}}">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td class="text-center  vcenter">
                            <div class="input-group date datepicker2">
                                {!! Form::text('recess_termination_date['.$termId.']', !empty($prevData[$termId]['recess_termination_date'])? Helper::formatDate($prevData[$termId]['recess_termination_date']):null, ['id'=> 'recessTerminationDate-'.$termId, 'class' =>
                                'form-control recess-term-date recess-termination-date', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '', 'readonly' => '',$disabled,'data-term-id' => $termId]) !!}
                                <span class="input-group-btn">
                                    <button class="btn default recess-reset-date" id="recessTerminationReset_{{$termId}}" data-term-id="{{$termId}}" type="button" remove="recessTerminationDate-{{$termId}}" {{$disabled}}>
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default recess-date-set" type="button" {{$disabled}} id="recessTerminationSet_{{$termId}}">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td class="text-center  vcenter">
                            <div class="col-md-12">
                                {!! Form::text('recess_number_of_week['.$termId.']', !empty($prevData[$termId]['recess_number_of_week'])?$prevData[$termId]['recess_number_of_week']:null, ['id'=> 'recessNoOfWeeks-'.$termId, 'class' => 'form-control recess-number-of-week integer-only', 'readonly',$disabled]) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('recess_number_of_week') }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    @else
                    <tr>
                        <td colspan="10">@lang('label.NO_TERM_FOUND')</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-5 col-md-5">
                    <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                    </button>
                    <a href="{{ URL::to('termToCourse') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('public/js/custom.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    //function for no of weeks
    $(document).on('change', '.term-date', function () {
        var termId = $(this).attr('data-term-id');
        var initialDate = new Date($('#initialDate-' + termId).val());
        var terminationDate = new Date($('#terminationDate-' + termId).val());
        if (terminationDate < initialDate) {
            swal("@lang('label.TERMINATION_DATE_MUST_BE_GREATER_THAN_INITIAL_DATE')");
            $('#terminationDate-' + termId).val('');
            $('noOfWeeks-' + termId).val('');
            return false;
        }

        var weeks = Math.ceil((terminationDate - initialDate) / (24 * 3600 * 1000 * 7));

        if (isNaN(weeks)) {
            var weeks = '';
        }
        $("#noOfWeeks-" + termId).val(weeks);
    });
    //function for no of weeks in recess
    $(document).on('change', '.recess-term-date', function () {
        var termId = $(this).attr('data-term-id');
        var recessInitialDate = new Date($('#recessInitialDate-' + termId).val());
        var recessTerminationDate = new Date($('#recessTerminationDate-' + termId).val());
        if (recessTerminationDate < recessInitialDate) {
            swal("@lang('label.TERMINATION_DATE_MUST_BE_GREATER_THAN_INITIAL_DATE')");
            $('#recessTerminationDate-' + termId).val('');
            $('recessNoOfWeeks-' + termId).val('');
            return false;
        }

        var weeks = Math.ceil((recessTerminationDate - recessInitialDate) / (24 * 3600 * 1000 * 7));

        if (isNaN(weeks)) {
            var weeks = '';
        }
        $("#recessNoOfWeeks-" + termId).val(weeks);
    });

    $(document).on('click', '.reset-date', function () {
        var termId = $(this).attr('data-term-id');
        $("#noOfWeeks-" + termId).val('');
    });
    $(document).on('click', '.recess-reset-date', function () {
        var termId = $(this).attr('data-term-id');
        $("#recessNoOfWeeks-" + termId).val('');
    });
    //'check all' change
    $(document).on('click', '#checkAll', function () {
        if (this.checked) {
            $(".initial-date").prop('disabled', false);
            $(".termination-date").prop('disabled', false);
            $(".reset-date").prop('disabled', false);
            $(".date-set").prop('disabled', false);
            $(".number-of-week").prop('disabled', false);
            $('.term').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
            //recess
            $(".recess-initial-date").prop('disabled', false);
            $(".recess-termination-date").prop('disabled', false);
            $(".recess-reset-date").prop('disabled', false);
            $(".recess-date-set").prop('disabled', false);
            $(".recess-number-of-week").prop('disabled', false);
        } else {
            $(".initial-date").prop('disabled', true);
            $(".termination-date").prop('disabled', true);
            $(".reset-date").prop('disabled', true);
            $(".date-set").prop('disabled', true);
            $(".number-of-week").prop('disabled', true);
            $(".term").removeAttr('checked');
            $(".term-date").prop('disabled', true).val('');
            $(".number-of-week ").prop('disabled', true).val('');
            //recess
            $(".recess-initial-date").prop('disabled', true);
            $(".recess-termination-date").prop('disabled', true);
            $(".recess-reset-date").prop('disabled', true);
            $(".recess-date-set").prop('disabled', true);
            $(".recess-number-of-week").prop('disabled', true);
            $(".recess-term-date").prop('disabled', true).val('');
            $(".recess-number-of-week ").prop('disabled', true).val('');

        }
    });

    $(document).on('click', '.term', function () {
        var termId = $(this).data('term-id');
        if (this.checked == false) { //if this item is unchecked
            $("#initialDate-" + termId).prop('disabled', true).val('');
            $("#terminationDate-" + termId).prop('disabled', true).val('');
            $("#initialReset_" + termId).prop('disabled', true);
            $("#terminationReset_" + termId).prop('disabled', true);
            $("#initialSet_" + termId).prop('disabled', true);
            $("#terminationSet_" + termId).prop('disabled', true);
            $("#noOfWeeks-" + termId).prop('disabled', true).val(' ');
            //recess
            $("#recessInitialDate-" + termId).prop('disabled', true).val('');
            $("#recessTerminationDate-" + termId).prop('disabled', true).val('');
            $("#recessInitialReset_" + termId).prop('disabled', true);
            $("#recessTerminationReset_" + termId).prop('disabled', true);
            $("#recessInitialSet_" + termId).prop('disabled', true);
            $("#recessTerminationSet_" + termId).prop('disabled', true);
            $("#recessNoOfWeeks-" + termId).prop('disabled', true).val(' ');
//                $('#checkAll')[0].checked = false; //change 'check all' checked status to false
        } else {
            $("#initialDate-" + termId).prop('disabled', false);
            $("#terminationDate-" + termId).prop('disabled', false);
            $("#initialReset_" + termId).prop('disabled', false);
            $("#terminationReset_" + termId).prop('disabled', false);
            $("#initialSet_" + termId).prop('disabled', false);
            $("#terminationSet_" + termId).prop('disabled', false);
            $("#noOfWeeks-" + termId).prop('disabled', false);
            //recess
            $("#recessInitialDate-" + termId).prop('disabled', false);
            $("#recessTerminationDate-" + termId).prop('disabled', false);
            $("#recessInitialReset_" + termId).prop('disabled', false);
            $("#recessTerminationReset_" + termId).prop('disabled', false);
            $("#recessInitialSet_" + termId).prop('disabled', false);
            $("#recessTerminationSet_" + termId).prop('disabled', false);
            $("#recessNoOfWeeks-" + termId).prop('disabled', false);

//                $('#checkAll')[0].checked = true;
        }
        //check 'check all' if all checkbox items are checked

        allCheck();
    });


//        $(document).on('click', '.term', function () {
//            allCheck();
//        });
    allCheck();


});
function allCheck() {

    if ($('.term:checked').length == $('.term').length) {
        $('#checkAll')[0].checked = true;
    } else {
        $('#checkAll')[0].checked = false;
    }
}

</script>