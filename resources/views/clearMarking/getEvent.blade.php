<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="eventId">@lang('label.EVENT') :<span class="text-danger"></span></label>
        <div class="col-md-7">
            {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
        </div>
    </div>
</div>