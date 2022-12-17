@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-unlock"></i>@lang('label.UNLOCK_CI_MODERATION_MARKING')
            </div>
        </div>
        <div class="portlet-body">
            @if(!empty($courseArr))
            {!! Form::open(array('group' => 'form', 'url' => 'unlockCiModerationMarking/filter','class' => 'form-horizontal')) !!}
<!--            <div class="row">
                 Begin Filter
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId">@lang('label.COURSE')</label>
                            <div class="col-md-8">
                                {!! Form::select('fil_course_id', $courseArr,  Request::get('fil_course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="termId">@lang('label.TERM')</label>
                            <div class="col-md-8">
                                {!! Form::select('fil_term_id', $termArr,  Request::get('fil_term_id'), ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                <i class="fa fa-search"></i> @lang('label.FILTER')
                            </button>
                        </div>
                    </div>
                </div>

            </div>-->
            {!! Form::close() !!}
            <!-- End Filter -->

            <!--            <div class="row">
                            <div class="col-md-offset-8 col-md-4" id="manageEvDiv">
                                <a class="btn btn-icon-only btn-warning tooltips vcenter" title="Download PDF" 
                                   href="{{action('AppointmentController@index', ['download'=>'pdf', 'fil_search' => Request::get('fil_search'), 'fil_service_id' => Request::get('fil_service_id') ])}}">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>-->
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <div class="max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                    <th class="vcenter">@lang('label.EVENT')</th>
                                    <th class="vcenter">@lang('label.SUB_EVENT')</th>
                                    <th class="vcenter">@lang('label.SUB_SUB_EVENT')</th>
                                    <th class="vcenter">@lang('label.SUB_SUB_SUB_EVENT')</th>
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
                                    <td class="vcenter">{{ $target->event }}</td>
                                    <td class="vcenter">{{ $target->sub_event }}</td>
                                    <td class="vcenter">{{ $target->sub_sub_event }}</td>
                                    <td class="vcenter">{{ $target->sub_sub_sub_event }}</td>
                                    <td class="vcenter">{{ $target->ci_name }}</td>
                                    <td class="vcenter">{{ $target->unlock_message }}</td>
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
                                    <td colspan="10" class="vcenter">@lang('label.NO_REQUEST_TO_UNLOCK_FOUND')</td>
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
                        <p><strong><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_ACTIVE_COURSE_FOUND_IN_THIS_ACTIVE_TRAINING_YEAR')</strong></p>
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
                        url: "{{URL::to('unlockCiModerationMarking/unlockRequest')}}",
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
                            toastr.success('@lang("label.CI_MODERATION_MARKING_HAS_BEEN_UNLOCKED_SUCCESSFULLY")', res, options);
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
                        url: "{{URL::to('unlockCiModerationMarking/denyRequest')}}",
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
                            toastr.success('@lang("label.CI_MODERATION_MARKING_HAS_BEEN_DENIED_TO_UNLOCK")', res, options);
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