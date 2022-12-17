<?php
$basePath = URL::to('/');
?>
@if (Request::get('view') == 'pdf' || Request::get('view') == 'print') 
<?php
if (Request::get('view') == 'pdf') {
    $basePath = base_path();
}
?>
<html>
    <head>
        <title>@lang('label.NDC_AMS_TITLE')</title>
        <link rel="shortcut icon" href="{{$basePath}}/public/img/favicon_sint.png" />
        <meta charset="UTF-8">
        <link href="{{asset('public/fonts/css.css?family=Open Sans')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('public/assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/css/components.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/morris/morris.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/jqvmap/jqvmap/jqvmap.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/css/plugins.min.css')}}" rel="stylesheet" type="text/css" />


        <!--BEGIN THEME LAYOUT STYLES--> 
        <!--<link href="{{asset('public/assets/layouts/layout/css/layout.min.css')}}" rel="stylesheet" type="text/css" />-->
        <link href="{{asset('public/assets/layouts/layout/css/custom.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/css/custom.css')}}" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link href="{{asset('public/assets/layouts/layout/css/downloadPdfPrint/print.css')}}" rel="stylesheet" type="text/css" /> 
        <link href="{{asset('public/assets/layouts/layout/css/downloadPdfPrint/printInvoice.css')}}" rel="stylesheet" type="text/css" /> 

        <style type="text/css" media="print">
            @page { 
                margin: 67px 48px 67px 96px;
                size: auto; 
            }
            body {
                font-size: 10px;
                font-family: Arial;
                
            }
            * {
                -webkit-print-color-adjust: exact !important; 
                color-adjust: exact !important; 
            }
        </style>

        <script src="{{asset('public/assets/global/plugins/jquery.min.js')}}" type="text/javascript"></script>
    </head>
    <body>


        <div class="portlet-body">
            <div class="row text-center">
                <div class="col-md-12 text-center">
                    <img width="500" height="auto" src="{{$basePath}}/public/img/sint_ams_logo.png" alt=""/>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <div class="text-center bold uppercase">
                        <span class="header">@lang('label.CI_DS_PROFILE')</span>
                    </div>
                </div>
            </div>
            <!-- START:: User Basic Info -->
            <div class="row">
                <!-- START::User Image -->
                <div class="col-md-12 text-center">
                    <table class="no-border">
                        <tr class="no-border">
                            <td width="15%" class="no-border v-top text-center">
                                <div class="profile-userpic text-center">
                                    @if(!empty($userInfoData->photo) && File::exists('public/uploads/user/' . $userInfoData->photo))
                                    <img src="{{$basePath}}/public/uploads/user/{{$userInfoData->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
                                         alt="{{ !empty($userInfoData->user_name)? $userInfoData->user_name:''}}" style="width: 150px;height: 150px;" />
                                    @else 
                                    <img src="{{$basePath}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
                                         alt="{{ !empty($userInfoData->user_name)? $userInfoData->user_name:'' }}"  style="width: 150px;height: 150px;" />
                                    @endif
                                </div>
                                <div class="profile-usertitle">
                                    <div class="text-center margin-bottom-10">
                                        <b>{!! !empty($userInfoData->user_name)? $userInfoData->user_name:'' !!}</b>
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
                            </td>

                            <td width="85%" class="v-top no-border">
                                <!-- END::User Image -->
                                <!--<div class="column sortable ">-->
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-info-circle green-color-style-color"></i> <strong>@lang('label.BASIC_INFORMATION')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr >
                                            <td class="vcenter fit bold info">@lang('label.COMMISSIONING_COURSE')</td>
                                            <td>{{$userInfoData->commissioning_course_name}}</td>
                                            <td class="vcenter fit bold info">@lang('label.WING')</td>
                                            <td> {{ !empty($userInfoData->wing_name) ? $userInfoData->wing_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="vcenter fit bold info">@lang('label.TYPE_OF_COMMISSION')</td>
                                            <td>{{ !empty($commissionTypeList[$userInfoData->commission_type]) ? $commissionTypeList[$userInfoData->commission_type] : '' }}</td>
                                            <td class="vcenter fit bold info">@lang('label.ARMS_SERVICES')</td>
                                            <td> {{ !empty($userInfoData->arms_service_name) ? $userInfoData->arms_service_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="vcenter fit bold info">@lang('label.COMMISSIONING_DATE')</td>
                                            <td>{{ isset($userInfoData->commisioning_date) ? Helper::formatDate($userInfoData->commisioning_date): ''}}</td>
                                            <td class="vcenter fit bold info">@lang('label.EMAIL')</td>
                                            <td>{{ !empty($userInfoData->email) ? $userInfoData->email: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="vcenter fit bold info">@lang('label.ANTI_DATE_SENIORITY')</td>
                                            <td>{{ !empty($userInfoData->anti_date_seniority) ? $userInfoData->anti_date_seniority: __('label.N_A')}}</td>
                                            <td class="vcenter fit bold info">@lang('label.PHONE')</td>
                                            <td class="vcenter">
                                                {{ !empty($userInfoData->phone_official) ? $userInfoData->phone_official . ' ' . __('label._OFFICIAL') . ', ': ''}}
                                                {{ !empty($userInfoData->phone) ? $userInfoData->phone . ' ' . __('label._PERSONAL'): ''}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="vcenter fit bold info">@lang('label.DT_OF_JOINING_AT_AFWC_WG')</td>
                                            <td>{{ !empty($userInfoData->join_date) ? Helper::formatDate($userInfoData->join_date): ''}}</td>
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
                                            <td class="vcenter fit bold info">@lang('label.FATHERS_NAME')</td>
                                            <td class="vcenter">{{ !empty($userInfoData->father_name) ? $userInfoData->father_name: ''}}</td> 
                                        </tr>

                                    </table>
                                </div>
                                <!--</div>-->
                            </td>
                        </tr>
                    </table>
                    <!-- SIDEBAR USER TITLE -->

                </div>

            </div>
            <!-- END:: User Basic Info -->
            <!-- SATRT::Marital Information and  Child Information -->
            <div class="row">
                <div class="col-md-12">
                    <table class="no-border">
                        <tr class="no-border">
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-life-ring green-color-style-color"></i> <strong>@lang('label.MARITAL_INFORMATION')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        @if(!empty($userInfoData->marital_status) && $userInfoData->marital_status == '1')
                                        <tr>
                                            <th class="vcenter fit bold info">@lang('label.SPOUSE_NAME')</th>
                                            <td class="vcenter">{{ !empty($userInfoData->spouse_name) ? $userInfoData->spouse_name: ''}}</td>
                                            <th class="vcenter fit bold info">@lang('label.DATE_OF_MARRIAGE')</th>
                                            <td class="vcenter"> {{ !empty($userInfoData->date_of_marriage) ? Helper::formatDate($userInfoData->date_of_marriage): ''}}</td>
                                        </tr>
                                        <tr>
                                            <th class="vcenter fit bold info">@lang('label.NICK_NAME')</th>
                                            <td class="vcenter">{{ !empty($userInfoData->spouse_nick_name) ? $userInfoData->spouse_nick_name: ''}}</td>
                                            <th class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</th>
                                            <td class="vcenter"> {{ !empty($userInfoData->spouse_dob) ? Helper::formatDate($userInfoData->spouse_dob): ''}}</td>
                                        </tr>
                                        <tr>
                                            <th class="vcenter fit bold info">@lang('label.MOBILE')</th>
                                            <td class="vcenter">{{ !empty($userInfoData->spouse_mobile) ? $userInfoData->spouse_mobile: ''}}</td>
                                            <th class="vcenter fit bold info">@lang('label.PROFESSION')</th>
                                            <td class="vcenter">{{ !empty($userInfoData->spouse_occupation) ? $spouseProfession[$userInfoData->spouse_occupation]: ''}}</td>
                                        </tr>
                                        <tr>
                                            <th class="vcenter fit bold info">@lang('label.WORK_ADDRESS')</th>
                                            <td class="vcenter" colspan="3">{{ !empty($userInfoData->spouse_work_address) ? $userInfoData->spouse_work_address: ''}}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </td>
                            <!--<div class="column sortable ">-->
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-life-ring green-color-style-color"></i> <strong>@lang('label.CHILDREN_INFORMATION')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th class="vcenter text-center fit bold info">@lang('label.SL')</th>
                                            <th class="vcenter fit bold info">@lang('label.NAME')</th>
                                            <th class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</th>
                                            <th class="vcenter fit bold info">@lang('label.SCHOOL_PROFESSION')</th>
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
                                <!--</div> -->
                            </td>
                        </tr>
                    </table>
                </div>


            </div>
            <!--END::Marital Information and  Child Information -->

            <!--Start::Permanent address and present address -->
            <div class="row">
                <!-- Start::user Permanent Address-->
                <div class="col-md-12">
                    <table class="no-border">
                        <tr class="no-border">
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-map-marker green-color-style-color"></i> <strong>@lang('label.PERMANENT_ADDRESS')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        @if(!empty($addressInfo))
                                        <tr>
                                            <th class="vcenter fit bold info">
                                                @lang('label.DIVISION')
                                            </th>
                                            <td class="vcenter">
                                                {{ !empty($addressInfo->division_id) && !empty($divisionList[$addressInfo->division_id]) ? $divisionList[$addressInfo->division_id] : __("label.N_A")}}
                                            </td>
                                            <th class="vcenter fit bold info">
                                                @lang('label.DISTRICT')
                                            </th>
                                            <td class="vcenter">
                                                {{ !empty($addressInfo->district_id) && !empty($districtList[$addressInfo->district_id]) ? $districtList[$addressInfo->district_id] : __("label.N_A")}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="vcenter fit bold info">
                                                @lang('label.THANA')
                                            </th>
                                            <td class="vcenter">
                                                {{ !empty($addressInfo->thana_id) && !empty($thanaList[$addressInfo->thana_id]) ? $thanaList[$addressInfo->thana_id] : __("label.N_A")}}
                                            </td>
                                            <th class="vcenter fit bold info">
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
                            </td>
                            <!-- End::user Permanent Address-->
                            <!-- Start::present address-->
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-10">
                                    <i class="fa fa-map-marker green-color-style-color"></i> <strong>@lang('label.PRESENT_ADDRESS')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        @if(!empty($presentAddressInfo))
                                        <tr>
                                            <th class="vcenter fit bold info">
                                                @lang('label.DIVISION')
                                            </th>
                                            <td class="vcenter">
                                                {{ !empty($presentAddressInfo->division_id) && !empty($divisionList[$presentAddressInfo->division_id]) ? $divisionList[$presentAddressInfo->division_id] : ''}}
                                            </td>
                                            <th class="vcenter fit bold info">
                                                @lang('label.DISTRICT')
                                            </th>
                                            <td class="vcenter">
                                                {{ !empty($presentAddressInfo->district_id) && !empty($presentDistrictList[$presentAddressInfo->district_id]) ? $presentDistrictList[$presentAddressInfo->district_id] : __("label.N_A")}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="vcenter fit bold info">
                                                @lang('label.THANA')
                                            </th>
                                            <td class="vcenter">
                                                {{ !empty($presentAddressInfo->thana_id) && !empty($presentThanaList[$presentAddressInfo->thana_id]) ? $presentThanaList[$presentAddressInfo->thana_id] : __("label.N_A")}}
                                            </td>
                                            <th class="vcenter fit bold info">
                                                @lang('label.ADDRESS') (@lang('label.RESIDENCE'))
                                            </th>
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
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- End::present address-->

            </div>
            <!--END::Permanent address and present address -->

            <!-- SATRT::Passport & Bank Information -->
            <div class="row">
                <div class="col-md-12">
                    <table class="no-border">
                        <tr class="no-border">
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-paper-plane green-color-style-color"></i> <strong>@lang('label.PASSPORT_DETAILS')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        @if(!empty($passportInfoData))
                                        <tr>
                                            <th class="vcenter fit bold info">@lang('label.PASSPORT_NO')</th>
                                            <td class="vcenter">{{ !empty($passportInfoData->passport_no) ? $passportInfoData->passport_no: __('label.N_A')}}</td>
                                            <th class="vcenter fit bold info">@lang('label.DATE_OF_ISSUE')</th>
                                            <td class="vcenter"> {{ !empty($passportInfoData->date_of_issue) ? Helper::formatDate($passportInfoData->date_of_issue): __('label.N_A')}}</td>
                                        </tr>
                                        <tr>
                                            <th class="vcenter fit bold info">@lang('label.PLACE_OF_ISSUE')</th>
                                            <td class="vcenter">{{ !empty($passportInfoData->place_of_issue) ? $passportInfoData->place_of_issue: ''}}</td>
                                            <th class="vcenter fit bold info">@lang('label.DATE_OF_EXPIRY')</th>
                                            <td class="vcenter"> {{ !empty($passportInfoData->date_of_expire) ? Helper::formatDate($passportInfoData->date_of_expire): ''}}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <th colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</th>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </td>
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-bank green-color-style-color"></i> <strong>@lang('label.BANK_INFO')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th class="vcenter text-center fit bold info">@lang('label.SL')</th>
                                            <th class="vcenter fit bold info">@lang('label.BANK_NAME')</th>
                                            <th class="vcenter fit bold info">@lang('label.BRANCH')</th>
                                            <th class="vcenter fit bold info">@lang('label.ACCT_NO')</th>
                                            <th class="vcenter fit bold info">@lang('label.ONLINE')</th>
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
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <!--END::Passport & Bank Information -->

            <!-- START:: Academic qualification Information-->
            <div class="row"> 
                <div class="col-md-12">
                    <div class="caption margin-bottom-5">
                        <i class="fa fa-graduation-cap green-color-style-color"></i> <strong>@lang('label.ACADEMIC_QUALIFICATION')</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</th>
                                <th class="vcenter fit bold info">@lang('label.INSTITUTE_NAME')</th>
                                <th class="vcenter fit bold info">@lang('label.EXAMINATION')</th>
                                <th class="vcenter fit bold info">@lang('label.FROM')</th>
                                <th class="vcenter fit bold info">@lang('label.TO')</th>
                                <th class="vcenter text-center fit bold info">@lang('label.QUAL_ERODE')</th>
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
            <!-- End:: Academic qualification Information-->
            <!-- START:: Mil qualification Information-->
            <div class="row"> 
                <div class="col-md-12">
                    <div class="caption margin-bottom-5">
                        <i class="fa fa-user green-color-style-color"></i> <strong>@lang('label.MIL_QUALIFICATION')</strong>
                    </div>
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
                            ?>
                            @if(!empty($milQualification))
                            @foreach($milQualification as $mKey => $milInfo) 
                            <tr>
                                <td class="vcenter text-center">{{$cSlShow}}</td>
                                <td class="vcenter">{{ !empty($milInfo['institute_name']) ? $milInfo['institute_name']: ''}}</td>
                                <td class="vcenter">  
                                    @if($milInfo['course']== 5)
                                    {{ !empty($milInfo['course_name']) ? $milInfo['course_name']: ''}}
                                    @else
                                    {{ !empty($milInfo['course']) && !empty($milCourseList[$milInfo['course']]) ? $milCourseList[$milInfo['course']]: ''}}
                                    @endif
                                </td>
                                <td class="vcenter">{{ !empty($milInfo['from']) ? $milInfo['from']: ''}}</td>
                                <td class="vcenter"> {{ !empty($milInfo['to']) ? $milInfo['to']: (!empty($milInfo['year']) ? $milInfo['year']: '')}}</td>
                                <td class="vcenter text-center"> 
                                    @if($milInfo['course']== 5)
                                    {{ !empty($milInfo['other_result']) ? $milInfo['other_result']: ''}}
                                    @else
                                    {{ !empty($milInfo['result']) ? $milInfo['result']: ''}}
                                    @endif
                                </td>
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
            <!-- End:: Mil qualification Information-->

            <!-- Start:: Record of service Information-->
            <div class="row">
                <div class="col-md-12">
                    <div class="caption margin-bottom-5">
                        <i class="fa fa fa-cogs green-color-style-color"></i> <strong>@lang('label.RECORD_OF_SERVICE')</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <td class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                <td class="vcenter fit bold info">@lang('label.FROM')</td>
                                <td class="vcenter fit bold info">@lang('label.TO')</td>
                                <td class="vcenter fit bold info">@lang('label.UNIT_FMN_INST')</td>
                                <td class="vcenter fit bold info">@lang('label.RESPONSIBILITY')</td>
                                <td class="vcenter fit bold info">@lang('label.APPT')</td>
                            </tr>
                            <?php
                            $serviceRecord = !empty($serviceRecordInfoData) ? json_decode($serviceRecordInfoData->service_record_info, true) : null;
                            $srSlShow = 1;
                            $respList = Common::getSvcResposibilityList();
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
                                $respType = (!empty($serviceRecordInfo['resp']) && !empty($respList[$serviceRecordInfo['resp']])) ? $respList[$serviceRecordInfo['resp']] : '';
                                ?>
                                <td class="vcenter">{{ $unitFmnInst }}</td>
                                <td class="vcenter">{{ $respType }}</td>
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
            <!-- End:: Record of service Information-->

            <!-- Start:: un mission Information-->
            <!--            <div class="row">
                            <div class="col-md-12">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-globe green-color-style-color"></i> <strong>@lang('label.UN_MSN')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th class="vcenter text-center fit bold info" rowspan="2">@lang('label.SERIAL')</th>
                                            <th class="vcenter text-center fit bold info" colspan="2">@lang('label.DURATION')</th>
                                            <th class="vcenter fit bold info" rowspan="2">@lang('label.MSN')</th>
                                            <th class="vcenter fit bold info" rowspan="2">@lang('label.APPT')</th>
                                            <th class="vcenter fit bold info" rowspan="2">@lang('label.REMARKS')</th>
                                        </tr>
                                        <tr>
                                            <th class="vcenter fit bold info">@lang('label.FROM')</th>
                                            <th class="vcenter fit bold info">@lang('label.TO')</th>
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
            
                        </div>-->
            <!-- End:: un mission Information-->

            <!-- Start:: country visited Information-->
            <div class="row">
                <div class="col-md-12">
                    <div class="caption margin-bottom-5">
                        <i class="fa fa-plane green-color-style-color"></i> <strong>@lang('label.COUNTRY_VISITED')</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <td class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                <td class="vcenter fit bold info">@lang('label.NAME_OF_COUNTRY')</td>
                                <td class="vcenter fit bold info">@lang('label.FROM')</td>
                                <td class="vcenter fit bold info">@lang('label.TO')</td>
                                <td class="vcenter fit bold info">@lang('label.REASONS_FOR_VISIT')</td>
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
            <!-- End:: country visited Information-->

            <div class="row"> 

                <!-- START:: Cm Others Info -->

                <div class="col-md-6">
                    <div class="portlet portlet-sortable box green-color-style">
                        <div class="portlet-title ui-sortable-handle">
                            <div class="caption">
                                <i class="fa fa-calendar green-color-style-color"></i>@lang('label.DECORATION_AWARD')
                            </div>
                        </div>
                        <div class="portlet-body" style="padding: 8px !important">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    @if(!empty($othersInfoData))
                                    <?php
                                    $sl = 0;
                                    $decArr = !empty($othersInfoData->decoration_id) ? explode(', ', $othersInfoData->decoration_id) : [];
                                    ?>
                                    @if(!empty($decArr))
                                    <tr>
                                        <td>
                                            @foreach($decArr as $dec)
                                            {!! ++$sl !!}.&nbsp;{!! !empty($decorationList[$dec]) ? $decorationList[$dec] : (!empty($dec) ? $dec : '') !!}{!! '<br />' !!}
                                            @endforeach
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                    </tr>
                                    @endif
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
                <div class="col-md-6">
                    <div class="portlet portlet-sortable box green-color-style">
                        <div class="portlet-title ui-sortable-handle">
                            <div class="caption">
                                <i class="fa fa-calendar green-color-style-color"></i>@lang('label.EXTRA_CURRICULAR_EXPERTISE')
                            </div>
                        </div>
                        <div class="portlet-body" style="padding: 8px !important">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    @if(!empty($othersInfoData))
                                    <?php
                                    $sl = 0;
                                    $exCurrArr = !empty($othersInfoData->extra_curriclar_expt) ? explode(',', $othersInfoData->extra_curriclar_expt) : [];
                                    ?>
                                    @if(!empty($exCurrArr))
                                    <tr>
                                        <td>
                                            @foreach($exCurrArr as $key => $curr)
                                            {!! ++$sl !!}.&nbsp;{!! !empty($curr) ? $curr : '' !!}{!! '<br />' !!}
                                            @endforeach
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                    </tr>
                                    @endif
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
            </div>
            <!-- END:: user Others Info -->
        </div>


        <!--footer-->
        <table class="no-border margin-top-10">
            <tr>
                <td class="no-border text-left">
                    @lang('label.GENERATED_ON') {!! '<strong>'.Helper::formatDate(date('Y-m-d H:i:s')).'</strong> by <strong>'.Auth::user()->full_name.'</strong>' !!}.
                </td>
                <td class="no-border text-right">
                    <strong>@lang('label.GENERATED_FROM_AFWC')</strong>
                </td>
            </tr>
        </table>

        <script src="{{asset('public/assets/global/plugins/bootstrap/js/bootstrap.min.js')}}"  type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->


        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{asset('public/assets/global/scripts/app.min.js')}}" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!--<script src="{{asset('public/assets/layouts/layout/scripts/layout.min.js')}}" type="text/javascript"></script>-->



        <!--<script src="{{asset('public/js/apexcharts.min.js')}}" type="text/javascript"></script>-->


        <script>
document.addEventListener("DOMContentLoaded", function (event) {
    window.print();
});
        </script>

    </body>
</html>
@else

@endif