<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <title><?php echo app('translator')->get('label.NDC_AMS_TITLE'); ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="<?php echo app('translator')->get('label.SINT_AMS_TITLE'); ?>" name="description" />
        <meta content="" name="author" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <?php // <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" /> ?>
        <link href="<?php echo e(asset('public/assets/global/plugins/font-awesome/css/font-awesome.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/simple-line-icons/simple-line-icons.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')); ?>" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="<?php echo e(asset('public/assets/pages/css/profile.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/morris/morris.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/fullcalendar/fullcalendar.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/jqvmap/jqvmap/jqvmap.css')); ?>" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->

        <!-- BEGIN DROPDOWN SELECT PLUGINS -->
        <link href="<?php echo e(asset('public/assets/global/plugins/select2/css/select2.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/select2/css/select2-bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
        <!-- END DROPDOWN SELECT PLUGINS -->

        <!-- MultiSelect STYLES -->
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css')); ?>" rel="stylesheet" type="text/css" />

        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?php echo e(asset('public/assets/global/css/components.min.css')); ?>" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/css/plugins.min.css')); ?>" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="<?php echo e(asset('public/assets/layouts/layout/css/layout.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/layouts/layout/css/themes/darkblue.min.css')); ?>" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?php echo e(asset('public/assets/layouts/layout/css/custom.css')); ?>" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <!-- SWEETALERT STYLES -->
        <link href="<?php echo e(asset('public/assets/global/plugins/sweetalert/lib/sweet-alert.css')); ?>" rel="stylesheet" type="text/css" />

        <!-- DATEPICKER STYLES -->
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')); ?>" rel="stylesheet" type="text/css" />

        <!-- Toaster STYLES -->
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-toastr/toastr.min.css')); ?>" rel="stylesheet" type="text/css" />

        <!-- Modal STYLES -->
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css')); ?>" rel="stylesheet" type="text/css" />

        <!-- Full Calendar -->
        <link href="<?php echo e(asset('public/assets/global/plugins/fullcalendar/fullcalendar.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('public/assets/global/plugins/amcharts4/export.css')); ?>" rel="stylesheet" type="text/css"/>
        <link rel="shortcut icon" href="<?php echo e(URL::to('/')); ?>/public/img/favicon_sint.png" />


        <script src="<?php echo e(asset('public/assets/global/plugins/jquery.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('public/assets/global/plugins/jquery-ui/jquery-ui.min.js')); ?>" type="text/javascript"></script>
        <link href="<?php echo e(asset('public/assets/global/plugins/jquery-ui/jquery-ui.min.css')); ?>" rel="stylesheet" type="text/css"/>

        <!--- START:: JS for Slider -->
        <link href="<?php echo e(asset('public/assets/slider/css/slider.css')); ?>" rel="stylesheet" type="text/css"/>
        <script src="<?php echo e(asset('public/assets/slider/js/jssor.slider-27.5.0.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('public/assets/slider/js/main.js')); ?>" type="text/javascript"></script>
        <!--- END:: JS for Slider -->


        <link href="<?php echo e(asset('public/css/apexcharts.css')); ?>" rel="stylesheet" type="text/css"/>
        <!--- START:: CSS for Gallery -->
        <link href="<?php echo e(asset('public/assets/gallery/css/album-style.css')); ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo e(asset('public/assets/gallery/css/lightgallery.css')); ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo e(asset('public/assets/gallery/css/gallery-style.css')); ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo e(asset('public/assets/gallery/css/lightbox.css')); ?>" rel="stylesheet" type="text/css"/>
        <!--- END:: CSS for Gallery -->

        <!--        Summer Note CSS         -->
        <link href="<?php echo e(asset('public/assets/global/plugins/summer-note/summernote.min.css')); ?>" rel="stylesheet" type="text/css" />
        <!--        END Summer Note CSS         -->

    </head>
    <!-- END HEAD -->
<?php /**PATH C:\xampp\htdocs\afwc\resources\views/layouts/default/header.blade.php ENDPATH**/ ?>