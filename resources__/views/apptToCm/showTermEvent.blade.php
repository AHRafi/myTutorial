@if(!empty($activeTermInfo))

<div class="form-group">
    <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        <div class="control-label pull-left"> <strong> {{$activeTermInfo->name}} </strong></div>
    </div>
    {!! Form::hidden('term_id', $activeTermInfo->id, ['id' => 'termId']) !!}
</div> 

@if(sizeof($eventList) > 1)
<div class="form-group">
    <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
    </div>
</div>

@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_ACTIVE_EVENT_FOUND')</p>
    </div>
</div>
@endif

@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_ACTIVE_TERM_FOUND')</p>
    </div>
</div>
@endif