<?php $crGenStartDisabled = !$prevCrGen->isEmpty() ? 'disabled' : ''; ?>
<div class="row margin-top-10 margin-bottom-10">
    
    @if(!$targetArr->isEmpty())

    <div class="col-md-12">
        {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm'.$submitFrom)) !!}
        {{csrf_field()}}


        {!! Form::hidden('course_id', $request->course_id,['id' => 'courseId'])  !!}
        {!! Form::hidden('ds_id', $request->ds_id,['id' => 'dsId'])  !!}

        <div class="table-responsive max-height-200 webkit-scrollbar cm-list-filterable">
            <table class="table borderless table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th class="vcenter" width="20px">
                            <div class="md-checkbox has-success tooltips" title="@lang('label.CHECK_ALL')">
                                <!--<input type="checkbox" id="checkedAll" class="md-check">-->
                                {!! Form::checkbox('check_all',1,false,['id' => 'checkedAll'. $submitFrom, 'class'=> 'md-check checked-all checked-all-'. $selectionClass
                                , 'data-class-initial' => $selectionClass, $crGenStartDisabled]) !!} 
                                <label for="checkedAll{{ $submitFrom }}">
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
                    $cmChecked = '';
                    $disabled = '';
                    $title = '';
                    $spanDisabled = '';
                    if (!empty($prevCmArr)) {
                        $cmChecked = in_array($target->id, $prevCmArr) ? 'checked' : '';
                    }

                    if (!empty($prevOtherGroupCmArr)) {
                        if (array_key_exists($target->id, $prevOtherGroupCmArr)) {
                            $cmChecked = '';
                            $disabled = 'disabled';
                            $spanDisabled = 'span-disabled';
                            $title = __('label.ALREADY_ASSIGNED_TO_DS', ['ds' => $prevOtherGroupCmArr[$target->id]]);
                        }
                    }
                    ?>
                    <tr>
                        <td class="vcenter" width="20px">
                            <div class="md-checkbox has-success tooltips" title="{{$title}}" >
                                {!! Form::checkbox('cm_id['.$target->id.']',$target->id, $cmChecked, ['id' => $target->id . '_' . $submitFrom
                                , 'class'=> 'md-check cm-select cm-select-' . $target->id . ' cm-select-type-'. $selectionClass
                                , 'data-class-initial' => $selectionClass, $disabled, $crGenStartDisabled]) !!}
                                <label for="{!! $target->id . '_' . $submitFrom !!}">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>
                            </div>
                        </td>
                        <td class=" vcenter">
                            <?php if (!empty($target->photo && File::exists('public/uploads/cm/' . $target->photo))) { ?>
                                <img width="22" height="25" src="{{URL::to('/')}}/public/uploads/cm/{{$target->photo}}" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                            <?php } else { ?>
                                <img width="22" height="25" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                            <?php } ?>&nbsp;&nbsp;
                            <span class="{{ $spanDisabled }}">{!!Common::getFurnishedCmName($target->cm_name)!!}</span>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        {!! Form::close() !!}                  
    </div>
    @if($prevCrGen->isEmpty())
    <div class="col-md-6 margin-top-10">
        <button type="button" class="btn btn-primary assign-selected-cm" data-id="{{$submitFrom}}" id="assignSelectedCm{{$submitFrom}}">
            @lang('label.SET')&nbsp;<i class="fa fa-arrow-circle-right"></i> 
        </button>
    </div>
    @endif
    @else
    <?php
    $cmSelectionErrAlert = __('label.NO_CM_FOUND_FOR_SELECTION');
    if (!empty($prevOtherGroupCmArr)) {
        $cmSelectionErrAlert = __('label.ALL_CM_HAVE_BEEN_ASSIGNED_TO_OTHER_DS');
    }
    ?>
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! $cmSelectionErrAlert !!}</strong></p>
        </div>
    </div>
    @endif

</div>
<!-- Modal end-->

<script type="text/javascript">
    //    CHECK ALL
    $(document).ready(function () {
<?php if (!$targetArr->isEmpty()) { ?>
            var submitFrom = '<?php echo $submitFrom; ?>';
            var selectionClass = '<?php echo $selectionClass; ?>';
            var cmArr = [];
            $('.cm-select:checked').each(function () {
                var cmId = $(this).val();
                var thisSelectionClass = $(this).attr('data-class-initial');

                if (thisSelectionClass != selectionClass) {
                    cmArr.push(cmId);
                }
            });

            $('.cm-select-type-' + selectionClass).each(function () {
                var cmId = $(this).val();
                if ($.inArray(cmId, cmArr) > -1) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });

            // this code for  database 'check all' if all checkbox items are checked
            if ($('.cm-select:checked').length == $('.cm-select').length) {
                $('#checkedAll' + submitFrom)[0].checked = true; //change 'check all' checked status to true
            }

            $("#checkedAll" + submitFrom).change(function () {
                var selectionClass = $(this).attr('data-class-initial');
                if (this.checked) {
                    $(".cm-select-type-" + selectionClass).each(function () {
                        var cmId = $(this).val();
                        if (!this.hasAttribute("disabled")) {
                            this.checked = true;
                            allSameCmSelected(cmId, true);
                        }
                    });
                } else {
                    $(".cm-select-type-" + selectionClass).each(function () {
                        this.checked = false;
                        var cmId = $(this).val();
                        allSameCmSelected(cmId, false);

                    });
                }

                $('.cm-list-filterable').each(function () {
                    var checkedCmLength = $(this).find('.cm-select:checked').length;
                    var cmLength = $(this).find('.cm-select').length;
                    if (checkedCmLength == cmLength) {
                        $(this).find('.checked-all').prop('checked', true); //change 'check all' checked status to true
                    } else {
                        $(this).find('.checked-all').prop('checked', false);
                    }

                });
            });

            $('.cm-select').change(function () {
                var cmId = $(this).val();

                if (this.checked == true) {
                    allSameCmSelected(cmId, true);
                } else {
                    allSameCmSelected(cmId, false);
                }

                $('.cm-list-filterable').each(function () {
                    var checkedCmLength = $(this).find('.cm-select:checked').length;
                    var cmLength = $(this).find('.cm-select').length;
                    if (checkedCmLength == cmLength) {
                        $(this).find('.checked-all').prop('checked', true); //change 'check all' checked status to true
                    } else {
                        $(this).find('.checked-all').prop('checked', false);
                    }

                });
            });
<?php } ?>
    });

    function allSameCmSelected(cmId, stat) {
        $('.cm-list-filterable').each(function () {
            $(this).find('.cm-select-' + cmId).prop('checked', stat);

        });
    }

</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

