@include('layouts.default.header')
<body id="addFullMenuClass" class="page-header-fixed page-content-white">
    <div class="page-wrapper">
        @include('layouts.default.topNavbar')
        <div class="clearfix"> </div>
        <div class="page-container">
            @include('layouts.default.sidebar')
            <div class="page-content-wrapper">
                @if($controllerName == 'dashboard')
                <div class="page-content dashboard-content">	
                    @else
                    <div class="page-content">
                        @endif
                        @yield('data_count')
                        <div class="clearfix"></div>
                    </div>
                </div>
                <a href="javascript:;" class="page-quick-sidebar-toggler">
                    <i class="icon-login"></i>
                </a>

            </div>
            @include('layouts.default.footer')
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
            @if ($routeName == 'dynamicsearch')
                    document.getElementById("mySidenav").style.width = "300px";
            @endif

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
    //                    $("#fullMenu").text("{{__('label.SCREEN_WITH_FULL_MENU')}}");
            } else {
            localStorage.removeItem('fullMenu');
            $("#fullMenu").attr("data-fullMenu", "1");
            $("#addFullMenuClass").removeClass("page-sidebar-closed");
            $("#addsidebarFullMenu").removeClass("page-sidebar-menu-closed");
            $("#jssor_1").css("width", '1046px');
    //                    $("#fullMenu").text("{{__('label.FULL_SCREEN')}}");
            }
            });
            if (localStorage.getItem('fullMenu') == '1') {
            $("#fullMenu").attr("data-fullMenu", "2");
            $("#addFullMenuClass").addClass("page-sidebar-closed");
            $("#addsidebarFullMenu").addClass("page-sidebar-menu-closed");
            //$("#jssor_1").css("width",'1234px');
    //                $("#fullMenu").text("{{__('label.SCREEN_WITH_FULL_MENU')}}");
            } else {
            $("#fullMenu").attr("data-fullMenu", "1");
            $("#addFullMenuClass").removeClass("page-sidebar-closed");
            $("#addsidebarFullMenu").removeClass("page-sidebar-menu-closed");
            //$("#jssor_1").css("width",'1046px');
    //                $("#fullMenu").text("{{__('label.FULL_SCREEN')}}");
            }

            });
        </script>
        @include('layouts.default.footerScript')
</body>
</html>