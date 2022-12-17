@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.MANAGE_LESSON')
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="portlet-body" style="padding-bottom: 0px;  padding-left: 8px; padding-right: 8px">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="vcenter fit bold info" width="15%">@lang('label.LESSON')</td>
                                    <td>{{ $targetInfo->title }}</td>
                                    <td class="vcenter fit bold info" width="15%">@lang('label.DATE_OF_EVAL')</td>
                                    <td> {{ Helper::formatDate($targetInfo->eval_date) }}</td>
                                </tr>
                                <tr>

                                    <td class="vcenter fit bold info" width="15%">@lang('label.DEADLINE_OF_EVAL')</td>
                                    <td>{{ Helper::formatDate($targetInfo->eval_deadline) }}</td>
                                </tr>

                            </tbody></table>
                    </div>
                </div>

                <div class="portlet-body" style="padding-bottom: 0px;  padding-left: 8px; padding-right: 8px !important">
                    <div class="tabbable tabbable-tabdrop" id="tabs">
                        <ul class="nav nav-pills">
                            {{-- <li class="bg-yellow-casablanca  active">
                                <a class="bold tab-color" href="#tab_5_1" data-toggle="tab" aria-expanded="false">@lang('label.OBJECTIVE')</a>
                            </li> --}}
                            <li class="bg-yellow-casablanca active">
                                <a class="bold tab-color" href="#tab_5_2" data-toggle="tab" aria-expanded="false">@lang('label.CONSIDERATIONS')</a>
                            </li>
                            <li class="bg-yellow-casablanca">
                                <a class="bold tab-color" href="#tab_5_3" data-toggle="tab" aria-expanded="false">@lang('label.GRADING')</a>
                            </li>
                            <li class="bg-yellow-casablanca">
                                <a class="bold tab-color" href="#tab_5_4" data-toggle="tab" aria-expanded="true">@lang('label.COMMENT')</a>
                            </li>

                        </ul>

                        <div class="tab-content">
                            {{-- <div class="tab-pane active" id="tab_5_1">
                                {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitObjectiveForm')) !!}
                                {!! Form::hidden('lesson_id',$target->id) !!}
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr class="info">
                                                        <th class="vcenter">@lang('label.SL_NO')</th>
                                                        <th class="vcenter">
                                                            @if(sizeof($objectiveArr) == 0)
                                                            #
                                                            @elseif(sizeof($objectiveArr) >= 1)
                                                            <div class="md-checkbox padding-left-10" >
                                                                {!! Form::checkbox('objective_check_all',1,false, ['id' => 'objectiveCheckAll', 'class'=> 'md-check']) !!}
                                                                <label for="objectiveCheckAll">
                                                                    <span class=""></span>
                                                                    <span class="check"></span>
                                                                    <span class="box"></span>@lang('label.CHECK_ALL')
                                                                </label>

                                                            </div>
                                                            @endif
                                                        </th>
                                                        <th class="vcenter">@lang('label.NAME')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($objectiveArr))
                                                    <?php $sl = 0 ?>
                                                    @foreach($objectiveArr as $objectiveId => $objectiveName)
                                                    <?php
                                                    $checkedObjective = '';
                                                    if (!empty($prevRelatedObjective)) {
                                                        if (in_array($objectiveId, $prevRelatedObjective)) {
                                                            $checkedObjective = 'checked';
                                                        }
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="vcenter" width="20%">{!! ++$sl!!}</td>
                                                        <td class="text-center vcenter" width="20%">
                                                            <div class="md-checkbox has-success">
                                                                {!! Form::checkbox('objective['.$objectiveId.']', $objectiveId, false, ['id' => 'finishedObjective_'.$objectiveId, 'data-id'=> $objectiveId,'class'=> 'md-check objective-check',$checkedObjective]) !!}
                                                                <label for="{!! 'finishedObjective_'.$objectiveId !!}">
                                                                    <span class="inc"></span>
                                                                    <span class="check"></span>
                                                                    <span class="box"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td class="vcenter">{!! $objectiveName !!}</td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="5">
                                                            @lang('label.NO_OBJECTIVE_FOUND')
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        @if(!empty($objectiveArr))
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-4 col-md-8">

                                                    <button class="btn btn-success" id="finishedObjectiveBtn" type="button">
                                                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                                    </button>

                                                    <a href="{{ URL::to('/lesson/') }}" class="btn btn-outline grey-salsa">@lang('label.CANCEL')</a>

                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div> --}}
                            <div class="tab-pane active" id="tab_5_2">
                                {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitConsiderationForm')) !!}
                                {!! Form::hidden('lesson_id',$target->id) !!}
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr class="info">
                                                        <th class="vcenter">@lang('label.SL_NO')</th>
                                                        <th class="vcenter">
                                                            @if(sizeof($considerationArr) == 0)
                                                            #
                                                            @elseif(sizeof($considerationArr) >= 1)
                                                            <div class="md-checkbox" >
                                                                {!! Form::checkbox('consideration_check_all',1,false, ['id' => 'considerationCheckAll', 'class'=> 'md-check']) !!}
                                                                <label for="considerationCheckAll">
                                                                    <span class=""></span>
                                                                    <span class="check"></span>
                                                                    <span class="box"></span>@lang('label.CHECK_ALL')
                                                                </label>

                                                            </div>
                                                            @endif
                                                        </th>
                                                        <th class="vcenter">@lang('label.NAME')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($considerationArr))
                                                    <?php $sl = 0 ?>
                                                    @foreach($considerationArr as $considerationId => $considerationName)
                                                    <?php
                                                    $checkedConsideration = '';
                                                    if (!empty($prevRelatedConsideration)) {
                                                        if (in_array($considerationId, $prevRelatedConsideration)) {
                                                            $checkedConsideration = 'checked';
                                                        }
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="vcenter" width="20%">{!! ++$sl!!}</td>
                                                        <td class="text-center vcenter" width="20%">
                                                            <div class="md-checkbox has-success">
                                                                {!! Form::checkbox('consideration['.$considerationId.']', $considerationId, $checkedConsideration, ['id' => 'finishedConsideration_'.$considerationId, 'data-id'=> $considerationId,'class'=> 'md-check consideration-check']) !!}
                                                                <label for="{!! 'finishedConsideration_'.$considerationId !!}">
                                                                    <span class="inc"></span>
                                                                    <span class="check"></span>
                                                                    <span class="box"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td class="vcenter">{!! $considerationName !!}</td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="5">
                                                            @lang('label.NO_CONSIDERATION_FOUND')
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        @if(!empty($considerationArr))
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-4 col-md-8">

                                                    <button class="btn btn-success" id="finishedConsiderationBtn" type="button">
                                                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                                    </button>

                                                    <a href="{{ URL::to('/lesson/') }}" class="btn btn-outline grey-salsa">@lang('label.CANCEL')</a>

                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane" id="tab_5_3">
                                {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitGradingForm')) !!}
                                {!! Form::hidden('lesson_id',$target->id) !!}
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr class="info">
                                                        <th class="vcenter">@lang('label.SL_NO')</th>
                                                        <th class="vcenter">
                                                            @if(sizeof($gradingArr) == 0)
                                                            #
                                                            @elseif(sizeof($gradingArr) >= 1)
                                                            <div class="md-checkbox" >
                                                                {!! Form::checkbox('grading_check_all',1,false, ['id' => 'gradingCheckAll', 'class'=> 'md-check']) !!}
                                                                <label for="gradingCheckAll">
                                                                    <span class=""></span>
                                                                    <span class="check"></span>
                                                                    <span class="box"></span>@lang('label.CHECK_ALL')
                                                                </label>

                                                            </div>
                                                            @endif
                                                        </th>
                                                        <th class="vcenter">@lang('label.NAME')</th>
                                                        <th class="vcenter text-center">@lang('label.WT')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($gradingArr))
                                                    <?php $sl = 0 ?>
                                                    @foreach($gradingArr as $grading)
                                                    <?php
                                                    $checkedGrading = '';
                                                    if (!empty($prevRelatedGrading)) {
                                                        if (in_array($grading->id, $prevRelatedGrading)) {
                                                            $checkedGrading = 'checked';
                                                        }
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="vcenter" width="20%">{!! ++$sl!!}</td>
                                                        <td class="text-center vcenter" width="20%">
                                                            <div class="md-checkbox has-success">
                                                                {!! Form::checkbox('grading['.$grading->id.']', $grading->id, $checkedGrading, ['id' => 'finishedGrading_'.$grading->id, 'data-id'=> $grading->id,'class'=> 'md-check grading-check']) !!}
                                                                <label for="{!! 'finishedGrading_'.$grading->id !!}">
                                                                    <span class="inc"></span>
                                                                    <span class="check"></span>
                                                                    <span class="box"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td class="vcenter">{!! $grading->title !!}</td>
                                                        <td class="vcenter text-center">{!! $grading->wt !!}</td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="5">
                                                            @lang('label.NO_GRADING_FOUND')
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        @if(!empty($gradingArr))
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-4 col-md-8">

                                                    <button class="btn btn-success" id="finishedGradingBtn" type="button">
                                                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                                    </button>

                                                    <a href="{{ URL::to('/lesson/') }}" class="btn btn-outline grey-salsa">@lang('label.CANCEL')</a>

                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane" id="tab_5_4">
                                {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitCmntForm')) !!}
                                {!! Form::hidden('lesson_id',$target->id) !!}
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr class="info">
                                                        <th class="vcenter">@lang('label.SL_NO')</th>
                                                        <th class="vcenter">
                                                            @if(sizeof($cmntArr) == 0)
                                                            #
                                                            @elseif(sizeof($cmntArr) >= 1)
                                                            <div class="md-checkbox" >
                                                                {!! Form::checkbox('cmnt_check_all',1,false, ['id' => 'cmntCheckAll', 'class'=> 'md-check']) !!}
                                                                <label for="cmntCheckAll">
                                                                    <span class=""></span>
                                                                    <span class="check"></span>
                                                                    <span class="box"></span>@lang('label.CHECK_ALL')
                                                                </label>

                                                            </div>
                                                            @endif
                                                        </th>
                                                        <th class="vcenter">@lang('label.TITLE')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($cmntArr))
                                                    <?php $sl = 0 ?>
                                                    @foreach($cmntArr as $cmntId => $cmntName)
                                                    <?php
                                                    $checkedCmnt = '';
                                                    if (!empty($prevRelatedCmnt)) {
                                                        if (in_array($cmntId, $prevRelatedCmnt)) {
                                                            $checkedCmnt = 'checked';
                                                        }
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="vcenter" width="20%">{!! ++$sl!!}</td>
                                                        <td class="text-center vcenter" width="20%">
                                                            <div class="md-checkbox has-success">
                                                                {!! Form::checkbox('cmnt['.$cmntId.']', $cmntId, $checkedCmnt, ['id' => 'finishedCmnt_'.$cmntId, 'data-id'=> $cmntId,'class'=> 'md-check cmnt-check']) !!}
                                                                <label for="{!! 'finishedCmnt_'.$cmntId !!}">
                                                                    <span class="inc"></span>
                                                                    <span class="check"></span>
                                                                    <span class="box"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td class="vcenter">{!! $cmntName !!}</td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="5">
                                                            @lang('label.NO_COMMENT_FOUND')
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        @if(!empty($cmntArr))
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-4 col-md-8">

                                                    <button class="btn btn-success" id="finishedCmntBtn" type="button">
                                                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                                    </button>

                                                    <a href="{{ URL::to('/lesson/') }}" class="btn btn-outline grey-salsa">@lang('label.CANCEL')</a>

                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {

        // this code for  database 'check all' if all checkbox items are checked
        if ($('.objective-check:checked').length == $('.objective-check').length) {
            $('#objectiveCheckAll').prop("checked", true);
        } else {
            $('#objectiveCheckAll').prop("checked", false);
        }




        $("#objectiveCheckAll").change(function () {
            if (this.checked) {
                $(".objective-check").each(function () {
                    this.checked = true;
                });
            } else {
                $(".objective-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.objective-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#objectiveCheckAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.objective-check:checked').length == $('.objective-check').length) {
                $('#objectiveCheckAll')[0].checked = true; //change 'check all' checked status to true
            }
        });

        $(document).on('click', '#finishedObjectiveBtn', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitObjectiveForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            swal({
                title: 'Are you sure?',
                text: "@lang('label.YOU_WANT_TO_ADD_OBJECTIVE')",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{route('lesson.saveObjective')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            toastr.success(res.message, res.heading, options);
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
                                toastr.error('Something went wrong', 'Error', options);
                            }
                            App.unblockUI();
                        }
                    });
                }

            });

        });








//         // this code for  database 'check all' if all checkbox items are checked
//        if ($('.consideration-check:checked').length == $('.consideration-check').length) {
//            $('#considerationCheckAll').prop("checked", true);
//        } else {
//            $('#considerationCheckAll').prop("checked", false);
//        }

        $("#considerationCheckAll").change(function () {
            if (this.checked) {
                $(".consideration-check").each(function () {
                    this.checked = true;
                });
            } else {
                $(".consideration-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.consideration-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#considerationCheckAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.consideration-check:checked').length == $('.consideration-check').length) {
                $('#considerationCheckAll')[0].checked = true; //change 'check all' checked status to true
            }
        });


         $(document).on('click', '#finishedConsiderationBtn', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitConsiderationForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            swal({
                title: 'Are you sure?',
                text: "@lang('label.YOU_WANT_TO_ADD_CONSIDERATION')",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{route('lesson.saveConsideration')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            toastr.success(res.message, res.heading, options);
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
                                toastr.error('Something went wrong', 'Error', options);
                            }
                            App.unblockUI();
                        }
                    });
                }

            });

        });


         $("#gradingCheckAll").change(function () {
            if (this.checked) {
                $(".grading-check").each(function () {
                    this.checked = true;
                });
            } else {
                $(".grading-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.grading-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#gradingCheckAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.grading-check:checked').length == $('.grading-check').length) {
                $('#gradingCheckAll')[0].checked = true; //change 'check all' checked status to true
            }
        });


        $(document).on('click', '#finishedGradingBtn', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitGradingForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            swal({
                title: 'Are you sure?',
                text: "@lang('label.YOU_WANT_TO_ADD_GRADING')",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{route('lesson.saveGrading')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            toastr.success(res.message, res.heading, options);
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
                                toastr.error('Something went wrong', 'Error', options);
                            }
                            App.unblockUI();
                        }
                    });
                }

            });

        });


         $("#cmntCheckAll").change(function () {
            if (this.checked) {
                $(".cmnt-check").each(function () {
                    this.checked = true;
                });
            } else {
                $(".cmnt-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.cmnt-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#cmntCheckAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.cmnt-check:checked').length == $('.cmnt-check').length) {
                $('#cmntCheckAll')[0].checked = true; //change 'check all' checked status to true
            }
        });


        $(document).on('click', '#finishedCmntBtn', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitCmntForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            swal({
                title: 'Are you sure?',
                text: "@lang('label.YOU_WANT_TO_ADD_COMMENT')",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{route('lesson.saveCmnt')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            toastr.success(res.message, res.heading, options);
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
                                toastr.error('Something went wrong', 'Error', options);
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
