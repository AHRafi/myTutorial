@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CM_TO_SYN')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7 show-term">
                                    {!! Form::select('term_id', $termList, Request::get('term_id'),  ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="synId">@lang('label.SYN') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7 show-syn">
                                    {!! Form::select('syn_id', $synList, Request::get('syn_id'),  ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div id="showSubSynCm">
                            @if(!empty(Request::get('sub_syn_id')))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="subSynId">@lang('label.SUB_SYN') :<span class="text-danger"> *</span></label>
                                    <div class="col-md-7">
                                        {!! Form::select('sub_syn_id', $subSynList, Request::get('sub_syn_id'),  ['class' => 'form-control js-source-states', 'id' => 'subSynId']) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!--get module data-->
                    <div id="showCm">
                        @if(!empty(Request::get('course_id')) && !empty(Request::get('syn_id')) && !empty(Request::get('term_id')))
                        <div class="row">
                            @if(!$targetArr->isEmpty())
                            <div class="col-md-12 margin-top-10">
                                <span class="label label-success">
                                    @lang('label.TOTAL_NO_OF_CM'):&nbsp;{!! !empty($targetArr)?sizeof($targetArr):0 !!}
                                </span>&nbsp;
                                <span class="label label-purple">
                                    @lang('label.TOTAL_ASSIGNED_CM'):&nbsp;{!! !$checkPreviousDataArr->isEmpty() ? sizeof($checkPreviousDataArr) : 0 !!}
                                </span>&nbsp;

                                <?php
                                $sub = !empty(Request::get('sub_syn_id')) ? 'Sub ' : '';
                                ?>
                                <button class="label label-primary tooltips" href="#modalAssignedCm" id="assignedCm" 
                                        data-course-id="{!! !empty(Request::get('course_id')) ? Request::get('course_id') : 0 !!}" 
                                        data-term-id="{!! !empty(Request::get('term_id')) ? Request::get('term_id') : 0 !!}" 
                                        data-syn-id="{!! !empty(Request::get('syn_id')) ? Request::get('syn_id') : 0 !!}" 
                                        data-sub-syn-id="{!! !empty(Request::get('sub_syn_id')) ? Request::get('sub_syn_id') : 0 !!}" 
                                        data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_CM')">
                                    @lang('label.TOTAL_NO_OF_CM_ASSIGNED_TO_THIS_SYN_SUB_SYN', ['sub' => $sub]): &nbsp;{!! $totalNumOfAssignedCm !!}&nbsp; <i class="fa fa-search-plus"></i>
                                </button>
                            </div>
                            {!! Form::hidden('has_sub_syn', !empty($hasSubSyn) ? $hasSubSyn : 0) !!}
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
                                        $class = 'cm-to-syn';
                                        if (!empty($previousCmToSynList)) {
                                            $checked = array_key_exists($target->id, $previousCmToSynList) ? 'checked' : '';
                                            if (!empty($checkPreviousDataList[$target->id])) {
                                                $class = '';
                                                if ($request->syn_id != $checkPreviousDataList[$target->id]) {
                                                    $disabled = 'disabled';
                                                    $syn = !empty($checkPreviousDataList[$target->id]) && !empty($synDataList[$checkPreviousDataList[$target->id]]) ? $synDataList[$checkPreviousDataList[$target->id]] : '';
                                                    $title = __('label.ALREADY_ASSIGNED_TO_SYN', ['syn' => $syn]);

                                                    if (!empty($checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]])) {
                                                        $subSyn = !empty($checkPreviousDataList[$target->id]) && !empty($checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]) && !empty($subSynDataList[$checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]]) ? $subSynDataList[$checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]] : '';
                                                        $title = __('label.ALREADY_ASSIGNED_TO_SUB_SYN_OF_THIS_SYN', ['syn' => $syn, 'sub_syn' => $subSyn]);
                                                    }
                                                } else {
                                                    if (!empty($checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]])) {
                                                        if (!empty($request->sub_syn_id) && $request->sub_syn_id != $checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]) {
                                                            $disabled = 'disabled';
                                                            $syn = !empty($checkPreviousDataList[$target->id]) && !empty($synDataList[$checkPreviousDataList[$target->id]]) ? $synDataList[$checkPreviousDataList[$target->id]] : '';
                                                            $subSyn = !empty($checkPreviousDataList[$target->id]) && !empty($checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]) && !empty($subSynDataList[$checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]]) ? $subSynDataList[$checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]] : '';
                                                            $title = __('label.ALREADY_ASSIGNED_TO_SUB_SYN_OF_THIS_SYN', ['syn' => $syn, 'sub_syn' => $subSyn]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td class="vcenter text-center">{!! ++$sl !!}</td>
                                            <td class="vcenter">
                                                <div class="md-checkbox has-success tooltips" title="{!! $title !!}" >
                                                    {!! Form::checkbox('cm_id['.$target->id.']',$target->id,$checked, ['id' => $target->id, 'class'=> 'md-check '.$class,$disabled]) !!}
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
                                            <td class="vcenter">{!! !empty($target->rank_name) ? $target->rank_name : '' !!} </td>
                                            <td class="vcenter">{!! $target->full_name!!}</td>
                                            <td class="vcenter">{!! !empty($target->wing_name) ? $target->wing_name : '' !!}</td>

                                            <td class="vcenter">
                                                {!! !empty($checkPreviousDataList[$target->id]) && !empty($synDataList[$checkPreviousDataList[$target->id]]) ? $synDataList[$checkPreviousDataList[$target->id]] : ''!!}
                                                {!! !empty($checkPreviousDataList[$target->id]) && !empty($checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]) && !empty($subSynDataList[$checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]]) ? '('.$subSynDataList[$checkSubSynWisePrevDataList[$target->id][$checkPreviousDataList[$target->id]]].')' : ''!!}
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
    <div class="modal-dialog modal-lg">
        <div id="showAssignedCm">

        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {

<?php
if (!empty(Request::get('course_id')) && !empty(Request::get('syn_id')) && !empty(Request::get('term_id'))) {
    if (!$targetArr->isEmpty()) {
        ?>
                $('#dataTable').dataTable({
                    "paging": true,
                    "pageLength": 100,
                    "info": false,
                    "order": false
                });

                $('#checkAll').change(function () {  //'check all' change
                    $('.cm-to-syn').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                });
                $('.cm-to-syn').change(function () {
                    if (this.checked == false) { //if this item is unchecked
                        $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
                    }
                    //check 'check all' if all checkbox items are checked
                    if ($('.cm-to-syn:checked').length == $('.cm-to-syn').length) {
                        $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
                    }
                });

                //'check all' change
                $(document).on('click', '#checkAll', function () {
                    if (this.checked) {
                        $('.cm-to-syn').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                    } else {
                        $(".cm-to-syn").removeAttr('checked');
                    }
                });

                $(document).on('click', '.cm-to-syn', function () {
                    allCheck();
                });
                allCheck();
        <?php
    }
}
?>

//        $(document).on("change", "#courseId", function () {
//
//            var courseId = $("#courseId").val();
//
//            $('#showSubSynCm').html('');
//            $('#showCm').html('');
//            $('#termId').html("<option value='0'>@lang('label.SELECT_TERM_OPT')</option>");
//            $('#synId').html("<option value='0'>@lang('label.SELECT_SYN_OPT')</option>");
//
//            var options = {
//                closeButton: true,
//                debug: false,
//                positionClass: "toast-bottom-right",
//                onclick: null
//            };
//
//            $.ajax({
//                url: "{{ URL::to('cmToSyn/getTerm')}}",
//                type: "POST",
//                dataType: "json",
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                data: {
//                    course_id: courseId,
//                },
//                beforeSend: function () {
//                    App.blockUI({boxed: true});
//                },
//                success: function (res) {
//                    $('#termId').html(res.html);
//                    $('.js-source-states').select2();
//                    App.unblockUI();
//                },
//                error: function (jqXhr, ajaxOptions, thrownError) {
//                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
//                    App.unblockUI();
//                }
//            });//ajax
//        });

        $(document).on("change", "#termId", function () {

            var courseId = $("#courseId").val();
            var termId = $("#termId").val();

            $('#showSubSynCm').html('');
            $('#showCm').html('');
            $('#synId').html("<option value='0'>@lang('label.SELECT_SYN_OPT')</option>");

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('cmToSyn/getSyn')}}",
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
                    $('.show-syn').html(res.html);
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

            $('#subSynId').html("<option value='0'>@lang('label.SELECT_SUB_SYN_OPT')</option>");
            $('#showCm').html('');

            if (synId == '0') {
                $('#showSubSynCm').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('cmToSyn/getSubSynCm')}}",
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
                    $('#showSubSynCm').html(res.html);
                    $('.tooltips').tooltip();
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
                url: "{{ URL::to('cmToSyn/getCm')}}",
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
//            alert('ddd');
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
                        url: "{{URL::to('cmToSyn/saveCmToSyn')}}",
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
                            toastr.success(res, res.message, options);
                            $('.button-submit').prop('disabled', false);
                            App.unblockUI();
                            var courseId = $("#courseId").val();
                            var synId = $("#synId").val();
                            var subSynId = res.subSynId != 0 ? "&sub_syn_id=" + res.subSynId : '';
                            var termId = $("#termId").val();

                            location = "cmToSyn?course_id=" + courseId + "&syn_id=" + synId + "&term_id=" + termId + subSynId;
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
            var courseId = $(this).attr('data-course-id');
            var termId = $(this).attr('data-term-id');
            var synId = $(this).attr('data-syn-id');
            var subSynId = $(this).attr('data-sub-syn-id');
            $.ajax({
                url: "{{ URL::to('cmToSyn/getAssignedCm')}}",
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

        if ($('.cm-to-syn:checked').length == $('.cm-to-syn').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
@stop