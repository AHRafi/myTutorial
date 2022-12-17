@extends('layouts.default.master')
@section('data_count')
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-power-off"></i>@lang('label.ACTIVATE_GS_FEEDBACK_FOR_CM')
                </div>
            </div>

            <div class="portlet-body">
                {!! Form::open([
                    'group' => 'form',
                    'url' => '#',
                    'class' => 'form-horizontal',
                    'id' => 'assessmentActDeactForm',
                ]) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                    <div class="col-md-7">
                                        <div class="control-label pull-left"> <strong> {{ $activeTrainingYearInfo->name }}
                                            </strong></div>
                                        {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                    <div class="col-md-7">
                                        <div class="control-label pull-left"> <strong> {{ $courseList->name }} </strong>
                                        </div>
                                        {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Event assessment summary -->
                        <div class="row margin-top-10">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="vcenter text-center">@lang('label.SL_NO')</th>
                                            <th class="vcenter">@lang('label.LESSON')</th>
                                            <th class="vcenter">@lang('label.SUBJECT')</th>
                                            <th class="vcenter">@lang('label.GS')</th>
                                            <th class="vcenter text-center">@lang('label.ACTIVATION_STATUS')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($targetArr))

                                            <?php $sl = 0; ?>
                                            @foreach ($targetArr as $target)
                                                <tr>
                                                    <td class="text-center">{!! ++$sl !!}</td>
                                                    <td class="vcenter text-left">{!! $target->lesson ?? '' !!}</td>
                                                    <td class="vcenter text-left">{!! $target->subject ?? '' !!}</td>
                                                    <td class="vcenter text-left">{!! $target->gs_name ?? '' !!}</td>
                                                    <td class="text-center">
                                                        <div class="width-160">
                                                            {!! Form::checkbox(
                                                                'act_deact_stat[' . $target->subject_id . '][' . $target->lesson_id . '][' . $target->gs_id . ']',
                                                                0,
                                                                !empty($statArr[$target->subject_id][$target->lesson_id][$target->gs_id]) && $statArr[$target->subject_id][$target->lesson_id][$target->gs_id] == '1' ? 1 : 0,
                                                                [
                                                                    'id' => 'actDeactStat_' . $target->subject_id . '_' . $target->lesson_id . '_' . $target->gs_id,
                                                                    'class' => 'make-switch act-deact-switch',
                                                                    'data-on-text' => __('label.ACTIVATE'),
                                                                    'data-off-text' => __('label.DEACTIVATE'),
                                                                    'criteria' => '1',
                                                                    'course-id' => $courseList->id,
                                                                    'lesson-id' => $target->lesson_id,
                                                                    'subject-id' => $target->subject_id,
                                                                    'gs-id' => $target->gs_id,
                                                                ],
                                                            ) !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="10">@lang('label.NO_LESSON_FOUND')</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(function() {
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $(".act-deact-switch").bootstrapSwitch({
                offColor: 'danger'
            });

            $(".table-head-fixer-color").tableHeadFixer();

            $('#dataTable').dataTable({
                "paging": true,
                "pageLength": 100,
                "info": false,
                "order": false
            });


            //deligate reports
            $(document).on('switchChange.bootstrapSwitch', '.act-deact-switch', function() {

                var courseId = $(this).attr('course-id');
                var status = this.checked == true ? '1' : '0';
                var lessonId = $(this).attr('lesson-id');
                var subjectId = $(this).attr('subject-id');
                var gsId = $(this).attr('gs-id');

                $.ajax({
                    url: "{{ URL::to('activateGsFeedbackForCm/setStat') }}",
                    type: "POST",
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId,
                        status: status,
                        lesson_id: lessonId,
                        subject_id: subjectId,
                        gs_id: gsId,
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        toastr.success(res.message, res.heading, options);
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 400) {
                            var errorsHtml = '';
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function(key, value) {
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

            });

        });
    </script>
    <script src="{{ asset('public/js/custom.js') }}" type="text/javascript"></script>
@stop
