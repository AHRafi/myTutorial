<div class="modal-content">
    <div class="modal-header clone-modal-header">
        <div class="col-md-7 text-right">
            <h4 class="modal-title">@lang('label.EDIT_BASIC_PROFILE')</h4>
        </div>
        <div class="col-md-5">
            <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        </div>
    </div>
    <div class="modal-body">

        <!--START :: ASSIGN OPPORTUNITY-->
        {!! Form::open(array('group' => 'form', 'url' => '', 'class' => 'form-horizontal', 'id' => 'opportunityCancelForm')) !!}
        {{csrf_field()}}
        {!! Form::hidden('user_id', $nameArr->id) !!}

        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commanding_officer_name">@lang('label.COMMANDING_OFFICER_NAME') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('commanding_officer_name', $nameArr->commanding_officer_name, ['id'=> 'commanding_officer_name', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('commanding_officer_name') }}</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commanding_officer_contact_no">@lang('label.COMMANDING_OFFICER_CONTACT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('commanding_officer_contact_no', $nameArr->commanding_officer_contact_no, ['id'=> 'commanding_officer_contact_no', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('commanding_officer_contact_no') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cancelRemarks">@lang('label.UNIT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('unit', $nameArr->unit_name, ['id'=> 'unit', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('unit') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="arms_service_name">@lang('label.ARMS_SERVICES') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('arms_service_name', $nameArr->arms_service_name, ['id'=> 'arms_service_name', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('arms_service_name') }}</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commisioning_date">@lang('label.COMMISSIONING_DATE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('commisioning_date', $nameArr->commisioning_date, ['id'=> 'commisioning_date', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('commisioning_date') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="formation_name">@lang('label.FORMATION') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('formation_name', $nameArr->formation_name, ['id'=> 'formation_name', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('formation_name') }}</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commisioning_date">@lang('label.ANTI_DATE_SENIORITY') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('anti_date_seniority', $nameArr->anti_date_seniority, ['id'=> 'commisioning_date', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('anti_date_seniority') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="formation_name">@lang('label.FORMATION') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('formation_name', $nameArr->formation_name, ['id'=> 'formation_name', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('formation_name') }}</span>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-6">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"> </div>
                        <div>
                            <span class="btn green-seagreen btn-outline btn-file">
                                <span class="fileinput-new"> Select image </span>
                                <span class="fileinput-exists"> Change </span>
                                {!! Form::file('photo', null, ['id'=> 'photo']) !!}
                            </span>
                            <a href="javascript:;" class="btn green-seagreen fileinput-exists" data-dismiss="fileinput"> Remove </a>
                        </div>
                    </div>
                    <div class="clearfix margin-top-10">
                        <span class="label label-danger">@lang('label.NOTE')</span> @lang('label.USER_IMAGE_FOR_IMAGE_DESCRIPTION')
                    </div>
                </div>
                
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commisioning_date">@lang('label.ANTI_DATE_SENIORITY') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('anti_date_seniority', $nameArr->anti_date_seniority, ['id'=> 'commisioning_date', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('anti_date_seniority') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="nationality">@lang('label.NATIONALITY') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('nationality', $nameArr->nationality, ['id'=> 'nationality', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('nationality') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="height-ft">@lang('label.HEIGHT_FT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('height', $nameArr->height, ['id'=> 'height', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('height') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="height_inch">@lang('label.HEIGHT_INCH') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('height_inch', $nameArr->height_inch, ['id'=> 'height_inch', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('height_inch') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="height-ft">@lang('label.WEIGHT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('weight', $nameArr->weight, ['id'=> 'weight', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('weight') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="birth_place">@lang('label.BIRTH_PLACE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('birth_place', $nameArr->birth_place, ['id'=> 'birth_place', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('birth_place') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="religion">@lang('label.RELIGION') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('religion_name', $nameArr->religion_name, ['id'=> 'religion_name', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('religion_name') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="medical_categorize">@lang('label.MEDICAL_CATEGORIZE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('medical_categorize', $nameArr->medical_categorize, ['id'=> 'medical_categorize', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('medical_categorize') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="religion">@lang('label.EMAIL') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('religion_name', $nameArr->email, ['id'=> 'email', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="phone">@lang('label.PHONE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-8">
                            {!! Form::text('phone', $nameArr->phone, ['id'=> 'phone', 'class' => 'form-control']) !!} 
                            <span class="text-danger">{{ $errors->first('phone') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="modal-footer">
        <!--        <button class="btn blue-steel btn-show-opportunity-details-block tooltips" type="button" data-placement="top" title="@lang('label.CLICK_TO_SHOW_OPPORTUNITY_DETAILS')">
                    @lang('label.SHOW_OPPORTUNITY_DETAILS')
                </button>
                <button class="btn purple-sharp btn-hide-opportunity-details-block tooltips" type="button" data-placement="top" title="@lang('label.CLICK_TO_HIDE_OPPORTUNITY_DETAILS')">
                    @lang('label.HIDE_OPPORTUNITY_DETAILS')
                </button>-->
        <button type="button" class="btn btn-info"  id="editStudentProfile">
            @lang('label.UPDATE_BASIC_PROFILE')
        </button>
        <button type="button" data-dismiss="modal" data-placement="top" class="btn dark btn-outline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

