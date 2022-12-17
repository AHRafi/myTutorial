@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.GS_TO_LESSON')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(['group' => 'form', 'url' => '#', 'class' => 'form-horizontal', 'id' => 'submitForm']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{ $activeTrainingYear->name }}
                                        </strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYear->id, ['id' => 'trainingYearId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{ $activeCourse->name }} </strong>
                                    </div>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="gsId">@lang('label.GS') :<span
                                        class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('gs_id', $gsList, Request::get('gs_id') ?? null, [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'gsId',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--get module data-->
                    <div id="showLesson">
                        @if (!empty(Request::get('gs_id')))
                        @if (!empty($targetArr))
                        <div class="row">
                            <div class="col-md-12">
                                <span class="label label-sm label-blue-steel">
                                    @lang('label.TOTAL_NO_OF_LESSON'):&nbsp;{!! !empty($targetArr) ? sizeOf($targetArr) : 0 !!}
                                </span>&nbsp;
                                <span class="label label-purple">@lang('label.TOTAL_NO_OF_LESSON_ASSIGNED'):
                                    &nbsp;{!! !empty($count) ? $count : 0 !!}
                                </span>&nbsp;

                                <button class="label label-sm label-green-seagreen btn-label-groove tooltips"
                                        href="#modalAssignedLessen" id="assignedLesson" data-toggle="modal"
                                        title="@lang('label.SHOW_LESSON_ASSIGNED_TO_THIS_GS')">
                                    @lang('label.TOTAL_NO_OF_LESSON_ASSIGNED_TO_THIS_GS'):&nbsp;{!! !empty($count) ? $count : 0 !!}&nbsp; <i
                                        class="fa fa-search-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row margin-top-10">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center vcenter " width="5%">@lang('label.SL_NO')
                                            </th>
                                            <th class="vcenter" width="10%">
                                                <?php
                                                //disable
                                                $disabledCAll = '';
                                                if (!empty($disableDataArr)) {
                                                    $disabledCAll = 'disabled';
                                                }
                                                ?>

                                                <div class="md-checkbox has-success">
                                                    {!! Form::checkbox('check_all', 1, false, ['id' => 'checkAll', 'class' => 'md-check', $disabledCAll]) !!}
                                                    <label for="checkAll">
                                                        <span class="inc"></span>
                                                        <span class="check mark-caheck"></span>
                                                        <span class="box mark-caheck"></span>
                                                    </label>&nbsp;&nbsp;
                                                    <span class="bold">@lang('label.CHECK_ALL')</span>
                                                </div>
                                            </th>
                                            <th class="vcenter">@lang('label.LESSON')</th>
                                            <th class="vcenter">@lang('label.SUBJECT')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sl = 0; @endphp

                                        @foreach ($targetArr as $target)
                                        <?php
                                        $disabled = '';
                                        $checked = '';
                                        $title = __('label.CHECK');
                                        if (!empty($disableDataArr[$target['subject_id']][$target['id']])) {
                                            $disabled = 'disabled';
                                            $title = __('label.THIS_LESSON_IS_ALREADY_ASSIGNED_TO_GS', ['gs' => $disableDataArr[$target['subject_id']][$target['id']]]);
                                        }

                                        if (!empty($assignedLesson[$target['subject_id']][$target['id']])) {
                                            $checked = 'checked';
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center vcenter">{!! ++$sl !!}</td>
                                            <td class="vcenter">
                                                <div class="md-checkbox has-success tooltips">
                                                    {!! Form::checkbox('lesson[' . $target['subject_id'] . '][' . $target['id'] . ']', $target['id'], $checked, [
                                                    'id' => $target['id'] . '_' . $target['subject_id'],
                                                    'data-id' => $target['id'],
                                                    'class' => 'md-check gs-to-lesson',
                                                    $disabled,
                                                    ]) !!}

                                                    <label for="{!! $target['id'] . '_' . $target['subject_id'] ?? '' !!}">
                                                        <span class="inc"></span>
                                                        <span class="check mark-caheck tooltips"
                                                              title="{{ $title }}"></span>
                                                        <span class="box mark-caheck tooltips"
                                                              title="{{ $title }}"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="vcenter">{!! $target['lesson'] ?? '' !!}</td>
                                            <td class="vcenter">{!! $target['subject'] ?? '' !!}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- if submit wt chack Start -->
                        <div class="form-actions">
                            <div class="col-md-offset-4 col-md-8">
                                <button class="button-submit btn btn-circle green" type="button">
                                    <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                </button>
                                <a href="{{ URL::to('gsToLesson?gs_id=' . Request::get('gs_id')) }}"
                                   class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                            </div>
                        </div>
                        @else
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_LESSON_FOUND')</p>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>

<!--Assigned Sub Event list-->
<div class="modal fade" id="modalAssignedLessen" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showAssignedLesson">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
<?php if (!empty($targetArr)) { ?>
            allCheck();
            $('#dataTable').dataTable({
                "paging": true,
                "pageLength": 100,
                "info": false,
                "order": false
            });
<?php } ?>

        //'check all' change
        $(document).on('click', '#checkAll', function () {
            if ($('#checkAll').is(':checked')) {
                $('.gs-to-lesson').each(function () {
                    if (this.checked == false) {
                        var key = $(this).attr('data-id');
                        $(this).prop('checked', true);
                    }
                });
            } else {
                $(".gs-to-lesson").removeAttr('checked');
                $(".has-checked").attr('disabled', true);
                $(".has-checked").removeAttr('checked');
            }
        });

        $(document).on('click', '.gs-to-lesson', function () {
            allCheck();
        });

        function allCheck() {

            if ($('.gs-to-lesson:checked').length == $('.gs-to-lesson').length) {
                $('#checkAll')[0].checked = true;
            } else {
                $('#checkAll')[0].checked = false;
            }
        }
        // End:  CHECK ALL



        $(document).on("change", "#gsId", function () {
            var courseId = $("#courseId").val();
            var gsId = $("#gsId").val();
            if (gsId == '0') {
                $('#showLesson').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('gsToLesson/getLesson') }}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    gs_id: gsId,
                },
                beforeSend: function () {
                    App.blockUI({
                        boxed: true
                    });
                },
                success: function (res) {
                    $('#showLesson').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang('label.SOMETHING_WENT_WRONG')', 'Error', options);
                            App.unblockUI();
                }
            }); //ajax
        });


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
                        url: "{{ URL::to('gsToLesson/saveGsToLesson') }}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res.message, res.heading, options);
                            var gsId = form_data.get('gs_id');
                            location = 'gsToLesson?gs_id=' + gsId;
                        },
                        error: function (jqXhr, ajaxOptions, thrownError) {
                            if (jqXhr.status == 400) {
                                var errorsHtml = '';
                                var errors = jqXhr.responseJSON.message;
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value + '</li>';
                                });
                                toastr.error(errorsHtml, jqXhr.responseJSON.heading,
                                        options);
                            } else if (jqXhr.status == 401) {
                                toastr.error(jqXhr.responseJSON.message, '',
                                        options);
                            } else {
                                toastr.error('Error', 'Something went wrong',
                                        options);
                            }
                            App.unblockUI();
                        }
                    });
                }

            });

        });

        // Start Show Assigned Sub Event Modal
        $(document).on("click", "#assignedLesson", function (e) {
            e.preventDefault();
            var courseId = $("#courseId").val();
            var gsId = $("#gsId").val();
            $.ajax({
                url: "{{ URL::to('gsToLesson/getAssignedLesson') }}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    gs_id: gsId
                },
                success: function (res) {
                    $("#showAssignedLesson").html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {}
            }); //ajax
        });
        // End Show Assigned CM Modal
    });
</script>

@stop
