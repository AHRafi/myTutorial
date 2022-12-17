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

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">

                    <!--Start Event Select-->
                    <div class="form-group">
                        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4">
                            <div id="showEvent" >
                                {!! Form::select('event_id', $eventList, null,  ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
                                <span class="text-danger">{{ $errors->first('event_id') }}</span>
                            </div>
                        </div>
                    </div>
                    <!--End Event Select-->

                    <div id="showPrevEvent">
                        <!--Start Check Has Sub Event--> 
                        <div class = "form-group" id="hasSubEvent">
                            <label class = "control-label col-md-4" for="checkSubEvent">@lang('label.HAS_SUB_EVENT')</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('has_sub_event',1,false, ['id' => 'checkSubEvent', 'class'=> 'md-check']) !!}

                                    <label for="checkSubEvent">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_IF_HAS_SUB_EVENT')</span>
                                </div>
                            </div>
                        </div>
                        <!--End Check Has Sub Event--> 

                        <!--Start Sub Event Select-->
                        <div class="form-group" id="selectSubEvent">
                            <label class="control-label col-md-4" for="subEventId">@lang('label.SUB_EVENT') :<span class="text-danger"> *</span></label>
                            <div class="col-md-4">
                                <div id="showSubEvent" >
                                    {!! Form::select('sub_event_id', $subEventList, null,  ['class' => 'form-control js-source-states', 'id' => 'subEventId']) !!}
                                    <span class="text-danger">{{ $errors->first('sub_event_id') }}</span>
                                </div>
                            </div>
                        </div>
                        <!--End Sub Event Select-->

                        <!--Start Check Has Sub Event DS--> 
                        <div class = "form-group" id="hasSubDS">
                            <label class = "control-label col-md-4" for="checkSubDS">@lang('label.HAS_DS_ASSESSMENT_GROUP')</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('has_sub_ds_ass_group',1,false, ['id' => 'checkSubDS', 'class'=> 'md-check']) !!}
                                    <label for="checkSubDS">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_IF_HAS_SUB_EVENT_DS_ASSESSMENT_GROUP')</span>
                                </div>
                            </div>
                        </div>
                        <!--End Check Has Sub Event DS--> 

                        <!--Start Check Has Sub Sub Event--> 
                        <div class = "form-group" id="hasSubSubEvent">
                            <label class = "control-label col-md-4" for="checkSubSubEvent">@lang('label.HAS_SUB_SUB_EVENT')</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('has_sub_2_event',1,false, ['id' => 'checkSubSubEvent', 'class'=> 'md-check']) !!}
                                    <label for="checkSubSubEvent">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_IF_HAS_SUB_SUB_EVENT')</span>
                                </div>
                            </div>
                        </div>
                        <!--End Check Has Sub Sub Event--> 

                        <!--Start Sub Sub Event Select-->
                        <div class="form-group" id="selectSubSubEvent">
                            <label class="control-label col-md-4" for="subSubEventId">@lang('label.SUB_SUB_EVENT') :<span class="text-danger"> *</span></label>
                            <div class="col-md-4">
                                <div id="showSubSubEvent" >
                                    {!! Form::select('sub_2_event_id', $subSubEventList, null,  ['class' => 'form-control js-source-states', 'id' => 'subSubEventId']) !!}
                                    <span class="text-danger">{{ $errors->first('sub_sub_event_id') }}</span>
                                </div>
                            </div>
                        </div>
                        <!--End Sub Sub Event Select-->

                        <!--Start Check Has Sub Sub Event DS-->
                        <div class = "form-group" id="hasSubSubDS">
                            <label class = "control-label col-md-4" for="checkSubSubDS">@lang('label.HAS_DS_ASSESSMENT_GROUP')</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('has_sub_2_ds_ass_group',1,false, ['id' => 'checkSubSubDS', 'class'=> 'md-check']) !!}
                                    <label for="checkSubSubDS">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_IF_HAS_SUB_SUB_EVENT_DS_ASSESSMENT_GROUP')</span>
                                </div>
                            </div>
                        </div>
                        <!--End Check Has Sub Sub Event DS--> 

                        <!--Start Check Has Sub Sub Sub Event-->
                        <div class = "form-group" id="hasSubSubSubEvent">
                            <label class = "control-label col-md-4" for="checkSubSubSubEvent">@lang('label.HAS_SUB_SUB_SUB_EVENT')</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('has_sub_3_event',1,false, ['id' => 'checkSubSubSubEvent', 'class'=> 'md-check']) !!}
                                    <label for="checkSubSubSubEvent">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_IF_HAS_SUB_SUB_SUB_EVENT')</span>
                                </div>
                            </div>
                        </div>
                        <!--End Check Has Sub Sub Sub Event--> 

                        <!--Start Sub Sub Sub Event Select-->
                        <div class="form-group" id="selectSubSubSubEvent">
                            <label class="control-label col-md-4" for="subSubSubEventId">@lang('label.SUB_SUB_SUB_EVENT') :<span class="text-danger"> *</span></label>
                            <div class="col-md-4">
                                <div id="showSubSubSubEvent" >
                                    {!! Form::select('sub_3_event_id', $subSubSubEventList, null,  ['class' => 'form-control js-source-states', 'id' => 'subSubSubEventId']) !!}
                                    <span class="text-danger">{{ $errors->first('sub_sub_sub_event_id') }}</span>
                                </div>
                            </div>
                        </div>
                        <!--End Sub Sub Sub Event Select-->

                        <!--Start Check Has Sub Sub Sub Event DS-->
                        <div class = "form-group" id="hasSubSubSubDS">
                            <label class = "control-label col-md-4" for="checkSubSubSubDS">@lang('label.HAS_DS_ASSESSMENT_GROUP')</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('has_sub_3_ds_ass_group',1,false, ['id' => 'checkSubSubSubDS', 'class'=> 'md-check']) !!}
                                    <label for="checkSubSubSubDS">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_IF_HAS_SUB_SUB_SUB_EVENT_DS_ASSESSMENT_GROUP')</span>
                                </div>
                            </div>
                        </div>
                        <!--End Check Has Sub Sub Sub Event DS-->

                        <div class = "form-group" id="actions">
                            <div class = "form-actions">
                                <div class = "col-md-offset-4 col-md-8">
                                    <button class = "button-submit btn btn-circle green" type="button">
                                        <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                                    </button>
                                    <a href = "{{ URL::to('eventTree') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
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
                    }
                });//ajax
            }


        });

        // check sub event
        $("#checkSubEvent").change(function () {
            if (this.checked) {
                displayHandler(['selectSubEvent'], 'block');
            } else {
                displayHandler(['selectSubEvent', 'hasSubDS', 'hasSubSubEvent', 'selectSubSubEvent'], 'none');
            }
        });

        //chang sub event
        $(document).on("change", "#subEventId", function () {
            var subEventId = $("#subEventId").val();

            if (subEventId != 0) {
                displayHandler(['hasSubDS', 'hasSubSubEvent'], 'block');
            } else {
                displayHandler(['hasSubDS', 'hasSubSubEvent', 'selectSubSubEvent'], 'none');
            }
        });


        // check sub sub event
        $("#checkSubSubEvent").change(function () {
            if (this.checked) {
                displayHandler(['selectSubSubEvent'], 'block');
            } else {
                displayHandler(['selectSubSubEvent', 'hasSubSubDS', 'hasSubSubSubEvent'], 'none');
            }
        });
        
        // check sub Sub event
