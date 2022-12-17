<!--Start:: CM Selection -->
@if(!$prevCrGen->isEmpty())
<div class = "row">
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.COURSE_REPORT_GENERATION_HAS_ALREADY_BEEN_STARTED') !!}</strong></p>
        </div>
    </div>
</div>
@else
@if(!$cmArr->isEmpty())
<div class="col-md-12 text-right">
    <button class="btn green btn-danger tooltips" type="button" id="buttonDelete" >
        <i class="fa fa-trash"></i> &nbsp;@lang('label.REMOVE_GROUPING')
    </button>
</div>
@endif
@endif
<?php $crGenStartDisabled = !$prevCrGen->isEmpty() ? 'disabled' : ''; ?>
<div class = "row">
    <div class = "col-md-12 tab-pane margin-top-10">
        <div class="row margin-bottom-10">
            <div class="col-md-12">
                <span class="col-md-12 border-bottom-1-green-seagreen">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>@lang('label.CM_SELECTION')</strong>
                        </div>
                        <div class="col-md-6 text-right padding-bottom-1">
                            <button type="button" id="cmSelectionShow" class="btn btn-xs green-sharp tooltips"
                                    title="@lang('label.SHOW_CM_SELECTION')">
                                <i class="fa fa-caret-down"></i>
                            </button>
                            <button type="button" id="cmSelectionHide" class="btn btn-xs green-sharp tooltips"
                                    title="@lang('label.HIDE_CM_SELECTION')">
                                <i class="fa fa-caret-right"></i>
                            </button>
                        </div>
                    </div>
                </span>
            </div>
        </div>


        <div class="row" id="showCmSelectionSection">
            <!-- START:: Search CM -->
            <div class="col-md-7">
                <div class="filter-block">

                    <div class="row margin-bottom-10">
                        <div class="col-md-12">
                            <span class="col-md-12 border-bottom-1-green-seagreen">
                                <i class="fa fa-plus-square"></i> <strong>@lang('label.CM_SELECTION_FILTER')</strong>
                            </span>
                        </div>
                    </div>

                    <div class = "row">
                        <div class = "col-md-5">
                            <ul class = "ver-inline-menu tabbable margin-bottom-10">
                                <li class = "active">
                                    <a data-toggle = "tab" href = "#tab_3" id="individualSearchFull">
                                        <i class="fa fa fa-users "></i>@lang('label.INDIVIDUAL_SEARCH') </a>
                                </li>
                                <li>
                                    <a data-toggle = "tab" href = "#tab_1">
                                        <i class = "fa fa-users green-color-style-color"></i> @lang('label.GROUP_TEMPLATE_WISE') </a>
                                    <span class = "after"> </span>
                                </li>

                            </ul>
                        </div>

                        <div class = "col-md-7">
                            <div class = "tab-content">

                                <!--Start:: Individual Search -->
                                <div id = "tab_3" class = "tab-pane active">
                                    <div class="row margin-bottom-10">
                                        <div class="col-md-12">
                                            <span class="col-md-12 border-bottom-1-green-seagreen">
                                                <strong>@lang('label.INDIVIDUAL_SEARCH')</strong>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label col-md-4" for="individualSearch">@lang('label.SEARCH') :</label>
                                                <div class="col-md-8">
                                                    {!! Form::text('individual_search',  Request::get('individual_search'), ['class' => 'form-control individual-search', 'id' => 'individualSearch', 'title' => __('label.PERSONAL_NO'), 'placeholder' => __('label.PERSONAL_NO')]) !!} 
                                                    <!--                                                <datalist id="cmPersonalNo">
                                                                                                        @if (!$nameArr->isEmpty())
                                                                                                        @foreach($nameArr as $item)
                                                                                                        <option value="{{$item->personal_no}}" />
                                                                                                        @endforeach
                                                                                                        @endif
                                                                                                    </datalist>-->
                                                </div>
                                            </div>
                                            @if(!$targetArr->isEmpty())
                                            @foreach($targetArr as $target)
                                            @if (!empty($prevCmArr) && in_array($target->id, $prevCmArr))
                                            @if(empty($prevOtherGroupCmArr) || (!empty($prevOtherGroupCmArr) && !array_key_exists($target->id, $prevOtherGroupCmArr)))
                                            {!! Form:: hidden('cm_selected['.$target->id.']', $target->id, ['id' => 'cmSelected_' . $target->id, 'class' => 'cm-selected']) !!}

                                            @endif
                                            @endif
                                            @endforeach
                                            @endif
                                            <div id="showIndividualSearchCm">
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
                                                                    $checked = '';
                                                                    $disabled = '';
                                                                    $spanDisabled = '';
                                                                    $title = '';
                                                                    if (!empty($prevCmArr)) {
                                                                        $checked = in_array($target->id, $prevCmArr) ? 'checked' : '';
                                                                    }

                                                                    if (!empty($prevOtherGroupCmArr)) {
                                                                        if (array_key_exists($target->id, $prevOtherGroupCmArr)) {
                                                                            $checked = '';
                                                                            $disabled = 'disabled';
                                                                            $spanDisabled = 'span-disabled';
                                                                            $title = __('label.ALREADY_ASSIGNED_TO_DS', ['ds' => $prevOtherGroupCmArr[$target->id]]);
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td class="vcenter" width="20px">
                                                                            <div class="md-checkbox has-success tooltips" title="{{$title}}" >
                                                                                {!! Form::checkbox('cm_id['.$target->id.']',$target->id, $checked, ['id' => $target->id . '_' . $submitFrom
                                                                                , 'class'=> 'md-check cm-select cm-select-' . $target->id . ' cm-select-type-'. $selectionClass, $disabled, $crGenStartDisabled]) !!}
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end:: Individual Search -->

                                <!--Start:: Group Template Wise search -->
                                <div id = "tab_1" class = "tab-pane">
                                    <div class="row margin-bottom-10">
                                        <div class="col-md-12">
                                            <span class="col-md-12 border-bottom-1-green-seagreen">
                                                <strong>@lang('label.GROUP_TEMPLATE_WISE_SEARCH')</strong>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label col-md-4" for="cmGroupId">@lang('label.CM_GROUP') :</label>
                                                <div class="col-md-8">
                                                    {!! Form::select('cm_group_id', $cmGroupList, null, ['class' => 'form-control js-source-states', 'id' => 'cmGroupId']) !!}
                                                </div>
                                            </div>


                                            <!--get Group template wise search cm-->
                                            <div id="getCmGroupWiseSearchCm"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--end:: Group Template Wise search -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End::Search CM-->

            <div class="col-md-5">
                <div class="filter-block">

                    <div class="row margin-bottom-10">
                        <div class="col-md-12">
                            <span class="col-md-12 border-bottom-1-green-seagreen">
                                <strong>@lang('label.SELECTED_CM_LIST')</strong>
                            </span>
                        </div>
                    </div>

                    {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'selectedCmForm')) !!}
                    @csrf
                    <div id="selectedCmList">

                        <div class="row margin-bottom-10 selected-cm-list">
                            @if(!$cmArr->isEmpty())
                            <div class="col-md-12 margin-top-10">
                                <div class="table-responsive max-height-250 webkit-scrollbar">
                                    <table class="table table-bordered table-hover" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th class="vcenter text-center">@lang('label.SL')</th>
                                                <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                                <th class="vcenter">@lang('label.NAME')</th>
                                                @if($prevCrGen->isEmpty())
                                                <th class="vcenter text-center">@lang('label.REMOVE')</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="selected-cm-body">
                                            <?php $cmSl = 0; ?>
                                            @foreach($cmArr as $target)

                                            <tr>
                                                {{ Form::hidden('selected_cm['.$target->id.']', $target->id, array('id' =>  $target->id, 'class' => 'selected-cm')) }}
                                                <td class="vcenter text-center initial-serial-cm">{!! ++$cmSl !!}</td>
                                                <td class="text-center vcenter" width="50px">
                                                    <?php if (!empty($target->photo && File::exists('public/uploads/cm/' . $target->photo))) { ?>
                                                        <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target->photo}}" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                                                    <?php } else { ?>
                                                        <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                                                    <?php } ?>
                                                </td>
                                                <td class="vcenter">{!!Common::getFurnishedCmName($target->cm_name)!!}</td>
                                                @if($prevCrGen->isEmpty())
                                                <td class="text-center"> 
                                                    <button class="btn btn-danger remove-selected-cm tooltips" type="button" data-id="{!! $target->id !!}" title="@lang('label.REMOVE')">×</button>
                                                </td>
                                                @endif
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 margin-top-10">
                                <div class="alert alert-success alert-dismissable">
                                    <p><strong> {!! __('label.TOTAL_NO_OF_SELECTED_CM') !!} : &nbsp;<span class="selected-cm-no">{!! sizeof($cmArr) !!}</span></strong></p>
                                </div>
                            </div>
                            @else
                            <div class="col-md-12 margin-top-10">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_SELECTED_FOR_THE_DS_YET') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<!--end:: CM Selection -->


