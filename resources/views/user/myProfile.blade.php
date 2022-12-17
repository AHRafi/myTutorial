@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user"></i>@lang('label.MY_PROFILE')
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="my-profile-sidebar">
                <!-- PORTLET MAIN -->
                <div class="portlet light">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-pic">
                        @if(!empty($target->photo))
                        <img class="img-responsive" src="{{URL::to('/')}}/public/uploads/user/{{$target->photo}}" alt="{{$target->full_name}}"/>
                        @else
                        <img class="img-responsive" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{$target->full_name}}"/>
                        @endif
                    </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">{{$target->rank->code.' '.$target->full_name}} </div>
                        <div class="profile-usertitle-job"> {{$target->appointment->name}} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->

                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="{{url('myProfile')}}">
                                    <i class="icon-home"></i>@lang('label.OVERVIEW')  
                                </a>
                            </li>
                            <li>
                                <a href="{{url('accountSetting')}}">
                                    <i class="icon-settings"></i>@lang('label.ACCOUNT_SETTINGS')  
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- END MENU -->
                </div>
                <!-- END PORTLET MAIN -->
            </div>
            <!-- END BEGIN PROFILE SIDEBAR -->

            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-advance">
                            <tr>
                                <th>{!! __('label.ORGANISATION') !!}</th>
                                <td>
                                    @if($target->category_id == '1')
                                    <span>@lang('label.AHQ_PA_DTE')</span>
                                    @elseif($target->category_id == '2')
                                    <span>@lang('label.HQ_ARTDOC')</span>
                                    @else
                                    <span>@lang('label.CENTRE')</span>	
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{!! __('label.USER_GROUP') !!}</th>
                                <td> {!! $target->group_name !!}</td>
                            </tr>
                            <tr>
                                <th>{!! __('label.RANK') !!}</th>
                                <td> {!! $target->rank->code !!}</td>
                            </tr>
                            <tr>
                                <th>{!! __('label.CENTER') !!}</th>
                                <td> {!! !empty($target->center_name)?$target->center_name:'' !!}</td>
                            </tr>
                            <tr>
                                <th>{!! __('label.APPOINTMENT') !!}</th>
                                <td>{!! $target->appointment->code !!} </td>
                            </tr>
                            <tr>
                                <th>{!! __('label.PERSONEL_NO') !!}</th>
                                <td>{!! $target->personal_no !!} </td>
                            </tr>
                            <tr>
                                <th>{!! __('label.FULL_NAME') !!}</th>
                                <td>{{ $target->full_name }}</td>
                            </tr>
                            <tr>
                                <th>{!! __('label.OFFICIAL_NAME') !!}</th>
                                <td>{{ $target->official_name }}</td>
                            </tr>
                            <tr>
                                <th>{!! __('label.USERNAME') !!}</th>
                                <td>{{ $target->username }}</td>
                            </tr>
                            <tr>
                                <th>{!! __('label.EMAIL') !!}</th>
                                <td>{{ $target->email }}</td>
                            </tr>
                            <tr>
                                <th>{!! __('label.PHONE') !!}</th>
                                <td>{{ $target->phone }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->

    </div>
</div>
</div>
@stop