//        $("#checkSubSubEvent").change(function () {
//            if (this.checked) {
//                $('#selectSubSubEvent').removeClass('display-none');
//            } else {
//                $('#selectSubSubEvent').addClass('display-none');
//            }
//            $.ajax({
//                url: "{{ URL::to('eventTree/getSubSubEvent')}}",
//                type: "POST",
//                dataType: "json",
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                data: {
//                },
//                success: function (res) {
//                    $('#subSubEventId').html(res.html);
//                },
//                error: function (jqXhr, ajaxOptions, thrownError) {
//                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
//                    App.unblockUI();
//                }
//            });//ajax
//        });

        //chang sub Sub event
        $(document).on("change", "#subSubEventId", function () {
            var subSubEventId = $("#subSubEventId").val();

            $('#checkSubSubSubEvent').prop('checked', false);
            $('#checkSubSubDS').prop('checked', false);
            $('#selectSubSubSubEvent').addClass('display-none');
            $('#hasSubSubSubDS').addClass('display-none');

            if (subSubEventId != 0) {
                $('#hasSubSubDS').removeClass('display-none');
                $('#hasSubSubSubEvent').removeClass('display-none');
            } else {
                $('#hasSubSubDS').addClass('display-none');
                $('#hasSubSubSubEvent').addClass('display-none');
            }
        });

        // check sub sub Sub event
        $("#checkSubSubSubEvent").change(function () {

            $('#hasSubSubSubDS').addClass('display-none');
            $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");

            if (this.checked) {
                $('#selectSubSubSubEvent').removeClass('display-none');
            } else {
                $('#selectSubSubSubEvent').addClass('display-none');
            }
            $.ajax({
                url: "{{ URL::to('eventTree/getSubSubSubEvent')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                },
                success: function (res) {
                    $('#subSubSubEventId').html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        //chang Sub sub Sub event
        $(document).on("change", "#subSubSubEventId", function () {
            var subSubSubEventId = $("#subSubSubEventId").val();

            $('#checkSubSubSubDS').prop('checked', false);

            if (subSubSubEventId != 0) {
                $('#hasSubSubSubDS').removeClass('display-none');
            } else {
                $('#hasSubSubSubDS').addClass('display-none');
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
            for(var i = 0; i <= displayIdArr.length; i++){
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