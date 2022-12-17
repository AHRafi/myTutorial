<!--Start:: CM Selection -->
@if(!$prevCrGen->isEmpty())
<div class = "row">
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.COURSE_REPORT_GENERATION_HAS_ALREADY_BEEN_STARTED') !!}</strong></p>
        </div>
    </div>
</div>
@endif
<?php $crGenStartDisabled = !$prevCrGen->isEmpty() ? 'disabled' : ''; ?>
<div class="row">
    <div class="col-md-12 text-center">
        <div class="md-radio-inline">
            <div class="md-radio">
                <?php
                $checked1 = empty($prevReflection) || (!empty($prevReflection->reflection_type) && $prevReflection->reflection_type == '1') ? true : false;
                $checked2 = !empty($prevReflection->reflection_type) && $prevReflection->reflection_type == '2' ? true : false;
                $checked3 = !empty($prevReflection->reflection_type) && $prevReflection->reflection_type == '3' ? true : false;
                $displayNone = !empty($prevReflection->reflection_type) && $prevReflection->reflection_type == '2' ? '' : 'display-none';
                ?>
                {!! Form::radio('reflection_type', '1', $checked1, ['id' => 'reflectionType1', 'class' => 'md-radiobtn md-reflection', 'data-val' => '1', $crGenStartDisabled]) !!}
                <label for="reflectionType1">
                    <span class="inc"></span>
                    <span class="check"></span>
                    <span class="box"></span>
                </label>
                <span class="bold">@lang('label.OVERALL_MKS_PERCENTAGE')</span>
            </div>
            <div class="md-radio">
                {!! Form::radio('reflection_type', '2', $checked2, ['id' => 'reflectionType2', 'class' => 'md-radiobtn md-reflection', 'data-val' => '2', $crGenStartDisabled]) !!}
                <label for="reflectionType2">
                    <span class="inc"></span>
                    <span class="check"></span>
                    <span class="box bold"></span>
                </label>
                <span class="bold">@lang('label.WT_BASED_CRITERIA')</span>
            </div>
            <div class="md-radio">
                {!! Form::radio('reflection_type', '3', $checked3, ['id' => 'reflectionType3', 'class' => 'md-radiobtn md-reflection', 'data-val' => '3', $crGenStartDisabled]) !!}
                <label for="reflectionType3">
                    <span class="inc"></span>
                    <span class="check"></span>
                    <span class="box bold"></span>
                </label>
                <span class="bold">@lang('label.MUTUAL_ASSESSEMNT_COOP')</span>
            </div>
        </div>
    </div>
