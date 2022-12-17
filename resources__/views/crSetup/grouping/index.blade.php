@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.ASSIGN_GROUP')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-8">
                                    {!! Form::select('training_year_id', $trainingYearList, Request::get('training_year_id'), ['class' => 'form-control js-source-states', 'id' => 'trainingYearId']) !!}
                                    <span class="text-danger">{{ $errors->first('training_year_id') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="courseId">@lang('label.COURSE'):</label>
                                <div class="col-md-8">
                                    {!! Form::select('course_id', $courseList, Request::get('course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                                    <span class="text-danger">{{ $errors->first('course_id') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="dsId">@lang('label.DS') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('ds_id', $dsList, null, ['class' => 'form-control js-source-states', 'id' => 'dsId']) !!}
                                </div>
                            </div>
                        </div>

                    </div>



                    <!--get module data-->


                </div>
            </div>

            <div class="margin-top-10" id="showCmSelectionPanel"></div>
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

        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
            if (trainingYearId == 0) {
                return false;
            }

            $.ajax({
                url: "{{ URL::to('crMarkingReflection/getCourse')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#courseId').html(res.html);
                    $('.course-err').html(res.html1);
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax

        });
        //End::Get Course
//        
        $(document).on("change", "#dsId", function () {

            var courseId = $("#courseId").val();
            var dsId = $("#dsId").val();
            if (dsId == '0') {
                $('#showCmSelectionPanel').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('crGrouping/getCmSelectionPanel')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    ds_id: dsId,
                },
                beforeSend: function () {
                    $('#showCmSelectionPanel').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmSelectionPanel').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        //    Start:: Group Wise Search CM 
        $(document).on('change', '#cmGroupId', function () {
            var courseId = $("#courseId").val();
            var dsId = $("#dsId").val();
            var cmGroupId = $("#cmGroupId").val();
            if (cmGroupId == '0') {
                $('#getCmGroupWiseSearchCm').html('');
                return false;
            }
            $.ajax({
                url: "{{URL::to('crGrouping/getCmGroupWiseSearchCm')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    ds_id: dsId,
                    cm_group_id: cmGroupId,
                },
                beforeSend: function () {
                    $("#getCmGroupWiseSearchCm").html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $("#getCmGroupWiseSearchCm").html(res.html);
                    $('.js-source-states').tooltip();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });
        });
//    End:: Group Wise Search CM

//    Start:: Individual Search CM
        $(document).on("keyup", "#individualSearch", function () {
            var courseId = $("#courseId").val();
            var dsId = $("#dsId").val();
            var individualSearch = $(this).val();
//        alert(individualSearch);
            $.ajax({
                url: "{{URL::to('crGrouping/getFilterIndividualCm')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    ds_id: dsId,
                    individual_search: individualSearch,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $("#showIndividualSearchCm").html(res.html);
                    $('.js-source-states').tooltip();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });
        });
//    End:: Individual Search CM

//Start :: set assigned cm
        $(document).on("click", ".assign-selected-cm", function (e) {
            e.preventDefault();
            var dataId = $(this).attr('data-id');
            var form_data = new FormData($('#submitForm' + dataId)[0]);
            $('.cm-select:checked').each(function () {
                var cmId = $(this).val();
                $('<input>').attr('type', 'hidden')
                        .attr('name', 'selected_cm_id[' + cmId + ']')
                        .attr('value', cmId)
                        .attr('class', 'selected-cm-id')
                        .attr('id', 'selectedCmId_' + cmId)
                        .appendTo('#selectedCmForm');

            });

            $('.cm-select:not(:checked)').each(function () {
                var cmId = $(this).val();
                $('#selectedCmForm').find('#selectedCmId_' + cmId).remove();
            });
            $('.cm-selected').each(function () {
                var selectedCmId = $(this).val();
                form_data.append('selected_cm_id[' + selectedCmId + ']', selectedCmId);
            });
//            console.log(form_data);
//            return false;
            $('.selected-cm-id').each(function () {
                var selectedCmId = $(this).val();
                form_data.append('selected_cm_id[' + selectedCmId + ']', selectedCmId);
            });

            $.ajax({
                url: "{{URL::to('crGrouping/setCm')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                success: function (res) {
                    $("#selectedCmList").html(res.html);
                    $('.js-source-states').tooltip();
                    $('.tooltips').tooltip();
                    App.unblockUI();
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
                        toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
                    }
                    App.unblockUI();
                }
            });
        });
//End :: set assigned cm

        //Start:: Save CM
        $(document).on('click', '.cm-list-submit', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);

            $('.selected-cm').each(function () {
                var selectedCm = $(this).val();
                form_data.append('selected_cm[' + selectedCm + ']', selectedCm);
            });

            swal({
                title: 'Are you sure?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('crGrouping/saveGroup')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: form_data,
                        beforeSend: function () {
                            $(this).prop('disablded', true);
                            $('.assign-selected-cm').prop('disablded', true);
                        },
                        success: function (res) {
                            toastr.success(res, res.message, options);
                            $(this).prop('disablded', false);
                            $('.assign-selected-cm').prop('disablded', false);
                            App.unblockUI();
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
                            $(this).prop('disablded', false);
                            $('.assign-selected-cm').prop('disablded', false);
                            App.unblockUI();
                        }
                    });
                }
            });
        });
//        End:: Save CM
//Start:: Remove Marking Group
        $(document).on('click', '#buttonDelete', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);

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
                confirmButtonText: 'Yes, Remove',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url: "{{URL::to('crGrouping/removeGroup')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: form_data,
                        beforeSend: function () {
                            $(this).prop('disablded', true);
                            $('.assign-selected-cm').prop('disablded', true);
                        },
                        success: function (res) {
                            toastr.success(res, res.message, options);
                            $(this).prop('disablded', false);

                            var courseId = $("#courseId").val();
                            var dsId = $("#dsId").val();
                            if (dsId == '0') {
                                $('#showCmSelectionPanel').html('');
                                return false;
                            }
                            var options = {
                                closeButton: true,
                                debug: false,
                                positionClass: "toast-bottom-right",
                                onclick: null
                            };

                            $.ajax({
                                url: "{{ URL::to('crGrouping/getCmSelectionPanel')}}",
                                type: "POST",
                                dataType: "json",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    course_id: courseId,
                                    ds_id: dsId,
                                },
                                beforeSend: function () {
                                    $('#showCmSelectionPanel').html('');
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showCmSelectionPanel').html(res.html);
                                    $('.tooltips').tooltip();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
                                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                                    App.unblockUI();
                                }
                            });//ajax
                            
                            App.unblockUI();
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
                            $("#buttonDelete").prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }
            });

        });
        //End:: Remove Marking Group
    });

</script>








@stop