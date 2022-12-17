@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    <!--    @php
        print_r($spouseProfession);
        @endphp-->
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CM_PROFILE')
            </div>
        </div>
        <div class="portlet-body">
            <!-- Begin Filter-->
            {!! Form::open(array('group' => 'form', 'url' => 'individualProfileReportCrnt/filter','class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearList->name}} </strong></div>
                            {!! Form::hidden('training_year_id', $activeTrainingYearList->id, ['id' => 'trainingYearId']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                        <div class="col-md-7">
                            <div class="control-label pull-left"> <strong> {{$courseList->name}} </strong></div>
                            {!! Form::hidden('course_id', $courseList->id, ['id' => 'courseId']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="cmId">@lang('label.CM')</label>
                        <div class="col-md-8">
                            {!! Form::select('cm_id', $cmList,  Request::get('cm_id'), ['class' => 'form-control js-source-states', 'id' => 'cmId']) !!}
                            <span class="text-danger">{{ $errors->first('cm_id') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-center">
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


            {!! Form::close() !!}
            <!--filter form close-->

            @if($request->generate == 'true')
            <?php
            $url = 'generate=' . $request->generate . '&training_year_id=' . $request->training_year_id . '&course_id=' . $request->course_id . '&cm_id=' . 0;
            ?>
            <div class="row">
                <div class="col-md-12 text-right">
                    <a href="{{ URL::to('cmProfileReportCrnt?'.$url) }}" class="btn btn-sm blue-dark">
                        <i class="fa fa-th"></i>&nbsp;@lang('label.SHOW_ALL_CM_LIST')
                    </a>
                    <a class="btn btn-md btn-primary vcenter" target="_blank"  href="{!! URL::full().'&view=print' !!}">
                        <span class="tooltips" title="@lang('label.PRINT')"><i class="fa fa-print"></i> </span> 
                    </a>
                    <!--                    <a class="btn btn-success vcenter" href="{!! URL::full().'&view=pdf' !!}">
                                            <span class="tooltips" title="@lang('label.DOWNLOAD_PDF')"><i class="fa fa-file-pdf-o"></i></span>
                                        </a>-->
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PROFILE SIDEBAR -->
                    <div class="profile">
                        <div class="tabbable-line tabbable-full-width">
                            <div class="tab-pane active" id="tab_1">
                                <!-- START:: User Basic Info -->
                                <div class="row">
                                    <!-- START::User Image -->
                                    <div class="col-md-2 text-center">
                                        <!-- SIDEBAR USER TITLE -->
                                        <div class="profile-userpic">
                                            @if(!empty($cmInfoData->photo) && File::exists('public/uploads/cm/' . $cmInfoData->photo))
                                            <img src="{{URL::to('/')}}/public/uploads/cm/{{$cmInfoData->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
                                                 alt="{{ Common::getFurnishedCmName($cmInfoData->cm_name)}}" style="width: 100%;height: 100%;" />
                                            @else 
                                            <img src="{{URL::to('/')}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
                                                 alt="{{ Common::getFurnishedCmName($cmInfoData->cm_name) }}"  style="width: 100%;height: 100%;" />
                                            @endif
                                        </div>
                                        <div class="profile-usertitle">
                                            <div class="text-center margin-bottom-10">
                                                <b>{!! Common::getFurnishedCmName($cmInfoData->cm_name) !!}</b>
                                            </div>
                                            <?php
                                            $labelColorPN = 'grey-mint';
                                            $fontColorPN = 'blue-hoki';

                                            if ($cmInfoData->wing_id == 1) {
                                                $labelColorPN = 'green-seagreen';
                                            } elseif ($cmInfoData->wing_id == 2) {
                                                $labelColorPN = 'white';
                                                $fontColorPN = 'white';
                                            } elseif ($cmInfoData->wing_id == 3) {
                                                $labelColorPN = 'blue-madison';
                                            }
                                            ?>
                                            <div class="bold label label-square label-sm font-size-11 label-{{$labelColorPN}}">
                                                <span class="bg-font-{{$fontColorPN}}">{{!empty($cmInfoData->personal_no)? $cmInfoData->personal_no:''}}</span>
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
                                                            <td>{{$cmInfoData->course_name}}</td>
                                                            <td class="vcenter fit bold info">@lang('label.WING')</td>
                                                            <td> {{ !empty($cmInfoData->wing_name) ? $cmInfoData->wing_name: ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.COMMISSIONING_COURSE')</td>
                                                            <td>{{$cmInfoData->commissioning_course_name}}</td>
                                                            <td class="vcenter fit bold info">@lang('label.ARMS_SERVICES')</td>
                                                            <td> {{ !empty($cmInfoData->arms_service_name) ? $cmInfoData->arms_service_name: ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.TYPE_OF_COMMISSION')</td>
                                                            <td>{{ !empty($commissionTypeList[$cmInfoData->commission_type]) ? $commissionTypeList[$cmInfoData->commission_type] : '' }}</td>
                                                            <td class="vcenter fit bold info">@lang('label.EMAIL')</td>
                                                            <td>{{ !empty($cmInfoData->email) ? $cmInfoData->email: ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.COMMISSIONING_DATE')</td>
                                                            <td>{{ isset($cmInfoData->commisioning_date) ? Helper::formatDate($cmInfoData->commisioning_date): ''}}</td>
                                                            <td class="vcenter fit bold info">@lang('label.PHONE')</td>
                                                            <td>{{ !empty($cmInfoData->number) ? $cmInfoData->number: ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.ANTI_DATE_SENIORITY')</td>
                                                            <td>{{ !empty($cmInfoData->anti_date_seniority) ? $cmInfoData->anti_date_seniority: __('label.N_A')}}</td>
                                                            <td class="vcenter fit bold info">@lang('label.BLOOD_GROUP')</td>
                                                            <td>{{ !empty($bloodGroupList[$cmInfoData->blood_group]) ? $bloodGroupList[$cmInfoData->blood_group]: ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</td>
                                                            <td class="vcenter">{{ !empty($cmInfoData->date_of_birth) ? Helper::formatDate($cmInfoData->date_of_birth) : '' }}</td>
                                                            <td class="vcenter fit bold info">@lang('label.RELIGION')</td>
                                                            <td>{{ !empty($cmInfoData->religion_name) ? $cmInfoData->religion_name: ''}}</td>
                                                        </tr>

                                                        <tr>
                                                            <?php
                                                            $maritalStatus = (!empty($maritalStatusList) && ($cmInfoData->marital_status != '0') && isset($maritalStatusList[$cmInfoData->marital_status])) ? $maritalStatusList[$cmInfoData->marital_status] : __("label.N_A");
                                                            ?>
                                                            <td class="vcenter fit bold info">@lang('label.BIRTH_PLACE')</td>
                                                            <td>{{ !empty($cmInfoData->birth_place) ? $cmInfoData->birth_place: ''}}</td>
                                                            <td class = "vcenter fit bold info">@lang('label.MARITIAL_STATUS')</td>
                                                            <td> {{ $maritalStatus }} </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.EMAIL')</td>
                                                            <td class="vcenter">{{ !empty($cmInfoData->email) ? $cmInfoData->email: ''}}</td> 
                                                            <td class="vcenter fit bold info">@lang('label.PHONE')</td>
                                                            <td class="vcenter">{{ !empty($cmInfoData->number) ? $cmInfoData->number: ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.FATHERS_NAME')</td>
                                                            <td class="vcenter" colspan="3">{{ !empty($cmInfoData->father_name) ? $cmInfoData->father_name: ''}}</td> 
                                                        </tr>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!--</div>-->
                                    </div>
                                </div>
                                <!-- END:: User Basic Info -->




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
                                                            <!--<td class="vcenter fit bold info">@lang('label.FROM')</td>-->
                                                            <td class="vcenter fit bold info">@lang('label.YEAR')</td>
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
                                                            <!--<td class="vcenter">{{ !empty($civilEducationInfo['from']) ? $civilEducationInfo['from']: ''}}</td>-->
                                                            <td class="vcenter"> {{ !empty($civilEducationInfo['to']) ? $civilEducationInfo['to']: (!empty($civilEducationInfo['year']) ? $civilEducationInfo['year']: '')}}</td>
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
                                                <div class="row">
                                                    <div class="col-md-12">
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
                                    </div>

                                </div>
                                <!-- End:: Record of service Information-->

                                <!-- Start:: un mission Information-->
                                <!--                                <div class="row">
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
                                                                                            <td class="vcenter text-center fit bold info" colspan="2">@lang('label.YEAR')</td>
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
                                
                                                                </div>-->
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
                                                            <td class="vcenter text-center fit bold info">@lang('label.SERIAL')</td>
                                                            <td class="vcenter fit bold info">@lang('label.NAME_OF_COUNTRY')</td>
                                                            <td class="vcenter text-center fit bold info">@lang('label.FROM')</td>
                                                            <td class="vcenter text-center fit bold info">@lang('label.TO')</td>
                                                            <td class="vcenter fit bold info">@lang('label.REASONS_FOR_VISIT')</td>
                                                        </tr>
<!--                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.FROM')</td>
                                                            <td class="vcenter fit bold info">@lang('label.TO')</td>
                                                        </tr>-->
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
                                                            <td class="vcenter">{{ !empty($countryInfo['to']) ? $countryInfo['to']: (!empty($countryInfo['year']) ? $countryInfo['year']: '')}}</td>
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
                                                        @if(!empty($cmInfoData->marital_status) && $cmInfoData->marital_status == '1')
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.SPOUSE_NAME')</td>
                                                            <td class="vcenter">{{ !empty($cmInfoData->spouse_name) ? $cmInfoData->spouse_name: ''}}</td>
                                                            <td class="vcenter fit bold info">@lang('label.DATE_OF_MARRIAGE')</td>
                                                            <td class="vcenter"> {{ !empty($cmInfoData->date_of_marriage) ? Helper::formatDate($cmInfoData->date_of_marriage): ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.NICK_NAME')</td>
                                                            <td class="vcenter">{{ !empty($cmInfoData->spouse_nick_name) ? $cmInfoData->spouse_nick_name: ''}}</td>
                                                            <td class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</td>
                                                            <td class="vcenter"> {{ !empty($cmInfoData->spouse_dob) ? Helper::formatDate($cmInfoData->spouse_dob): ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.MOBILE')</td>
                                                            <td class="vcenter">{{ !empty($cmInfoData->spouse_mobile) ? $cmInfoData->spouse_mobile: ''}}</td>
                                                            <td class="vcenter fit bold info">@lang('label.PROFESSION')</td>
                                                            <td class="vcenter">{{ !empty($cmInfoData->spouse_occupation) ? $spouseProfession[$cmInfoData->spouse_occupation]: ''}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter fit bold info">@lang('label.WORK_ADDRESS')</td>
                                                            <td class="vcenter" colspan="3">{{ !empty($cmInfoData->spouse_work_address) ? $cmInfoData->spouse_work_address: ''}}</td>
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
                                                        $childData = !empty($childInfoData) ? json_decode($childInfoData->cm_child_info, TRUE) : null;
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
                                    <!-- Start::Cm Permanent Address-->
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
                                                                {{ !empty($addressInfo->thana_id) && !empty($thanaList[$addressInfo->thana_id]) ? $thanaList[$addressInfo->thana_id] : (!empty($addressInfo->thana_id) ? $addressInfo->thana_id : __("label.N_A"))}}
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
                                    <!-- End::Cm Permanent Address-->
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
                                                                {{ !empty($presentAddressInfo->division_id) && !empty($divisionList[$presentAddressInfo->division_id]) ? $divisionList[$presentAddressInfo->division_id] : ''}}
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
                                                                {{ !empty($presentAddressInfo->thana_id) && !empty($presentThanaList[$presentAddressInfo->thana_id]) ? $presentThanaList[$presentAddressInfo->thana_id] : (!empty($presentAddressInfo->thana_id) ? $presentAddressInfo->thana_id : __("label.N_A"))}}
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


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="portlet portlet-sortable box green-color-style">
                                            <div class="portlet-title ui-sortable-handle">
                                                <div class="caption">
                                                    <i class="fa fa-calendar green-color-style-color"></i>@lang('label.ADMIN_RESPONSIBILITY_APPT')
                                                </div>
                                            </div>
                                            <div class="portlet-body" style="padding: 8px !important">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        @if(!empty($othersInfoData))
                                                        <?php
                                                        $sl = 0;
                                                        $adResApptArr = !empty($othersInfoData->admin_resp_appt) ? explode(',', $othersInfoData->admin_resp_appt) : [];
                                                        ?>
                                                        @if(!empty($adResApptArr))
                                                        <tr>
                                                            <td>
                                                                @foreach($adResApptArr as $key => $appt)
                                                                {!! ++$sl !!}.&nbsp;{!! !empty($appt) ? $appt : '' !!}{!! '<br />' !!}
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

                                <!-- END:: Cm Others Info -->





                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script type="text/javascript" src="{{asset('public/js/apexcharts.min.js')}}"></script>
<script type="text/javascript">

$(function () {
    //table header fix
    $(".table-head-fixer-color").tableHeadFixer('');
    //Start::Get Course
    $(document).on("change", "#trainingYearId", function () {
        var trainingYearId = $("#trainingYearId").val();
        if (trainingYearId == '0') {
            $("#courseId").html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
            $("#cmId").html("<option value='0'>@lang('label.ALL_CM_OPT')</option>");
            return false;
        }
        $.ajax({
            url: "{{ URL::to('individualProfileReportCrnt/getCourse')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                training_year_id: trainingYearId
            },
            beforeSend: function () {
                $("#cmId").html("<option value='0'>@lang('label.ALL_CM_OPT')</option>");
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#courseId').html(res.html);
                $(".js-source-states").select2();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        });//ajax

    });
    //End::Get Course
    //Start::Get Term
    $(document).on("change", "#courseId", function () {


        var courseId = $("#courseId").val();
        if (courseId == '0') {
            $("#cmId").html("<option value='0'>@lang('label.ALL_CM_OPT')</option>");
            return false;
        }

        $.ajax({
            url: "{{ URL::to('individualProfileReportCrnt/getCm')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId
            },
            beforeSend: function () {
                App.blockUI({boxed: true});
            },
            success: function (res) {
                $('#cmId').html(res.html);
                $(".js-source-states").select2();
                App.unblockUI();
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        });//ajax

    });
    //End::Get Term

// Course profile Graph
    var courseProfileGraphOptions = {
        series: [{
                name: '@lang("label.WT_PERCENT")',
                data: [
<?php
$percentArr = $minMaxArr = [];
if (!empty($termList)) {
    foreach ($termList as $termId => $termName) {
        $percent = !empty($achievedMksWtArr['term_total'][$termId]['total_percentage']) ? $achievedMksWtArr['term_total'][$termId]['total_percentage'] : 0;
        $percentArr[$termId] = $percent;
        echo "'$percent',";
    }
    $minMaxArr['max'] = max($percentArr);
    $minMaxArr['min'] = min($percentArr);
}
?>
                ]
            }],
        chart: {
            type: 'bar',
            height: 280
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '35%',
                endingShape: 'rounded'
            },
        },
        colors: ["#4C87B9", "#8E44AD", "#F2784B", "#1BA39C", "#EF4836"],
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        title: {
            text: "@lang('label.COURSE_PROFILE_TERM_WISE')",
            align: 'left',
            style: {
                fontSize: '14px',
                fontWeight: '700',
            },
        },
        xaxis: {
            categories: [
<?php
if (!empty($termList)) {
    foreach ($termList as $termId => $termName) {
        echo "'$termName',";
    }
}
?>
            ],
            labels: {
                show: true,
                style: {
                    color: undefined,
                    fontSize: '10px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
            title: {
                text: '@lang("label.TERM")',
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-xaxis-title',
                },
            }
        },
        yaxis: {
            title: {
                text: '@lang("label.PERCENTAGE")',
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-yaxis-title',
                },
            },
            min: <?php echo!empty($minMaxArr['min']) ? $minMaxArr['min'] - 0.5 : 0; ?>,
            max: <?php echo!empty($minMaxArr['max']) ? $minMaxArr['max'] + 0.5 : 100; ?>,
            labels: {
                formatter: function (val) {
                    return parseFloat(val).toFixed(2);
                },
                style: {
                    color: undefined,
                    fontSize: '10px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    cssClass: 'apexcharts-yaxis-title',
                },
            },
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return parseFloat(val).toFixed(2) + '%'
                }
            }
        }
    };
    var courseProfileGraph = new ApexCharts(document.querySelector("#showCourseProfileGraph"), courseProfileGraphOptions);
    courseProfileGraph.render();
// End Course profile Graph


// Start:: Event Wise Individual Result Graph
    var eventWiseIndividualResultOptions = {
        chart: {
            height: 300,
            type: 'line',
            shadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 1
            },
            toolbar: {
                show: false
            }
        },
        colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233'],
        dataLabels: {
            enabled: false,
            enabledOnSeries: undefined,
            formatter: function (val) {
                return parseFloat(val).toFixed(2)
            },
            textAnchor: 'middle',
            distributed: false,
            offsetX: 0,
            offsetY: -10,
            style: {
                fontSize: '12px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontWeight: 'bold',
                colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233'],
            },
            background: {
                enabled: true,
                foreColor: '#fff',
                padding: 4,
                borderRadius: 2,
                borderWidth: 1,
                borderColor: '#fff',
                opacity: 0.9,
                dropShadow: {
                    enabled: false,
                    top: 1,
                    left: 1,
                    blur: 1,
                    color: '#000',
                    opacity: 0.45
                }
            },
            dropShadow: {
                enabled: false,
                top: 1,
                left: 1,
                blur: 1,
                color: '#000',
                opacity: 0.45
            }
        },
        stroke: {
            curve: 'smooth'
        },
        series: [

            {
                name: "@lang('label.PERCENTAGE')",
                data: [
<?php
$percentArr = $minMaxArr = [];
if (!empty($eventList)) {
    foreach ($eventList as $eventId => $eventCode) {
        $percent = !empty($eventResultArr['event_percentage'][$eventId]) ? $eventResultArr['event_percentage'][$eventId] : 0;
        $percentArr[$eventId] = $percent;
        echo "'$percent',";
    }
    $minMaxArr['max'] = max($percentArr);
    $minMaxArr['min'] = min($percentArr);
}
?>
                ]
            },
        ],
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                opacity: 0.5
            },
        },
        markers: {

            size: 6
        },
        title: {
            text: "@lang('label.INDIVIDUAL_RESULT_EVENT_WISE')",
            align: 'left',
            style: {
                fontSize: '14px',
                fontWeight: '700',
            },
        },
        xaxis: {
            categories: [
<?php
if (!empty($eventList)) {
    foreach ($eventList as $eventId => $eventCode) {
        echo "'$eventCode',";
    }
}
?>
            ],
            title: {
                text: "@lang('label.EVENTS')",
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
            labels: {
                show: true,
                rotate: -45,
                rotateAlways: true,
                hideOverlappingLabels: false,
                showDuplicates: true,
                trim: true,
                minHeight: 100,
                maxHeight: 180,
                style: {
                    color: undefined,
                    fontSize: '10px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
        },
        yaxis: {
            title: {
                text: "@lang('label.PERCENTAGE')",
                style: {
                    color: undefined,
                    fontSize: '11px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 700,
                    cssClass: 'apexcharts-xaxis-title',
                },
            },
            min: <?php echo!empty($minMaxArr['min']) ? $minMaxArr['min'] - 0.5 : 0; ?>,
            max: <?php echo!empty($minMaxArr['max']) ? $minMaxArr['max'] + 0.5 : 100; ?>,
            labels: {
                show: true,
                align: 'right',
                minWidth: 0,
                maxWidth: 160,
                style: {
                    color: undefined,
                    fontSize: '10px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    cssClass: 'apexcharts-xaxis-title',
                },
                offsetX: 0,
                offsetY: 0,
                rotate: 0,
                formatter: (val) => {
                    return parseFloat(val).toFixed(2)
                },
            },
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return parseFloat(val).toFixed(2) + '%'
                }
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            floating: false,
            offsetY: 0,
            offsetX: -5
        }
    }

    var showEventWiseIndividualGraph = new ApexCharts(document.querySelector("#showEventWiseIndividualGraph"), eventWiseIndividualResultOptions);
    showEventWiseIndividualGraph.render();
// End:: Event Wise Individual Result Graph
});
</script>
@stop