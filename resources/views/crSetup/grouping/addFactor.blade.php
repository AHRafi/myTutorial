<?php
$v4 = 'nf' . uniqid();
?>
<div class="factor">
    <div class="col-md-10 margin-top-10 padding-0">
        {!! Form::text('factor['.$markingSlabId.']['.$v4.']', null, ['id'=> 'factor_'.$markingSlabId.'_'.$v4
        ,'class' => 'form-control factor factor-'.$markingSlabId]) !!} 
    </div>
    <div class="col-md-2 margin-top-10 padding-left-right-5">
        <button class="btn btn-inline btn-danger remove-factor  tooltips"  title="@lang('label.REMOVE')" type="button">
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
