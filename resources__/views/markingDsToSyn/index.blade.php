@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.ASSIGN_MARKING_DS_TO_SYN')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') :</label>
                        <div class="col-md-8">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYear->name}} </strong>
                                {!! Form::hidden('training_year_id', $activeTrainingYear->id,['id'=>'trainingYearId']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseIdMArking">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4">
                            <div id="showBatch" >
                                {!! Form::select('course_id', $courseList, null,  ['class' => 'form-control js-source-states', 'id' => 'courseIdMArking']) !!}
                                <span class="text-danger">{{ $errors->first('course_id') }}</span>
                            </div>
                        </div>
                    </div>
                    <!--get Term and Event data-->
                    <div id="showTermSyn"></div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {
        //get module
        $(document).on("change", "#courseIdMArking", function () {
            var trainingYearId = $("#trainingYearId").val();
            var courseId = $("#courseIdMArking").val();

            if (courseId === '0') {
                $('#showTermSyn').html('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('markingDsToSyn/getTermSyn')}}",
                type: "POST",
                dataType: "json",
                beforeSend: function () {
                    App.blockUI({boxed: true});
                   
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    training_year_id: trainingYearId
                },
                success: function (res) {
                    $('#showTermSyn').html(res.html);
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
        
        //Start::Get Marking Table
         //get module
        $(document).on("change", "#eventIdMarking", function () {
            var termId = $("#termIdMarkingTable").val();
            var courseId = $("#courseIdMarkingTable").val();
            var eventId = $("#eventIdMarking").val();
            if (eventId === '0') {
                $('#showTermSynMarkingTable').html('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('markingDsToSyn/getTermSynMarkingTable')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showTermSynMarkingTable').html(res.html);
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
        //End::Get Marking Table
        

        $(document).on('click', '.button-submit', function (e) {
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
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('markingDsToSyn/saveMarkingDsToSyn')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res, 'Marking DS To Syndicate assigned', options);
                            var termId = $("#termIdMarkingTable").val();
                            var courseId = $("#courseIdMarkingTable").val();
                            var eventId = $("#eventIdMarking").val();
                            if (eventId === '0') {
                                $('#showTermSynMarkingTable').html('');
                                return false;
                            }
                            $.ajax({
                                url: "{{ URL::to('markingDsToSyn/getTermSynMarkingTable')}}",
                                type: "POST",
                                dataType: "json",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    course_id: courseId,
                                    term_id: termId,
                                    event_id: eventId
                                },
                                beforeSend: function () {
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showTermSynMarkingTable').html(res.html);
                                    $('.tooltips').tooltip();
                                    $(".js-source-states").select2();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
//                    App.unblockUI();
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
                            App.unblockUI();
                        }
                    });
                }

            });

        });

        $(document).on('change', ".marking-ds-to-syn", function (e) {
            e.preventDefault();
            var dsId = $(this).val();
            var dsName = $('#dsName_' + dsId).val();
            $(this).parent().siblings('.ds-name').text(dsName);
        });

    });
</script>
@stop