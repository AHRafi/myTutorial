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
    <div class="row margin-top-10">
        
    </div>
</div>
<!--<script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>-->
<script type="text/javascript">

</script>

<!--EOF SHORT ICON-->
@endsection