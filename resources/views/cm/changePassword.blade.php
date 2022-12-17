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


                        <!--div class="form-group">
    <label class="control-label col-md-4" for="currentPassword">@lang('label.CURRENT_PASSWORD') :<span class="text-danger"> *</span></label>
    <div class="col-md-8">
        {!! Form::password('current_password', ['id'=> 'currentPassword', 'class' => 'form-control']) !!} 
        <span class="text-danger">{{ $errors->first('current_password') }}</span>
                                        
    </div>
</div-->

                        <div class="form-group">
                            <label class="control-label col-md-4" for="password">@lang('label.NEW_PASSWORD') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::password('password', ['id'=> 'password', 'class' => 'form-control']) !!} 
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
                                {!! Form::password('conf_password', ['id'=> 'confPassword', 'class' => 'form-control']) !!} 
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

    $(function() {
    @if(in_array(Auth::user()-> group_id, ['1', '2', '3']))

            $(document).on('change', '#groupId', function() {
    var groupId = $('#groupId').val();
    if (groupId == '0' || groupId == '1' || groupId == '2' || groupId == '3' || groupId == '4') {
    $('#showInstitueDteBr').html('');
    return false;
    } else if (groupId == '5' || groupId == '6' || groupId == '7') {
    $.ajax({
    url: "{{URL::to('user/getInstitue')}}",
            type: 'POST',
            dataType: 'json',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
            group_id: groupId
            },
            beforeSend: function() {
            App.blockUI({boxed: true});
            },
            success: function(res) {
            $('#showInstitueDteBr').html(res.html);
            App.unblockUI();
            $(".js-source-states").select2();
            },
    });
    } elseif (groupId == '8') {
    $.ajax({
    url: "{{URL::to('user/getDteBr')}}",
            type: 'POST',
            dataType: 'json',
             headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
            group_id: groupId
            },
            beforeSend: function() {
            App.blockUI({boxed: true});
            },
            success: function(res) {
            $('#showInstitueDteBr').html(res.html);
            App.unblockUI();
            $(".js-source-states").select2();
            },
    });
    }
    });
    @endif

            $(document).on('change', '#serviceId', function() {
    var serviceId = $('#serviceId').val();
    $.ajax({
    url: "{{URL::to('user/getServiceWiseRankAppPnp')}}",
            type: 'POST',
            dataType: 'json',
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
            service_id: serviceId
            },
            beforeSend: function() {
            App.blockUI({boxed: true});
            },
            success: function(res) {

            $('#rankAppointmentHolder').html(res.data);
            App.unblockUI();
            $(".js-source-states").select2();
            },
    });
    });
    });
</script>
@stop