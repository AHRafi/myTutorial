<?php $__env->startSection('login_content'); ?>
<!-- BEGIN LOGIN FORM -->
<form class="login-form" method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <div class="row login-form-logo">
        <div class="col-md-12">

            <h3 class="form-title font-white"><?php echo app('translator')->get('label.LOGIN_SINT_AMS'); ?></h3>
            <!-- BEGIN LOGO -->
            <div class="logo">
                <a href="#">
                    <img src="<?php echo e(URL::to('/')); ?>/public/img/logo_sint_login.png" alt="logo" height="150px"/>
                </a>
            </div>
            <!-- END LOGO -->

        </div>
    </div>

    <div class="form-group login-form-group">

        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <?php if($errors->has('username')): ?>
        <span class="invalid-feedback">
            <strong class="text-danger"><?php echo e($errors->first('username')); ?></strong>
        </span>
        <?php endif; ?>
        <label class="control-label visible-ie8 visible-ie9"><?php echo app('translator')->get('label.USERNAME'); ?></label>
        <div class="input-group bootstrap-touchspin width-inherit">
            <span class="input-group-addon bootstrap-touchspin-prefix bold green">
                <img src="<?php echo e(URL::to('/')); ?>/public/img/username_icon.png" alt="username"/>
            </span>
            <input id="userName" type="text" class="form-control form-control-solid placeholder-no-fix <?php echo e($errors->has('username') ? ' is-invalid' : ''); ?>" placeholder="<?php echo app('translator')->get('label.USERNAME'); ?>" name="username" value="<?php echo e(old('username')); ?>" required/>
        </div>
    </div>
    <div class="form-group login-form-group">
        <label class="control-label visible-ie8 visible-ie9"><?php echo app('translator')->get('label.PASSWORD'); ?></label>
        <div class="input-group bootstrap-touchspin width-inherit">
            <span class="input-group-addon bootstrap-touchspin-prefix bold green">
                <img src="<?php echo e(URL::to('/')); ?>/public/img/password_icon.png" alt="password"/>
            </span>
            <input id="password" type="password" class="form-control form-control-solid placeholder-no-fix<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" placeholder="<?php echo app('translator')->get('label.PASSWORD'); ?>" name="password" required/>
            <span class="input-group-btn">
                <button class="btn default show-pass" type="button" id="showPass">
                    <i class="fa fa-eye" id="passIcon"></i>
                </button>
            </span>
        </div>

        <?php if($errors->has('password')): ?>
        <span class="invalid-feedback">
            <strong class="text-danger"><?php echo e($errors->first('password')); ?></strong>
        </span>
        <?php endif; ?>
    </div>

    <div class="form-actions login-form-group">
        <button type="submit" class="btn green"><?php echo app('translator')->get('label.LOGIN'); ?></button>
        <!--label class="rememberme check mt-checkbox mt-checkbox-outline">
            <input type="checkbox" name="remember" value="1" />Remember
            <span></span>
        </label>
        <a href="<?php echo e(route('password.request')); ?>" id="forget-password" class="forget-password">Forgot Password?</a-->
    </div>
    <div class="login-options">
        <div class="copyright">&copy; <?php echo e(date('Y')); ?> <?php echo app('translator')->get('label.LOGIN_SINT_BANGLADESH_ARMY'); ?> | <?php echo app('translator')->get('label.POWERED_BY'); ?>
            <a target="_blank" href="http://www.swapnoloke.com/"><?php echo app('translator')->get('label.SWAPNOLOKE'); ?></a>
        </div>
    </div>

</form>

<script src="<?php echo e(asset('public/assets/global/plugins/jquery.min.js')); ?>" type="text/javascript"></script>
        
<script>
    $(document).ready(function () {
        //START::show pass
        $(document).on('click', '#showPass', function () {
            $('#passIcon').toggleClass("fa-eye fa-eye-slash");
            var input = $('#password');
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
        //END::show pass
    });

</script>
<!-- END LOGIN FORM -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.login', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/auth/login.blade.php ENDPATH**/ ?>