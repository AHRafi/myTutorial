@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CELEBRATION_REPORT')
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'celebrationCmAnalytics/filter','class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearList->name}} </strong></div>
                            {!! Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                            {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="celEvent">@lang('label.CEL_EVENT')</label>
                        <div class="col-md-8">
                            {!! Form::select('cel_event', $celEventList,  Request::get('cel_event'), ['class' => 'form-control js-source-states', 'id' => 'celEvent']) !!}
                            <span class="text-danger">{{ $errors->first('cel_event') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="month">@lang('label.MONTH')</label>
                        <div class="col-md-8">
                            {!! Form::select('month', $monthList, Request::get('month'), ['class' => 'form-control js-source-states', 'id' => 'month']) !!}
                            <span class="text-danger">{{ $errors->first('month') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-2" for="dayFrom">@lang('label.DAY')</label>
                        <div class="col-md-10">
                            <div class="input-group bootstrap-touchspin  width-full">
                                <span class="input-group-addon bootstrap-touchspin-prefix bold">@lang('label.FROM')</span>
                                {!! Form::select('day_from', $dayList,  Request::get('day_from'), ['class' => 'form-control js-source-states width-inherit', 'id' => 'dayFrom']) !!}
                                <span class="input-group-addon bootstrap-touchspin-postfix bold">@lang('label.TO')</span>
                                {!! Form::select('day_to', $dayList,  Request::get('day_to'), ['class' => 'form-control js-source-states width-inherit', 'id' => 'dayTo']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-8">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> @lang('label.GENERATE')
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            @if(Request::get('generate') == 'true')
            @if (!empty($targetArr))
            <div class="row">
                <div class="col-md-12 text-right">
                    <a class="btn btn-md btn-primary vcenter" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                        <span class="tooltips" title="@lang('label.PRINT')"><i class="fa fa-print"></i> </span> 
                    </a>
                    <!--                                        <a class="btn btn-success vcenter" href="{!! URL::full().'&view=pdf' !!}">
                                                                <span class="tooltips" title="@lang('label.DOWNLOAD_PDF')"><i class="fa fa-file-pdf-o"></i></span>
                                                            </a>-->
                    <a class="btn btn-warning vcenter" href="{!! URL::full().'&view=excel' !!}">
                        <span class="tooltips" title="@lang('label.DOWNLOAD_EXCEL')"><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.CEL_EVENT')}} : <strong>{{ !empty($celEventList[Request::get('cel_event')]) && Request::get('cel_event') != 0 ? $celEventList[Request::get('cel_event')] : __('label.N_A') }} |</strong>
                            {{__('label.MONTH')}} : <strong>{{ !empty($monthList[Request::get('month')]) && Request::get('month') != '00' ? $monthList[Request::get('month')] : __('label.N_A') }} |</strong>
                            {{__('label.DAY')}} : <strong>{{ __('label.FROM') . ' ' . Request::get('day_from') . ' ' . __('label.TO') . ' ' . Request::get('day_to') }} </strong>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="webkit-scrollbar table-responsive {{(!empty(Request::get('month')) && Request::get('month') != '00') ? '' : 'max-height-500'}}">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SERIAL')</th>
                                    <th class="vcenter width-100">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter">@lang('label.RANK')</th>
                                    <th class="vcenter">@lang('label.FULL_NAME')</th>
                                    <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                    @if(Request::get('cel_event') == '1')
                                    <th class="vcenter text-center">@lang('label.SELF_BIRTH_DATE')</th>
                                    @elseif(Request::get('cel_event') == '2')
                                    <th class="vcenter text-center">@lang('label.SELF_BIRTH_DATE')</th>
                                    <th class="vcenter text-center">@lang('label.SPOUSE_BIRTH_DATE')</th>
                                    @elseif(Request::get('cel_event') == '3')
                                    <th class="vcenter text-center">@lang('label.MARRIAGE_DATE')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty(Request::get('month')) && Request::get('month') != '00')
                                <tr class="active">
                                    <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.THIS_MONTH')</td>
                                </tr>
                                @if (!empty($targetArr['this']))
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($targetArr['this'] as $date => $target)

                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    @if(Request::get('cel_event') == '1')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '2')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '3')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                    @endif
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NO_CM_FOUND')</td>
                                </tr>
                                @endif
                                <tr class="active">
                                    <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NEXT_MONTH')</td>
                                </tr>
                                @if (!empty($targetArr['coming']))
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($targetArr['coming'] as $date => $target)

                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    @if(Request::get('cel_event') == '1')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '2')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '3')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                    @endif
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NO_CM_FOUND')</td>
                                </tr>
                                @endif
                                <tr class="active">
                                    <td class="vcenter text-center bold" colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.PREVIOUS_MONTH')</td>
                                </tr>
                                @if (!empty($targetArr['prev']))
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($targetArr['prev'] as $date => $target)

                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    @if(Request::get('cel_event') == '1')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '2')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '3')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                    @endif
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NO_CM_FOUND')</td>
                                </tr>
                                @endif
                                @else
                                @if (!empty($targetArr['all']))
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($targetArr['all'] as $date => $target)

                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['personal_no']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['rank']) !!}</td>
                                    <td class="vcenter">{!! Common::getFurnishedCmName($target['full_name']) !!}</td>

                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/cm/' . $target['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    @if(Request::get('cel_event') == '1')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '2')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                    @elseif(Request::get('cel_event') == '3')
                                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                    @endif
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="{!! in_array(Request::get('cel_event'), ['1', '3']) ? 6 : (Request::get('cel_event') == '2' ? 7 : 5) !!}">@lang('label.NO_CM_FOUND')</td>
                                </tr>
                                @endif
                                @endif
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            @endif
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {

        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null,
        };

        $(document).on("change", "#month", function () {
            var month = $("#month").val();

            $("#dayFrom").html("<option value='0'>@lang('label.SELECT_DAY_OPT')</option>");
            $("#dayTo").html("<option value='0'>@lang('label.SELECT_DAY_OPT')</option>");

            if (month == '00') {
                return false;
            }

            $.ajax({
                url: "{{ URL::to('celebrationCmAnalytics/getmonthDayList')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    month: month,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $("#dayFrom").html(res.html);
                    $("#dayTo").html(res.html2);
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }
                    App.unblockUI();
                }
            });//ajax
        });


    });
</script>
@endsection
