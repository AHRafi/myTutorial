@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">

    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.MIL_QUAL_WISE_DS_ANALYTICS')
            </div>
        </div>

        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'milQualWiseDsAnalytics/filter','class' => 'form-horizontal')) !!}
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
                        <label class="control-label col-md-4" for="courseId">@lang('label.MIL_COURSE') </label>
                        <div class="col-md-8">
                            {!! Form::select('mil_course_id[]', $milCourseList, !empty(Request::get('mil_course_id'))?explode(',',Request::get('mil_course_id')):'' , [ 'id' => 'courseId', 'class' => 'form-control mt-multiselect btn btn-default',  'multiple' , 'data-select-all'=>"true", 'data-width' => '100%']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="knResult">@lang('label.KNOWLEDGE_BASED_RESULT_GRADE')</label>
                        <div class="col-md-8">
                            {!! Form::select('kn_result', $knGradeList,  Request::get('kn_result'), ['class' => 'form-control js-source-states', 'id' => 'knResult']) !!}
                            <span class="text-danger">{{ $errors->first('kn_result') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="instResult">@lang('label.INSTRUCTION_BASED_RESULT_GRADE')</label>
                        <div class="col-md-8">
                            {!! Form::select('inst_result', $instGradeList,  Request::get('inst_result'), ['class' => 'form-control js-source-states', 'id' => 'instResult']) !!}
                            <span class="text-danger">{{ $errors->first('inst_result') }}</span>
                        </div>
                    </div>
                </div>
                <!--                <div class="col-md-4">
                                    <div class="form-group">
                                            <label class="control-label col-md-4" for="result">@lang('label.FOREIGN_COURSE')</label>
                                            <div class="col-md-8">
                                                <div class="md-checkbox cm-matrix" >
                                                    {!! Form::checkbox('foreign_course', '1', Request::get('foreign_course')?? 0 ,['class' => 'form-control','id' => 'foreignCourse' , 'class'=> 'md-check ']) !!}
                                                    <label for="foreignCourse">
                                                        <span class="inc"></span>
                                                        <span class="check mark-caheck tooltips"></span>
                                                        <span class="box mark-caheck tooltips"></span>
                                                    </label>
                                                    <span class="padding-left-10">   @lang('label.FOREIGN_COURSE_CHECK')</span>
                                                </div>
                                            </div>
                                        </div>  
                                </div>-->

                <div class="col-md-4 text-center">
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
                            {{__('label.RANK')}} : <strong>{{ !empty($rankList[Request::get('rank_id')]) && Request::get('rank_id') != 0 ? $rankList[Request::get('rank_id')] : __('label.ALL') }} |</strong>
                            {{__('label.ARMS_SERVICE')}} : <strong>{{ !empty($armsServiceList[Request::get('arms_service_id')]) && Request::get('arms_service_id') != 0 ? $armsServiceList[Request::get('arms_service_id')] : __('label.ALL') }} |</strong>

                            {{__('label.KNOWLEDGE_BASED_RESULT_GRADE')}} : <strong>{{ !empty(Request::get('kn_result')) && Request::get('kn_result') != '' ? Request::get('kn_result') : __('label.N_A') }} |</strong>
                            {{__('label.INSTRUCTION_BASED_RESULT_GRADE')}} : <strong>{{ !empty(Request::get('inst_result')) && Request::get('inst_result') != '' ? Request::get('inst_result') : __('label.N_A') }} |</strong>
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
                                    <th class="vcenter">@lang('label.INSTITUTE_NAME')</th>
                                    <th class="vcenter">@lang('label.MIL_COURSE')</th>
                                    <th class="vcenter">@lang('label.RESULT')</th>
                                    <th class="vcenter">@lang('label.FROM')</th>
                                    <th class="vcenter">@lang('label.TO')</th>


<!--                                    <th class="vcenter text-center">@lang('label.PROFILE_DETAILS')</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($targetArr))
                                <?php
                                $sl = 0;
                                ?>

                                @foreach($targetArr as $id => $target)

                                <tr>
                                    <td class="vcenter text-center" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                        {!! ++$sl !!}
                                    </td>

                                     <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                        {!! Common::getFurnishedCmName($target['personal_no']) !!}
                                    </td>
                                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                        {!! Common::getFurnishedCmName($target['rank']) !!}
                                    </td>
                                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                        {!! Common::getFurnishedCmName($target['full_name']) !!}
                                    </td>

                                    <td class="vcenter text-center" width="50px" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/user/' . $target['photo']))
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img class="profile-zoom" width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                        {!! $target['arms_service_name'] ?? '' !!}
                                    </td>
                                    <td class="vcenter" rowspan="{{!empty($target['rec_svc_span']) ? $target['rec_svc_span'] : 1}}">
                                        {!! $target['appointment_name']?? '' !!}
                                    </td>




                                    @if(!empty($target['rec_svc']))
                                    <?php $i = 0; ?>
                                    @foreach($target['rec_svc'] as $rsKey => $rsInfo)
                                    <?php
                                    if ($i > 0) {
                                        echo '<tr>';
                                    }
                                    ?>
                                    <td class="vcenter">{!! $rsInfo['institute_name'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $rsInfo['course'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $rsInfo['result'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $rsInfo['from'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $rsInfo['to'] ?? '' !!}</td>

                                    <?php
                                    if ($i < ($target['rec_svc_span'] - 1)) {
                                        echo '</tr>';
                                    }
                                    $i++;
                                    ?>
                                    @endforeach
                                    @else 
                                    <td class="vcenter"></td>
                                    <td class="vcenter"></td>
                                    <td class="vcenter"></td>
                                    <td class="vcenter"></td>
                                    <td class="vcenter"></td>
                                    @endif
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