<div class="form-group">
    <label class="control-label col-md-4" for="dteBrId">@lang('label.DTE_BR') :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        {!! Form::select('dte_br_id', $dteBrList, null, ['class' => 'form-control js-source-states', 'id' => 'dteBrId']) !!}
    </div>
</div>
