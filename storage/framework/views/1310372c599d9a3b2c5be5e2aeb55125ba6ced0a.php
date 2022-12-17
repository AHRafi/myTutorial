<?php echo $__env->make('layouts.default.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<body id="addFullMenuClass" class="page-header-fixed page-content-white">
    <div class="page-wrapper">
        <?php echo $__env->make('layouts.default.topNavbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="clearfix"> </div>
        <div class="page-container">
            <?php echo $__env->make('layouts.default.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="page-content-wrapper">
                <?php if($controllerName == 'dashboard'): ?>
                <div class="page-content dashboard-content">	
                    <?php else: ?>
                    <div class="page-content">
                        <?php endif; ?>
                        <?php echo $__env->yieldContent('data_count'); ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <a href="javascript:;" class="page-quick-sidebar-toggler">
                    <i class="icon-login"></i>
                </a>

            </div>
            <?php echo $__env->make('layouts.default.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <div class="quick-nav-overlay"></div>
        <script type="text/javascript">
<?php
$routeName = strtolower(Route::getFacadeRoot()->current()->uri());
?>

            // sidebar overlay script
            function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            }
            // sidebar overlay
            $(function () {

            // sidebar overlay  
            <?php if($routeName == 'dynamicsearch'): ?>
                    document.getElementById("mySidenav").style.width = "300px";
            <?php endif; ?>

                    $(document).on("click", "#showDynamicNav", function () {
            document.getElementById("mySidenav").style.width = "300px";
            });
            //End sidebar overlay

            $(document).on("click", "#fullMenu", function () {
            var fullMenu = $("#fullMenu").attr("data-fullMenu");
            if (fullMenu == '1') {
            localStorage.setItem('fullMenu', fullMenu);
            $("#fullMenu").attr("data-fullMenu", "2");
            $("#addFullMenuClass").addClass("page-sidebar-closed");
            $("#addsidebarFullMenu").addClass("page-sidebar-menu-closed");
            $("#jssor_1").css("width", '1234px');
    //                    $("#fullMenu").text("<?php echo e(__('label.SCREEN_WITH_FULL_MENU')); ?>");
            } else {
            localStorage.removeItem('fullMenu');
            $("#fullMenu").attr("data-fullMenu", "1");
            $("#addFullMenuClass").removeClass("page-sidebar-closed");
            $("#addsidebarFullMenu").removeClass("page-sidebar-menu-closed");
            $("#jssor_1").css("width", '1046px');
    //                    $("#fullMenu").text("<?php echo e(__('label.FULL_SCREEN')); ?>");
            }
            });
            if (localStorage.getItem('fullMenu') == '1') {
            $("#fullMenu").attr("data-fullMenu", "2");
            $("#addFullMenuClass").addClass("page-sidebar-closed");
            $("#addsidebarFullMenu").addClass("page-sidebar-menu-closed");
            //$("#jssor_1").css("width",'1234px');
    //                $("#fullMenu").text("<?php echo e(__('label.SCREEN_WITH_FULL_MENU')); ?>");
            } else {
            $("#fullMenu").attr("data-fullMenu", "1");
            $("#addFullMenuClass").removeClass("page-sidebar-closed");
            $("#addsidebarFullMenu").removeClass("page-sidebar-menu-closed");
            //$("#jssor_1").css("width",'1046px');
    //                $("#fullMenu").text("<?php echo e(__('label.FULL_SCREEN')); ?>");
            }

            });
        </script>
        <?php echo $__env->make('layouts.default.footerScript', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\afwc\resources\views/layouts/default/master.blade.php ENDPATH**/ ?>