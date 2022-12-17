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
            @lang('label.TOTAL_NO_OF_EVENTS'):&nbsp;{!! !$targetArr->isEmpty() ? sizeOf($targetArr) : 0 !!}
        </span>&nbsp;
        <span class="label label-sm label-green-seagreen">
            @lang('label.TOTAL_NO_OF_ASSIGNED_EVENTS'):&nbsp;{!! !empty($prevDataList) ? sizeOf($prevDataList) : 0 !!}
        </span>&nbsp;
        <button class="label label-sm label-purple btn-label-groove tooltips" href="#modalAssignedEvent" id="assignedEvent"  data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_EVENT')">
            @lang('label.EVENT_ASSIGNED_TO_THIS_TERM'):&nbsp;{!! !empty($prevCourseWiseTermToEventList) ? sizeOf($prevCourseWiseTermToEventList) : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>
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
                            {!! Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check']) !!}
                            <label for="checkAll">
                                <span class="inc"></span>
                                <span class="check mark-caheck"></span>
                                <span class="box mark-caheck"></span>
                            </label>&nbsp;&nbsp;
                            <span class="bold">@lang('label.CHECK_ALL')</span>
                        </div>
                    </th>
                    <th class="vcenter">@lang('label.EVENT')</th>
                    <th class="vcenter text-center">@lang('label.HAS_SUB_EVENT')</th>
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
                $class = 'term-to-event';
                if (!empty($prevTermToEventList)) {
                    $checked = array_key_exists($target->id, $prevCourseWiseTermToEventList) ? 'checked' : '';
                    if (!empty($prevTermToEventList[$target->id]) && ($request->term_id != $prevTermToEventList[$target->id])) {
                        if ($target->has_sub_event == '0') {
                            $disabled = 'disabled';
                            $term = !empty($prevTermToEventList[$target->id]) && !empty($termList[$prevTermToEventList[$target->id]]) ? $termList[$prevTermToEventList[$target->id]] : '';
                            $title = __('label.ALREADY_ASSIGNED_TO_THIS_TERM', ['term' => $term]);
                        }
                    }
                }

                if (!empty($hasChild)) {
                    if (array_key_exists($target->id, $hasChild)) {
                        $disabled = 'disabled';
                        $title = __('label.SUB_EVENT_OF_THE_EVENT_HAS_BEEN_ASSIGNED_TO_TERM');
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
                            {!! Form::checkbox('event_id['.$target->id.']',$target->id, $checked, ['id' => $target->id, 'data-id'=>$target->id,'class'=> 'md-check '.$class,$disabled]) !!}

                            @if ($disabled == 'disabled' && $checked == 'checked')
                            {!! Form::hidden('event_id['.$target->id.']', $target->id) !!}
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
                        @if($target->has_sub_event == '1')
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
        <a href = "{{ URL::to('termToEvent') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>

@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_EVENT_FOUND')</p>
    </div>
</div>
@endif
<!-- if submit wt chack End -->
<script type="text/javascript">
//    CHECK ALL
    $(document).ready(function () {

        $('#checkAll').change(function () {  //'check all' change
            $('.term-to-event').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
        });
        $('.term-to-event').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
            }
            //check 'check all' if all checkbox items are checked
            if ($('.term-to-event:checked').length == $('.term-to-event').length) {
                $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
            }
        });

        //'check all' change
        $(document).on('click', '#checkAll', function () {
            if (this.checked) {
                $('.term-to-event').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
            } else {
                $(".term-to-event").removeAttr('checked');
            }
        });

        $(document).on('click', '.term-to-event', function () {
            allCheck();
        });


//        $(document).on('click', '.term', function () {
//            allCheck();
//        });
<?php if (!$targetArr->isEmpty()) { ?>
            allCheck();
<?php } ?>
    });


    function allCheck() {

        if ($('.term-to-event:checked').length == $('.term-to-event').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
//    CHECK ALL
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>