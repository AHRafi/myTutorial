<div class="form-group">
    <label class="control-label col-md-4" for="eventId">@lang('label.CM') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        {!! Form::select('cm_id', $cmList, null, ['class' => 'form-control js-source-states', 'id' => 'cmId']) !!}
        @if(sizeof($cmList) <= 1)
        <span class="text-danger">{!! __('label.NO_CM_IS_ASSIGNED_TO_THIS_COURSE') !!}</span>
        @endif
    </div>
</div>