</div>
<div class="row  margin-top-20 {{$displayNone}}">
    <div class="col-md-offset-3 col-md-6 wt-reflection">
        <div class="text-center">
            <span class="bold text-green-steel">@lang('label.PLEASE_CHOOSE_WT_BASED_CRITERIA_OR_EVENTS_FOR_MARKING_REFLECTION')</span>
        </div>
        <div class="margin-top-10 table-responsive max-height-500 webkit-scrollbar">
            <ul>
                <li>
                    <div class="md-checkbox has-success">
                        <?php $dsReflChecked = !empty($prevWtReflArr[1][0][0][0][0]) ? 'checked' : ''; ?>
                        {!! Form::checkbox('wt_reflection[1][0][0][0][0]',1, $dsReflChecked, ['id' => 'wtReflection_1_0_0_0_0'
                        , 'class'=> 'md-check ', $crGenStartDisabled]) !!}
                        <label for="{!! 'wtReflection_1_0_0_0_0' !!}">
                            <span class="inc"></span>
                            <span class="check mark-caheck"></span>
                            <span class="box mark-caheck"></span>
                        </label>
                        <span class="margin-left-14">@lang('label.DS_OBSN')</span>
                    </div>
                </li>
                @if(!empty($eventMksWtArr['mks_wt']))
                @foreach($eventMksWtArr['mks_wt'] as $eventId => $evInfo)
                <li>
                    <div class="md-checkbox has-success">
                        <?php $evReflChecked = !empty($prevWtReflArr[2][$eventId][0][0][0]) ? 'checked' : ''; ?>
                        {!! Form::checkbox('wt_reflection[2]['.$eventId.'][0][0][0]',1, $evReflChecked, ['id' => 'wtReflection_2_'.$eventId.'_0_0_0'
                        , 'class'=> 'md-check event wt-refl-2', 'data-event-id' => $eventId, $crGenStartDisabled]) !!}
                        <label for="{!! 'wtReflection_2_'.$eventId.'_0_0_0' !!}">
                            <span class="inc"></span>
                            <span class="check mark-caheck"></span>
                            <span class="box mark-caheck"></span>
                        </label>
                        <span class="margin-left-14">{!! !empty($eventMksWtArr['event'][$eventId]['name']) ? $eventMksWtArr['event'][$eventId]['name'] : '' !!}</span>
                    </div>
                    @if(!empty($evInfo))
                    <ul>
                        @foreach($evInfo as $subEventId => $subEvInfo)
                        @if(!empty($subEventId))
                        <li>
                            <div class="md-checkbox has-success">
                                <?php $subEvReflChecked = !empty($prevWtReflArr[2][$eventId][$subEventId][0][0]) ? 'checked' : ''; ?>
                                {!! Form::checkbox('wt_reflection[2]['.$eventId.']['.$subEventId.'][0][0]',1, $subEvReflChecked
                                , ['id' => 'wtReflection_2_'.$eventId.'_'.$subEventId.'_0_0'
                                , 'class'=> 'md-check sub-event wt-refl-2-'.$eventId. ' se-p-e-' . $eventId
                                , 'data-event-id' => $eventId, 'data-sub-event-id' => $subEventId
                                , $crGenStartDisabled]) !!}
                                <label for="{!! 'wtReflection_2_'.$eventId.'_'.$subEventId.'_0_0' !!}">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>
                                <span class="margin-left-14">{!! !empty($eventMksWtArr['event'][$eventId][$subEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId]['name'] : '' !!}</span>
                            </div>
                            @if(!empty($subEvInfo))
                            <ul>
                                @foreach($subEvInfo as $subSubEventId => $subSubEvInfo)
                                @if(!empty($subSubEventId))
                                <li>
                                    <div class="md-checkbox has-success">
                                        <?php $subSubEvReflChecked = !empty($prevWtReflArr[2][$eventId][$subEventId][$subSubEventId][0]) ? 'checked' : ''; ?>
                                        {!! Form::checkbox('wt_reflection[2]['.$eventId.']['.$subEventId.']['.$subSubEventId.'][0]',1, $subSubEvReflChecked
                                        , ['id' => 'wtReflection_2_'.$eventId.'_'.$subEventId.'_'.$subSubEventId.'_0'
                                        , 'class'=> 'md-check sub-sub-event wt-refl-2-'.$eventId.'-'.$subEventId. ' wt-refl-2-'.$eventId
                                        . ' sse-p-se-' .$eventId.'-'. $subEventId. ' sse-p-e-' . $eventId
                                        , 'data-event-id' => $eventId, 'data-sub-event-id' => $subEventId
                                        , 'data-sub-sub-event-id' => $subSubEventId, $crGenStartDisabled]) !!}
                                        <label for="{!! 'wtReflection_2_'.$eventId.'_'.$subEventId.'_'.$subSubEventId.'_0' !!}">
                                            <span class="inc"></span>
                                            <span class="check mark-caheck"></span>
                                            <span class="box mark-caheck"></span>
                                        </label>
                                        <span class="margin-left-14">{!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId]['name'] : '' !!}</span>
                                    </div>
                                    @if(!empty($subSubEvInfo))
                                    <ul>
                                        @foreach($subSubEvInfo as $subSubSubEventId => $subSubSubEvInfo)
                                        @if(!empty($subSubSubEventId))
                                        <li>
                                            <div class="md-checkbox has-success">
                                                <?php $subSubSubEvReflChecked = !empty($prevWtReflArr[2][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]) ? 'checked' : ''; ?>
                                                {!! Form::checkbox('wt_reflection[2]['.$eventId.']['.$subEventId.']['.$subSubEventId.']['.$subSubSubEventId.']',1, $subSubSubEvReflChecked
                                                , ['id' => 'wtReflection_2_'.$eventId.'_'.$subEventId.'_'.$subSubEventId.'_'.$subSubSubEventId
                                                , 'class'=> 'md-check sub-sub-sub-event wt-refl-2-'.$eventId.'-'.$subEventId
                                                . ' wt-refl-2-'.$eventId.'-'.$subEventId.'-'.$subSubEventId. ' wt-refl-2-'.$eventId
                                                . ' ssse-p-sse-'.$eventId.'-'.$subEventId.'-' . $subSubEventId 
                                                . ' ssse-p-se-' .$eventId.'-'. $subEventId. ' ssse-p-e-' . $eventId
                                                , 'data-event-id' => $eventId, 'data-sub-event-id' => $subEventId
                                                , 'data-sub-sub-event-id' => $subSubEventId, 'data-sub-sub-sub-event-id' => $subSubSubEventId
                                                , $crGenStartDisabled]) !!}
                                                <label for="{!! 'wtReflection_2_'.$eventId.'_'.$subEventId.'_'.$subSubEventId.'_'.$subSubSubEventId !!}">
                                                    <span class="inc"></span>
                                                    <span class="check mark-caheck"></span>
                                                    <span class="box mark-caheck"></span>
                                                </label>
                                                <span class="margin-left-14">{!! !empty($eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name']) ? $eventMksWtArr['event'][$eventId][$subEventId][$subSubEventId][$subSubSubEventId]['name'] : '' !!}</span>
                                            </div>
                                        </li>
                                        @endif
                                        @endforeach
                                    </ul>
                                    @endif
                                </li>
                                @endif
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    @endif
                </li>
                @endforeach
                @endif
            </ul>
        </div>
    </div>
</div>
@if($prevCrGen->isEmpty())
<div class="row  margin-top-20">
    <div class="col-md-12 text-center">
        
        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
            <i class="fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href="{{ URL::to('crFactorToTrait') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>
@endif


<!-- if submit wt chack End -->
<script type="text/javascript">
    $(function () {
        $(".md-reflection").on('click', function () {
            var val = $(this).attr('data-val');
            var wtReflBlock = $(".wt-reflection").parent();
            if (val == '2') {
                wtReflBlock.removeClass('display-none');
            } else {
                if (!wtReflBlock.hasClass('display-none')) {
                    wtReflBlock.addClass('display-none');
                }
            }
        });
        $(".event").on('click', function () {
            var eventId = $(this).attr('data-event-id');
            if (this.checked == true) {
                $(".wt-refl-2-" + eventId).prop('checked', true);
            } else {
                $(".wt-refl-2-" + eventId).prop('checked', false);
            }
        });
        $(".sub-event").on('click', function () {
            var eventId = $(this).attr('data-event-id');
            var subEventId = $(this).attr('data-sub-event-id');
            if (this.checked == true) {
                $(".wt-refl-2-" + eventId + "-" + subEventId).prop('checked', true);

            } else {
                $(".wt-refl-2-" + eventId + "-" + subEventId).prop('checked', false);
            }

            //checking and unchecking event through sub event
            if ($('.se-p-e-' + eventId + ':checked').length == $('.se-p-e-' + eventId).length) {
                $("#wtReflection_2_" + eventId + "_0_0_0").prop("checked", true);
            } else {
                $("#wtReflection_2_" + eventId + "_0_0_0").prop("checked", false);
            }
        });
        $(".sub-sub-event").on('click', function () {
            var eventId = $(this).attr('data-event-id');
            var subEventId = $(this).attr('data-sub-event-id');
            var subSubEventId = $(this).attr('data-sub-sub-event-id');
            if (this.checked == true) {
                $(".wt-refl-2-" + eventId + "-" + subEventId + "-" + subSubEventId).prop('checked', true);
            } else {
                $(".wt-refl-2-" + eventId + "-" + subEventId + "-" + subSubEventId).prop('checked', false);
            }

            //checking and unchecking sub event through sub sub sub event
            if ($('.sse-p-se-' + eventId + '-' + subEventId + ':checked').length == $('.sse-p-se-' + eventId + '-' + subEventId).length) {
                $("#wtReflection_2_" + eventId + "_" + subEventId + "_0_0").prop("checked", true);
            } else {
                $("#wtReflection_2_" + eventId + "_" + subEventId + "_0_0").prop("checked", false);
            }

            //checking and unchecking event through sub sub event
            if ($('.sse-p-e-' + eventId + ':checked').length == $('.sse-p-e-' + eventId).length) {
                $("#wtReflection_2_" + eventId + "_0_0_0").prop("checked", true);
            } else {
                $("#wtReflection_2_" + eventId + "_0_0_0").prop("checked", false);
            }
        });
        $(".sub-sub-sub-event").on('click', function () {
            var eventId = $(this).attr('data-event-id');
            var subEventId = $(this).attr('data-sub-event-id');
            var subSubEventId = $(this).attr('data-sub-sub-event-id');
            var subSubSubEventId = $(this).attr('data-sub-sub-sub-event-id');

            //checking and unchecking sub sub event through sub sub sub event
            if ($('.ssse-p-sse-' + eventId + '-' + subEventId + '-' + subSubEventId + ':checked').length == $('.ssse-p-sse-' + eventId + '-' + subEventId + '-' + subSubEventId).length) {
                $("#wtReflection_2_" + eventId + "_" + subEventId + "_" + subSubEventId + "_0").prop("checked", true);
            } else {
                $("#wtReflection_2_" + eventId + "_" + subEventId + "_" + subSubEventId + "_0").prop("checked", false);
            }

            //checking and unchecking sub event through sub sub sub event
            if ($('.ssse-p-se-' + eventId + '-' + subEventId + ':checked').length == $('.ssse-p-se-' + eventId + '-' + subEventId).length) {
                $("#wtReflection_2_" + eventId + "_" + subEventId + "_0_0").prop("checked", true);
            } else {
                $("#wtReflection_2_" + eventId + "_" + subEventId + "_0_0").prop("checked", false);
            }

            //checking and unchecking event through sub sub sub event
            if ($('.ssse-p-e-' + eventId + ':checked').length == $('.ssse-p-e-' + eventId).length) {
                $("#wtReflection_2_" + eventId + "_0_0_0").prop("checked", true);
            } else {
                $("#wtReflection_2_" + eventId + "_0_0_0").prop("checked", false);
            }
        });
    });
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>