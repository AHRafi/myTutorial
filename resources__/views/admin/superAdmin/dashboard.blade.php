@extends('layouts.default.master')
@section('data_count')
@if (session('status'))

<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<div class="portlet-body">
    @include('admin.commonFeatures.all')
    <div class="clear-fix"></div>

    <!--TERM TO COURSE CARD-->
    @include('admin.commonFeatures.courseBlock')

    <!--END :    : TERM TO COURSE CARD-->
    <!--TERM SCHEDULING CARD-->
    <div class="row margin-top-20">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat yellow-casablanca tooltips" href="{!! URL::to('/termToCourse/activationOrClosing') !!}" title="@lang('label.TERM_SCHEDULING_ACTIVATION_CLOSING')">
                <div class="visual">
                    <i class="fa fa-sliders"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-sliders"></i>
                    </div>
                    <div class="desc">@lang('label.TERM_SCHEDULING_ACTIVATION_CLOSING')</div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat purple-soft tooltips" href="{!! URL::to('/termToEvent') !!}" title="@lang('label.TERM_TO_EVENT')">
                <div class="visual">
                    <i class="fa fa-cubes"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-cubes"></i>
                    </div>
                    <div class="desc">@lang('label.TERM_TO_EVENT')</div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat green-sharp tooltips" href="{!! URL::to('/termToMAEvent') !!}" title="@lang('label.TERM_TO_MA_EVENT')">
                <div class="visual">
                    <i class="fa fa-puzzle-piece"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-puzzle-piece"></i>
                    </div>
                    <div class="desc">@lang('label.TERM_TO_MA_EVENT')</div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat blue-hoki tooltips" href="{!! URL::to('/markingGroup') !!}" title="@lang('label.ASSIGN_MARKING_GROUP')">
                <div class="visual">
                    <i class="fa fa-pencil"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <div class="desc">@lang('label.ASSIGN_MARKING_GROUP')</div>
                </div>
            </a>
        </div> 
    </div>
    <div class="row margin-top-20">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat yellow tooltips" href="{!! URL::to('/termToCourse') !!}" title="@lang('label.TERM_SCHEDULING')">
                <div class="visual">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div class="desc">@lang('label.TERM_SCHEDULING')</div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat blue-steel tooltips" href="{!! URL::to('/synToCourse') !!}" title="@lang('label.SYN_TO_COURSE')">
                <div class="visual">
                    <i class="fa fa-sitemap"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-sitemap"></i>
                    </div>
                    <div class="desc">@lang('label.SYN_TO_COURSE')</div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat purple-studio tooltips" href="{!! URL::to('/cmGroupMemberTemplate') !!}" title="@lang('label.CM_GROUP_MEMBER_TEMPLATE')">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="desc">@lang('label.CM_GROUP_MEMBER_TEMPLATE')</div>
                </div>
            </a>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a class="dashboard-stat-v2  dashboard-stat green-jungle tooltips" href="{!! URL::to('/dsGroupMemberTemplate') !!}" title="@lang('label.DS_GROUP_MEMBER_TEMPLATE')">
                <div class="visual">
                    <i class="fa fa-book"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <i class="fa fa-book"></i>
                    </div>
                    <div class="desc">@lang('label.DS_GROUP_MEMBER_TEMPLATE')</div>
                </div>
            </a>
        </div> 
    </div>
    <!--END :: TERM SCHEDULING CARD-->
</div>
<script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">

</script>

<!--EOF SHORT ICON-->
@endsection