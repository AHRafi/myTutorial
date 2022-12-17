@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.MARITAL_INFO_WISE_DS_ANALYTICS')
            </div>
        </div>

        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'maritalInfoWiseDsAnalytics/filter','class' => 'form-horizontal')) !!}
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
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="armsService">@lang('label.ARMS_SERVICE') </label>
                        <div class="col-md-8">
                            {!! Form::select('arms_service_id', $armsServiceList,  Request::get('arms_service_id'), ['class' => 'form-control js-source-states', 'id' => 'armsService']) !!}
                            <span class="text-danger">{{ $errors->first('course_id') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="maritalStatus">@lang('label.MARITAL_STATUS') </label>
                        <div class="col-md-8">
                           {!! Form::select('marital_status', $maritalStatusList,  Request::get('marital_status'), ['class' => 'form-control js-source-states', 'id' => 'maritalStatus']) !!}
                            <span class="text-danger">{{ $errors->first('maritalStatus') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.MARRIAGE_DATE_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('marriage_date_from',Request::get('marriage_date_from') ?? null, ['class' => 'form-control', 'id' => 'domFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="domFrom">
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
                        <label class="control-label col-md-4">@lang('label.MARRIAGE_DATE_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2 ">
                                {!! Form::text('marriage_date_to',Request::get('marriage_date_to') ?? null, ['class' => 'form-control', 'id' => 'domTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="domTo">
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
                        <label class="control-label col-md-4" for="maritalStatus">@lang('label.SPOUSE_PROFESSION') </label>
                        <div class="col-md-8">
                           {!! Form::select('spouse_profession', $spouseProf,  Request::get('spouse_profession'), ['class' => 'form-control js-source-states', 'id' => 'maritalStatus']) !!}
                            <span class="text-danger">{{ $errors->first('spouse_profession') }}</span>
                        </div>
                    </div>
                </div>
                </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.SPOUSE_DOB_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('spouse_birth_date_from',Request::get('spouse_birth_date_from') ?? null, ['class' => 'form-control', 'id' => 'dobSpFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="dobSpFrom">
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
                        <label class="control-label col-md-4">@lang('label.SPOUSE_DOB_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2 ">
                                {!! Form::text('spouse_birth_date_to',Request::get('spouse_birth_date_to') ?? null, ['class' => 'form-control', 'id' => 'dobSpTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="dobSpTo">
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
                            {{__('label.MARITAL_STATUS')}} : <strong>{{ !empty($maritalStatusList[Request::get('marital_status')]) && Request::get('marital_status') != 0 ? $maritalStatusList[Request::get('marital_status')] : __('label.N_A') }} |</strong>
                            {{__('label.SPOUSE_PROFESSION')}} : <strong>{{ !empty($spouseProf[Request::get('spouse_profession')]) && Request::get('spouse_profession') != 0 ? $spouseProf[Request::get('spouse_profession')] : __('label.N_A') }} |</strong>
                            {{__('label.SPOUSE_DOB_FROM')}} : <strong>{{ !empty(Request::get('spouse_birth_date_from')) && Request::get('spouse_birth_date_from') != '' ? Request::get('spouse_birth_date_from') : __('label.N_A') }} |</strong>
                            {{__('label.SPOUSE_DOB_TO')}} : <strong>{{ !empty(Request::get('spouse_birth_date_to')) && Request::get('spouse_birth_date_to') != '' ? Request::get('spouse_birth_date_to') : __('label.N_A') }} |</strong>
                            {{__('label.MARRIAGE_DATE_FROM')}} : <strong>{{ !empty(Request::get('marriage_date_from')) && Request::get('marriage_date_from') != '' ? Request::get('marriage_date_from') : __('label.N_A') }} |</strong>
                            {{__('label.MARRIAGE_DATE_TO')}} : <strong>{{ !empty(Request::get('marriage_date_to')) && Request::get('marriage_date_to') != '' ? Request::get('marriage_date_to') : __('label.N_A') }} |</strong>
                            {{__('label.TOTAL_NO_OF_DS')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="webkit-scrollbar table-responsive max-height-500">
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
                                    <th class="vcenter text-center">@lang('label.DOB_SELF')</th>
                                    <th class="vcenter text-center">@lang('label.MARRIAGE_DATE')</th>
                                    <th class="vcenter text-center">@lang('label.SPOUSE_BIRTH_DATE')</th>
                                    <th class="vcenter">@lang('label.SPOUSE_PROFESSION')</th>
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
                                    <td class="vcenter text-center">{!! !empty($target['date_of_birth']) ? Helper::formatDate($target['date_of_birth']) : '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['date_of_marriage']) ? Helper::formatDate($target['date_of_marriage']) : '' !!}</td>
                                    <td class="vcenter text-center">{!! !empty($target['spouse_dob']) ? Helper::formatDate($target['spouse_dob']) : '' !!}</td>
                                    <td class="vcenter">{!! !empty($target['spouse_occupation']) && $target['spouse_occupation'] !=0 && $spouseProf[$target['spouse_occupation']] ? $spouseProf[$target['spouse_occupation']] : '' !!}</td>
                                    
                             
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
