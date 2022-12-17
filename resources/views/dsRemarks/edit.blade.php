
@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-book"></i>@lang('label.EDIT_DS_RMKS')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::model($target, [ 'files'=> true, 'class' => 'form-horizontal','id' => 'submitRmks'] ) !!}
            {!! Form::hidden('filter', Helper::queryPageStr($qpArr)) !!}
            {!! Form::hidden('id', $target->id) !!}
            {{csrf_field()}}

            <div class="row">
                <div class="col-md-12">

                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') :</label>
                        <div class="col-md-4">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                        </div>
                    </div>
                    {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id) !!}
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4">
                            <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                            {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4">
                            {!! Form::select('term_id', $termList, null,  ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                        </div>
                    </div>
                    <div id="showCM">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="eventId">@lang('label.CM') :<span class="text-danger"> *</span></label>
                            <div class="col-md-4">
                                {!! Form::select('cm_id', $cmList, null, ['class' => 'form-control js-source-states', 'id' => 'cmId']) !!}
                                @if(sizeof($cmList) <= 1)
                                <span class="text-danger">{!! __('label.NO_CM_IS_ASSIGNED_TO_THIS_COURSE') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="showTermEvent">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :</label>
                            <div class="col-md-4">
                                {!! Form::select('event_id', $eventList, null, ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                                @if(sizeof($eventList) <= 1)
                                <span class="text-danger">{!! __('label.NO_EVENT_IS_ASSIGNED_TO_THIS_COURSE') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="showRmksDateTime">
                        <div class = "form-group">
                            <label class = "control-label col-md-4" for="date">@lang('label.DATE') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-4">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('date', !empty($target->date) ? Helper::formatDate($target->date) : null, ['id'=> 'date', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="date">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('date') }}</span>
                            </div>         
                        </div>

                        <div class = "form-group">
                            <label class = "control-label col-md-4" for="remarks">@lang('label.REMARKS') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-4">
                                {!! Form::textarea('remarks', null, ['id'=> 'remarks', 'class' => 'form-control','cols'=>'20','rows' => '3']) !!}
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-4 col-md-8">
                                    <button class="btn btn-circle purple" href="#previewModal" type="button" data-toggle="modal" id="submitPreview">
                                        <i class="fa fa-check"></i> @lang('label.PROCEED')
                                    </button>
                                    <a href="{{ URL::to('/dsRemarks'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>

<!-- preview modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div id="showPreviewModal">
        </div>
    </div>
</div>
<!-- End preview modal -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {

    var options = {
        closeButton: true,
        debug: false,
        positionClass: "toast-bottom-right",
        onclick: null
    };

    $(document).on("change", "#termId", function () {
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        if (termId == '0') {
            return false;
        }
        $.ajax({
            url: "{{ URL::to('dsRemarks/getEventCmDateRmks')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#showTermEvent').html(res.html);
                $('.js-source-states').select2();
                App.unblockUI();

            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                if (jqXhr.status == 401) {
                    toastr.error(jqXhr.responseJSON.message, 'Error', options);
                } else {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                }
                App.unblockUI();
            }
        });//ajax
    });

    //preview submit form function
    $(document).on("click", "#submitPreview", function (e) {
        e.preventDefault();
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null,
        };
        // Serialize the form data
        var formData = new FormData($('#submitRmks')[0]);
        $.ajax({
            url: "{{ URL::to('dsRemarks/preview') }}",
            type: "POST",
            dataType: 'json', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#showPreviewModal").html('');
            },
            success: function (res) {
                $("#showPreviewModal").html(res.html);
                $(".js-source-states").select2({dropdownParent: $('#showPreviewModal'), width: '100%'});
                $('.tooltips').tooltip();
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

                $("#showPreviewModal").html('');
                App.unblockUI();
            }
        }); //ajax

    });
    //endof preview form


    $(document).on('click', '#submitRmksButton', function (e) {
        e.preventDefault();
        var formData = new FormData($('#submitRmks')[0]);
        $.ajax({
            type: 'POST',
            url: "{{ URL::to('dsRemarks/update') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#submitRmksButton').prop('disabled', true);
                App.blockUI({boxed: true});
            },
            success: function (data) {
                toastr.success(data, data.message, options);
                $('#submitRmksButton').prop('disabled', false);
                setTimeout(window.location.replace('{{ URL::to("/dsRemarks")}}'), 8000);
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
                var errorsHtml = '';
                if (jqXhr.status == 400) {
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

                $('#submitRmksButton').prop('disabled', false);
                App.unblockUI();
            }

        });
    });


//Rquest for unlock

});
</script>

@stop
