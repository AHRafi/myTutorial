@extends('layouts.default.master')
@section('data_count')
<!-- BEGIN CONTENT BODY -->
<!-- BEGIN PORTLET-->
@include('layouts.flash')
<!-- END PORTLET-->



<div class="col-md-12">
    <!-- BEGIN PROFILE SIDEBAR -->
    <div class="profile">
        <div class="tabbable-line tabbable-full-width">
            @if(Auth::user()->id == 125)
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_1" data-toggle="tab"> @lang('label.CONFIGURE_IP') </a>
                </li>
                <li>
                    <a href="#tab_2" data-toggle="tab"> @lang('label.IP_BLOCKER') </a>
                </li>
            </ul>
            @endif


            <div class="tab-content">
                @if(Auth::user()->id == 125)
                <div class="tab-pane active" id="tab_1">

                    <div class="portlet box green">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-line-chart"></i>@lang('label.CONFIGURE_IP')
                            </div>
                        </div>
                        <div class="portlet-body">
                            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
                            {{csrf_field()}}

                            <?php
                            //check and show previous value
                            $checked = '';
                            if (!empty($target) && ($target->configurable == 1)) {
                                $checked = 'checked';
                            }
                            ?>

                            <div class="form-group">
                                <label class="control-label col-md-4" for="configurable">@lang('label.CONFIGURE_IP') :</label>
                                <div class="col-md-8 checkbox-center md-checkbox has-success">
                                    {!! Form::checkbox('configurable',1,$checked, ['id' => 'configurable', 'class'=> 'md-check']) !!}

                                    <label for="configurable">
                                        <span class="inc"></span>
                                        <span class="check mark-caheck"></span>
                                        <span class="box mark-caheck"></span>
                                    </label>
                                    <span class="text-success">@lang('label.PUT_TICK_IF_IP_IS_CONFIGURABLE_FOR_LOGIN')</span>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-5 col-md-5">
                                        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                        </button>
                                        <a href="{{ URL::to('ipBlocker') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>

                </div>

                <div class = "tab-pane" id = "tab_2">

                    <div class="col-md-12">
                        <div class="portlet box green">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-line-chart"></i>@lang('label.IP_BLOCKER')
                                </div>
                            </div>
                            <div class="portlet-body">
                                {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm2')) !!}
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-2">

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                                        <th class="vcenter">@lang('label.USER')</th>
                                                        <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                                                        <th class="text-center vcenter">@lang('label.IP')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!$userList->isEmpty())
                                                    <?php
                                                    $sl = 0;
                                                    ?>
                                                    @foreach($userList as $user)
                                                    <tr>
                                                        <td class="text-center vcenter">{{ ++$sl }}</td>

                                                        <td class="vcenter">{{ $user->user_name ?? '' }}</td>
                                                        <td class="vcenter">{{ $user->official_name ?? '' }}</td>
                                                        <td class="text-center vcenter width-200">
                                                            <div class="width-inherit">
                                                                {!! Form::text('ip['.$user->id.']', !empty($user->ip) ? $user->ip : null, ['id'=> 'ip'.$user->id, 'class' => 'form-control text-width-100-per integer-decimal-only', '']) !!}
                                                                {!! Form::hidden('user['.$user->id.']', $user->user_name ?? '') !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="8" class="vcenter">@lang('label.NO_USER_FOUND')</td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-5 col-md-5">
                                                    <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit2" >
                                                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                                    </button>
                                                    <a href="{{ URL::to('ipBlocker') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>



                </div>
                @else
                <div class = "tab-pane active" id = "tab_2">

                    <div class="col-md-12">
                        <div class="portlet box green">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-line-chart"></i>@lang('label.IP_BLOCKER')
                                </div>
                            </div>
                            <div class="portlet-body">
                                {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm2')) !!}
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-2">

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                                        <th class="vcenter">@lang('label.USER')</th>
                                                        <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                                                        <th class="text-center vcenter">@lang('label.IP')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!$userList->isEmpty())
                                                    <?php
                                                    $sl = 0;
                                                    ?>
                                                    @foreach($userList as $user)
                                                    <tr id="ipBlock-{{ $user->id }}">
                                                        <td class="text-center vcenter ip-sl-key">{{ ++$sl }}</td>

                                                        <td class="vcenter">{{ $user->user_name ?? '' }}</td>
                                                        <td class="vcenter">{{ $user->official_name ?? '' }}</td>
                                                        <td class="text-center vcenter width-200">
                                                            <div class="width-inherit">
                                                                {!! Form::text('ip['.$user->id.']', !empty($user->ip) ? $user->ip : null, ['id'=> 'ip'.$user->id, 'class' => 'form-control text-width-100-per integer-decimal-only', '']) !!}
                                                                {!! Form::hidden('user['.$user->id.']', $user->user_name ?? '') !!}


                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @if($user->id == 125)
                                                    {!! Form::hidden('ip['.$user->id.']', !empty($user->ip) ? $user->ip : null, ['id'=> 'ip'.$user->id, 'class' => 'form-control text-width-100-per integer-decimal-only', '']) !!}
                                                    {!! Form::hidden('user['.$user->id.']', $user->user_name ?? '') !!}
                                                    @endif
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="8" class="vcenter">@lang('label.NO_USER_FOUND')</td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-5 col-md-5">
                                                    <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit2" >
                                                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                                    </button>
                                                    <a href="{{ URL::to('ipBlocker') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>



                </div>
                @endif
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(function () {

        // Remove Swapnoloke User Info Row 
        $('#ipBlock-' + 125).remove();
        slRearrange();
        
        // IP Blocker List Serial Rearrange
        function slRearrange() {
            var counter = 1;
            $('.ip-sl-key').each(function () {
                $(this).text(counter);
                counter++;
            });
        }



        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null,
        };

        //function for no of weeks


        $(document).on('click', '#buttonSubmit', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);
            swal({
                title: 'Are you sure?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('ipBlocker/configure')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res, "@lang('label.SUCCESSFULLY_CONFIGURED')", options);
                            //App.blockUI({ boxed: false });
                            //setTimeout(location.reload.bind(location), 1000);
                        },
                        error: function (jqXhr, ajaxOptions, thrownError) {
                            if (jqXhr.status == 400) {
                                var errorsHtml = '';
                                var errors = jqXhr.responseJSON.message;
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value[0] + '</li>';
                                });
                                toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                            } else if (jqXhr.status == 401) {
                                //toastr.error(jqXhr.responseJSON.message, '', options);
                                var errors = jqXhr.responseJSON.message;
                                var errorsHtml = '';
                                if (typeof (errors) === 'object') {
                                    $.each(errors, function (key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, '', options);
                                } else {
                                    toastr.error(jqXhr.responseJSON.message, '', options);
                                }
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            App.unblockUI();
                        }
                    });
                }
            });
        });


        $(document).on('click', '#buttonSubmit2', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm2')[0]);
            swal({
                title: 'Are you sure?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('ipBlocker/saveIP')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res, "@lang('label.USER_VALID_LOGIN_IP_SET_SUCCESSFULLY')", options);
                            //App.blockUI({ boxed: false });
                            //setTimeout(location.reload.bind(location), 1000);
                        },
                        error: function (jqXhr, ajaxOptions, thrownError) {
                            if (jqXhr.status == 400) {
                                var errorsHtml = '';
                                var errors = jqXhr.responseJSON.message;
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value[0] + '</li>';
                                });
                                toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                            } else if (jqXhr.status == 401) {
                                //toastr.error(jqXhr.responseJSON.message, '', options);
                                var errors = jqXhr.responseJSON.message;
                                var errorsHtml = '';
                                if (typeof (errors) === 'object') {
                                    $.each(errors, function (key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, '', options);
                                } else {
                                    toastr.error(jqXhr.responseJSON.message, '', options);
                                }
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            App.unblockUI();
                        }
                    });
                }
            });
        });


    });
</script>



@endsection