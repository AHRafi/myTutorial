
<?php $__env->startSection('data_count'); ?>	
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user"></i><?php echo app('translator')->get('label.CREATE_CM'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php echo Form::open(array('group' => 'form', 'url' => 'cm', 'files'=> true, 'class' => 'form-horizontal')); ?>

            <?php echo Form::hidden('filter', Helper::queryPageStr($qpArr)); ?>

            <?php echo e(csrf_field()); ?>


            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">

                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?> :</label>
                            <div class="col-md-4"> <div class="control-label pull-left"> <strong> <?php echo e($activeCourse->name); ?> </strong></div>
                                <?php echo Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']); ?>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="wingId"><?php echo app('translator')->get('label.WING'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::select('wing_id', $wingList, null, ['class' => 'form-control js-source-states', 'id' => 'wingId']); ?>

                                <span class="text-danger"><?php echo e($errors->first('wing_id')); ?></span>
                            </div>
                        </div>

                        <div class="form-group" id="showRank">
                            <label class="control-label col-md-4" for="rankId"><?php echo app('translator')->get('label.RANK'); ?> :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-8">
                                <div id="rankHolder">
                                    <?php echo Form::select('rank_id', $rankList, null, ['class' => 'form-control js-source-states', 'id' => 'rankId']); ?>

                                </div> 

                                <span class="text-danger"><?php echo e($errors->first('rank_id')); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class = "control-label col-md-4" for="armsServiceId"><?php echo app('translator')->get('label.ARMS_SERVICE'); ?> :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::select('arms_service_id', $armsServiceList, null,  ['class' => 'form-control js-source-states', 'id' => 'armsServiceId']); ?>

                                <span class="text-danger"><?php echo e($errors->first('arms_service_id')); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="personalNo"><?php echo app('translator')->get('label.PERSONAL_NO'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::text('personal_no', null, ['id'=> 'personalNo', 'class' => 'form-control', 'autocomplete' => 'off']); ?> 
                                <span class="text-danger"><?php echo e($errors->first('personal_no')); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="fullName"><?php echo app('translator')->get('label.FULL_NAME'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::textarea('full_name', null, ['id'=> 'fullName', 'class' => 'form-control full-name-text-area','cols'=>'20','rows' => '1']); ?>

                                <div class="clearfix">
                                    <span class="label label-success"><?php echo app('translator')->get('label.NOTE'); ?></span>
                                    <?php echo app('translator')->get('label.PRESS_CTRL_B'); ?>
                                </div>
                                <span class="text-danger"><?php echo e($errors->first('full_name')); ?></span>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="officialName"><?php echo app('translator')->get('label.OFFICIAL_NAME'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::text('official_name', null, ['id'=> 'officialName', 'class' => 'form-control', 'autocomplete' => 'off']); ?> 
                                <span class="text-danger"><?php echo e($errors->first('official_name')); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="fatherName"><?php echo app('translator')->get('label.FATHERS_NAME'); ?> :</label>
                            <div class="col-md-8">
                                <?php echo Form::text('father_name', null, ['id'=> 'fatherName', 'class' => 'form-control', 'autocomplete' => 'off']); ?> 
                                <span class="text-danger"><?php echo e($errors->first('father_name')); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="username"><?php echo app('translator')->get('label.USERNAME'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <?php echo Form::text('username', null, ['id'=> 'username', 'class' => 'form-control', 'autocomplete' => 'off']); ?> 

                                <span class="text-danger"><?php echo e($errors->first('username')); ?></span>
                                <div class="clearfix margin-top-10">
                                    <span class="label label-danger"><?php echo app('translator')->get('label.NOTE'); ?></span> <?php echo app('translator')->get('label.USERNAME_DESCRIPTION'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="password"><?php echo app('translator')->get('label.PASSWORD'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <?php echo Form::password('password', ['id'=> 'password', 'class' => 'form-control password-visible', 'autocomplete' => 'off']); ?> 
                                    <span class="input-group-btn">
                                        <button class="btn default show-pass" type="button" id="showPass">
                                            <i class="fa fa-eye" id="passIcon"></i>
                                        </button>
                                    </span>
                                </div>

                                <span class="text-danger"><?php echo e($errors->first('password')); ?></span>
                                <div class="clearfix margin-top-10">
                                    <span class="label label-danger"><?php echo app('translator')->get('label.NOTE'); ?></span>
                                    <?php echo app('translator')->get('label.COMPLEX_PASSWORD_INSTRUCTION'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="confPassword"><?php echo app('translator')->get('label.CONF_PASSWORD'); ?> :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <?php echo Form::password('conf_password', ['id'=> 'confPassword', 'class' => 'form-control password-visible', 'autocomplete' => 'off']); ?> 
                                    <span class="input-group-btn">
                                        <button class="btn default show-pass" type="button" id="showConPass">
                                            <i class="fa fa-eye" id="conPassIcon"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger"><?php echo e($errors->first('conf_password')); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="status"><?php echo app('translator')->get('label.STATUS'); ?> :</label>
                            <div class="col-md-8">
                                <?php echo Form::select('status', ['1' => __('label.ACTIVE'), '2' => __('label.INACTIVE')], '1', ['class' => 'form-control js-source-states-2', 'id' => 'status']); ?>

                                <span class="text-danger"><?php echo e($errors->first('status')); ?></span>
                            </div>
                        </div>
                    </div>
                    <!--image-->
                    <div class="col-md-4">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"> </div>
                            <div>
                                <span class="btn green-seagreen btn-outline btn-file">
                                    <span class="fileinput-new"> Select image </span>
                                    <span class="fileinput-exists"> Change </span>
                                    <?php echo Form::file('photo', null, ['id'=> 'photo']); ?>

                                </span>
                                <a href="javascript:;" class="btn green-seagreen fileinput-exists" data-dismiss="fileinput"> Remove </a>
                            </div>
                        </div>
                        <div class="clearfix margin-top-10">
                            <span class="label label-danger"><?php echo app('translator')->get('label.NOTE'); ?></span> <?php echo app('translator')->get('label.USER_IMAGE_FOR_IMAGE_DESCRIPTION'); ?>
                        </div>
                        <span class="text-danger"><?php echo e($errors->first('photo')); ?></span>
                    </div>


                    <!--Start Course in Academy Information-->
                    <div class="col-md-12">
                        <div class="row">
                            <div class="row margin-bottom-10">
                                <div class="col-md-12">
                                    <span class="col-md-12 border-bottom-1-green-seagreen">
                                        <strong><?php echo app('translator')->get('label.COURSE_IN_ACADEMY_INFORMATION'); ?></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label class="control-label col-md-5" for="commissioningCourseId"><?php echo app('translator')->get('label.COMMISSIONING_COURSE'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php echo Form::select('commissioning_course_id', $commissioningCourseList, null, ['class' => 'form-control js-source-states', 'id' => 'commissioningCourseId', 'autocomplete' => 'off']); ?>

                                        <span class="text-danger"><?php echo e($errors->first('commissioning_course_id')); ?></span>
                                    </div>
                                </div>

                                <div class = "form-group">
                                    <label class = "control-label col-md-5" for="commisioningDate"><?php echo app('translator')->get('label.COMMISSIONING_DATE'); ?> :</label>
                                    <div class="col-md-7">
                                        <div class="input-group date datepicker2">
                                            <?php echo Form::text('commisioning_date', null, ['id'=> 'commisioningDate', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']); ?> 
                                            <span class="input-group-btn">
                                                <button class="btn default reset-date" type="button" remove="commisioningDate">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                <button class="btn default date-set" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <span class="text-danger"><?php echo e($errors->first('commisioning_date')); ?></span>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label class="control-label col-md-5" for="commissionType"><?php echo app('translator')->get('label.TYPE_OF_COMMISSION'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php echo Form::select('commission_type', $commissionTypeList, null, ['class' => 'form-control js-source-states', 'id' => 'commissionType', 'autocomplete' => 'off']); ?>

                                        <span class="text-danger"><?php echo e($errors->first('commission_type')); ?></span>
                                    </div>
                                </div>

                                <div class = "form-group">
                                    <label class = "control-label col-md-5" for="anteDateSeniority"><?php echo app('translator')->get('label.ANTE_DATE_SENIORITY'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php echo Form::text('ante_date_seniority', null, ['id'=> 'antiDateSeniority', 'class' => 'form-control']); ?>

                                        <span class="text-danger"><?php echo e($errors->first('ante_date_seniority')); ?></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--End Course in information-->

                    <!--Start Basic Information-->
                    <div class="col-md-12">
                        <div class="row">
                            <div class="row margin-bottom-10">
                                <div class="col-md-12">
                                    <span class="col-md-12 border-bottom-1-green-seagreen">
                                        <strong><?php echo app('translator')->get('label.BASIC_INFORMATION'); ?></strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="row">

                                <div class = "form-group">
                                    <label class = "control-label col-md-5" for="dob"><?php echo app('translator')->get('label.DATE_OF_BIRTH'); ?> :</label>
                                    <div class="col-md-7">
                                        <div class="input-group date datepicker2">
                                            <?php echo Form::text('date_of_birth', null, ['id'=> 'dob', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']); ?> 
                                            <span class="input-group-btn">
                                                <button class="btn default reset-date" type="button" remove="dob">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                <button class="btn default date-set" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <span class="text-danger"><?php echo e($errors->first('date_of_birth')); ?></span>
                                    </div>                              
                                </div>

                                <div class = "form-group">
                                    <label class = "control-label col-md-5" for="birthPlace"><?php echo app('translator')->get('label.BIRTH_PLACE'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php echo Form::text('birth_place', null, ['id'=> 'birthPlace', 'class' => 'form-control']); ?>

                                        <span class="text-danger"><?php echo e($errors->first('birth_place')); ?></span>
                                    </div>                                
                                </div>

                                <div class = "form-group">
                                    <label class = "control-label col-md-5" for="religionId"><?php echo app('translator')->get('label.RELIGION'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php echo Form::select('religion_id', $religionList, null,  ['class' => 'form-control js-source-states', 'id' => 'religionId']); ?>

                                        <span class="text-danger"><?php echo e($errors->first('religion_id')); ?></span>
                                    </div>                                
                                </div>
                                <div class = "form-group">
                                    <label class = "control-label col-md-5" for="gender"><?php echo app('translator')->get('label.GENDER'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php if(!empty($genderList)): ?>
                                        <div class="md-radio-inline">
                                            <?php $__currentLoopData = $genderList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genderId => $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                            $checked = $genderId == '1' ? true : false;
                                            ?>
                                            <div class="md-radio">
                                                <?php echo Form::radio('gender', $genderId, $checked, ['id' => 'gender'.$genderId, 'class' => 'md-radiobtn md-gender', 'data-val' => $genderId]); ?>

                                                <label for="gender<?php echo e($genderId); ?>">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span>
                                                </label>
                                                <span class=""><?php echo e($gender); ?></span>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <span class="text-danger"><?php echo e($errors->first('gender')); ?></span>
                                        <?php endif; ?>
                                    </div>                                
                                </div>

                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="row">

                                <div class="form-group">
                                    <label class="control-label col-md-5" for="email"><?php echo app('translator')->get('label.EMAIL'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php echo Form::email('email', null, ['id'=> 'email', 'class' => 'form-control', 'autocomplete' => 'off']); ?> 

                                        <span class="text-danger"><?php echo e($errors->first('email')); ?></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-5" for="number"><?php echo app('translator')->get('label.PHONE'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php echo Form::text('number', null, ['id'=> 'number', 'class' => 'form-control', 'autocomplete' => 'off']); ?> 
                                        <span class="text-danger"><?php echo e($errors->first('number')); ?></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-5" for="bloodGroup"><?php echo app('translator')->get('label.BLOOD_GROUP'); ?> :</label>
                                    <div class="col-md-7">
                                        <?php echo Form::select('blood_group', $bloodGroupList, null,  ['class' => 'form-control js-source-states', 'id' => 'bloodGroup']); ?>

                                        <span class="text-danger"><?php echo e($errors->first('blood_group')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End Basic Information-->


                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn btn-circle green" type="submit">
                            <i class="fa fa-check"></i> <?php echo app('translator')->get('label.SUBMIT'); ?>
                        </button>
                        <a href="<?php echo e(URL::to('/cm'.Helper::queryPageStr($qpArr))); ?>" class="btn btn-circle btn-outline grey-salsa"><?php echo app('translator')->get('label.CANCEL'); ?></a>
                    </div>
                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>	
    </div>
</div>
<script src="<?php echo e(asset('public/assets/global/plugins/summer-note/summernote.min.js')); ?>" type="text/javascript"></script>
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
    $(document).on('click', '#showConPass', function () {
        $('#conPassIcon').toggleClass("fa-eye fa-eye-slash");
        var input = $('#confPassword');
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
    //END::show pass

    $('#fullName').summernote({
        toolbar: [],
        height: 27,
    });
});

$(document).on('change', '#wingId', function () {
    var wingId = $('#wingId').val();

    $.ajax({
        url: "<?php echo e(URL::to('cm/getRank')); ?>",
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            wing_id: wingId
        },
        success: function (res) {
            $('#rankId').html(res.html1);
            $('#armsServiceId').html(res.html2);
            $('#commissioningCourseId').html(res.html3);
        },
    });
});

$(document).on('change', '#commissioningCourseId', function () {
    var commissioningCourseId = $('#commissioningCourseId').val();

    $('#commisioningDate').val('');
    if (commissioningCourseId == '0') {
        return false;
    }

    var options = {
        closeButton: true,
        debug: false,
        positionClass: "toast-bottom-right",
        onclick: null
    };
    $.ajax({
        url: "<?php echo e(URL::to('cm/getCommisioningDate')); ?>",
        type: "POST",
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            commissioning_course_id: commissioningCourseId,
        },
        beforeSend: function () {
            App.blockUI({boxed: true});
        },
        success: function (res) {
            $('#commisioningDate').val(res.commisioningDate);
            App.unblockUI();
        },
        error: function (jqXhr, ajaxOptions, thrownError) {
            toastr.error('<?php echo app('translator')->get("label.SOMETHING_WENT_WRONG"); ?>', 'Error', options);
            App.unblockUI();
        }
    });//ajax
});


</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/cm/create.blade.php ENDPATH**/ ?>