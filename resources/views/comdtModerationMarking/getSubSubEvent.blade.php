
<div class="form-group">
    <label class="control-label col-md-4" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        {!! Form::select('sub_sub_event_id', $subSubEventList, null, ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']) !!}
    </div>
</div>

