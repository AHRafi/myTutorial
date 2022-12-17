
@if(!empty($termList))
<div class="form-group">
    <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        <div class="control-label pull-left"> <strong> {{$termList->name}} </strong></div>
    </div>
</div>
{!! Form::hidden('term_id',$termList->id,['id'=>'termId'])!!}
@if(sizeof($eventList) > 1)
<div class="form-group">
    <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
    </div>
</div>
@else
<div class="row">
    <div class="col-md-offset-2 col-md-7">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_TERM') !!}</strong></p>
        </div>
    </div>
</div>
@endif
@else
<div class="row">
    <div class="col-md-offset-2 col-md-7">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_ACTIVE_TERM_FOUND') !!}</strong></p>
        </div>
    </div>
</div>
@endif