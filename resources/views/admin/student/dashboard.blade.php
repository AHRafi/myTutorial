@extends('layouts.default.master')
@section('data_count')
@if (session('status'))

<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<div class="portlet-body">
    <!-- <div class="page-bar bg-default">
         <ul class="page-breadcrumb margin-top-10">
             <li>
                 <a href="{{url('dashboard')}}">@lang('label.DASHBOARD')</a>
                 <i class="fa fa-circle"></i>
             </li>
             <li>
                 <span class="bold" style="color: #006400">@lang('label.WELCOME_TO_RECRUIT_TRAINING_MANAGEMENT_SOFTWARE_STCS')</span>
             </li>
         </ul>
         <div class="page-toolbar margin-top-15">
             <h5 class="dashboard-date bold" style="color: #006400"><span class="icon-calendar"></span> Today is <span class="bold" style="color: #006400">{!! date('d F Y') !!}</span> </h5>   
         </div>
     </div>-->
    <div class="clear-fix"></div>
</div>
<!--<script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>-->
<script type="text/javascript">

</script>

<!--EOF SHORT ICON-->
@endsection