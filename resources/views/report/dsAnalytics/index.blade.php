@extends('layouts.default.master')
@section('data_count')


<!--@php

echo "<pre>";
print_r($dsDetailArr);
@endphp
<h3>ok</h3>
@php
echo "<pre>";
print_r($targetArr);


@endphp-->
<div class="col-md-12">
    <!-- BEGIN ACCORDION PORTLET-->
  
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DS_ANALYTICS')
            </div>

            <div class="tools"></div>
        </div>
        
            <!--            <button type="button" class="btn btn-secondary">Secondary</button>-->
            <a class="btn search-btn grey-mint btn-lg tooltips" title="@lang('label.CLICK_FOR_SEARCH')">
                <i class="fa fa-th"></i>
            </a>

        @if(Request::get('generate') == 'true')
        <div class="portlet-body">
        <table class="table table-bordered table-hover table-head-fixer-color" id="searchTable">
                <thead>
                    <tr>
                        <th class="vcenter text-center">@lang('label.SERIAL')</th>
                        <th class="vcenter text-center">@lang('label.PHOTO')</th>
                        <th class="vcenter text-center">@lang('label.DS')</th>
                        <th class="vcenter">@lang('label.ARMS_SERVICE')</th>

                        @if(!empty(Request::get('matrix_2')))
                        <th class="vcenter">@lang('label.COMMISSIONING_COURSE')</th>
                        @endif
                        @if(!empty(Request::get('matrix_3')))
                        <th class="vcenter">@lang('label.EMAIL')</th>
                        <th class="vcenter">@lang('label.MOBILE')</th>
                        <th class="vcenter">@lang('label.DATE_OF_BIRTH')</th>
                        @endif
                        @if(!empty(Request::get('matrix_4')))
                        <th class="vcenter">@lang('label.DATE_OF_MARRIAGE')</th>
                        <th class="vcenter">@lang('label.DATE_OF_BIRTH_OF_SPOUSE')</th>


                        @endif
                        @if(!empty(Request::get('matrix_5')))
                        <th class="vcenter">@lang('label.PASSPORT_NO')</th>
                        <th class="vcenter">@lang('label.PASSPORT_DATE_OF_ISSUE')</th>
                        <th class="vcenter">@lang('label.PASSPORT_DATE_OF_EXPIRE')</th>
                        @endif
                        @if(!empty(Request::get('matrix_6')))
                        <th class="vcenter">@lang('label.BANK_NAME')</th>
                        <th class="vcenter">@lang('label.BRANCH')</th>

                        @endif
                        
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
                        <td class="vcenter text-center" width="50px">
                            @if(!empty($target['photo']) && File::exists('public/uploads/user/' . $target['photo']))
                            <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                            @else
                            <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                            @endif
                        </td>
                        <td class="vcenter width-180">
                            <div class="width-inherit">
                                {!! !empty($target['ds_name']) ? $target['ds_name']:'' !!}
                            </div>
                        </td>

                        <td class="vcenter">{!! $target['arms_service_name'] ?? '' !!}</td>
                        @if(!empty(Request::get('matrix_2')))
                        <td class="vcenter">{!! $target['comm_course_name'] ?? '' !!}</td>
                        @endif
                        @if(!empty(Request::get('matrix_3')))
                        <td class="vcenter">{!! $target['email'] ?? '' !!}</td>
                        <td class="vcenter">{!! $target['phone'] ?? '' !!}</td>
                        <td class="vcenter">{!! $target['date_of_birth'] ?? '' !!}</td>
                        @endif
                        @if(!empty(Request::get('matrix_4')))
                        <td class="vcenter">{!! $target['date_of_marriage'] !!}</td>
                        <td class="vcenter">{!! $target['spouse_dob'] !!}</td>
                        @endif
                        @if(!empty(Request::get('matrix_5')))
                        <td class="vcenter">{{  $dsDetailArr[$target['id']]['passport']['passport_no'] ?? ' ' }}</td>
                        <td class="vcenter">{{  $dsDetailArr[$target['id']]['passport']['date_of_issue'] ?? ' ' }}</td>
                        <td class="vcenter">{{  $dsDetailArr[$target['id']]['passport']['date_of_expire'] ?? ' ' }}</td>
                        @endif
                        @if(!empty(Request::get('matrix_6')))
                        <td class="vcenter">{{ $dsDetailArr[$target['id']]['bank']['name'] ?? " " }}</td>
                        <td class="vcenter">{{ $dsDetailArr[$target['id']]['bank']['branch'] ?? " " }}</td>

                        @endif
                        

                        
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="11">@lang('label.NO_CM_FOUND')</td>
                    </tr>
                    @endif
                </tbody>

            </table>
            </div>
            @elseif(Request::get('generate') == 'false')
            <div class="alert alert-danger alert-dismissable col-md-offset-2 col-md-8">
                <p><strong><i class="fa fa-exclamation-triangle"></i></i> {!! __('label.REQUIRED_FIELD_HAVE_NOT_FILLED') !!}</strong></p>
            </div>
            @else
            <div class="alert alert-info alert-dismissable col-md-offset-2 col-md-8 ">
                <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.CLICK_BUTTON_TO_SEARch_DS') !!}</strong></p>
            </div>

            @endif
        


        

       
        
            <div class="search-engine-block">
            {!! Form::open(array('group' => 'form', 'url' => 'dsAnalytics/filter','class' => 'form-horizontal', 'id' => 'submitForm')) !!}
            <div class="panel panel-default ">
                <div class="panel-heading">
                    <div class="form-group">
                        <label class="text-center margin-top-3 col-md-10"><span class="bold ">@lang('label.SEARCH_BOX') </span></label>
                        <div class="col-md-1">
                            <a class="pull-right search-div-close btn btn-danger btn-xs tooltips" title="@lang('label.CLOSE')"><i class="fa fa-close"></i></a> 

                        </div>
                    </div> 
                </div>
            </div>
            <div class="max-height-300 table-responsive webkit-scrollbar">
            <div class="panel panel-default">
                <div class="panel-heading matrix" data-id="1">
                    {!! Form::hidden('matrix[1]', Request::get('matrix_1') ?? 0, ['id' => 'matrix_1', 'class' => 'matrix-check']) !!}
                    <span class="bold"><i class="fa fa-search"></i> @lang('label.SERVICE_INFORMATION')</span>
                </div>
                <div class="panel-body matrix-1 matrix-body display-none" style="overflow-y:auto;">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="serviceId">@lang('label.WINGS')</label>
                        <div class="col-md-8">
                            {!! Form::select('service_id', $serviceList,  Request::get('service_id')  ?? null, [ 'class' => 'form-control js-source-states','id' => 'serviceId']) !!}
                        </div>
                    </div>             
                    <div class="form-group">
                        <label class="control-label col-md-4" for="rankId">@lang('label.RANK')</label>
                        <div class="col-md-8">
                            {!! Form::select('rank_id', $rankList,  Request::get('rank_id') ?? null, ['class' => 'form-control js-source-states', 'id' => 'rankId']) !!}

                        </div>
                    </div>    
                    <div class="form-group">
                        <label class="control-label col-md-4" for="armsSvcId">@lang('label.ARMS_SERVICE')</label>
                        
                        <div class="col-md-8">
                            {!! Form::select('arms_service_id', $armsSvcList,  Request::get('arms_service_id') ?? null , ['class' => 'form-control js-source-states', 'id' => 'armsSvcId']) !!}
                        </div>
                    </div>  
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading matrix" data-id="2">
                    {!! Form::hidden('matrix[2]', Request::get('matrix_2') ?? 0, ['id' => 'matrix_2', 'class' => 'matrix-check']) !!}
                    <span class="bold"><i class="fa fa-search"></i> @lang('label.COMMISSION_COURSE_INFORMATION')</span>
                </div>
                <div class="panel-body matrix-2 matrix-body display-none">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commissioningCourseId">@lang('label.COMMISSIONING_COURSE')</label>
                        <div class="col-md-8">
                            {!! Form::select('commissioning_course_id', $commissioningCourseList,  Request::get('commissioning_course_id') ?? null, ['class' => 'form-control js-source-states', 'id' => 'commissioningCourseId']) !!}

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4" for="commissioningTypeId">@lang('label.TYPE_OF_COMMISSION')</label>
                        <div class="col-md-8">
                            {!! Form::select('commissioning_type_id', $commissioningTypeList,  Request::get('commissioning_type_id') ?? null, ['class' => 'form-control js-source-states', 'id' => 'commissioningTypeId']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading matrix" data-id="3">
                    {!! Form::hidden('matrix[3]', Request::get('matrix_3') ?? 0 , ['id' => 'matrix_3', 'class' => 'matrix-check']) !!}
                    <span class="bold"><i class="fa fa-search"></i> @lang('label.BASIC_INFORMATION') </span>
                </div>
                <div class="panel-body matrix-3 matrix-body display-none">
                    <div class="form-group">
                        <label class="col-md-12 bold text-left">@lang('label.DATE_OF_BIRTH') </label>
                        <label class="control-label col-md-4">@lang('label.YEAR_OF_BIRTH') </label>
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::select('birth_ydar', $yearList, Request::get('birth_year') ?? 0, ['class' => 'form-control js-source-states']) !!}

                            </div>
                        </div>
                        <br>
                        <label class="control-label col-md-4" for="courseId">@lang('label.MONTH_OF_BIRTH') </label>
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::select('birth_month', $monthList, Request::get('birth_month') ?? 0, ['class' => 'form-control js-source-states']) !!}

                            </div>
                        </div>
                    </div>                                 
                    <div class="form-group">
                        <label class="control-label col-md-4" for="bloodGrouprId">@lang('label.BLOOD_GROUP')</label>
                        <div class="col-md-8">
                            {!! Form::select('blood_group_id', $bloodGroupList, Request::get('blood_group_id') ?? 0, ['class' => 'form-control js-source-states', 'id' => 'bloodGroupId']) !!}
                        </div>
                    </div>                                 
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading matrix" data-id="4">
                    {!! Form::hidden('matrix[4]', Request::get('matrix_5') ?? 0, ['id' => 'matrix_4', 'class' => 'matrix-check']) !!}
                    <span class="bold"><i class="fa fa-search"></i> @lang('label.MARITAL_INFORMATION')</span> 
                </div>
                <div class="panel-body matrix-4 matrix-body display-none">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.DATE_OF_MARRIAGE')</label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('date_of_marriage',  Request::get('date_of_marriage') ?? null, ['class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>                                 
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.DATE_OF_BIRTH_OF_SPOUSE') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('date_of_birth_of_spouse', Request::get('date_of_birth_of_spouse') ?? null, ['class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button">
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
            <div class="panel panel-default">
                <div class="panel-heading matrix" data-id="5">
                    {!! Form::hidden('matrix[5]', Request::get('matrix_5') ?? 0, ['id' => 'matrix_5', 'class' => 'matrix-check']) !!}
                    <span class="bold"><i class="fa fa-search"></i>@lang('label.PASSPORT_DETAILS') </span>
                </div>
                <div class="panel-body matrix-5 matrix-body display-none">
                    <div class="form-group">
                        <label class="col-md-12 bold text-left">@lang('label.DATE_OF_ISSUE') </label>
                        <label class="control-label col-md-4">@lang('label.DATE_OF_ISSUE_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('passport_issue_date_from',Request::get('passport_issue_date_from') ?? null, ['class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <label class="control-label col-md-4" for="courseId">@lang('label.DATE_OF_ISSUE_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('passport_issue_date_to',Request::get('passport_issue_date_to') ?? null, ['class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-md-12 bold text-left">@lang('label.DATE_OF_EXPIRE_LABEL') </label>
                        <label class="control-label col-md-4">@lang('label.DATE_OF_EXPIRE_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('passport_expire_date_from',Request::get('passport_expire_date_from') ?? null, ['class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <label class="control-label col-md-4" for="courseId">@lang('label.DATE_OF_EXPIRE_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('passport_expire_date_to',Request::get('passport_expire_date_to') ?? null, ['class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button">
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
            <div class="panel panel-default">
                <div class="panel-heading matrix" data-id="6">
                    {!! Form::hidden('matrix[6]',Request::get('matrix_6') ?? 0, ['id' => 'matrix_6', 'class' => 'matrix-check']) !!}
                    <span class="bold"><i class="fa fa-search"></i>@lang('label.BANK_INFO')</span>
                </div>
                <div class="panel-body matrix-6 matrix-body display-none">
                    <div class="form-group">
                        <label class="control-label col-md-4">@lang('label.BANK_NAME')</label>
                        <div class="col-md-8">
                            {!! Form::text('bank_name', Request::get('bankName') ?? null, ['class' => 'form-control']) !!}
                        </div>
                    </div>                                 

                    <div class="form-group">
                        <label class="control-label col-md-4" for="result">@lang('label.BRANCH')</label>
                        <div class="col-md-8">
                            {!! Form::text('branch', Request::get('branch') ?? null, ['class' => 'form-control']) !!}
                        </div>
                    </div>           
                    <div class="form-group">
                        <label class="control-label col-md-4" for="result">@lang('label.ONLINE')</label>
                        <div class="col-md-8">
                            <div class="md-checkbox cm-matrix" >
                                {!! Form::checkbox('online_check', '1', Request::get('online_check')?? 0 ,['class' => 'form-control','id' => 'onlineCheck' , 'class'=> 'md-check ']) !!}
                                <label for="onlineCheck">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck tooltips"></span>
                                    <span class="box mark-caheck tooltips"></span>
                                </label>
                                <span class="padding-left-10">Check, if online available</span>
                            </div>
                        </div>
                    </div>                                 
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading matrix" data-id="7">
                    {!! Form::hidden('matrix[7]', Request::get('matrix_7')?? 0, ['id' => 'matrix_7', 'class' => 'matrix-check']) !!}
                    <span class="bold"><i class="fa fa-search"></i>@lang('label.MIL_QUALIFICATION') </span>
                </div>

                <div class="panel-body matrix-7 matrix-body display-none">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            {!! Form::select('mil_course_id', $miCourseList,  Request::get('mil_course_id')?? null, ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                        </div>
                    </div>                                 
                    <div class="form-group">
                        <label class="control-label col-md-4" for="result">@lang('label.RESULT')</label>
                        <div class="col-md-8">
                            {!! Form::text('mil_result', Request::get('mil_result') ?? null, ['class' => 'form-control', 'id' => 'result']) !!}
                        </div>
                    </div>                                 
                </div>
            </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                    <i class="fa fa-search"></i> Generate </button>
                            </div>
                        </div>                                 
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
     

</div>
        
    </div>




<script type="text/javascript">
    
$(function(){
    $(".search-engine-block").hide();
    
    $(".search-btn").on("click",function(){
        $(".search-engine-block").show(750);
        
    });
    $(".search-div-close").on("click", function () {
            $(".search-engine-block").hide(750);
        });
    
    $('.matrix').on("click", function () {
            var matrixId = $(this).attr("data-id");
            var martixCheck = $('#matrix_' + matrixId).val();

            if (martixCheck == 1) {
                //activtad this panel body
                $('#matrix_' + matrixId).val(0);
                $('.matrix-' + matrixId).addClass('display-none');
            } else {
                //deactivtad other panel body
                //                        $('.matrix-check').val(0);
                //                        $('.matrix-body').addClass('display-none');
                //activtad this panel body
                $('#matrix_' + matrixId).val(1);
                $('.matrix-' + matrixId).removeClass('display-none');
            }
        });
    
    
    
    
    
    
});   
</script>    

@stop