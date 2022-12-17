
@if(!empty($eventList))
<label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
<div class="col-md-4">
    {!! Form::select('event_id', ['0' => __('label.SELECT_EVENT_OPT')] + $eventList, null,  ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
</div>
@else
<div class="row">
    <div class="col-md-offset-2 col-md-7">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_IS_ASSIGNED_TO_THE_TERM') !!}</strong></p>
        </div>
    </div>
</div>
@endif