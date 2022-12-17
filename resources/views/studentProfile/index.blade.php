@extends('layouts.default.master')
@section('data_count')
<!-- BEGIN CONTENT BODY -->
<!-- BEGIN PORTLET-->
@include('layouts.flash')
<!-- END PORTLET-->
<div class="row margin-left-right-0">
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile">
            <div class="tabbable-line tabbable-full-width">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab_1_1" data-toggle="tab"> @lang('label.OVERVIEW') </a>
                    </li>
                    <li>
                        <a href="#tab_1_2" data-toggle="tab"> @lang('label.EDIT_BASIC_INFORMATION') </a>
                    </li>
                    <li>
                        <a href="#tab_1_3" data-toggle="tab"> @lang('label.EDIT_OTHERS') </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1_1">
                        <!-- START:: User Basic Info -->
                        <div class="row">
                            <!-- START::User Image -->
                            <div class="col-md-2 text-center">
                                <!-- SIDEBAR USER TITLE -->
                                <div class="profile-userpic">
                                    @if(!empty($studentInfoData->photo) && File::exists('public/uploads/user/' . $studentInfoData->photo))
                                    <img src="{{URL::to('/')}}/public/uploads/user/{{$studentInfoData->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
                                         alt="{{ !empty($studentInfoData->student_name)? $studentInfoData->student_name:''}}" style="width: 250px;height: 160px;" />
                                    @else 
                                    <img src="{{URL::to('/')}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
                                         alt="{{ !empty($studentInfoData->student_name)? $studentInfoData->student_name:'' }}"  style="width: 250px;height: 160px;" />
                                    @endif
                                </div>
                                <div class="profile-usertitle">
                                    <div class="text-center">
                                        <b>{{!empty($studentInfoData->student_name)? $studentInfoData->student_name:''}}</b>
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
                                                    <td class="vcenter fit bold info">@lang('label.COURSE')</td>
                                                    <td>{{$studentInfoData->course_name}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.ARMS_SERVICES')</td>
                                                    <td> {{ !empty($studentInfoData->arms_service_name) ? $studentInfoData->arms_service_name: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.COMMISSIONING_COURSE')</td>
                                                    <td>{{$studentInfoData->commissioning_course_name}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.UNIT')</td>
                                                    <td> {{ !empty($studentInfoData->unit_name) ? $studentInfoData->unit_name: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.COMMISSIONING_DATE')</td>
                                                    <td>{{ isset($studentInfoData->commisioning_date) ? Helper::formatDate($studentInfoData->commisioning_date): ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.FORMATION')</td>
                                                    <td>{{ !empty($studentInfoData->formation_name) ? $studentInfoData->formation_name: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.ANTI_DATE_SENIORITY')</td>
                                                    <td>{{ !empty($studentInfoData->anti_date_seniority) ? $studentInfoData->anti_date_seniority: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.COMMANDING_OFFICER_NAME')</td>
                                                    <td>{{ !empty($studentInfoData->commanding_officer_name) ? $studentInfoData->commanding_officer_name: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.COURSE_POSITION')</td>
                                                    <td class="text-right vcenter text-right">{{ !empty($studentInfoData->course_position) ? $studentInfoData->course_position . ' ' . __('label.OUT_OF') . ' ' . (!empty($studentInfoData->position_out) ? $studentInfoData->position_out: '') : ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.COMMANDING_OFFICER_CONTACT')</td>
                                                    <td class="vcenter">{{ !empty($studentInfoData->commanding_officer_contact_no) ? $studentInfoData->commanding_officer_contact_no: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.NATIONALITY')</td>
                                                    <td class="vcenter">{{ !empty($studentInfoData->nationality) ? $studentInfoData->nationality: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.HEIGHT')</td>
                                                    <?php
                                                    $ft = !empty($studentInfoData->ht_ft) ? $studentInfoData->ht_ft : 0;
                                                    $inch = !empty($studentInfoData->ht_inch) ? $studentInfoData->ht_inch : 0;
                                                    $height = $ft . '\' ' . $inch . '"';
                                                    if ($ft == 0 && $inch == 0) {
                                                        $height = '';
                                                    } elseif ($inch == 0 && $ft != 0) {
                                                        $height = $ft . '\'';
                                                    } elseif ($ft == 0 && $inch != 0) {
                                                        $height = $inch . '"';
                                                    }
                                                    ?>
                                                    <td class="text-right vcenter text-right">{{ $height }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.BIRTH_PLACE')</td>
                                                    <td>{{ !empty($studentInfoData->birth_place) ? $studentInfoData->birth_place: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.WEIGHT')</td>
                                                    <td class="vcenter text-right">{{ ((!empty($studentInfoData->weight)) && ($studentInfoData->weight != '0.00')) ? $studentInfoData->weight . ' ' .__('label.KG') : ''}}</td>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $maritalStatus = (!empty($maritalStatusList) && ($studentInfoData->marital_status != '0') && isset($maritalStatusList[$studentInfoData->marital_status])) ? $maritalStatusList[$studentInfoData->marital_status] : __("label.N_A");
                                                    ?>
                                                    <td class="vcenter fit bold info">@lang('label.RELIGION')</td>
                                                    <td>{{ !empty($studentInfoData->religion_name) ? $studentInfoData->religion_name: ''}}</td>
                                                    <td class = "vcenter fit bold info">@lang('label.MARITIAL_STATUS')</td>
                                                    <td> {{ $maritalStatus }} </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.MEDICAL_CATEGORIZE')</td>
                                                    <td>{{ !empty($studentInfoData->medical_categorize) ? $studentInfoData->medical_categorize: ''}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.PHONE')</td>
                                                    <td colspan="3" class="vcenter">{{ !empty($studentInfoData->phone) ? $studentInfoData->phone: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.EMAIL')</td>
                                                    <td colspan="3" class="vcenter">{{ !empty($studentInfoData->email) ? $studentInfoData->email: ''}}</td> 
                                                </tr>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--</div>-->
                            </div>
                        </div>
                        <!-- END:: User Basic Info -->
                        <!-- SATRT::Family Information and marital status -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa fa-users green-color-style-color"></i>@lang('label.FAMILY_INFORMATION')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.FATHERS_NAME')</td>
                                                    <td class="vcenter">{{ !empty($studentInfoData->father_name) ? $studentInfoData->father_name: __("label.N_A")}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.MOTHERS_NAME')</td>
                                                    <td class="vcenter"> {{ !empty($studentInfoData->mother_name) ? $studentInfoData->mother_name: __("label.N_A")}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.OCCUPATION')</td>
                                                    <td class="vcenter">{{ !empty($studentInfoData->father_occupation) ? $studentInfoData->father_occupation: __("label.N_A")}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.OCCUPATION')</td>
                                                    <td class="vcenter"> {{ !empty($studentInfoData->mother_occupation) ? $studentInfoData->mother_occupation: __("label.N_A")}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.WORK_ADDRESS')</td>
                                                    <td class="vcenter">{{ !empty($studentInfoData->father_address) ? $studentInfoData->father_address: __("label.N_A")}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.WORK_ADDRESS')</td>
                                                    <td class="vcenter">{{ !empty($studentInfoData->mother_address) ? $studentInfoData->mother_address: __("label.N_A")}}</td>
                                                </tr>
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
                                            <i class="fa fa-life-ring green-color-style-color"></i>@lang('label.MARITAL_INFORMATION')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                @if(!empty($studentInfoData->marital_status) && $studentInfoData->marital_status == '1')
                                                <tr>
                                                    <td class = "vcenter fit bold info">@lang('label.DATE_OF_MARRIAGE')</td>
                                                    <td colspan="3" class="vcenter">{{ isset($studentInfoData->date_of_marriage) ? Helper::formatDate($studentInfoData->date_of_marriage): ''}}</td>
                                                    @endif
                                                </tr>
                                                @if($studentInfoData->marital_status == '1')
                                                <tr>
                                                    <td class = "vcenter fit bold info">@lang('label.SPOUSE_NAME')</td>
                                                    <td class="vcenter">{{!empty($studentInfoData->spouse_name) ? $studentInfoData->spouse_name: ''}}</td>
                                                    <td class = "vcenter fit bold info">@lang('label.OCCUPATION')</td>
                                                    <td class="vcenter">{{!empty($studentInfoData->spouse_occupation) ? $studentInfoData->spouse_occupation: ''}}</td>
                                                </tr>
                                                <tr>
                                                    <td class = "vcenter fit bold info">@lang('label.WORK_ADDRESS')</td>
                                                    <td colspan="3" class="vcenter">{{!empty($studentInfoData->spouse_work_address) ? $studentInfoData->spouse_work_address: __('label.N_A')}}</td>
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
                                <!--</div> -->

                            </div>


                        </div>
                        <!--END::Family Information and marital status -->

                        <!--Start::Brother/Sister -->
                        <div class="row">
                            <!--Start::Brother/Sister  info-->
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa fa-users green-color-style-color"></i>@lang('label.BROTHER_SISTER')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.NAME')</td>
                                                    <td class="vcenter fit bold info">@lang('label.RELATION')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.AGE')</td>
                                                    <td class="vcenter fit bold info">@lang('label.OCCUPATION')</td>
                                                    <td class="vcenter fit bold info">@lang('label.WORK_ADDRESS')</td>
                                                </tr>
                                                <?php
                                                $brotherSister = !empty($brotherSisterInfoData) ? json_decode($brotherSisterInfoData->brother_sister_info, true) : null;
                                                $bSlShow = 1;
                                                ?>
                                                @if(!empty($brotherSister))
                                                @foreach($brotherSister as $var => $brotherSisterInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center">{{ $bSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($brotherSisterInfo['name']) ? $brotherSisterInfo['name']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($brotherSisterInfo['relation']) ? $brotherSisterInfo['relation']: ''}}</td>
                                                    <td class="vcenter text-right">{{ !empty($brotherSisterInfo['age']) ? $brotherSisterInfo['age']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($brotherSisterInfo['occupation']) ? $brotherSisterInfo['occupation']: ''}}</td>
                                                    <td class="vcenter">{{ !empty($brotherSisterInfo['address']) ? $brotherSisterInfo['address']: ''}}</td>
                                                </tr>
                                                <?php $bSlShow++; ?>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td class="vcenter" colspan="6">@lang('label.NO_DATA_FOUND')</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--End::Brother/Sister info-->
                        </div>
                        <!--END::Brother/Sister -->

                        <!--Start::Permanent address and next of kin -->
                        <div class="row">
                            <!-- Start::Student Permanent Address-->
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
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DIVISION')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($addressInfo->division_id) ? $divisionList[$addressInfo->division_id]: __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DISTRICT')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($addressInfo->district_id) ? $districtList[$addressInfo->district_id]: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.THANA')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($addressInfo->thana_id) ? $thanaList[$addressInfo->thana_id]: __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.ADDRESS')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($addressInfo->address_details) ? $addressInfo->address_details: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End::Student Permanent Address-->
                            <!-- Start::Student next kin-->
                            <div class="col-md-6">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-map-marker green-color-style-color"></i> @lang('label.NEXT_OF_KIN')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.NAME')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($nextKinAddressInfo->name) ? $nextKinAddressInfo->name: __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.RELATION')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($nextKinAddressInfo->relation) ? $nextKinAddressInfo->relation: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DIVISION')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($nextKinAddressInfo->division_id) ? $divisionList[$nextKinAddressInfo->division_id]: __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DISTRICT')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($nextKinAddressInfo->district_id) ? $nextKinDistrictList[$nextKinAddressInfo->district_id]: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.THANA')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($nextKinAddressInfo->thana_id) ? $nextKinThanaList[$nextKinAddressInfo->thana_id]: __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.ADDRESS')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($nextKinAddressInfo->address_details) ? $nextKinAddressInfo->address_details: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End::Student next kin-->

                        </div>
                        <!--END::Permanent address and next of kin -->


                        <!-- START:: Student Civil Education and Service Record Information-->
                        <div class="row">

                            <!--Start::Civil Education  info-->
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-graduation-cap green-color-style-color"></i>@lang('label.CIVIL_EDUCATION')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.INSTITUTE_NAME')</td>
                                                    <td class="vcenter fit bold info">@lang('label.EXAMINATION')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.RESULT')</td>
                                                    <td class="vcenter text-right fit bold info">@lang('label.YEAR')</td>
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
                                                    <td class="vcenter text-right">{{ !empty($civilEducationInfo['result']) ? $civilEducationInfo['result']: ''}}</td>
                                                    <td class="vcenter text-right"> {{ !empty($civilEducationInfo['year']) ? $civilEducationInfo['year']: ''}}</td>
                                                </tr>
                                                <?php
                                                $cSlShow++;
                                                ?>
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
                            <!--Start::Service record  info-->
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa fa-cogs green-color-style-color"></i>@lang('label.SERVICE_RECORD')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.UNIT')</td>
                                                    <td class="vcenter fit bold info">@lang('label.APPOINTMENT')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.YEAR')</td>
                                                </tr>
                                                <?php
                                                $serviceRecord = !empty($serviceRecordInfoData) ? json_decode($serviceRecordInfoData->service_record_info, true) : null;
                                                $srSlShow = 1;
                                                ?>
                                                @if(!empty($serviceRecord))
                                                @foreach($serviceRecord as $srVar => $serviceRecordInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center width-50">{{ $srSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($serviceRecordInfo['unit']) ? $unitList[$serviceRecordInfo['unit']]: ''}}</td>
                                                    <td class="vcenter">{{ !empty($serviceRecordInfo['appointment']) ? $appointmentList[$serviceRecordInfo['appointment']]: ''}}</td>
                                                    <td class="vcenter text-right">{{ !empty($serviceRecordInfo['year']) ? $serviceRecordInfo['year']: ''}}</td>

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
                            <!--End::Service Record info-->
                        </div>
                        <!--End::Civil Education info-->
                        <!-- END:: Student Civil Education Information-->

                        <!-- Start::Student awards record and punishment record-->
                        <div class="row">
                            <!--Start::Awards Record  info-->
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-trophy green-color-style-color"></i>@lang('label.AWARD_RECORD')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.AWARD')</td>
                                                    <td class="vcenter fit bold info">@lang('label.REASON')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.YEAR')</td>
                                                </tr>
                                                <?php
                                                $aSlShow = 1;
                                                $awardRecord = !empty($awardRecordInfoData) ? json_decode($awardRecordInfoData->award_record_info, true) : null;
                                                //echo '<pre>';        print_r($brotherSister);exit;
                                                ?>
                                                @if(!empty($awardRecord))
                                                @foreach($awardRecord as $arVar => $awardRecordInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center width-50">{{$aSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($awardRecordInfo['award']) ? $awardRecordInfo['award']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($awardRecordInfo['reason']) ? $awardRecordInfo['reason']: ''}}</td>
                                                    <td class="vcenter text-right"> {{ !empty($awardRecordInfo['year']) ? $awardRecordInfo['year']: ''}}</td>
                                                </tr>
                                                <?php
                                                $aSlShow++;
                                                ?>
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
                            <!--End::Award record  info-->
                        </div>
                        <div class="row">
                            <!--Start::Punishment Record  info-->
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-gavel green-color-style-color"></i>@lang('label.PUNISHMENT_RECORD')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.PUNISHMENT')</td>
                                                    <td class="vcenter fit bold info">@lang('label.REASON')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.YEAR')</td>
                                                </tr>
                                                <?php
                                                $pSlShow = 1;
                                                $punishmentRecord = !empty($punishmentRecordInfoData) ? json_decode($punishmentRecordInfoData->punishment_record_info, true) : null;
                                                //echo '<pre>';        print_r($brotherSister);exit;
                                                ?>
                                                @if(!empty($punishmentRecord))
                                                @foreach($punishmentRecord as $arVar => $punishmentRecordInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center width-50">{{$pSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($punishmentRecordInfo['punishment']) ? $punishmentRecordInfo['punishment']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($punishmentRecordInfo['reason']) ? $punishmentRecordInfo['reason']: ''}}</td>
                                                    <td class="vcenter text-right"> {{ !empty($punishmentRecordInfo['year']) ? $punishmentRecordInfo['year']: ''}}</td>
                                                </tr>
                                                <?php
                                                $pSlShow++;
                                                ?>
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
                            <!--End::Punishment record  info-->
                        </div>
                        <!-- End::Student awards record and punishment record-->

                        <!-- Start::Student  -->
                        <div class="row">
                            <!--END::Student Defence Relative -->
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-user green-color-style-color"></i> @lang('label.DEFENCE_RELATIVE')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.COURSE')</td>
                                                    <td class="vcenter fit bold info">@lang('label.INSTITUTE_NAME')</td>
                                                    <td class="vcenter fit bold info">@lang('label.GRADING')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.YEAR')</td>
                                                </tr>
                                                <?php
                                                $dSlShow = 1;
                                                $defenceRelative = !empty($defenceRelativeInfoData) ? json_decode($defenceRelativeInfoData->student_relative_info, true) : null;
                                                //echo '<pre>';        print_r($brotherSister);exit;
                                                ?>
                                                @if(!empty($defenceRelative))
                                                @foreach($defenceRelative as $drVar => $defenceRelativeInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center width-50">{{$dSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($defenceRelativeInfo['course']) ? $courseList[$defenceRelativeInfo['course']]: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($defenceRelativeInfo['institute']) ? $defenceRelativeInfo['institute']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($defenceRelativeInfo['grading']) ? $defenceRelativeInfo['grading']: ''}}</td>
                                                    <td class="vcenter text-right"> {{ !empty($defenceRelativeInfo['year']) ? $defenceRelativeInfo['year']: ''}}</td>
                                                </tr>
                                                <?php
                                                $dSlShow++;
                                                ?>
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
                            <!--END::Student Defence Relative -->
                        </div>
                        <div class="row">

                            <!--END::Student winter training -->
                            <div class="col-md-12">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-user green-color-style-color"></i> @lang('label.WINTER_COLLECTIVE_TRAINING')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                    <td class="vcenter fit bold info">@lang('label.EXERCISE')</td>
                                                    <td class="vcenter text-center fit bold info">@lang('label.YEAR')</td>
                                                    <td class="vcenter fit bold info">@lang('label.PLACE')</td>
                                                </tr>
                                                <?php
                                                $wSlShow = 1;
                                                $winterTraining = !empty($studentWinterTrainingInfoData) ? json_decode($studentWinterTrainingInfoData->training_info, true) : null;
                                                //echo '<pre>';        print_r($brotherSister);exit;
                                                ?>
                                                @if(!empty($winterTraining))
                                                @foreach($winterTraining as $wtVar => $winterTrainingInfo)                               
                                                <tr>
                                                    <td class="vcenter text-center width-50">{{$wSlShow}}</td>
                                                    <td class="vcenter">{{ !empty($winterTrainingInfo['exercise']) ? $winterTrainingInfo['exercise']: ''}}</td>
                                                    <td class="vcenter text-right"> {{ !empty($winterTrainingInfo['year']) ? $winterTrainingInfo['year']: ''}}</td>
                                                    <td class="vcenter"> {{ !empty($winterTrainingInfo['place']) ? $winterTrainingInfo['place']: ''}}</td>

                                                </tr>
                                                <?php
                                                $wSlShow++;
                                                ?>
                                                @endforeach

                                                <tr>
                                                    <td colspan="4" class="vcenter text-center">
                                                        <span class="bold">
                                                            @lang('label.NUMBER_OF_WINTER_COLLECTIVE_EXERCISE_PARTICIPATED'): {{ !empty($studentWinterTrainingInfoData->participated_no) ? $studentWinterTrainingInfoData->participated_no: ''}}
                                                        </span>
                                                    </td>


                                                </tr>
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
                            <!--END::Student Winter training -->

                        </div>
                        <!-- End::Student relative in defence  -->

                        <!--Start::Student next kin Information & student medical details-->
                        <div class="row">
                            <!--Start::Medical details  info-->
                            <div class="col-md-6">
                                <div class="portlet portlet-sortable box green-color-style">
                                    <div class="portlet-title ui-sortable-handle">
                                        <div class="caption">
                                            <i class="fa fa-plus-square green-color-style-color"></i>@lang('label.MEDICAL_DETAILS')
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 8px !important">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.CATEGORY')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($studentMedicalDetails->category) ? $studentMedicalDetails->category: __("label.N_A")}}
                                                    </td>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.BlOOD_GROUP')
                                                    </td>
                                                    <td class="vcenter">
                                                        {{ !empty($studentMedicalDetails->blood_group) ? $studentMedicalDetails->blood_group: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.DATE_OF_BIRTH')
                                                    </td>
                                                    <td class="vcenter">{{ isset($studentMedicalDetails->date_of_birth) ? Helper::formatDate($studentMedicalDetails->date_of_birth): __("label.N_A")}}</td>

                                                    <td class="vcenter fit bold info">@lang('label.HEIGHT')</td>
                                                    <?php
                                                    $ft = !empty($studentInfoData->ht_ft) ? $studentInfoData->ht_ft : 0;
                                                    $inch = !empty($studentInfoData->ht_inch) ? $studentInfoData->ht_inch : 0;
                                                    $height = $ft . '\' ' . $inch . '"';
                                                    if ($ft == 0 && $inch == 0) {
                                                        $height = '';
                                                    } elseif ($inch == 0 && $ft != 0) {
                                                        $height = $ft . '\'';
                                                    } elseif ($ft == 0 && $inch != 0) {
                                                        $height = $inch . '"';
                                                    }
                                                    ?>
                                                    <td class="vcenter text-right text-right">{{ $height }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenterfit bold info">@lang('label.WEIGHT')</td>
                                                    <td class="vcenter text-right">{{ ((!empty($studentInfoData->weight)) && ($studentInfoData->weight != '0.00')) ? $studentInfoData->weight . ' ' .__('label.KG') : ''}}</td>

                                                    <td class="vcenter fit bold info">
                                                        @lang('label.OVER_UNDER_WEIGHT')
                                                    </td>
                                                    <td class="vcenter">
                                                        @if(!empty($studentMedicalDetails->over_under_weight) && $studentMedicalDetails->over_under_weight == 2)
                                                        @lang('label.NORMAL')
                                                        @elseif(!empty($studentMedicalDetails->over_under_weight) && $studentMedicalDetails->over_under_weight == 1)
                                                        @lang('label.UNDER')
                                                        @elseif(!empty($studentMedicalDetails->over_under_weight) && $studentMedicalDetails->over_under_weight == 3)
                                                        @lang('label.OVER')
                                                        @else
                                                        @lang('label.N_A')
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">
                                                        @lang('label.ANY_DISEASE')
                                                    </td>
                                                    <td colspan="3" class="vcenter">
                                                        {{ !empty($studentMedicalDetails->any_disease) ? $studentMedicalDetails->any_disease: __("label.N_A")}}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--End::Medical details  info-->
                            <!-- START:: Student Others Info -->
                            <div class="col-md-6">
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
                                                    <td class="vcenter fit bold info">@lang('label.VISITED_COUNTRIES')</td>
                                                    @php
                                                    $country = !empty($othersInfoData->visited_countries_id)? json_decode($othersInfoData->visited_countries_id, true):[];
                                                    @endphp
                                                    <td colspan="3" class="vcenter">
                                                        @if(!empty($country))
                                                        <?php $lastCountry = end($country); ?>
                                                        @foreach($country as $key => $countryId)
                                                        {{$countriesVisitedList[$countryId]}}
                                                        @if($lastCountry != $countryId)
                                                        ,&nbsp;
                                                        @endif
                                                        @endforeach

                                                        @else
                                                        @lang('label.N_A')
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.SPECIAL_QUALITY')</td>
                                                    <td colspan="3" class="vcenter">  {{ !empty($othersInfoData->special_quality) ? $othersInfoData->special_quality: __("label.N_A")}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter fit bold info">@lang('label.SWIMMING')</td>
                                                    <td class="vcenter">{{ !empty($othersInfoData->swimming)?$swimmingList[$othersInfoData->swimming]:  __("label.N_A")}}</td>
                                                    <td class="vcenter fit bold info">@lang('label.PROFESSIONAL_COMPUTER')</td>
                                                    @if(!empty($othersInfoData->professional_computer) && $othersInfoData->professional_computer == '1')
                                                    <td class="vcenter">@lang('label.YES') </td>
                                                    @else
                                                    <td class="vcenter">@lang('label.NO') </td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END:: Student Others Info -->

                        </div>
                        <!--END::Student next kin Information & student medical details -->



                    </div>
                    <!--tab_1_2-->
                    <div class = "tab-pane" id = "tab_1_2">
                        <div class = "row profile-account">
                            <div class = "col-md-3">
                                <ul class = "ver-inline-menu tabbable margin-bottom-10">
                                    <li class = "active">
                                        <a data-toggle = "tab" href = "#tab_1-1">
                                            <i class = "fa fa-cog"></i> @lang('label.PERSONAL_INFO') </a>
                                        <span class = "after"> </span>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2-2">
                                            <i class = "fa fa-picture-o"></i> @lang('label.CHANGE_PHOTO')
                                        </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_3-3">
                                            <i class = "fa fa-lock"></i> @lang('label.CHANGE_PASSWORD') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_4-4">
                                            <i class = "fa fa-users"></i> @lang('label.FAMILY_INFO') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_5-5">
                                            <i class = "fa fa-life-ring"></i> @lang('label.MARITAL_INFO') </a>
                                    </li>
                                    <!--                                    <li>
                                                                            <a data-toggle = "tab" href = "#tab_4-4">
                                                                                <i class = "fa fa-eye"></i> Privacity Settings </a>
                                                                        </li>-->
                                </ul>
                            </div>
                            <div class = "col-md-9">
                                <div class = "tab-content">
                                    <!--Start::Edit basic student -->
                                    <div id = "tab_1-1" class = "tab-pane active">
                                        <div class="row margin-bottom-10">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_BASIC_INFORMATION')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editStudentProfileForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}
                                        <div class="col-md-6">
                                            <div class = "form-group">
                                                <label class = "control-label" for="fullName">@lang('label.FULL_NAME')<span class="text-danger"> *</span></label>
                                                {!! Form::text('full_name', !empty($studentInfoData->full_name)?$studentInfoData->full_name:'', ['id'=> 'fullName', 'class' => 'form-control']) !!} 
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="officialName">@lang('label.OFFICIAL_NAME')<span class="text-danger"> *</span></label>
                                                {!! Form::text('official_name', !empty($studentInfoData->official_name)?$studentInfoData->official_name:'', ['id'=> 'officialName', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="username">@lang('label.USERNAME')<span class="text-danger"> *</span></label>
                                                {!! Form::text('username', !empty($studentInfoData->username)?$studentInfoData->username:'', ['id'=> 'username', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="commisioningDate">@lang('label.COMMISSIONING_DATE')</label>
                                                <div class="input-group date datepicker2">
                                                    {!! Form::text('commisioning_date', !empty($studentInfoData->commisioning_date)?Helper::formatDate($studentInfoData->commisioning_date):null, ['id'=> 'commisioningDate', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                                    <span class="input-group-btn">
                                                        <button class="btn default reset-date" type="button" remove="commisioningDate">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                        <button class="btn default date-set" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="antiDateSeniority">@lang('label.ANTI_DATE_SENIORITY')</label>
                                                {!! Form::text('anti_date_seniority', !empty($studentInfoData->anti_date_seniority)?$studentInfoData->anti_date_seniority:null, ['id'=> 'antiDateSeniority', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="">@lang('label.COURSE_POSITION')</label>
                                                <div class="input-group">
                                                    {!! Form::text('course_position', !empty($studentInfoData->course_position)?$studentInfoData->course_position:null, ['id'=> 'coursePosition', 'class' => 'form-control integer-only text-right']) !!}
                                                    <span class="input-group-addon">@lang('label.OUT_OF')</span>
                                                    {!! Form::text('position_out', !empty($studentInfoData->position_out)?$studentInfoData->position_out:null, ['id'=> 'positionOut', 'class' => 'form-control integer-only text-right']) !!}
                                                </div>
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="nationality">@lang('label.NATIONALITY')</label>
                                                {!! Form::text('nationality', !empty($studentInfoData->nationality)?$studentInfoData->nationality:null, ['id'=> 'nationality', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="birthPlace">@lang('label.BIRTH_PLACE')</label>
                                                {!! Form::text('birth_place', !empty($studentInfoData->birth_place)?$studentInfoData->birth_place:null, ['id'=> 'birthPlace', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="religionId">@lang('label.RELIGION')</label>
                                                {!! Form::select('religion_id', $religionList, !empty($studentInfoData->religion_id)?$studentInfoData->religion_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'religionId']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="email">@lang('label.EMAIL')</label>
                                                {!! Form::email('email', !empty($studentInfoData->email)?$studentInfoData->email:null, ['id'=> 'email', 'class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6 border-left">

                                            <div class = "form-group">
                                                <label class = "control-label" for="appointmentId">@lang('label.APPT')<span class="text-danger"> *</span></label>
                                                {!! Form::select('appointment_id', $appointmentList, !empty($studentInfoData->student_appointment_id)?$studentInfoData->student_appointment_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'appointmentId']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="armsServiceId">@lang('label.ARMS_SERVICE')</label>
                                                {!! Form::select('arms_service_id', $armsServiceList, !empty($studentInfoData->arms_service_id)?$studentInfoData->arms_service_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'armsServiceId']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="unitId">@lang('label.UNIT')</label>
                                                {!! Form::select('unit_id', $unitList, !empty($studentInfoData->unit_id)?$studentInfoData->unit_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'unitId']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="formationId">@lang('label.FORMATION')</label>
                                                {!! Form::select('formation_id', $formationList, !empty($studentInfoData->formation_id)?$studentInfoData->formation_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'formationId']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="commandingOfficerName">@lang('label.COMMANDING_OFFICER_NAME')</label>
                                                {!! Form::text('commanding_officer_name', !empty($studentInfoData->commanding_officer_name)?$studentInfoData->commanding_officer_name:null, ['id'=> 'commandingOfficerName', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="commandingOfficerContactNo">@lang('label.COMMANDING_OFFICER_CONTACT')</label>
                                                {!! Form::text('commanding_officer_contact_no', !empty($studentInfoData->commanding_officer_contact_no)?$studentInfoData->commanding_officer_contact_no:null, ['id'=> 'commandingOfficerContactNo', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="">@lang('label.HEIGHT')</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">@lang('label.FT')</span>
                                                    {!! Form::text('ht_ft',!empty($studentInfoData->ht_ft) ? $studentInfoData->ht_ft : '', ['id'=> 'htFt', 'class' => 'form-control integer-only text-right']) !!}
                                                    <span class="input-group-addon">@lang('label.INCH')</span>
                                                    {!! Form::text('ht_inch', !empty($studentInfoData->ht_inch) ? $studentInfoData->ht_inch : '', ['id'=> 'htInch', 'class' => 'form-control integer-decimal-only text-right']) !!}
                                                </div>
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="weight">@lang('label.WEIGHT')</label>
                                                <div class="input-group">
                                                    {!! Form::text('weight', !empty($studentInfoData->weight)?$studentInfoData->weight:'', ['id'=> 'weight', 'class' => 'form-control integer-decimal-only text-right']) !!}
                                                    <span class="input-group-addon">@lang('label.KG')</span>
                                                </div>
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="medicalCategorize">@lang('label.MEDICAL_CATEGORIZE')</label>
                                                {!! Form::text('medical_categorize', !empty($studentInfoData->medical_categorize)?$studentInfoData->medical_categorize:null, ['id'=> 'medicalCategorize', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="phone">@lang('label.PHONE')</label>
                                                {!! Form::text('phone', !empty($studentInfoData->phone)?$studentInfoData->phone:null, ['id'=> 'phone', 'class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                        <div class = "col-md-12 margin-top-10">
                                            <a type="button" class = "btn  btn-circle green" id="editStudentProfileButton"> @lang('label.SAVE_CHANGES') </a>
                                            <a type="button" class = "btn  btn-circle default" href="{{ URL::to('studentProfile') }}"> @lang('label.CANCEL') </a>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <!--End::Edit basic student -->

                                    <!--Start::change photo -->
                                    <div id = "tab_2-2" class = "tab-pane">
                                        <div class="row margin-bottom-10">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.CHANGE_PHOTO')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editStudentProfilePhotoForm', 'enctype' => 'multipart/form-data']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}
                                        <div class = "form-group">
                                            <div class = "fileinput fileinput-new" data-provides = "fileinput">
                                                <div class = "fileinput-new thumbnail">
                                                    <div class="profile-userpic">
                                                        @if(!empty($studentInfoData->photo))
                                                        <img src="{{URL::to('/')}}/public/uploads/user/{{$studentInfoData->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
                                                             alt="{{ $studentInfoData->student_name }}" style="width: 150px;height: auto;" />
                                                        @else 
                                                        <img src="{{URL::to('/')}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
                                                             alt="{{ $studentInfoData->student_name}}" style="width: 150px;height: auto;" />
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class = "fileinput-preview fileinput-exists thumbnail" style = "max-width: 200px; max-height: 150px;"> </div>
                                                <div>
                                                    <span class = "btn default btn-file">
                                                        <span class = "fileinput-new"> @lang('label.SELECT_IMAGE') </span>
                                                        <span class = "fileinput-exists"> @lang('label.CHANGE') </span>
                                                        {!!Form::file('photo', ['id' => 'photo']) !!} 
                                                    </span>
                                                    <a href = "javascript:;" class = "btn default fileinput-exists" data-dismiss = "fileinput">  @lang('label.REMOVE')  </a>
                                                </div>
                                            </div>
                                            <div class = "clearfix margin-top-10">
                                                <span class = "label label-danger"> @lang('label.NOTE') </span>
                                                <span> @lang('label.USER_IMAGE_FOR_IMAGE_DESCRIPTION') </span>
                                            </div>
                                        </div>
                                        <div class = "margin-top-10">
                                            <a class = "btn  btn-circle green" id="editStudentProfilePhotoButton"> @lang('label.SUBMIT')  </a>
                                            <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL')  </a>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <!--End::change photo -->

                                    <!--Start::change password -->
                                    <div id = "tab_3-3" class = "tab-pane">
                                        <div class="row margin-bottom-10">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.CHANGE_PASSWORD')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(array('group' => 'form', 'id' => 'editStudentPasswordForm', 'class' => 'form-horizontal')) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}
                                        {{csrf_field()}}
                                        <div class="form-body">
                                            <div class="col-md-6">
                                                <!--div class="form-group">
                            <label class="control-label col-md-4" for="currentPassword">@lang('label.CURRENT_PASSWORD') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::password('current_password', ['id'=> 'currentPassword', 'class' => 'form-control']) !!} 
                                <span class="text-danger">{{ $errors->first('current_password') }}</span>
                                                                
                            </div>
                        </div-->

                                                <div class="form-group">
                                                    <label class="control-label" for="password">@lang('label.NEW_PASSWORD') :<span class="text-danger"> *</span></label>
                                                    {!! Form::password('password', ['id'=> 'password', 'class' => 'form-control']) !!} 
                                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                                    <div class="clearfix margin-top-10">
                                                        <span class="label label-danger">@lang('label.NOTE')</span>
                                                        @lang('label.COMPLEX_PASSWORD_INSTRUCTION')
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label" for="confPassword">@lang('label.CONF_PASSWORD') :<span class="text-danger"> *</span></label>
                                                    {!! Form::password('conf_password', ['id'=> 'confPassword', 'class' => 'form-control']) !!} 
                                                    <span class="text-danger">{{ $errors->first('conf_password') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="col-md-12">
                                                <button class="btn btn-circle green" type="submit" id="editStudentPasswordButton">
                                                    <i class="fa fa-check"></i> @lang('label.SUBMIT')
                                                </button>
                                                <a href="{{ URL::to('studentProfile') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>

                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <!--End::change password -->

                                    <!--Start::change family info -->
                                    <div id = "tab_4-4" class = "tab-pane">
                                        <div class="row margin-bottom-10">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_FAMILY_INFO')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editFamilyInfoForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}
                                        <div class="col-md-6">
                                            <div class = "form-group">
                                                <label class = "control-label" for="fatherName">@lang('label.FATHERS_NAME') <span class="text-danger"> *</span></label>
                                                {!! Form::text('father_name', !empty($studentInfoData->father_name)?$studentInfoData->father_name:null, ['id'=> 'fatherName', 'class' => 'form-control']) !!} 
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="fatherOccupation">@lang('label.OCCUPATION') <span class="text-danger"> *</span></label>
                                                {!! Form::text('father_occupation', !empty($studentInfoData->father_occupation)?$studentInfoData->father_occupation:null, ['id'=> 'fatherOccupation', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="fatherAddress">@lang('label.WORK_ADDRESS') <span class="text-danger"> *</span></label>
                                                {!! Form::text('father_address', !empty($studentInfoData->father_address)?$studentInfoData->father_address:null, ['id'=> 'fatherAddress', 'class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6 border-left">
                                            <div class = "form-group">
                                                <label class = "control-label" for="motherName">@lang('label.MOTHERS_NAME') <span class="text-danger"> *</span></label>
                                                {!! Form::text('mother_name', !empty($studentInfoData->mother_name)?$studentInfoData->mother_name:null, ['id'=> 'motherName', 'class' => 'form-control']) !!} 
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="motherOccupation">@lang('label.OCCUPATION') <span class="text-danger"> *</span></label>
                                                {!! Form::text('mother_occupation', !empty($studentInfoData->mother_occupation)?$studentInfoData->mother_occupation:null, ['id'=> 'motherOccupation', 'class' => 'form-control']) !!}
                                            </div>
                                            <div class = "form-group">
                                                <label class = "control-label" for="motherAddress">@lang('label.WORK_ADDRESS') <span class="text-danger"> *</span></label>
                                                {!! Form::text('mother_address', !empty($studentInfoData->mother_address)?$studentInfoData->mother_address:null, ['id'=> 'motherAddress', 'class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                        <div class = "col-md-12 margin-top-10">
                                            <a class = "btn  btn-circle green" id="editFamilyInfoButton"> @lang('label.SAVE_CHANGES') </a>
                                            <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                    <!--End::change family info -->

                                    <!--Start::change marital status -->
                                    <div id = "tab_5-5" class = "tab-pane">
                                        <div class="row margin-bottom-10">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_MARITAL_STATUS')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                {!! Form::open(['id' => 'editMaritialStatusForm']) !!}
                                                {!! Form::hidden('user_id', $studentInfoData->user_id) !!}
                                                <div class = "col-md-12">
                                                    <div class = "form-group">
                                                        <label class = "control-label" for="maritalStatus">@lang('label.MARITAL_STATUS') </label>
                                                        {!! Form::select('marital_status', $maritalStatusList, !empty($studentInfoData->marital_status)?$studentInfoData->marital_status:'0',  ['class' => 'form-control js-source-states', 'id' => 'maritalStatus']) !!}
                                                    </div>
                                                </div>
                                                <div id="spouseInfoDiv">
                                                    <div class = "col-md-12">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="marriageDate">@lang('label.DATE_OF_MARRIAGE')<span class="text-danger"> *</span></label>
                                                            <div class="input-group date datepicker2">
                                                                {!! Form::text('date_of_marriage', !empty($studentInfoData->date_of_marriage)?Helper::formatDate($studentInfoData->date_of_marriage):'', ['id'=> 'marriageDate', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
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
                                                    <div class = "col-md-6">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseName">@lang('label.SPOUSE_NAME')<span class="text-danger"> *</span> </label>
                                                            {!! Form::text('spouse_name', !empty($studentInfoData->spouse_name)?$studentInfoData->spouse_name:null, ['id'=> 'spouseName', 'class' => 'form-control']) !!} 
                                                        </div>
                                                    </div>
                                                    <div class = "col-md-6">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseOccupation">@lang('label.OCCUPATION')</label>
                                                            {!! Form::text('spouse_occupation', !empty($studentInfoData->spouse_occupation)?$studentInfoData->spouse_occupation:null, ['id'=> 'spouseOccupation', 'class' => 'form-control']) !!}
                                                        </div>
                                                    </div>
                                                    <div class = "col-md-12">
                                                        <div class = "form-group">
                                                            <label class = "control-label" for="spouseWorkAddress">@lang('label.WORK_ADDRESS')</label>
                                                            {!! Form::text('spouse_work_address', !empty($studentInfoData->spouse_work_address)?$studentInfoData->spouse_work_address:null, ['id'=> 'spouseWorkAddress', 'class' => 'form-control']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class = "col-md-12 margin-top-10">
                                                    <a type="button" class = "btn  btn-circle green" id="editMaritialStatusButton"> @lang('label.SAVE_CHANGES') </a>
                                                    <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!--Start::change marital status -->

                                </div>
                            </div>
                            <!--end col-md-9 -->
                        </div>
                    </div>
                    <!--end tab-pane-->
                    <div class = "tab-pane" id = "tab_1_3">
                        <div class = "row">
                            <div class = "col-md-3">
                                <ul class = "ver-inline-menu tabbable margin-bottom-10">
                                    <li class = "active">
                                        <a data-toggle = "tab" href = "#tab_1">
                                            <i class="fa fa fa-users green-color-style-color"></i>@lang('label.BROTHER_SISTER') </a>
                                        <span class = "after"> </span>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_2">
                                            <i class="fa fa-map-marker"></i> @lang('label.PERMANENT_ADDRESS') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_3">
                                            <i class="fa fa-graduation-cap"></i> @lang('label.CIVIL_EDUCATION') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_4">
                                            <i class="fa fa-cogs"></i> @lang('label.SERVICE_RECORD') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_5">
                                            <i class="fa fa-trophy"></i></i> @lang('label.AWARD_RECORD') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_6">
                                            <i class="fa fa-gavel"></i></i> @lang('label.PUNISHMENT_RECORD') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_7">
                                            <i class="fa fa-user"></i></i> @lang('label.DEFENCE_RELATIVE') </a>
                                    </li>

                                    <li>
                                        <a data-toggle = "tab" href = "#tab_8">
                                            <i class="fa fa-user"></i></i> @lang('label.NEXT_OF_KIN') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_9">
                                            <i class="fa fa-plus-square"></i></i> @lang('label.MEDICAL_DETAILS') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_10">
                                            <i class="fa fa-user"></i></i> @lang('label.WINTER_COLLECTIVE_TRAINING') </a>
                                    </li>
                                    <li>
                                        <a data-toggle = "tab" href = "#tab_11">
                                            <i class="fa fa-cog"></i> @lang('label.OTHERS') </a>
                                    </li>
                                </ul>
                            </div>
                            <div class = "col-md-9">
                                <div class = "tab-content">
                                    <div id = "tab_1" class = "tab-pane active">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_BROTHER_SISTER_INFO')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editStudentBrotherSisterForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter ">@lang('label.NAME')<span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter ">@lang('label.RELATION')<span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.AGE')</th>
                                                                <th scope="col" class="vcenter ">@lang('label.OCCUPATION')</th>
                                                                <th scope="col" class="vcenter">@lang('label.ADDRESS')</th>
                                                                <th scope="col" class="vcenter text-center"></th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $bSl = 1;
                                                        $bsKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $brotherSister = !empty($brotherSisterInfoData) ? json_decode($brotherSisterInfoData->brother_sister_info, true) : null;
                                                        //echo '<pre>';        print_r($brotherSister);exit;
                                                        ?>
                                                        @if(!empty($brotherSister))
                                                        @foreach($brotherSister as $bsVar => $brotherSisterInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-brother-sister-sl text">{{ $bSl }}</td>
                                                                <td class="vcenter text-center">
                                                                    {!! Form::text('brother_sister['.$bsVar.'][name]', !empty($brotherSisterInfo['name'])?$brotherSisterInfo['name']:null, ['id'=> 'brotherSister['.$bsVar.'][name]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-center">
                                                                    {!! Form::text('brother_sister['.$bsVar.'][relation]', !empty($brotherSisterInfo['relation'])?$brotherSisterInfo['relation']:null, ['id'=> 'brotherSister['.$bsVar.'][relation]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-center">
                                                                    {!! Form::text('brother_sister['.$bsVar.'][age]', !empty($brotherSisterInfo['age'])?$brotherSisterInfo['age']:null, ['id'=> 'brotherSister['.$bsVar.'][age]', 'class' => 'form-control integer-only text-right']) !!}
                                                                </td>
                                                                <td class="vcenter text-center">
                                                                    {!! Form::text('brother_sister['.$bsVar.'][occupation]', !empty($brotherSisterInfo['occupation'])?$brotherSisterInfo['occupation']:null, ['id'=> 'brotherSister['.$bsVar.'][occupation]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-center">
                                                                    {!! Form::text('brother_sister['.$bsVar.'][address]',!empty($brotherSisterInfo['address'])?$brotherSisterInfo['address']:null, ['id'=> 'brotherSister['.$bsVar.'][address]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                @if($bSl == 1)
                                                                <td class="vcenter text-center">
                                                                    <a class="btn btn-green-seagreen add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                </td>
                                                                @else
                                                                <td class="vcenter">
                                                                    <a class="btn badge-red-intense remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
                                                                </td>
                                                                @endif
                                                            </tr>
                                                        </tbody>
                                                        <?php $bSl++; ?>
                                                        @endforeach
                                                        @else
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-brother-sister-sl">1</td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('brother_sister['.$bsKey.'][name]', null, ['id'=> 'brotherSister['.$bsKey.'][name]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('brother_sister['.$bsKey.'][relation]', null, ['id'=> 'brotherSister['.$bsKey.'][relation]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-center">
                                                                    {!! Form::text('brother_sister['.$bsKey.'][age]', null, ['id'=> 'brotherSister['.$bsKey.'][age]', 'class' => 'form-control integer-only text-right']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('brother_sister['.$bsKey.'][occupation]', null, ['id'=> 'brotherSister['.$bsKey.'][occupation]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('brother_sister['.$bsKey.'][address]', null, ['id'=> 'brotherSister['.$bsKey.'][address]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-center">
                                                                    <a class="btn btn-green-seagreen add-btn" id="1" type="button" />
                                                                    <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif
                                                        <tbody  id="brotherSisterInputRow">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 margin-top-10">
                                                <a class = "btn  btn-circle green" id="editStudentBrotherSisterButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}
                                    </div>
                                    <div id="tab_2" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_PERMANENT_ADDRESS')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editStudentPermanentAddressForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}
                                        <div class="row margin-top-10">
                                            <div class="col-md-6">
                                                <div class = "form-group">
                                                    <label class = "control-label" for="divisionId">@lang('label.DIVISION') <span class="text-danger"> *</span></label>
                                                    {!! Form::select('division_id', $divisionList, !empty($addressInfo->division_id)?$addressInfo->division_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'divisionId']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="districtId">@lang('label.DISTRICT') <span class="text-danger"> *</span></label>
                                                    {!! Form::select('district_id', $districtList, !empty($addressInfo->district_id)?$addressInfo->district_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'districtId']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="thanaId">@lang('label.THANA') <span class="text-danger"> *</span></label>
                                                    {!! Form::select('thana_id', $thanaList, !empty($addressInfo->thana_id)?$addressInfo->thana_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'thanaId']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="addressDetails">@lang('label.ADDRESS')</label>
                                                    {!! Form::text('address_details', !empty($addressInfo->address_details)?$addressInfo->address_details:null,  ['class' => 'form-control', 'id' => 'addressDetails']) !!}
                                                </div>

                                                <div class = "col-md-12 margin-top-10">
                                                    <a class = "btn  btn-circle green" id="editStudentPermanentAddressButton"> @lang('label.SAVE_CHANGES') </a>
                                                    <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}

                                    </div>
                                    <!--START::Student Civil Education-->
                                    <div id="tab_3" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_CIVIL_EDUCATION')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editStudentCivilEducationForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.INSTITUTE_NAME') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.EXAMINATION') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.RESULT') <span class="text-danger"> *</th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.YEAR') <span class="text-danger"> *</th>
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
                                                                <td class="vcenter text-center">
                                                                    {!! Form::text('civil_education['.$ceVar.'][institute_name]', $civilEducation[$ceVar]['institute_name'], ['id'=> 'civilEducation['.$ceVar.'][institute_name]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('civil_education['.$ceVar.'][examination]', $civilEducation[$ceVar]['examination'], ['id'=> 'civilEducation['.$ceVar.'][examination]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-right">
                                                                    {!! Form::text('civil_education['.$ceVar.'][result]', $civilEducation[$ceVar]['result'], ['id'=> 'civilEducation['.$ceVar.'][result]', 'class' => 'form-control integer-only text-right']) !!}
                                                                </td>
                                                                <td class="vcenter text-right">
                                                                    {!! Form::text('civil_education['.$ceVar.'][year]', $civilEducation[$ceVar]['year'], ['id'=> 'civilEducation['.$ceVar.'][year]', 'class' => 'form-control text-right']) !!}
                                                                </td>
                                                                @if($cSl == 1)
                                                                <td class="vcenter text-center">
                                                                    <a class="btn btn-green-seagreen civil-education-add-btn" id="" type="button" ><i class="fa fa-plus"></i></a>
                                                                </td>
                                                                @else
                                                                <td class="vcenter text-center">
                                                                    <a class="btn badge-red-intense civil-education-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
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
                                                                <td class="vcenter">
                                                                    {!! Form::text('civil_education['.$ceKey.'][institute_name]', null, ['id'=> 'civilEducation['.$ceKey.'][institute_name]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('civil_education['.$ceKey.'][examination]', null, ['id'=> 'civilEducation['.$ceKey.'][examination]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-right">
                                                                    {!! Form::text('civil_education['.$ceKey.'][result]', null, ['id'=> 'civilEducation['.$ceKey.'][result]', 'class' => 'form-control integer-decimal-only text-right']) !!}
                                                                </td>
                                                                <td class="vcenter text-right">
                                                                    {!! Form::text('civil_education['.$ceKey.'][year]', null, ['id'=> 'civilEducation['.$ceKey.'][year]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-center">
                                                                    <a class="btn btn-green-seagreen civil-education-add-btn" id="1" type="button" />
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
                                            <div class = "col-md-12 margin-top-10">
                                                <a class = "btn  btn-circle green" id="editStudentCivilEducationButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::Student Civil Education-->

                                    <!--START::Student service Record-->
                                    <div id="tab_4" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_SERVICE_RECORD')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editStudentServiceRecordForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center width-50">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.UNIT') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.APPOINTMENT') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.YEAR') <span class="text-danger"> *</th>
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
                                                                <td class="vcenter width-300">
                                                                    {!! Form::select('service_record['.$srVar.'][unit]', $unitList, !empty($serviceRecordInfo['unit'])?$serviceRecordInfo['unit']:'0',  ['class' => 'form-control js-source-states width-inherit', 'id' => 'serviceRecord['.$srVar.'][unit]']) !!}
                                                                </td>
                                                                <td class="vcenter width-300">
                                                                    {!! Form::select('service_record['.$srVar.'][appointment]', $appointmentList, !empty($serviceRecordInfo['appointment'])?$serviceRecordInfo['appointment']:'0',  ['class' => 'form-control js-source-states width-inherit', 'id' => 'serviceRecord['.$srVar.'][appointment]']) !!}
                                                                <td class="vcenter text-right width-100">
                                                                    {!! Form::text('service_record['.$srVar.'][year]', !empty($serviceRecordInfo['year'])?$serviceRecordInfo['year']:'', ['id'=> 'serviceRecord['.$srVar.'][year]', 'class' => 'form-control text-right width-inherit']) !!}
                                                                </td>

                                                                <td class="vcenter text-center width-50 text-center">
                                                                    @if($sSl == 1)
                                                                    <a class="btn btn-green-seagreen service-record-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn badge-red-intense service-record-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
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
                                                                <td class="vcenter">

                                                                    {!! Form::select('service_record['.$srKey.'][unit]', $unitList, null,  ['class' => 'form-control js-source-states', 'id' => 'serviceRecord['.$srKey.'][unit]']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::select('service_record['.$srKey.'][appointment]', $appointmentList, null,  ['class' => 'form-control js-source-states', 'id' => 'serviceRecord['.$srKey.'][appointment]']) !!}
                                                                </td>
                                                                <td class="vcenter text-right width-100">
                                                                    {!! Form::text('service_record['.$srKey.'][year]', null, ['id'=> 'serviceRecord['.$srKey.'][year]', 'class' => 'form-control integer-only width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn btn-green-seagreen service-record-add-btn" id="1" type="button" />
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
                                            <div class = "col-md-12 margin-top-10">
                                                <a class = "btn  btn-circle green" id="editStudentServiceRecordButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::Student Service Record-->

                                    <!--START::Student award Record-->
                                    <div id="tab_5" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_AWARD_RECORD')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editStudentAwardRecordForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center width-50">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.AWARD') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.REASON') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.YEAR') <span class="text-danger"> *</th>
                                                                <th scope="col" class="vcenter text-center"></th>
                                                            </tr> 
                                                        </thead>
                                                        <?php
                                                        $aSl = 1;
                                                        $arKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $awardRecord = !empty($awardRecordInfoData) ? json_decode($awardRecordInfoData->award_record_info, true) : null;
                                                        //echo '<pre>';        print_r(json_decode($serviceRecordInfoData->service_record_info, true));exit;
                                                        ?>
                                                        @if(!empty($awardRecord))
                                                        @foreach($awardRecord as $arVar => $awardRecordInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-award-record-sl text">{{ $aSl }}</td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('award_record['.$arVar.'][award]', !empty($awardRecordInfo['award'])?$awardRecordInfo['award']:null,  ['class' => 'form-control width-inherit', 'id' => 'awardRecord['.$arVar.'][award]']) !!}
                                                                </td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('award_record['.$arVar.'][reason]',!empty($awardRecordInfo['reason'])?$awardRecordInfo['reason']:null,  ['class' => 'form-control width-inherit', 'id' => 'awardRecord['.$arVar.'][reason]']) !!}
                                                                <td class="vcenter text-right width-100">
                                                                    {!! Form::text('award_record['.$arVar.'][year]', !empty($awardRecordInfo['year'])?$awardRecordInfo['year']:null, ['id'=> 'awardRecord['.$arVar.'][year]', 'class' => 'form-control text-right width-inherit']) !!}
                                                                </td>

                                                                <td class="vcenter text-center">
                                                                    @if($aSl == 1)
                                                                    <a class="btn btn-green-seagreen award-record-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn badge-red-intense award-record-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                        <?php $aSl++; ?>
                                                        @endforeach
                                                        @else
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-award-record-sl">1</td>
                                                                <td class="vcenter width-250">

                                                                    {!! Form::text('award_record['.$arKey.'][award]', null,  ['class' => 'form-control width-inherit', 'id' => 'award_record['.$arKey.'][award]']) !!}
                                                                </td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('award_record['.$arKey.'][reason]', null,  ['class' => 'form-control width-inherit', 'id' => 'award_record['.$arKey.'][reason]']) !!}
                                                                </td>
                                                                <td class="vcenter text-right width-100">
                                                                    {!! Form::text('award_record['.$arKey.'][year]', null, ['id'=> 'award_record['.$arKey.'][year]', 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn btn-green-seagreen award-record-add-btn" id="1" type="button" />
                                                                    <i class="fa fa-plus"></i>
                                                                    </a>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif
                                                        <tbody  id="awardRecordInputRow">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class = "col-md-12 margin-top-10">
                                                <a class = "btn  btn-circle green" id="editStudentAwardRecordButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::Student Award Record-->

                                    <!--START::Student punishment Record-->
                                    <div id="tab_6" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_PUNISHMENT_RECORD')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editStudentPunishmentRecordForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="info">
                                                                <th scope="col" class="vcenter text-center width-50">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.PUNISHMENT') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.REASON') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.YEAR') <span class="text-danger"> *</th>
                                                                <th scope="col" class="vcenter text-center"></th>
                                                            </tr> 
                                                        </thead>
                                                        <?php
                                                        $pSl = 1;
                                                        $prKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $punishmentRecord = !empty($punishmentRecordInfoData) ? json_decode($punishmentRecordInfoData->punishment_record_info, true) : null;
                                                        //echo '<pre>';        print_r(json_decode($punishmentRecordInfoData->punishment_record_info, true));exit;
                                                        ?>
                                                        @if(!empty($punishmentRecord))
                                                        @foreach($punishmentRecord as $prVar => $punishmentRecordInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-punishment-record-sl text">{{ $pSl }}</td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('punishment_record['.$prVar.'][punishment]', !empty($punishmentRecordInfo['punishment'])?$punishmentRecordInfo['punishment']:null,  ['class' => 'form-control width-inherit', 'id' => 'punishmentRecord['.$prVar.'][punishment]']) !!}
                                                                </td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('punishment_record['.$prVar.'][reason]',!empty($punishmentRecordInfo['reason'])?$punishmentRecordInfo['reason']:null,  ['class' => 'form-control width-inherit', 'id' => 'punishmentRecord['.$prVar.'][reason]']) !!}
                                                                <td class="vcenter text-right width-100">
                                                                    {!! Form::text('punishment_record['.$prVar.'][year]', !empty($punishmentRecordInfo['year'])?$punishmentRecordInfo['year']:null, ['id'=> 'punishmentRecord['.$prVar.'][year]', 'class' => 'form-control text-right width-inherit']) !!}
                                                                </td>

                                                                <td class="vcenter text-center width-50">
                                                                    @if($pSl == 1)
                                                                    <a class="btn btn-green-seagreen punishment-record-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn badge-red-intense punishment-record-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
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
                                                                <td class="vcenter width-250">

                                                                    {!! Form::text('punishment_record['.$prKey.'][punishment]', null,  ['class' => 'form-control width-inherit', 'id' => 'punishment_record['.$prKey.'][punishment]']) !!}
                                                                </td>
                                                                <td class="vcenter width-250">
                                                                    {!! Form::text('punishment_record['.$prKey.'][reason]', null,  ['class' => 'form-control width-inherit', 'id' => 'punishment_record['.$prKey.'][reason]']) !!}
                                                                </td>
                                                                <td class="vcenter text-right width-100">
                                                                    {!! Form::text('punishment_record['.$prKey.'][year]', null, ['id'=> 'punishment_record['.$prKey.'][year]', 'class' => 'form-control width-inherit']) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn btn-green-seagreen punishment-record-add-btn" id="1" type="button" />
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
                                            <div class = "col-md-12 margin-top-10">
                                                <a class = "btn  btn-circle green" id="editStudentPunishmentRecordButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::Student Punshment Record-->

                                    <!--START::Student Defence Relative-->
                                    <div id="tab_7" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_DEFENCE_RELATIVE')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editStudentDefenceRelativeForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="info">

                                                                <th scope="col" class="vcenter text-center width-50">@lang('label.SERIAL')</th>
                                                                <th scope="col" class="vcenter">@lang('label.COURSE') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.INSTITUTE_NAME') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter">@lang('label.GRADING') <span class="text-danger"> *</span></th>
                                                                <th scope="col" class="vcenter text-center">@lang('label.YEAR') <span class="text-danger"> *</th>
                                                                <th scope="col" class="vcenter text-center"></th>
                                                            </tr> 
                                                        </thead>
                                                        <?php
                                                        $dSl = 1;
                                                        $drKey = uniqid();
                                                        ?>

                                                        <?php
                                                        $defenceRelative = !empty($defenceRelativeInfoData) ? json_decode($defenceRelativeInfoData->student_relative_info, true) : null;
                                                        //echo '<pre>';        print_r(json_decode($punishmentRecordInfoData->punishment_record_info, true));exit;
                                                        ?>
                                                        @if(!empty($defenceRelative))
                                                        @foreach($defenceRelative as $drVar => $defenceRelativeInfo)
                                                        <tbody>
                                                            <tr>
                                                                <td class="vcenter text-center initial-defence-relative-sl text">{{ $dSl }}</td>
                                                                <td class="vcenter width-200">
                                                                    {!! Form::select('defence_relative['.$drVar.'][course]', $courseList, !empty($defenceRelativeInfo['course'])?$defenceRelativeInfo['course']:null,  ['class' => 'form-control js-source-states width-inherit', 'id' => 'defenceRelative['.$drVar.'][course]']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('defence_relative['.$drVar.'][institute]', !empty($defenceRelativeInfo['institute'])?$defenceRelativeInfo['institute']:null,  ['class' => 'form-control', 'id' => 'defenceRelative['.$drVar.'][institute]']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('defence_relative['.$drVar.'][grading]',!empty($defenceRelativeInfo['grading'])?$defenceRelativeInfo['grading']:null,  ['class' => 'form-control', 'id' => 'defenceRelative['.$drVar.'][grading]']) !!}
                                                                <td class="vcenter text-right">
                                                                    {!! Form::text('defence_relative['.$drVar.'][year]', !empty($defenceRelativeInfo['year'])?$defenceRelativeInfo['year']:null, ['id'=> 'defenceRelative['.$drVar.'][year]', 'class' => 'form-control text-right']) !!}
                                                                </td>

                                                                <td class="vcenter text-center width-50">
                                                                    @if($dSl == 1)
                                                                    <a class="btn btn-green-seagreen defence-relative-add-btn" id="1" type="button" ><i class="fa fa-plus"></i></a>
                                                                    @else
                                                                    <a class="btn badge-red-intense defence-relative-remove-Btn" id="" type="button" /><i class="fa fa-close"></i></a>
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
                                                                <td class="vcenter width-200">
                                                                    {!! Form::select('defence_relative['.$drKey.'][course]', $courseList, null, ['class' => 'form-control js-source-states width-inherit', 'id' => 'defenceRelative['.$drKey.'][course]']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('defence_relative['.$drKey.'][institute]', null,  ['class' => 'form-control', 'id' => 'defenceRelative['.$drKey.'][institute]']) !!}
                                                                </td>
                                                                <td class="vcenter">
                                                                    {!! Form::text('defence_relative['.$drKey.'][grading]', null,  ['class' => 'form-control', 'id' => 'defenceRelative['.$drKey.'][grading]']) !!}
                                                                </td>
                                                                <td class="vcenter text-right">
                                                                    {!! Form::text('defence_relative['.$drKey.'][year]', null, ['id'=> 'punishment_record['.$drKey.'][year]', 'class' => 'form-control']) !!}
                                                                </td>
                                                                <td class="vcenter text-center width-50">
                                                                    <a class="btn btn-green-seagreen defence-relative-add-btn" id="1" type="button" />
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
                                            <div class = "col-md-12 margin-top-10">
                                                <a class = "btn  btn-circle green" id="editStudentDefenceRelativeButton"> @lang('label.SAVE_CHANGES') </a>
                                                <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::Student Defence Relative-->

                                    <!--Start::Student Next of Kin -->
                                    <div id="tab_8" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_NEXT_OF_KIN')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editStudentNextKinForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}
                                        <div class="row margin-top-10">
                                            <div class="col-md-6">
                                                <div class = "form-group">
                                                    <label class = "control-label" for="kinName">@lang('label.NAME') <span class="text-danger"> *</span></label>
                                                    {!! Form::text('kin_name', !empty($nextKinAddressInfo)?$nextKinAddressInfo->name:null, ['id'=> 'kinName', 'class' => 'form-control']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="kinRelation">@lang('label.RELATION') <span class="text-danger"> *</span></label>
                                                    {!! Form::text('kin_relation',  !empty($nextKinAddressInfo)?$nextKinAddressInfo->relation:null, ['id'=> 'kinRelation', 'class' => 'form-control']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="kinDivisionId">@lang('label.DIVISION') <span class="text-danger"> *</span></label>
                                                    {!! Form::select('kin_division_id', $divisionList, !empty($nextKinAddressInfo->division_id)?$nextKinAddressInfo->division_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'kinDivisionId']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="kinDistrictId">@lang('label.DISTRICT') <span class="text-danger"> *</span></label>
                                                    {!! Form::select('kin_district_id', $nextKinDistrictList, !empty($nextKinAddressInfo->district_id)?$nextKinAddressInfo->district_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'kinDistrictId']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="kinThanaId">@lang('label.THANA') <span class="text-danger"> *</span></label>
                                                    {!! Form::select('kin_thana_id', $nextKinThanaList, !empty($nextKinAddressInfo->thana_id)?$nextKinAddressInfo->thana_id:'0',  ['class' => 'form-control js-source-states', 'id' => 'kinThanaId']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="addressDetails">@lang('label.ADDRESS')</label>
                                                    {!! Form::text('kin_address_details', !empty($nextKinAddressInfo->address_details)?$nextKinAddressInfo->address_details:null,  ['class' => 'form-control', 'id' => 'addressDetails']) !!}
                                                </div>

                                                <div class = "col-md-12 margin-top-10">
                                                    <a class = "btn  btn-circle green" id="editStudentNextKinButton"> @lang('label.SAVE_CHANGES') </a>
                                                    <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}

                                    </div>
                                    <!--End::Student Next of Kin -->

                                    <!--Start::Student medical details -->
                                    <div id="tab_9" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_MEDICAL_DETAILS')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editStudentMedicalDetailsForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}
                                        <div class="row margin-top-10">
                                            <div class="col-md-6">
                                                <div class = "form-group">
                                                    <label class = "control-label" for="category">@lang('label.CATEGORY') <span class="text-danger"> *</span></label>
                                                    {!! Form::text('category', !empty($studentMedicalDetails->category)?$studentMedicalDetails->category:null, ['id'=> 'category', 'class' => 'form-control']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="bloodGroup">@lang('label.BlOOD_GROUP') <span class="text-danger"> *</span></label>
                                                    {!! Form::text('blood_group',  !empty($studentMedicalDetails->blood_group)?$studentMedicalDetails->blood_group:null, ['id'=> 'bloodGroup', 'class' => 'form-control']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="dateOfBirth">@lang('label.DATE_OF_BIRTH')  <span class="text-danger"> *</span></label>
                                                    <div class="input-group date datepicker2">
                                                        {!! Form::text('date_of_birth', !empty($studentMedicalDetails->date_of_birth)?Helper::formatDate($studentMedicalDetails->date_of_birth):null, ['id'=> 'dateOfBirth', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                                        <span class="input-group-btn">
                                                            <button class="btn default reset-date" type="button" remove="commisioningDate">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                            <button class="btn default date-set" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class = "form-group">
                                                    <label class = "control-label" for="">@lang('label.HEIGHT')</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">@lang('label.FT')</span>
                                                        {!! Form::text('ht_ft',!empty($studentInfoData->ht_ft) ? $studentInfoData->ht_ft : '', ['id'=> 'htFtMedical', 'class' => 'form-control integer-only text-right']) !!}
                                                        <span class="input-group-addon">@lang('label.INCH')</span>
                                                        {!! Form::text('ht_inch', !empty($studentInfoData->ht_inch) ? $studentInfoData->ht_inch : '', ['id'=> 'htInchMedical', 'class' => 'form-control integer-decimal-only text-right']) !!}
                                                    </div>
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="weight">@lang('label.WEIGHT')</label>
                                                    <div class="input-group">
                                                        {!! Form::text('weight', !empty($studentInfoData->weight)?$studentInfoData->weight:null, ['id'=> 'weightMedical', 'class' => 'form-control integer-decimal-only text-right']) !!}
                                                        <span class="input-group-addon">@lang('label.KG')</span>
                                                    </div>
                                                </div>
                                                <div class = "form-group">
                                                    <?php
                                                    $bmi = '';
                                                    if (!empty($studentMedicalDetails->over_under_weight)) {
                                                        if ($studentMedicalDetails->over_under_weight == '1') {
                                                            $bmi = __('label.UNDER');
                                                        } elseif ($studentMedicalDetails->over_under_weight == '2') {
                                                            $bmi = __('label.NORMAL');
                                                        } elseif ($studentMedicalDetails->over_under_weight == '3') {
                                                            $bmi = __('label.OVER');
                                                        }
                                                    }
                                                    ?>
                                                    <label class = "control-label" for="overUnderWeight">@lang('label.OVER_UNDER_WEIGHT')</label>
                                                    {!! Form::text('bmi', $bmi, ['id'=> 'overUnderWeight', 'class' => 'form-control']) !!}
                                                    {!! Form::hidden('over_under_weight', !empty($studentMedicalDetails->over_under_weight)?$studentMedicalDetails->over_under_weight:'0', ['id'=> 'overUnderWeightValue']) !!}
                                                </div>
                                                <div class = "form-group">
                                                    <label class = "control-label" for="anyDisease">@lang('label.ANY_DISEASE')</label>
                                                    {!! Form::text('any_disease', !empty($studentMedicalDetails->any_disease)?$studentMedicalDetails->any_disease:null, ['id'=> 'anyDisease', 'class' => 'form-control']) !!}
                                                </div>

                                                <div class = "col-md-12 margin-top-10">
                                                    <a class = "btn  btn-circle green" id="editStudentMedicalDetailsButton"> @lang('label.SAVE_CHANGES') </a>
                                                    <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}

                                    </div>
                                    <!--End::Student medical details -->


                                    <!--START::Student Winter Collective Training-->
                                    <div id="tab_10" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_WINTER_COLLECTIVE_TRAINING')</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {!! Form::open(['id' => 'editStudentWinterTrainingForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}

                                        <div class="row margin-top-10">

                                            <div class = "col-md-4 margin-top-4">
                                                <label>@lang('label.NUMBER_OF_WINTER_COLLECTIVE_EXERCISE') <span class="text-danger"> *</span></label>
                                            </div>
                                            <div class="col-md-3">
                                                {!! Form::text('participated_no', !empty($studentWinterTrainingInfoData->participated_no)?$studentWinterTrainingInfoData->participated_no:null, ['id'=> 'participatedNo', 'class' => 'form-control integer-only']) !!}
                                            </div>
                                            <?php
                                            $winterTraining = !empty($studentWinterTrainingInfoData) ? json_decode($studentWinterTrainingInfoData->training_info, true) : null;
                                            //echo '<pre>';        print_r(json_decode($punishmentRecordInfoData->punishment_record_info, true));exit;

                                            $wSl = 1;
                                            $drKey = uniqid();
                                            ?>
                                            @if(!empty($winterTraining))
                                            <div id="winterTrainingRowAdd">
                                                <div class="col-md-12 margin-top-10">
                                                    <div class="table-responsive" id="winterTrainingRowAdd">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr class="info">
                                                                    <th scope="col" class="vcenter text-center">@lang('label.SERIAL') </th>
                                                                    <th scope="col" class="vcenter">@lang('label.EXERCISE') <span class="text-danger"> *</span></th>
                                                                    <th scope="col" class="vcenter text-center">@lang('label.YEAR') <span class="text-danger"> *</span></th>
                                                                    <th scope="col" class="vcenter">@lang('label.PLACE')</th>
                                                                </tr>
                                                            </thead>
                                                            @foreach($winterTraining as $wtVar => $studentWinterTrainingInfo)
                                                            <tbody>
                                                            <td class="vcenter text-center">{{ $wSl }}</td>
                                                            <td class="vcenter">
                                                                {!! Form::text('winter_training['.$wtVar.'][exercise]', !empty($studentWinterTrainingInfo['exercise'])?$studentWinterTrainingInfo['exercise']:null,  ['class' => 'form-control', 'id' => 'winterTraining['.$wtVar.'][exercise]']) !!}
                                                            </td>
                                                            <td class="vcenter text-right">
                                                                {!! Form::text('winter_training['.$wtVar.'][year]', !empty($studentWinterTrainingInfo['year'])?$studentWinterTrainingInfo['year']:null,  ['class' => 'form-control text-right', 'id' => 'winterTraining['.$wtVar.'][year]']) !!}
                                                            </td>
                                                            <td class="vcenter">
                                                                {!! Form::text('winter_training['.$wtVar.'][place]', !empty($studentWinterTrainingInfo['place'])?$studentWinterTrainingInfo['place']:null,  ['class' => 'form-control', 'id' => 'winterTraining['.$wtVar.'][place]']) !!}
                                                            </td>

                                                            </tbody>
                                                            <?php $wSl++; ?>
                                                            @endforeach
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class = "col-md-12 margin-top-10">
                                                    <a class = "btn  btn-circle green" id="editStudentWinterTrainingButton"> @lang('label.SAVE_CHANGES') </a>
                                                    <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                                </div>
                                            </div>
                                            @endif
                                            <div id="winterTrainingRowAdd">

                                            </div>


                                        </div>

                                        {!! Form::close() !!}

                                    </div>
                                    <!--END::Student Winter Collective Training-->

                                    <!--Start:: Student others-->
                                    <div id="tab_11" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="col-md-12 border-bottom-1-green-seagreen">
                                                    <strong>@lang('label.EDIT_OTHERS')</strong>
                                                </span>
                                            </div>
                                        </div>
                                        {!! Form::open(['id' => 'editStudentOthersForm']) !!}
                                        {!! Form::hidden('user_id', $studentInfoData->user_id) !!}

                                        <div class="row margin-top-10">
                                            <div class="col-md-6">
                                                <div class = "form-group">
                                                    @php
                                                    $countryVisited =!empty($othersInfoData->visited_countries_id)? json_decode($othersInfoData->visited_countries_id, true):null;
                                                    @endphp
                                                    <label class = "control-label" for="visitedCountriesId">@lang('label.VISITED_COUNTRIES')</label>
                                                    {!! Form::select('visited_countries_id[]', $countriesVisitedList, $countryVisited,  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'visitedCountriesId']) !!}
                                                </div>

                                                <div class = "form-group">
                                                    <label class = "control-label" for="specialQuality">@lang('label.SPECIAL_QUALITY')</label>
                                                    {!! Form::text('special_quality', !empty($othersInfoData->special_quality)?$othersInfoData->special_quality:null,  ['class' => 'form-control', 'id' => 'specialQuality']) !!}
                                                </div>

                                                <div class = "form-group">
                                                    <label class = "control-label" for="swimming">@lang('label.SWIMMING') <span class="text-danger"> *</span></label>
                                                    {!! Form::select('swimming', $swimmingList, !empty($othersInfoData->swimming)?$othersInfoData->swimming:'0',  ['class' => 'form-control js-source-states', 'id' => 'swimming']) !!}
                                                </div>

                                                <div class = "form-group">
                                                    <label class = "control-label" for="professionalComputer">@lang('label.PROFESSIONAL_COMPUTER') <span class="text-danger"> *</span>&nbsp;&nbsp;</label>
                                                    {!! Form::radio('professional_computer', '1', (!empty($othersInfoData->professional_computer) && $othersInfoData->professional_computer == 1)?true:false, ['id' => 'professionalComputerYes']) !!}&nbsp;@lang('label.YES')&nbsp;&nbsp;
                                                    {!! Form::radio('professional_computer', '2', ((!empty($othersInfoData->professional_computer) && $othersInfoData->professional_computer != 1) || empty($othersInfoData->professional_computer))?true:false, ['id' => 'professionalComputerNo']) !!}&nbsp;@lang('label.NO')&nbsp;&nbsp;
                                                </div>

                                            </div>

                                        </div>

                                        <div class = "col-md-12 margin-top-10">
                                            <a class = "btn  btn-circle green" id="editStudentOthersButton"> @lang('label.SAVE_CHANGES') </a>
                                            <a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>
                                        </div>
                                        {!! Form::close() !!}

                                    </div>
                                    <!--End:: Student others-->



                                </div>
                            </div>
                        </div>
                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type = "text/javascript">
        $(function () {
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
            //START:: Change Basic Information
            //        editStudentProfilePhotoButton


            $(document).on('click', '#editStudentProfileButton', function (e) {
                e.preventDefault();
                //var name = $("input[name=name]").val();
                var formData = new FormData($('#editStudentProfileForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/update') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //END:: Change Basic Information

            //START:: Change Profile photo
            $(document).on('click', '#editStudentProfilePhotoButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentProfilePhotoForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updatePhoto') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //END:: Change Profile photo

            //START:: Change Password
            $(document).on('click', '#editStudentPasswordButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentPasswordForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updatePassword') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //END:: Change Password

            //START:: Change Password
            $(document).on('click', '#editFamilyInfoButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editFamilyInfoForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateFamilyInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //END:: Change Password

            //START:: MARITIAL STATUS

            //Start:: show hide spouse div
            $(document).on('change', "#maritalStatus", function (e) {
                e.preventDefault();
                var statusVal = $(this).val();
                if (statusVal == '1') {
                    $('#spouseInfoDiv').show('100');
                } else {
                    $('#spouseInfoDiv').hide('100');
                }
            });
            //END:: show hide spouse div

            //Start:: Edit spouse info
            $(document).on('click', '#editMaritialStatusButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editMaritialStatusForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateMaritalStatus') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //End:: Edit spouse info

            //END:: MARITIAL STATUS

            //Start:: Multiple row create for brother/sister

            //When '+' button clicked
            $(document).on('click', ".add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/rowAddForBrotherSister') }}",

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



            //Start::Edit brothers sistes 
            $(document).on('click', '#editStudentBrotherSisterButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentBrotherSisterForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateBrotherSisterInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //End::Edit brothers sistes 


            //End:: Multiple row create for brother/sister

            //Start:: Edit student others info
            $(document).on('click', '#editStudentOthersButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentOthersForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateStudentOthersInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //End:: Edit student others info

            //Start:: Multiselect countries
            var countryAllSelected = false;
            $('#visitedCountriesId').multiselect({
                numberDisplayed: 0,
                includeSelectAllOption: true,
                buttonWidth: '100%',
                maxHeight: 250,
                nonSelectedText: "@lang('label.SELECT_VISITED_COUNTRIES_OPT')",
                //        enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                onSelectAll: function () {
                    countryAllSelected = true;
                },
                onChange: function () {
                    countryAllSelected = false;
                }
            });
            //End:: Multiselect countries

            //Start:: Division District Thana for student

            //GET district when click division
            $(document).on('change', '#divisionId', function () {
                var divisionId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('studentProfile/getDistrict')}}",
                    data: {
                        division_id: divisionId
                    },
                    success: function (result) {
                        $('#districtId').html(result.html);
                        $('#thanaId').html(result.htmlThana);
                    }
                });
            });

            //GET thana when click district
            $(document).on('change', '#districtId', function () {
                var districtId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('studentProfile/getThana')}}",
                    data: {
                        district_id: districtId
                    },
                    success: function (result) {
                        $('#thanaId').html(result.html);
                    }
                });
            });
            //End:: Division District Thana 

            //START:: Edit permanent address
            $(document).on('click', '#editStudentPermanentAddressButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentPermanentAddressForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updatePermanentAddress') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //END:: Edit permanent address

            //Start::Civil education row add
            // //When '+' button clicked
            $(document).on('click', ".civil-education-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/rowAddForCivilEducation') }}",

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
            $(document).on('click', '#editStudentCivilEducationButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentCivilEducationForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateCivilEducationInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //End::Edit civil education 

            //Start::Student Service Record 
            //Start:: Multiple row create for service record

            //When '+' button clicked
            $(document).on('click', ".service-record-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/rowAddForServiceRecord') }}",

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
            $(document).on('click', '#editStudentServiceRecordButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentServiceRecordForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateServiceRecordInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //End::Student Service Record 

            //Start::Student Award Record 
            //Start:: Multiple row create for award record

            //When '+' button clicked
            $(document).on('click', ".award-record-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/rowAddForAwardRecord') }}",

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
            $(document).on('click', '#editStudentAwardRecordButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentAwardRecordForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateAwardRecordInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //End::Student award Record 

            //Start::Student punishment Record 
            //Start:: Multiple row create for punishment record

            //When '+' button clicked
            $(document).on('click', ".punishment-record-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/rowAddForPunishmentRecord') }}",

                    success: function (result) {
                        $('#punishmentRecordInputRow').append(result.html);
                        recalcSL('punishment-record');
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

            //End:: Multiple row create for service record
            //Start::Edit punishment record 
            $(document).on('click', '#editStudentPunishmentRecordButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentPunishmentRecordForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updatePunishmentRecordInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //End::Student punishment Record 

            //Start::Student defence relative
            //Start:: Multiple row create for defence relative

            //When '+' button clicked
            $(document).on('click', ".defence-relative-add-btn", function () {

                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/rowAddForDefenceRelative') }}",

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
            $(document).on('click', '#editStudentDefenceRelativeButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentDefenceRelativeForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateDefenceRelativeInfo') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //End::Student defence relative 

            //Start:: Division District Thana for next kin

            //GET district when click division
            $(document).on('change', '#kinDivisionId', function () {
                var divisionId = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{url('studentProfile/getDistrict')}}",
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
                    url: "{{url('studentProfile/getThana')}}",
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
            $(document).on('click', '#editStudentNextKinButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentNextKinForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateNextKin') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //END:: Edit next of kin

            //START:: Edit student medical details
            $(document).on('click', '#editStudentMedicalDetailsButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentMedicalDetailsForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateMedicalDetails') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //END:: Edit student medical details

            //START:: Edit student winter training
            $(document).on('click', '#editStudentWinterTrainingButton', function (e) {
                e.preventDefault();
                var formData = new FormData($('#editStudentWinterTrainingForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ url('studentProfile/updateWinterTraining') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.success(data.success);
                        setTimeout(function () {
                            window.location.href = "{{ url('studentProfile') }}"
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
            //END:: Edit student winter training

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
                        '<a class = "btn  btn-circle green" id="editStudentWinterTrainingButton"> @lang('label.SAVE_CHANGES') </a>&nbsp;' +
                        '<a href = "{{ URL::to('studentProfile') }}" class = "btn  btn-circle default"> @lang('label.CANCEL') </a>' +
                '</div>'
                        );
            });
            //End::Number of winter collective training
            //




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