<div class="form-group">
	<label class="control-label col-md-4" for="rankId">@lang('label.RANK') :<span class="text-danger"> *</span></label>
	<div class="col-md-8">
		{!! Form::select('rank_id', $rankList, null, ['class' => 'form-control js-source-states', 'id' => 'rankId']) !!}
	</div>
</div>


<div class="form-group">
	<label class="control-label col-md-4" for="appointmentId">@lang('label.APPOINTMENT') :<span class="text-danger"> *</span></label>
	<div class="col-md-8">
		{!! Form::select('appointment_id', $appointmentList, null, ['class' => 'form-control js-source-states', 'id' => 'appointmentId']) !!}
	</div>
</div>

<div class="form-group">
	<label class="control-label col-md-4" for="personalServiceNo">@lang('label.PERSONNEL_NO') :<span class="text-danger"> *</span></label>
	<div class="col-md-4">
		{!! Form::select('pnp_id', $pnpList, null, ['class' => 'form-control js-source-states', 'id' => 'pnpId']) !!}
	</div>
	<div class="col-md-4">
		{!! Form::text('personal_service_no', null, ['id'=> 'personalServiceNo', 'class' => 'form-control integer-only']) !!} 
	</div>
</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>