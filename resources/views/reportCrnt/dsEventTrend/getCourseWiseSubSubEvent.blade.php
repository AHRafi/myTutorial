<label class="control-label col-md-4" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :@if(sizeof($subSubEventList) > 1)<span class="text-danger"> *</span>@endif</label>
<div class="col-md-8">
    {!! Form::select('sub_sub_event_id', $subSubEventList, Request::get('sub_sub_event_id'),  ['class' => 'form-control js-source-states', 'id' => 'subSubEventId', 'data-width' => '100%']) !!}
    {!! Form::hidden('has[sub_sub_event]', sizeof($subSubEventList) > 1 ? 1 : 0) !!}
    <span class="text-danger">{{ $errors->first('sub_sub_event_id') }}</span>
</div>