@if(sizeof($factorList) > 1)
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="factorId">@lang('label.FACTOR') :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            {!! Form::select('factor_id', $factorList, null, ['class' => 'form-control js-source-states', 'id' => 'factorId']) !!}
        </div>
    </div>
</div>
@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_MUTUAL_ASSESSMENT_EVENT_FOUND') !!}</strong></p>
    </div>
</div>
@endif