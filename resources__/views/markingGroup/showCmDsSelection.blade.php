
<div class="col-md-9">
    @if(!$eventAssessmentMarkingDataArr->isEmpty())
    <div class="alert alert-danger alert-dismissable margin-bottom-2">
        <p>
            <strong>
                <i class="fa fa-bell-o fa-fw"></i> {!! __('label.MARKING_HAS_ALREADY_BEEN_STARTED') !!}&nbsp;
                <a href="{{url('clearMarking')}}">{!! __('label.CLICK_HERE_TO_GO_TO', ['module'=> __('label.CLEAR_MARKING')]) !!}</a>
            </strong>
        </p>
    </div>
    @endif
</div>
<!--<div class="col-md-3 text-right">
    <button class="btn green-sharp tooltips" type="button" id="buttonActDeact" data-status="1">
        <i class="fa fa-"></i> &nbsp;@lang('label.ACTIVATE_ASSESSMENT')
    </button>
</div>-->
@if($eventAssessmentMarkingDataArr->isEmpty())
<div class="col-md-3 text-right">
    <button class="btn green btn-danger tooltips" type="button" id="buttonDelete" >
        <i class="fa fa-trash"></i> &nbsp;@lang('label.REMOVE_GROUP_ASSIGNMENT')
    </button>
