@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.ASSIGN_MARKING_GROUP')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            @csrf
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeCourse->name}} </strong></div>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>

                        <div id="showTerm">
                            @if(sizeof($termList) > 1)
                            @if(!empty($eventGroupList))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
                                    <div class="col-md-7">
                                        {!! Form::select('term_id', $termList, null, ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="col-md-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_EVENT_GROUP_IS_ASSIGNED_TO_THIS_COURSE') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                            @else
                            <div class="col-md-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_TERM_IS_INITIATED_TO_THIS_COURSE') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div id="showEvent"></div>
                    </div>
                    <div class="row">
                        <!--get sub event list or cm ds -->
                        <div id="showSubEventCmDs"></div>

                        <!--get sub sub event list or cm ds -->
                        <div id="showSubSubEventCmDs"></div>

                        <!--get sub sub sub event list or cm ds -->
                        <div id="showSubSubSubEventCmDs"></div>
                    </div>

                    <!--get  cm ds -->
                    <div id="showCmDs"></div>

                    <!--get  cm ds Selection-->
                    <div id="showCmDsSelection"></div>

                    <!--Assigned Cm-->
                    <div class="modal fade" id="modalCmSelection" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-full">
                            <div id="showCmSelection">

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
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            $('#showSubEventCmDs').html('');
            $('#showSubSubEventCmDs').html('');
            $('#showSubSubSubEventCmDs').html('');
            $('#showCmDs').html('');
            $('#showCmDsSelection').html('');
            $('#showTerm').html("");
            $('#showEvent').html("");
            $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
            $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");

            if (courseId == '0') {
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getTerm')}}",
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
                    $('#showTerm').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });
        $(document).on("change", "#termId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            $('#showSubEventCmDs').html('');
            $('#showSubSubEventCmDs').html('');
            $('#showSubSubSubEventCmDs').html('');
            $('#showCmDs').html('');
            $('#showCmDsSelection').html('');
            $('#showEvent').html("");
            $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
            $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");

            if (termId == '0') {
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getEvent')}}",
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
                    $('#showEvent').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });
        $(document).on("change", "#eventId", function () {
            $('#showSubEventCmDs').html('');
            $('#showSubSubEventCmDs').html('');
            $('#showSubSubSubEventCmDs').html('');
            $('#showCmDs').html('');
            $('#showCmDsSelection').html('');
            $('#subEventId').html("<option value='0'>@lang('label.SELECT_SUB_EVENT_OPT')</option>");
            $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
            $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            if (eventId == '0') {
                $('#showSubEventCmDs').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getSubEventCmDs')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubEventCmDs').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });
        $(document).on("change", "#subEventId", function () {
            $('#showSubSubEventCmDs').html('');
            $('#showSubSubSubEventCmDs').html('');
            $('#showCmDs').html('');
            $('#showCmDsSelection').html('');
            $('#subSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_EVENT_OPT')</option>");
            $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            if (subEventId == '0') {
                $('#showSubSubEventCmDs').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getSubSubEventCmDs')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubEventCmDs').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });
        $(document).on("change", "#subSubEventId", function () {
            $('#showSubSubSubEventCmDs').html('');
            $('#showCmDs').html('');
            $('#showCmDsSelection').html('');
            $('#subSubSubEventId').html("<option value='0'>@lang('label.SELECT_SUB_SUB_SUB_EVENT_OPT')</option>");
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            if (subSubEventId == '0') {
                $('#showSubSubSubEventCmDs').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getSubSubSubEventCmDs')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSubSubEventCmDs').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });
        $(document).on("change", "#subSubSubEventId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            $('#showCmDsSelection').html('');
            if (subSubSubEventId == '0') {
                $('#showCmDs').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getCmDs')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmDs').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });
        $(document).on("change", "#eventGroupId", function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var eventGroupId = $(this).val();
            if (eventGroupId == '0') {
                $('#showCmDsSelection').html('');
                return false;
            }
            $('#showCmDsSelection').html('');
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getCmDsSelection')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                    event_group_id: eventGroupId,
                },
                beforeSend: function () {
                    $('#showCmDsSelection').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCmDsSelection').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });

//    Start:: Group Wise Search CM 
        $(document).on('change', '#cmGroupId2', function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var eventGroupId = $("#eventGroupId").val();
            var dsGroupId = $("#dsGroupId").val();
            var cmGroupId2 = $("#cmGroupId2").val();
            if (cmGroupId2 == '0') {
                $('#showGroupTemplateWiseSearchCm').html('');
                return false;
            }
            $.ajax({
                url: "{{URL::to('markingGroup/getGroupTemplateWiseSearchCm')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                    event_group_id: eventGroupId,
                    cm_group_id_2: cmGroupId2,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $("#showGroupTemplateWiseSearchCm").html(res.html);
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

//    Start:: Syn Wise Search CM
        $(document).on("change", "#synId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var eventGroupId = $("#eventGroupId").val();
            var synId = $("#synId").val();
            $('#showSynWiseSearchCm').html('');
            if (synId == '0') {
                $('#showSubSyn').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getSubSyn')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                    event_group_id: eventGroupId,
                    syn_id: synId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSubSyn').html(res.html);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });
        $(document).on("change", "#subSynId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var eventGroupId = $("#eventGroupId").val();
            var synId = $("#synId").val();
            var subSynId = $("#subSynId").val();
            if (subSynId == '0') {
                $('#showSynWiseSearchCm').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('markingGroup/getSynWiseSearchCm')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                    event_group_id: eventGroupId,
                    syn_id: synId,
                    sub_syn_id: subSynId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showSynWiseSearchCm').html(res.html);
                    $('.js-source-states').tooltip();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            }); //ajax
        });
//    End:: Syn Wise Search CM

//    Start:: Individual Search CM
        $(document).on("keyup", "#individualSearch", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var eventGroupId = $("#eventGroupId").val();
            var individualSearch = $(this).val();
//        alert(individualSearch);
            $.ajax({
                url: "{{URL::to('markingGroup/getFilterIndividualCm')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                    event_group_id: eventGroupId,
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

//    Start:: Group Wise Search DS 
        $(document).on('change', '#dsGroupId2', function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var eventGroupId = $("#eventGroupId").val();
            var dsGroupId2 = $("#dsGroupId2").val();
            if (dsGroupId2 == '0') {
                $('#showGroupTemplateWiseSearchDs').html('');
                return false;
            }
            $.ajax({
                url: "{{URL::to('markingGroup/getGroupTemplateWiseSearchDs')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                    event_group_id: eventGroupId,
                    ds_group_id_2: dsGroupId2,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $("#showGroupTemplateWiseSearchDs").html(res.html);
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
//    End:: Group Wise Search DS

//    Start:: Individual Search DS
        $(document).on("keyup", "#individualSearchDs", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            var eventGroupId = $("#eventGroupId").val();
            var individualSearchDs = $(this).val();
//        alert(individualSearch);
            $.ajax({
                url: "{{URL::to('markingGroup/getFilterIndividualDs')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                    event_group_id: eventGroupId,
                    individual_search_ds: individualSearchDs,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $("#showIndividualSearchDs").html(res.html);
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
//    End:: Individual Search DS

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
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{URL::to('markingGroup/setCm')}}",
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

//Start :: set assigned DS
        $(document).on("click", ".assign-selected-ds", function (e) {
            e.preventDefault();
            var dataId = $(this).attr('data-id');
            var form_data = new FormData($('#submitForm' + dataId)[0]);
            $('.ds-select:checked').each(function () {
                var dsId = $(this).val();
                $('<input>').attr('type', 'hidden')
                        .attr('name', 'selected_ds_id[' + dsId + ']')
                        .attr('value', dsId)
                        .attr('class', 'selected-ds-id')
                        .attr('id', 'selectedDsId_' + dsId)
                        .appendTo('#selectedDsForm');

            });
            $('.ds-select:not(:checked)').each(function () {
                var dsId = $(this).val();
                $('#selectedDsForm').find('#selectedDsId_' + dsId).remove();
            });
            $('.ds-selected').each(function () {
                var selectedDsId = $(this).val();
                form_data.append('selected_ds_id[' + selectedDsId + ']', selectedDsId);
            });
            $('.selected-ds-id').each(function () {
                var selectedDsId = $(this).val();
                form_data.append('selected_ds_id[' + selectedDsId + ']', selectedDsId);
            });
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{URL::to('markingGroup/setDs')}}",
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
                    $("#selectedDsList").html(res.html);
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
//End :: set assigned DS

//        Start:: Save CM & DS
        $(document).on('click', '.cm-ds-list-submit', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);

            $('.selected-cm').each(function () {
                var selectedCm = $(this).val();
                form_data.append('selected_cm[' + selectedCm + ']', selectedCm);
            });

            $('.selected-ds').each(function () {
                var selectedDs = $(this).val();
                form_data.append('selected_ds[' + selectedDs + ']', selectedDs);
            });


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
                        url: "{{URL::to('markingGroup/saveMarkingGroup')}}",
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
                            $('.assign-selected-ds').prop('disablded', true);
                        },
                        success: function (res) {
                            toastr.success(res, res.message, options);
                            $(this).prop('disablded', false);
                            $('.assign-selected-cm').prop('disablded', false);
                            $('.assign-selected-ds').prop('disablded', false);
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
                            $('.assign-selected-ds').prop('disablded', false);
                            App.unblockUI();
                        }
                    });
                }
            });
        });
//        End:: Save CM & DS

        // Start Show Assigned CM Modal
        $(document).on("click", "#cmSelection", function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var eventId = $("#eventId").val();
            var subEventId = $("#subEventId").val();
            var subSubEventId = $("#subSubEventId").val();
            var subSubSubEventId = $("#subSubSubEventId").val();
            $.ajax({
                url: "{{ URL::to('assignmentGroupMgt/getAssignedCm')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    event_id: eventId,
                    sub_event_id: subEventId,
                    sub_sub_event_id: subSubEventId,
                    sub_sub_sub_event_id: subSubSubEventId,
                },
                success: function (res) {
                    $("#showCmSelection").html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });
        // End Show Assigned CM Modal


        //Start:: Remove Marking Group
        $(document).on('click', '#buttonDelete', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);

            $('.selected-cm').each(function () {
                var selectedCm = $(this).val();
                form_data.append('selected_cm[' + selectedCm + ']', selectedCm);
            });

            $('.selected-ds').each(function () {
                var selectedDs = $(this).val();
                form_data.append('selected_ds[' + selectedDs + ']', selectedDs);
            });

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
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url: "{{URL::to('markingGroup/removeMarkingGroup')}}",
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
                            $('.assign-selected-ds').prop('disablded', true);
                        },
                        success: function (res) {
                            toastr.success(res, res.message, options);
                            $(this).prop('disablded', false);
                            var courseId = $("#courseId").val();
                            var termId = $("#termId").val();
                            var eventId = $("#eventId").val();
                            var subEventId = $("#subEventId").val();
                            var subSubEventId = $("#subSubEventId").val();
                            var subSubSubEventId = $("#subSubSubEventId").val();
                            var eventGroupId = $('#eventGroupId').val();
                            $('#showCmDsSelection').html('');
                            if (eventGroupId == '0') {
                                return false;
                            }
                            $.ajax({
                                url: "{{ URL::to('markingGroup/getCmDsSelection')}}",
                                type: "POST",
                                dataType: "json",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    course_id: courseId,
                                    term_id: termId,
                                    event_id: eventId,
                                    sub_event_id: subEventId,
                                    sub_sub_event_id: subSubEventId,
                                    sub_sub_sub_event_id: subSubSubEventId,
                                    event_group_id: eventGroupId,
                                },
                                beforeSend: function () {
                                    $('#showCmDsSelection').html('');
                                    App.blockUI({boxed: true});
                                },
                                success: function (res) {
                                    $('#showCmDsSelection').html(res.html);
                                    $('.tooltips').tooltip();
                                    App.unblockUI();
                                },
                                error: function (jqXhr, ajaxOptions, thrownError) {
                                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                                    App.unblockUI();
                                }
                            }); //ajax
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