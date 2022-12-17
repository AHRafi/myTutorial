@extends('layouts.default.master')
@section('data_count')
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i>@lang('label.LESSON_WISE_GS_FEEDBACK_FROM_DS')
                </div>
            </div>
            <div class="portlet-body">
                {!! Form::open([
                    'group' => 'form',
                    'url' => 'lessonWiseGsFeedbackFromDs/filter',
                    'class' => 'form-horizontal',
                    'id' => 'submitForm',
                ]) !!}

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                {!! Form::select('training_year_id', $activeTrainingYearList, Request::get('training_year_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'trainingYearId',
                                ]) !!}
                                <span class="text-danger">{{ $errors->first('training_year_id') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                {!! Form::select('course_id', $courseList, Request::get('course_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'courseId',
                                ]) !!}
                                <span class="text-danger">{{ $errors->first('course_id') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="gsId">@lang('label.GS') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('gs_id', $gsList, Request::get('gs_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'gsId',
                                ]) !!}
                                <span class="text-danger">{{ $errors->first('gs_id') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="lessonId">@lang('label.LESSON') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('lesson_id', $lessonList, Request::get('lesson_id'), [
                                    'class' => 'form-control js-source-states',
                                    'id' => 'lessonId',
                                ]) !!}
                                <span class="text-danger">{{ $errors->first('lesson_id') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="form-group">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn"
                                id="generateReport" value="Show Filter Info" data-id="1">
                                <i class="fa fa-search"></i> @lang('label.GENERATE')
                            </button>
                        </div>
                    </div>
                </div>

                @if (Request::get('generate') == 'true')
                    @if (!empty($dsDataList))
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-md btn-primary vcenter" target="_blank" href="{!! URL::full() . '&view=print' !!}">
                                    <span class="tooltips" title="@lang('label.PRINT')"><i class="fa fa-print"></i> </span>
                                </a>
                                <!--                    <a class="btn btn-success vcenter" href="{!! URL::full() . '&view=pdf' !!}">
                                                                <span class="tooltips" title="@lang('label.DOWNLOAD_PDF')"><i class="fa fa-file-pdf-o"></i></span>
                                                            </a>-->
                                <a class="btn btn-warning vcenter" href="{!! URL::full() . '&view=excel' !!}">
                                    <span class="tooltips" title="@lang('label.DOWNLOAD_EXCEL')"><i class="fa fa-file-excel-o"></i>
                                    </span>
                                </a>

                                <label class="control-label" for="sortBy">@lang('label.SORT_BY') :</label>&nbsp;

                                <label class="control-label" for="sortBy">
                                    {!! Form::select('sort', $sortByList, Request::get('sort'), ['class' => 'form-control', 'id' => 'sortBy']) !!}
                                </label>

                                <button class="btn green-jungle filter-btn" id="sortByHref" type="submit">
                                    <i class="fa fa-arrow-right"></i> @lang('label.GO')
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-blue-hoki bg-font-blue-hoki">
                                <h5 style="padding: 10px;">
                                    {{ __('label.TRAINING_YEAR') }} :
                                    <strong>{{ !empty($activeTrainingYearList[Request::get('training_year_id')]) && Request::get('training_year_id') != 0 ? $activeTrainingYearList[Request::get('training_year_id')] : __('label.N_A') }}
                                        |</strong>
                                    {{ __('label.COURSE') }} :
                                    <strong>{{ !empty($courseList[Request::get('course_id')]) && Request::get('course_id') != 0 ? $courseList[Request::get('course_id')] : __('label.N_A') }}
                                        |</strong>
                                    {{ __('label.GS') }} :
                                    <strong>{{ !empty($gsList[Request::get('gs_id')]) && Request::get('gs_id') != 0 ? $gsList[Request::get('gs_id')] : __('label.N_A') }}
                                        |</strong>
                                    {{ __('label.LESSON') }} :
                                    <strong>{{ !empty($lessonList[Request::get('lesson_id')]) && Request::get('lesson_id') != 0 ? $lessonList[Request::get('lesson_id')] : __('label.N_A') }}
                                    </strong>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <div class="webkit-scrollbar max-height-500">
                                <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                            <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                            <th class="vcenter">@lang('label.RANK')</th>
                                            <th class="vcenter">@lang('label.FULL_NAME')</th>
                                            <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                                            <th class="vcenter">@lang('label.WING')</th>
                                            <th class="vcenter">@lang('label.PHOTO')</th>

                                            <th class="vcenter text-center">@lang('label.GRADING')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($dsDataList))
                                            <?php
                                            $sl = 0;
                                            ?>

                                            @foreach ($dsDataList as $id => $target)
                                                <tr>
                                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                                    <td class="vcenter">{!! !empty($target['personal_no']) ? $target['personal_no'] : '' !!}</td>
                                                    <td class="vcenter">{!! $target['rank'] ?? '' !!}</td>
                                                    <td class="vcenter">{!! $target['ds_name'] ?? '' !!}</td>
                                                    <td class="vcenter">{!! $target['official_name'] ?? '' !!}</td>
                                                    <td class="vcenter">{!! $target['wing'] ?? '' !!}</td>
                                                    <td class="vcenter text-center" width="50px">
                                                        @if (!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                                            <img width="50" height="60"
                                                                src="{{ URL::to('/') }}/public/uploads/cm/{{ $target['photo'] }}"
                                                                alt="{{ $target['official_name'] ?? '' }}" />
                                                        @else
                                                            <img width="50" height="60"
                                                                src="{{ URL::to('/') }}/public/img/unknown.png"
                                                                alt="{{ $target['official_name'] ?? '' }}" />
                                                        @endif
                                                    </td>
                                                    <td class="vcenter text-center">{!! !empty($target['grading']) && $target['grading'] != 0 ? $target['grading'] : '' !!}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11">@lang('label.NO_DS_IS_ASSIGNED_TO_THIS_LESSON')</td>
                                            </tr>
                                        @endif
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                {!! Form::close() !!}
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
            //table header fix
            $(".table-head-fixer-color").tableHeadFixer({
                left: 5
            });

            $(document).on("change", "#trainingYearId", function() {
                var trainingYearId = $("#trainingYearId").val();
                if (trainingYearId == '0') {
                    $("#courseId").html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
                    $("#gsId").html("<option value='0'>@lang('label.SELECT_GS_OPT')</option>");
                    return false;
                }
                $.ajax({
                    url: "{{ URL::to('lessonWiseGsFeedbackFromDs/getCourse') }}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        training_year_id: trainingYearId
                    },
                    beforeSend: function() {
                        $("#gsId").html("<option value='0'>@lang('label.SELECT_GS_OPT')</option>");
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#courseId').html(res.html);
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {}
                }); //ajax

            });
            //End::Get Course

            //Start::Get GS
            $(document).on("change", "#courseId", function() {


                var courseId = $("#courseId").val();
                if (courseId == '0') {
                    $("#gsId").html("<option value='0'>@lang('label.SELECT_GS_OPT')</option>");
                    return false;
                }

                $.ajax({
                    url: "{{ URL::to('lessonWiseGsFeedbackFromDs/getGs') }}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        course_id: courseId
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#gsId').html(res.html);
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {}
                }); //ajax

            });
            //End::Get Gs

            //Start::Get lesson
            $(document).on("change", "#gsId", function() {

                var gsId = $("#gsId").val();
                if (gsId == '0') {
                    $("#lessonId").html("<option value='0'>@lang('label.SELECT_LESSON_OPT')</option>");
                    return false;
                }

                $.ajax({
                    url: "{{ URL::to('lessonWiseGsFeedbackFromDs/getLesson') }}",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        gs_id: gsId
                    },
                    beforeSend: function() {
                        App.blockUI({
                            boxed: true
                        });
                    },
                    success: function(res) {
                        $('#lessonId').html(res.html);
                        $(".js-source-states").select2();
                        App.unblockUI();
                    },
                    error: function(jqXhr, ajaxOptions, thrownError) {}
                }); //ajax

            });
            //End::Get lesson
        });
    </script>


@stop
