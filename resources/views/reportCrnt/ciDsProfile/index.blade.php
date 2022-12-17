@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CI_DS_PROFILE')
            </div>
        </div>
        <div class="portlet-body">
            @if (!empty($targetArr))
            <div class="row">
                <div class="col-md-12 text-right">
                    <a class="btn btn-md btn-primary vcenter" target="_blank"  href="{!! URL::full().'?view=print' !!}">
                        <span class="tooltips" title="@lang('label.PRINT')"><i class="fa fa-print"></i> </span> 
                    </a>
<!--                    <a class="btn btn-success vcenter" href="{!! URL::full().'?view=pdf' !!}">
                        <span class="tooltips" title="@lang('label.DOWNLOAD_PDF')"><i class="fa fa-file-pdf-o"></i></span>
                    </a>-->
<!--                    <a class="btn btn-warning vcenter" href="{!! URL::full().'&view=excel' !!}">
                        <span class="tooltips" title="@lang('label.DOWNLOAD_EXCEL')"><i class="fa fa-file-excel-o"></i> </span>
                    </a>-->
                </div>
            </div>
            @endif
            <div class="row margin-top-10">
                <div class="col-md-12 table-responsive">
                    <div class="max-height-500 webkit-scrollbar">
                        <table class="table table-bordered table-hover table-head-fixer-color" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center">@lang('label.SERIAL')</th>
                                    <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                    <th class="vcenter">@lang('label.RANK')</th>
                                    <th class="vcenter">@lang('label.FULL_NAME')</th>
                                    <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                                    <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                    <th class="vcenter">@lang('label.APPOINTMENT')</th>
                                    <th class="vcenter">@lang('label.ARMS_SERVICE')</th>
                                    <th class="vcenter">@lang('label.COMMISSIONING_COURSE')</th>
                                    <th class="vcenter">@lang('label.EMAIL')</th>
                                    <th class="vcenter">@lang('label.MOBILE')</th>
                                    <th class="vcenter text-center">@lang('label.PROFILE_DETAILS')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($targetArr))
                                <?php
                                $sl = 0;
                                $synId = null;
                                $subSynId = null;
                                ?>
                                @foreach($targetArr as $id => $target)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter">{!! !empty($target['personal_no']) ? $target['personal_no']:'' !!}</td>
                                    <td class="vcenter">{!! $target['rank']?? '' !!}</td>
                                    <td class="vcenter">{!! $target['full_name']??'' !!}</td>
                                    <td class="vcenter">{!! $target['official_name']??'' !!}</td>
                                    <td class="vcenter text-center" width="50px">
                                        @if(!empty($target['photo']) && File::exists('public/uploads/user/' . $target['photo']))
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$target['photo']}}" alt="{{$target['official_name']?? ''}}"/>
                                        @else
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target['official_name']?? ''}}"/>
                                        @endif
                                    </td>
                                    <td class="vcenter">{!! $target['appointment'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['arms_service_name'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['comm_course_name'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['email'] ?? '' !!}</td>
                                    <td class="vcenter">{!! $target['number'] ?? '' !!}</td>
                                    <td class="td-actions text-center vcenter">
                                        <div class="width-inherit">
                                            <a class="btn btn-xs green-seagreen tooltips vcenter" title="@lang('label.CLICK_HERE_TO_VIEW_PROFILE')" href="{!! URL::to('ciDsProfileReportCrnt/' . $target['id'] . '/profile') !!}">
                                                <i class="fa fa-user"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="11">@lang('label.NO_DATA_FOUND')</td>
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


<script type="text/javascript">

    $(function () {
        //table header fix
        $(".table-head-fixer-color").tableHeadFixer('');
    });

</script>
@stop