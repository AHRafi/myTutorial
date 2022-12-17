@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CI_COMDT_OBSN_MARKING_LIMIT')
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
                        @if(!empty($criteriaWiseWt->ci_obsn_wt) && !empty($criteriaWiseWt->comdt_obsn_wt))
                        <div class="row margin-top-10">

                            <div class="col-md-10 col-md-offset-1">
                                <span class="label label-md bold label-blue-steel">
                                    @lang('label.CI_OBSN_WT'):&nbsp;{!! $criteriaWiseWt->ci_obsn_wt ?? '' !!}
                                </span>&nbsp;
                                <span class="label label-md bold label-purple-wisteria">
                                    @lang('label.COMDT_OBSN_WT'):&nbsp;{!! $criteriaWiseWt->comdt_obsn_wt ?? '' !!}
                                </span>
                            </div>
                            <div class="col-md-10 col-md-offset-1 margin-top-10">
                                <div class="table-responsive webkit-scrollbar">
                                    <table class="table table-bordered table-hover" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                                <th class="vcenter">@lang('label.OBSN')</th>
                                                <th class="text-center vcenter">@lang('label.MKS')</th>
                                                <th class="text-center vcenter">@lang('label.LIMIT_PERCENT')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $ciReadonly = !empty($ciObsnData) ? 'readonly' : '';
                                            $comdtReadonly = !empty($comdtObsnData) ? 'readonly' : '';
                                            ?>
                                            <tr>
                                                <td class="text-center vcenter width-80">1</td>
                                                <td class="vcenter width-250">
                                                    <div class="width-inherit">@lang('label.CI_OBSN')</div>
                                                </td>
                                                <td class="vcenter width-80">
                                                    {!! Form::text('ci_mks_limit', $prevDataArr->ci_mks_limit ?? 100.00,['id' => 'ciMksLimit', 'class' => 'form-control integer-decimal-only width-full text-right','autocomplete' => 'off', $ciReadonly]) !!}
                                                </td>
                                                <td class="text-center vcenter width-80">
                                                    <div class="input-group bootstrap-touchspin width-full">
                                                        <span class="input-group-addon bootstrap-touchspin-prefix bold">&plusmn;</span>
                                                        {!! Form::text('ci_limit_percent', $prevDataArr->ci_limit_percent ?? null, ['id'=> 'ciLimitPercent', 'class' => 'form-control text-right integer-decimal-only text-input-width-100-per limit-percent', $ciReadonly]) !!}
                                                        <span class="input-group-addon bootstrap-touchspin-postfix bold">%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center vcenter width-80">2</td>
                                                <td class="vcenter width-250">
                                                    <div class="width-inherit">@lang('label.COMDT_OBSN')</div>
                                                </td>
                                                <td class="vcenter width-80">
                                                    {!! Form::text('comdt_mks_limit', $prevDataArr->comdt_mks_limit ?? 100.00,['id' => 'comdtMksLimit', 'class' => 'form-control integer-decimal-only width-full text-right','autocomplete' => 'off', $comdtReadonly]) !!}
                                                </td>
                                                <td class="text-center vcenter width-80">
                                                    <div class="input-group bootstrap-touchspin width-full">
                                                        <span class="input-group-addon bootstrap-touchspin-prefix bold">&plusmn;</span>
                                                        {!! Form::text('comdt_limit_percent', $prevDataArr->comdt_limit_percent ?? null, ['id'=> 'comdtLimitPercent', 'class' => 'form-control text-right integer-decimal-only text-input-width-100-per limit-percent', $comdtReadonly]) !!}
                                                        <span class="input-group-addon bootstrap-touchspin-postfix bold">%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class = "form-actions margin-top-10">
                            <div class = "col-md-offset-4 col-md-8">
                                <button class = "button-submit btn btn-circle green" type="button">
                                    <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                                </button>
                                <a href = "{{ URL::to('ciComdtObsnMarkingLimit') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                            </div>
                        </div>
                        @else
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.CI_OR_COMDT_OBSN_WT_IS_NOT_ASSIGNED_YET') !!}</strong></p>
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
                        url: "{{URL::to('ciComdtObsnMarkingLimit/saveMarkingLimit')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.CI_COMDT_OBSN_MARKING_LIMIT_HAS_BEEN_ASSIGNED")', res, options);
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