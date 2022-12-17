
<div class="form-group">
    <label class="control-label col-md-4" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        {!! Form::select('sub_event_id', $subEventList, null, ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
    </div>
</div>
{!! Form::hidden('required_event[sub]', 1) !!}
