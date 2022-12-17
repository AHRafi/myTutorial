<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="dsId">@lang('label.DS') :<span class="text-danger"></span></label>
        <div class="col-md-7">
            {!! Form::select('ds_id', $dsList, null,  ['class' => 'form-control js-source-states', 'id' => 'dsId']) !!}

        </div>
    </div>
</div>