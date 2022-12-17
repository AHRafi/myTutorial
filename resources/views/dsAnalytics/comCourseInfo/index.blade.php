@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.COMMISSIONING_COURSE_WISE_DS_ANALYTICS')
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'comCourseWiseDsAnalytics/filter','class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="name">@lang('label.NAME')</label>
                        <div class="col-md-8">
                            {!! Form::text('name',  Request::get('name'), ['class' => 'form-control tooltips', 'id' => 'name', 'title' => 'Full Name/Official Name', 'placeholder' => 'Full Name/Official Name', 'list' => 'cmName', 'autocomplete' => 'off']) !!} 
                            <datalist id="cmName">
                                @if (!$nameArr->isEmpty())
                                @foreach($nameArr as $item)
                                <option value="{!! $item->full_name !!}" />
                                @endforeach
                                @endif
                            </datalist>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="wingId">@lang('label.WING') </label>
                        <div class="col-md-8">
                            {!! Form::select('wing_id', $wingList,  Request::get('wing_id'), ['class' => 'form-control js-source-states', 'id' => 'wingId']) !!}
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="wingId">@lang('label.APPT_AFWC') </label>
                        <div class="col-md-8">
                            {!! Form::select('appt_id', $appointmentList,  Request::get('appt_id'), ['class' => 'form-control js-source-states', 'id' => 'apptId']) !!}
                            <span class="text-danger">{{ $errors->first('appt_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="rank">@lang('label.RANK') </label>
                        <div class="col-md-8">
                            {!! Form::select('rank_id', $rankList,  Request::get('rank_id'), ['class' => 'form-control js-source-states', 'id' => 'rank']) !!}
                            <span class="text-danger">{{ $errors->first('rank_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="armsService">@lang('label.ARMS_SERVICE') </label>
                        <div class="col-md-8">
                            {!! Form::select('arms_service_id', $armsServiceList,  Request::get('arms_service_id'), ['class' => 'form-control js-source-states', 'id' => 'armsService']) !!}
                            <span class="text-danger">{{ $errors->first('arms_service_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="comCourse">@lang('label.COMMISSIONING_COURSE') </label>
                        <div class="col-md-8">
                            {!! Form::select('com_course_id', $comCourseList,  Request::get('com_course_id'), ['class' => 'form-control js-source-states', 'id' => 'comCourse']) !!}
                            <span class="text-danger">{{ $errors->first('com_course_id') }}</span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.COMMISSIONING_DATE_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('commissioning_date_from',Request::get('commissioning_date_from') ?? null, ['class' => 'form-control', 'id' => 'docmFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="docmFrom">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.COMMISSIONING_DATE_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2 ">
                                {!! Form::text('commissioning_date_to',Request::get('commissioning_date_to') ?? null, ['class' => 'form-control', 'id' => 'docmTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="docmTo">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commissionType">@lang('label.COMMISSIONING_TYPE') </label>
                        <div class="col-md-8">
                            {!! Form::select('com_type_id', $commissionTypeList,  Request::get('com_type_id'), ['class' => 'form-control js-source-states', 'id' => 'commissionType']) !!}
                            <span class="text-danger">{{ $errors->first('com_type_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.JOINING_DATE_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('joining_date_from',Request::get('joining_date_from') ?? null, ['class' => 'form-control', 'id' => 'docmFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="docmFrom">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.JOINING_DATE_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2 ">
                                {!! Form::text('joining_date_to',Request::get('joining_date_to') ?? null, ['class' => 'form-control', 'id' => 'docmTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="docmTo">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12 text-center">
                    <div class="form-group">
                        <!--<label class="control-label col-md-4" for="">&nbsp;</label>-->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> @lang('label.GENERATE')
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!--filter form close-->

            @if($request->generate == 'true')
            @if (!empty($targetArr))
            <div class="row">
                <div class="col-md-12 text-right">
<!--                    <label class="control-label" for="printOption">
                        {!! Form::select('print_option', $printOptionList, Request::get('print_option'),['class' => 'form-control','id'=>'printOption']) !!}
                    </label>-->
                    <label class="control-label" for="columns">
                        {!! Form::select('columns[]', $columnArr, !empty(Request::get('columns')) ? explode(',',Request::get('columns')) : [], [ 'id' => 'columns', 'class' => 'form-control mt-multiselect btn btn-default',  'multiple' , 'data-select-all'=>"true"]) !!}
                    </label>
                    <a class="btn btn-md print btn-primary vcenter" target="_blank"  href="{!! URL::full().'&view=print&columns=' !!}">
                        <span class="tooltips" title="@lang('label.PRINT')"><i class="fa fa-print"></i> </span> 
                    </a>



                    <!--                                        <a class="btn btn-success vcenter" href="{!! URL::full().'&view=pdf' !!}">
                                                                <span class="tooltips" title="@lang('label.DOWNLOAD_PDF')"><i class="fa fa-file-pdf-o"></i></span>
                                                            </a>-->
                    <a class="btn btn-warning excel vcenter" href="{!! URL::full().'&view=excel&columns=' !!}">
                        <span class="tooltips" title="@lang('label.DOWNLOAD_EXCEL')"><i class="fa fa-file-excel-o"></i> </span>
                    </a>
                    <label class="control-label" for="sortBy">@lang('label.SORT_BY') :</label>&nbsp;

                    <label class="control-label" for="sortBy">
                        {!! Form::select('sort', $sortByList, Request::get('sort'),['class' => 'form-control','id'=>'sortBy']) !!}
                    </label>

                    <button class="btn green-jungle filter-btn"  id="sortByHref" type="submit">
                        <i class="fa fa-arrow-right"></i>  @lang('label.GO')
                    </button>
                </div>
            </div>
            @endif
            {!! Form::close() !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-blue-hoki bg-font-blue-hoki">
                        <h5 style="padding: 10px;">
                            {{__('label.NAME')}} : <strong>{{ !empty(Request::get('name')) && Request::get('name') != '' ? Request::get('name') : __('label.N_A') }} |</strong>
                            {{__('label.SERVICE')}} : <strong>{{ !empty($wingList[Request::get('wing_id')]) && Request::get('wing_id') != 0 ? $wingList[Request::get('wing_id')] : __('label.ALL') }} |</strong>
                            {{__('label.RANK')}} : <strong>{{ !empty($rankList[Request::get('rank')]) && Request::get('rank_id') != 0 ? $rankList[Request::get('rank')] : __('label.ALL') }} |</strong>
                            {{__('label.ARMS_SERVICE')}} : <strong>{{ !empty($armsServiceList[Request::get('arms_service')]) && Request::get('arms_service') != 0 ? $armsServiceList[Request::get('arms_service')] : __('label.ALL') }} |</strong>
                            {{__('label.APPT_AFWC')}} : <strong>{{ !empty($appointmentList[Request::get('appt_id')]) && Request::get('appt_id') != 0 ? $appointmentList[Request::get('appt_id')] : __('label.ALL') }} |</strong>
                            {{__('label.JOINING_DATE_FROM')}} : <strong>{{ !empty(Request::get('joining_date_from')) && Request::get('joining_date_from') != '' ? Request::get('joining_date_from') : __('label.N_A') }} |</strong>
                            {{__('label.JOINING_DATE_TO')}} : <strong>{{ !empty(Request::get('joining_date_to')) && Request::get('joining_date_to') != '' ? Request::get('joining_date_to') : __('label.N_A') }} |</strong>
                            {{__('label.COMMISSIONING_COURSE')}} : <strong>{{ !empty($comCourseList[Request::get('com_course_id')]) && Request::get('com_course_id') != 0 ? $comCourseList[Request::get('com_course_id')] : __('label.ALL') }} |</strong>
                            {{__('label.COMMISSIONING_DATE_FROM')}} : <strong>{{ !empty(Request::get('commissioning_date_from')) && Request::get('commissioning_date_from') != '' ? Request::get('commissioning_date_from') : __('label.N_A') }} |</strong>
                            {{__('label.COMMISSIONING_DATE_TO')}} : <strong>{{ !empty(Request::get('commissioning_date_to')) && Request::get('commissioning_date_to') != '' ? Request::get('commissioning_date_to') : __('label.N_A') }} |</strong>
                            {{__('label.COMMISSIONING_TYPE')}} : <strong>{{ !empty($commissionTypeList[Request::get('com_type_id')]) && Request::get('com_type_id') != 0 ? $commissionTypeList[Request::get('com_type_id')] : __('label.ALL') }} |</strong>

                            {{__('label.TOTAL_NO_OF_DS')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

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
                                    <th class="vcenter text-center">@lang('label.SERIAL')</th>
                                     <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter">@lang('label.RANK')</th>
                                    <th class="vcenter">@lang('label.FULL_NAME')</th>
                                    <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                    <th class="vcenter">@lang('label.ARMS_SERVICE')</th>
                                    <th class="vcenter">@lang('label.APPT_AFWC')</th>
                                    <th class="vcenter text-center">@lang('label.JOINING_DATE')</th>
                                    <th class="vcenter">@lang('label.COMMISSIONING_COURSE')</th>
                                    <th class="vcenter text-center">@lang('label.COMMISSIONING_DATE')</th>
                                    <th class="vcenter text-center">@lang('label.COMMISSIONING_TYPE')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($targetArr))
                                <?php
                                $sl = 0;
                                ?>

                                @foreach($targetArr as $id => $target)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>

                                   <td class="vcenter">
                                        {!! $target['personal_no']?? '' !!}
                                    </td>
                                    <td class="vcenter">
                                        {!! $target['rank']?? '' !!}
                                    </td>
                                    <td class="vcenter">
                                        {!! $target['full_name']?? '' !!}
                                    </td>

                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/user/' . $target['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    <td class="vcenter">{!! $target['arms_service_name'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['appointment_name']?? '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['join_date']) ? Helper::formatDate($target['join_date']): '' !!}</td>
                                    <td class="vcenter">{!! !empty($target['commissioning_course_id']) && !empty($comCourseList[$target['commissioning_course_id']]) ? $comCourseList[$target['commissioning_course_id']]: '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['commisioning_date']) ? Helper::formatDate($target['commisioning_date']): '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['commission_type']) && !empty($commissionTypeList[$target['commission_type']]) ? $commissionTypeList[$target['commission_type']] : '' !!}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="11">@lang('label.NO_DS_FOUND')</td>
                                </tr>
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
        //table header fix
        $(".table-head-fixer-color").tableHeadFixer();
    $('#columns').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            nonSelectedText: "@lang('label.SELECT_FIELD')",
//        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                courseAllSelected = true;
            },
            onChange: function () {
                courseAllSelected = false;
            }
        });

        $("#columns").on('change', function () {
            var columns = $(this).val();
            if(columns == null){
                return false;
            }
            columns = columns.toString();
            
            var hrefPrint = "<?php echo URL::full() . '&view=print&columns='; ?>" + columns;
            var hrefExcel = "<?php echo URL::full() . '&view=excel&columns='; ?>" + columns;
            $('.print').attr('href', hrefPrint);
            $('.excel').attr('href', hrefExcel);
        });
        });

</script>
@stop