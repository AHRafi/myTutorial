@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user"></i>@lang('label.MY_PROFILE')
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="my-profile-sidebar">
                <!-- PORTLET MAIN -->
                <div class="portlet light">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-pic">
                        @if(!empty($target->photo))
                        <img class="img-responsive" src="{{URL::to('/')}}/public/uploads/user/{{$target->photo}}" alt="{{$target->full_name}}"/>
                        @else
                        <img class="img-responsive" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target->full_name}}"/>
                        @endif
                    </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">{{$target->rank->code.' '.$target->full_name}} </div>
                        <div class="profile-usertitle-job"> {{$target->appointment->name}} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->

                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li>
                                <a href="{{url('myProfile')}}">
                                    <i class="icon-home"></i>@lang('label.OVERVIEW')  
                                </a>
                            </li>
                            <li class="active">
                                <a href="{{url('myProfile')}}">
                                    <i class="icon-settings"></i>@lang('label.ACCOUNT_SETTINGS')  
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- END MENU -->
                </div>
                <!-- END PORTLET MAIN -->
            </div>
            <!-- END BEGIN PROFILE SIDEBAR -->

            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light ">
                            <div class="portlet-title tabbable-line">

                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#tab_1_1" data-toggle="tab">@lang('label.PERSONAL_INFO')</a>
                                    </li>
                                    <li>
                                        <a href="#tab_1_2" data-toggle="tab">@lang('label.CHANGE_PHOTO')</a>
                                    </li>
                                    <li>
                                        <a href="#tab_1_3" data-toggle="tab">@lang('label.CHANGE_PASSWORD')</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="portlet-body">
                                <div class="tab-content">
                                    <!-- PERSONAL INFO TAB -->
                                    <div class="tab-pane active" id="tab_1_1">
                                        {!! Form::open(array('group' => 'form', 'url' => '#', 'files'=> true, 'class' => 'form-horizontal', 'id' => 'infoForm')) !!}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="groupId">@lang('label.USER_GROUP') :<span class="text-danger"> *</span></label>
                                                    <div class="col-md-8">
                                                        {!! Form::select('group_id', $groupList, $target->group_id, ['class' => 'form-control js-source-states', 'id' => 'groupId', 'autocomplete' => 'off']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-4">@lang('label.RANK') <span class="text-danger"> *</span></label>
                                                    <div class="col-md-8">
                                                        {!! Form::select('rank_id', $rankList, $target->rank->id, ['class' => 'form-control js-source-states', 'id' => 'rankId', 'autocomplete' => 'off']) !!}
                                                    </div>
                                                </div>
                                                @if($target->group_id >= 4)
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="centerId">@lang('label.CENTER') :<span class="text-danger"> *</span></label>
                                                    <div class="col-md-8">
                                                        {!! Form::select('center_id', $centerList, null, ['class' => 'form-control js-source-states', 'id' => 'centerId']) !!}
                                                    </div>
                                                </div>
                                                @else
                                                <div id="showCenterId">
                                                </div>
                                                @endif 

                                                <div class="form-group">
                                                    <label class="control-label col-md-4">@lang('label.FULL_NAME') <span class="text-danger"> *</span></label>
                                                    <div class="col-md-8">
                                                        {!! Form::text('full_name', $target->full_name, ['id'=> 'fullName', 'class' => 'form-control', 'list' => 'userFullName', 'autocomplete' => 'off']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-4" for="userEmail">@lang('label.EMAIL') :</label>
                                                    <div class="col-md-8">
                                                        {!! Form::email('email', $target->email, ['id'=> 'userEmail', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-4">@lang('label.USERNAME') <span class="text-danger"> *</span></label>
                                                    <div class="col-md-8">
                                                        {!! Form::text('username', $target->username, ['id'=> 'username', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                                        <div class="clearfix margin-top-10" >
                                                            <span class="label label-danger" title="adfsdf">@lang('label.NOTE')</span> @lang('label.USERNAME_DESCRIPTION')
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-5" for="apptId">@lang('label.APPOINTMENT') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                                                    <div class="col-md-7">
                                                        {!! Form::select('appointment_id', $appointmentList, $target->appointment_id, ['class' => 'form-control js-source-states', 'id' => 'apptId', 'autocomplete' => 'off']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5" for="personalNo">@lang('label.PERSONEL_NO') :<span class="text-danger"> *</span></label>
                                                    <div class="col-md-7">
                                                        {!! Form::text('personal_no', $target->personal_no, ['id'=> 'personalNo', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5" for="officialName">@lang('label.OFFICIAL_NAME') :<span class="text-danger"> *</span></label>
                                                    <div class="col-md-7">
                                                        {!! Form::text('official_name', $target->official_name, ['id'=> 'officialName', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5" for="phone">@lang('label.PHONE') :</label>
                                                    <div class="col-md-7">
                                                        {!! Form::text('phone', $target->phone, ['id'=> 'phone', 'class' => 'form-control interger-decimal-only', 'autocomplete' => 'off']) !!} 
                                                    </div>
                                                </div>
                                                <div class="col-md-12 text-center">
                                                    <div class="margin-top-10">
                                                        <button type="button" class="btn btn-danger btn-circle update tooltips" id="updatePersonalInfo" data-update-id="1" title="">@lang('label.SAVE_CHANGES')</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <!-- END PERSONAL INFO TAB -->

                                    <!-- CHANGE AVATAR TAB -->
                                    <div class="tab-pane" id="tab_1_2">
                                        {!! Form::open(array('group' => 'form', 'url' => '#', 'files'=> true, 'class' => 'form-horizontal', 'id' => 'photoForm')) !!}
                                        <div class="row">
                                            <div class="col-md-offset-1 col-md-5">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                        @if(!empty($target->photo))
                                                        <img src="{{URL::to('/')}}/public/uploads/user/{{$target->photo}}" alt="{{ $target->full_name}}"/>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="btn red btn-outline btn-file">
                                                            <span class="fileinput-new">@lang('label.SELECT_IMAGE')</span>
                                                            <span class="fileinput-exists">@lang('label.CHANGE')</span>
                                                            {!! Form::file('photo', null, ['id'=> 'photo']) !!}
                                                        </span>
                                                        @if(!empty($target->photo))
                                                        <a href="javascript:;" class="btn red" data-dismiss="fileinput"> @lang('label.REMOVE')  </a>
                                                        @else
                                                        <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> @lang('label.REMOVE')  </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="clearfix margin-top-10">
                                                    <span class="label label-danger">@lang('label.NOTE')</span> @lang('label.USER_IMAGE_FOR_IMAGE_DESCRIPTION')
                                                </div>
                                            </div>
                                            {!! Form::hidden('user_id', $target->id, ['id'=> 'userId']) !!}
                                            <div class="col-md-12 text-center">
                                                <div class="margin-top-10">
                                                    <button type="button" class="btn btn-danger btn-circle update tooltips" id="updatePhoto" data-update-id="2" title="">@lang('label.SAVE_CHANGES')</button>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <!-- END CHANGE AVATAR TAB -->

                                    <!-- CHANGE PASSWORD TAB -->
                                    <div class="tab-pane" id="tab_1_3">
                                        {!! Form::open(array('group' => 'form', 'url' => '#', 'files'=> true, 'class' => 'form-horizontal', 'id' => 'passwordInfoForm')) !!}
                                        <div class="row">
                                            <div class="col-md-offset-2 col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('label.NEW_PASSWORD') <span class="text-danger"> *</span></label>
                                                    {!! Form::password('password', ['id'=> 'password', 'class' => 'form-control']) !!} 
                                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                                    <div class="clearfix margin-top-10">
                                                        <span class="label label-danger">@lang('label.NOTE')</span>
                                                        @lang('label.COMPLEX_PASSWORD_INSTRUCTION')
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">@lang('label.CONF_PASSWORD') <span class="text-danger"> *</span></label>
                                                    {!! Form::password('conf_password', ['id'=> 'confPassword', 'class' => 'form-control']) !!} 
                                                </div>
                                                {!! Form::hidden('user_id', $target->id, ['id'=> 'userId']) !!}
                                                <div class="margin-top-10 text-center">
                                                    <button type="button" class="btn btn-danger btn-circle update tooltips" id="updatePass" data-update-id="3" title="">@lang('label.SAVE_CHANGES')</button>
                                                </div>
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                        <div class="col-md-12 bottom-height"></div>
                                        <!-- END CHANGE PASSWORD TAB -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PROFILE CONTENT -->

            </div>	

        </div>
    </div>
    <div class="modal fade" id="Details" tabindex="-1" role="basic" aria-hidden="true">
        <div id="showDetails"></div> 
    </div>
    <script type="text/javascript">
        $(function () {
            $(document).on('change', '#groupId', function () {
                var groupId = $('#groupId').val();
                if (groupId <= '3') {
                    $('#showCenterId').html('');
                } else if (groupId >= '4') {
                    $.ajax({
                        url: "{{URL::to('getProfileCenter')}}",
                        type: 'POST',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            group_id: groupId
                        },
                        beforeSend: function () {
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('#showCenterId').html(res.html);
                            App.unblockUI();
                            $(".js-source-states").select2();
                        },
                    });
                }
            });
            $(document).on('click', '.update', function (e) {
                e.preventDefault();
                var updateId = $(this).data('update-id');

                if (updateId == 1) {
                    //update personal info 
                    var formData = new FormData($('#infoForm')[0]);
                    formData.append('update_id', updateId);
                } else if (updateId == 2) {
                    //update photo
                    var formData = new FormData($('#photoForm')[0]);
                    formData.append('update_id', updateId);
                } else if (updateId == 3) {
                    //update password
                    var formData = new FormData($('#passwordInfoForm')[0]);
                    formData.append('update_id', updateId);
                }
                var options = {
                    closeButton: true,
                    debug: false,
                    positionClass: "toast-bottom-right",
                    onclick: null
                };
                swal({
                    title: 'Are you sure?',
                       
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes, Update',
                    cancelButtonText: 'No, Cancel',
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "{{URL::to('updateProfile')}}",
                            type: "POST",
                            datatype: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            beforeSend: function () {
                                App.blockUI({boxed: true});
                            },
                            success: function (res) {
                                toastr.success(res, res.message, options);
                                App.unblockUI();
                            },
                            error: function (jqXhr, ajaxOptions, thrownError) {
                                if (jqXhr.status == 400) {
                                    var errorsHtml = '';
                                    var errors = jqXhr.responseJSON.message;
                                    var i = 0;
                                    var firstId = 0
                                    $.each(errors, function (key, value) {
                                        errorsHtml += '<li>' + value[0] + '</li>';
                                        i++;
                                    });
                                    toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                                } else {
                                    if (jqXhr.status == 401) {
                                        toastr.error(jqXhr.responseJSON.message, options);
                                    }
                                }
                                App.unblockUI();
                            }
                        });
                    }
                    ;
                });
            });
        });
    </script>
    @stop