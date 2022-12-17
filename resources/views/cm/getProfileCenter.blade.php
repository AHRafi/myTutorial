<div class="form-group">
    <label class="control-label col-md-4" for="centerId">@lang('label.CENTER') :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        {!! Form::select('center_id', $centerList, null, ['class' => 'form-control js-source-states', 'id' => 'centerId']) !!}
        <span class="text-danger">{{ $errors->first('center_id') }}</span>
    </div>
</div>