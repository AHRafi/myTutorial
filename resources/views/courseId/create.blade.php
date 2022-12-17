@extends('layouts.default.master')

@section('data_count')
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-calendar"></i>@lang('label.CREATE_COURSE_ID')
            </div>
        </div>
        @if(empty($activeCourse))
        <div class="portlet-body form">
            {!! Form::open(array('group' => 'form', 'url' => 'courseId', 'class' => 'form-horizontal')) !!}
            {!! Form::hidden('page', Helper::queryPageStr($qpArr)) !!}
            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="trainingYear">@lang('label.TRAINING_YEAR') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8 margin-top-8"> 
                                <strong> {{$trainingYearList->name}} </strong>
                                {!! Form::hidden('training_year_id', $trainingYearList->id,['id'=>'trainingYearId']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="name">@lang('label.NAME') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('name', null, ['id'=> 'name', 'class' =>
                                'form-control','autocomplete'=>'off']) !!}
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="initialDate">@lang('label.INITIAL_DATE') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('initial_date', null, ['id'=> 'initialDate', 'class' => 'form-control
                                    course-date', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!}
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="initialDate" id="resetInitialDate">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-danger">{{ $errors->first('initial_date') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="terminationDate">@lang('label.TERMINATION_DATE')
                                :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('termination_date', null, ['id'=> 'terminationDate', 'class' =>
                                    'form-control course-date', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!}
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="terminationDate" id="resetTerminationDate">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-danger">{{ $errors->first('termination_date') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="noOfWeeks">@lang('label.NO_OF_WEEKS') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('no_of_weeks', null, ['id'=> 'noOfWeeks', 'class' => 'form-control
                                number_of_week integer-only', 'readonly']) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('no_of_weeks') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="shortInfo">@lang('label.SHORT_INFO') :</label>
                            <div class="col-md-8">
                                {!! Form::text('short_info', null, ['id'=> 'shortInfo', 'class' =>
                                'form-control','autocomplete'=>'off']) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('short_info') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="totalCourseWt">@lang('label.TOTAL_COURSE_WT') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('total_course_wt', Helper::numberFormatDigit2(1000), ['id'=> 'totalCourseWt', 'class' => 'form-control
                                integer-decimal-only text-right']) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('total_course_wt') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="eventMksLimit">@lang('label.EVENT_MKS_LIMIT') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('event_mks_limit', Helper::numberFormatDigit2(100), ['id'=> 'eventMksLimit', 'class' => 'form-control
                                integer-decimal-only text-right']) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('event_mks_limit') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="highestMksLimit">@lang('label.HIGHEST_MKS_LIMIT') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('highest_mks_limit',  Helper::numberFormatDigit2(70), ['id'=> 'highestMksLimit', 'class' => 'form-control
                                integer-decimal-only text-right']) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('highest_mks_limit') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="lowestMksLimit">@lang('label.LOWEST_MKS_LIMIT') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('lowest_mks_limit', Helper::numberFormatDigit2(50), ['id'=> 'lowestMksLimit', 'class' => 'form-control
                                integer-decimal-only text-right']) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('lowest_mks_limit') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="status">@lang('label.STATUS') :</label>
                            <div class="col-md-8">
                                {!! Form::select('status', ['1' => __('label.ACTIVE'), '0' => __('label.INACTIVE')],
                                null, ['class' => 'form-control', 'id' => 'status']) !!}
                                <span class="text-danger">{{ $errors->first('status') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn btn-circle green" type="submit">
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href="{{ URL::to('/courseId'.Helper::queryPageStr($qpArr)) }}"
                           class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        @else
        <?php
        $activeCourse = !empty($activeCourse) ? $activeCourse->name : '';
        ?>
        <div class="portlet-body">
            <div class="alert alert-danger alert-dismissable">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.COURSE_IS_ALREADY_ACTIVE', ['course' => $activeCourse]) !!}</strong></p>
            </div>
        </div>
        @endif


    </div>
</div>
<script type="text/javascript">
    $(function () {
        //function for no of weeks
        $(document).on('change', '.course-date', function () {
            var initialDate = new Date($('#initialDate').val());
            var terminationDate = new Date($('#terminationDate').val());
            if (terminationDate < initialDate) {
                swal('Termination Date must be greater than InitialDate ');
                $('#terminationDate').val('');
                $('noOfWeeks').val('');
                return false;
            }
            var weeks = Math.ceil((terminationDate - initialDate) / (24 * 3600 * 1000 * 7));
            if (isNaN(weeks)) {
                var weeks = '';
            }
            $("#noOfWeeks").val(weeks);
        });

        $(document).on('click', '#resetInitialDate', function () {
            $("#noOfWeeks").val('');
        });
        $(document).on('click', '#resetTerminationDate', function () {
            $("#noOfWeeks").val('');
        });

        //start: highest limit can't exceed event mks limit
        $(document).on('keyup', '#eventMksLimit', function () {

            var eventMksLimit = parseFloat($("#eventMksLimit").val());
            var highestMksLimit = parseFloat($("#highestMksLimit").val());

            if (eventMksLimit < highestMksLimit) {
                swal({
                    title: "@lang('label.HIGHEST_LIMIT_CAN_NOT_EXCEED_EVENT_MKS_LIMIT') ",
                    text: "",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "@lang('label.OK')",
                    closeOnConfirm: true,
                    closeOnCancel: true,
                }, function (isConfirm) {
                    if (isConfirm) {
                        $('#eventMksLimit').val('');
                        $('#eventMksLimit').focus();
                        $('#highestMksLimit').val('');
                        $('#lowestMksLimit').val('');
                    }
                });
                return false;
            }
        });
        $(document).on('keyup', '#highestMksLimit', function () {

            var eventMksLimit = parseFloat($("#eventMksLimit").val());
            var highestMksLimit = parseFloat($("#highestMksLimit").val());
            var lowestMksLimit = parseFloat($("#lowestMksLimit").val());

            if (eventMksLimit < highestMksLimit) {
                swal({
                    title: "@lang('label.HIGHEST_LIMIT_CAN_NOT_EXCEED_EVENT_MKS_LIMIT') ",
                    text: "",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "@lang('label.OK')",
                    closeOnConfirm: true,
                    closeOnCancel: true,
                }, function (isConfirm) {
                    if (isConfirm) {
                        $('#highestMksLimit').val('');
                        $('#highestMksLimit').focus();
                    }
                });
                return false;
            } else if (lowestMksLimit > highestMksLimit) {
                swal({
                    title: "@lang('label.LOWEST_LIMIT_CAN_NOT_EXCEED_HIGHEST_LIMIT') ",
                    text: "",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "@lang('label.OK')",
                    closeOnConfirm: true,
                    closeOnCancel: true,
                }, function (isConfirm) {
                    if (isConfirm) {
                        $('#highestMksLimit').val('');
                        $('#highestMksLimit').focus();
                        $('#lowestMksLimit').val('');
                    }
                });
                return false;
            }
        });
        //end: highest limit can't exceed event mks limit

        //start: lowest limit can't exceed highest limit
        $(document).on('keyup', '#lowestMksLimit', function () {

            var highestMksLimit = parseFloat($("#highestMksLimit").val());
            var lowestMksLimit = parseFloat($("#lowestMksLimit").val());

            if (highestMksLimit < lowestMksLimit) {
                swal({
                    title: "@lang('label.LOWEST_LIMIT_CAN_NOT_EXCEED_HIGHEST_LIMIT') ",
                    text: "",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "@lang('label.OK')",
                    closeOnConfirm: true,
                    closeOnCancel: true,
                }, function (isConfirm) {
                    if (isConfirm) {
                        $('#lowestMksLimit').val('');
                        $('#lowestMksLimit').focus();
                    }
                });
                return false;
            }
        });
        //end: lowest limit can't exceed highest limit

    });
</script>

@stop