<label class="control-label col-md-4" for="subSubSubEventId">@lang('label.SUB_SUB_SUB_EVENT') :@if(sizeof($subSubSubEventList) > 1)<span class="text-danger"> *</span>@endif</label>
<div class="col-md-8">
    {!! Form::select('sub_sub_sub_event_id', $subSubSubEventList, Request::get('sub_sub_sub_event_id'),  ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId', 'data-width' => '100%']) !!}
    {!! Form::hidden('has[sub_sub_sub_event]', sizeof($subSubSubEventList) > 1 ? 1 : 0) !!}
    <span class="text-danger">{{ $errors->first('sub_sub_sub_event_id') }}</span>
</div>