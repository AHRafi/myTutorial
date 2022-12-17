@extends('layouts.default.master')
@section('data_count')
<!-- BEGIN CONTENT BODY -->
<!-- BEGIN PORTLET-->
@include('layouts.flash')
<!-- END PORTLET-->
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile-content">
            <!-- START:: User Basic Info -->
            <div class="row" id="basicInfo">
                <div class="col-md-12">
                    <div class="col-md-2">
                        <br>
                        <!-- PORTLET MAIN -->
                        <ul class="list-unstyled profile-nav">
                            <li>
                                @if(!empty($nameArr->photo))
                                <img src="{{URL::to('/')}}/public/uploads/user/{{$nameArr->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
                                     alt="{{ $nameArr->student_name }}" style="width: 250px;height: auto;" />
                                @else 
                                <img src="{{URL::to('/')}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
                                     alt="{{ $nameArr->student_name}}"  style="width: 200px;height: auto;" />
                                @endif
                            </li>                                    
                        </ul>
                        <!-- SIDEBAR USER TITLE -->
                        <div class="profile-usertitle">
                            <div class="text-center">
                                <b>{{ $nameArr->student_name}}</b>
                            </div>
                        </div>
                        <!-- END SIDEBAR USER TITLE -->
                        <!-- END PORTLET MAIN -->
                    </div>

                    <div class="col-md-10">
                        <br>
                        <!--<div class="column sortable ">-->
                        <div class="portlet portlet-sortable box green-color-style">
                            <div class="portlet-title ui-sortable-handle">
                                <div class="caption">
                                    <i class="fa fa-info-circle green-color-style-color"></i>@lang('label.BASIC_INFORMATION')
                                </div>
                                <div class="actions">
                                    <a type="button" class="btn border border-default btn-sm tooltips edit-student-profile" href="#studentProfile" data-id="{!! $nameArr->user_id !!}" data-toggle="modal">
                                        <i class="fa fa-edit"></i> @lang('label.EDIT_PROFILE')
                                    </a>

                                </div> 
                            </div>
                            <div class="portlet-body" style="padding: 8px !important">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr >
                                            <td class="fit bold info">@lang('label.COURSE')</td>
                                            <td>{{$nameArr->course_name}}</td>
                                            <td class="fit bold info">@lang('label.ARMS_SERVICES')</td>
                                            <td> {{ !empty($nameArr->arms_service_name) ? $nameArr->arms_service_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COMMISSIONING_COURSE')</td>
                                            <td>{{$nameArr->commissioning_course_name}}</td>
                                            <td class="fit bold info">@lang('label.UNIT')</td>
                                            <td> {{ !empty($nameArr->unit_name) ? $nameArr->unit_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COMMISSIONING_DATE')</td>
                                            <td>{{ isset($nameArr->commisioning_date) ? $nameArr->commisioning_date: ''}}</td>
                                            <td class="fit bold info">@lang('label.FORMATION')</td>
                                            <td>{{ !empty($nameArr->formation_name) ? $nameArr->formation_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.ANTI_DATE_SENIORITY')</td>
                                            <td>{{ !empty($nameArr->anti_date_seniority) ? $nameArr->anti_date_seniority: ''}}</td>
                                            <td class="fit bold info">@lang('label.COMMANDING_OFFICER_NAME')</td>
                                            <td>{{ !empty($nameArr->commanding_officer_name) ? $nameArr->commanding_officer_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COURSE_POSITION')</td>
                                            <td>{{ !empty($nameArr->course_position) ? $nameArr->course_position: ''}}</td>
                                            <td class="fit bold info">@lang('label.COMMANDING_OFFICER_CONTACT')</td>
                                            <td>{{ !empty($nameArr->commanding_officer_contact_no) ? $nameArr->commanding_officer_contact_no: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.NATIONALITY')</td>
                                            <td>{{ !empty($nameArr->nationality) ? $nameArr->nationality: ''}}</td>
                                            <td class="fit bold info">@lang('label.HEIGHT')</td>
                                            <?php
                                            $height = $nameArr->ht_ft . '\' ' . $nameArr->ht_inch . '"';
                                            if ($nameArr->ht_ft == 0 && $nameArr->ht_inch == 0) {
                                                $height = '';
                                            } elseif ($nameArr->ht_inch == 0 && $nameArr->ht_ft != 0) {
                                                $height = $nameArr->ht_ft . '\'';
                                            } elseif ($nameArr->ht_ft == 0 && $nameArr->ht_inch != 0) {
                                                $height = $nameArr->ht_inch . '"';
                                            }
                                            ?>
                                            <td>{{ $height }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.BIRTH_PLACE')</td>
                                            <td>{{ !empty($nameArr->birth_place) ? $nameArr->birth_place: ''}}</td>
                                            <td class="fit bold info">@lang('label.WEIGHT')</td>
                                            <td>{{ !empty($nameArr->weight) ? $nameArr->weight: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.RELIGION')</td>
                                            <td>{{ !empty($nameArr->religion_name) ? $nameArr->religion_name: ''}}</td>
                                            <td class="fit bold info">@lang('label.MEDICAL_CATEGORIZE')</td>
                                            <td>{{ !empty($nameArr->medical_categorize) ? $nameArr->medical_categorize: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.EMAIL')</td>
                                            <td>{{ !empty($nameArr->email) ? $nameArr->email: ''}}</td>
                                            <td class="fit bold info">@lang('label.PHONE')</td>
                                            <td colspan="3">{{ !empty($nameArr->phone) ? $nameArr->phone: ''}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--</div>-->
                    </div>
                </div>
            </div>
            <!-- END:: User Basic Info -->
            <!-- SATRT::Family Information -->
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <br/>
                        <div class="portlet portlet-sortable box green-color-style">
                            <div class="portlet-title ui-sortable-handle">
                                <div class="caption">
                                    <i class="fa fa fa-users green-color-style-color"></i>@lang('label.FAMILY_INFORMATION')
                                </div>
                                <div class="actions">
                                    <a type="button" class="btn border border-default btn-sm tooltips edit-student-profile" href="#studentProfile" data-id="{!! $nameArr->user_id !!}" data-toggle="modal">
                                        <i class="fa fa-edit"></i> @lang('label.EDIT_PROFILE')
                                    </a>

                                </div> 
                            </div>
                            <div class="portlet-body" style="padding: 8px !important">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr >
                                            <td class="fit bold info">@lang('label.COURSE')</td>
                                            <td>{{$nameArr->course_name}}</td>
                                            <td class="fit bold info">@lang('label.ARMS_SERVICES')</td>
                                            <td> {{ !empty($nameArr->arms_service_name) ? $nameArr->arms_service_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COMMISSIONING_COURSE')</td>
                                            <td>{{$nameArr->commissioning_course_name}}</td>
                                            <td class="fit bold info">@lang('label.UNIT')</td>
                                            <td> {{ !empty($nameArr->unit_name) ? $nameArr->unit_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COMMISSIONING_DATE')</td>
                                            <td>{{ isset($nameArr->commisioning_date) ? $nameArr->commisioning_date: ''}}</td>
                                            <td class="fit bold info">@lang('label.FORMATION')</td>
                                            <td>{{ !empty($nameArr->formation_name) ? $nameArr->formation_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.ANTI_DATE_SENIORITY')</td>
                                            <td>{{ !empty($nameArr->anti_date_seniority) ? $nameArr->anti_date_seniority: ''}}</td>
                                            <td class="fit bold info">@lang('label.COMMANDING_OFFICER_NAME')</td>
                                            <td>{{ !empty($nameArr->commanding_officer_name) ? $nameArr->commanding_officer_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COURSE_POSITION')</td>
                                            <td>{{ !empty($nameArr->course_position) ? $nameArr->course_position: ''}}</td>
                                            <td class="fit bold info">@lang('label.COMMANDING_OFFICER_CONTACT')</td>
                                            <td>{{ !empty($nameArr->commanding_officer_contact_no) ? $nameArr->commanding_officer_contact_no: ''}}</td>
                                        </tr>
                                        
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <br>
                        <!--<div class="column sortable ">-->
                        <div class="portlet portlet-sortable box green-color-style">
                            <div class="portlet-title ui-sortable-handle">
                                <div class="caption">
                                    <i class="fa fa-life-ring green-color-style-color"></i>@lang('label.MARITAL_INFORMATION')
                                </div>
                                <div class="actions">
                                    <a type="button" class="btn border border-default btn-sm tooltips edit-student-profile" href="#studentProfile" data-id="{!! $nameArr->user_id !!}" data-toggle="modal">
                                        <i class="fa fa-edit"></i> @lang('label.EDIT_PROFILE')
                                    </a>

                                </div> 
                            </div>
                            <div class="portlet-body" style="padding: 8px !important">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr >
                                            <td class="fit bold info">@lang('label.COURSE')</td>
                                            <td>{{$nameArr->course_name}}</td>
                                            <td class="fit bold info">@lang('label.ARMS_SERVICES')</td>
                                            <td> {{ !empty($nameArr->arms_service_name) ? $nameArr->arms_service_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COMMISSIONING_COURSE')</td>
                                            <td>{{$nameArr->commissioning_course_name}}</td>
                                            <td class="fit bold info">@lang('label.UNIT')</td>
                                            <td> {{ !empty($nameArr->unit_name) ? $nameArr->unit_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COMMISSIONING_DATE')</td>
                                            <td>{{ isset($nameArr->commisioning_date) ? $nameArr->commisioning_date: ''}}</td>
                                            <td class="fit bold info">@lang('label.FORMATION')</td>
                                            <td>{{ !empty($nameArr->formation_name) ? $nameArr->formation_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.ANTI_DATE_SENIORITY')</td>
                                            <td>{{ !empty($nameArr->anti_date_seniority) ? $nameArr->anti_date_seniority: ''}}</td>
                                            <td class="fit bold info">@lang('label.COMMANDING_OFFICER_NAME')</td>
                                            <td>{{ !empty($nameArr->commanding_officer_name) ? $nameArr->commanding_officer_name: ''}}</td>
                                        </tr>
                                        <tr>
                                            <td class="fit bold info">@lang('label.COURSE_POSITION')</td>
                                            <td>{{ !empty($nameArr->course_position) ? $nameArr->course_position: ''}}</td>
                                            <td class="fit bold info">@lang('label.COMMANDING_OFFICER_CONTACT')</td>
                                            <td>{{ !empty($nameArr->commanding_officer_contact_no) ? $nameArr->commanding_officer_contact_no: ''}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--</div>-->
                    </div>
                </div>
            </div>
            <!-- END::Family Information -->

        </div>
    </div>
</div>

<!-- Modal start -->

<!--Studen Basic info update-->
<div class="modal fade" id="studentProfile" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div id="showStudentProfile"></div>
    </div>
</div>

<!-- Modal End -->


<script type="text/javascript">
    $(function () {
        //opportunity details modal
        $(document).on('click', ".edit-student-profile", function (e) {
            e.preventDefault();

            var userId = $(this).attr("data-id");
            $.ajax({
                url: "{{ URL::to('studentProfile/edit')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    user_id: userId
                },
                beforeSend: function () {
                    $("#showStudentProfile").html('');
                },
                success: function (res) {
                    $("#showStudentProfile").html(res.html);
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });

    });
</script>
@endsection