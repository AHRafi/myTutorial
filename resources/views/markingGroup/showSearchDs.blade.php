
<div class="row margin-top-10 margin-bottom-10">

    @if(!$targetArr->isEmpty())

    <div class="col-md-12">
        {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm'.$submitFromDs)) !!}
        {{csrf_field()}}


        {!! Form::hidden('course_id', $request->course_id,['id' => 'courseId'])  !!}
        {!! Form::hidden('term_id', $request->term_id,['id' => 'termId'])  !!}
        {!! Form::hidden('event_id', $request->event_id,['id' => 'eventId'])  !!}
        {!! Form::hidden('sub_event_id', $request->sub_event_id,['id' => 'subEventId'])  !!}
        {!! Form::hidden('sub_sub_event_id', $request->sub_sub_event_id,['id' => 'subSubEventId'])  !!}
        {!! Form::hidden('sub_sub_sub_event_id', $request->sub_sub_sub_event_id,['id' => 'subSubSubEventId'])  !!}

        <div class="table-responsive max-height-200 webkit-scrollbar ds-list-filterable">
            <table class="table borderless table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th class="vcenter" width="20px">
                            <div class="md-checkbox has-success tooltips" title="@lang('label.CHECK_ALL')">
                                <!--<input type="checkbox" id="checkedAll" class="md-check">-->
                                {!! Form::checkbox('check_all',1,false,['id' => 'checkedAll'. $submitFromDs, 'class'=> 'md-check checked-all checked-all-'. $selectionClassDs
                                , 'data-class-initial' => $selectionClassDs]) !!} 
                                <label for="checkedAll{{ $submitFromDs }}">
                                    <span></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>
                            </div>
                        </th>
                        <th class="vcenter">@lang('label.CHECK_ALL')</th>
                    </tr>
                </thead>
                <tbody>

                    <?php $sl = 0; ?>
                    @foreach($targetArr as $target)
                    <?php
                    $dsChecked = '';
                    $disabled = '';
                    $spanDisabled = '';
                    $title = '';
                    if (!empty($prevDsArr)) {
                        $dsChecked = in_array($target->id, $prevDsArr) ? 'checked' : '';
                    }

                    if (!empty($prevOtherGroupDsArr)) {
                        if (array_key_exists($target->id, $prevOtherGroupDsArr)) {
                            $dsChecked = '';
//                            $disabled = 'disabled';
//                            $spanDisabled = 'span-disabled';
//                            $title = __('label.ALREADY_ASSIGNED_TO_GROUP', ['group' => $prevOtherGroupDsArr[$target->id]]);
                        }
                    }
                    ?>
                    <tr>
                        <td class="vcenter" width="20px">
                            <div class="md-checkbox has-success tooltips" title="{{$title}}" >
                                {!! Form::checkbox('ds_id['.$target->id.']',$target->id, $dsChecked, ['id' => $target->id . '_' . $submitFromDs
                                , 'class'=> 'md-check ds-select ds-select-' . $target->id . ' ds-select-type-'. $selectionClassDs
                                , 'data-submit-form' => $submitFromDs, 'data-class-initial' => $selectionClassDs, $disabled]) !!}
                                <label for="{!! $target->id . '_' . $submitFromDs !!}">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>
                            </div>
                        </td>
                        <td class=" vcenter">
                            <?php if (!empty($target->photo && File::exists('public/uploads/user/' . $target->photo))) { ?>
                                <img width="22" height="25" src="{{URL::to('/')}}/public/uploads/user/{{$target->photo}}" alt="{{ $target->full_name}}"/>
                            <?php } else { ?>
                                <img width="22" height="25" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $target->full_name}}"/>
                            <?php } ?>&nbsp;&nbsp;
                            <span class="{{ $spanDisabled }}">{{$target->ds_name}}</span>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        {!! Form::close() !!}                  
    </div>

    @if($eventAssessmentMarkingDataArr->isEmpty())
    <div class="col-md-6 margin-top-10">
        <button type="button" class="btn btn-primary assign-selected-ds" data-id="{{$submitFromDs}}" id="assignSelectedDs{{$submitFromDs}}">
            @lang('label.SET')&nbsp;<i class="fa fa-arrow-circle-right"></i> 
        </button>
    </div>
    @endif


    @else
    <?php
    $dsSelectionErrAlert = __('label.NO_DS_FOUND_FOR_SELECTION');
    if (!empty($prevOtherGroupDsArr)) {
        $dsSelectionErrAlert = __('label.ALL_DS_HAVE_BEEN_ASSIGNED_TO_OTHER_MARKING_GROUPS');
    }
    ?>
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! $dsSelectionErrAlert !!}</strong></p>
        </div>
    </div>
    @endif

</div>
<!-- Modal end-->
<script type="text/javascript">
    //    CHECK ALL
    $(document).ready(function () {
<?php if (!$targetArr->isEmpty()) { ?>
            var submitFromDs = '<?php echo $submitFromDs; ?>';
            var selectionClassDs = '<?php echo $selectionClassDs; ?>';
            var dsArr = [];
            $('.ds-select:checked').each(function () {
                var dsId = $(this).val();
                var thisSelectionClass = $(this).attr('data-class-initial');

                if (thisSelectionClass != selectionClassDs) {
                    dsArr.push(dsId);
                }
            });

            $('.ds-select-type-' + selectionClassDs).each(function () {
                var dsId = $(this).val();
                if ($.inArray(dsId, dsArr) > -1) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });
            // this code for  database 'check all' if all checkbox items are checked
            if ($('.ds-select:checked').length == $('.ds-select').length) {
                $('#checkedAll' + submitFromDs)[0].checked = true; //change 'check all' checked status to true
            }

            $("#checkedAll" + submitFromDs).change(function () {
                var selectionClassDs = $(this).attr('data-class-initial');
                if (this.checked) {
                    $(".ds-select-type-" + selectionClassDs).each(function () {
                        var dsId = $(this).val();
                        if (!this.hasAttribute("disabled")) {
                            this.checked = true;
                            allSameDsSelected(dsId, true);
                        }
                    });
                } else {
                    $(".ds-select-type-" + selectionClassDs).each(function () {
                        this.checked = false;
                        var dsId = $(this).val();
                        allSameDsSelected(dsId, false);
                    });
                }

                $('.ds-list-filterable').each(function () {
                    var checkedDsLength = $(this).find('.ds-select:checked').length;
                    var dsLength = $(this).find('.ds-select').length;
                    if (checkedDsLength == dsLength) {
                        $(this).find('.checked-all').prop('checked', true); //change 'check all' checked status to true
                    } else {
                        $(this).find('.checked-all').prop('checked', false);
                    }

                });
            });

            $('.ds-select').change(function () {
                var dsId = $(this).val();

                if (this.checked == true) {
                    allSameDsSelected(dsId, true);
                } else {
                    allSameDsSelected(dsId, false);
                }

                $('.ds-list-filterable').each(function () {
                    var checkedDsLength = $(this).find('.ds-select:checked').length;
                    var dsLength = $(this).find('.ds-select').length;
                    if (checkedDsLength == dsLength) {
                        $(this).find('.checked-all').prop('checked', true); //change 'check all' checked status to true
                    } else {
                        $(this).find('.checked-all').prop('checked', false);
                    }

                });
            });
<?php } ?>
    });

    function allSameDsSelected(dsId, stat) {
        $('.ds-list-filterable').each(function () {
            $(this).find('.ds-select-' + dsId).prop('checked', stat);

        });
    }

</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

