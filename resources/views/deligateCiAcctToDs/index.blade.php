@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gears"></i>@lang('label.DELIGATE_CI_ACCOUNT_TO_DS')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'delgtCiAcctToDsForm')) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') :</label>
                        <div class="col-md-8">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong>
                                {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id,['id'=>'trainingYearId']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4">
                            <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                            {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                        </div>
                    </div>
                    <div id="showDsList">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="dsId">@lang('label.DS') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('ds_id', $dsList, !empty($prevDeligationInfo->ds_id) ? $prevDeligationInfo->ds_id : null,  ['class' => 'form-control js-source-states', 'id' => 'dsId']) !!}
                                <span class="text-danger">{{ $errors->first('ds_id') }}</span>
                            </div>
                        </div>
                        <div class="form-group margin-top-35">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-circle red-soft deligate-account"type="button">
                                    <i class="fa fa-gears"></i> @lang('label.DELIGATE_ACCOUNT')
                                </button>&nbsp;&nbsp;
                                @if(!empty($prevDeligationInfo->ds_id))
                                <button class="btn btn-circle red-mint cancel-deligation" type="button">
                                    <i class="fa fa-times-circle"></i> @lang('label.CANCEL_DELIGATION')
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center" id="showDsInfo">
                    @if(!empty($prevDeligationInfo))
                    <div class="profile-userpic">
                        @if(!empty($prevDeligationInfo->photo) && File::exists('public/uploads/user/' . $prevDeligationInfo->photo))
                        <img src="{{URL::to('/')}}/public/uploads/user/{{$prevDeligationInfo->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
                             alt="{{ !empty($prevDeligationInfo->ds_name)? $prevDeligationInfo->ds_name:''}}" style="width: 150px;height: 180px;" />
                        @else 
                        <img src="{{URL::to('/')}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
                             alt="{{ !empty($prevDeligationInfo->ds_name)? $prevDeligationInfo->ds_name:'' }}"  style="width: 150px;height: 180px;" />
                        @endif
                    </div>
                    <div class="profile-usertitle">
                        @if(!empty($prevDeligationInfo->ds_name))
                        <div class="text-center margin-bottom-10">
                            <b>{{$prevDeligationInfo->ds_name}}</b>
                        </div>
                        @endif
                        @if(!empty($prevDeligationInfo->appt))
                        <div class="text-center margin-bottom-10">
                            {{'('.$prevDeligationInfo->appt.')'}}
                        </div>
                        @endif
                        <?php
                        $labelColorPN = 'grey-mint';
                        $fontColorPN = 'blue-hoki';

                        if ($prevDeligationInfo->wing_id == 1) {
                            $labelColorPN = 'green-seagreen';
                        } elseif ($prevDeligationInfo->wing_id == 2) {
                            $labelColorPN = 'white';
                            $fontColorPN = 'white';
                        } elseif ($prevDeligationInfo->wing_id == 3) {
                            $labelColorPN = 'blue-madison';
                        }
                        ?>
                        @if(!empty($prevDeligationInfo->personal_no))
                        <div class="bold label label-square label-sm font-size-11 label-{{$labelColorPN}}">
                            <span class="bg-font-{{$fontColorPN}}">{{$prevDeligationInfo->personal_no}}</span>
                        </div>
                        @endif
                    </div>

                    @endif

                </div>
                <div class="col-md-4">
                    <div class="alert alert-info alert-dismissable">
                        <div class="margin-bottom-10 border-bottom-1-info">
                            <span class="bold font-size-14">@lang('label.ACCOUNT_PRIVILEDGES')</span>
                        </div>
                        <div class="margin-bottom-10">
                            <span><i class="fa fa-gears"></i> @lang('label.CI_MODERATION_MARKING')</span>
                        </div>
                        <!--                        <div class="margin-bottom-10">
                                                    <span><i class="fa fa-gears"></i> @lang('label.COMDT_MODERATION_MARKING')</span>
                                                </div>-->
                        <div class="margin-bottom-10">
                            <span><i class="fa fa-gears"></i> @lang('label.CI_OBSN_MARKING')</span>
                        </div>
                        <div class="margin-bottom-10">
                            <span><i class="fa fa-gears"></i> @lang('label.COMDT_OBSN_MARKING')</span>
                        </div>
                        <div class="margin-bottom-10">
                            <span><i class="fa fa-gears"></i> @lang('label.UNLOCK_EVENT_ASSESSMENT')</span>
                        </div>
                        <div class="margin-bottom-10">
                            <span><i class="fa fa-gears"></i> @lang('label.UNLOCK_CI_MODERATION_MARKING')</span>
                        </div>
                        <!--                        <div class="margin-bottom-10">
                                                    <span><i class="fa fa-gears"></i> @lang('label.UNLOCK_COMDT_MODERATION_MARKING')</span>
                                                </div>-->
                        <div class="margin-bottom-10">
                            <span><i class="fa fa-gears"></i> @lang('label.UNLOCK_CI_OBSN_MARKING')</span>
                        </div>
                        <div class="margin-bottom-10">
                            <span><i class="fa fa-gears"></i> @lang('label.UNLOCK_COMDT_OBSN_MARKING')</span>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();

            if (courseId == '0') {
                $('#showDsList').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('deligateCiAcctToDs/getDsList')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showDsList').html(res.html);
                    $('#showDsInfo').html(res.html2);
                    $('.tooltips').tooltip();
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        //get module
        $(document).on("change", "#dsId", function () {
            var dsId = $("#dsId").val();

            if (dsId === '0') {
                $('#showDsInfo').html('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('deligateCiAcctToDs/getDsInfo')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    ds_id: dsId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showDsInfo').html(res.html);
                    $('.tooltips').tooltip();
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
//                    App.unblockUI();
                }
            });//ajax
            App.unblockUI();
        });

        //deligate account
        $(document).on('click', '.deligate-account', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#delgtCiAcctToDsForm')[0]);


            swal({
                title: 'Are you sure?',
                 
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Delegate Account',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('deligateCiAcctToDs/setDeligation')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.deligate-account').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('.deligate-account').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);
                            App.unblockUI();
                            $('#showDsList').html(res.html);
                            $('#showDsInfo').html(res.html2);
                            $('.tooltips').tooltip();
                            $(".js-source-states").select2();
                        },
                        error: function (jqXhr, ajaxOptions, thrownError) {
                            if (jqXhr.status == 400) {
                                var errorsHtml = '';
                                var errors = jqXhr.responseJSON.message;
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value + '</li>';
                                });
                                toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                            } else if (jqXhr.status == 401) {
                                toastr.error(jqXhr.responseJSON.message, '', options);
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            $('.deligate-account').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });
        //cancel deligation
        $(document).on('click', '.cancel-deligation', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#delgtCiAcctToDsForm')[0]);


            swal({
                title: 'Are you sure?',
                 
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Cancel Delegation',
                cancelButtonText: 'No, Retain Delegation',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('deligateCiAcctToDs/cancelDeligation')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.deligate-account').prop('disabled', true);
                            $('.cancel-deligation').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            $('.deligate-account').prop('disabled', false);
                            $('.cancel-deligation').prop('disabled', false);
                            toastr.success(res.message, res.heading, options);
                            App.unblockUI();
                            $('#showDsList').html(res.html);
                            $('#showDsInfo').html(res.html2);
                            $('.tooltips').tooltip();
                            $(".js-source-states").select2();
                        },
                        error: function (jqXhr, ajaxOptions, thrownError) {
                            if (jqXhr.status == 400) {
                                var errorsHtml = '';
                                var errors = jqXhr.responseJSON.message;
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value + '</li>';
                                });
                                toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                            } else if (jqXhr.status == 401) {
                                toastr.error(jqXhr.responseJSON.message, '', options);
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            $('.deligate-account').prop('disabled', false);
                            $('.cancel-deligation').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }

            });

        });
    });
</script>
@stop