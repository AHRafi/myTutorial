<?php
$currentControllerFunction = Route::currentRouteAction();
$currentCont = preg_match('/([a-z]*)@/i', request()->route()->getActionName(), $currentControllerFunction);
$controllerName = str_replace('controller', '', strtolower($currentControllerFunction[1]));
$routeName = strtolower(Route::getFacadeRoot()->current()->uri());


$iconArr = [
    '1' => 'fa fa-search',
    '2' => 'fa fa-file-text-o',
    '3' => 'fa fa-adjust',
    '4' => 'fa fa-asterisk',
    '5' => 'fa fa-certificate',
    '6' => 'fa fa-check-circle',
    '7' => 'fa fa-cube',
    '8' => 'fa fa-dot-circle-o',
    '9' => 'fa fa-external-link-square',
    '10' => 'fa fa-gear',
    '11' => 'fa fa-globe',
    '12' => 'fa fa-hdd-o',
    '13' => 'fa fa-industry',
    '14' => 'fa fa-inbox',
    '15' => 'fa fa-life-bouy',
    '16' => 'fa fa-square',
    '17' => 'fa fa-sun-o',
    '18' => 'fa fa-tachometer',
    '19' => 'fa fa-tasks',
    '20' => 'fa fa-university',
    '21' => 'fa fa-tag',
    '22' => 'fa fa-clone',
    '23' => 'fa fa-circle',
    '24' => 'fa fa-gg',
    '25' => 'fa fa-users',
];

