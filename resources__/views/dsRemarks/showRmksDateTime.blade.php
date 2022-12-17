<div class = "form-group">
    <label class = "control-label col-md-4" for="date">@lang('label.DATE') :<span class="text-danger hide-mandatory-sign"> *</span></label>
    <div class="col-md-4">
        <div class="input-group date datepicker2">
            {!! Form::text('date', null, ['id'=> 'date', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
            <span class="input-group-btn">
                <button class="btn default reset-date" type="button" remove="date">
                    <i class="fa fa-times"></i>
                </button>
                <button class="btn default date-set" type="button">
                    <i class="fa fa-calendar"></i>
                </button>
            </span>
        </div>
        <span class="text-danger">{{ $errors->first('date') }}</span>
    </div>         
</div>

<div class = "form-group">
    <label class = "control-label col-md-4" for="commisioningDate">@lang('label.REMARKS') :<span class="text-danger hide-mandatory-sign"> *</span></label>
    <div class="col-md-4">
        {!! Form::textarea('remarks', null, ['id'=> 'remarks', 'class' => 'form-control','cols'=>'20','rows' => '3']) !!}
    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-4 col-md-8">
            <button class="btn btn-circle green" type="submit">
                <i class="fa fa-check"></i> @lang('label.SUBMIT')
            </button>
            <a href="dsRemarks" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
        </div>
    </div>
</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>