@if(sizeof($subSubEventList) > 1)
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="subSubEventId">@lang('label.GROUPING')&nbsp;@lang('label.SUB_SUB_EVENT') :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            {!! Form::select('sub_sub_event_id', $subSubEventList, null, ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']) !!}
        </div>
    </div>
</div>
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_SUB_SUB_EVENT_IS_ASSIGNED_TO_THIS_SUB_EVENT_OF_THIS_TERM_OF_THE_COURSE') !!}</strong></p>
    </div>
</div>
@endif