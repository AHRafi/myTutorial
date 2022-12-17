@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CI_COMDT_MODERATION_MARKING_LIMIT')
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
                                    <div class="control-label pull-left"> <strong> {{$activeCourse->name}} </strong></div>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--get module data-->
                    <div id="showMarkingLimit">
                        <div class="row margin-top-10">
                            <div class="col-md-10 table-responsive webkit-scrollbar col-md-offset-1">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                            <th class="vcenter">@lang('label.TERM')</th>
                        <!--                    <th colspan="2" class="text-center vcenter">@lang('label.MODERATION_MARKING_LIMIT')</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center vcenter">@lang('label.CI_MODERAION')</th>-->
                                            <th class="text-center vcenter">@lang('label.MODERATION')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!$termArr->isEmpty())
                                        <?php $sl = 0; ?>
                                        @foreach($termArr as $term)
                                        <?php
                                        $readonly = (!empty($ciModDataArr) && in_array($term->id, $ciModDataArr)) ? 'readonly' : '';
                                        $ciMod = !empty($prevDataArr[$term->id]['ci_mod']) ? $prevDataArr[$term->id]['ci_mod'] : null;
                                        $comdtMod = !empty($prevDataArr[$term->id]['comdt_mod']) ? $prevDataArr[$term->id]['comdt_mod'] : null;
                                        ?>
                                        <tr>
                                            <td class="text-center vcenter width-80">{!! ++$sl !!}</td>
                                            <td class="vcenter width-250">
                                                <div class="width-inherit">{!! $term->name ?? '' !!}</div>
                                            </td>
                                            <td class="text-center vcenter width-150">
                                                <div class="input-group bootstrap-touchspin width-inherit">
                                                    <span class="input-group-addon bootstrap-touchspin-prefix bold">&plusmn;</span>
                                                    {!! Form::text('mod['.$term->id.'][ci_mod]', $ciMod, ['id'=> 'ciMod_' . $term->id, 'class' => 'form-control text-right integer-decimal-only text-input-width-100-per ci-mod ci-mod-' . $term->id, 'data-term-id' => $term->id, $readonly]) !!}
                                                    <span class="input-group-addon bootstrap-touchspin-postfix bold">%</span>
                                                </div>
                                            </td>
                        <!--                    <td class="text-center vcenter width-150">
                                                <div class="input-group bootstrap-touchspin width-inherit">
                                                    <span class="input-group-addon bootstrap-touchspin-prefix bold">&plusmn;</span>
                                                    {!! Form::text('mod['.$term->id.'][comdt_mod]', $comdtMod, ['id'=> 'comdtMod_' . $term->id, 'class' => 'form-control text-right integer-decimal-only text-input-width-100-per comdt-mod comdt-mod-' . $term->id, 'data-term-id' => $term->id]) !!}
                                                    <span class="input-group-addon bootstrap-touchspin-postfix bold">%</span>
                                                </div>
                                            </td>-->
                                        </tr>
                                        {!! Form::hidden('mod['.$term->id.'][term_name]', $term->name ?? '', ['id' => 'termName_' . $term->id]) !!}
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="4">@lang('label.NO_TERM_IS_ASSIGNED_TO_THIS_COURSE')</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(!$termArr->isEmpty())
                        <div class = "form-actions margin-top-10">
                            <div class = "col-md-offset-4 col-md-8">
                                <button class = "button-submit btn btn-circle green" type="button">
                                    <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                                </button>
                                <a href = "{{ URL::to('ciComdtModerationMarkingLimit') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
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


<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {

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
                        url: "{{URL::to('ciComdtModerationMarkingLimit/saveMarkingLimit')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.CI_COMDT_MODERATION_MARKING_LIMIT_HAS_BEEN_ASSIGNED")', res, options);
//                            $(document).trigger("change", "#courseId");
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

    $(function () {

        //start: for CI mod
        $(document).on('keyup', '.ci-mod', function () {
            modRange(this);
        });
        //start: for CI mod

        //start: for Comdt mod
        $(document).on('keyup', '.comdt-mod', function () {
            modRange(this);
        });
        //start: for Comdt mod
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

</script>

@stop