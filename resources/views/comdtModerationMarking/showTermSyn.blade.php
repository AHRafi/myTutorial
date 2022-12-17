
@if(!empty($termInfo))
@if(!empty($synList))
<div class="form-group">
    <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">{!! $termInfo->name !!}</div>
</div>
{!! Form::hidden('term_id',$termInfo->id,['id'=>'termId'])!!}
<div class="form-group">
    <label class="control-label col-md-4" for="synId">@lang('label.SYN') :<span class="text-danger"> *</span></label>
    <div class="col-md-4">
        {!! Form::select('syn_id', ['0' => __('label.SELECT_SYN_OPT')] + $synList, null,  ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
    </div>
</div>
@else
<div class="row">
    <div class="col-md-offset-2 col-md-7">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_SYN_IS_ASSIGNED_FOR_MARKING_DS_TO_SYN') !!}</strong></p>
        </div>
    </div>
</div>
@endif
@else
<div class="row">
    <div class="col-md-offset-2 col-md-7">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_ACTIVE_TERM_FOUND') !!}</strong></p>
        </div>
    </div>
</div>
@endif