
@if(sizeof($eventList) > 1)
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
        <div class="col-md-4">
            {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
        </div>
    </div>
</div>
@else
<div class="alert alert-danger alert-dismissable col-md-offset-2 col-md-7">
    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_COURSE') !!}</strong></p>
</div>
@endif
