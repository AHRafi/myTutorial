{!! Form::select('cm_group_id[]', $cmGroupList, null,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'cmGroupId', 'data-width' => '100%']) !!}
<span class="text-danger">{{ $errors->first('cm_group_id') }}</span>