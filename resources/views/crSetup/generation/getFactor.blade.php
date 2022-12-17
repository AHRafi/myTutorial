<?php
$traitId = $request->trait_id;
$trait = !empty($request->trait_id) && !empty($traitList[$request->trait_id]) ? $traitList[$request->trait_id] : '';
$sId = $request->sentence;
?>
@if(!empty($sentenceArr[$sId]['format']))
{!! Form::hidden('sentence_format['.$traitId.']', $sentenceArr[$sId]['format'], ['id' => 'sentenceFormat_'.$traitId, 'data-trait-id' => $traitId]) !!}
@endif
@if(!empty($sentenceArr[$sId]['factor_format']))
<div class="col-md-12 margin-top-10">
    <span class="bold">@lang('label.FACTORS')</span>
</div>
@foreach($sentenceArr[$sId]['factor_format'] as $key => $info)
<?php $factor = json_encode($info); ?>
{!! Form::hidden('factor_format['.$traitId.']['.$key.']', $factor, ['class' => 'factor-format-'.$traitId, 'id' => 'factorFormat_'.$traitId.'_'.$key, 'data-trait-id' => $traitId, 'data-key' => $key]) !!}
@if($info['type'] == "select")
<div class="col-md-2 margin-top-10 width-180">
    <div class="input-group bootstrap-touchspin width-inherit">
        <span class="input-group-addon bootstrap-touchspin-prefix bold">{{$key}}</span>
        {!! Form::select('factor['.$traitId.']['.$key.']', $factorArr[$traitId], null, ['class' => 'form-control js-source-states width-inherit factor-'.$traitId, 'id' => 'factor_'.$traitId.'_'.$key, 'data-trait-id' => $traitId, 'data-key' => $key]) !!}
        <span class="input-group-addon bootstrap-touchspin-postfix invisible-addon"></span>
    </div>
</div>
@elseif($info['type'] == "text")
<div class="col-md-4 margin-top-10 width-450">
    <div class="input-group bootstrap-touchspin width-inherit">
        <span class="input-group-addon bootstrap-touchspin-prefix bold">{{$key}}</span>
        {!! Form::text('factor['.$traitId.']['.$key.']', null, ['class' => 'form-control text-width-100-per factor-'.$traitId, 'id' => 'factor_'.$traitId.'_'.$key, 'data-trait-id' => $traitId, 'data-key' => $key]) !!}
        <span class="input-group-addon bootstrap-touchspin-postfix invisible-addon"></span>
    </div>
</div>
@endif
@endforeach
<div class="col-md-12 margin-top-10">
    <div class="col-md-1 padding-0">
        <button class="btn btn-sm green-seagreen set-final-sentence tooltips" type="button" data-trait-id="{{$traitId}}"
                title="{{__('label.CLICK_HERE_TO_SET_FINAL_SENTENCE_FOR_TRAIT', ['trait' => $trait])}}">
            <i class="glyphicon glyphicon-arrow-right"></i>
        </button>
    </div>
    <div class="col-md-11 padding-0">
        {!! Form::text('final_sentence['.$traitId.']', null, ['class' => 'form-control text-width-100-per final-sentence', 'id' => 'finalSentence_'.$traitId, 'data-trait-id' => $traitId]) !!}
    </div>
</div>

@endif
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