<!--Start: Save CM & DS-->
@if($prevCrGen->isEmpty())
<div class = "row">
    <div class = "text-center col-md-12 margin-top-30">
        <button class = "cm-list-submit btn btn-circle green" type="button">
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href = "{{ URL::to('crGrouping') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>
@endif
<!--End: Save CM & DS-->


<!-- if submit wt chack End -->
<script type="text/javascript">
    $(function () {
        //cm selection toggle
        $('#cmSelectionShow').hide();
        $(document).on("click", "#cmSelectionShow", function (e) {
            e.stopImmediatePropagation();
            $('#showCmSelectionSection').show(300);
            $('#cmSelectionHide').show();
            $('#cmSelectionShow').hide();

        });
        $(document).on("click", "#cmSelectionHide", function (e) {
            e.stopImmediatePropagation();
            $('#showCmSelectionSection').hide(300);
            $('#cmSelectionShow').show();
            $('#cmSelectionHide').hide();

        });


//  Start:: Set Cm Remove
        //row remove
        $(document).on('click', '.remove-selected-cm', function () {

            $(this).parent().parent().remove();
            var removeCmId = $(this).attr('data-id');
            $('#cmSelected_' + removeCmId).remove();
            $('#selectedCmId_' + removeCmId).remove();
            slCounter('cm');
            // if all CM are removed
            if ($('.remove-selected-cm').length == 0) {
                $(".selected-cm-list").html("<div class='col-md-12'>" +
                        "<div class='alert alert-danger alert-dismissable'>" +
                        "<p><strong><i class='fa fa-bell-o fa-fw'></i> {!! __('label.NO_CM_IS_SELECTED_FOR_THE_DS_YET') !!}</strong></p>" +
                        "</div>" +
                        "</div>");
            }
            $(".cm-select-" + removeCmId).prop("checked", false);

            $('.cm-list-filterable').each(function () {
                $(this).find('.cm-select-' + removeCmId).prop('checked', false);
                var checkedCmLength = $(this).find('.cm-select:checked').length;
                var cmLength = $(this).find('.cm-select').length;
                if (checkedCmLength != cmLength) {
                    $(this).find('.checked-all').prop('checked', false);
                }

            });
        });

        //  End:: Set Cm Remove
        //    Start:: CHECK ALL  CM
<?php if (!$targetArr->isEmpty()) { ?>
            var submitFrom = '<?php echo $submitFrom; ?>';
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

    function slCounter(authority) {
        var sl = 0;
        $('.initial-serial-' + authority).each(function () {
            sl++;
            $(this).text(sl);
        });
        $('.selected-' + authority + '-no').text(sl);
    }

    function allSameCmSelected(cmId, stat) {
        $('.cm-list-filterable').each(function () {
            $(this).find('.cm-select-' + cmId).prop('checked', stat);

        });
    }
//    End:: CHECK ALL CM
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>