</div>
@endif
<!--Start:: CM Selection -->
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
                            <!--                            <li>
                                                            <a data-toggle = "tab" href = "#tab_2">
                                                                <i class = "fa fa-life-ring"></i> @lang('label.SYN_WISE') </a>
                                                        </li>-->

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
                                                    {!! Form::hidden('term_id', $request->term_id,['id' => 'termId'])  !!}
                                                    {!! Form::hidden('event_id', $request->event_id,['id' => 'eventId'])  !!}
                                                    {!! Form::hidden('sub_event_id', $request->sub_event_id,['id' => 'subEventId'])  !!}
                                                    {!! Form::hidden('sub_sub_event_id', $request->sub_sub_event_id,['id' => 'subSubEventId'])  !!}
                                                    {!! Form::hidden('sub_sub_sub_event_id', $request->sub_sub_sub_event_id,['id' => 'subSubSubEventId'])  !!}

                                                    <div class="table-responsive max-height-200 webkit-scrollbar cm-list-filterable">
                                                        <table class="table borderless table-hover" id="dataTable">
                                                            <thead>
                                                                <tr>
                                                                    <th class="vcenter" width="20px">
                                                                        <div class="md-checkbox has-success tooltips" title="@lang('label.CHECK_ALL')">
                                                                            <!--<input type="checkbox" id="checkedAll" class="md-check">-->
                                                                            {!! Form::checkbox('check_all',1,false,['id' => 'checkedAll'. $submitFrom, 'class'=> 'md-check checked-all checked-all-'. $selectionClass
                                                                            , 'data-class-initial' => $selectionClass]) !!} 
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
                                                                        $title = __('label.ALREADY_ASSIGNED_TO_GROUP', ['group' => $prevOtherGroupCmArr[$target->id]]);
                                                                    }
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td class="vcenter" width="20px">
                                                                        <div class="md-checkbox has-success tooltips" title="{{$title}}" >
                                                                            {!! Form::checkbox('cm_id['.$target->id.']',$target->id, $checked, ['id' => $target->id . '_' . $submitFrom
                                                                            , 'class'=> 'md-check cm-select cm-select-' . $target->id . ' cm-select-type-'. $selectionClass, $disabled]) !!}
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

                                                @if($eventAssessmentMarkingDataArr->isEmpty())
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
                                                    $cmSelectionErrAlert = __('label.ALL_CM_HAVE_BEEN_ASSIGNED_TO_OTHER_MARKING_GROUPS');
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
                                            <label class="control-label col-md-4" for="cmGroupId2">@lang('label.CM_GROUP') :</label>
                                            <div class="col-md-8">
                                                {!! Form::select('cm_group_id_2', $cmGroupList, null, ['class' => 'form-control js-source-states', 'id' => 'cmGroupId2']) !!}
                                            </div>
                                        </div>


                                        <!--get Group template wise search cm-->
                                        <div id="showGroupTemplateWiseSearchCm"></div>
                                    </div>
                                </div>
                            </div>
                            <!--end:: Group Template Wise search -->

                            <!--Start:: Syn Wise Search -->
                            <div id = "tab_2" class = "tab-pane">
                                <div class="row margin-bottom-10">
                                    <div class="col-md-12">
                                        <span class="col-md-12 border-bottom-1-green-seagreen">
                                            <strong>@lang('label.SYN_WISE_SEARCH')</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="synId">@lang('label.SYN') :</label>
                                            <div class="col-md-8 show-syn">
                                                {!! Form::select('syn_id', $synList, Request::get('syn_id'),  ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
                                            </div>
                                        </div>

                                        <!--Get Sub Syn-->
                                        <div id="showSubSyn"></div>

                                        <!--Get Syn wise search CM-->
                                        <div id="showSynWiseSearchCm"></div>

                                    </div>

                                </div>
                            </div>
                            <!--end:: Syn Wise Search -->

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
                                            @if($eventAssessmentMarkingDataArr->isEmpty())
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
                                            @if($eventAssessmentMarkingDataArr->isEmpty())
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
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_SELECTED_FOR_THIS_MARKING_GROUP_YET') !!}</strong></p>
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
<!--end:: CM Selection -->

<!--Start:: DS Selection -->
<div class = "col-md-12 tab-pane margin-top-30">
    <div class="row margin-bottom-10">
        <div class="col-md-12">
            <span class="col-md-12 border-bottom-1-green-seagreen">
                <div class="row">
                    <div class="col-md-6">
                        <strong>@lang('label.DS_SELECTION')</strong>
                    </div>
                    <div class="col-md-6 text-right padding-bottom-1">
                        <button type="button" id="dsSelectionShow" class="btn btn-xs green-sharp tooltips"
                                title="@lang('label.SHOW_DS_SELECTION')">
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <button type="button" id="dsSelectionHide" class="btn btn-xs green-sharp tooltips"
                                title="@lang('label.HIDE_DS_SELECTION')">
                            <i class="fa fa-caret-right"></i>
                        </button>
                    </div>
                </div>
            </span>
        </div>
    </div>
    <div class="row" id="showDsSelectionSection">
        <!-- START:: Search DS -->
        <div class="col-md-7">
            <div class="filter-block">

                <div class="row margin-bottom-10">
                    <div class="col-md-12">
                        <span class="col-md-12 border-bottom-1-green-seagreen">
                            <i class="fa fa-plus-square"></i> <strong>@lang('label.DS_SELECTION_FILTER')</strong>
                        </span>
                    </div>
                </div>

                <div class = "row">
                    <div class = "col-md-5">
                        <ul class = "ver-inline-menu tabbable margin-bottom-10">
                            <li class = "active">
                                <a data-toggle = "tab" href = "#tab_2_ds" id="individualSearchFull">
                                    <i class="fa fa fa-users "></i>@lang('label.INDIVIDUAL_SEARCH') </a>
                            </li>
                            <li>
                                <a data-toggle = "tab" href = "#tab_1_ds">
                                    <i class = "fa fa-users green-color-style-color"></i> @lang('label.GROUP_TEMPLATE_WISE') </a>
                                <span class = "after"> </span>
                            </li>

                        </ul>
                    </div>

                    <div class = "col-md-7">
                        <div class = "tab-content">

                            <!--Start:: Individual Search -->
                            <div id = "tab_2_ds" class = "tab-pane active">
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
                                            <label class="control-label col-md-4" for="individualSearchDs">@lang('label.SEARCH') :</label>
                                            <div class="col-md-8">
                                                {!! Form::text('individual_search_ds',  Request::get('individual_search_ds'), ['class' => 'form-control individual-search-ds', 'id' => 'individualSearchDs', 'title' => __('label.PERSONAL_NO'), 'placeholder' => __('label.PERSONAL_NO')]) !!} 
                                            </div>
                                        </div>

                                        @if(!$targetArrDs->isEmpty())
                                        @foreach($targetArrDs as $target)
                                        @if (!empty($prevDsArr) && in_array($target->id, $prevDsArr))
                                        @if(empty($prevOtherGroupDsArr) || (!empty($prevOtherGroupDsArr) && !array_key_exists($target->id, $prevOtherGroupDsArr)))
                                        {!! Form:: hidden('ds_selected['.$target->id.']', $target->id, ['id' => 'dsSelected_' . $target->id, 'class' => 'ds-selected']) !!}

                                        @endif
                                        @endif
                                        @endforeach
                                        @endif

                                        <div id="showIndividualSearchDs">
                                            <div class="row margin-top-10 margin-bottom-10">

                                                @if(!$targetArrDs->isEmpty())

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
                                                                            {!! Form::checkbox('check_all',1,false,['id' => 'checkedAll'. $submitFromDs, 'class'=> 'md-check checked-all checked-all-'. $selectionClass
                                                                            , 'data-class-initial' => $selectionClass]) !!} 
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
                                                                @foreach($targetArrDs as $target)
                                                                <?php
                                                                $dsChecked = '';
                                                                $dsDisabled = '';
                                                                $spanDisabled = '';
                                                                $dsTitle = '';
                                                                if (!empty($prevDsArr)) {
                                                                    $dsChecked = in_array($target->id, $prevDsArr) ? 'checked' : '';
                                                                }

                                                                if (!empty($prevOtherGroupDsArr)) {
                                                                    if (array_key_exists($target->id, $prevOtherGroupDsArr)) {
                                                                        $dsChecked = '';
//                                                                        $dsDisabled = 'disabled';
//                                                                        $spanDisabled = 'span-disabled';
//                                                                        $dsTitle = __('label.ALREADY_ASSIGNED_TO_GROUP', ['group' => $prevOtherGroupDsArr[$target->id]]);
                                                                    }
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td class="vcenter" width="20px">
                                                                        <div class="md-checkbox has-success tooltips" title="{{$dsTitle}}" >
                                                                            {!! Form::checkbox('ds_id['.$target->id.']',$target->id, $dsChecked, ['id' => $target->id . '_' . $submitFromDs
                                                                            , 'class'=> 'md-check ds-select ds-select-' . $target->id . ' ds-select-type-'. $selectionClass
                                                                            , $dsDisabled]) !!}
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end:: Individual Search -->

                            <!--Start:: Group Template Wise search -->
                            <div id = "tab_1_ds" class = "tab-pane">
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
                                            <label class="control-label col-md-4" for="dsGroupId2">@lang('label.DS_GROUP') :</label>
                                            <div class="col-md-8">
                                                {!! Form::select('ds_group_id_2', $dsGroupList, null, ['class' => 'form-control js-source-states', 'id' => 'dsGroupId2']) !!}
                                            </div>
                                        </div>


                                        <!--get Group template wise search ds-->
                                        <div id="showGroupTemplateWiseSearchDs"></div>
                                    </div>
                                </div>
                            </div>
                            <!--end:: Group Template Wise search -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Search DS-->

        <div class="col-md-5">
            <div class="filter-block">

                <div class="row margin-bottom-10">
                    <div class="col-md-12">
                        <span class="col-md-12 border-bottom-1-green-seagreen">
                            <strong>@lang('label.SELECTED_DS_LIST')</strong>
                        </span>
                    </div>
                </div>

                {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'selectedDsForm')) !!}
                @csrf
                <div id="selectedDsList">
                    <div class="row margin-bottom-10 selected-ds-list">
                        @if(!$dsArr->isEmpty())
                        <div class="col-md-12 margin-top-10">
                            <div class="table-responsive max-height-250 webkit-scrollbar ">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="vcenter text-center">@lang('label.SL')</th>
                                            <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                            <th class="vcenter">@lang('label.NAME')</th>
                                            @if($eventAssessmentMarkingDataArr->isEmpty())
                                            <th class="vcenter text-center">@lang('label.REMOVE')</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="selected-ds-body">
                                        <?php $dsSl = 0; ?>
                                        @foreach($dsArr as $target)

                                        <tr>
                                            {{ Form::hidden('selected_ds['.$target->id.']', $target->id, array('id' => $target->id, 'class' => 'selected-ds')) }}
                                            <td class="vcenter text-center initial-serial-ds">{!! ++$dsSl !!}</td>
                                            <td class="text-center vcenter" width="50px">
                                                <?php if (!empty($target->photo && File::exists('public/uploads/user/' . $target->photo))) { ?>
                                                    <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$target->photo}}" alt="{{ $target->full_name}}"/>
                                                <?php } else { ?>
                                                    <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $target->full_name}}"/>
                                                <?php } ?>
                                            </td>
                                            <td class="vcenter">{{$target->ds_name}}</td>
                                            @if($eventAssessmentMarkingDataArr->isEmpty())
                                            <td class="text-center"> 
                                                <button class="btn btn-danger remove-selected-ds tooltips" type="button" data-id="{!! $target->id !!}" title="@lang('label.REMOVE')">×</button>
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
                                <p><strong> {!! __('label.TOTAL_NO_OF_SELECTED_DS') !!} : &nbsp;<span class="selected-ds-no">{!! sizeof($dsArr) !!}</span></strong></p>
                            </div>
                        </div>
                        @else
                        <div class="col-md-12 margin-top-10">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_DS_IS_SELECTED_FOR_THIS_MARKING_GROUP_YET') !!}</strong></p>
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
<!--End: DS Selection -->
@if(($cloneEventInfo->has_group_cloning)==1)
<!--Start:: Group Cloning -->
<div class = "col-md-12 tab-pane margin-top-30">
    <div class="row margin-bottom-10">
        <div class="col-md-12">
            <span class="col-md-12 border-bottom-1-green-seagreen">
                <div class="row">
                    <div class="col-md-6">
                        <strong>@lang('label.GROUP_CLONING')</strong>
                    </div>
                    <div class="col-md-6 text-right padding-bottom-1">
                        <button type="button" id="groupCloneShow" class="btn btn-xs green-sharp tooltips"
                                title="@lang('label.SHOW_GROUP_CLONE')">
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <button type="button" id="groupCloneHide" class="btn btn-xs green-sharp tooltips"
                                title="@lang('label.HIDE_GROUP_CLONE')">
                            <i class="fa fa-caret-right"></i>
                        </button>
                    </div>
                </div>
            </span>
        </div>
    </div>
    <div class="row" id="showGroupCloneSection">
        <!--Start Check Group Clone--> 
        <div class = "form-group" id="hasSubEvent">
            <?php
            $checked = '';
            if (!empty($cloneSubEventIds)) {
                $checked = 'checked';
            }
            ?>
            <label class = "control-label col-md-4"></label>
            <div class = "col-md-8 margin-top-8">
                <div class="md-checkbox">
                    {!! Form::checkbox('has_group_cloning',1,null,['id' => 'checkGroupCloning', 'class'=> 'md-check has-group-cloning', $checked]) !!}

                    <label for="checkGroupCloning">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span>
                    </label>
                    <span class="">@lang('label.PUT_TICK_TO_CLONE_GROUP_SELECTIONS')</span>
                </div>
            </div>
        </div>
        <!--End Check Group Clone--> 

        <!--Event Select-->
        <div class="form-group cloneEventMulti">
            <label class="control-label col-md-4" for="cloneSubEventId">@lang('label.SUB_EVENT') :<span class="text-danger"> *</span></label>
            <div class="col-md-4" id ="showSubEvent">
                {!! Form::select('clone_sub_event_id[]', $cloneSubEventList, $cloneSubEventIds,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'cloneSubEventId', 'data-width' => '100%']) !!}
                <span class="text-danger">{{ $errors->first('clone_sub_event_id') }}</span>
            </div>
        </div>
    </div>
