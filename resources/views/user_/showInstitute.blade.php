<div class="form-group">
    <label class="control-label col-md-4" for="instituteId">@lang('label.INSTITUTE') :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        {!! Form::select('institute_id', $instituteList, null, ['class' => 'form-control js-source-states', 'id' => 'instituteId']) !!}
    </div>
</div>
