@if(sizeof($eventGroupList) > 1)
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="eventGroupId">@lang('label.EVENT_GROUP') :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            {!! Form::select('event_group_id', $eventGroupList, null, ['class' => 'form-control js-source-states', 'id' => 'eventGroupId']) !!}
        </div>
    </div>
</div>
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_GROUP_FOUND') !!}</strong></p>
    </div>
</div>
@endif
