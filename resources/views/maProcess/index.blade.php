@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.MA_PROCESS')
            </div>

            <div class="actions">
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeCourse->name}} </strong></div>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--get module data-->
                    <div id="showMarkingLimit">
                        <div class="row margin-top-10">
                            <div class="col-md-8 table-responsive webkit-scrollbar col-md-offset-2">
                                <table class="table table-bordered" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                            <th class="vcenter">@lang('label.TERM')</th>
                                            <th class="text-center vcenter">@lang('label.PROCESS')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!$termArr->isEmpty())
                                        <?php $sl = 0; ?>
                                        @foreach($termArr as $term)
                                        <?php
                                        $process = !empty($prevDataArr[$term->id]['process']) ? $prevDataArr[$term->id]['process'] : null;
                                        ?>
                                        <tr>
                                            <td class="text-center vcenter width-50">{!! ++$sl !!}</td>
                                            <td class="vcenter width-250">
                                                <div class="width-inherit">{!! $term->name ?? '' !!}</div>
                                            </td>
                                            <td class="text-center vcenter width-150">
                                                {!! Form::select('process['.$term->id.'][type]', $processList, $process, ['id'=> 'type_' . $term->id, 'class' => 'form-control js-source-states width-inherit', 'data-term-id' => $term->id]) !!}
                                                    
                                            </td>
                                        </tr>
                                        {!! Form::hidden('process['.$term->id.'][term_name]', $term->name ?? '', ['id' => 'termName_' . $term->id]) !!}
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="4">@lang('label.NO_TERM_IS_ASSIGNED_TO_THIS_COURSE')</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(!$termArr->isEmpty())
                        <div class = "form-actions margin-top-10">
                            <div class = "col-md-offset-2 col-md-8 text-center">
                                <button class = "button-submit btn btn-circle green" type="button">
                                    <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                                </button>
                                <a href = "{{ URL::to('maProcess') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                            </div>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>


<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {

    $(document).on("change", "#courseId", function () {

        var courseId = $("#courseId").val();
        if (courseId == '0') {
            $('#showMarkingLimit').html('');
            return false;
        }
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };

        $.ajax({
            url: "{{ URL::to('ciComdtModerationMarkingLimit/getMarkingLimit')}}",
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
                $('#showMarkingLimit').html(res.html);
                $('.tooltips').tooltip();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                App.unblockUI();
            }
        });//ajax
    });

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
                    url: "{{URL::to('maProcess/saveProcess')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (res) {
                        toastr.success('@lang("label.MA_PROCESS_HAS_BEEN_SET_SUCCESSFULLY")', res, options);
//                            $(document).trigger("change", "#courseId");
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
                            toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
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