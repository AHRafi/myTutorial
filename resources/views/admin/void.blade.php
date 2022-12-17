@extends('layouts.default.master')
@section('data_count')
@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}

</div>
@endif
<!--h1> Welcome, {{ Auth::user()->name }}</h1-->
@include('layouts.flash')
<div class="portlet-body">
    <div class="page-bar bg-default">
        <ul class="page-breadcrumb margin-top-10">
            <li>
                <a href="{{url('dashboard')}}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Dashboard</span>
            </li>
        </ul>
        <div class="page-toolbar margin-top-15">
            <h5 class="dashboard-date font-blue-madison"><span class="icon-calendar"></span> Today is <span class="font-blue-madison">{!! date('d F Y') !!}</span> </h5>   
        </div>
    </div>
</div>
&nbsp;
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i> {{$message}}</p>
        </div>
    </div>
</div>
</div>
@stop