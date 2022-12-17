@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.PASSPORT_INFORMATION_WISE_CM_ANALYTICS')
            </div>
        </div>

        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'passportInfoWiseCmAnalytics/filter','class' => 'form-horizontal')) !!}
            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') </label>
                        <div class="col-md-8">
                            {!! Form::select('course_id[]', $courseList, !empty(Request::get('course_id'))?explode(',',Request::get('course_id')):'' , [ 'id' => 'courseId', 'class' => 'form-control mt-multiselect btn btn-default',  'multiple' , 'data-select-all'=>"true", 'data-width' => '100%']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="name">@lang('label.NAME')</label>
                        <div class="col-md-8">
                            {!! Form::text('name',  Request::get('name'), ['class' => 'form-control tooltips', 'id' => 'name', 'title' => 'Full Name/Official Name', 'placeholder' => 'Full Name/Official Name', 'list' => 'cmName', 'autocomplete' => 'off']) !!} 
                            <datalist id="cmName">
                                @if (!$nameArr->isEmpty())
                                @foreach($nameArr as $item)
                                <option value="{!! $item->official_name !!}" />
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
                        <label class="control-label col-md-4">@lang('label.DATE_OF_ISSUE_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('issue_from',Request::get('issue_from') ?? null, ['class' => 'form-control', 'id' => 'doisFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="doisFrom">
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
                        <label class="control-label col-md-4">@lang('label.DATE_OF_ISSUE_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('issue_to',Request::get('issue_to') ?? null, ['class' => 'form-control', 'id' => 'doisTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="doisTo">
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
                        <label class="control-label col-md-4">@lang('label.DATE_OF_EXPIRE_FROM') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2">
                                {!! Form::text('expire_from',Request::get('expire_from') ?? null, ['class' => 'form-control', 'id' => 'doexpryFrom', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="doexpryFrom">
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
                        <label class="control-label col-md-4">@lang('label.DATE_OF_EXPIRE_TO') </label>
                        <div class="col-md-8">
                            <div class="input-group date datepicker2 ">
                                {!! Form::text('expire_to',Request::get('expire_to') ?? null, ['class' => 'form-control', 'id' => 'doexpryTo', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                <span class="input-group-btn">
                                    <button class="btn default reset-date" type="button" remove="doexpryTo">
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
                            {{__('label.DATE_OF_ISSUE_FROM')}} : <strong>{{ !empty(Request::get('issue_from')) && Request::get('issue_from') != '' ? Request::get('issue_from') : __('label.N_A') }} |</strong>
                            {{__('label.DATE_OF_ISSUE_TO')}} : <strong>{{ !empty(Request::get('issue_to')) && Request::get('issue_to') != '' ? Request::get('issue_to') : __('label.N_A') }} |</strong>
                            {{__('label.DATE_OF_EXPIRE_FROM')}} : <strong>{{ !empty(Request::get('expire_from')) && Request::get('expire_from') != '' ? Request::get('expire_from') : __('label.N_A') }} |</strong>
                            {{__('label.DATE_OF_EXPIRE_TO')}} : <strong>{{ !empty(Request::get('expire_to')) && Request::get('expire_to') != '' ? Request::get('expire_to') : __('label.N_A') }} |</strong>
                            {{__('label.TOTAL_NO_OF_CM')}} : <strong>{{ !empty($targetArr) ? sizeof($targetArr) : 0 }}</strong>

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
                                    <th class="vcenter">@lang('label.AFWC_COURSE_NAME')</th>
                                    <th class="vcenter">@lang('label.PASSPORT_NO')</th>
                                    <th class="vcenter">@lang('label.PLACE_OF_ISSUE')</th>
                                    <th class="vcenter text-center">@lang('label.DATE_OF_ISSUE')</th>
                                    <th class="vcenter text-center">@lang('label.DATE_OF_EXPIRE')</th>
                                    <th class="vcenter text-center">@lang('label.PASS_SCAN_COPY')</th>
                                    <th class="vcenter text-center">@lang('label.PHOTO_WITHOUT_UNIFORM')</th>

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
                                    <td class="vcenter text-center">{!! $target['arms_service_name'] ?? '' !!}</td>

                                    <td class="vcenter">{!! $target['course_name']?? '' !!}</td>
                                    <td class="vcenter">{!! $target['passport_no'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['place_of_issue'] ?? '' !!}</td>
                                    <td class="vcenter text-center">{!! $target['date_of_issue'] ? Helper::formatDate($target['date_of_issue']) : '' !!}</td>
                                    <td class="vcenter text-center">{!! $target['date_of_expire'] ? Helper::formatDate($target['date_of_expire'] ) : '' !!}</td>

                                    <td class="vcenter text-center">
                                        @if(!empty($target['pass_scan_copy']) && File::exists('public/uploads/cmPassport/' . $target['pass_scan_copy']))

                                        <a class="btn btn-xs yellow-casablanca download-content tooltips" title="{{$target['pass_scan_copy']}}" data-content="{{$target['pass_scan_copy']}}" >
                                            <span class="tooltips" title="@lang('label.DOWNLOAD')"><i class="fa fa-file-pdf-o"></i> </span> 
                                        </a>
                                        @else
                                        <span class="label label-sm label-gray-mint">@lang('label.N_A') </span> 
                                        @endif
                                    </td>
                                    <td class="vcenter text-center" width="35px">
                                        @if(!empty($target['photo_without_uniform']) && File::exists('public/uploads/cmPassPhoto/' . $target['photo_without_uniform']))
                                        <img class="profile-zoom" width="35" height="50" src="{{URL::to('/')}}/public/uploads/cmPassPhoto/{{$target['photo_without_uniform']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img class="profile-zoom" width="35" height="50" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>

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
        var courseAllSelected = false;
        $('#courseId').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            nonSelectedText: "@lang('label.SELECT_COURSE')",
//        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                courseAllSelected = true;
            },
            onChange: function () {
                courseAllSelected = false;
            }
        });
        $("#passDownload").on('change', function () {
            var copyName = $(name).val();
            alert(copyName);
        });
        $(document).ready(function () {
            $(document).on("click", '.download-content', function (e) {
                var content = $(this).attr("data-content");
                var basePath = "{!! URL::to('/') !!}";
                var path = basePath + '/public/uploads/cmPassport/' + content;
                var a = document.createElement("a");
                a.href = path;
                a.setAttribute("download", content);
                a.click();
            });
        });
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
            if (columns == null) {
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







