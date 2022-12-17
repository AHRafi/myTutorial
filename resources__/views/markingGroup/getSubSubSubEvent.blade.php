@if (!$targetArr->isEmpty())
<div class="row">
    <div class="col-md-12">
        <span class="label label-sm label-blue-steel">
            @lang('label.TOTAL_NO_OF_SUB_SUB_SUB_EVENTS'):&nbsp;{!! !$targetArr->isEmpty() ? sizeOf($targetArr) : 0 !!}
        </span>&nbsp;
        <span class="label label-sm label-green-seagreen">
            @lang('label.TOTAL_NO_OF_ASSIGNED_SUB_SUB_SUB_EVENTS'):&nbsp;{!! !$prevDataArr->isEmpty() ? sizeOf($prevDataArr) : 0 !!}
        </span>
    </div>
</div>
<div class="row margin-top-10">
    <div class="col-md-12 table-responsive">
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
                    <th class="vcenter">@lang('label.SUB_SUB_SUB_EVENT')</th>
                    <!--<th class="vcenter">@lang('label.ASSIGNED_TERM')</th>-->

                </tr>
            </thead>
            <tbody>
                @php $sl = 0 @endphp
                @foreach($targetArr as $target)
                <?php
                $checked = '';
                $disabled = '';
                $title = '';
                $class = 'term-to-sub-sub-sub-event';
                if (!empty($prevTermToSubSubSubEventList)) {
                    $checked = array_key_exists($target->id, $prevTermToSubSubSubEventList) ? 'checked' : '';
                    if (!empty($prevTermToSubSubSubEventList[$target->id]) && ($request->term_id != $prevTermToSubSubSubEventList[$target->id])) {
                        $disabled = 'disabled';
                        $term = !empty($prevTermToSubSubSubEventList[$target->id]) && !empty($termList[$prevTermToSubSubSubEventList[$target->id]]) ? $termList[$prevTermToSubSubSubEventList[$target->id]] : '';
                        $title = __('label.ALREADY_ASSIGNED_TO_THIS_TERM', ['term' => $term]);
                    }
                }
                ?>
                <tr>
                    <td class="text-center vcenter">{!! ++$sl !!}</td>
                    <td class="vcenter">
                        <div class="md-checkbox has-success tooltips" title="{!!$title!!}" >
                            {!! Form::checkbox('sub_sub_sub_event_id['.$target->id.']',$target->id, $checked, ['id' => $target->id, 'data-id'=>$target->id,'class'=> 'md-check '.$class]) !!}

                            <label for="{!! $target->id !!}">
                                <span class="inc"></span>
                                <span class="check mark-caheck tooltips"></span>
                                <span class="box mark-caheck tooltips"></span>
                            </label>
                        </div>
                    </td>
                    <td class="vcenter">{!! $target->event_code!!}</td>
<!--                    <td>
                        @if(!empty($prevDataList[$target->id]))
                        @foreach($prevDataList[$target->id] as $termId)
                        {!! isset($termList[$termId])?$termList[$termId]:''!!}
                        @endforeach
                        @endif
                    </td>-->
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
        <a href = "{{ URL::to('termToSubSubSubEvent') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>

@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_SUB_SUB_SUB_EVENT_FOUND')</p>
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
                $('.term-to-sub-sub-sub-event').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
            } else {
                $(".term-to-sub-sub-sub-event").removeAttr('checked');
            }
        });

        $(document).on('click', '.term-to-sub-sub-sub-event', function () {
            allCheck();
        });
        
<?php if (!$targetArr->isEmpty()) { ?>
            allCheck();
<?php } ?>
    });


    function allCheck() {

        if ($('.term-to-sub-sub-sub-event:checked').length == $('.term-to-sub-sub-sub-event').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
//    CHECK ALL
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>