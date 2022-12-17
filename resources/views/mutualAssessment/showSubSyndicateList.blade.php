@if(sizeof($subSyndicateList) > 1)
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-5" for="courseId">@lang('label.SUB_SYNDICATE') :<span class="text-danger"> *</span></label>
        <div class="col-md-7">
            {!! Form::select('sub_syn_id', $subSyndicateList, null, ['class' => 'form-control js-source-states sub-syn', 'id' => 'subSyndicateId']) !!}
        </div>
    </div>
</div>
@endif