</div>
<!--End:: Group Cloning -->
@endif

<!--Start: Save CM & DS-->
@if($eventAssessmentMarkingDataArr->isEmpty())
<div class = "row">
    <div class = "text-centre col-md-12 margin-top-30">
        <button class = "cm-ds-list-submit btn btn-circle green" type="button">
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href = "{{ URL::to('markingGroup') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>
@endif
<!--End: Save CM & DS-->


<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
    var options = {
        closeButton: true,
        debug: false,
        positionClass: "toast-bottom-right",
        onclick: null
    };


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

    //ds selection toggle
    $('#dsSelectionShow').hide();
    $(document).on("click", "#dsSelectionShow", function (e) {
        e.stopImmediatePropagation();
        $('#showDsSelectionSection').show(300);
        $('#dsSelectionHide').show();
        $('#dsSelectionShow').hide();

    });
    $(document).on("click", "#dsSelectionHide", function (e) {
        e.stopImmediatePropagation();
        $('#showDsSelectionSection').hide(300);
        $('#dsSelectionShow').show();
        $('#dsSelectionHide').hide();

    });

    //group cloning toggle
    $('#groupCloneShow').hide();
    $(document).on("click", "#groupCloneShow", function (e) {
        e.stopImmediatePropagation();
        $('#showGroupCloneSection').show(300);
        $('#groupCloneHide').show();
        $('#groupCloneShow').hide();

    });
    $(document).on("click", "#groupCloneHide", function (e) {
        e.stopImmediatePropagation();
        $('#showGroupCloneSection').hide(300);
        $('#groupCloneShow').show();
        $('#groupCloneHide').hide();

    });

    //event multi select show hide
