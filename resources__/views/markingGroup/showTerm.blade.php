@if(sizeof($termList) > 1)
@if(!empty($eventGroupList))
<div class="form-group">
    <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        {!! Form::select('term_id', $termList, null, ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
    </div>
</div>
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_GROUP_IS_ASSIGNED_TO_THIS_COURSE') !!}</strong></p>
    </div>
</div>
@endif
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_TERM_IS_INITIATED_TO_THIS_COURSE') !!}</strong></p>
    </div>
</div>
@endif