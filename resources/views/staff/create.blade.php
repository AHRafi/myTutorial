@extends('layouts.default.master')
@section('data_count')	
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user"></i>@lang('label.CREATE_STAFF')
            </div>
        </div>

        <div class="portlet-body form">
            {!! Form::open(array('group' => 'form', 'url' => 'staff/store', 'files'=> true, 'class' => 'form-horizontal')) !!}
            {!! Form::hidden('filter', Helper::queryPageStr($qpArr)) !!}
            {{csrf_field()}}

            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">

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
                                {!! Form::select('rank_id', $rankList, null, ['class' => 'form-control js-source-states', 'id' => 'rankId']) !!}
                                <span class="text-danger">{{ $errors->first('rank_id') }}</span>
                            </div>
                        </div>

                        <div class="form-group" id="showArmsService">
                            <label class = "control-label col-md-4" for="armsServiceId">@lang('label.ARMS_SERVICE') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('arms_service_id', $armsServiceList, null,  ['class' => 'form-control js-source-states', 'id' => 'armsServiceId']) !!}
                                <span class="text-danger">{{ $errors->first('arms_service_id') }}</span>
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
                                    @if (!$staffNameArr->isEmpty())
                                    @foreach($staffNameArr as $staff)
                                    <option value="{{$staff->full_name}}" />
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
                                {!! Form::text('username', null, ['id'=> 'userName', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 

                                <span class="text-danger">{{ $errors->first('username') }}</span>
                                <div class="clearfix margin-top-10">
                                    <span class="label label-danger">@lang('label.NOTE')</span> @lang('label.USERNAME_DESCRIPTION')
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="password">@lang('label.PASSWORD') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::password('password', ['id'=> 'password', 'class' => 'form-control password-visible', 'autocomplete' => 'off']) !!} 
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
                                    {!! Form::password('conf_password', ['id'=> 'confPassword', 'class' => 'form-control password-visible', 'autocomplete' => 'off']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default show-pass" type="button" id="showConPass">
                                            <i class="fa fa-eye" id="conPassIcon"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('conf_password') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="joinDate">@lang('label.DT_OF_JOINING_AT_AFWC_WG') :</label>
                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('join_date', null, ['id'=> 'joinDate', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="joinDate">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
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
                            <label class="control-label col-md-4" for="extensionNo">@lang('label.PHONE_OFFICIAL') :</label>
                            <div class="col-md-8">
                                {!! Form::text('phone_official', null, ['id'=> 'phoneOfficial', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('phone_official') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="phone">@lang('label.PHONE') @lang('label._PERSONAL') :</label>
                            <div class="col-md-8">
                                {!! Form::text('phone', null, ['id'=> 'phone', 'class' => 'form-control interger-decimal-only', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('phone') }}</span>
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
                        <a href="{{ URL::to('/staff'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>	
    </div>
</div>

<script>
    $(document).ready(function () {
        //START::show pass
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



        $('#showArmsService').hide();
        $('#showFathersName').hide();
        $('#showBasicInfo').hide();
        $('#showAcademicInfo').hide();

        $(document).on('change', "#groupId", function (e) {
            e.preventDefault();
            var statusVal = $(this).val();
            var groupId = $('#groupId').val();
            if (groupId == '3' || groupId == '4') {
                $('#showArmsService').show();
                $('#showFathersName').show();
                $('#showBasicInfo').show();
                $('#showAcademicInfo').show();
                $('.js-source-states').select2();
            } else {
                $('#showArmsService').hide();
                $('#showFathersName').hide();
                $('#showBasicInfo').hide();
                $('#showAcademicInfo').hide();
            }

        });

        $("#groupId").trigger('change');

        $(document).on('change', '#wingId', function () {
            var wingId = $('#wingId').val();
//        $('#rankId').html("<option value='0'>@lang('label.SELECT_RANK_OPT')</option>");

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
                    $('#armsServiceId').html(res.html1);
                    $('#showCommissioningCourse').html(res.html2);
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


        $(document).on('change', '#commissioningCourseId', function () {
            var commissioningCourseId = $('#commissioningCourseId').val();

            $('#commisioningDate').val('');
            if (commissioningCourseId == '0') {
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('user/getCommisioningDate')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    commissioning_course_id: commissioningCourseId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#commisioningDate').val(res.commisioningDate);
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });
    });

</script>

@stop