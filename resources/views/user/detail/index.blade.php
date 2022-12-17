@extends('layouts.default.master')
@section('data_count')
<!-- BEGIN CONTENT BODY -->
<!-- BEGIN PORTLET-->
@include('layouts.flash')
<!-- END PORTLET-->
<div class="row margin-left-right-0">

    <div class="col-md-12 text-right">
        <div class="actions">
            <a href="{{ URL::to('/user'.Helper::queryPageStr($qpArr)) }}" class="btn btn-sm blue-dark">
                <i class="fa fa-reply"></i>&nbsp;@lang('label.CLICK_TO_GO_BACK')
            </a>
        </div>
    </div>
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile">
            <div class="tabbable-line tabbable-full-width">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab_1" data-toggle="tab"> @lang('label.OVERVIEW') </a>
                    </li>
                    <li>
                        <a href="#tab_2" data-toggle="tab"> @lang('label.EDIT_PROFILE') </a>
                    </li>
                </ul>


                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <!-- START:: User Basic Info -->
                        <div class="row">
                            <!-- START::User Image -->
                            <div class="col-md-2 text-center">
                                <!-- SIDEBAR USER TITLE -->
                                <div class="profile-userpic">
                                    @if(!empty($userInfoData->photo) && File::exists('public/uploads/user/' . $userInfoData->photo))
                                    <img src="{{URL::to('/')}}/public/uploads/user/{{$userInfoData->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
                                         alt="{{ !empty($userInfoData->user_name)? $userInfoData->user_name:''}}" style="width: 100%;height: 100%;" />
                                    @else 
                                    <img src="{{URL::to('/')}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
                                         alt="{{ !empty($userInfoData->user_name)? $userInfoData->user_name:'' }}"  style="width: 100%;height: 100%;" />
                                    @endif
                                </div>
                                <div class="profile-usertitle">
                                    <div class="text-center margin-bottom-10">
                                        <b>{{!empty($userInfoData->user_name)? $userInfoData->user_name:''}}</b>
                                    </div>
                                    <?php
                                    $labelColorPN = 'grey-mint';
                                    $fontColorPN = 'blue-hoki';

                                    if ($userInfoData->wing_id == 1) {
                                        $labelColorPN = 'green-seagreen';
                                    } elseif ($userInfoData->wing_id == 2) {
                                        $labelColorPN = 'white';
                                        $fontColorPN = 'white';
                                    } elseif ($userInfoData->wing_id == 3) {
                                        $labelColorPN = 'blue-madison';
                                    }
                                    ?>
                                    <div class="bold label label-square label-sm font-size-11 label-{{$labelColorPN}}">
                                        <span class="bg-font-{{$fontColorPN}}">{{!empty($userInfoData->personal_no)? $userInfoData->personal_no:''}}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- END::User Image -->

                            <div class="col-md-10">
                                <!--<div class="column sortable ">-->
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-info-circle green-color-style-color"></i>@lang('label.BASIC_INFORMATION')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr >
                                                    <td class="vcenter fit bold info">@lang('label.WING')</td>
                                                    <td class="vcenter" > {{ !empty($userInfoData->wing_name) ? $userInfoData->wing_name: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.FATHERS_NAME')</td>
                                                    <td class="vcenter" >{{ !empty($userInfoData->father_name) ? $userInfoData->father_name: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.COMMISSIONING_COURSE')</td>
                                                    <td>{{$userInfoData->commissioning_course_name}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.ARMS_SERVICES')</td>
                                                    <td> {{ !empty($userInfoData->arms_service_name) ? $userInfoData->arms_service_name: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.TYPE_OF_COMMISSION')</td>
                                                    <td>{{ !empty($commissionTypeList[$userInfoData->commission_type]) ? $commissionTypeList[$userInfoData->commission_type] : '' }}</td>
                                                    <td class="vcenter fit bold info">@lang('label.EMAIL')</td>
                                                    <td>{{ !empty($userInfoData->email) ? $userInfoData->email: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.COMMISSIONING_DATE')</td>
                                                    <td>{{ isset($userInfoData->commisioning_date) ? Helper::formatDate($userInfoData->commisioning_date): ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.PHONE')</td>
                                                    <td>{{ !empty($userInfoData->phone) ? $userInfoData->phone: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.ANTI_DATE_SENIORITY')</td>
                                                    <td>{{ !empty($userInfoData->anti_date_seniority) ? $userInfoData->anti_date_seniority: __('label.N_A')}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.BLOOD_GROUP')</td>
                                                    <td>{{ !empty($bloodGroupList[$userInfoData->blood_group]) ? $bloodGroupList[$userInfoData->blood_group]: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</td>
                                                    <td class="vcenter">{{ !empty($userInfoData->date_of_birth) ? Helper::formatDate($userInfoData->date_of_birth) : '' }}</td>
                                                    <td class="vcenter fit bold info">@lang('label.RELIGION')</td>
                                                    <td>{{ !empty($userInfoData->religion_name) ? $userInfoData->religion_name: ''}}</td>
                                                </tr>

                                                <tr>
                                                    <?php
                                                    $maritalStatus = (!empty($maritalStatusList) && ($userInfoData->marital_status != '0') && isset($maritalStatusList[$userInfoData->marital_status])) ? $maritalStatusList[$userInfoData->marital_status] : __("label.N_A");
                                                    ?>
                                                    <td class="vcenter fit bold info">@lang('label.BIRTH_PLACE')</td>
                                                    <td>{{ !empty($userInfoData->birth_place) ? $userInfoData->birth_place: ''}}</td>
                                                    <td class = "vcenter fit bold info">@lang('label.MARITIAL_STATUS')</td>
                                                    <td> {{ $maritalStatus }} </td>
                                                </tr>

                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.EMAIL')</td>
                                                    <td class="vcenter">{{ !empty($userInfoData->email) ? $userInfoData->email: ''}}</td> 
                                                    <td class="vcenter fit bold info">@lang('label.PHONE')</td>
                                                    <td class="vcenter">{{ !empty($userInfoData->phone) ? $userInfoData->phone: ''}}</td>
                                                </tr>
                                                

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--</div>-->
                            </div>
                        </div>
                        <!-- END:: User Basic Info -->

                        <!-- SATRT::Marital Information and  Child Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-life-ring green-color-style-color"></i>@lang('label.MARITAL_INFORMATION')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                @if(!empty($userInfoData->marital_status) && $userInfoData->marital_status == '1')
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.SPOUSE_NAME')</td>
                                                    <td class="vcenter">{{ !empty($userInfoData->spouse_name) ? $userInfoData->spouse_name: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.DATE_OF_MARRIAGE')</td>
                                                    <td class="vcenter"> {{ !empty($userInfoData->date_of_marriage) ? Helper::formatDate($userInfoData->date_of_marriage): ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.NICK_NAME')</td>
                                                    <td class="vcenter">{{ !empty($userInfoData->spouse_nick_name) ? $userInfoData->spouse_nick_name: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</td>
                                                    <td class="vcenter"> {{ !empty($userInfoData->spouse_dob) ? Helper::formatDate($userInfoData->spouse_dob): ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.MOBILE')</td>
                                                    <td class="vcenter">{{ !empty($userInfoData->spouse_mobile) ? $userInfoData->spouse_mobile: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.PROFESSION')</td>
                                                    <td class="vcenter">{{ !empty($userInfoData->spouse_occupation) ? $userInfoData->spouse_occupation: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.WORK_ADDRESS')</td>
                                                    <td class="vcenter" colspan="3">{{ !empty($userInfoData->spouse_work_address) ? $userInfoData->spouse_work_address: ''}}</td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!--<div class="column sortable ">-->
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-life-ring green-color-style-color"></i>@lang('label.CHILDREN_INFORMATION')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter text-center fit bold info">@lang('label.SL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.NAME')</td>
                                                    <td class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</td>
                                                    <td class="vcenter fit bold info">@lang('label.SCHOOL_PROFESSION')</td>
                                                </tr>
                                                <?php
                                                $childData = !empty($childInfoData) ? json_decode($childInfoData->user_child_info, TRUE) : null;
                                                $sl = 0;
                                                ?>
                                                @if(!empty($childData))
                                                @foreach($childData as $cKey => $child)
                                                <tr>
                                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                                    <td class="vcenter">{!! !empty($child['name']) ? $child['name']: '' !!}</td>
                                                    <td class="vcenter">{!! !empty($child['dob']) ? $child['dob']: '' !!}</td>
                                                    <td class="vcenter">{!! !empty($child['school']) ? $child['school']: '' !!}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td class="vcenter bold" colspan="4">@lang('label.NO_OF_CHILDREN'):&nbsp;{!! !empty($childInfoData->no_of_child) ? $childInfoData->no_of_child : 0 !!}</td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td class="vcenter" colspan="4">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>


                                    </div>
                                </div>
                                <!--</div> -->

                            </div>


                        </div>
                        <!--END::Marital Information and  Child Information -->

                        <!--Start::Permanent address and present address -->
                        <div class="row">
                            <!-- Start::User Permanent Address-->
                            <div class="col-md-6">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-map-marker green-color-style-color"></i> @lang('label.PERMANENT_ADDRESS')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                @if(!empty($addressInfo))
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DIVISION')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($addressInfo->division_id) && !empty($divisionList[$addressInfo->division_id]) ? $divisionList[$addressInfo->division_id] : __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DISTRICT')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($addressInfo->district_id) && !empty($districtList[$addressInfo->district_id]) ? $districtList[$addressInfo->district_id] : __("label.N_A")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.THANA')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($addressInfo->thana_id) && !empty($thanaList[$addressInfo->thana_id]) ? $thanaList[$addressInfo->thana_id] : __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.ADDRESS')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($addressInfo->address_details) ? $addressInfo->address_details: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End::User Permanent Address-->
                            <!-- Start::present address-->
                            <div class="col-md-6">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-map-marker green-color-style-color"></i> @lang('label.PRESENT_ADDRESS')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                @if(!empty($presentAddressInfo))
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DIVISION')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($presentAddressInfo->division_id) && !empty($divisionList[$presentAddressInfo->division_id]) ? $divisionList[$presentAddressInfo->division_id] : __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DISTRICT')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($presentAddressInfo->district_id) && !empty($presentDistrictList[$presentAddressInfo->district_id]) ? $presentDistrictList[$presentAddressInfo->district_id] : __("label.N_A")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.THANA')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($presentAddressInfo->thana_id) && !empty($presentThanaList[$presentAddressInfo->thana_id]) ? $presentThanaList[$presentAddressInfo->thana_id] : __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.ADDRESS') (@lang('label.RESIDENCE'))
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($presentAddressInfo->address_details) ? $presentAddressInfo->address_details: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End::present address-->

                        </div>
                        <!--END::Permanent address and present address -->

                        <!-- SATRT::Passport & Bank Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-paper-plane green-color-style-color"></i>@lang('label.PASSPORT_DETAILS')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                @if(!empty($passportInfoData))
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.PASSPORT_NO')</td>
                                                    <td class="vcenter">{{ !empty($passportInfoData->passport_no) ? $passportInfoData->passport_no: __('label.N_A')}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.DATE_OF_ISSUE')</td>
                                                    <td class="vcenter"> {{ !empty($passportInfoData->date_of_issue) ? Helper::formatDate($passportInfoData->date_of_issue): __('label.N_A')}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.PLACE_OF_ISSUE')</td>
                                                    <td class="vcenter">{{ !empty($passportInfoData->place_of_issue) ? $passportInfoData->place_of_issue: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.DATE_OF_EXPIRY')</td>
                                                    <td class="vcenter"> {{ !empty($passportInfoData->date_of_expire) ? Helper::formatDate($passportInfoData->date_of_expire): ''}}</td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-bank green-color-style-color"></i>@lang('label.BANK_INFO')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter text-center fit bold info">@lang('label.SL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.BANK_NAME')</td>
                                                    <td class="vcenter fit bold info">@lang('label.BRANCH')</td>
                                                    <td class="vcenter fit bold info">@lang('label.ACCT_NO')</td>
                                                    <td class="vcenter fit bold info">@lang('label.ONLINE')</td>
                                                </tr>
                                                <?php
                                                $bankData = !empty($bankInfoData) ? json_decode($bankInfoData->bank_info, TRUE) : null;
                                                $sl = 0;
                                                ?>
                                                @if(!empty($bankData))
                                                @foreach($bankData as $bKey => $bank)
                                                <tr>
                                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                                    <td class="vcenter">{!! !empty($bank['name']) ? $bank['name']: '' !!}</td>
                                                    <td class="vcenter">{!! !empty($bank['branch']) ? $bank['branch']: '' !!}</td>
                                                    <td class="vcenter">{!! !empty($bank['account']) ? $bank['account']: '' !!}</td>
                                                    <td class="vcenter">{!! !empty($bank['is_online']) ? __('label.YES') : __('label.NO') !!}</td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td class="vcenter" colspan="5">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>


                                    </div>
                                </div>
                            </div>


                        </div>
                        <!--END::Passport & Bank Information -->

                        <!-- START:: Academic qualification Information-->
                        <div class="row"> 
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-graduation-cap green-color-style-color"></i>@lang('label.ACADEMIC_QUALIFICATION')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.INSTITUTE_NAME')</td>
                                                    <td class="vcenter fit bold info">@lang('label.EXAMINATION')</td>
                                                    <td class="vcenter fit bold info">@lang('label.FROM')</td>
                                                    <td class="vcenter fit bold info">@lang('label.TO')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.QUAL_ERODE')</td>
                                                </tr>
                                                <?php
                                                $cSlShow = 1;
                                                $civilEducation = !empty($civilEducationInfoData) ? json_decode($civilEducationInfoData->civil_education_info, true) : null;
                                                //echo '<pre>';        print_r($brotherSister);exit;
                                                ?>
                                                @if(!empty($civilEducation))
                                                @foreach($civilEducation as $ceVar => $civilEducationInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center">{{$cSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($civilEducationInfo['institute_name']) ? $civilEducationInfo['institute_name']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($civilEducationInfo['examination']) ? $civilEducationInfo['examination']: ''}}</td>
                                                    <td class="vcenter">{{ !empty($civilEducationInfo['from']) ? $civilEducationInfo['from']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($civilEducationInfo['to']) ? $civilEducationInfo['to']: ''}}</td>
                                                    <td class="vcenter text-center"> {{ !empty($civilEducationInfo['qual_erode']) ? $civilEducationInfo['qual_erode']: ''}}</td>
                                                </tr>
                                                <?php
                                                $cSlShow++;
                                                ?>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="6" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- End:: Academic qualification Information-->
                        <!-- START:: Mil qualification Information-->
                        <div class="row"> 
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-user green-color-style-color"></i>@lang('label.MIL_QUALIFICATION')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.INSTITUTE_N_COUNTRY')</td>
                                                    <td class="vcenter fit bold info">@lang('label.COURSE')</td>
                                                    <td class="vcenter fit bold info">@lang('label.FROM')</td>
                                                    <td class="vcenter fit bold info">@lang('label.TO')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.RESULT')</td>
                                                </tr>
                                                <?php
                                                $cSlShow = 1;
                                                $milQualification = !empty($defenceRelativeInfoData) ? json_decode($defenceRelativeInfoData->user_relative_info, true) : null;
                                                ?>
                                                @if(!empty($milQualification))
                                                @foreach($milQualification as $mKey => $milInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center">{{$cSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($milInfo['institute_name']) ? $milInfo['institute_name']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($milCourseList[$milInfo['course']]) ? $milCourseList[$milInfo['course']]: ''}}</td>
                                                    <td class="vcenter">{{ !empty($milInfo['from']) ? $milInfo['from']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($milInfo['to']) ? $milInfo['to']: ''}}</td>
                                                    <td class="vcenter text-center"> {{ !empty($milInfo['result']) ? $milInfo['result']: ''}}</td>
                                                </tr>
                                                <?php
                                                $cSlShow++;
                                                ?>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="6" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- End:: Mil qualification Information-->
                        <!-- Start:: Record of service Information-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa fa-cogs green-color-style-color"></i>@lang('label.RECORD_OF_SERVICE')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.FROM')</td>
                                                    <td class="vcenter fit bold info">@lang('label.TO')</td>
                                                    <td class="vcenter fit bold info">@lang('label.UNIT_FMN_INST')</td>
                                                    <td class="vcenter fit bold info">@lang('label.APPT')</td>
                                                </tr>
                                                <?php
                                                $serviceRecord = !empty($serviceRecordInfoData) ? json_decode($serviceRecordInfoData->service_record_info, true) : null;
                                                $srSlShow = 1;
                                                ?>
                                                @if(!empty($serviceRecord))
                                                @foreach($serviceRecord as $srVar => $serviceRecordInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center width-50">{{ $srSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($serviceRecordInfo['from']) ? $serviceRecordInfo['from']: ''}}</td>
                                                    <td class="vcenter">{{ !empty($serviceRecordInfo['to']) ? $serviceRecordInfo['to']: ''}}</td>
                                                    <?php
                                                    $unitFmnInst = (!empty($serviceRecordInfo['unit_fmn_inst']) && $serviceRecordInfo['unit_fmn_inst']) != 0 ? (!empty($organizationList[$serviceRecordInfo['unit_fmn_inst']]) ? $organizationList[$serviceRecordInfo['unit_fmn_inst']] : $serviceRecordInfo['unit_fmn_inst']) : '';
                                                    $appointment = (!empty($serviceRecordInfo['appointment']) && $serviceRecordInfo['appointment']) != 0 ? (!empty($allAppointmentList[$serviceRecordInfo['appointment']]) ? $allAppointmentList[$serviceRecordInfo['appointment']] : $serviceRecordInfo['appointment']) : '';
                                                    ?>
                                                    <td class="vcenter">{{ $unitFmnInst }}</td>
                                                    <td class="vcenter">{{ $appointment }}</td>
                                                </tr>
                                                <?php $srSlShow++; ?>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="5" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- End:: Record of service Information-->

                        <!-- Start:: un mission Information-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-globe green-color-style-color"></i>@lang('label.UN_MSN')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter text-center fit bold info" rowspan="2">@lang('label.SERIAL')</td>
                                                    <td class="vcenter text-center fit bold info" colspan="2">@lang('label.DURATION')</td>
                                                    <td class="vcenter fit bold info" rowspan="2">@lang('label.MSN')</td>
                                                    <td class="vcenter fit bold info" rowspan="2">@lang('label.APPT')</td>
                                                    <td class="vcenter fit bold info" rowspan="2">@lang('label.REMARKS')</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.FROM')</td>
                                                    <td class="vcenter fit bold info">@lang('label.TO')</td>
                                                </tr>
                                                <?php
                                                $msnData = !empty($msnDataInfo) ? json_decode($msnDataInfo->msn_info, true) : null;
                                                $srSlShow = 1;
                                                ?>
                                                @if(!empty($msnData))
                                                @foreach($msnData as $mKey => $msnInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center width-50">{{ $srSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($msnInfo['from']) ? $msnInfo['from']: ''}}</td>
                                                    <td class="vcenter">{{ !empty($msnInfo['to']) ? $msnInfo['to']: ''}}</td>
                                                    <td class="vcenter">{{ !empty($msnInfo['msn']) ? $msnInfo['msn']: ''}}</td>
                                                    <?php
                                                    $appointment = (!empty($msnInfo['appointment']) && $msnInfo['appointment']) != 0 ? (!empty($allAppointmentList[$msnInfo['appointment']]) ? $allAppointmentList[$msnInfo['appointment']] : $msnInfo['appointment']) : '';
                                                    ?>
                                                    <td class="vcenter">{{ $appointment }}</td>
                                                    <td class="vcenter">{{ !empty($msnInfo['remark']) ? $msnInfo['remark']: ''}}</td>
                                                </tr>
                                                <?php $srSlShow++; ?>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="6" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- End:: un mission Information-->

                        <!-- Start:: country visited Information-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-plane green-color-style-color"></i>@lang('label.COUNTRY_VISITED')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter text-center fit bold info" rowspan="2">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info" rowspan="2">@lang('label.NAME_OF_COUNTRY')</td>
                                                    <td class="vcenter text-center fit bold info" colspan="2">@lang('label.DURATION')</td>
                                                    <td class="vcenter fit bold info" rowspan="2">@lang('label.REASONS_FOR_VISIT')</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.FROM')</td>
                                                    <td class="vcenter fit bold info">@lang('label.TO')</td>
                                                </tr>
                                                <?php
                                                $countryVisitData = !empty($countryVisitDataInfo) ? json_decode($countryVisitDataInfo->visit_info, true) : null;
                                                $srSlShow = 1;
                                                ?>
                                                @if(!empty($countryVisitData))
                                                @foreach($countryVisitData as $cKey => $countryInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center width-50">{{ $srSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($countryInfo['country_name']) ? $countryInfo['country_name']: ''}}</td>
                                                    <td class="vcenter">{{ !empty($countryInfo['from']) ? $countryInfo['from']: ''}}</td>
                                                    <td class="vcenter">{{ !empty($countryInfo['to']) ? $countryInfo['to']: ''}}</td>
                                                    <td class="vcenter">{{ !empty($countryInfo['reason']) ? $countryInfo['reason']: ''}}</td>
                                                </tr>
                                                <?php $srSlShow++; ?>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="5" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- End:: country visited Information-->

                        <!-- START:: key appt Info -->

                        <!-- End:: key appt Info -->

                        <div class="row"> 
                            <div class="col-md-5">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-calendar green-color-style-color"></i>@lang('label.KEY_APPT')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                @if(!empty($keyAppt))
                                                <?php $sl = 0; ?>
                                                <tr>
                                                    <td>
                                                        @foreach($keyAppt as $key => $appt)
                                                        {!! ++$sl !!}.&nbsp;{!! !empty($appt) ? $appt : '' !!}{!! '<br />' !!}
                                                        @endforeach
                                                    </td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- START:: User Others Info -->
                            <div class="col-md-7">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-cog green-color-style-color"></i> @lang('label.OTHERS')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.DECORATION_AWARD')</td>
                                                    <td class="vcenter fit bold info">@lang('label.HOBBY')</td>
                                                </tr>
                                                @if(!empty($othersInfoData))
                                                <tr>
                                                    <?php
                                                    $decArr = !empty($othersInfoData->decoration_id) ? explode(', ', $othersInfoData->decoration_id) : [];
                                                    $hobbyArr = !empty($othersInfoData->hobby_id) ? explode(',', $othersInfoData->hobby_id) : [];
                                                    ?>
                                                    <td>
                                                        @if(!empty($decArr))
                                                        <?php $sl = 0; ?>
                                                        @foreach($decArr as $dec)
                                                        {!! ++$sl !!}.&nbsp;{!! !empty($decorationList[$dec]) ? $decorationList[$dec] : (!empty($dec) ? $dec : '') !!}{!! '<br />' !!}
                                                        @endforeach
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if(!empty($hobbyArr))
                                                        <?php $sl = 0; ?>
                                                        @foreach($hobbyArr as $hobby)
                                                        {!! ++$sl !!}.&nbsp;{!! !empty($hobbyList[$hobby]) ? $hobbyList[$hobby] : (!empty($hobby) ? $hobby : '') !!}{!! '<br />' !!}
                                                        @endforeach
                                                        @endif

                                                    </td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td colspan="3" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END:: User Others Info -->



                    </div>
                    <!--tab_1_3-->
                    <div class = "tab-pane" id = "tab_2">
                        <div class = "row">
                            <div class = "col-md-3">
                                <ul class = "ver-inline-menu tabbable margin-bottom-10">
                                    <li class = "active">
                                        <a data-toggle = "tab" href = "#tab_2_1">
                                            <i class="fa fa-map-marker"></i> @lang('label.ADDRESS') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_2">
                                            <i class = "fa fa-life-ring"></i> @lang('label.MARITAL_INFO') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_3">
                                            <i class="fa fa-paper-plane"></i></i> @lang('label.PASSPORT_DETAILS') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_4">
                                            <i class="fa fa-bank"></i></i> @lang('label.BANK_INFO') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_5">
                                            <i class="fa fa-graduation-cap"></i> @lang('label.ACADEMIC_QUALIFICATION') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_6">
                                            <i class="fa fa-user"></i></i> @lang('label.MIL_QUALIFICATION') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_7">
                                            <i class="fa fa-cogs"></i> @lang('label.RECORD_OF_SERVICE') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_8">
                                            <i class="fa fa-globe"></i></i> @lang('label.UN_MSN') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_9">
                                            <i class="fa fa-plane"></i></i> @lang('label.COUNTRY_VISITED') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2_10">
                                            <i class="fa fa-cog"></i> @lang('label.OTHERS') </a>
                                    </li>
                                </ul>
                            </div>
                            <div class = "col-md-9">
                                <div class = "tab-content">
                                    <!--Start Edit User Permanent Address-->
                                    <div id="tab_2_1"  class = "tab-pane active">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_ADDRESS')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editUserPermanentAddressForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}
                                        <div class="row margin-top-10">

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-12 margin-bottom-10">
                                                        <strong>@lang('label.PRESENT_ADDRESS')</strong>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="divisionId">@lang('label.DIVISION') <span class="text-danger"> *</span></label>
                                                            {!! Form::select('present_division_id', $divisionList, !empty($presentAddressInfo->division_id)?$presentAddressInfo->division_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'divisionId']) !!}
                                                        </div>
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="districtId">@lang('label.DISTRICT') </label>
                                                            {!! Form::select('present_district_id', $presentDistrictList, !empty($presentAddressInfo->district_id)?$presentAddressInfo->district_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'districtId']) !!}
                                                        </div>
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="thanaId">@lang('label.THANA') </label>
                                                            {!! Form::select('present_thana_id', $presentThanaList, !empty($presentAddressInfo->thana_id)?$presentAddressInfo->thana_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'thanaId']) !!}
                                                        </div>
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="addressDetails">@lang('label.ADDRESS') (@lang('label.RESIDENCE'))</label>
                                                            {!! Form::text('present_address_details', !empty($presentAddressInfo->address_details)?$presentAddressInfo->address_details:null,  ['class' => 'form-control', 'id' => 'addressDetails']) !!}
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-12 margin-bottom-10 checkbox-center md-checkbox has-success">
                                                        <strong>@lang('label.PERMANENT_ADDRESS')</strong> (<span class="custom-check-mark">
                                                            <?php
                                                            $checked = !empty($addressInfo->same_as_present) ? 'checked' : '';
                                                            ?>
                                                            <input id="forPermanentAddr" class="md-check" name="for_addr" type="checkbox" {{$checked}} value="{{!empty($addressInfo->same_as_present) ? $addressInfo->same_as_present : null}}">
                                                            <label for="forPermanentAddr" class="course-member">
                                                                <span class="inc"></span>
                                                                <span class="check mark-caheck"></span>
                                                                <span class="box mark-caheck"></span>
                                                            </label>
                                                            <span class="text-green">@lang('label.SAME_AS_PRESENT_ADDRESS')</span>
                                                        </span>)
                                                    </div>
                                                </div>
                                                <div class="row permanent-address-block">
                                                    <div class="col-md-12">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="divisionId">@lang('label.DIVISION') <span class="text-danger"> *</span></label>
                                                            {!! Form::select('permanent_division_id', $divisionList, !empty($addressInfo->division_id)?$addressInfo->division_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'perDivisionId']) !!}
                                                        </div>
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="districtId">@lang('label.DISTRICT') </label>
                                                            {!! Form::select('permanent_district_id', $districtList, !empty($addressInfo->district_id)?$addressInfo->district_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'perDistrictId']) !!}
                                                        </div>
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="thanaId">@lang('label.THANA') </label>
                                                            {!! Form::select('permanent_thana_id', $thanaList, !empty($addressInfo->thana_id)?$addressInfo->thana_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'perThanaId']) !!}
                                                        </div>
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="addressDetails">@lang('label.ADDRESS')</label>
                                                            {!! Form::text('permanent_address_details', !empty($addressInfo->address_details)?$addressInfo->address_details:null,  ['class' => 'form-control', 'id' => 'perAddressDetails']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 permanent-address-present">
                                                    <div class="alert alert-success alert-dismissable">
                                                        <p><strong><i class="fa fa-map-marker"></i> {!! __('label.PERMANENT_ADDRESS_IS_SAME_AS_PRESENT_ADDRESS') !!}</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 margin-top-10 text-center">
                                                <a class = "btn  btn-circle green" id="editUserPermanentAddressButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}

                                    </div>
                                    <!--End Edit User Permanent Address-->

                                    <!--Start::change marital status -->
                                    <div id = "tab_2_2" class = "tab-pane">
                                        <div class="row margin-bottom-10">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_MARITAL_INFO')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editMaritialStatusForm']) !!}
                                        <div class="row">
                                            <div class="col-md-6">

                                                {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}
                                                <div class = "col-md-12">
                                                    <div class = "form-group">
                                                        <label class = "control-label" for="maritalStatus">@lang('label.MARITAL_STATUS') </label>
                                                        {!! Form::select('marital_status', $maritalStatusList, !empty($userInfoData->marital_status)?$userInfoData->marital_status:'0',  ['class' => 'form-control js-source-states', 'id' => 'maritalStatus']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="spouseInfoDiv">
                                                <div class = "col-md-12">
                                                    <div class = "col-md-6 col-sm-6">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseName">@lang('label.SPOUSE_NAME')<span class="text-danger"> *</span> </label>
                                                            {!! Form::text('spouse_name', !empty($userInfoData->spouse_name)?$userInfoData->spouse_name:null, ['id'=> 'spouseName', 'class' => 'form-control']) !!} 
                                                        </div>
                                                    </div>
                                                    <div class = "col-md-6 col-sm-6">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="marriageDate">@lang('label.DATE_OF_MARRIAGE')<span class="text-danger"> *</span></label>
                                                            <div class="input-group date datepicker2">
                                                                {!! Form::text('date_of_marriage', !empty($userInfoData->date_of_marriage)?Helper::formatDate($userInfoData->date_of_marriage):'', ['id'=> 'marriageDate', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                <span class="input-group-btn">
                                                                    <button class="btn default reset-date" type="button" remove="marriageDate">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                    <button class="btn default date-set" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class = "col-md-6 col-sm-6">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseNickName">@lang('label.NICK_NAME')<span class="text-danger"> *</span> </label>
                                                            {!! Form::text('spouse_nick_name', !empty($userInfoData->spouse_name) ? $userInfoData->spouse_name : null, ['id'=> 'spouseNickName', 'class' => 'form-control']) !!} 
                                                        </div>
                                                    </div>

                                                    <div class = "col-md-6 col-sm-6">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseDob">@lang('label.DATE_OF_BIRTH')<span class="text-danger"> *</span></label>
                                                            <div class="input-group date datepicker2">
                                                                {!! Form::text('spouse_date_of_birth', !empty($userInfoData->spouse_dob)?Helper::formatDate($userInfoData->spouse_dob):'', ['id'=> 'spouseDob', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                <span class="input-group-btn">
                                                                    <button class="btn default reset-date" type="button" remove="spouseDob">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                    <button class="btn default date-set" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class = "col-md-6 col-sm-6">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseMobile">@lang('label.PHONE')</label>
                                                            {!! Form::text('spouse_mobile', !empty($userInfoData->spouse_mobile)?$userInfoData->spouse_mobile : null, ['id'=> 'spouseMobile', 'class' => 'form-control']) !!}
                                                        </div>
                                                    </div>
                                                    <div class = "col-md-6 col-sm-6">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseOccupation">@lang('label.PROFESSION')</label>
                                                            {!! Form::text('spouse_occupation', !empty($userInfoData->spouse_occupation)?$userInfoData->spouse_occupation:null, ['id'=> 'spouseOccupation', 'class' => 'form-control']) !!}
                                                        </div>
                                                    </div>
                                                    <div class = "col-md-12">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseWorkPlace">@lang('label.WORK_ADDRESS')</label>
                                                            {!! Form::textarea('spouse_work_address', !empty($userInfoData->spouse_work_address) ? $userInfoData->spouse_work_address : null, ['id'=> 'spouseWorkPlace', 'class' => 'form-control','cols'=>'12','rows'=>'2']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="numberOfChild" class="col-md-12">
                                                <div class = "col-md-6">
                                                    <div class="row">
                                                        <div class = "form-group">
                                                            <label class = "control-label col-md-3" for="noOfChild">@lang('label.NO_OF_CHILDREN')</label>
                                                            <div class="col-md-9">
                                                                {!! Form::text('no_of_child', !empty($childInfoData->no_of_child) ? $childInfoData->no_of_child : '', ['id'=> 'noOfChild', 'class' => 'form-control integer-only text-inherit']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="childInfo">
                                                @if(!empty($childInfoData))
                                                <?php
                                                $childInfo = !empty($childInfoData) ? json_decode($childInfoData->user_child_info, true) : null;
                                                $cSl = 0;
                                                ?>
                                                @if(!empty($childInfo))
                                                <div class="col-md-12 margin-top-10">
                                                    <div class="table-responsive" id="winterTrainingRowAdd">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr class="info">
                                                                    <th scope="col" class="vcenter text-center">@lang('label.SERIAL') </th>
                                                                    <th scope="col" class="vcenter">@lang('label.NAME') <span class="text-danger"> *</span></th>
                                                                    <th scope="col" class="vcenter text-center">@lang('label.DATE_OF_BIRTH') <span class="text-danger"> *</span></th>
                                                                    <th scope="col" class="vcenter">@lang('label.SCHOOL_PROFESSION')</th>
                                                                </tr>
                                                            </thead>
                                                            @foreach($childInfo as $cKey => $child)
                                                            <tbody>
                                                            <td class="vcenter text-center">{{ ++$cSl }}</td>
                                                            <td class="vcenter">
                                                                {!! Form::text('child['.$cKey.'][name]', !empty($child['name']) ? $child['name'] : '',  ['class' => 'form-control', 'id' => 'childName'.$cKey]) !!}
                                                            </td>
                                                            <td class="vcenter text-right">
                                                                <div class="input-group date datepicker2">
                                                                    {!! Form::text('child['.$cKey.'][dob]', !empty($child['dob']) ? Helper::formatDate($child['dob']) : '', ['id'=> 'childDob'.$cKey, 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                    <span class="input-group-btn">
                                                                        <button class="btn default reset-date" type="button" remove="childDob{{$cKey}}">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                        <button class="btn default date-set" type="button">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                                </div>

                                                            </td>
                                                            <td class="vcenter">
                                                                {!! Form::text('child['.$cKey.'][school]', !empty($child['school']) ? $child['school'] : '',  ['class' => 'form-control', 'id' => 'childSchool'.$cKey]) !!}
                                                            </td>

                                                            </tbody>
                                                            @endforeach
                                                        </table>
                                                    </div>
                                                </div>
                                                @endif
                                                @endif
                                            </div>


                                            <div class = "col-md-12 margin-top-10 text-center">
                                                <a type="button" class = "btn  btn-circle green" id="editMaritialStatusButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}

                                    </div>

                                    <!--end::change marital status -->

                                    <!--Start::User medical details -->
                                    <div id="tab_2_3" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_PASSPORT_DETAILS')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editUserMedicalDetailsForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}
                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="col-md-6 col-sm-6">
                                                    <div class = "form-group">
                                                        <label class = "control-label" for="passportNo">@lang('label.PASSPORT_NO') <span class="text-danger"> *</span></label>
                                                        {!! Form::text('passport_no', !empty($passportInfoData->passport_no) ? $passportInfoData->passport_no : null, ['id'=> 'passportNo', 'class' => 'form-control']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <div class = "form-group">
                                                        <label class = "control-label" for="dateOfIssue">@lang('label.DATE_OF_ISSUE') <span class="text-danger"> *</span></label>
                                                        <div class="input-group date datepicker2">
                                                            {!! Form::text('date_of_issue', !empty($passportInfoData->date_of_issue) ? Helper::formatDate($passportInfoData->date_of_issue) : null, ['id'=> 'dateOfIssue', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY']) !!}
                                                            <span class="input-group-btn">
                                                                <button class="btn default reset-date" type="button" remove="dateOfIssue">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                                <button class="btn default date-set" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <div class = "form-group">
                                                        <label class = "control-label" for="placeOfIssue">@lang('label.PLACE_OF_ISSUE') <span class="text-danger"> *</span></label>
                                                        {!! Form::text('place_of_issue', !empty($passportInfoData->place_of_issue) ? $passportInfoData->place_of_issue : null, ['id'=> 'placeOfIssue', 'class' => 'form-control']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <div class = "form-group">
                                                        <label class = "control-label" for="dateOfExpiry">@lang('label.DATE_OF_EXPIRY') <span class="text-danger"> *</span></label>
                                                        <div class="input-group date datepicker2">
                                                            {!! Form::text('date_of_expire', !empty($passportInfoData->date_of_expire) ? Helper::formatDate($passportInfoData->date_of_expire) : null, ['id'=> 'dateOfExpiry', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY']) !!}
                                                            <span class="input-group-btn">
                                                                <button class="btn default reset-date" type="button" remove="dateOfExpiry">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                                <button class="btn default date-set" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class = "col-md-12 margin-top-10 text-center">
                                                    <a class = "btn  btn-circle green" id="editUserMedicalDetailsButton"> @lang('label.SAVE_CHANGES') </a>
                                                    <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}

                                    </div>
                                    <!--End::User medical details -->
                                    <!--START::User award Record-->
                                    <div id="tab_2_4" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_BANK_INFO')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editUserBankForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center width-50">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.BANK_NAME') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.BRANCH') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.ACCT_NO') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.ONLINE')</th>
                                                                <th scope="col" class="vcenter text-center"></th>
                                                            </tr> 
                                                        </thead>
                                                        <?php
                                                        $aSl = 1;
                                                        $aKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $bankData = !empty($bankInfoData) ? json_decode($bankInfoData->bank_info, true) : null;
                                                        //echo '<pre>';        print_r(json_decode($serviceRecordInfoData->service_record_info, true));exit;
                                                        ?>
                                                        @if(!empty($bankData))
                                                        @foreach($bankData as $bKey => $bankInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-bank-sl text">{{ $aSl }}</td>
                                                                <td class="vcenter width-180">
                                                                    {!! Form::text('bank['.$bKey.'][name]', !empty($bankInfo['name'])?$bankInfo['name']:null,  ['class' => 'form-control width-inherit', 'id' => 'name'.$bKey]) !!}
                                                                </td>
                                                                <td class="vcenter width-180">
                                                                    {!! Form::text('bank['.$bKey.'][branch]',!empty($bankInfo['branch'])?$bankInfo['branch']:null,  ['class' => 'form-control width-inherit', 'id' => 'branch'.$bKey]) !!}
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('bank['.$bKey.'][account]', !empty($bankInfo['account'])?$bankInfo['account']:null, ['id'=> 'acct'.$bKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter width-85">
                                                                    <div class="for-present-svc-block checkbox-center md-checkbox has-success">
                                                                        <?php
                                                                        $onlineCheck = !empty($bankInfo['is_online']) ? 'checked' : '';
                                                                        ?>
                                                                        <input id="forOnline{{$bKey}}" class="md-check" name="bank[{{$bKey}}][is_online]" type="checkbox" {{$onlineCheck}} value="1">
                                                                        <label for="forOnline{{$bKey}}" class="course-member">
                                                                            <span class="inc"></span>
                                                                            <span class="check mark-caheck"></span>
                                                                            <span class="box mark-caheck"></span>
                                                                        </label>&nbsp;&nbsp;
                                                                        <span class="text-green">@lang('label.YES')</span>
                                                                    </div>
                                                                </td>

                                                                <td class="vcenter text-center width-50">
                                                                    @if($aSl == 1)
                                                                    <a class="btn label-green-seagreen bank-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn label-red-intense bank-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                        <?php $aSl++; ?>
                                                        @endforeach
                                                        @else
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-bank-sl">1</td>
                                                                <td class="vcenter width-180">
                                                                    {!! Form::text('bank['.$aKey.'][name]', null,  ['class' => 'form-control width-inherit', 'id' => 'name'.$aKey]) !!}
                                                                </td>
                                                                <td class="vcenter width-180">
                                                                    {!! Form::text('bank['.$aKey.'][branch]', null,  ['class' => 'form-control width-inherit', 'id' => 'branch'.$aKey]) !!}
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('bank['.$aKey.'][account]', null, ['id'=> 'acct'.$aKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter width-85">
                                                                    <div class="for-present-svc-block checkbox-center md-checkbox has-success">
                                                                        <input id="forOnline{{$aKey}}" class="md-check" name="bank[{{$aKey}}][is_online]" type="checkbox" value="1">
                                                                        <label for="forOnline{{$aKey}}" class="course-member">
                                                                            <span class="inc"></span>
                                                                            <span class="check mark-caheck"></span>
                                                                            <span class="box mark-caheck"></span>
                                                                        </label>&nbsp;&nbsp;
                                                                        <span class="text-green">@lang('label.YES')</span>
                                                                    </div>
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn label-green-seagreen bank-add-btn" id="1" type="button" />
                                                                    <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif
                                                        <tbody  id="newBankRow">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 margin-top-10 text-center">
                                                <a class = "btn  btn-circle green" id="editUserBankButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::User Award Record-->

                                    <!--START::User Civil Education-->
                                    <div id="tab_2_5" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_ACADEMIC_QUALIFICATION')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editUserCivilEducationForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12 table-responsive">
                                                <div class="max-height-500 webkit-scrollbar">
                                                    <table class="table table-bordered table-hover table-head-fixer-color">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.INSTITUTE_NAME') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.EXAMINATION') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.FROM') </th>
                                                                <th scope="col" class="vcenter">@lang('label.TO') </th>
                                                                <th scope="col" class="vcenter">@lang('label.QUAL_ERODE')</th>
                                                                <th scope="col" class="vcenter text-center"></th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $cSl = 1;
                                                        $ceKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $civilEducation = !empty($civilEducationInfoData) ? json_decode($civilEducationInfoData->civil_education_info, true) : null;
                                                        //echo '<pre>';        print_r($brotherSister);exit;
                                                        ?>
                                                        @if(!empty($civilEducation))
                                                        @foreach($civilEducation as $ceVar => $civilEducationInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-civil-education-sl">{{ $cSl }}</td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('academic_qual['.$ceVar.'][institute_name]', $civilEducation[$ceVar]['institute_name'] ?? '', ['id'=> 'inst'.$ceVar, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter width-180">
                                                                    {!! Form::text('academic_qual['.$ceVar.'][examination]', $civilEducation[$ceVar]['examination'] ?? '', ['id'=> 'exam'.$ceVar, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('academic_qual['.$ceVar.'][from]', $civilEducation[$ceVar]['from'] ?? '', ['id'=> 'academicQualFrom'.$ceVar, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('academic_qual['.$ceVar.'][to]', $civilEducation[$ceVar]['to'] ?? '', ['id'=> 'academicQualTo'.$ceVar, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-85">
                                                                    {!! Form::text('academic_qual['.$ceVar.'][qual_erode]', $civilEducation[$ceVar]['qual_erode'] ?? '', ['id'=> 'qualErode'.$ceVar, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                @if($cSl == 1)
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn label-green-seagreen civil-education-add-btn" id="" type="button" ><i class="fa fa-plus"></i></a>
                                                                </td>
                                                                @else
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn label-red-intense civil-education-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
                                                                </td>
                                                                @endif
                                                            </tr>
                                                        </tbody>
                                                        <?php $cSl++; ?>
                                                        @endforeach
                                                        @else
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-civil-education-sl">1</td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('academic_qual['.$ceKey.'][institute_name]', null, ['id'=> 'inst'.$ceKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter width-180">
                                                                    {!! Form::text('academic_qual['.$ceKey.'][examination]', null, ['id'=> 'exm'.$ceKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('academic_qual['.$ceKey.'][from]', '', ['id'=> 'academicQualFrom'.$ceKey, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('academic_qual['.$ceKey.'][to]', '', ['id'=> 'academicQualTo'.$ceKey, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-85">
                                                                    {!! Form::text('academic_qual['.$ceKey.'][qual_erode]', null, ['id'=> 'qualErode'.$ceKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn label-green-seagreen civil-education-add-btn" id="1" type="button" />
                                                                    <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif
                                                        <tbody  id="civilEducationInputRow">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 text-center margin-top-10">
                                                <a class = "btn  btn-circle green" id="editUserCivilEducationButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::User Civil Education-->




                                    <!--START::User Defence Relative-->
                                    <div id="tab_2_6" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_MIL_QUALIFICATION')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editUserDefenceRelativeForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12 table-responsive">
                                                <div class="max-height-500 webkit-scrollbar">
                                                    <table class="table table-bordered table-hover table-head-fixer-color">
                                                        <thead>
                                                            <tr class="info">

                                                                <th scope="col" class="vcenter text-center width-50">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.INSTITUTE_N_COUNTRY') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.COURSE_TRG') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.FROM') </th>
                                                                <th scope="col" class="vcenter">@lang('label.TO') </th>
                                                                <th scope="col" class="vcenter">@lang('label.Result') <span class="text-danger"> *</th>
                                                                <th scope="col" class="vcenter text-center"></th>
                                                            </tr> 
                                                        </thead>
                                                        <?php
                                                        $dSl = 1;
                                                        $drKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $defenceRelative = !empty($defenceRelativeInfoData) ? json_decode($defenceRelativeInfoData->user_relative_info, true) : null;
                                                        //echo '<pre>';        print_r(json_decode($punishmentRecordInfoData->punishment_record_info, true));exit;
                                                        ?>
                                                        @if(!empty($defenceRelative))
                                                        @foreach($defenceRelative as $drVar => $defenceRelativeInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-defence-relative-sl text">{{ $dSl }}</td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('mil_qual['.$drVar.'][institute_name]', $defenceRelativeInfo['institute_name'] ?? '', ['id'=> 'inst'.$drVar, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    <div class="width-inherit">
                                                                        {!! Form::select('mil_qual['.$drVar.'][course]',$milCourseList, $defenceRelativeInfo['course'] ?? '', ['id'=> 'course'.$drVar, 'class' => 'form-control js-source-states text-width-100-per']) !!}
                                                                    </div>
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('mil_qual['.$drVar.'][from]', !empty($defenceRelativeInfo['from']) ? $defenceRelativeInfo['from'] : '', 
                                                                    ['id'=> 'milQualFrom'.$drVar, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('mil_qual['.$drVar.'][to]', !empty($defenceRelativeInfo['to']) ? $defenceRelativeInfo['to'] : '', 
                                                                    ['id'=> 'milQualTo'.$drVar, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-85">
                                                                    {!! Form::text('mil_qual['.$drVar.'][result]', $defenceRelativeInfo['result'] ?? '', ['id'=> 'qualErode'.$drVar, 'class' => 'form-control width-inherit']) !!}
                                                                </td>

                                                                <td class="vcenter text-center width-50">
                                                                    @if($dSl == 1)
                                                                    <a class="btn label-green-seagreen defence-relative-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn label-red-intense defence-relative-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                        <?php $dSl++; ?>
                                                        @endforeach
                                                        @else
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-defence-relative-sl">1</td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('mil_qual['.$drKey.'][institute_name]', '', ['id'=> 'inst'.$drKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    <div class="width-inherit">
                                                                        {!! Form::select('mil_qual['.$drKey.'][course]', $milCourseList,null, ['id'=> 'course'.$drKey, 'class' => 'form-control js-source-states text-width-100-per']) !!}
                                                                    </div>
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('mil_qual['.$drKey.'][from]', '', ['id'=> 'milQualFrom'.$drKey, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('mil_qual['.$drKey.'][to]', '', ['id'=> 'milQualTo'.$drKey, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-85">
                                                                    {!! Form::text('mil_qual['.$drKey.'][result]', '', ['id'=> 'qualErode'.$drKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn label-green-seagreen defence-relative-add-btn" id="1" type="button" />
                                                                    <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif
                                                        <tbody  id="defenceRelativeInputRow">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 margin-top-10 text-center">
                                                <a class = "btn  btn-circle green" id="editUserDefenceRelativeButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::User Defence Relative-->
                                    <!--START::User service Record-->
                                    <div id="tab_2_7" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_RECORD_OF_SERVICE')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editUserServiceRecordForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive webkit-scrollbar">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center width-50">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.FROM') </th>
                                                                <th scope="col" class="vcenter">@lang('label.TO') </th>
                                                                <th scope="col" class="vcenter">@lang('label.UNIT_FMN_INST') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.APPOINTMENT') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center"></th>
                                                            </tr> 
                                                        </thead>
                                                        <?php
                                                        $sSl = 1;
                                                        $srKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $serviceRecord = !empty($serviceRecordInfoData) ? json_decode($serviceRecordInfoData->service_record_info, true) : null;
                                                        //echo '<pre>';        print_r(json_decode($serviceRecordInfoData->service_record_info, true));exit;
                                                        ?>
                                                        @if(!empty($serviceRecord))
                                                        @foreach($serviceRecord as $srVar => $serviceRecordInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-service-record-sl text">{{ $sSl }}</td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('service_record['.$srVar.'][from]',!empty($serviceRecordInfo['from'])?$serviceRecordInfo['from']:''
                                                                    , ['id'=> 'serviceRecordFrom'.$srVar, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('service_record['.$srVar.'][to]',!empty($serviceRecordInfo['to'])?$serviceRecordInfo['to']:''
                                                                    , ['id'=> 'serviceRecordTo'.$srVar, 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <?php
                                                                $unitFmnInst = (!empty($serviceRecordInfo['unit_fmn_inst']) && $serviceRecordInfo['unit_fmn_inst']) != 0 ? (!empty($organizationList[$serviceRecordInfo['unit_fmn_inst']]) ? $organizationList[$serviceRecordInfo['unit_fmn_inst']] : $serviceRecordInfo['unit_fmn_inst']) : '';
                                                                $appointment = (!empty($serviceRecordInfo['appointment']) && $serviceRecordInfo['appointment']) != 0 ? (!empty($allAppointmentList[$serviceRecordInfo['appointment']]) ? $allAppointmentList[$serviceRecordInfo['appointment']] : $serviceRecordInfo['appointment']) : '';
                                                                ?>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('service_record['.$srVar.'][unit_fmn_inst]', $unitFmnInst, ['class' => 'form-control width-inherit', 'id' => 'unitFmnInst'.$srVar]) !!}
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('service_record['.$srVar.'][appointment]', $appointment, ['class' => 'form-control width-inherit', 'id' => 'svcAppointment'.$srVar]) !!}
                                                                </td>

                                                                <td class="vcenter text-center width-50 text-center">
                                                                    @if($sSl == 1)
                                                                    <a class="btn label-green-seagreen service-record-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn label-red-intense service-record-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                        <?php $sSl++; ?>
                                                        @endforeach
                                                        @else
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-service-record-sl">1</td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('service_record['.$srKey.'][from]','', ['id'=> 'serviceRecordFrom'.$srKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter  width-210">
                                                                    {!! Form::text('service_record['.$srKey.'][to]','', ['id'=> 'serviceRecordTo'.$srKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('service_record['.$srKey.'][unit_fmn_inst]', null, ['class' => 'form-control width-inherit', 'id' => 'unitFmnInst'.$srKey]) !!}
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('service_record['.$srKey.'][appointment]', null, ['class' => 'form-control width-inherit', 'id' => 'svcAppointment'.$srKey]) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn label-green-seagreen service-record-add-btn" id="1" type="button" />
                                                                    <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif
                                                        <tbody  id="serviceRecordInputRow">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 margin-top-10 text-center">
                                                <a class = "btn  btn-circle green" id="editUserServiceRecordButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::User Service Record-->
                                    <!--START::User punishment Record-->
                                    <div id="tab_2_8" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_UN_MSN')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editUserPunishmentRecordForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive webkit-scrollbar">
                                                    <table class="table table-bordered table-hover table-head-fixer-color">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center" rowspan="2">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter text-center" colspan="2">@lang('label.DURATION') </th>
                                                                <th scope="col" class="vcenter" rowspan="2">@lang('label.MSN') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter" rowspan="2">@lang('label.APPT') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter" rowspan="2">@lang('label.REMARKS')</th>
                                                                <th scope="col" class="vcenter text-center" rowspan="2"></th>
                                                            </tr>
                                                            <tr>
                                                                <th scope="col" class="vcenter text-center">@lang('label.FROM')</th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.TO')</th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $pSl = 1;
                                                        $prKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $msnData = !empty($msnDataInfo) ? json_decode($msnDataInfo->msn_info, true) : null;
                                                        //echo '<pre>';        print_r(json_decode($punishmentRecordInfoData->punishment_record_info, true));exit;
                                                        ?>
                                                        @if(!empty($msnData))
                                                        @foreach($msnData as $msnKey => $msnInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-punishment-record-sl text">{{ $pSl }}</td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('un_msn['.$msnKey.'][from]',!empty($msnInfo['from']) ? $msnInfo['from'] : '', ['id'=> 'unMsnFrom'.$msnKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('un_msn['.$msnKey.'][to]',!empty($msnInfo['to']) ? $msnInfo['to'] : '', ['id'=> 'unMsnTo'.$msnKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('un_msn['.$msnKey.'][msn]', !empty($msnInfo['msn']) ? $msnInfo['msn'] : '',  ['class' => 'form-control width-inherit', 'id' => 'unMsn'.$msnKey]) !!} 
                                                                </td>
                                                                <?php
                                                                $appointment = (!empty($msnInfo['appointment']) && $msnInfo['appointment']) != 0 ? (!empty($allAppointmentList[$msnInfo['appointment']]) ? $allAppointmentList[$msnInfo['appointment']] : $msnInfo['appointment']) : '';
                                                                ?>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('un_msn['.$msnKey.'][appointment]', $appointment,  ['class' => 'form-control width-inherit', 'id' => 'unAppt'.$msnKey]) !!}
                                                                </td>
                                                                <td class="vcenter width-150">
                                                                    {!! Form::text('un_msn['.$msnKey.'][remark]', !empty($msnInfo['remark']) ? $msnInfo['remark'] : '', ['id'=> 'remark'.$msnKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>

                                                                <td class="vcenter text-center width-50">
                                                                    @if($pSl == 1)
                                                                    <a class="btn label-green-seagreen un-msn-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn label-red-intense punishment-record-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                        <?php $pSl++; ?>
                                                        @endforeach
                                                        @else
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-punishment-record-sl">1</td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('un_msn['.$prKey.'][from]','', ['id'=> 'unMsnFrom'.$prKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('un_msn['.$prKey.'][to]','', ['id'=> 'unMsnTo'.$prKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('un_msn['.$prKey.'][msn]', null,  ['class' => 'form-control width-inherit', 'id' => 'unMsn'.$prKey]) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('un_msn['.$prKey.'][appointment]', null,  ['class' => 'form-control width-inherit', 'id' => 'unAppt'.$prKey]) !!}
                                                                </td>
                                                                <td class="vcenter width-150">
                                                                    {!! Form::text('un_msn['.$prKey.'][remark]', null, ['id'=> 'remark'.$prKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn label-green-seagreen un-msn-add-btn" id="1" type="button" />
                                                                    <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif
                                                        <tbody  id="punishmentRecordInputRow">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 margin-top-10 text-center">
                                                <a class = "btn  btn-circle green" id="editUserPunishmentRecordButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::User Punshment Record-->
                                    <!--START::User punishment Record-->
                                    <div id="tab_2_9" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_COUNTRY_VISITED')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editUserCountryVisitedForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive webkit-scrollbar">
                                                    <table class="table table-bordered table-hover table-head-fixer-color">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center" rowspan="2">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter" rowspan="2">@lang('label.NAME_OF_COUNTRY') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center" colspan="2">@lang('label.DURATION') </th>
                                                                <th scope="col" class="vcenter" rowspan="2">@lang('label.REASONS_FOR_VISIT') </th>
                                                                <th scope="col" class="vcenter text-center" rowspan="2"></th>
                                                            </tr>
                                                            <tr>
                                                                <th scope="col" class="vcenter">@lang('label.FROM')</th>
                                                                <th scope="col" class="vcenter">@lang('label.TO')</th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $pSl = 1;
                                                        $crKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $countryVisitData = !empty($countryVisitDataInfo) ? json_decode($countryVisitDataInfo->visit_info, true) : null;
                                                        //echo '<pre>';        print_r(json_decode($punishmentRecordInfoData->punishment_record_info, true));exit;
                                                        ?>
                                                        @if(!empty($countryVisitData))
                                                        @foreach($countryVisitData as $visitKey => $visitInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-country-visit-sl text">{{ $pSl }}</td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('country_visit['.$visitKey.'][country_name]', !empty($visitInfo['country_name']) ? $visitInfo['country_name'] : '',  ['class' => 'form-control width-inherit', 'id' => 'country'.$visitKey]) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('country_visit['.$visitKey.'][from]',!empty($visitInfo['from']) ? $visitInfo['from'] : '', ['id'=> 'visitFrom'.$visitKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('country_visit['.$visitKey.'][to]',!empty($visitInfo['to']) ? $visitInfo['to'] : '', ['id'=> 'visitTo'.$visitKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-150">
                                                                    {!! Form::text('country_visit['.$visitKey.'][reason]', !empty($visitInfo['reason']) ? $visitInfo['reason'] : '', ['id'=> 'reason'.$visitKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>

                                                                <td class="vcenter text-center width-50">
                                                                    @if($pSl == 1)
                                                                    <a class="btn label-green-seagreen country-visit-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn label-red-intense country-visit-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                        <?php $pSl++; ?>
                                                        @endforeach
                                                        @else
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-country-visit-sl">1</td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('country_visit['.$crKey.'][country_name]', '',  ['class' => 'form-control width-inherit', 'id' => 'country'.$crKey]) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('country_visit['.$crKey.'][from]','', ['id'=> 'visitFrom'.$crKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>
                                                                <td class="vcenter width-210">
                                                                    {!! Form::text('country_visit['.$crKey.'][to]','', ['id'=> 'visitTo'.$crKey
                                                                    , 'class' => 'form-control width-inherit', 'placeholder' => 'DD/MM/YYYY']) !!} 
                                                                </td>

                                                                <td class="vcenter width-150">
                                                                    {!! Form::text('country_visit['.$crKey.'][reason]','', ['id'=> 'reason'.$crKey, 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn label-green-seagreen country-visit-add-btn" id="1" type="button" />
                                                                    <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif
                                                        <tbody  id="newCountryRow">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 margin-top-10 text-center">
                                                <a class = "btn  btn-circle green" id="editUserCountryVisitedButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::User Punshment Record-->


                                    <!--Start:: User others-->
                                    <div id="tab_2_10" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_OTHERS')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editUserOthersForm']) !!}
                                        {!! Form::hidden('user_basic_profile_id', $userInfoData->user_basic_profile_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-6">
                                                <div class = "form-group">
                                                    @php
                                                    $decoration =!empty($othersInfoData->decoration_id)? explode(',', $othersInfoData->decoration_id):[];
                                                    @endphp
                                                    <label class = "control-label" for="decorationId">@lang('label.DECORATION_AWARD')</label>
                                                    {!! Form::select('decoration_id[]', $decorationList, $decoration,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'decorationId']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    @php
                                                    $hobby =!empty($othersInfoData->hobby_id)? explode(',', $othersInfoData->hobby_id):[];
                                                    @endphp
                                                    <label class = "control-label" for="hobbyId">@lang('label.HOBBY')</label>
                                                    {!! Form::select('hobby_id[]', $hobbyList, $hobby,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'hobbyId']) !!}
                                                </div>

                                            </div>

                                        </div>

                                        <div class = "col-md-12 margin-top-10">
                                            <a class = "btn  btn-circle green" id="editUserOthersButton"> @lang('label.SAVE_CHANGES') </a>
                                            <a href = "{{ URL::to('user/'.$userInfoData->users_id.'/profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                        </div>
                                        {!! Form::close() !!}

                                    </div>
                                    <!--End:: User others-->

                                </div>
                            </div>
                        </div>
                        <!--end tab-pane-->
                    </div>
                    <!--end tab_1_3-->
                </div>
            </div>
        </div>
    </div>

    <script type = "text/javascript">
        $(function () {

            $(".table-head-fixer-color").tableHeadFixer();
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            if ($('#maritalStatus option:selected').val() != '1') {
                $('#spouseInfoDiv').hide();
            } else {
                $('#spouseInfoDiv').show();
            }
            //AJAX Header for csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //START:: Family Information
            $(document).on('click', '#editFamilyInfoButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editFamilyInfoForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateFamilyInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //END:: Marital Information

            //START:: MARITIAL STATUS

            //hide numberOfChild initially
            if ($.inArray($('#maritalStatus').val(), ['0', '2']) > -1) {
                $('#numberOfChild').hide();
                $('#childInfo').hide();
            }

            //Start:: show hide spouse div
            $(document).on('change', "#maritalStatus", function (e) {
                e.preventDefault();
                var statusVal = $(this).val();
                if (statusVal == '1') {
                    $('#spouseInfoDiv').show();
                } else {
                    $('#spouseInfoDiv').hide();
                }
                if (statusVal == 0) {
                    $('#numberOfChild').hide();
                    $('#childInfo').hide();
                    return false;
                }
                if ($.inArray(statusVal, ['0', '2']) > -1) {
                    $('#numberOfChild').hide();
                    $('#childInfo').hide();
                } else {
                    $('#numberOfChild').show();
                    $('#childInfo').show();
                }

            });
            //END:: show hide spouse div

            //Start:: Edit spouse info
            $(document).on('click', '#editMaritialStatusButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editMaritialStatusForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateMaritalStatus') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //End:: Edit spouse info

            //END:: MARITIAL STATUS

            //Start:: Multiple row create for brother/sister

            //When '+' button clicked
            $(document).on('click', ".add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForBrotherSister') }}",
                    success: function (result) {
                        $('#brotherSisterInputRow').append(result.html);
                        recalcSL('brother-sister');
                    }
                });
            });
            //Once remove button is clicked
            $(document).on('click', '.remove-Btn', function (e) {

                //            var button_id = $(this).attr("id");
                //            $("#remove" + button_id + "").remove();
                $(this).parent().parent().remove();
                recalcSL('brother-sister');
            });
            //Start:: Edit user others info
            $(document).on('click', '#editUserOthersButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserOthersForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateUserOthersInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //End:: Edit user others info

            //Start:: Multiselect decorations
            var decorationAllSelected = false;
            $('#decorationId').multiselect({
                numberDisplayed: 0,
                includeSelectAllOption: true,
                buttonWidth: '100%',
                maxHeight: 250,
                nonSelectedText: "@lang('label.SELECT_DECORATION')",
                //        enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                onSelectAll: function () {
                    decorationAllSelected = true;
                },
                onChange: function () {
                    decorationAllSelected = false;
                }
            });
            //End:: Multiselect decorations
            //Start:: Multiselect awards
            var awardAllSelected = false;
            $('#awardId').multiselect({
                numberDisplayed: 0,
                includeSelectAllOption: true,
                buttonWidth: '100%',
                maxHeight: 250,
                nonSelectedText: "@lang('label.SELECT_AWARD')",
                //        enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                onSelectAll: function () {
                    awardAllSelected = true;
                },
                onChange: function () {
                    awardAllSelected = false;
                }
            });
            //End:: Multiselect awards
            //Start:: Multiselect hobbies
            var hobbyAllSelected = false;
            $('#hobbyId').multiselect({
                numberDisplayed: 0,
                includeSelectAllOption: true,
                buttonWidth: '100%',
                maxHeight: 250,
                nonSelectedText: "@lang('label.SELECT_HOBBY')",
                //        enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                onSelectAll: function () {
                    hobbyAllSelected = true;
                },
                onChange: function () {
                    hobbyAllSelected = false;
                }
            });
            //End:: Multiselect hobbies

            //Start:: Division District Thana for user

            //GET district when click division
            $(document).on('change', '#divisionId', function () {
                var divisionId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('user/getDistrict')}}",
                    data: {
                        division_id: divisionId
                    },
                    success: function (result) {
                        $('#districtId').html(result.html);
                        $('#thanaId').html(result.htmlThana);
                    }
                });
            });
            $(document).on('change', '#perDivisionId', function () {
                var divisionId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('user/getDistrict')}}",
                    data: {
                        division_id: divisionId
                    },
                    success: function (result) {
                        $('#perDistrictId').html(result.html);
                        $('#perThanaId').html(result.htmlThana);
                    }
                });
            });
            //GET thana when click district
            $(document).on('change', '#districtId', function () {
                var districtId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('user/getThana')}}",
                    data: {
                        district_id: districtId
                    },
                    success: function (result) {
                        $('#thanaId').html(result.html);
                    }
                });
            });
            $(document).on('change', '#perDistrictId', function () {
                var districtId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('user/getThana')}}",
                    data: {
                        district_id: districtId
                    },
                    success: function (result) {
                        $('#perThanaId').html(result.html);
                    }
                });
            });
            //End:: Division District Thana 

            //START:: Edit permanent address
            $(document).on('click', '#editUserPermanentAddressButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserPermanentAddressForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updatePermanentAddress') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value[0] + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //END:: Edit permanent address

            //Start::Civil education row add
            // //When '+' button clicked
            $(document).on('click', ".civil-education-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForCivilEducation') }}",
                    success: function (result) {
                        $('#civilEducationInputRow').append(result.html);
                        recalcSL('civil-education');
                    }
                });
            });
            //Once remove button is clicked
            $(document).on('click', '.civil-education-remove-Btn', function (e) {

                //            var button_id = $(this).attr("id");
                //            $("#remove" + button_id + "").remove();
                $(this).parent().parent().remove();
                recalcSL('civil-education');
            });
            //End::Civil education row add
            //
            //Start::Edit civil education  
            $(document).on('click', '#editUserCivilEducationButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserCivilEducationForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateCivilEducationInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //End::Edit civil education 

            //Start::User Service Record 
            //Start:: Multiple row create for service record

            //When '+' button clicked
            $(document).on('click', ".service-record-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForServiceRecord') }}",
                    success: function (result) {
                        $('#serviceRecordInputRow').append(result.html);
                        recalcSL('service-record');
                    }
                });
            });
            //Once remove button is clicked
            $(document).on('click', '.service-record-remove-Btn', function (e) {

                //            var button_id = $(this).attr("id");
                //            $("#remove" + button_id + "").remove();
                $(this).parent().parent().remove();
                recalcSL('service-record');
            });
            //End:: Multiple row create for service record
            //Start::Edit service record 
            $(document).on('click', '#editUserServiceRecordButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserServiceRecordForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateServiceRecordInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //End::User Service Record 

            //Start::User Award Record 
            //Start:: Multiple row create for award record

            //When '+' button clicked
            $(document).on('click', ".award-record-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForAwardRecord') }}",
                    success: function (result) {
                        $('#awardRecordInputRow').append(result.html);
                        recalcSL('award-record');
                    }
                });
            });
            //Once remove button is clicked
            $(document).on('click', '.award-record-remove-Btn', function (e) {

                //            var button_id = $(this).attr("id");
                //            $("#remove" + button_id + "").remove();
                $(this).parent().parent().remove();
                recalcSL('award-record');
            });
            //End:: Multiple row create for service record
            //Start::Edit award record 
            $(document).on('click', '#editUserAwardRecordButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserAwardRecordForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateAwardRecordInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('user/'.$userInfoData->user_basic_profile_id.'/profile') }}"
                        }, 200);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '');
                        } else {
                            toastr.error('Error', 'Something went wrong');
                        }
                    }

                });
            });
            //End::User award Record 

            //Start::User punishment Record 
            //Start:: Multiple row create for punishment record

            //When '+' button clicked
            $(document).on('click', ".un-msn-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForUnMsn') }}",
                    success: function (result) {
                        $('#punishmentRecordInputRow').append(result.html);
                        recalcSL('punishment-record');
                    }
                });
            });
            $(document).on('click', ".country-visit-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForCountry') }}",
                    success: function (result) {
                        $('#newCountryRow').append(result.html);
                        recalcSL('country-visit');
                    }
                });
            });
            $(document).on('click', ".bank-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForBank') }}",
                    success: function (result) {
                        $('#newBankRow').append(result.html);
                        recalcSL('bank');
                    }
                });
            });
            //Once remove button is clicked
            $(document).on('click', '.punishment-record-remove-Btn', function (e) {

                //            var button_id = $(this).attr("id");
                //            $("#remove" + button_id + "").remove();
                $(this).parent().parent().remove();
                recalcSL('punishment-record');
            });
            $(document).on('click', '.country-visit-remove-Btn', function (e) {

                //            var button_id = $(this).attr("id");
                //            $("#remove" + button_id + "").remove();
                $(this).parent().parent().remove();
                recalcSL('country-visit');
            });
            $(document).on('click', '.bank-remove-Btn', function (e) {

                //            var button_id = $(this).attr("id");
                //            $("#remove" + button_id + "").remove();
                $(this).parent().parent().remove();
                recalcSL('bank');
            });
            //End:: Multiple row create for service record
            //Start::Edit punishment record 
            $(document).on('click', '#editUserPunishmentRecordButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserPunishmentRecordForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateUnMsn') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //End::User punishment Record 

            $(document).on('click', '#editUserCountryVisitedButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserCountryVisitedForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateCountryVisit') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            $(document).on('click', '#editUserBankButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserBankForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateBank') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //Start::User defence relative
            //Start:: Multiple row create for defence relative

            //When '+' button clicked
            $(document).on('click', ".defence-relative-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForDefenceRelative') }}",
                    success: function (result) {
                        $('#defenceRelativeInputRow').append(result.html);
                        recalcSL('defence-relative');
                    }
                });
            });
            //Once remove button is clicked
            $(document).on('click', '.defence-relative-remove-Btn', function (e) {

                //            var button_id = $(this).attr("id");
                //            $("#remove" + button_id + "").remove();
                $(this).parent().parent().remove();
                recalcSL('defence-relative');
            });
            //End:: Multiple row create for defence relative
            //Start::Edit defence relative
            $(document).on('click', '#editUserDefenceRelativeButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserDefenceRelativeForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateDefenceRelativeInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }
                });
            });
            //End::User defence relative 

            //Start:: Division District Thana for next kin

            //GET district when click division
            $(document).on('change', '#kinDivisionId', function () {
                var divisionId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('user/getDistrict')}}",
                    data: {
                        division_id: divisionId
                    },
                    success: function (result) {
                        $('#kinDistrictId').html(result.html);
                        $('#kinThanaId').html(result.htmlThana);
                    }
                });
            });
            //GET thana when click district
            $(document).on('change', '#kinDistrictId', function () {
                var districtId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('user/getThana')}}",
                    data: {
                        district_id: districtId
                    },
                    success: function (result) {
                        $('#kinThanaId').html(result.html);
                    }
                });
            });
            //End:: Division District Thana fot next kin

            //START:: Edit next of kin
            $(document).on('click', '#editUserNextKinButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserNextKinForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateNextKin') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }

                });
            });
            //END:: Edit next of kin

            //START:: Edit user medical details
            $(document).on('click', '#editUserMedicalDetailsButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserMedicalDetailsForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateMedicalDetails') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data, data.message, options);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '', options);
                        } else {
                            toastr.error('Error', 'Something went wrong', options);
                        }
                    }
                });
            });
            //Start::BMI Calculation
            $("#htFtMedical").on('keyup', function () {
                var ft = $(this).val();
                var inch = $("#htInch").val();
                var weight = $("#weight").val();
                bmiCalc(ft, inch, weight);
            });
            $("#htInchMedical").on('keyup', function () {
                var ft = $("#htFt").val();
                var inch = $(this).val();
                var weight = $("#weight").val();
                bmiCalc(ft, inch, weight);
            });
            $("#weightMedical").on('keyup', function () {
                var ft = $("#htFt").val();
                var inch = $("#htInch").val();
                var weight = $(this).val();
                bmiCalc(ft, inch, weight);
            });
            //End::BMI Calculation
            //END:: Edit user medical details

            //START:: Edit user winter training
            $(document).on('click', '#editUserWinterTrainingButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editUserWinterTrainingForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/updateWinterTraining') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('user/'.$userInfoData->user_basic_profile_id.'/profile') }}"
                        }, 200);
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        var errorsHtml = '';
                        if (jqXhr.status == 400) {
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value[0] + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, '');
                        } else {
                            toastr.error('Error', 'Something went wrong');
                        }
                    }

                });
            });
            //END:: Edit user winter training

            //Start::Number of winter collective training
            $("#participatedNo").on('keyup', function () {
                var participatedNo = $(this).val();
                //alert(participatedNo);
                $("#winterTrainingRowAdd").html('');
                if (participatedNo == '') {
                    participatedNo = 0;
                    return false;
                }
                $("#winterTrainingRowAdd").append('<div class="col-md-12 margin-top-10">' +
                        '<div class="table-responsive" id="winterTrainingRowAdd">' +
                        '<table class="table table-bordered">' +
                        '<thead>' +
                        '<tr class="info">' +
                        '<th class="vcenter" scope="col" class="vcenter text-center">@lang('label.SERIAL') </th>' +
                        '<th class="vcenter" scope="col" class="vcenter text-center">@lang('label.EXERCISE') <span class="text-danger"> *</span></th>' +
                        '<th class="vcenter" scope="col" class="vcenter text-center">@lang('label.YEAR') <span class="text-danger"> *</span></th>' +
                        '<th class="vcenter" scope="col" class="vcenter text-center">@lang('label.PLACE')</th>' +
                '</tr>' +
                        '</thead>' +
                        '<tbody id="winterTrainingRow">'
                        );
                for (var i = 1; i <= participatedNo; i++) {
                    $("#winterTrainingRow").append(
                            '<tr>' +
                            '<td class="vcenter text-center">' + i + '</td>' +
                            '<td class="vcenter"><input type="text" name="winter_training[' + i + '][exercise]" class="form-control" id="winterTraining[' + i + '][exercise]" /></td>' +
                            '<td class="vcenter"><input type="text" name="winter_training[' + i + '][year]" class="form-control" id="winterTraining[' + i + '][year]" /></td>' +
                            '<td class="vcenter"><input type="text" name="winter_training[' + i + '][place]" class="form-control" id="winterTraining[' + i + '][place]" /></td>' +
                            '</tr>'
                            );
                }
                $("#winterTrainingRowAdd").append('</tbody>' +
                        '</table>' +
                        '</div>' +
                        '</div>' +
                        '<div class = "col-md-12 margin-top-10">' +
                        '<a class = "btn  btn-circle green" id="editUserWinterTrainingButton"> @lang('label.SAVE_CHANGES') </a>&nbsp;' +
                        '<a href = "{{ URL::to('user / '.$userInfoData->users_id.' / profile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>' +
                '</div>'
                        );
            });
            //End::Number of winter collective training
            //
            //Start::Number of child
            $("#noOfChild").on('keyup', function () {
                var noOfChild = $(this).val();
                //alert(participatedNo);
                //$("#childInfo").html('');
                if (noOfChild == '') {
                    $('#childInfo').html('');
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: "{{ url('user/rowAddForChild') }}",
                    data: {no_of_child: noOfChild},
                    success: function (result) {
                        $('#childInfo').html(result.html);
                    }
                });
            });
            //End::Number of child
            //

            if ($('#forPermanentAddr').prop('checked')) {
                $('.permanent-address-block').hide();
                $('.permanent-address-present').show();
            } else {
                $('.permanent-address-block').show();
                $('.permanent-address-present').hide();
            }

            $(document).on('click', '#forPermanentAddr', function () {
                if (this.checked == true) {
                    $('.permanent-address-block').hide(300);
                    $('#forPermanentAddr').val(1);
                    $('.permanent-address-present').show(300);
                } else {
                    $('.permanent-address-block').show(300);
                    $('#forPermanentAddr').val(0);
                    $('.permanent-address-present').hide(300);
                }
            });
            //for at svc at present
            if ($('#forPresentSvc').prop('checked')) {
                $('.for-to-svc-block').hide();
            } else {
                $('.for-to-svc-block').show();
            }

            $(document).on('click', '#forPresentSvc', function () {
                if (this.checked == true) {
                    $('.for-to-svc-block').hide(300);
                    $('#forPresentSvc').val(1);
                } else {
                    $('.for-to-svc-block').show(300);
                    $('#forPresentSvc').val(0);
                }
            });
        });
        function recalcSL(type) {
            var sl = 0;
            $('.initial-' + type + '-sl').each(function () {
                sl = sl + 1;
                $(this).text(sl);
            });
            $('.new-' + type + '-sl').each(function () {
                sl = sl + 1;
                $(this).text(sl);
            });
        }

        function bmiCalc(ft, inch, weight) {
            var heightFtMtr = ft * 12 * 0.0254;
            var heightInchMtr = inch * 0.0254;
            var height = [heightFtMtr, heightInchMtr].reduce(function (a, b) {
                return a + b;
            });
            var bmi = (weight / (height * height));
            var ans = '';
            var value = '';
            if (bmi > 18.5 && bmi < 25) {
                ans = 'Normal';
                value = '2';
            } else if (bmi < 18.5) {
                ans = 'Under';
                value = '1';
            } else if (bmi >= 25) {
                ans = 'Over';
                value = '3';
            }
            $("#overUnderWeight").val(ans);
            $("#overUnderWeightValue").val(value);
        }
    </script>
    @endsection