
@if(sizeof($subEventList) > 1)
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            {!! Form::select('sub_event_id', $subEventList, null, ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
        </div>
    </div>
</div>
@else
<div class="alert alert-danger alert-dismissable col-md-offset-2 col-md-7">
    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_SUB_EVENT_IS_ASSIGNED_TO_THIS_EVENT') !!}</strong></p>
</div>
@endif