<?php if (empty($cloneSubEventIds)) { ?>
        $('.cloneEventMulti').hide();
<?php } ?>

    $(document).on('click', '#checkGroupCloning', function () {
        if (this.checked == false) {
            $('.cloneEventMulti').hide(300);
        } else {
            $('.cloneEventMulti').show(300);
        }
    });
    //Start:: Multiselect Event
    var eventAllSelected = false;
    $('#cloneSubEventId').multiselect({
        numberDisplayed: 0,
        includeSelectAllOption: true,
        buttonWidth: 'inherit',
        maxHeight: 250,
        nonSelectedText: "@lang('label.SELECT_SUB_EVENT_OPT')",
        enableCaseInsensitiveFiltering: true,
        onSelectAll: function () {
            eventAllSelected = true;
        },
        onChange: function () {
            eventAllSelected = false;
        }
    });
    //End:: Multiselect Event



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
                    "<p><strong><i class='fa fa-bell-o fa-fw'></i> {!! __('label.NO_CM_IS_SELECTED_FOR_THIS_MARKING_GROUP_YET') !!}</strong></p>" +
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



//  Start:: Set DS Remove
    //row remove
    $(document).on('click', '.remove-selected-ds', function () {

        $(this).parent().parent().remove();
        var removeDsId = $(this).attr('data-id');

        $('#dsSelected_' + removeDsId).remove();
        $('#selectedDsId_' + removeDsId).remove();
        slCounter('ds');

        // if all DS are removed
        if ($('.remove-selected-ds').length == 0) {
            $(".selected-ds-list").html("<div class='col-md-12'>" +
                    "<div class='alert alert-danger alert-dismissable'>" +
                    "<p><strong><i class='fa fa-bell-o fa-fw'></i> {!! __('label.NO_DS_IS_SELECTED_FOR_THIS_MARKING_GROUP_YET') !!}</strong></p>" +
                    "</div>" +
                    "</div>");
        }

        $(".ds-select-" + removeDsId).prop("checked", false);

        $('.ds-list-filterable').each(function () {
            $(this).find('.ds-select-' + removeDsId).prop('checked', false);
            var checkedDsLength = $(this).find('.ds-select:checked').length;
            var dsLength = $(this).find('.ds-select').length;
            if (checkedDsLength != dsLength) {
                $(this).find('.checked-all').prop('checked', false);
            }

        });
    });


