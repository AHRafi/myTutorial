<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="subSubSubEventId">@lang('label.SUB_SUB_SUB_EVENT') :<span class="text-danger"> {{sizeof($subSubSubEventList) > 1 ? '*' : ''}}</span></label>
        <div class="col-md-7">
            {!! Form::select('sub_sub_sub_event_id', $subSubSubEventList, Request::get('sub_sub_sub_event_id'), ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId']) !!}
            <span class="text-danger">{{ $errors->first('sub_sub_sub_event_id') }}</span>
        </div>
    </div>
</div>
{!! Form::hidden('has_sub_sub_sub_event', sizeof($subSubSubEventList) > 1 ? 1 : 0) !!}
