<?php
$v4 = 'nf' . uniqid();
?>
<div class="sentence">
    <div class="col-md-11 margin-top-10 padding-0">
        {!! Form::text('sentence['.$markingSlabId.']['.$v4.']', null, ['id'=> 'sentence_'.$markingSlabId.'_'.$v4
        ,'class' => 'form-control sentence sentence-'.$markingSlabId]) !!} 
    </div>
    <div class="col-md-1 margin-top-10 padding-left-right-5">
        <button class="btn btn-inline btn-danger remove-sentence  tooltips"  title="@lang('label.REMOVE')" type="button">
            <i class="fa fa-remove"></i>
        </button>
    </div>
</div>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
    $(".tooltips").tooltip();
});
</script>
