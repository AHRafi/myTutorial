{!! Form::select('cm_id[]', $cmArr, null,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'cmId', 'data-width' => '100%']) !!}
<span class="text-danger">{{ $errors->first('cm_id') }}</span>