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
                        <span class="header">@lang('label.CM_PROFILE')</span>

                    </div>
                </div>
            </div>
            <!-- START:: User Basic Info -->
            <div class="row">
                <!-- START::User Image -->
                <div class="col-md-12 text-center">
                    <table class="no-border">
                        <thead>
                            <tr class="no-border">
                                <td width="15%" class="no-border v-top text-center">
                                    <div class="profile-userpic text-center">
                                        @if(!empty($cmInfoData->photo) && File::exists('public/uploads/cm/' . $cmInfoData->photo))
                                        <img src="{{$basePath}}/public/uploads/cm/{{$cmInfoData->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
                                             alt="{{ Common::getFurnishedCmName($cmInfoData->cm_name)}}" style="width: 150px;height: 150px;" />
                                        @else 
                                        <img src="{{$basePath}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
                                             alt="{{ Common::getFurnishedCmName($cmInfoData->cm_name) }}"  style="width: 150px;height: 150px;" />
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
                                </td>

                                <td width="85%" class="v-top no-border">
                                    <!-- END::User Image -->
                                    <!--<div class="column sortable ">-->
                                    <div class="caption margin-bottom-5">
                                        <i class="fa fa-info-circle green-color-style-color"></i> <strong>@lang('label.BASIC_INFORMATION')</strong>
                                    </div>
                                    <table class="table table-responsive table-bordered table-head-fixer-color">
                                        <thead>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.COURSE')</th>
                                                <td>{{$cmInfoData->course_name}}</td>
                                                <th class="vcenter fit bold info">@lang('label.WING')</th>
                                                <td> {{ !empty($cmInfoData->wing_name) ? $cmInfoData->wing_name: ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.COMMISSIONING_COURSE')</th>
                                                <td>{{$cmInfoData->commissioning_course_name}}</td>
                                                <th class="vcenter fit bold info">@lang('label.ARMS_SERVICES')</th>
                                                <td> {{ !empty($cmInfoData->arms_service_name) ? $cmInfoData->arms_service_name: ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.TYPE_OF_COMMISSION')</th>
                                                <td>{{ !empty($commissionTypeList[$cmInfoData->commission_type]) ? $commissionTypeList[$cmInfoData->commission_type] : '' }}</td>
                                                <th class="vcenter fit bold info">@lang('label.EMAIL')</th>
                                                <td>{{ !empty($cmInfoData->email) ? $cmInfoData->email: ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.COMMISSIONING_DATE')</th>
                                                <td>{{ isset($cmInfoData->commisioning_date) ? Helper::formatDate($cmInfoData->commisioning_date): ''}}</td>
                                                <th class="vcenter fit bold info">@lang('label.PHONE')</th>
                                                <td>{{ !empty($cmInfoData->number) ? $cmInfoData->number: ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.ANTI_DATE_SENIORITY')</th>
                                                <td>{{ !empty($cmInfoData->anti_date_seniority) ? $cmInfoData->anti_date_seniority: __('label.N_A')}}</td>
                                                <th class="vcenter fit bold info">@lang('label.BLOOD_GROUP')</th>
                                                <td>{{ !empty($bloodGroupList[$cmInfoData->blood_group]) ? $bloodGroupList[$cmInfoData->blood_group]: ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</th>
                                                <td class="vcenter">{{ !empty($cmInfoData->date_of_birth) ? Helper::formatDate($cmInfoData->date_of_birth) : '' }}</td>
                                                <th class="vcenter fit bold info">@lang('label.RELIGION')</th>
                                                <td>{{ !empty($cmInfoData->religion_name) ? $cmInfoData->religion_name: ''}}</td>
                                            </tr>

                                            <tr>
                                                <?php
                                                $maritalStatus = (!empty($maritalStatusList) && ($cmInfoData->marital_status != '0') && isset($maritalStatusList[$cmInfoData->marital_status])) ? $maritalStatusList[$cmInfoData->marital_status] : __("label.N_A");
                                                ?>
                                                <th class="vcenter fit bold info">@lang('label.BIRTH_PLACE')</th>
                                                <td>{{ !empty($cmInfoData->birth_place) ? $cmInfoData->birth_place: ''}}</td>
                                                <th class = "vcenter fit bold info">@lang('label.MARITIAL_STATUS')</th>
                                                <td> {{ $maritalStatus }} </td>
                                            </tr>

                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.EMAIL')</th>
                                                <td class="vcenter">{{ !empty($cmInfoData->email) ? $cmInfoData->email: ''}}</td> 
                                                <th class="vcenter fit bold info">@lang('label.PHONE')</th>
                                                <td class="vcenter">{{ !empty($cmInfoData->number) ? $cmInfoData->number: ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.FATHERS_NAME')</th>
                                                <td class="vcenter" colspan="3">{{ !empty($cmInfoData->father_name) ? $cmInfoData->father_name: ''}}</td> 
                                            </tr>
                                        </thead>
                                    </table>
                                    <!--</div>-->
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <!-- SIDEBAR USER TITLE -->

                </div>

            </div>
            <!-- END:: User Basic Info -->

            <!-- Start:: Course Profile -->

            <div class="row">
                <div class="col-md-12">
                    <table class="no-border">
                        <tr class="no-border">
                            <td class="no-border v-top" width="65%">
                                <table class="table table-responsive table-bordered table-head-fixer-color">
                                    <thead>
                                        <tr>
                                            <th class="vcenter text-center" colspan="6">@lang('label.COURSE_PROFILE_TERM_WISE')</th>
                                        </tr>
                                        <tr>
                                            <th class="vcenter" rowspan="2">@lang('label.TERM')</th>
                                            <th class="vcenter text-center" colspan="2">@lang('label.WT')</th>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.PERCENT')</th>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.GRADE')</th>
                                            <th class="vcenter text-center" rowspan="2">@lang('label.POSITION')</th>
                                        </tr>
                                        <tr>
                                            <th class="vcenter text-center">@lang('label.ASSIGNED')</th>
                                            <th class="vcenter text-center">@lang('label.ACHIEVED')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($termList))
                                        @foreach($termList as $termId => $termName)
                                        <?php
                                        $synName = (!empty($SynArr[$termId]['syn_name']) ? $SynArr[$termId]['syn_name'] : '') . (!empty($SynArr[$termId]['sub_syn_name']) ? ' (' . $SynArr[$termId]['sub_syn_name'] . ')' : '');
                                        ?>
                                        <tr>
                                            <td class="vcenter width-80">
                                                <div class="width-inherit">
                                                    {!! !empty($termName) ? $termName : '' !!}
                                                </div>
                                            </td>
<!--                                            <td class="vcenter width-180">
                                                <div class="width-inherit">
                                                    {!! $synName !!}
                                                </div>
                                            </td>-->
                                            <td class="vcenter {{!empty($eventMksWtArr['total_wt'][$termId]) ? 'text-right' : 'text-center'}}">{!! !empty($eventMksWtArr['total_wt'][$termId]) ? Helper::numberFormat2Digit($eventMksWtArr['total_wt'][$termId]) : '--' !!}</td>
                                            <td class="vcenter {{!empty($achievedMksWtArr['term_total'][$termId]['total_wt']) ? 'text-right' : 'text-center'}}">{!! !empty($achievedMksWtArr['term_total'][$termId]['total_wt']) ? Helper::numberFormat2Digit($achievedMksWtArr['term_total'][$termId]['total_wt']) : '--' !!}</td>
                                            <td class="vcenter {{!empty($achievedMksWtArr['term_total'][$termId]['total_percentage']) ? 'text-right' : 'text-center'}}">{!! !empty($achievedMksWtArr['term_total'][$termId]['total_percentage']) ? Helper::numberFormat2Digit($achievedMksWtArr['term_total'][$termId]['total_percentage']) : '--' !!}</td>
                                            <td class="vcenter text-center">{!! !empty($achievedMksWtArr['term_total'][$termId]['total_grade']) ? $achievedMksWtArr['term_total'][$termId]['total_grade'] : '--' !!}</td>
                                            <td class="vcenter text-center">{!! !empty($cmArr[$cmInfoData->cm_basic_profile_id]['term_total'][$termId]['position']) ? $cmArr[$cmInfoData->cm_basic_profile_id]['term_total'][$termId]['position'] : '' !!}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td class="vcenter text-right bold">@lang('label.TOTAL')</td>
                                            <td class="vcenter {{!empty($eventMksWtArr['term_total_agg_wt']) ? 'text-right' : 'text-center'}} bold">{!! !empty($eventMksWtArr['term_total_agg_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['term_total_agg_wt']) : '--' !!}</td>
                                            <td class="vcenter {{!empty($achievedMksWtArr['term_agg_total_wt']) ? 'text-right' : 'text-center'}} bold">{!! !empty($achievedMksWtArr['term_agg_total_wt']) ? Helper::numberFormat2Digit($achievedMksWtArr['term_agg_total_wt']) : '--' !!}</td>
                                            <td class="vcenter {{!empty($achievedMksWtArr['term_agg_percentage']) ? 'text-right' : 'text-center'}} bold">{!! !empty($achievedMksWtArr['term_agg_percentage']) ? Helper::numberFormat2Digit($achievedMksWtArr['term_agg_percentage']) : '--' !!}</td>
                                            <td class="vcenter text-center bold">{!! !empty($achievedMksWtArr['term_agg_grade']) ? $achievedMksWtArr['term_agg_grade'] : '--' !!}</td>
                                            <td class="vcenter text-center">{!! !empty($cmArr[$cmInfoData->cm_basic_profile_id]['total_term_agg_position']) ? $cmArr[$cmInfoData->cm_basic_profile_id]['total_term_agg_position'] : '' !!}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                            <td class="no-border v-top" width="35%">
                                <div id="showCourseProfileGraph"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-responsive table-bordered table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter text-center" colspan="11">@lang('label.COURSE_PROFILE_AGGREGATED')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center" colspan="5">@lang('label.TERM_AGGREGATED_RESULT')</th>
                                <th class="vcenter text-center" rowspan="3">@lang('label.CI_OBSN') ({!! !empty($eventMksWtArr['ci_obsn_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['ci_obsn_wt']) : '0.00' !!})</th>
                                <th class="vcenter text-center" rowspan="3">@lang('label.COMDT_OBSN') ({!! !empty($eventMksWtArr['comdt_obsn_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['comdt_obsn_wt']) : '0.00' !!})</th>
                                <th class="vcenter text-center" colspan="4">@lang('label.FINAL')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center" colspan="2">@lang('label.WT')</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.PERCENT')</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.GRADE')</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.POSITION')</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.WT') ({!! !empty($eventMksWtArr['term_total_agg_final_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['term_total_agg_final_wt']) : '0.00' !!})</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.PERCENT')</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.GRADE')</th>
                                <th class="vcenter text-center" rowspan="2">@lang('label.POSITION')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center">@lang('label.ASSIGNED')</th>
                                <th class="vcenter text-center">@lang('label.ACHIEVED')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="vcenter {{!empty($eventMksWtArr['term_total_agg_wt']) ? 'text-right' : 'text-center'}}">{!! !empty($eventMksWtArr['term_total_agg_wt']) ? Helper::numberFormat2Digit($eventMksWtArr['term_total_agg_wt']) : '--' !!}</td>
                                <td class="vcenter {{!empty($achievedMksWtArr['term_agg_total_wt']) ? 'text-right' : 'text-center'}}">{!! !empty($achievedMksWtArr['term_agg_total_wt']) ? Helper::numberFormat2Digit($achievedMksWtArr['term_agg_total_wt']) : '--' !!}</td>
                                <td class="vcenter {{!empty($achievedMksWtArr['term_agg_percentage']) ? 'text-right' : 'text-center'}}">{!! !empty($achievedMksWtArr['term_agg_percentage']) ? Helper::numberFormat2Digit($achievedMksWtArr['term_agg_percentage']) : '--' !!}</td>
                                <td class="vcenter text-center">{!! !empty($achievedMksWtArr['term_agg_grade']) ? $achievedMksWtArr['term_agg_grade'] : '--' !!}</td>
                                <td class="vcenter text-center">{!! !empty($cmArr[$cmInfoData->cm_basic_profile_id]['total_term_agg_position']) ? $cmArr[$cmInfoData->cm_basic_profile_id]['total_term_agg_position'] : '' !!}</td>
                                <td class="vcenter {{!empty($achievedMksWtArr['ci_obsn']) ? 'text-right' : 'text-center'}}">{!! !empty($achievedMksWtArr['ci_obsn']) ? Helper::numberFormat2Digit($achievedMksWtArr['ci_obsn']) : '--' !!}</td>
                                <td class="vcenter {{!empty($achievedMksWtArr['comdt_obsn']) ? 'text-right' : 'text-center'}}">{!! !empty($achievedMksWtArr['comdt_obsn']) ? Helper::numberFormat2Digit($achievedMksWtArr['comdt_obsn']) : '--' !!}</td>
                                <td class="vcenter {{!empty($achievedMksWtArr['final_wt']) ? 'text-right' : 'text-center'}}">{!! !empty($achievedMksWtArr['final_wt']) ? Helper::numberFormat2Digit($achievedMksWtArr['final_wt']) : '--' !!}</td>
                                <td class="vcenter {{!empty($achievedMksWtArr['final_percent']) ? 'text-right' : 'text-center'}}">{!! !empty($achievedMksWtArr['final_percent']) ? Helper::numberFormat2Digit($achievedMksWtArr['final_percent']) : '--' !!}</td>
                                <td class="vcenter text-center">{!! !empty($achievedMksWtArr['final_grade']) ? $achievedMksWtArr['final_grade'] : '--' !!}</td>
                                <td class="vcenter text-center">{!! !empty($cmArr[$cmInfoData->cm_basic_profile_id]['final_position']) ? $cmArr[$cmInfoData->cm_basic_profile_id]['final_position'] : '' !!}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <table class="no-border">
                        <tr class="no-border">
                            <td class="no-border v-top" width="65%">
                                <div id="showEventWiseIndividualGraph"></div>
                            </td>

                            <td class="no-border v-top" width="35%">
                                <table class="table table-responsive table-bordered table-head-fixer-color">
                                    <thead>
                                        <tr>
                                            <th class="vcenter text-center" colspan="{{!empty($factorList) ? sizeof($factorList) + 1 : 2}}">
                                                @lang('label.MUTUAL_ASSESSMENT_POSITION')
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="vcenter" rowspan="2">@lang('label.TERM')</th>
                                            @if(!empty($factorList))
                                            @foreach($factorList as $factorId => $factor)
                                            <th class="vcenter text-center">{!! $factor ?? '' !!}</th>
                                            @endforeach
                                            @else
                                            <th class="vcenter text-center">@lang('label.NO_MUTUAL_ASSESSMENT_EVENT_FOUND')</th>
                                            @endif
                                        </tr>  
                                    </thead>
                                    <tbody>
                                        @if(!empty($termList))
                                        @foreach($termList as $termId => $termName)
                                        <tr>
                                            <td class="vcenter width-80">
                                                <div class="width-inherit">
                                                    {!! !empty($termName) ? $termName : '' !!}
                                                </div>
                                            </td>
                                            @if(!empty($factorList))
                                            @foreach($factorList as $factorId => $factor)
                                            <?php
                                            $posn = !empty($muaPosnArr[$termId][$factorId]['final_pos']) ? $muaPosnArr[$termId][$factorId]['final_pos'] : 0;
                                            $totalCm = !empty($muaPosnArr[$termId][$factorId]['total_cm']) ? $muaPosnArr[$termId][$factorId]['total_cm'] : 0;
                                            ?>
                                            <td class="vcenter text-center">
                                                {!! !empty($posn) && !empty($totalCm) ? $posn . '/' . $totalCm : '' !!}
                                            </td>
                                            @endforeach
                                            @else
                                            <th class="vcenter text-center"></th>
                                            @endif
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table> 
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
            <!-- End:: Course Profile -->
			
			<!--Start :: DS Rmks on CM-->
            <div class="row">
                <div class="col-md-12">
                    <div class="caption margin-bottom-5">
                        <i class="fa fa-plane green-color-style-color"></i> <strong>@lang('label.DS_RMKS')</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-head-fixer-color">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center fit bold info">@lang('label.SL')</th>
                                    <th class="vcenter text-center fit bold info">@lang('label.DATE')</th>
                                    <th class="vcenter fit bold info">@lang('label.TERM')</th>
                                    <th class="vcenter fit bold info">@lang('label.EVENT')</th>
                                    <th class="vcenter text-center fit bold info">@lang('label.RMKS')</th>
                                    <th class="vcenter text-center fit bold info">@lang('label.REMARKED_BY')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$dsRemarksInfo->isEmpty())
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($dsRemarksInfo as $remarks)
                                <tr>
                                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                                    <td class="vcenter text-center">{{ !empty($remarks->date) ? Helper::formatDate($remarks->date) : '' }}</td>
                                    <td class="vcenter">{!! $remarks->term !!}</td>
                                    <td class="vcenter">{{ $remarks->event }}</td>
                                    <td class="vcenter">{{ $remarks->remarks ?? '' }}</td>
                                    <td class="vcenter text-center">{{ $remarks->official_name }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="7"><strong>@lang('label.NO_DS_REMARKS_FOUND')</strong></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!--End :: DS Rmks on CM-->
			
            <!-- START:: Academic qualification Information-->
            <div class="row"> 
                <div class="col-md-12">
                    <div class="caption margin-bottom-5">
                        <i class="fa fa-graduation-cap green-color-style-color"></i> <strong>@lang('label.ACADEMIC_QUALIFICATION')</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-head-fixer-color">
                            <thead>
                                <tr>
                                    <th scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</th>
                                    <th class="vcenter fit bold info">@lang('label.INSTITUTE_NAME')</th>
                                    <th class="vcenter fit bold info">@lang('label.EXAMINATION')</th>
                                    <!--<th class="vcenter fit bold info">@lang('label.FROM')</th>-->
                                    <th class="vcenter fit bold info">@lang('label.YEAR')</th>
                                    <th class="vcenter text-center fit bold info">@lang('label.QUAL_ERODE')</th>
                                </tr>
                            </thead>
                            <tbody>
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
                            </tbody>    
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
                    <table class="table table-bordered table-head-fixer-color">
                        <thead>
                            <tr>
                                <th scope="col" class="vcenter text-center fit bold info">@lang('label.SERIAL')</th>
                                <th class="vcenter fit bold info">@lang('label.INSTITUTE_N_COUNTRY')</th>
                                <th class="vcenter fit bold info">@lang('label.COURSE')</th>
                                <th class="vcenter fit bold info">@lang('label.FROM')</th>
                                <th class="vcenter fit bold info">@lang('label.TO')</th>
                                <th class="vcenter text-center fit bold info">@lang('label.RESULT')</th>
                            </tr> 
                        </thead>
                        <tbody>
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
                        </tbody>
                    </table>
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
                        <table class="table table-bordered table-head-fixer-color">
                            <thead>
                                <tr class="info">
                                    <th class="vcenter text-center fit bold info">@lang('label.SERIAL')</th>
                                    <th class="vcenter fit bold info">@lang('label.FROM')</th>
                                    <th class="vcenter fit bold info">@lang('label.TO')</th>
                                    <th class="vcenter fit bold info">@lang('label.UNIT_FMN_INST')</th>
                                    <th class="vcenter fit bold info">@lang('label.RESPONSIBILITY')</th>
                                    <th class="vcenter fit bold info">@lang('label.APPT')</th>
                                </tr>
                            </thead>
                            <tbody>
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
                            </tbody>
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
                                    <table class="table table-bordered table-head-fixer-color">
                                        <thead>
                                            <tr>
                                                <th class="vcenter text-center fit bold info" rowspan="2">@lang('label.SERIAL')</th>
                                                <th class="vcenter text-center fit bold info" colspan="2">@lang('label.YEAR')</th>
                                                <th class="vcenter fit bold info" rowspan="2">@lang('label.MSN')</th>
                                                <th class="vcenter fit bold info" rowspan="2">@lang('label.APPT')</th>
                                                <th class="vcenter fit bold info" rowspan="2">@lang('label.REMARKS')</th>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.FROM')</th>
                                                <th class="vcenter fit bold info">@lang('label.TO')</th>
                                                </trh
                                        </thead>
                                        <tbody>
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
                                        </tbody>
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
                        <table class="table table-bordered table-head-fixer-color">
                            <thead>
                                <tr>
                                    <th class="vcenter text-center fit bold info" >@lang('label.SERIAL')</th>
                                    <th class="vcenter fit bold info" >@lang('label.NAME_OF_COUNTRY')</th>
                                    <th class="vcenter text-center fit bold info" >@lang('label.FROM')</th>
                                    <th class="vcenter text-center fit bold info" >@lang('label.TO')</th>
                                    <th class="vcenter fit bold info" >@lang('label.REASONS_FOR_VISIT')</th>
                                </tr>
<!--                                <tr>
                                    <th class="vcenter fit bold info">@lang('label.FROM')</th>
                                    <th class="vcenter fit bold info">@lang('label.TO')</th>
                                </tr>-->
                            </thead>
                            <tbody>
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
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!-- End:: country visited Information-->

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
                                    <table class="table table-bordered table-head-fixer-color">
                                        <thead>
                                            @if(!empty($cmInfoData->marital_status) && $cmInfoData->marital_status == '1')
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.SPOUSE_NAME')</th>
                                                <td class="vcenter">{{ !empty($cmInfoData->spouse_name) ? $cmInfoData->spouse_name: ''}}</td>
                                                <th class="vcenter fit bold info">@lang('label.DATE_OF_MARRIAGE')</th>
                                                <td class="vcenter"> {{ !empty($cmInfoData->date_of_marriage) ? Helper::formatDate($cmInfoData->date_of_marriage): ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.NICK_NAME')</th>
                                                <td class="vcenter">{{ !empty($cmInfoData->spouse_nick_name) ? $cmInfoData->spouse_nick_name: ''}}</td>
                                                <th class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</th>
                                                <td class="vcenter"> {{ !empty($cmInfoData->spouse_dob) ? Helper::formatDate($cmInfoData->spouse_dob): ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.MOBILE')</th>
                                                <td class="vcenter">{{ !empty($cmInfoData->spouse_mobile) ? $cmInfoData->spouse_mobile: ''}}</td>
                                                <th class="vcenter fit bold info">@lang('label.PROFESSION')</th>
                                                <td class="vcenter">{{ !empty($cmInfoData->spouse_occupation) ? $spouseProfession[$cmInfoData->spouse_occupation] : ''}}</td>
                                            </tr>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.WORK_ADDRESS')</th>
                                                <td class="vcenter" colspan="3">{{ !empty($cmInfoData->spouse_work_address) ? $cmInfoData->spouse_work_address: ''}}</td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                            </tr>
                                            @endif
                                        </thead>
                                    </table>
                                </div>
                            </td>
                            <!--<div class="column sortable ">-->
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-life-ring green-color-style-color"></i> <strong>@lang('label.CHILDREN_INFORMATION')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-head-fixer-color">
                                        <thead>
                                            <tr>
                                                <th class="vcenter text-center fit bold info">@lang('label.SL')</th>
                                                <th class="vcenter fit bold info">@lang('label.NAME')</th>
                                                <th class="vcenter fit bold info">@lang('label.DATE_OF_BIRTH')</th>
                                                <th class="vcenter fit bold info">@lang('label.SCHOOL_PROFESSION')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                        </tbody>
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
                <!-- Start::Cm Permanent Address-->
                <div class="col-md-12">
                    <table class="no-border">
                        <tr class="no-border">
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-map-marker green-color-style-color"></i> <strong>@lang('label.PERMANENT_ADDRESS')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-head-fixer-color">
                                        <thead>
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
                                                    {{ !empty($addressInfo->thana_id) && !empty($thanaList[$addressInfo->thana_id]) ? $thanaList[$addressInfo->thana_id] : (!empty($addressInfo->thana_id) ? $addressInfo->thana_id : __("label.N_A"))}}
                                                </td>
                                                <th class="vcenter fit bold info">
                                                    @lang('label.ADDRESS')
                                                </th>
                                                <td class="vcenter">
                                                    {{ !empty($addressInfo->address_details) ? $addressInfo->address_details: __("label.N_A")}}
                                                </td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                            </tr>
                                            @endif
                                        </thead>
                                    </table>
                                </div>
                            </td>
                            <!-- End::Cm Permanent Address-->
                            <!-- Start::present address-->
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-10">
                                    <i class="fa fa-map-marker green-color-style-color"></i> <strong>@lang('label.PRESENT_ADDRESS')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-head-fixer-color">
                                        <thead>
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
                                                    {{ !empty($presentAddressInfo->thana_id) && !empty($presentThanaList[$presentAddressInfo->thana_id]) ? $presentThanaList[$presentAddressInfo->thana_id] : (!empty($presentAddressInfo->thana_id) ? $presentAddressInfo->thana_id : __("label.N_A"))}}
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
                                        </thead>
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
                                    <table class="table table-bordered table-head-fixer-color">
                                        <thead>
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
                                                <td colspan="4" class="vcenter">@lang('label.NO_DATA_FOUND')</td>
                                            </tr>
                                            @endif
                                        </thead>
                                    </table>
                                </div>
                            </td>
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-bank green-color-style-color"></i> <strong>@lang('label.BANK_INFO')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-head-fixer-color">
                                        <thead>
                                            <tr>
                                                <th class="vcenter text-center fit bold info">@lang('label.SL')</th>
                                                <th class="vcenter fit bold info">@lang('label.BANK_NAME')</th>
                                                <th class="vcenter fit bold info">@lang('label.BRANCH')</th>
                                                <th class="vcenter fit bold info">@lang('label.ACCT_NO')</th>
                                                <th class="vcenter fit bold info">@lang('label.ONLINE')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <!--END::Passport & Bank Information -->

            <div class="row"> 
                <!-- START:: key appt Info -->
                <div class="col-md-12">
                    <table class="no-border">
                        <tr class="no-border">

                            <!-- START:: Cm Others Info -->
<!--                            <td class="no-border v-top" width="60%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-cog green-color-style-color"></i> <strong>@lang('label.OTHERS')</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered  table-head-fixer-color">
                                        <thead>
                                            <tr>
                                                <th class="vcenter fit bold info">@lang('label.DECORATION_AWARD')</th>
                                                <th class="vcenter fit bold info">@lang('label.HOBBY')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                        </tbody>
                                    </table>
                                </div>
                            </td>-->
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-calendar green-color-style-color"></i> <strong>@lang('label.DECORATION_AWARD')</strong>
                                </div>
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
                            </td>
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-calendar green-color-style-color"></i> <strong>@lang('label.EXTRA_CURRICULAR_EXPERTISE')</strong>
                                </div>
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
                            </td>
                            <!-- END:: key appt Info -->
                        </tr>
                        
                        <tr class="no-border">
                            <td class="no-border v-top" width="50%">
                                <div class="caption margin-bottom-5">
                                    <i class="fa fa-calendar green-color-style-color"></i> <strong>@lang('label.ADMIN_RESPONSIBILITY_APPT')</strong>
                                </div>
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
                            </td>
                        </tr>
                        
                    </table>
                </div>
            </div>
            <!-- END:: Cm Others Info -->

            
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
        <script type="text/javascript" src="{{asset('public/js/apexcharts.min.js')}}"></script>

        <script>
document.addEventListener("DOMContentLoaded", function (event) {
    window.print();
});
$(function () {
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
            height: 280,
            animations: {
                enabled: false,
                animateGradually: {
                    enabled: false,
                },
                dynamicAnimation: {
                    enabled: false,
                }
            },
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
            },
            animations: {
                enabled: false,
                animateGradually: {
                    enabled: false,
                },
                dynamicAnimation: {
                    enabled: false,
                }
            },
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

    </body>
</html>
@else

@endif