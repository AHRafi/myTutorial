<label class="control-label col-md-4" for="subEventId">@lang('label.SUB_EVENT') :@if(sizeof($subEventList) > 1)<span class="text-danger"> *</span>@endif</label>
<div class="col-md-8">
    {!! Form::select('sub_event_id', $subEventList, Request::get('sub_event_id'),  ['class' => 'form-control js-source-states', 'id' => 'subEventId', 'data-width' => '100%']) !!}
    {!! Form::hidden('has[sub_event]', sizeof($subEventList) > 1 ? 1 : 0) !!}
    <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
</div>