//  End:: Set DS Remove
});


function slCounter(authority) {
    var sl = 0;
    $('.initial-serial-' + authority).each(function () {
        sl++;
        $(this).text(sl);
    });
    $('.selected-' + authority + '-no').text(sl);
}

$(function () {
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

//    Start:: CHECK ALL DS
<?php if (!$targetArrDs->isEmpty()) { ?>
        var submitFromDs = '<?php echo $submitFromDs; ?>';
        // this code for  database 'check all' if all checkbox items are checked
        if ($('.ds-select:checked').length == $('.ds-select').length) {
            $('#checkedAll' + submitFromDs)[0].checked = true; //change 'check all' checked status to true
        }

        $("#checkedAll" + submitFromDs).change(function () {
            var selectionClass = $(this).attr('data-class-initial');
            if (this.checked) {
                $(".ds-select-type-" + selectionClass).each(function () {
                    var dsId = $(this).val();
                    if (!this.hasAttribute("disabled")) {
                        this.checked = true;
                        allSameDsSelected(dsId, true);
                    }
                });
            } else {
                $(".ds-select-type-" + selectionClass).each(function () {
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



function allSameCmSelected(cmId, stat) {
    $('.cm-list-filterable').each(function () {
        $(this).find('.cm-select-' + cmId).prop('checked', stat);

    });
}
//    End:: CHECK ALL CM


function allSameDsSelected(dsId, stat) {
    $('.ds-list-filterable').each(function () {
        $(this).find('.ds-select-' + dsId).prop('checked', stat);

    });
}
//    End:: CHECK ALL  DS

</script>