//echo '<pre>';print_r($permittedReportArr);echo '</pre>';
?>
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul id="addsidebarFullMenu" class="page-sidebar-menu  page-header-fixed" data-keep-expanded="false"
            data-auto-scroll="true" data-slide-speed="200" style="padding-top: 10px">
            <!--li class="sidebar-toggler-wrapper hide">
            <div class="sidebar-toggler">
                <span></span>
            </div>
        </li-->

            <!-- start dashboard menu -->
            <li <?php $current = ( in_array($controllerName, array('dashboard'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                <a href="{{url('/dashboard')}}" class="nav-link ">
                    <i class="icon-home"></i>
                    <span class="title"> @lang('label.DASHBOARD')</span>
                </a>
            </li>

            @if(Auth::user()->group_id == '1')
            <li <?php
            $current = ( in_array($controllerName, array('trainingyear', 'term', 'trade', 'module'
                        , 'syndicate', 'subsyndicate', 'event', 'courseid', 'noofparticular', 'termtocourse'
                        , 'event', 'subevent', 'subsubevent', 'subsubsubevent', 'eventtree', 'eventgroup'
                        , 'gradingsystem'))) ? 'start active open' : '';
            ?>class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-calendar"></i>
                    <span class="title">@lang('label.TERMS_COURSE_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('trainingyear'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/trainingYear')}}" class="nav-link">
                            <span class="title">@lang('label.TRAINING_YEAR')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('term'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/term')}}" class="nav-link">
                            <span class="title">@lang('label.TERM')</span>
                        </a>
                    </li>
                    <li
                        <?php $current = ( in_array($controllerName, array('courseid'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/courseId')}}" class="nav-link ">
                            <span class="title">@lang('label.COURSE_ID')</span>
                        </a>
                    </li>
                    <!--<li <?php $current = ( in_array($controllerName, array('syndicate'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/syndicate')}}" class="nav-link">
                            <span class="title">@lang('label.SYN')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('subsyndicate'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/subSyndicate')}}" class="nav-link">
                            <span class="title">@lang('label.SUB_SYN')</span>
                        </a>
                    </li>-->

                    <li <?php $current = ( in_array($controllerName, array('termtocourse')) && ($routeName != 'termtocourse/activationorclosing')) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('/termToCourse')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_SCHEDULING')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termtocourse')) && ($routeName == 'termtocourse/activationorclosing' )) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('termToCourse/activationOrClosing')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_SCHEDULING_ACTIVATION_CLOSING')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('event'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/event')}}" class="nav-link">
                            <span class="title">@lang('label.EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('subevent'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/subEvent')}}" class="nav-link">
                            <span class="title">@lang('label.SUB_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('subsubevent'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/subSubEvent')}}" class="nav-link">
                            <span class="title">@lang('label.SUB_SUB_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('subsubsubevent'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/subSubSubEvent')}}" class="nav-link">
                            <span class="title">@lang('label.SUB_SUB_SUB_EVENT')</span>
                        </a>
                    </li>
                    <!--                    <li <?php $current = ( in_array($controllerName, array('eventtree'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/eventTree')}}" class="nav-link">
                                                <span class="title">@lang('label.EVENT_TREE')</span>
                                            </a>
                                        </li>-->
                    <li <?php $current = ( in_array($controllerName, array('eventgroup'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/eventGroup')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_GROUP')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('gradingsystem'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/gradingSystem')}}" class="nav-link">
                            <span class="title">@lang('label.GRADING_SYSTEM')</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li <?php
            $current = ( in_array($controllerName, array('usergroup', 'rank', 'appointment', 'cmappointment', 'armsservice'
                        , 'cmgroup', 'dsgroup', 'wing', 'unit', 'user', 'cm', 'cmprofile', 'commissioningcourse', 'serviceappointment'
                        , 'milcourse', 'corpsregtbr', 'decoration', 'award', 'hobby', 'ipblocker', 'staff'))) ? 'start active open' : '';
            ?>class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cogs"></i>
                    <span class="title">@lang('label.ADMIN_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('usergroup'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/userGroup')}}" class="nav-link ">
                            <span class="title">@lang('label.USER_GROUP')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('wing'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/wing')}}" class="nav-link">
                            <span class="title">@lang('label.WING')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('rank'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/rank')}}" class="nav-link ">
                            <span class="title">@lang('label.RANK')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('appointment'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/appointment')}}" class="nav-link ">
                            <span class="title">@lang('label.APPOINTMENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('cmappointment'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/cmAppointment')}}" class="nav-link ">
                            <span class="title">@lang('label.CM_APPOINTMENT')</span>
                        </a>
                    </li>
                    <!--                    <li <?php $current = ( in_array($controllerName, array('serviceappointment'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/serviceAppointment')}}" class="nav-link ">
                                                <span class="title">@lang('label.SERVICE_APPOINTMENT')</span>
                                            </a>
                                        </li>-->
                    <li
                        <?php $current = ( in_array($controllerName, array('commissioningcourse'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/commissioningCourse')}}" class="nav-link ">
                            <span class="title">@lang('label.COMMISSIONING_COURSE')</span>
                        </a>
                    </li>
                    <li
                        <?php $current = ( in_array($controllerName, array('milcourse'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/milCourse')}}" class="nav-link ">
                            <span class="title">@lang('label.MIL_COURSE')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('armsservice'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/armsService')}}" class="nav-link ">
                            <span class="title">@lang('label.ARMS_SERVICES')</span>
                        </a>
                    </li>

                    <!--                    <li <?php $current = ( in_array($controllerName, array('unit'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/unit')}}" class="nav-link ">
                                                <span class="title">@lang('label.UNIT_FMN_INST')</span>
                                            </a>
                                        </li>-->
                    <!--                    <li <?php $current = ( in_array($controllerName, array('decoration'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/decoration')}}" class="nav-link ">
                                                <span class="title">@lang('label.DECORATION')</span>
                                            </a>
                                        </li>-->
                    <!--                    <li <?php $current = ( in_array($controllerName, array('award'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/award')}}" class="nav-link ">
                                                <span class="title">@lang('label.AWARD')</span>
                                            </a>
                                        </li>-->
                    <!--                    <li <?php $current = ( in_array($controllerName, array('hobby'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/hobby')}}" class="nav-link ">
                                                <span class="title">@lang('label.HOBBY')</span>
                                            </a>
                                        </li>-->
                    <li <?php $current = ( in_array($controllerName, array('cmgroup'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/cmGroup')}}" class="nav-link ">
                            <span class="title">@lang('label.CM_GROUP')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('dsgroup'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/dsGroup')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_GROUP')</span>
                        </a>
                    </li>


                    <li <?php $current = ( in_array($controllerName, array('user'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/user')}}" class="nav-link ">
                            <span class="title">@lang('label.USER')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('ipblocker'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('ipBlocker')}}" class="nav-link ">
                            <span class="title">@lang('label.IP_BLOCKER')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('cm'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/cm')}}" class="nav-link ">
                            <span class="title">@lang('label.CM')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('staff'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/staff')}}" class="nav-link ">
                            <span class="title">@lang('label.STAFF')</span>
                        </a>
                    </li>


                </ul>
            </li>

            <?php
            $current = (in_array($controllerName, array('syntosubsyn', 'syntocourse', 'cmgrouptocourse', 'dsgrouptocourse', 'cmtosyn', 'citowing'
                        , 'dstosyn', 'termtoevent', 'termtosubevent', 'termtosubsubevent', 'termtosubsubsubevent'
                        , 'cmtosyn', 'cmtosubsyn', 'eventtosubevent', 'eventtosubsubevent', 'eventgrouptocourse'
                        , 'eventtosubsubsubevent', 'maeventtocourse', 'eventtoapptmatrix'
                        , 'markinggroup', 'cmgroupmembertemplate', 'dsgroupmembertemplate', 'eventtoeventgroup'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">@lang('label.RELATIONSHIP_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <!--
                                        <li <?php $current = ( in_array($controllerName, array('syntocourse'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/synToCourse')}}" class="nav-link ">
                                                <span class="title">@lang('label.SYN_TO_COURSE')</span>
                                            </a>
                                        </li>
                                        <li <?php $current = ( in_array($controllerName, array('syntosubsyn'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/synToSubSyn')}}" class="nav-link ">
                                                <span class="title">@lang('label.SYN_TO_SUB_SYN')</span>
                                            </a>
                                        </li>-->
                    <li <?php $current = ( in_array($controllerName, array('cmgrouptocourse'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/cmGroupToCourse')}}" class="nav-link ">
                            <span class="title">@lang('label.CM_GROUP_TO_COURSE')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('cmgroupmembertemplate'))) ? 'start active open' : ''; ?> 
                        class="nav-item {{$current}}">
                        <a href="{{url('/cmGroupMemberTemplate')}}" class="nav-link ">
                            <span class="title">@lang('label.CM_GROUP_MEMBER_TEMPLATE')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('dsgrouptocourse'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/dsGroupToCourse')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_GROUP_TO_COURSE')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('dsgroupmembertemplate'))) ? 'start active open' : ''; ?> 
                        class="nav-item {{$current}}">
                        <a href="{{url('/dsGroupMemberTemplate')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_GROUP_MEMBER_TEMPLATE')</span>
                        </a>
                    </li>
                    <!--<li <?php $current = ( in_array($controllerName, array('cmtosyn'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/cmToSyn')}}" class="nav-link ">
                            <span class="title">@lang('label.CM_TO_SYN')</span>
                        </a>
                    </li>
                                        <li <?php $current = ( in_array($controllerName, array('cmtosubsyn'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/cmToSubSyn')}}" class="nav-link ">
                                                <span class="title">@lang('label.CM_TO_SUB_SYN')</span>
                                            </a>
                                        </li>            -->
                    <!--                    <li <?php $current = ( in_array($controllerName, array('citowing'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                                            <a href="{{url('ciToWing')}}" class="nav-link ">
                                                <span class="title">@lang('label.CI_TO_WING')</span>
                                            </a>
                                        </li>-->

<!--                    <li <?php $current = ( in_array($controllerName, array('dstosyn')) && ($routeName == 'dstosyn' )) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
    <a href="{{url('dsToSyn')}}" class="nav-link ">
        <span class="title">@lang('label.DS_TO_SYN')</span>
    </a>
</li>-->

                    <li <?php $current = ( in_array($controllerName, array('eventtosubevent'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('eventToSubEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_TO_SUB_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('eventtosubsubevent'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('eventToSubSubEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_TO_SUB_SUB_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('eventtosubsubsubevent'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('eventToSubSubSubEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_TO_SUB_SUB_SUB_EVENT')</span>
                        </a>
                    </li>
                    <!--                    <li <?php $current = ( in_array($controllerName, array('eventgrouptocourse'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('/eventGroupToCourse')}}" class="nav-link ">
                                                <span class="title">@lang('label.EVENT_GROUP_TO_COURSE')</span>
                                            </a>
                                        </li>-->
                    <li <?php $current = ( in_array($controllerName, array('termtoevent'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('termToEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_TO_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termtosubevent'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('termToSubEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_TO_SUB_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termtosubsubevent'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('termToSubSubEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_TO_SUB_SUB_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termtosubsubsubevent'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('termToSubSubSubEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_TO_SUB_SUB_SUB_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('eventtoeventgroup'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/eventToEventGroup')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_TO_EVENT_GROUP')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('markinggroup'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/markingGroup')}}" class="nav-link ">
                            <span class="title">@lang('label.ASSIGN_MARKING_GROUP')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('eventtoapptmatrix'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('eventToApptMatrix')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_TO_APPT_MATRIX')</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!--Start : Mks & Wt Destribution Menu-->
            <?php
            $current = (in_array($controllerName, array('cicomdtmoderationmarkinglimit', 'criteriawisewt'
                        , 'eventmkswt', 'subeventmkswt', 'subsubeventmkswt', 'subsubsubeventmkswt'
                        , 'dsobsnmarkinglimit', 'cicomdtobsnmarkinglimit'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-book"></i>
                    <span class="title">@lang('label.MARKS_WT_DISTRIBUTION')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($routeName, array('criteriawisewt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('criteriaWiseWt')}}" class="nav-link ">
                            <span class="title">@lang('label.CRITERIA_WISE_WT')</span>
                        </a>
                    </li> 
                    <li <?php $current = ( in_array($routeName, array('eventmkswt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('eventMksWt')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_MKS_WT')</span>
                        </a>
                    </li> 
                    <li <?php $current = ( in_array($routeName, array('subeventmkswt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('subEventMksWt')}}" class="nav-link ">
                            <span class="title">@lang('label.SUB_EVENT_MKS_WT')</span>
                        </a>
                    </li> 
                    <li <?php $current = ( in_array($routeName, array('subsubeventmkswt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('subSubEventMksWt')}}" class="nav-link ">
                            <span class="title">@lang('label.SUB_SUB_EVENT_MKS_WT')</span>
                        </a>
                    </li> 
                    <li <?php $current = ( in_array($routeName, array('subsubsubeventmkswt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('subSubSubEventMksWt')}}" class="nav-link ">
                            <span class="title">@lang('label.SUB_SUB_SUB_EVENT_MKS_WT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($routeName, array('cicomdtmoderationmarkinglimit'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('ciComdtModerationMarkingLimit')}}" class="nav-link ">
                            <span class="title">@lang('label.CI_COMDT_MODERATION_MARKING_LIMIT')</span>
                        </a>
                    </li> 
                    <li <?php $current = ( in_array($routeName, array('dsobsnmarkinglimit'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('dsObsnMarkingLimit')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_OBSN_MARKING_LIMIT')</span>
                        </a>
                    </li> 
                    <li <?php $current = ( in_array($routeName, array('cicomdtobsnmarkinglimit'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('ciComdtObsnMarkingLimit')}}" class="nav-link ">
                            <span class="title">@lang('label.CI_COMDT_OBSN_MARKING_LIMIT')</span>
                        </a>
                    </li> 
                </ul>
            </li>
            <!--End : Mks & Wt Destribution Menu-->

            <?php
            $current = (in_array($controllerName, array('mutualassessment', 'mutualassessmentevent'
                        , 'maprocess', 'unlockmarequest'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">@lang('label.MUTUAL_ASSESSMENT')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">

                    <li <?php $current = ( in_array($controllerName, array('mutualassessmentevent'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/mutualAssessmentFactor')}}" class="nav-link">
                            <span class="title">@lang('label.MUTUAL_ASSESSMENT_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('maprocess'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/maProcess')}}" class="nav-link">
                            <span class="title">@lang('label.MA_PROCESS')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('mutualassessment')) && ($routeName != 'mutualassessment/importmarkingsheet')) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('mutualAssessment/markingSheet')}}" class="nav-link ">
                            <span class="title">@lang('label.GENERATE_MARKING_SHEET')</span>
                        </a>
                    </li> 
                    <li <?php $current = ( in_array($controllerName, array('mutualassessment')) && ($routeName == 'mutualassessment/importmarkingsheet')) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('mutualAssessment/importMarkingSheet')}}" class="nav-link ">
                            <span class="title">@lang('label.IMPORT_MARKING_SHEET')</span>
                        </a>
                    </li> 
                    <li <?php $current = ( in_array($controllerName, array('unlockmarequest'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('unlockMaRequest')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.UNLOCK_MA_REQUEST')</span>
                        </a>   
                    </li>
                </ul>
            </li>



            <!--Start : Appt to CM Menu-->
            <li <?php $current = ( in_array($controllerName, array('appttocm'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                <a href="{{url('apptToCm')}}" class="nav-link ">
                    <i class="fa fa-user-secret"></i>
                    <span class="title">@lang('label.APPT_TO_CM')</span>
                </a>
            </li>
            <!--End : Appt to CM Menu-->
            @endif



            <!-- Start:: Ds Access-->
            @if(in_array(Auth::user()->group_id,[4]))
            <li <?php
            $current = ( in_array($controllerName, array('eventassessmentmarking'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="{{ url('eventAssessmentMarking') }}" class="nav-link">
                    <i class="fa fa-book"></i>
                    <span class="title">@lang('label.EVENT_ASSESSMENT')</span>
                </a>
            </li>
            <li <?php
            $current = ( in_array($controllerName, array('dsremarks'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="{{ url('dsRemarks') }}" class="nav-link">
                    <i class="fa fa-tag"></i>
                    <span class="title">@lang('label.DS_RMKS')</span>
                </a>
            </li>




            <li <?php
            $current = ( in_array($controllerName, array('dsobsnmarking'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="{{ url('dsObsnMarking') }}" class="nav-link">
                    <i class="fa fa-pencil"></i>
                    <span class="title">@lang('label.DS_OBSN_MARKING')</span>
                </a>
            </li>



            @endif
            <!-- End:: Ds Access-->
            <!-- Start:: Ci Access-->
            @if(in_array(Auth::user()->group_id,[3]))
            <li <?php
            $current = ( in_array($controllerName, array('deligateciaccttods'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="{{ url('deligateCiAcctToDs') }}" class="nav-link">
                    <i class="fa fa-gears"></i>
                    <span class="title">@lang('label.DELIGATE_CI_ACCOUNT_TO_DS')</span>
                </a>
            </li>
            <li <?php
            $current = ( in_array($controllerName, array('deligatereportstods'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="{{ url('deligateReportsToDs') }}" class="nav-link">
                    <i class="fa fa-line-chart"></i>
                    <span class="title">@lang('label.DELIGATE_REPORTS_TO_DS')</span>
                </a>
            </li>

            @endif
            <!-- End:: Ci,Comdt Access-->

            <!-- Start:: deligated DS,Ci,Comdt Access-->
            @if(in_array(Auth::user()->group_id,[2,3]) || in_array(Auth::user()->id, $dsDeligationList))
            @if(in_array(Auth::user()->group_id,[3]) || in_array(Auth::user()->id, $dsDeligationList))
            <li <?php
            $current = ( in_array($controllerName, array('cimoderationmarking'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="{{ url('ciModerationMarking') }}" class="nav-link">
                    <i class="fa fa-book"></i>
                    @if(in_array(Auth::user()->group_id,[3]))
                    <span class="title">@lang('label.MODERATION_MARKING')</span>
                    @elseif(in_array(Auth::user()->id, $dsDeligationList))
                    <span class="title">@lang('label.CI_MODERATION_MARKING')</span>
                    @endif
                </a>
            </li>
            @endif
            <!--            <li <?php
            $current = ( in_array($controllerName, array('comdtmoderationmarking'))) ? 'start active open' : '';
            ?>
                            class="nav-item {{$current}}">
                            <a href="{{ url('comdtModerationMarking') }}" class="nav-link">
                                <i class="fa fa-book"></i>
                                @if(in_array(Auth::user()->group_id,[2]))
                                <span class="title">@lang('label.MODERATION_MARKING')</span>
                                @elseif(in_array(Auth::user()->group_id,[3]) || in_array(Auth::user()->id, $dsDeligationList))
                                <span class="title">@lang('label.COMDT_MODERATION_MARKING')</span>
                                @endif
                            </a>
                        </li>-->

            @if(in_array(Auth::user()->group_id,[3]) || in_array(Auth::user()->id, $dsDeligationList))
            <li <?php
            $current = ( in_array($controllerName, array('ciobsnmarking'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="{{ url('ciObsnMarking') }}" class="nav-link">
                    <i class="fa fa-pencil"></i>
                    <span class="title">@lang('label.CI_OBSN_MARKING')</span>
                </a>
            </li>
            @endif
            <li <?php
            $current = ( in_array($controllerName, array('comdtobsnmarking'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="{{ url('comdtObsnMarking') }}" class="nav-link">
                    <i class="fa fa-pencil"></i>
                    <span class="title">@lang('label.COMDT_OBSN_MARKING')</span>
                </a>
            </li>



            <!--Start :: Unlock Request-->
            <li <?php
            $current = ( in_array($controllerName, array('unlockeventassessment', 'unlockcimoderationmarking'
                        , 'unlockcomdtmoderationmarking', 'unlockciobsnmarking', 'unlockcomdtobsnmarking'
                        , 'unlockdsobsnmarking'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-unlock"></i>
                    <span class="title">@lang('label.UNLOCK_REQUEST')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">

                    @if(in_array(Auth::user()->group_id, [3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php $current = ( in_array($controllerName, array('unlockeventassessment'))) ? 'start active open' : '';
            ?> class="nav-item {{$current}}">
                        <a href="{{url('unlockEventAssessment')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.UNLOCK_EVENT_ASSESSMENT')</span>
                        </a>   
                    </li>
                    @endif
                    <li <?php $current = ( in_array($controllerName, array('unlockcimoderationmarking'))) ? 'start active open' : '';
            ?> class="nav-item {{$current}}">
                        <a href="{{url('unlockCiModerationMarking')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.UNLOCK_CI_MODERATION_MARKING')</span>
                        </a>   
                    </li>
<!--                    <li <?php $current = ( in_array($controllerName, array('unlockcomdtmoderationmarking'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('unlockComdtModerationMarking')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.UNLOCK_COMDT_MODERATION_MARKING')</span>
                        </a>   
                    </li>-->
                    @if(in_array(Auth::user()->group_id, [3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php $current = ( in_array($controllerName, array('unlockdsobsnmarking'))) ? 'start active open' : '';
            ?> class="nav-item {{$current}}">
                        <a href="{{url('unlockDsObsnMarking')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.UNLOCK_DS_OBSN_MARKING')</span>
                        </a>   
                    </li>
                    @endif
                    <li <?php $current = ( in_array($controllerName, array('unlockciobsnmarking'))) ? 'start active open' : '';
            ?> class="nav-item {{$current}}">
                        <a href="{{url('unlockCiObsnMarking')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.UNLOCK_CI_OBSN_MARKING')</span>
                        </a>   
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('unlockcomdtobsnmarking'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('unlockComdtObsnMarking')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.UNLOCK_COMDT_OBSN_MARKING')</span>
                        </a>   
                    </li>

                </ul>
            </li> 
            <!--End :: Unlock Request-->

            @endif
            <!-- End:: deligated DS,Ci,Comdt Access-->

            <!--Start : GS Evaluation Setup-->
            @if(in_array(Auth::user()->group_id,[1,4]))
            <?php
            $current = (in_array($controllerName, array('corecurriculum', 'subject', 'gsmodule'
                        , 'gsgrading', 'considerations', 'comment', 'lesson', 'objective', 'gs'
                        , 'moduletosubject', 'subjecttods', 'gstolesson', 'subjecttolesson'
                        , 'dsevalofgs'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">@lang('label.GS_FEEDBACK')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @if(in_array(Auth::user()->group_id,[1]))
                    <li <?php $current = ( in_array($controllerName, array('corecurriculum'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/coreCurriculum')}}" class="nav-link">
                            <span class="title">@lang('label.CORE_CURRICULUM')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('gsmodule'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/gsmodule')}}" class="nav-link">
                            <span class="title">@lang('label.MODULE')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('subject'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/subject')}}" class="nav-link">
                            <span class="title">@lang('label.SUBJECT')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('gsgrading'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('gsgrading')}}" class="nav-link ">
                            <span class="title">@lang('label.GRADING')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('considerations'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('considerations')}}" class="nav-link ">
                            <span class="title">@lang('label.CONSIDERATIONS')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('comment'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('comment')}}" class="nav-link ">
                            <span class="title">@lang('label.COMMENT')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[4]))
                    <li <?php $current = ( in_array($controllerName, array('lesson'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('lesson')}}" class="nav-link ">
                            <span class="title">@lang('label.LESSON')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('objective'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/objective')}}" class="nav-link">
                            <span class="title">@lang('label.OBJECTIVE')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[1]))
                    <li <?php $current = ( in_array($controllerName, array('gs'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/gs')}}" class="nav-link">
                            <span class="title">@lang('label.GS')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('moduletosubject'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/moduleToSubject')}}" class="nav-link ">
                            <span class="title">@lang('label.MODULE_TO_SUBJECT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('subjecttods'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/subjectToDs')}}" class="nav-link ">
                            <span class="title">@lang('label.SUBJECT_TO_DS')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[4]))
                    <li <?php $current = ( in_array($controllerName, array('subjecttolesson'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('subjectToLesson')}}" class="nav-link ">
                            <span class="title">@lang('label.SUBJECT_TO_LESSON')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('gstolesson'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('gsToLesson')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.GS_TO_LESSON')</span>
                        </a>
                    </li>
                    <li <?php
                    $current = ( in_array($controllerName, array('dsevalofgs'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="{{ url('dsEvalOfGs') }}" class="nav-link">
                            <span class="title">@lang('label.DS_EVAL_OF_GS')</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            <!--End : GS Evaluation Setup-->

            @if(in_array(Auth::user()->group_id,[1]))
            <li <?php
            $current = ( in_array($controllerName, array('activategsfeedbackfords', 'activategsfeedbackforcm'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-power-off"></i>
                    <span class="title">@lang('label.ACTIVATE_GS_FEEDBACK')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">

                    <li <?php $current = ( in_array($controllerName, array('activategsfeedbackfords'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('activateGsFeedbackForDs')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.FOR_DS')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('activategsfeedbackforcm'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('activateGsFeedbackForCm')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.FOR_CM')</span>
                        </a>
                    </li>

                </ul>
            </li>
            @endif

            <!--Start:: Unlock GS Feedback-->
            @if(in_array(Auth::user()->group_id,[1]))
            <li <?php
            $current = ( in_array($controllerName, array('unlockdsfeedback', 'module', 'unlockcmfeedback'))) ? 'start active open' : '';
            ?> class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-archive"></i>
                    <span class="title">@lang('label.UNLOCK_GS_FEEDBACK')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">

                    <li <?php $current = ( in_array($controllerName, array('unlockdsfeedback'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('unlockDsFeedback')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.FROM_DS')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('unlockcmfeedback'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('unlockCmFeedback')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.FROM_CM')</span>
                        </a>
                    </li>

                </ul>
            </li>
            @endif
            <!-- End : Unlock GS Eval Feedback-->
            <li <?php
            $current = ( in_array($controllerName, array('lessonwisegsfeedbackfromds', 'lessonwisegsfeedbackfromcm'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-area-chart"></i>
                    <span class="title">@lang('label.GS_FEEDBACK_REPORTS')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <!--Start :: Lesson Wise Gs Feedback From Ds-->
            <li <?php
            $current = ( in_array($controllerName, array('lessonwisegsfeedbackfromds', 'lessonwisegsfeedbackfromcm'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <span class="title">@lang('label.LESSON_WISE_GS_FEEDBACK')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">

                    <li <?php $current = ( in_array($controllerName, array('lessonwisegsfeedbackfromds'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('lessonWiseGsFeedbackFromDs')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.FROM_DS')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('lessonwisegsfeedbackfromcm'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('lessonWiseGsFeedbackFromCm')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.FROM_CM')</span>
                        </a>
                    </li>

                </ul>
            </li>
            <!--End :: Lesson Wise Gs Feedback From Ds-->
                    

                </ul>
            </li>
            <!--End :: Lesson Wise Gs Feedback From Ds-->
            

            <!-- start:: Notice Board menu -->
            <li <?php $current = ( in_array($controllerName, array('noticeboard'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                <a href="{{url('/noticeBoard')}}" class="nav-link">
                    <i class="fa fa-clipboard"></i>
                    <span class="title"> @lang('label.NOTICE_BOARD')</span>
                </a>
            </li>
            <!-- end:: Notice Board menu -->
            @if(in_array(Auth::user()->group_id,[1,3,4]))
            <li <?php $current = ( in_array($controllerName, array('mkssubmissionstate'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                <a href="{{url('mksSubmissionState')}}" class="nav-link nav-toggle">
                    <i class="fa fa-info-circle"></i>
                    <span class="title">@lang('label.MKS_SUBMISSION_STATE')</span>
                </a>   
            </li>
            @if(in_array(Auth::user()->group_id,[1]))
            <li <?php $current = ( in_array($controllerName, array('mutualassessmentsubmissionstate'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                <a href="{{url('mutualAssessmentSubmissionState')}}" class="nav-link nav-toggle">
                    <i class="fa fa-users"></i>
                    <span class="title">@lang('label.MUTUAL_ASSESSMENT_SUBMISSION_STATE')</span>
                </a>
            </li>
            @endif
            @endif
            @if(in_array(Auth::user()->group_id,[1,3]) || in_array(Auth::user()->id, $dsDeligationList))

            <li <?php $current = ( in_array($controllerName, array('clearmarking'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                <a href="{{url('clearMarking')}}" class="nav-link nav-toggle">
                    <i class="fa fa-eraser"></i>
                    <span class="title">@lang('label.CLEAR_MARKING')</span>
                </a>   
            </li>
            @endif

            @if(in_array(Auth::user()->group_id,[1]))
            <li <?php $current = ( in_array($controllerName, array('assessmentactdeact'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                <a href="{{url('assessmentActDeact')}}" class="nav-link nav-toggle">
                    <i class="fa fa-power-off"></i>
                    <span class="title">@lang('label.ASSESSMENT_ACTIVATE_DEACTIVATE')</span>
                </a>   
            </li>

            @endif

            <!-- Start:: Course Report Setup-->
            @if(in_array(Auth::user()->group_id,[1]))
            <li <?php
            $current = ( in_array($controllerName, array('crmarkingslab', 'crsentencetotrait', 'crgrouping'
                        , 'crmarkingreflection', 'crtrait', 'crclearreport', 'reportactivation'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-pencil"></i>
                    <span class="title">@lang('label.COURSE_REPORT_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">

                    <li <?php $current = ( in_array($controllerName, array('crtrait'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('crTrait')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.TRAIT')</span>
                        </a>   
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('crmarkingslab'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('crMarkingSlab')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.MARKING_SLAB')</span>
                        </a>   
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('crmarkingreflection'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('crMarkingReflection')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.MARKING_REFLECTION')</span>
                        </a>   
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('crsentencetotrait'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('crSentenceToTrait')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.FACTOR_TO_TRAIT')</span>
                        </a>   
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('crgrouping'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('crGrouping')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.ASSIGN_GROUP')</span>
                        </a>   
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('reportactivation'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('crReportactivation')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.REPORT_ACTIVATION')</span>
                        </a>   
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('crclearreport'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('crClearReport')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.CLEAR_COURSE_REPORTS')</span>
                        </a>   
                    </li>

                </ul>
            </li>
            @endif
            <!-- End:: Course Report Setup-->
            <!-- Start:: Course Report Generation-->
            @if(in_array(Auth::user()->group_id,[4]))
            <li <?php $current = ( in_array($controllerName, array('crgeneration'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                <a href="{{url('crGeneration')}}" class="nav-link nav-toggle">
                    <i class="fa fa-pencil"></i>
                    <span class="title">@lang('label.GENERATE_COURSE_REPORT')</span>
                </a>   
            </li>
            @endif
            <!-- End:: Course Report Generation-->

            <!--Start :: Analytical Engine-->
            @if(in_array(Auth::user()->group_id,[1,3,4]))
            <li <?php
            $current = ( in_array($controllerName, array('basicinfowisedsanalytics', 'maritalinfowisedsanalytics'
                        , 'addresswisedsanalytics', 'passportinfowisedsanalytics', 'recsvcwisedsanalytics', 'bankinfowisedsanalytics'
                        , 'milqualwisedsanalytics', 'comcoursewisedsanalytics', 'otherinfowisedsanalytics', 'basicinfowisecmanalytics', 'maritalinfowisecmanalytics'
                        , 'addresswisecmanalytics', 'passportinfowisecmanalytics', 'recsvcwisecmanalytics', 'bankinfowisecmanalytics'
                        , 'milqualwisecmanalytics', 'comcoursewisecmanalytics', 'otherinfowisecmanalytics', 'celebrationcmanalytics'
                        , 'celebrationdsanalytics'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-pie-chart"></i>
                    <span class="title">@lang('label.ANALYTICAL_ENGINE')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php
                    $current = ( in_array($controllerName, array('celebrationcmanalytics', 'basicinfowisecmanalytics', 'maritalinfowisecmanalytics'
                                , 'addresswisecmanalytics', 'passportinfowisecmanalytics', 'recsvcwisecmanalytics', 'bankinfowisecmanalytics'
                                , 'milqualwisecmanalytics', 'comcoursewisecmanalytics', 'otherinfowisecmanalytics'))) ? 'start active open' : '';
                    ?>class="nav-item {{$current}}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <span class="title">@lang('label.CM_ANALYTICS')</span>
                            <span class="arrow"></span>
                        </a> 
                        <ul class="sub-menu">
                            <li <?php $current = ( in_array($controllerName, array('basicinfowisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('basicInfoWiseCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.BASIC_INFORMATION_WISE')</span>
                                </a> 
                            </li>

                            <li <?php $current = ( in_array($controllerName, array('comcoursewisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('comCourseWiseCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.COMMISSIONING_COURSE_WISE')</span>
                                </a> 
                            </li>

                            <li <?php $current = ( in_array($controllerName, array('maritalinfowisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('maritalInfoWiseCmAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.MARITIAL_INFORMATION_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('addresswisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('addressWiseCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.ADDRESS_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('passportinfowisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('passportInfoWiseCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.PASSPORT_INFO_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('recsvcwisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('recSvcWiseCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.REC_SVC_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('milqualwisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('milQualWiseCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.MIL_QUAL_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('bankinfowisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('bankInfoWiseCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.BANK_INFO_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('otherinfowisecmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('otherInfoWiseCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.OTHER_INFO_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('celebrationcmanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('celebrationCmAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.CELEBRATION_REPORT')</span>
                                </a> 
                            </li>

                        </ul>
                    </li>
                    @if(in_array(Auth::user()->group_id,[1,3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php
                    $current = ( in_array($controllerName, array('basicinfowisedsanalytics', 'maritalinfowisedsanalytics'
                                , 'addresswisedsanalytics', 'passportinfowisedsanalytics', 'recsvcwisedsanalytics', 'bankinfowisedsanalytics'
                                , 'milqualwisedsanalytics', 'comcoursewisedsanalytics', 'otherinfowisedsanalytics'
                                , 'celebrationdsanalytics'))) ? 'start active open' : '';
                    ?>class="nav-item {{$current}}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <span class="title">@lang('label.DS_ANALYTICS')</span>
                            <span class="arrow"></span>
                        </a> 
                        <ul class="sub-menu">
                            <li <?php $current = ( in_array($controllerName, array('basicinfowisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('basicInfoWiseDsAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.BASIC_INFORMATION_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('comcoursewisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('comCourseWiseDsAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.COMMISSIONING_COURSE_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('maritalinfowisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('maritalInfoWiseDsAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.MARITIAL_INFORMATION_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('addresswisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('addressWiseDsAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.ADDRESS_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('passportinfowisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('passportInfoWiseDsAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.PASSPORT_INFO_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('recsvcwisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('recSvcWiseDsAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.REC_SVC_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('milqualwisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('milQualWiseDsAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.MIL_QUAL_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('bankinfowisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('bankInfoWiseDsAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.BANK_INFO_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('otherinfowisedsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('otherInfoWiseDsAnalytics')}}" class="nav-link nav-toggle">
                                    <span class="title">@lang('label.OTHER_INFO_WISE')</span>
                                </a> 
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('celebrationdsanalytics'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('celebrationDsAnalytics')}}" class="nav-link">
                                    <span class="title">@lang('label.CELEBRATION_REPORT')</span>
                                </a> 
                            </li>

                        </ul>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            <!--End :: Analytical Engine-->

            <!-- Start:: Current Report-->

            <?php
            $current = (in_array($controllerName, array('mutualassessmentdetailedreportcrnt', 'mutualassessmentsummaryreportcrnt'
                        , 'eventlistreportcrnt', 'markinggroupsummaryreportcrnt', 'eventresultreportcrnt', 'eventresultcombinedreportcrnt'
                        , 'termresultreportcrnt', 'performanceanalysisreportcrnt', 'nominalrollreportcrnt', 'individualprofilereportcrnt', 'cmprofilereportcrnt'
                        , 'courseprogressiveresultreportcrnt', 'courseresultreportcrnt', 'armsservicewiseeventtrendreportcrnt'
                        , 'wingwiseeventtrendreportcrnt', 'commissioningcoursewiseeventtrendreportcrnt', 'cmgroupwiseeventtrendreportcrnt'
                        , 'armsservicewisesubeventtrendreportcrnt', 'wingwisesubeventtrendreportcrnt', 'commissioningcoursewisesubeventtrendreportcrnt'
                        , 'armsservicewiseperformancetrendreportcrnt', 'wingwiseperformancetrendreportcrnt'
                        , 'commissioningcoursewiseperformancetrendreportcrnt', 'dsmarkingtrendreportcrnt'
                        , 'overallperformancetrendreportcrnt', 'eventavgtrendreportcrnt', 'cmwiseeventtrendreportcrnt'
                        , 'dsremarksreportcrnt', 'cidsprofilereportcrnt', 'dseventtrendreportcrnt', 'appttocmreportcrnt'
                        , 'dsobsnreportcrnt', 'eventmarkingstatereportcrnt', 'cmcoursereportcrnt'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-bar-chart"></i>
                    <span class="title">
                        @if(in_array(Auth::user()->group_id,[3]) || in_array(Auth::user()->id, $dsDeligationList))
                        @lang('label.CURRENT_REPORT')
                        @else
                        @lang('label.REPORT')
                        @endif
                    </span>
                    <span class="arrow"></span>
                </a>

                <ul class="sub-menu">
                    <!--                    <li <?php $current = ( in_array($controllerName, array('nominalrollreportcrnt'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('nominalRollReportCrnt')}}" class="nav-link">
                                                <span class="title">@lang('label.NOMINAL_ROLL')</span>
                                            </a>
                                        </li>-->

                    @if(in_array(Auth::user()->group_id,[1,3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php $current = ( in_array($controllerName, array('cidsprofilereportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('ciDsProfileReportCrnt')}}" class="nav-link">
                            <span class="title">@lang('label.CI_DS_PROFILE')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[3,4]))
                    <!-- Individual Profile ReportCrnt -->
                    <li <?php $current = ( in_array($controllerName, array('cmprofilereportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('cmProfileReportCrnt')}}" class="nav-link">
                            <span class="title">@lang('label.CM_PROFILE')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[3,4]))
                    <!-- Individual Profile ReportCrnt -->
                    <li <?php $current = ( in_array($controllerName, array('individualprofilereportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('individualProfileReportCrnt')}}" class="nav-link">
                            <span class="title">@lang('label.INDIVIDUAL_PROFILE')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[3,4]))
                    <li <?php
                    $current = ( in_array($controllerName, array('dsremarksreportcrnt'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="{{ url('dsRemarksReportCrnt') }}" class="nav-link">
                            <span class="title">@lang('label.DS_RMKS')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[1,3]) || in_array(Auth::user()->id, $dsDeligationList))

                    <li <?php $current = ( in_array($controllerName, array('appttocmreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('apptToCmReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.APPT_TO_CM')</span>
                        </a>
                    </li>
                    @endif
                    <li <?php $current = ( in_array($controllerName, array('eventlistreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('eventListReportCrnt')}}" class="nav-link">
                            <span class="title">@lang('label.EVENT_MKS_WT')</span>
                        </a>
                    </li>
                    @if(in_array(Auth::user()->group_id,[1,3,4]))
                    <li <?php $current = ( in_array($controllerName, array('markinggroupsummaryreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('markingGroupSummaryReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.MARKING_GROUP_SUMMARY')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[3,4]))
                    <li <?php $current = ( in_array($controllerName, array('eventresultreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('eventResultReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_RESULT')</span>
                        </a>
                    </li>



                    @endif
                    @if(in_array(Auth::user()->group_id,[3]) || in_array(Auth::user()->id, $dsDeligationList) || (!empty($reportDeligationList) && in_array(3, $reportDeligationList) && in_array(Auth::user()->id, $reportDeligationDsList)))
                    <li <?php $current = ( in_array($controllerName, array('eventresultcombinedreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('eventResultCombinedReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_RESULT_COMBINED')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[1,3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php $current = ( in_array($controllerName, array('eventmarkingstatereportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('eventMarkingStateReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_MARKING_STATE')</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->group_id,[3]) || in_array(Auth::user()->id, $dsDeligationList) || (!empty($reportDeligationList) && in_array(4, $reportDeligationList) && in_array(Auth::user()->id, $reportDeligationDsList)))
                    <li <?php $current = ( in_array($controllerName, array('performanceanalysisreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('performanceAnalysisReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.PERFORMANCE_ANALYSIS')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id, [2,3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <!--Start :: Performance Trend Analysis-->
                    <li <?php
                    $current = ( in_array($controllerName, array('armsservicewiseperformancetrendreportcrnt', 'wingwiseperformancetrendreportcrnt'
                                , 'commissioningcoursewiseperformancetrendreportcrnt', 'overallperformancetrendreportcrnt'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <span class="title">@lang('label.PERFORMANCE_TREND_ANALYSIS')</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li <?php $current = ( in_array($controllerName, array('overallperformancetrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('overallPerformanceTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.OVERALL_PERFORMANCE_TREND')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('armsservicewiseperformancetrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('armsServiceWisePerformanceTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.ARMS_SERVICE_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('wingwiseperformancetrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('wingWisePerformanceTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.WING_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('commissioningcoursewiseperformancetrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('commissioningCourseWisePerformanceTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.COMMISSIONING_COURSE_WISE')</span>
                                </a>
                            </li>
                        </ul>
                    </li> 
                    <!--End :: Performance Trend Analysis-->
                    <!--Start :: Event Trend Analysis-->
                    <li <?php
                    $current = ( in_array($controllerName, array('armsservicewiseeventtrendreportcrnt', 'wingwiseeventtrendreportcrnt'
                                , 'commissioningcoursewiseeventtrendreportcrnt', 'cmgroupwiseeventtrendreportcrnt', 'eventavgtrendreportcrnt'
                                , 'cmwiseeventtrendreportcrnt'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <span class="title">@lang('label.EVENT_TREND_ANALYSIS')</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li <?php $current = ( in_array($controllerName, array('eventavgtrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('eventAvgTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.EVENT_AVG_TREND')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('cmwiseeventtrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('cmWiseEventTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.CM_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('armsservicewiseeventtrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('armsServiceWiseEventTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.ARMS_SERVICE_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('wingwiseeventtrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('wingWiseEventTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.WING_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('commissioningcoursewiseeventtrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('commissioningCourseWiseEventTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.COMMISSIONING_COURSE_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('cmgroupwiseeventtrendreportcrnt'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('cmGroupWiseEventTrendReportCrnt')}}" class="nav-link ">
                                    <span class="title">@lang('label.CM_GROUP_WISE')</span>
                                </a>
                            </li>
                        </ul>
                    </li> 
                    <!--End :: Event Trend Analysis-->
                    <!--Start :: DS Marking Trend Analysis-->
                    <li <?php $current = ( in_array($controllerName, array('dsmarkingtrendreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('dsMarkingTrendReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_MARKING_TREND')</span>
                        </a>
                    </li>
                    <!--End :: DS Marking Trend Analysis-->
                    @endif
                    @if(in_array(Auth::user()->group_id,[4]))
                    <li <?php $current = ( in_array($controllerName, array('dseventtrendreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('dsEventTrendReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_EVENT_TREND')</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->group_id,[3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php $current = ( in_array($controllerName, array('dsobsnreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('dsObsnReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_OBSN')</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->group_id,[2,3]) || in_array(Auth::user()->id, $dsDeligationList) || (!empty($reportDeligationList) && in_array(1, $reportDeligationList) && in_array(Auth::user()->id, $reportDeligationDsList)))
                    <li <?php $current = ( in_array($controllerName, array('termresultreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('termResultReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_RESULT')</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->group_id,[2,3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php $current = ( in_array($controllerName, array('courseprogressiveresultreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('courseProgressiveResultReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.COURSE_PROGRESSIVE_RESULT')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id,[2,3]) || in_array(Auth::user()->id, $dsDeligationList) || (!empty($reportDeligationList) && in_array(2, $reportDeligationList) && in_array(Auth::user()->id, $reportDeligationDsList)))
                    <li <?php $current = ( in_array($controllerName, array('courseresultreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('courseResultReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.COURSE_RESULT')</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->group_id,[1,3,4]))
                    <li <?php $current = ( in_array($controllerName, array('mutualassessmentsummaryreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('mutualAssessmentSummaryReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.MUTUAL_ASSESSMENT')&nbsp;(@lang('label.SUMMARY'))</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->group_id,[1,3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php $current = ( in_array($controllerName, array('mutualassessmentdetailedreportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('mutualAssessmentDetailedReportCrnt')}}" class="nav-link ">
                            <span class="title">@lang('label.MUTUAL_ASSESSMENT')&nbsp;(@lang('label.DETAILED'))</span>
                        </a>
                    </li>
                    @endif
                    <li <?php $current = ( in_array($controllerName, array('cmcoursereportcrnt'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('cmCourseReportCrnt')}}" class="nav-link">
                            <span class="title">@lang('label.CM_COURSE_REPORT')</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- End:: Current Report-->

            <!-- Start:: Report Archive-->
            @if(in_array(Auth::user()->group_id, [3]) || in_array(Auth::user()->id, $dsDeligationList))

            <?php
            $current = (in_array($controllerName, array('mutualassessmentdetailedreport', 'mutualassessmentsummaryreport'
                        , 'eventlistreport', 'markinggroupsummaryreport', 'eventresultreport', 'eventresultcombinedreport'
                        , 'termresultreport', 'performanceanalysisreport', 'nominalrollreport', 'individualprofilereport'
                        , 'courseprogressiveresultreport', 'courseresultreport', 'armsservicewiseeventtrendreport'
                        , 'wingwiseeventtrendreport', 'commissioningcoursewiseeventtrendreport', 'cmgroupwiseeventtrendreport'
                        , 'armsservicewisesubeventtrendreport', 'wingwisesubeventtrendreport', 'commissioningcoursewisesubeventtrendreport'
                        , 'armsservicewiseperformancetrendreport', 'wingwiseperformancetrendreport'
                        , 'commissioningcoursewiseperformancetrendreport', 'dsmarkingtrendreport'
                        , 'overallperformancetrendreport', 'eventavgtrendreport', 'cmwiseeventtrendreport'
                        , 'dsremarksreport', 'cidsprofilereport', 'dsobsnreport', 'cmcoursereport'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-area-chart"></i>
                    <span class="title">@lang('label.REPORT_ARCHIVE')</span>
                    <span class="arrow"></span>
                </a>

                <ul class="sub-menu">
                    <!--                    <li <?php $current = ( in_array($controllerName, array('nominalrollreport'))) ? 'start active open' : ''; ?>
                                            class="nav-item {{$current}}">
                                            <a href="{{url('nominalRollReport')}}" class="nav-link">
                                                <span class="title">@lang('label.NOMINAL_ROLL')</span>
                                            </a>
                                        </li>-->
                    <li <?php $current = ( in_array($controllerName, array('cidsprofilereport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('ciDsProfileReport')}}" class="nav-link">
                            <span class="title">@lang('label.CI_DS_PROFILE')</span>
                        </a>
                    </li>
                    <!-- Individual Profile Report -->
                    <li <?php $current = ( in_array($controllerName, array('individualprofilereport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('individualProfileReport')}}" class="nav-link">
                            <span class="title">@lang('label.INDIVIDUAL_PROFILE')</span>
                        </a>
                    </li>
                    <li <?php
                    $current = ( in_array($controllerName, array('dsremarksreport'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="{{ url('dsRemarksReport') }}" class="nav-link">
                            <span class="title">@lang('label.DS_RMKS')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('eventlistreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('eventListReport')}}" class="nav-link">
                            <span class="title">@lang('label.EVENT_MKS_WT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('markinggroupsummaryreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('markingGroupSummaryReport')}}" class="nav-link ">
                            <span class="title">@lang('label.MARKING_GROUP_SUMMARY')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('eventresultreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('eventResultReport')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_RESULT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('eventresultcombinedreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('eventResultCombinedReport')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_RESULT_COMBINED')</span>
                        </a>
                    </li>


                    <li <?php $current = ( in_array($controllerName, array('performanceanalysisreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('performanceAnalysisReport')}}" class="nav-link ">
                            <span class="title">@lang('label.PERFORMANCE_ANALYSIS')</span>
                        </a>
                    </li>
                    <!--Start :: Performance Trend Analysis-->
                    <li <?php
                    $current = ( in_array($controllerName, array('armsservicewiseperformancetrendreport', 'wingwiseperformancetrendreport'
                                , 'commissioningcoursewiseperformancetrendreport', 'overallperformancetrendreport'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <span class="title">@lang('label.PERFORMANCE_TREND_ANALYSIS')</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li <?php $current = ( in_array($controllerName, array('overallperformancetrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('overallPerformanceTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.OVERALL_PERFORMANCE_TREND')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('armsservicewiseperformancetrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('armsServiceWisePerformanceTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.ARMS_SERVICE_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('wingwiseperformancetrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('wingWisePerformanceTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.WING_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('commissioningcoursewiseperformancetrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('commissioningCourseWisePerformanceTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.COMMISSIONING_COURSE_WISE')</span>
                                </a>
                            </li>
                        </ul>
                    </li> 
                    <!--End :: Performance Trend Analysis-->
                    <!--Start :: Event Trend Analysis-->
                    <li <?php
                    $current = ( in_array($controllerName, array('armsservicewiseeventtrendreport', 'wingwiseeventtrendreport'
                                , 'commissioningcoursewiseeventtrendreport', 'cmgroupwiseeventtrendreport', 'eventavgtrendreport'
                                , 'cmwiseeventtrendreport'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <span class="title">@lang('label.EVENT_TREND_ANALYSIS')</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li <?php $current = ( in_array($controllerName, array('eventavgtrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('eventAvgTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.EVENT_AVG_TREND')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('cmwiseeventtrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('cmWiseEventTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.CM_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('armsservicewiseeventtrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('armsServiceWiseEventTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.ARMS_SERVICE_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('wingwiseeventtrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('wingWiseEventTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.WING_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('commissioningcoursewiseeventtrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('commissioningCourseWiseEventTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.COMMISSIONING_COURSE_WISE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('cmgroupwiseeventtrendreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('cmGroupWiseEventTrendReport')}}" class="nav-link ">
                                    <span class="title">@lang('label.CM_GROUP_WISE')</span>
                                </a>
                            </li>
                        </ul>
                    </li> 
                    <!--End :: Event Trend Analysis-->

                    <!--Start :: DS Marking Trend Analysis-->
                    <li <?php $current = ( in_array($controllerName, array('dsmarkingtrendreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('dsMarkingTrendReport')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_MARKING_TREND')</span>
                        </a>
                    </li>
                    <!--End :: DS Marking Trend Analysis-->

                    <li <?php $current = ( in_array($controllerName, array('dsobsnreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('dsObsnReport')}}" class="nav-link ">
                            <span class="title">@lang('label.DS_OBSN')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termresultreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('termResultReport')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_RESULT')</span>
                        </a>
                    </li>


                    <li <?php $current = ( in_array($controllerName, array('courseprogressiveresultreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('courseProgressiveResultReport')}}" class="nav-link ">
                            <span class="title">@lang('label.COURSE_PROGRESSIVE_RESULT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('courseresultreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('courseResultReport')}}" class="nav-link ">
                            <span class="title">@lang('label.COURSE_RESULT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('mutualassessmentsummaryreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('mutualAssessmentSummaryReport')}}" class="nav-link ">
                            <span class="title">@lang('label.MUTUAL_ASSESSMENT')&nbsp;(@lang('label.SUMMARY'))</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('mutualassessmentdetailedreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('mutualAssessmentDetailedReport')}}" class="nav-link ">
                            <span class="title">@lang('label.MUTUAL_ASSESSMENT')&nbsp;(@lang('label.DETAILED'))</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('cmcoursereport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('cmCourseReport')}}" class="nav-link">
                            <span class="title">@lang('label.CM_COURSE_REPORT')</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            <!-- End:: Report Archive-->

            <!--Start:: Reference Archive Setup-->

            @if(in_array(Auth::user()->group_id,[1]))
            <li <?php
            $current = ( in_array($controllerName, array('mediacontenttype', 'contentclassification', 'contentcategory'
                        , 'module'))) ? 'start active open' : '';
            ?> class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-archive"></i>
                    <span class="title">@lang('label.REFERENCE_ARCHIVE_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">

                    <li <?php $current = ( in_array($controllerName, array('mediacontenttype'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('mediaContentType')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.MEDIA_CONTENT_TYPE')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('contentclassification'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('contentClassification')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.CONTENT_CLASSIFICATION')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('module'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('module')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.MODULE')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('contentcategory'))) ? 'start active open' : ''; ?>class="nav-item {{$current}}">
                        <a href="{{url('contentCategory')}}" class="nav-link nav-toggle">
                            <span class="title">@lang('label.CONTENT_CATEGORY_MANAGEMENT')</span>
                        </a>
                    </li>

                </ul>
            </li>
            @endif

            <!--End Reference Archive Setup-->




            <!--Start:: Reference Archive-->
            <?php
            $current = (in_array($controllerName, array('content', 'documentsearchreport', 'dailydocreport', 'monthlydocreport', 'originatorwisedocreport'
                        , 'classificationwisedocreport', 'catwisedocreport', 'coursewisedocreport', 'catwisedocsummary', 'coursewisedocsummary'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cubes"></i>
                    <span class="title">@lang('label.REFERENCE_ARCHIVE')</span>
                    <span class="arrow"></span>
                </a>

                <ul class="sub-menu">
                    <!--Start:: Content Management-->

                    @if(in_array(Auth::user()->group_id,[1,3,4]))
                    <li <?php $current = ( in_array($controllerName, array('content'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/content')}}" class="nav-link">
                            <span class="title"> @lang('label.REFERENCE_ARCHIVE_CONTENT')</span>
                        </a>
                    </li>
                    @endif

                    <!--End:: Content Management-->

                    <li <?php $current = ( in_array($controllerName, array('documentsearchreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/documentSearch')}}" class="nav-link">
                            <span class="title">@lang('label.DOC_SEARCH')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('dailydocreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/dailyDocReport')}}" class="nav-link">
                            <span class="title">@lang('label.DAILY_DOC_REPORT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('monthlydocreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/monthlyDocReport')}}" class="nav-link">
                            <span class="title">@lang('label.MONTHLY_DOC_REPORT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('catwisedocreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/catWiseDocReport')}}" class="nav-link">
                            <span class="title">@lang('label.CAT_WISE_REPORT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('originatorwisedocreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/originatorWiseDocReport')}}" class="nav-link">
                            <span class="title">@lang('label.ORIGINATOR_WISE_REPORT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('classificationwisedocreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/classificationWiseDocReport')}}" class="nav-link">
                            <span class="title">@lang('label.CLASSIFICATION_WISE_REPORT')</span>
                        </a>
                    </li>
                    @if(in_array(Auth::user()->group_id,[1,2,3]) || in_array(Auth::user()->id, $dsDeligationList))
                    <li <?php $current = ( in_array($controllerName, array('coursewisedocreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/courseWiseDocReport')}}" class="nav-link">
                            <span class="title">@lang('label.COURSE_WISE_REPORT')</span>
                        </a>
                    </li>
                    @endif

                    <!--summary-->
                    <li <?php $current = ( in_array($controllerName, array('catwisedocsummary'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/catWiseDocSummary')}}" class="nav-link">
                            <span class="title">@lang('label.CATEGORY_WISE_SUMMARY')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('coursewisedocsummary'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/courseWiseDocSummary')}}" class="nav-link">
                            <span class="title">@lang('label.COURSE_WISE_SUMMARY')</span>
                        </a>
                    </li>

                </ul>
            </li>

            <!--End:: Reference Archive-->

            <!-- start:: Manual menu -->
            <?php
            $current = (in_array($controllerName, array('manual', 'processmanual'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-clipboard"></i>
                    <span class="title">@lang('label.MANUAL')</span>
                    <span class="arrow"></span>
                </a>

                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('manual'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/userManual')}}" class="nav-link" target="_new">
                            <span class="title"> @lang('label.SYSTEM_MANUAL')</span>
                        </a>
                    </li>
                    <!-- start:: Process Manual menu -->
                    <li <?php $current = ( in_array($controllerName, array('processmanual'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/processManual')}}" class="nav-link" target="_new">
                            <span class="title"> @lang('label.PROCESS_MANUAL')</span>
                        </a>
                    </li>
                    <!-- end:: Process Manual menu -->
                </ul>
            </li>


            <!-- end:: Manual menu -->


        </ul>
    </div>
</div>
