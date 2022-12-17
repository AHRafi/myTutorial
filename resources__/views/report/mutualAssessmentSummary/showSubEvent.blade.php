
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger"> {{sizeof($subEventList) > 1 ? '*' : ''}}</span></label>
        <div class="col-md-7">
            {!! Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
            <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
        </div>
    </div>
</div>
{!! Form::hidden('has_sub_event', sizeof($subEventList) > 1 ? 1 : 0) !!}