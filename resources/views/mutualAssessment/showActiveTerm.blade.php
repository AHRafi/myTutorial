@if(!empty($activeTerm))
<div class="form-group">
    <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        <div class="control-label pull-left"><strong>{{$activeTerm->name}}</strong></div>
        {!! Form::hidden('term_id', $activeTerm->id, ['id'=>'termId']) !!}
    </div>
</div>
@if(sizeof($eventsList)>1)
<div class="form-group">
    <label class="control-label col-md-4" for="maEventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        {!! Form::select('ma_event_id', $eventsList, null, ['class' => 'form-control js-source-states', 'id' => 'maEventId']) !!}
    </div>
</div>
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_TERM') !!}</strong></p>
    </div>
</div>
@endif
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_ACTIVE_TERM_FOUND') !!}</strong></p>
    </div>
</div>
@endif
