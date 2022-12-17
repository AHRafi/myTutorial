<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="eventGroupId">@lang('label.EVENT_GROUP') :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            {!! Form::select('event_group_id', $eventGroupList, Request::get('event_group_id'), ['class' => 'form-control js-source-states', 'id' => 'eventGroupId']) !!}
            <span class="text-danger">{{ $errors->first('event_group_id') }}</span>
        </div>
    </div>
</div>
