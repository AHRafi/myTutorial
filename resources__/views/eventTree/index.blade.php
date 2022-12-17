@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EVENT_TREE')
            </div>
        </div>

        <div class="portlet-body form">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">

                        <!--Start Event Select-->
                        <div class="form-group">
                            <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div id="showEvent" >
                                    {!! Form::select('event_id', $eventList, null,  ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                                    <span class="text-danger">{{ $errors->first('event_id') }}</span>
                                </div>
                            </div>
                        </div>
                        <!--End Event Select-->

                        <div id="showPrevEvent">

                        </div>
                    </div>
                </div>
            </div>

            <!--Start Action-->
            <div class="form-actions" id="actions">
                <div class="row">
                    <div class = "col-md-offset-4 col-md-8">
                        <button class = "button-submit btn btn-circle green" type="button">
                            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href = "{{ URL::to('eventTree') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            <!--End Action-->
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {


        displayHandler([], 'none');
        //chang event
        $(document).on("change", "#eventId", function () {
            var eventId = $("#eventId").val();

            if (eventId == 0) {
                displayHandler([], 'none');
                return false;
            } else {
                displayHandler(['hasSubEvent', 'actions'], 'block');
//                $('#checkSubEvent').prop('checked', false);


                $.ajax({
                    url: "{{ URL::to('eventTree/getPrevEvent')}}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        event_id: eventId,
                    },

                    success: function (res) {
                        $('#showPrevEvent').html(res.html);
                        $('.js-source-states').select2();

                        if (!$("#checkSubEvent").is(':checked')) {
                            displayHandler(['selectSubEvent', 'hasSubDS', 'hasSubSubEvent', 'selectSubSubEvent',
                                'hasSubSubDS', 'hasSubSubSubEvent', 'selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
                        }

                        if ($("#subEventId").val() == 0) {
                            displayHandler(['hasSubDS', 'hasSubSubEvent', 'selectSubSubEvent', 'hasSubSubDS',
                                'hasSubSubSubEvent', 'selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
                        }

                        if (!$("#checkSubSubEvent").is(':checked')) {
                            displayHandler(['selectSubSubEvent', 'hasSubSubDS', 'hasSubSubSubEvent',
                                'selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
                        }

                        if ($("#subSubEventId").val() == 0) {
                            displayHandler(['hasSubSubDS', 'hasSubSubSubEvent', 'selectSubSubSubEvent',
                                'hasSubSubSubDS'], 'none');
                        }

                        if (!$("#checkSubSubSubEvent").is(':checked')) {
                            displayHandler(['selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
                        }

                        if ($("#subSubSubEventId").val() == 0) {
                            displayHandler(['hasSubSubSubDS'], 'none');
                        }
                    }
                });//ajax
            }
        });

        // check sub event
        $(document).on("change", "#checkSubEvent", function () {
            if (this.checked) {
                displayHandler(['selectSubEvent'], 'block');
            } else {
                displayHandler(['selectSubEvent', 'hasSubDS', 'hasSubSubEvent', 'selectSubSubEvent',
                    'hasSubSubDS', 'hasSubSubSubEvent', 'selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
            }
        });

        //chang sub event
        $(document).on("change", "#subEventId", function () {
            $('#checkSubDS').prop('checked', false);
            $('#checkSubSubEvent').prop('checked', false);
            displayHandler(['hasSubDS', 'hasSubSubEvent', 'selectSubSubEvent', 'hasSubSubDS',
                    'hasSubSubSubEvent', 'selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
            
            var subEventId = $("#subEventId").val();

            if (subEventId != 0) {
                displayHandler(['hasSubDS', 'hasSubSubEvent'], 'block');
            } else {
                displayHandler(['hasSubDS', 'hasSubSubEvent', 'selectSubSubEvent', 'hasSubSubDS',
                    'hasSubSubSubEvent', 'selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
            }
        });


        // check sub sub event
        $(document).on("change", "#checkSubSubEvent", function () {
            if (this.checked) {
                displayHandler(['selectSubSubEvent'], 'block');
            } else {
                displayHandler(['selectSubSubEvent', 'hasSubSubDS', 'hasSubSubSubEvent',
                    'selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
            }
        });


        //chang sub sub event
        $(document).on("change", "#subSubEventId", function () {
            $('#checkSubSubDS').prop('checked', false);
            $('#checkSubSubSubEvent').prop('checked', false);
            displayHandler(['hasSubSubDS', 'hasSubSubSubEvent', 'selectSubSubSubEvent',
                    'hasSubSubSubDS'], 'none');
                
            var subSubEventId = $("#subSubEventId").val();

            if (subSubEventId != 0) {
                displayHandler(['hasSubSubDS', 'hasSubSubSubEvent'], 'block');
            } else {
                displayHandler(['hasSubSubDS', 'hasSubSubSubEvent', 'selectSubSubSubEvent',
                    'hasSubSubSubDS'], 'none');

            }
        });

        // check sub sub sub event
        $(document).on("change", "#checkSubSubSubEvent", function () {
            if (this.checked) {
                displayHandler(['selectSubSubSubEvent'], 'block');
            } else {
                displayHandler(['selectSubSubSubEvent', 'hasSubSubSubDS'], 'none');
            }
        });

        //chang sub sub sub event
        $(document).on("change", "#subSubSubEventId", function () {
            $('#checkSubSubSubDS').prop('checked', false);
            var subSubSubEventId = $("#subSubSubEventId").val();

            if (subSubSubEventId != 0) {
                displayHandler(['hasSubSubSubDS'], 'block');
            } else {
                displayHandler(['hasSubSubSubDS'], 'none');
                
            }
        });

        //save data
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
                        url: "{{URL::to('eventTree/saveEventTree')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res, "@lang('label.EVENT_TREE_RELATED_EVENT')", options);
                            $(document).trigger("change", "#courseId");
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

    });

    function displayHandler(displayIdArr, displayType) {
        if (displayIdArr.length != 0) {
            for (var i = 0; i <= displayIdArr.length; i++) {
                $('#' + displayIdArr[i]).css('display', displayType);
            }
        } else {
            $('#hasSubEvent').css('display', displayType);
            $('#selectSubEvent').css('display', displayType);
            $('#hasSubDS').css('display', displayType);
            $('#hasSubSubEvent').css('display', displayType);
            $('#selectSubSubEvent').css('display', displayType);
            $('#hasSubSubDS').css('display', displayType);
            $('#hasSubSubSubEvent').css('display', displayType);
            $('#selectSubSubSubEvent').css('display', displayType);
            $('#hasSubSubSubDS').css('display', displayType);
            $('#actions').css('display', displayType);
        }
    }
</script>
@stop