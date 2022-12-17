{!! Form::select('syn_id', $synList, null,  ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
@if(sizeof($synList) < 2)
<span class="text-danger">@lang('label.NO_SYN_IS_ASSIGNED_FOR_DS_TO_SYN')</span>
@endif