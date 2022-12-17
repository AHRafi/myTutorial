<?php
$currentControllerFunction = Route::currentRouteAction();
$currentCont = preg_match('/([a-z]*)@/i', request()->route()->getActionName(), $currentControllerFunction);
$controllerName = str_replace('controller', '', strtolower($currentControllerFunction[1]));
$routeName = strtolower(Route::getFacadeRoot()->current()->uri());

//echo '<pre>';print_r($permittedReportArr);echo '</pre>';
?>
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul id="addsidebarFullMenu" class="page-sidebar-menu  page-header-fixed" data-keep-expanded="false"
            data-auto-scroll="true" data-slide-speed="200" style="padding-top: 39px">
            <!--li class="sidebar-toggler-wrapper hide">
            <div class="sidebar-toggler">
                <span></span>
            </div>
        </li-->

            <!-- start dashboard menu -->
            <li
                <?php $current = ( in_array($controllerName, array('dashboard'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                <a href="{{url('/dashboard')}}" class="nav-link ">
                    <i class="icon-home"></i>
                    <span class="title"> @lang('label.DASHBOARD')</span>
                </a>
            </li>
            @if(in_array(Auth::user()->group_id, ['3']))
            <!-- start administrative menu -->
            <li
            <?php
            $current = ( in_array($controllerName, array('rank', 'appointment', 'trainingyear', 'armsservice', 'term',
                        'center', 'trade', 'module', 'subject', 'event', 'user', 'recruitbatch'
                        , 'majorevent', 'dropcategory', 'noofparticular'))) ? 'start active open' : '';
            ?>class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cogs"></i>
                    <span class="title">@lang('label.ADMIN_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
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
                    <li <?php $current = ( in_array($controllerName, array('armsservice'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/armsService')}}" class="nav-link ">
                            <span class="title">@lang('label.ARMS_SERVICES')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('trainingyear'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/trainingYear')}}" class="nav-link">
                            <span class="title">@lang('label.TRAINING_YEAR')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('center'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/center')}}" class="nav-link">
                            <span class="title">@lang('label.CENTER')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('term'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/term')}}" class="nav-link">
                            <span class="title">@lang('label.TERM')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('trade'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/trade')}}" class="nav-link ">
                            <span class="title">@lang('label.TRADE')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('module'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/module')}}" class="nav-link ">
                            <span class="title">@lang('label.MODULE')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('subject'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/subject')}}" class="nav-link ">
                            <span class="title">@lang('label.SUBJECT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('event'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/event')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('majorevent'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/majorEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.MAJOR_EVENT')</span>
                        </a>
                    </li>

                    <li
                        <?php $current = ( in_array($controllerName, array('recruitbatch'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/recruitBatch')}}" class="nav-link ">
                            <span class="title">@lang('label.RECRUIT_BATCH')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('noofparticular'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/noOfParticular')}}" class="nav-link ">
                            <span class="title">@lang('label.NO_OF_PARTICULAR')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('user'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/user')}}" class="nav-link ">
                            <span class="title">@lang('label.USER')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('dropcategory'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/dropCategory')}}" class="nav-link ">
                            <span class="title">@lang('label.DROP_CATEGORY')</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php
            $current = (in_array($controllerName, array('centertobatch', 'moduletobatch'
                        , 'subjecttomodule', 'eventtosubject'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">@lang('label.RELATIONSHIP_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li
                        <?php $current = ( in_array($controllerName, array('centertobatch'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/centerToBatch')}}" class="nav-link ">
                            <span class="title">@lang('label.CENTER_TO_BATCH')</span>
                        </a>
                    </li>
                    <li
                        <?php $current = ( in_array($controllerName, array('moduletobatch'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/moduleToBatch')}}" class="nav-link ">
                            <span class="title">@lang('label.MODULE_TO_BATCH')</span>
                        </a>
                    </li>
                    <li
                        <?php $current = ( in_array($controllerName, array('subjecttomodule'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/subjectToModule')}}" class="nav-link ">
                            <span class="title">@lang('label.SUBJECT_TO_MODULE')</span>
                        </a>
                    </li>
                    <li
                        <?php $current = ( in_array($controllerName, array('eventtosubject'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/eventToSubject')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_TO_SUBJECT')</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- START:: Recruit Parameters Setup -->
            <li
            <?php
            $current = ( in_array($controllerName, array('skill', 'profession', 'civilcasecategory'))) ? 'start active open' : '';
            ?>class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-bars"></i>
                    <span class="title">@lang('label.RECRUIT_PARAMETERS')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('skill'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/skill')}}" class="nav-link">
                            <span class="title">@lang('label.SKILL')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('profession'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/profession')}}" class="nav-link">
                            <span class="title">@lang('label.PROFESSION')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('civilcasecategory'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/civilCaseCategory')}}" class="nav-link">
                            <span class="title">@lang('label.CIVIL_CASE_CATEGORY')</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- END:: Recruit Parameters Setup -->


            <li <?php
            $current = ( in_array($controllerName, array('modulewtdistr', 'subjectwtdistr', 'eventwtdistr',
                        'observationwtdistr'))) ? 'start active open' : '';
            ?> class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-check"></i>
                    <span class="title">@lang('label.MKS_WT_DISTR')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li
                        <?php $current = ( in_array($controllerName, array('modulewtdistr'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/moduleWtDistr')}}" class="nav-link ">
                            <span class="title">@lang('label.MODULE_WT_DISTR')</span>
                        </a>
                    </li>
                    <li
                        <?php $current = ( in_array($controllerName, array('subjectwtdistr'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/subjectWtDistr')}}" class="nav-link ">
                            <span class="title">@lang('label.SUBJECT_WT_DISTR')</span>
                        </a>
                    </li>

                    <li
                        <?php $current = ( in_array($controllerName, array('eventwtdistr'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/eventWtDistr')}}" class="nav-link ">
                            <span class="title">@lang('label.EVENT_WT_DISTR')</span>
                        </a>
                    </li>

                    <li
                        <?php $current = ( in_array($controllerName, array('observationwtdistr'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                        <a href="{{url('/observationWtDistr')}}" class="nav-link ">
                            <span class="title">@lang('label.OBSERVATION_WT_DISTR')</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if(in_array(Auth::user()->group_id, ['5']))
            <li
                <?php $current = ( in_array($controllerName, array('user'))) ? 'start active open' : ''; ?>class="nav-item {{$current}} nav-item ">
                <a href="{{url('/user')}}" class="nav-link ">
                    <i class="fa fa-user"></i>
                    <span class="title">@lang('label.USER')</span>
                </a>
            </li>
            <li <?php
            $current = (in_array($controllerName, array('particular', 'platoon', 'termtobatch', 'company'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-adjust"></i>
                    <span class="title">@lang('label.CENTER_ADMINISTRATION')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('particular'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/particular')}}" class="nav-link ">
                            <span class="title">@lang('label.PARTICULAR')</span>
                        </a>
                    </li>

                    <li <?php $current = ( in_array($controllerName, array('platoon'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/platoon')}}" class="nav-link ">
                            <span class="title">@lang('label.PLATOON')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('company'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/company')}}" class="nav-link ">
                            <span class="title">@lang('label.COMPANY')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termtobatch')) && ($routeName != 'termtobatch/activationorclosing')) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('/termToBatch')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_SCHEDULING')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termtobatch')) && ($routeName == 'termtobatch/activationorclosing' )) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('termToBatch/activationOrClosing')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_SCHEDULING_ACTIVATION_CLOSING')</span>
                        </a>
                    </li>

                </ul>
            </li>
            @endif 


            @if(in_array(Auth::user()->group_id, ['5']))
            <?php
            $current = (in_array($controllerName, array('platoontobatch', 'particulartoevent', 'citobatch', 'oictobatch'
                        , 'plcmdrtoplatoon', 'termtoevent', 'termtoparticular', 'companytoplatoon'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">@lang('label.RELATIONSHIP_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('particulartoevent'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/particularToEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.PARTICULAR_TO_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('platoontobatch'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/platoonToBatch')}}" class="nav-link ">
                            <span class="title">@lang('label.PLATOON_TO_BATCH')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('companytoplatoon'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/companyToPlatoon')}}" class="nav-link ">
                            <span class="title">@lang('label.COMPANY_TO_PALTOON')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('citobatch')) && ($routeName == 'citobatch' )) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('ciToBatch')}}" class="nav-link ">
                            <span class="title">@lang('label.CI_TO_BATCH')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('oictobatch')) && ($routeName == 'oictobatch' )) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('oicToBatch')}}" class="nav-link ">
                            <span class="title">@lang('label.OIC_TO_BATCH')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('plcmdrtoplatoon')) && ($routeName == 'plcmdrtoplatoon' )) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('pLCmdrToPlatoon')}}" class="nav-link ">
                            <span class="title">@lang('label.PL_CMDR_TO_PLATOON')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termtoevent'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('termToEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_TO_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('termtoparticular'))) ? 'start active open' : ''; ?> class="nav-item {{$current}}">
                        <a href="{{url('termToParticular')}}" class="nav-link ">
                            <span class="title">@lang('label.TERM_TO_PARTICULAR')</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li <?php
            $current = ( in_array($controllerName, array('particularwtdistr', 'tradeeventmksdistr'))) ? 'start active open' : '';
            ?> class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-check"></i>
                    <span class="title">@lang('label.MKS_WT_DISTR')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('tradeeventmksdistr'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/tradeEventMksDistr')}}" class="nav-link ">
                            <span class="title">@lang('label.TRADE_MKS_DISTR')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('particularwtdistr'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/particularWtDistr')}}" class="nav-link ">
                            <span class="title">@lang('label.PARTICULAR_WT_DISTR')</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if(in_array(Auth::user()->group_id, ['3','5']))
            <?php
            $current = (in_array($controllerName, array('revisednoofparticular', 'revisedevent'
                        , 'revisedparticulartoevent', 'revisedparticularwtdistr'))) ? 'start active open' : '';
            ?>
            <li class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cog"></i>
                    <span class="title">@lang('label.REVISED_SETUP')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @if(in_array(Auth::user()->group_id, ['3']))
                    <li <?php $current = ( in_array($controllerName, array('revisedevent'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/revisedEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.REVISED_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('revisednoofparticular'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/revisedNoOfParticular')}}" class="nav-link ">
                            <span class="title">@lang('label.REVISED_NO_OF_PARTICULAR')</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id, ['5']))
                    <li <?php $current = ( in_array($controllerName, array('revisedparticulartoevent'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/revisedParticularToEvent')}}" class="nav-link ">
                            <span class="title">@lang('label.REVISED_PARTICULAR_TO_EVENT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('revisedparticularwtdistr'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/revisedParticularWtDistr')}}" class="nav-link ">
                            <span class="title">@lang('label.REVISED_PARTICULAR_WT_DISTR')</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(in_array(Auth::user()->group_id, ['5']))

            <li <?php
            $current = ( in_array($controllerName, array('recruit', 'recruittoplatoon', 'recruittotrade', 'droprecruit'))) ?
                    'start active open' : '';
            ?> class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-users"></i>
                    <span class="title">@lang('label.RECRUIT_MANAGEMENT')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('recruit'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/recruit')}}" class="nav-link ">
                            <span class="title">@lang('label.RECRUIT')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('recruittoplatoon'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/recruitToPlatoon')}}" class="nav-link ">
                            <span class="title">@lang('label.RECRUIT_TO_PLATOON')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('recruittotrade'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/recruitToTrade')}}" class="nav-link ">
                            <span class="title">@lang('label.RECRUIT_TO_TRADE')</span>
                        </a>
                    </li>
                    <li <?php $current = ( in_array($controllerName, array('droprecruit'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('/dropRecruit')}}" class="nav-link ">
                            <span class="title">@lang('label.DROP_RECRUIT')</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--admin panel-->
            <li <?php $current = ( in_array($controllerName, array('unlockdonationmark'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-unlock"></i>
                    <span class="title">@lang('label.UNLOCK_REQUEST')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('unlockdonationmark'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('unlockDonationMark/ciUnlockReq')}}" class="nav-link ">
                            <span class="title">@lang('label.UNLOCK_REQUEST_CI_DONATION_MKS')</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!--//new add for admin (Rect State unlock)-->

            <li <?php $current = ( in_array($controllerName, array('rctstate'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-clock-o"></i>
                    <span class="title">@lang('label.RCT_STATE')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li <?php $current = ( in_array($controllerName, array('rctstate'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('rctState/unlockReqList')}}" class="nav-link ">
                            <span class="title">@lang('label.UNLOCK_REQUEST')</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            @if(Auth::user()->group_id == '8' && !empty($assignedCmdrInfo))
            <li <?php $current = ( in_array($controllerName, array('assignmks'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="{{url('/assignMks')}}" class="nav-link ">
                    <i class="fa fa-pencil-square-o"></i>
                    <span class="title">@lang('label.ASSIGN_MKS')</span>
                </a>
            </li>
            <!--platoon Cmdr-->
            <li <?php $current = ( in_array($controllerName, array('rctstate'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="{{url('/rctState')}}" class="nav-link ">
                    <i class="fa fa-clock-o"></i>
                    <span class="title">@lang('label.RCT_STATE')</span>
                </a>
            </li>
            @endif
            <!--ci panel-->
            @if(in_array(Auth::user()->group_id, [1,2,3,4,5,6,7]))
            <li <?php $current = ( in_array($routeName, array('dynamicsearch'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="{{url('dynamicSearch')}}" class="nav-link ">
                    <i class="fa fa-search"></i>
                    <span class="title" id="dynamicSearch" >@lang('label.DYNAMIC_SEARCH')</span>
                </a>
            </li>
            @endif
            <?php
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
            ?>
            @if(!in_array(Auth::user()->group_id,[1,2,4]))
            <?php
            $iconArr = [];
            ?>
            <!--not show ul && li-->
            <li <?php
            $current = ( in_array($controllerName, array('platoonwiseresultreport', 'droprecruitlist', 'markwtdistrreport',
                        'eventwiseresultreport', 'eventwiseresultcom', 'recruitlistreport', 'courseresultreport', 'coursedetailsresultreport'
                        , 'platoonlistreport', 'recruitprofilereport', 'recruitbiodatareport', 'coursereport'
                        , 'statewisedetailsreport', 'religionwisesummaryreport', 'maritalstatusreport', 'familystatusreport', 'politicallyinvolvedlist'
                        , 'rectskillsetreport', 'jobexperiencedlist', 'zonalstate', 'academicreport'
                        , 'borderdistancereport'))) ? 'start active open' : '';
            ?>
                class="nav-item {{$current}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-file-text-o"></i>
                    <span class="title">@lang('label.REPORT')</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @endif
                    <!-- Second Ul & Li ---> 
                    <li <?php
                    $current = ( in_array($controllerName, array('platoonwiseresultreport',
                                'eventwiseresultreport', 'eventwiseresultcom', 'courseresultreport', 'coursedetailsresultreport'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-edit"></i>
                            <span class="title">@lang('label.RESULT')</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li <?php $current = ( in_array($controllerName, array('platoonwiseresultreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('platoonWiseResult')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[2])?$iconArr[2]:''}}"></i>
                                    <span class="title">@lang('label.PLATOON_WISE_RESULT')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('eventwiseresultreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('eventWiseResult')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[3])?$iconArr[3]:''}}"></i>
                                    <span class="title">@lang('label.EVENT_WISE_RESULT')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('eventwiseresultcom'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('eventWiseResultCom')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[4])?$iconArr[4]:''}}"></i>
                                    <span class="title">@lang('label.EVENT_WISE_RESULT')<br /> (@lang('label.COMBINATION'))</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('courseresultreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('courseResult')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[5])?$iconArr[5]:''}}"></i>
                                    <span class="title">@lang('label.COURSE_RESULT')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('coursedetailsresultreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('courseDetailsResult')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[6])?$iconArr[6]:''}}"></i>
                                    <span class="title">@lang('label.COURSE_DETAILS_RESULT')</span>
                                </a>
                            </li>

                            @if(!in_array(Auth::user()->group_id,[1,2,4]))
                            <!--not show ul && li-->
                        </ul>
                    </li>
                    @endif

                    <!-- Third Ul & Li -->
                    <li <?php
                    $current = ( in_array($controllerName, array('droprecruitlist', 'recruitlistreport'
                                ,'platoonlistreport', 'recruitprofilereport', 'recruitbiodatareport', 'coursereport'
                                , 'statewisedetailsreport', 'religionwisesummaryreport', 'maritalstatusreport', 'familystatusreport', 'politicallyinvolvedlist'
                                , 'rectskillsetreport', 'jobexperiencedlist', 'zonalstate', 'academicreport'
                                , 'borderdistancereport'))) ? 'start active open' : '';
                    ?>
                        class="nav-item {{$current}}">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-line-chart"></i>
                            <span class="title">@lang('label.RCT_STATE')</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu second-layer">
                            @if(in_array(Auth::user()->group_id, [7,8]))
                            <li <?php $current = ( in_array($controllerName, array('statewisedetailsreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('stateWiseDetails')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[7])?$iconArr[7]:''}}"></i>
                                    <span class="title">@lang('label.STATE_WISE_DETAILS')</span>
                                </a>
                            </li>
                            @endif
                            <!-- center Observer admin panel -->
                            @if(in_array(Auth::user()->group_id, ['4','5','6']))
                            <li <?php $current = ( in_array($controllerName, array('droprecruitlist'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('dropRecruitList')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[8])?$iconArr[8]:''}}"></i>
                                    <span class="title">@lang('label.DROP_RECRUIT_LIST')</span>
                                </a>
                            </li>
                            @endif
                            @if(in_array(Auth::user()->group_id, ['1','2','3','4','5','6']))
                            <li <?php $current = ( in_array($controllerName, array('recruitlistreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('recruitList')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[11])?$iconArr[11]:''}}"></i>
                                    <span class="title">@lang('label.RECRUIT_PASSOUT_STATE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('platoonlistreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('platoonList')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[12]) ? $iconArr[12]:''}}"></i>
                                    <span class="title">@lang('label.PLATOON_STATE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('recruitbiodatareport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('recruitBioData')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[13])?$iconArr[13]:''}}"></i>
                                    <span class="title">@lang('label.RECRUIT_BIO_DATA')</span>
                                </a>
                            </li>

                            <li <?php $current = ( in_array($controllerName, array('recruitprofilereport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('recruitProfile')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[14])?$iconArr[14]:''}}"></i>
                                    <span class="title">@lang('label.RECRUIT_PROFILE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('coursereport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('courseReport')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[15])?$iconArr[15]:''}}"></i>
                                    <span class="title">@lang('label.COURSE_REPORT')</span>
                                </a>
                            </li>
                            <!--recruit state report-->
                            <li <?php $current = ( in_array($controllerName, array('statewisedetailsreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('stateWiseDetails')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[16])?$iconArr[16]:''}}"></i>
                                    <span class="title">@lang('label.STATE_WISE_DETAILS')</span>
                                </a>
                            </li>
                            @endif  <!--end of 1,2,3,5-->
                            <!--new report link-->
                            @if(!in_array(Auth::user()->group_id, ['8']))

                            <li <?php $current = ( in_array($controllerName, array('religionwisesummaryreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('religionWiseSummaryReport')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[17])?$iconArr[17]:''}}"></i>
                                    <span class="title">@lang('label.RELIGOIN_WISE_SUMMARY')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('maritalstatusreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('maritalStatusReport')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[18])?$iconArr[18]:''}}"></i>
                                    <span class="title">@lang('label.MARITAL_STATUS_WISE_REPORT')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('jobexperiencedlist'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('jobExperiencedList')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[19])?$iconArr[19]:''}}"></i>
                                    <span class="title">@lang('label.JOB_EXPERIENCED_LIST')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('politicallyinvolvedlist'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('politicallyInvolvedList')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[20])?$iconArr[20]:''}}"></i>
                                    <span class="title">@lang('label.POLITICAL_INVOLVED_LIST')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('rectskillsetreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('rectSkillSetReport')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[21])?$iconArr[21]:''}}"></i>
                                    <span class="title">@lang('label.RECT_SKILL_SET_REPORT')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('zonalstate'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('zonalState')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[22])?$iconArr[22]:''}}"></i>
                                    <span class="title">@lang('label.ZONAL_STATE')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('academicreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('academicReport')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[23])?$iconArr[23]:''}}"></i>
                                    <span class="title">@lang('label.ACADEMIC_REPORT')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('borderdistancereport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('borderDistanceReport')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[24])?$iconArr[24]:''}}"></i>
                                    <span class="title">@lang('label.BORDER_DISTANCE_REPORT')</span>
                                </a>
                            </li>
                            <li <?php $current = ( in_array($controllerName, array('familystatusreport'))) ? 'start active open' : ''; ?>
                                class="nav-item {{$current}}">
                                <a href="{{url('familyStatusReport')}}" class="nav-link ">
                                    <i class="{{!empty($iconArr[25])?$iconArr[25]:''}}"></i>
                                    <span class="title">@lang('label.FAMILY_STATUS_REPORT')</span>
                                </a>
                            </li>

                            @endif
                            @if(!in_array(Auth::user()->group_id,[1,2,4]))
                            <!--not show ul && li-->
                        </ul>
                    </li>
                    @endif
                    @if(in_array(Auth::user()->group_id, ['1','2','3','4','5','6']))
                    
                    <li <?php $current = ( in_array($controllerName, array('markwtdistrreport'))) ? 'start active open' : ''; ?>
                        class="nav-item {{$current}}">
                        <a href="{{url('markWtDistr')}}" class="nav-link ">
                            <i class="{{!empty($iconArr[10])?$iconArr[10]: 'fa fa-pie-chart'}}"></i>
                            <span class="title">@lang('label.MARKS_WT_DISTRIBUTION')</span>
                        </a>
                    </li>
                    @endif  <!--end of 1,2,3,5-->
                    @if(!in_array(Auth::user()->group_id,[1,2,4]))
                    <!--not show ul && li-->
                </ul>
            </li>
            @endif
            <!--oic panel-->
            @if(in_array(Auth::user()->group_id, ['7']))
            <li <?php $current = ( in_array($controllerName, array('unlockrequest'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="{{url('unlockRequest')}}" class="nav-link ">
                    <i class="fa fa-unlock" style="font-size:20px;"></i>
                    <span class="title">@lang('label.UNLOCK_REQUEST')</span>
                </a>
            </li>
            <li <?php $current = ( in_array($controllerName, array('oicdonationmks'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="{{url('/oicDonationMks')}}" class="nav-link ">
                    <i class="fa fa-check-circle" style="font-size:20px;"></i>
                    <span class="title">@lang('label.OBSN_MKS')</span>
                </a>
            </li>
            @endif
            <!--ci panel-->
            @if(in_array(Auth::user()->group_id, ['6']))
            <li <?php $current = ( in_array($controllerName, array('unlockdonationmark'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="{{url('unlockDonationMark/oicUnlockReq')}}" class="nav-link ">
                    <i class="fa fa-unlock" style="font-size:20px;"></i>
                    <span class="title">@lang('label.UNLOCK_REQUEST')</span>
                </a>
            </li>
            <li <?php $current = ( in_array($controllerName, array('cidonationmks'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="{{url('/ciDonationMks')}}" class="nav-link ">
                    <i class="fa fa-check-circle" style="font-size:20px;"></i>
                    <span class="title">@lang('label.OBSN_MKS')</span>
                </a>
            </li>
            @endif
            @if(in_array(Auth::user()->group_id, ['3']))
            <li <?php $current = ( in_array($controllerName, array('slider'))) ? 'start active open' : ''; ?>
                class="nav-item {{$current}}">
                <a href="{{url('slider')}}" class="nav-link ">
                    <i class="fa fa-file-image-o"></i>
                    <span class="title">@lang('label.SLIDER')</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>
