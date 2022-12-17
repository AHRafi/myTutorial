@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-unlock"></i>@lang('label.UNLOCK_MA_REQUEST_LIST')
            </div>
        </div>
        <div class="portlet-body">
            @if(empty($void))
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <div class="max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                    <th class="text-center vcenter">@lang('label.GROUP')</th>
                                    <th class="vcenter">@lang('label.REQUESTED_BY')</th>
                                    <th class="vcenter">@lang('label.UNLOCK_MESSAGE')</th>
                                    <th class="td-actions text-center vcenter">@lang('label.ACTION')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!$targetArr->isEmpty())
                                <?php
                                    $sl = 0;
                                ?>
                                @foreach($targetArr as $target)
                                <tr>
                                    <td class="text-center vcenter">{{ ++$sl }}</td>
                                    <td class="vcenter">
                                        @if($maProcessArr[$target->course_id] == 1)
                                        {{ $target->syndicate_name ?? '' }}
                                        @elseif($maProcessArr[$target->course_id] == 2)
                                        {{ $target->sub_syndicate_name ?? '' }}
                                        @elseif($maProcessArr[$target->course_id] == 3)
                                        {{ $target->event_group_name ?? '' }}
                                        <?php
                                        $event = !empty($target->sub_sub_sub_event_code) ? $target->sub_sub_sub_event_code : (!empty($target->sub_sub_event_code) ? $target->sub_sub_event_code : (!empty($target->sub_event_code) ? $target->sub_event_code : (!empty($target->event_code) ? $target->event_code : '')));
                                        ?>
                                        {{ !empty($event) ? ' ('.$event.')' : '' }}
                                        @else
                                        @endif
                                    </td>
                                    <td class="vcenter">{{ $target->requested_by }}</td>
                                    <td class="vcenter">{!! $target->unlock_message ?? '' !!}</td>
                                    <td class="td-actions text-center vcenter">
                                        <div class="width-inherit">
                                            <button class="btn btn-xs btn-primary tooltips" title="@lang('label.UNLOCK')" type="button" data-id="{{$target->id}}" id="unlockRequest">
                                                <i class="fa fa-unlock"></i>
                                            </button>
                                            <button class="btn btn-xs btn-danger tooltips" title="@lang('label.DENY')" type="button" data-id="{{$target->id}}" id="denyRequest">
                                                <i class="fa fa-ban"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5" class="vcenter">@lang('label.NO_REQUEST_TO_UNLOCK_FOUND')</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissable">
                        
                        <p><strong><i class="fa fa-bell-o fa-fw"></i>{{$void['body'] ?? ''}}</strong></p>
                    </div>
                </div>
            </div>
            @endif
        </div>	
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $(document).on('click', '#unlockRequest', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
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
                confirmButtonText: 'Yes, Unlock',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url: "{{URL::to('acceptMaUnlockRequest')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                        },
                        success: function (res) {
                            toastr.success('@lang("label.HAS_BEEN_UNLOCKED_SUCCESSFULLY")', res, options);
                            setTimeout(function () {
                                document.location.reload(true);
                            }, 1000);
                            App.unblockUI();
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
                                toastr.error(jqXhr.responseJSON.message, 'Error', options);
                            } else {
                                toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                            }
                            App.unblockUI();
                        }

                    });
                }
            });
        });
        $(document).on('click', '#denyRequest', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
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
                confirmButtonText: 'Yes, Deny',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('denyMaUnlockRequest')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                        },
                        success: function (res) {
                            toastr.success('@lang("label.REQUEST_HAS_BEEN_DENIED")', res, options);
                            setTimeout(function () {
                                document.location.reload(true);
                            }, 1000);
                            App.unblockUI();
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
                                toastr.error(jqXhr.responseJSON.message, 'Error', options);
                            } else {
                                toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                            }
                            App.unblockUI();
                        }

                    });
                }
            });
        });
    });
</script>
@stop