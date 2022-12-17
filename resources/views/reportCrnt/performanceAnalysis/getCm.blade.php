<?php $selectedCmList = !empty(Request::get('cm_id')) ? explode(',', Request::get('cm_id')) : []; ?>
{!! Form::select('cm_id[]', $cmList, $selectedCmList, ['multiple' => 'multiple', 'class' => 'form-control', 'id' => 'cmId']) !!}
<span class="text-danger">{{ $errors->first('cm_id') }}</span>