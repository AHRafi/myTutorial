@extends('layouts.default.master')
@section('data_count')	
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user"></i>@lang('label.UPDATE_PASSWORD')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::open(array('group' => 'form', 'url' => 'changePassword', 'class' => 'form-horizontal')) !!}

            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">


<!--                        <div class="form-group">
                            <label class="control-label col-md-4" for="currentPassword">@lang('label.CURRENT_PASSWORD') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::password('current_password', ['id'=> 'currentPassword', 'class' => 'form-control']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default show-pass" type="button" id="showCrntPass">
                                            <i class="fa fa-eye" id="crntPassIcon"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('current_password') }}</span>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label class="control-label col-md-4" for="password">@lang('label.NEW_PASSWORD') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::password('password', ['id'=> 'password', 'class' => 'form-control']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default show-pass" type="button" id="showPass">
                                            <i class="fa fa-eye" id="passIcon"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                <div class="clearfix margin-top-10">
                                    <span class="label label-danger">@lang('label.NOTE')</span>
                                    @lang('label.COMPLEX_PASSWORD_INSTRUCTION')
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="confPassword">@lang('label.CONF_PASSWORD') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::password('conf_password', ['id'=> 'confPassword', 'class' => 'form-control']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default show-pass" type="button" id="showConPass">
                                            <i class="fa fa-eye" id="conPassIcon"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('conf_password') }}</span>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn btn-circle green" type="submit">
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href="{{ URL::to('/dashboard') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>	
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        //START::show pass
        $(document).on('click', '#showCrntPass', function () {
            $('#crntPassIcon').toggleClass("fa-eye fa-eye-slash");
            var input = $('#currentPassword');
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
        $(document).on('click', '#showPass', function () {
            $('#passIcon').toggleClass("fa-eye fa-eye-slash");
            var input = $('#password');
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
        $(document).on('click', '#showConPass', function () {
            $('#conPassIcon').toggleClass("fa-eye fa-eye-slash");
            var input = $('#confPassword');
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
        //END::show pass
    });
</script>
@stop