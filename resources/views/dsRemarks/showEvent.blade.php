<div class="form-group">
    <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> </span></label>
    <div class="col-md-4">
        {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
        @if(sizeof($eventList) <= 1)
        <span class="text-danger">{!! __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_COURSE') !!}</span>
        @endif
    </div>
</div>
