@extends('layouts.default.master')
@section('data_count')	
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user"></i>@lang('label.CREATE_USER')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::open(array('group' => 'form', 'url' => 'user', 'files'=> true, 'class' => 'form-horizontal')) !!}
            {!! Form::hidden('filter', Helper::queryPageStr($qpArr)) !!}
            {{csrf_field()}}

            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="groupId">@lang('label.USER_GROUP') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('group_id', $groupList, null, ['class' => 'form-control js-source-states', 'id' => 'groupId', 'autocomplete' => 'off']) !!}
                                <span class="text-danger">{{ $errors->first('group_id') }}</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-md-4" for="wingId">@lang('label.WING') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('wing_id', $wingList, null, ['class' => 'form-control js-source-states', 'id' => 'wingId']) !!}
                                <span class="text-danger">{{ $errors->first('wing_id') }}</span>
                            </div>
<!--                            <div class="col-md-8 margin-top-8 bold">
                                {!! $wingList[1] !!}
                                {!! Form::hidden('wing_id', 1) !!}
                                <span class="text-danger">{{ $errors->first('wing_id') }}</span>
                            </div>-->
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="rankId">@lang('label.RANK') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-8">
                                <div id="rankHolder">
                                    {!! Form::select('rank_id', $rankList, null, ['class' => 'form-control js-source-states', 'id' => 'rankId', 'autocomplete' => 'off']) !!}
                                </div>                                
                                <span class="text-danger">{{ $errors->first('rank_id') }}</span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-4" for="apptId">@lang('label.APPOINTMENT') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('appointment_id', $appointmentList, null, ['class' => 'form-control js-source-states', 'id' => 'apptId', 'autocomplete' => 'off']) !!}
                                <span class="text-danger">{{ $errors->first('appointment_id') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="personalNo">@lang('label.PERSONAL_NO') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('personal_no', null, ['id'=> 'personalNo', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('personal_no') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="fullName">@lang('label.FULL_NAME') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('full_name', null, ['id'=> 'fullName', 'class' => 'form-control', 'list' => 'userFullName', 'autocomplete' => 'off']) !!} 
                                <datalist id="userFullName">
                                    @if (!$userNameArr->isEmpty())
                                    @foreach($userNameArr as $user)
                                    <option value="{{$user->full_name}}" />
                                    @endforeach
                                    @endif
                                </datalist>
                                <span class="text-danger">{{ $errors->first('full_name') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="officialName">@lang('label.OFFICIAL_NAME') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('official_name', null, ['id'=> 'officialName', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('official_name') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="username">@lang('label.USERNAME') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('username', null, ['id'=> 'username', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('username') }}</span>
                                <div class="clearfix margin-top-10">
                                    <span class="label label-danger">@lang('label.NOTE')</span> @lang('label.USERNAME_DESCRIPTION')
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="password">@lang('label.PASSWORD') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::password('password', ['id'=> 'password', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
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
                                {!! Form::password('conf_password', ['id'=> 'confPassword', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('conf_password') }}</span>
                            </div>
                        </div>
                        
                         <div class="form-group">
                            <label class="control-label col-md-4" for="extensionNo">@lang('label.EXTENSION_NO') :</label>
                            <div class="col-md-8">
                                {!! Form::text('extension_no', null, ['id'=> 'extensionNo', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('extension_no') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="userEmail">@lang('label.EMAIL') :</label>
                            <div class="col-md-8">
                                {!! Form::email('email', null, ['id'=> 'userEmail', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="phone">@lang('label.PHONE') :</label>
                            <div class="col-md-8">
                                {!! Form::text('phone', null, ['id'=> 'phone', 'class' => 'form-control interger-decimal-only', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('user_phone') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="status">@lang('label.STATUS') :</label>
                            <div class="col-md-8">
                                {!! Form::select('status', ['1' => __('label.ACTIVE'), '2' => __('label.INACTIVE')], '1', ['class' => 'form-control js-source-states-2', 'id' => 'status']) !!}
                                <span class="text-danger">{{ $errors->first('status') }}</span>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4">
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
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn btn-circle green" type="submit">
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href="{{ URL::to('/user'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>	
    </div>
</div>

<script>
    $(document).on('change', '#wingId', function () {
        var wingId = $('#wingId').val();
        
        $('#rankId').html("<option value='0'>@lang('label.SELECT_RANK_OPT')</option>");
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('user/getRank')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    wing_id: wingId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#rankId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        
        
//        if (groupId <= '3') {
//            $('#showCenterId').html('');
//            return false;
//        } else if (groupId >= '4') {
//            $.ajax({
//                url: "{{URL::to('user/getCenter')}}",
//                type: 'POST',
//                dataType: 'json',
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                data: {
//                    group_id: groupId
//                },
//                beforeSend: function () {
//                    App.blockUI({boxed: true});
//                },
//                success: function (res) {
//                    $('#showCenterId').html(res.html);
//                    App.unblockUI();
//                    $(".js-source-states").select2();
//                },
//            });
//        }
    });
</script>

@stop