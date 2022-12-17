<div class="form-group">
    <label class="control-label col-md-4" for="subSynId">@lang('label.SUB_SYN') :</label>
    <div class="col-md-8 show-sub-syn">
        {!! Form::select('sub_syn_id', $subSynList, Request::get('sub_syn_id'),  ['class' => 'form-control js-source-states', 'id' => 'subSynId']) !!}
    </div>
</div>