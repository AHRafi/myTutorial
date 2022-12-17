<div class="form-group">
    <label class="control-label col-md-4" for="dsId">@lang('label.DS') :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        {!! Form::select('ds_id', $dsList, !empty($prevDeligationInfo->ds_id) ? $prevDeligationInfo->ds_id : null,  ['class' => 'form-control js-source-states', 'id' => 'dsId']) !!}
        <span class="text-danger">{{ $errors->first('ds_id') }}</span>
    </div>
</div>
<div class="form-group margin-top-35">
    <div class="col-md-12 text-center">
        <button class="btn btn-circle red-soft deligate-account"type="button">
            <i class="fa fa-gears"></i> @lang('label.DELIGATE_ACCOUNT')
        </button>&nbsp;&nbsp;
        @if(!empty($prevDeligationInfo->ds_id))
        <button class="btn btn-circle red-mint cancel-deligation" type="button">
            <i class="fa fa-times-circle"></i> @lang('label.CANCEL_DELIGATION')
        </button>
        @endif
    </div>
</div>
