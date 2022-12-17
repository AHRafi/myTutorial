@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CM_TO_SUB_SYN')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') :</label>
                        <div class="col-md-8">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4">
                            {!! Form::select('course_id', $courseList, Request::get('course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4 show-term">
                            {!! Form::select('term_id', $termList, Request::get('term_id'),  ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4" for="synId">@lang('label.SYN') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4 show-syn">
                            {!! Form::select('syn_id', $synList, Request::get('syn_id'),  ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4" for="subSynId">@lang('label.SUB_SYN') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4 show-sub-syn">
                            {!! Form::select('sub_syn_id', $subSynList, Request::get('sub_syn_id'),  ['class' => 'form-control js-source-states', 'id' => 'subSynId']) !!}
                        </div>
                    </div>
                    <!--get module data-->
                    <div id="showCm">
                        @if(!empty(Request::get('course_id')) && !empty(Request::get('syn_id')) && !empty(Request::get('term_id')) && !empty(Request::get('sub_syn_id')))
                        <div class="row">
                            @if(!$targetArr->isEmpty())
                            <div class="col-md-12 margin-top-10">
                                <span class="label label-success">
                                    @lang('label.TOTAL_NO_OF_CM_ASSIGNED_TO_THIS_SYN'):&nbsp;{!! !empty($targetArr)?sizeof($targetArr):0 !!}
                                </span>&nbsp;

                                <button class="label label-primary tooltips" href="#modalAssignedCm" id="assignedCm"  data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_CM')">
                                    @lang('label.TOTAL_NO_OF_CM_ASSIGNED_TO_THIS_SUB_SYN'): &nbsp;{!! $totalNumOfAssignedCm !!}&nbsp; <i class="fa fa-search-plus"></i>
                                </button>
                            </div>
                            <div class="col-md-12 margin-top-10">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                            <th class="vcenter" width="15%">
                                                <div class="md-checkbox has-success tooltips" title="@lang('label.SELECT_ALL')">
                                                    {!! Form::checkbox('check_all',1,false, ['id' => 'checkAll', 'class'=> 'md-check']) !!}
                                                    <label for="checkAll">
                                                        <span class="inc"></span>
                                                        <span class="check mark-caheck"></span>
                                                        <span class="box mark-caheck"></span>
                                                    </label>&nbsp;&nbsp;
                                                    <span class="bold">@lang('label.CHECK_ALL')</span>
                                                </div>
                                            </th>
                                            <th class="text-center vcenter">@lang('label.PHOTO')</th>
                                            <th class=" vcenter">@lang('label.PERSONAL_NO')</th>
                                            <th class="vcenter">@lang('label.RANK')</th>
                                            <th class=" vcenter">@lang('label.FULL_NAME')</th>
                                            <th class=" vcenter">@lang('label.WING')</th>
                                            <th class=" vcenter">@lang('label.ASSIGNED_TO')</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php $sl = 0; ?>
                                        @foreach($targetArr as $target)
                                        <?php
                                        $checked = '';
                                        $disabled = '';
                                        $title = '';
                                        $class = 'cm-to-sub-syn';
                                        if (!empty($previousCmToSubSynList)) {
                                            $checked = array_key_exists($target->id, $previousCmToSubSynList) ? 'checked' : '';

                                            if (!empty($checkPreviousDataList[$target->id]) && ($request->sub_syn_id != $checkPreviousDataList[$target->id])) {
                                                $disabled = 'disabled';
                                                $class = '';
                                                $subSyn = !empty($checkPreviousDataList[$target->id]) && !empty($subSynDataList[$checkPreviousDataList[$target->id]]) ? $subSynDataList[$checkPreviousDataList[$target->id]] : '';
                                                $title = __('label.ALREADY_ASSIGNED_TO_THIS_SUB_SYN', ['subSyn' => $subSyn]);
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td class="vcenter text-center">{!! ++$sl !!}</td>
                                            <td class="vcenter">
                                                <div class="md-checkbox has-success tooltips" title="{!! $title !!}" >
                                                    {!! Form::checkbox('cm_id['.$target->id.']',$target->id,$checked, ['id' => $target->id, 'class'=> 'md-check '.$class, $disabled]) !!}
                                                    <label for="{!! $target->id !!}">
                                                        <span class="inc"></span>
                                                        <span class="check mark-caheck"></span>
                                                        <span class="box mark-caheck"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center vcenter" width="50px">
                                                <?php if (!empty($target->photo && File::exists('public/uploads/cm/' . $target->photo))) { ?>
                                                    <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target->photo}}" alt="{{ $target->full_name}}"/>
                                                <?php } else { ?>
                                                    <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $target->full_name}}"/>
                                                <?php } ?>
                                            </td>
                                            <td class="vcenter">{!! $target->personal_no !!}</td>
                                            <td class="vcenter">{!! !empty($target->rank_code) ? $target->rank_code : '' !!} </td>
                                            <td class="vcenter">{!! $target->full_name!!}</td>
                                            <td class="vcenter">{!! !empty($target->wing_name) ? $target->wing_name : '' !!}</td>

                                            <td class="vcenter">
                                                {!! !empty($checkPreviousDataList[$target->id]) && !empty($subSynDataList[$checkPreviousDataList[$target->id]]) ? $subSynDataList[$checkPreviousDataList[$target->id]] : ''!!}
                                            </td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                                                <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                            </button>
                                            <a href="{{ URL::to('cmToSyn') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="col-md-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_FOUND') !!}</strong></p>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>


<!--Assigned Cm list-->
<div class="modal fade" id="modalAssignedCm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div id="showAssignedCm">

        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
<?php
if (!empty(Request::get('course_id')) && !empty(Request::get('syn_id')) && !empty(Request::get('term_id')) && !empty(Request::get('sub_syn_id'))) {
    if (!$targetArr->isEmpty()) {
        ?>
                $('#dataTable').dataTable({
                    "paging": true,
                    "info": false,
                    "order": false
                });
                $('#checkAll').change(function () {  //'check all' change
                    $('.cm-to-sub-syn').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                });
                $('.cm-to-sub-syn').change(function () {
                    if (this.checked == false) { //if this item is unchecked
                        $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
                    }
                    //check 'check all' if all checkbox items are checked
                    if ($('.cm-to-sub-syn:checked').length == $('.cm-to-sub-syn').length) {
                        $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
                    }
                });

                //'check all' change
                $(document).on('click', '#checkAll', function () {
                    if (this.checked) {
                        $('.cm-to-sub-syn').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                    } else {
                        $(".cm-to-sub-syn").removeAttr('checked');
                    }
                });

                $(document).on('click', '.cm-to-sub-syn', function () {
                    allCheck();
                });

                allCheck();
        <?php
    }
}
?>

        $(document).on("change", "#courseId", function () {

            var courseId = $("#courseId").val();

            $('#showCm').html('');
            $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
            $('#synId').html("<option value='0'>@lang('label.SELECT_SYN_OPT')</option>");
            $('#subSynId').html("<option value='0'>@lang('label.SELECT_SUB_SYN_OPT')</option>");

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('cmToSubSyn/getTerm')}}",
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
                    $('#termId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#termId", function () {

            var courseId = $("#courseId").val();
            var termId = $("#termId").val();

            $('#showCm').html('');
            $('#synId').html("<option value='0'>@lang('label.SELECT_SYN_OPT')</option>");
            $('#subSynId').html("<option value='0'>@lang('label.SELECT_SUB_SYN_OPT')</option>");

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('cmToSubSyn/getSyn')}}",
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
                    $('#synId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#synId", function () {

            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var synId = $("#synId").val();

            $('#showCm').html('');
            $('#subSynId').html("<option value='0'>@lang('label.SELECT_SUB_SYN_OPT')</option>");

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('cmToSubSyn/getSubSyn')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    syn_id: synId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#subSynId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on("change", "#subSynId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var synId = $("#synId").val();
            var subSynId = $("#subSynId").val();

            if (subSynId == '0') {
                $('#showCm').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('cmToSubSyn/getCm')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    syn_id: synId,
                    sub_syn_id: subSynId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCm').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        //form submit
        $(document).on('click', '.button-submit', function (e) {
            e.preventDefault();
            var oTable = $('#dataTable').dataTable();
            var x = oTable.$('input,select,textarea').serializeArray();
            $.each(x, function (i, field) {

                $("#submitForm").append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', field.name)
                        .val(field.value));
            });
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
                        url: "{{URL::to('cmToSubSyn/saveCmToSubSyn')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('.button-submit').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success(res, '@lang("label.CM_HAS_BEEN_ASSIGNED")', options);
                            $('.button-submit').prop('disabled', false);
                            App.unblockUI();
                            var courseId = $("#courseId").val();
                            var synId = $("#synId").val();
                            var subSynId = $("#subSynId").val();
                            var termId = $("#termId").val();
                            location = "cmToSubSyn?course_id=" + courseId + "&syn_id=" + synId + "&term_id=" + termId + "&sub_syn_id=" + subSynId;
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
                            $('.button-submit').prop('disabled', false);
                            App.unblockUI();
                        }

                    });
                }
            });
        });

        // Start Show Assigned CM Modal
        $(document).on("click", "#assignedCm", function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            var synId = $("#synId").val();
            var subSynId = $("#subSynId").val();

            $.ajax({
                url: "{{ URL::to('cmToSubSyn/getAssignedCm')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    syn_id: synId,
                    sub_syn_id: subSynId,
                },
                success: function (res) {
                    $("#showAssignedCm").html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });
        // End Show Assigned CM Modal

    });

    function allCheck() {

        if ($('.cm-to-sub-syn:checked').length == $('.cm-to-sub-syn').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
@stop