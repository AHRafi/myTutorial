@if (!$targetArr->isEmpty())
<div class="row">
<!--    @if(!empty($eventAssessmentMarkingData))
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.MARKING_HAS_ALREADY_BEEN_STARTED') !!}</strong></p>
        </div>
    </div>
    @endif-->
    <div class="col-md-9">
        <span class="label label-sm label-blue-steel">
            @lang('label.TOTAL_NO_OF_SUB_EVENTS'):&nbsp;{!! !$targetArr->isEmpty() ? sizeOf($targetArr) : 0 !!}
        </span>&nbsp;
        <span class="label label-sm label-green-seagreen">
            @lang('label.TOTAL_NO_OF_ASSIGNED_SUB_EVENTS'):&nbsp;{!! !empty($prevDataList) ? sizeOf($prevDataList) : 0 !!}
        </span>&nbsp;
        <button class="label label-sm label-purple btn-label-groove tooltips" href="#modalAssignedSubEvent" id="assignedSubEvent"  data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_SUB_EVENT')">
            @lang('label.SUB_EVENT_ASSIGNED_TO_THIS_TERM'):&nbsp;{!! !empty($prevTermToSubEventList) ? sizeOf($prevTermToSubEventList) : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>
        </button>
    </div>

    @if(empty($eventAssessmentMarkingData))
    <div class="col-md-3 text-right">
        <button class="btn green btn-danger tooltips" type="button" id="buttonDelete" >
            <i class="fa fa-trash"></i> &nbsp;@lang('label.DELETE_SUB_EVENT')
        </button>
    </div>
    @endif
</div>
<div class="row margin-top-10">
    <div class="col-md-12 table-responsive">
        <?php
        $disabledEAM = !empty($eventAssessmentMarkingData) ? 'disabled' : '';
        ?>
        <table class="table table-bordered table-hover" id="dataTable">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="vcenter" width="15%">
                        <div class="md-checkbox has-success">
                            {!! Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check', $disabledEAM]) !!}
                            <label for="checkAll">
                                <span class="inc"></span>
                                <span class="check mark-caheck"></span>
                                <span class="box mark-caheck"></span>
                            </label>&nbsp;&nbsp;
                            <span class="bold">@lang('label.CHECK_ALL')</span>
                        </div>
                    </th>
                    <th class="vcenter">@lang('label.SUB_EVENT')</th>
                    <th class="vcenter text-center">@lang('label.HAS_SUB_SUB_EVENT')</th>
                    <th class="vcenter">@lang('label.ASSIGNED_TERM')</th>

                </tr>
            </thead>
            <tbody>
                @php $sl = 0 @endphp
                @foreach($targetArr as $target)
                <?php
                $checked = '';
                $disabled = '';
                $title = '';
                $class = 'term-to-sub-event';
                if (!empty($prevTermToSubEventList)) {
                    $checked = array_key_exists($target->id, $prevTermToSubEventList) ? 'checked' : '';

//                    if (!empty($prevTermToSubEventList[$target->id]) && ($request->term_id != $prevTermToSubEventList[$target->id])) {
//                        if ($target->has_sub_sub_event == '0') {
//                            $disabled = 'disabled';
//                            $term = !empty($prevTermToSubEventList[$target->id]) && !empty($termList[$prevTermToSubEventList[$target->id]]) ? $termList[$prevTermToSubEventList[$target->id]] : '';
//                            $title = __('label.ALREADY_ASSIGNED_TO_THIS_TERM', ['term' => $term]);
//                        }
//                    }
                }

                if (!empty($hasChild)) {
                    $disabled = '';
                    $title = '';
                    if (array_key_exists($target->id, $hasChild)) {
                        $disabled = 'disabled';
                        $title = __('label.SUB_SUB_EVENT_OF_THE_SUB_EVENT_HAS_BEEN_ASSIGNED_TO_TERM');
                    }
                }
                
                if (!empty($hasSubSubEvent[$target->id]) && ($request->term_id != $hasSubSubEvent[$target->id])) {
                    if ($target->has_sub_sub_event == '0') {
                        $disabled = 'disabled';
                        $term = !empty($hasSubSubEvent[$target->id]) && !empty($termList[$hasSubSubEvent[$target->id]]) ? $termList[$hasSubSubEvent[$target->id]] : '';
                        $title = __('label.ALREADY_ASSIGNED_TO_THIS_TERM', ['term' => $term]);
                    }
                }
                
                if (!empty($eventAssessmentMarkingData)) {
                    if (array_key_exists($target->id, $eventAssessmentMarkingData)) {
                        $disabled = 'disabled';
                        $title = __('label.MARKING_HAS_ALREADY_BEEN_STARTED');
                    }
                }

                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="vcenter">
                        <div class="md-checkbox has-success tooltips" title="{!!$title!!}" >
                            {!! Form::checkbox('sub_event_id['.$target->id.']',$target->id, $checked, ['id' => $target->id, 'data-id'=>$target->id,'class'=> 'md-check '.$class, $disabled]) !!}

                            @if (($disabled == 'disabled') && ($checked == 'checked') )
                            {!! Form::hidden('sub_event_id['.$target->id.']', $target->id) !!}
                            @endif

                            <label for="{!! $target->id !!}">
                                <span class="inc"></span>
                                <span class="check mark-caheck tooltips"></span>
                                <span class="box mark-caheck tooltips"></span>
                            </label>
                        </div>
                    </td>
                    <td class="vcenter">{!! $target->event_code!!}</td>
                    <td class="text-center vcenter">
                        @if($target->has_sub_sub_event == '1')
                        <span class="label label-sm label-success">@lang('label.YES')</span>
                        @else
                        <span class="label label-sm label-warning">@lang('label.NO')</span>
                        @endif
                    </td>
                    <td>
                        @if(!empty($prevDataList[$target->id]))
                        @foreach($prevDataList[$target->id] as $termId)
                        {!! isset($termList[$termId])?$termList[$termId].' ':''!!}
                        @endforeach
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- if submit wt chack Start -->
<div class = "form-actions">
    <div class = "col-md-offset-4 col-md-8">
        <button class = "button-submit btn btn-circle green" type="button">
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href = "{{ URL::to('termToSubEvent') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>

@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_SUB_EVENT_FOUND')</p>
    </div>
</div>
@endif
<!-- if submit wt chack End -->
<script type="text/javascript">
//    CHECK ALL
    $(document).ready(function () {
        //'check all' change
        $(document).on('click', '#checkAll', function () {
            if (this.checked) {
                $('.term-to-sub-event').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
            } else {
                $(".term-to-sub-event").removeAttr('checked');
            }
        });

        $(document).on('click', '.term-to-sub-event', function () {
            allCheck();
        });

<?php if (!$targetArr->isEmpty()) { ?>
            allCheck();
<?php } ?>
    });


    function allCheck() {

        if ($('.term-to-sub-event:checked').length == $('.term-to-sub-event').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
//    CHECK ALL
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>