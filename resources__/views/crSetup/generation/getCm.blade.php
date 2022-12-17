@if(!empty($assessmentActDeact))
<div class="form-group">
    <label class="control-label col-md-4" for="cmId">@lang('label.CM') :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        {!! Form::select('cm_id', $cmArr, Request::get('cm_id'), ['class' => 'form-control js-source-states', 'id' => 'cmId']) !!}
        <span class="text-danger">{{ $errors->first('cm_id') }}</span>
    </div>
</div>
@endif