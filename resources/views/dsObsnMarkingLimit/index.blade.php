@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DS_OBSN_MARKING_LIMIT')
            </div>
            <div class="actions">
                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#" data-original-title="" title=""> </a>
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
                                    <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                                    {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--get module data-->
                    <div id="showMarkingLimit">
                        @if(!empty($criteriaWiseWt->ds_obsn_wt))
                        <div class="row margin-top-10">

                            <div class="col-md-10 col-md-offset-1">
                                <span class="label label-md bold label-blue-steel">
                                    @lang('label.TOTAL_DS_OBSN_WT'):&nbsp;{!! $criteriaWiseWt->ds_obsn_wt ?? '' !!}
                                </span>
                                {!! Form::hidden('total_ds_obsn_wt',!empty($criteriaWiseWt->ds_obsn_wt) ? $criteriaWiseWt->ds_obsn_wt : null,['id' => 'totalDsObsnWt']) !!}
                            </div>
                            <div class="col-md-10 col-md-offset-1 margin-top-10">
                                <div class="table-responsive webkit-scrollbar">
                                    <table class="table table-bordered table-hover" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                                <th class="vcenter">@lang('label.TERM')</th>
                                                <th class="text-center vcenter">@lang('label.MKS')</th>
                                                <th class="text-center vcenter">@lang('label.LIMIT_PERCENT')</th>
                                                <th class="text-center vcenter">@lang('label.WT')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!$termArr->isEmpty())
                                            <?php $sl = 0; ?>
                                            @foreach($termArr as $term)
                                            <?php
                                            $eventMksLimit = !empty($courseList->event_mks_limit) ? $courseList->event_mks_limit : null;

                                            $readonly = (!empty($dsObsnDataArr) && in_array($term->id, $dsObsnDataArr)) ? 'readonly' : '';
                                            $obsn = !empty($prevDataArr[$term->id]['obsn']) ? $prevDataArr[$term->id]['obsn'] : null;
                                            $mksLimit = !empty($prevDataArr[$term->id]['mks_limit']) ? $prevDataArr[$term->id]['mks_limit'] : $eventMksLimit;
                                            $limitPercent = !empty($prevDataArr[$term->id]['limit_percent']) ? $prevDataArr[$term->id]['limit_percent'] : null;
                                            ?>
                                            <tr>
                                                <td class="text-center vcenter width-80">{!! ++$sl !!}</td>
                                                <td class="vcenter width-250">
                                                    <div class="width-inherit">{!! $term->name ?? '' !!}</div>
                                                </td>
                                                <td class="vcenter width-80">
                                                    {!! Form::text('ds_obsn['.$term->id.'][mks_limit]', $mksLimit,['id' => 'mksLimit_'.$term->id, 'data-key' => $term->id, 'class' => 'mks-limit form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readonly]) !!}
                                                </td>
                                                <td class="text-center vcenter width-150">
                                                    <div class="input-group bootstrap-touchspin width-inherit">
                                                        <span class="input-group-addon bootstrap-touchspin-prefix bold">&plusmn;</span>
                                                        {!! Form::text('ds_obsn['.$term->id.'][limit_percent]', $limitPercent, ['id'=> 'limitPercent_' . $term->id, 'class' => 'form-control text-right integer-decimal-only text-input-width-100-per limit-percent limit-percent-' . $term->id, 'data-term-id' => $term->id, $readonly]) !!}
                                                        <span class="input-group-addon bootstrap-touchspin-postfix bold">%</span>
                                                    </div>
                                                </td>
                                                <td class="vcenter width-80">
                                                    {!! Form::text('ds_obsn['.$term->id.'][obsn]', $obsn,['id' => 'wt_'.$term->id, 'data-key' => $term->id, 'class' => 'wt-distributed form-control integer-decimal-only width-inherit text-right','autocomplete' => 'off', $readonly]) !!}
                                                </td>
                                            </tr>
                                            {!! Form::hidden('ds_obsn['.$term->id.'][term_name]', $term->name ?? '', ['id' => 'termName_' . $term->id]) !!}
                                            @endforeach
                                            {!! Form::hidden('total_wt', !empty($total) ? $total : null,['id' => 'totalWt']) !!}
                                            <tr>
                                                <td class="vcenter text-right bold" colspan="4">@lang('label.TOTAL')</td>
                                                <td class="vcenter text-right">
                                                    <span class="total-wt bold">{!! !empty($total) ? Helper::numberFormat2Digit($total) : '' !!}</span>
                                                </td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td colspan="4">@lang('label.NO_TERM_IS_ASSIGNED_TO_THIS_COURSE')</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @if(!$termArr->isEmpty())
                        <div class = "form-actions margin-top-10">
                            <div class = "col-md-offset-4 col-md-8">
                                <button class = "button-submit btn btn-circle green" type="button">
                                    <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                                </button>
                                <a href = "{{ URL::to('dsObsnMarkingLimit') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                            </div>
                        </div>
                        @endif
                        @else
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.DS_OBSN_WT_IS_NOT_ASSIGNED_YET') !!}</strong></p>
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


<script type="text/javascript">
    $(function () {
        $(document).on('keyup', '.limit-percent', function () {
            modRange(this);
        });

        function modRange(modClass) {
            var mod = $(modClass).val();
            if (mod > 100) {
                swal({
                    title: "@lang('label.PLEASE_PUT_A_VALUE_UP_TO_100') ",
                    text: "",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "@lang('label.OK')",
                    closeOnConfirm: true,
                    closeOnCancel: true,
                }, function (isConfirm) {
                    if (isConfirm) {
                        $(modClass).val('');
                    }
                });
                return false;
            }
        }

        $(document).on('keyup', '.wt-distributed', function () {
            total();
            var key = $(this).attr('data-key');
            var totalAssignedWt = parseFloat($("#totalDsObsnWt").val());
            var totalGivenWt = parseFloat($(".total-wt").text());
            if (totalGivenWt == '' || isNaN(totalGivenWt)) {
                totalGivenWt = 0;
            }
            if (totalGivenWt > totalAssignedWt) {
                swal({
                    title: '@lang("label.TOTAL_EVENT_WT_EXCEEDED_FROM_ASSIGNED_TOTAL_EVENT_WT")',

                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ok',
                    closeOnConfirm: true,
                }, function (isConfirm) {
                    $('#wt_' + key).val('');
                    setTimeout(function () {
                        $('#wt_' + key).focus();
                    }, 250);
                    total();
                    return false;
                });

            }
        });

        function total() {
            var sum = 0;
            $('.wt-distributed').each(function () {
                var wt = $(this).val();
                if (wt == '' || isNaN(wt)) {
                    wt = 0;
                }
                sum += parseFloat(wt);
            });
            $("#totalWt").val(sum);
            $('.total-wt').text(sum.toFixed(2));
        }

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
                        url: "{{URL::to('dsObsnMarkingLimit/saveMarkingLimit')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.DS_OBSN_MARKING_LIMIT_HAS_BEEN_ASSIGNED")', res, options);
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