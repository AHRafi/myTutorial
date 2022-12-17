<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="<?php echo e(url('/dashboard')); ?>">
                <img src="<?php echo e(URL::to('/')); ?>/public/img/sint_ams_logo.png" class="logo-max-width" height="100" alt="logo" />
            </a>
            <!--            <div class="menu-toggler sidebar-toggler">
                            <a title="" data-container="body" class="btn-show-hide-link">
                                <i class="btn">
                                    <span id="fullMenu" data-fullMenu="1"><i class="fa fa-bars" style="font-size: 20px;"></i></span>
                                </i>
                            </a>
                        </div>-->
        </div>
        <!-- END LOGO 
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu top-menu-style-1">
            <ul class="nav navbar-nav pull-right">

                <li class="show-hide-side-menu">
                    <!--<a title="" data-container="body" class="show-tooltip" data-original-title="<?php echo (Session::has('hideMenu')) ? __('label.SHOW_MENU') : __('label.HIDE_MENU'); ?>" data-toggle="tooltip" data-placement="bottom"  href="<?php echo e(URL::to('changeMenuView')); ?>">
                        <i class="fa fa-exchange"></i>
                    </a>-->
                    <a title="" data-container="body" class="btn-show-hide-link">
                        <i class="btn">
                            <span id="fullMenu" data-fullMenu="1"><i class="fa fa-bars" style="font-size: 20px;"></i></span>
                        </i>
                    </a>
                </li>

                <!--                <li class="show-hide-side-menu">
                                    <a title="" data-container="body" class="btn-show-hide-link">
                                        <i class="btn">
                                            <span id="fullMenu" data-fullMenu="1"><?php echo __('label.FULL_SCREEN'); ?></span> 
                                        </i>
                                    </a>
                                </li>-->

                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <li class="dropdown dropdown-user">
                    <?php $user = Auth::user(); //get current user all information?>
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <?php if (!empty($user->photo)) { ?>
                            <img alt="<?php echo e($user['full_name']); ?>" class="img-circle" src="<?php echo e(URL::to('/')); ?>/public/uploads/user/<?php echo e($user->photo); ?>" />
                        <?php } else { ?>
                            <img alt="<?php echo e($user['full_name']); ?>" class="img-circle" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" />
                        <?php } ?>

                        <span class="username username-hide-on-mobile tooltips" data-placement="bottom" title="<?php echo e($user->full_name); ?>">
                            <?php echo app('translator')->get('label.WELCOME_LOGIN'); ?> <?php echo e($user->official_name); ?> (<?php echo $user->userGroup->name; ?>)
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-default">
                        <?php if(in_array(Auth::user()->group_id, [3,4])): ?>
                        <li>
                            <a href="<?php echo e(url('myProfile')); ?>">
                                <i class="icon-user"></i><?php echo app('translator')->get('label.MY_PROFILE'); ?></a>
                        </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?php echo e(url('changePassword')); ?>">
                                <i class="icon-key"></i><?php echo app('translator')->get('label.CHANGE_PASSWORD'); ?></a>
                        </li>
                        <!--<li class="divider"> </li>-->
                        <li>
                            <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                <i class="icon-logout"></i><?php echo app('translator')->get('label.LOGOUT'); ?>
                            </a>

                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                            </form>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
                <!--Start :: Refresh-->
                <li>
                    <a title="<?php echo app('translator')->get('label.REFRESH'); ?>" data-original-title="<?php echo app('translator')->get('label.REFRESH'); ?>" data-toggle="tooltip" data-placement="bottom" class="show-tooltip refresh">
                        <i class="fa fa-refresh"></i>
                    </a>
                </li>
                <!--End :: Refresh-->
                <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <li>
                    <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();
                            document.getElementById('logout-form2').submit();" title="" data-original-title="<?php echo app('translator')->get('label.LOGOUT_'); ?>" data-toggle="tooltip" data-placement="bottom" class="show-tooltip">
                        <i class="icon-logout"></i>
                    </a>
                    <form id="logout-form2" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo csrf_field(); ?>
                    </form>
                </li>

                <!-- END QUICK SIDEBAR TOGGLER -->
            </ul>
            <div class="col-md-offset-6 col-md-6">
                <div class="col-md-12 top-nav-notification">
                    <ul class="nav navbar-nav pull-right">
                        <?php if(in_array(Auth::user()->group_id, [4, 3])): ?>
                        <!--START::DS Remarks Notification-->
                        <li class="dropdown dropdown-extended dropdown-notification show-tooltip" data-container="body"  data-original-title="<?php echo app('translator')->get('label.DS_REMARKS_NOTIFICATIONS'); ?>" data-toggle="tooltip" data-placement="top" title="" >
                            <a href="<?php echo e(url('/dsRemarksReportCrnt')); ?>" class="notification-padding dropdown-toggle">
                                <i class="fa fa-user color-white" ></i>
                                <span class="badge badge-green-steel"><?php echo $dsRemarksCount ?? 0; ?></span>

                            </a>
                        </li>
                        <!--END::DS Remarks Notification-->
                        <?php endif; ?>
                        <?php if(in_array(Auth::user()->group_id, [2, 3]) || in_array(Auth::user()->id, $dsDeligationList)): ?>
                        <!--START::Unlock Request Notification-->

                        <li class="dropdown dropdown-extended dropdown-notification show-tooltip" data-container="body"  data-original-title="<?php echo app('translator')->get('label.UNLOCK_REQUEST_NOTIFICATION'); ?>" data-toggle="tooltip" data-placement="top" title="" >
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" >
                                <i class="fa fa-lock unlock-notification" ></i>
                                <span class="badge badge-green-steel"><?php echo $unlockCountArr['total'] ?? 0; ?></span>

                            </a>
                            <ul class="dropdown-menu max-height-250 overflow-auto">
                                <li class="external menu-list">
                                    <?php
                                    $totalUnlockCount = __('label.NO');
                                    if (!empty($unlockCountArr['total']) && $unlockCountArr['total'] != 0) {
                                        $totalUnlockCount = '<span class="bold">' . $unlockCountArr['total'] . '</span>';
                                    }
                                    $sUnlockCount = (!empty($unlockCountArr['total']) && $unlockCountArr['total'] > 1) ? 's' : '';
                                    ?>
                                    <h3>
                                        <?php echo app('translator')->get('label.YOU_HAVE_UNLOCK_REQUEST_NOTIFICATION', ['n' => $totalUnlockCount, 's' => $sUnlockCount]); ?>
                                    </h3>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list"  data-handle-color="#637283">
                                        <?php if(in_array(Auth::user()->group_id, [3]) || in_array(Auth::user()->id, $dsDeligationList)): ?> 
                                        <li>
                                            <a href="<?php echo e(url('unlockEventAssessment')); ?>" class="notification-padding">
                                                <span class="details">
                                                    <span class="badge badge-blue-madison req-number"><?php echo $unlockCountArr['event_assessment'] ?? 0; ?></span>&nbsp;
                                                    <?php echo app('translator')->get('label.EVENT_ASSESSMENT'); ?>
                                                </span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <li>
                                            <a href="<?php echo e(url('unlockCiModerationMarking')); ?>" class="notification-padding">
                                                <span class="details">
                                                    <span class="badge badge-blue-madison req-number"><?php echo $unlockCountArr['ci_moderation'] ?? 0; ?></span>&nbsp;
                                                    <?php echo app('translator')->get('label.CI_MODERATION'); ?>
                                                </span>
                                            </a>
                                        </li>
                                        <?php if(in_array(Auth::user()->group_id, [3]) || in_array(Auth::user()->id, $dsDeligationList)): ?>
                                        <li>
                                            <a href="<?php echo e(url('unlockDsObsnMarking')); ?>" class="notification-padding">
                                                <span class="details">
                                                    <span class="badge badge-blue-madison req-number"><?php echo $unlockCountArr['ds_observation'] ?? 0; ?></span>&nbsp;
                                                    <?php echo app('translator')->get('label.DS_OBSN'); ?>
                                                </span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <li>
                                            <a href="<?php echo e(url('unlockCiObsnMarking')); ?>" class="notification-padding">
                                                <span class="details">
                                                    <span class="badge badge-blue-madison req-number"><?php echo $unlockCountArr['ci_observation'] ?? 0; ?></span>&nbsp;
                                                    <?php echo app('translator')->get('label.CI_OBSN'); ?>
                                                </span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="<?php echo e(url('unlockComdtObsnMarking')); ?>" class="notification-padding">
                                                <span class="details">
                                                    <span class="badge badge-blue-madison req-number"><?php echo $unlockCountArr['comdt_observation'] ?? 0; ?></span>&nbsp;
                                                    <?php echo app('translator')->get('label.COMDT_OBSN'); ?>
                                                </span>
                                            </a>
                                        </li>

                                    </ul>
                                </li>

                            </ul>
                        </li>
                        <!-- END QUICK SIDEBAR TOGGLER -->

                        <!--END::Unlock Request Notification-->
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.show-tooltip').tooltip();
        $('.refresh').on('click', function () {
            location.reload();
        });
    })
</script><?php /**PATH C:\xampp\htdocs\afwc\resources\views/layouts/default/topNavbar.blade.php ENDPATH**/ ?>