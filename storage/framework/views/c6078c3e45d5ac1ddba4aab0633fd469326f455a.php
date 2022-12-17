
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user"></i><?php echo app('translator')->get('label.CM_LIST'); ?>
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="<?php echo e(URL::to('cm/create'.Helper::queryPageStr($qpArr))); ?>"> <?php echo app('translator')->get('label.CREATE_NEW_CM'); ?>
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">
            <div id="filterOpt">
                <!-- Begin Filter-->
                <?php echo Form::open(array('group' => 'form', 'url' => 'cm/filter','class' => 'form-horizontal')); ?>


                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="filSearch"><?php echo app('translator')->get('label.SEARCH'); ?></label>
                            <div class="col-md-8">
                                <?php echo Form::text('fil_search',  Request::get('fil_search'), ['class' => 'form-control tooltips', 'id' => 'filSearch', 'title' => 'Official Name', 'placeholder' => 'Official Name', 'list' => 'cmName', 'autocomplete' => 'off']); ?> 
                                <datalist id="cmName">
                                    <?php if(!$nameArr->isEmpty()): ?>
                                    <?php $__currentLoopData = $nameArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo $item->official_name; ?>" />
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="wingId"><?php echo app('translator')->get('label.WING'); ?></label>
                            <div class="col-md-8">
                                <?php echo Form::select('fil_wing_id', $wingList, Request::get('fil_wing_id'), ['class' => 'form-control js-source-states', 'id' => 'wingId']); ?>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="rankId"><?php echo app('translator')->get('label.RANK'); ?></label>
                            <div class="col-md-8">
                                <?php echo Form::select('fil_rank_id', $rankList, Request::get('fil_rank_id'), ['class' => 'form-control js-source-states', 'id' => 'rankId']); ?>

                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="courseId"><?php echo app('translator')->get('label.COURSE'); ?></label>
                            <div class="col-md-8">
                                <?php echo Form::select('fil_course_id', $courseList,  Request::get('fil_course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']); ?>

                            </div>
                        </div>
                    </div>

                    <div class="text-center col-md-4">
                        <div class="form">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20 filter-btn">
                                <i class="fa fa-search"></i> <?php echo app('translator')->get('label.FILTER'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <?php echo Form::close(); ?>

                <!-- End Filter -->
            </div>
            <!-- <div id="filterShow">
                <button type="button" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20" id="viewIcon">
                    <i class="fa fa-search"></i> <?php echo app('translator')->get('label.FILTER'); ?>
                </button>
            </div>

                       <div class="row">
                            <div class="col-md-offset-8 col-md-4" id="manageEvDiv">
                                <a class="btn btn-icon-only btn-warning tooltips vcenter" title="Download PDF" 
                                   href="<?php echo e(action('CmController@index', ['download'=>'pdf','fil_search' => Request::get('fil_search'), 'fil_group_id' => Request::get('fil_group_id'), 
                                          'fil_service_id' => Request::get('fil_service_id'),'fil_rank_id' => Request::get('fil_rank_id'),'fil_appointment_id' => Request::get('fil_appointment_id'),
                                      'fil_institute_id' => Request::get('fil_institute_id')])); ?>">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>-->


            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.COURSE_ID'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.WING'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.RANK'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.ARMS_SERVICE'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.PERSONAL_NO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.FULL_NAME'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.OFFICIAL_NAME'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.PHOTO'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.EMAIL'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.PHONE'); ?></th>
                            <th class="vcenter"><?php echo app('translator')->get('label.USERNAME'); ?></th>
                            <th class="text-center vcenter"><?php echo app('translator')->get('label.STATUS'); ?></th>
                            <th class="td-actions text-center vcenter"><?php echo app('translator')->get('label.ACTION'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!$targetArr->isEmpty()): ?>
                        <?php
                        $page = Request::get('page');
                        $page = empty($page) ? 1 : $page;
                        $sl = ($page - 1) * Session::get('paginatorCount');
                        ?>
                        <?php $__currentLoopData = $targetArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center vcenter"><?php echo e(++$sl); ?></td>
                            <td class="vcenter"><?php echo $target->course; ?></td>
                            <td class=" vcenter"><?php echo e($target->wing); ?></td>
                            <td class="vcenter"><?php echo e($target->rank); ?></td>
                            <td class="vcenter"><?php echo e($target->arms_service); ?></td>
                            <td class="vcenter"><?php echo e($target->personal_no); ?></td>
                            <td class="vcenter"><?php echo Common::getFurnishedCmName($target->full_name); ?></td>
                            <td class="vcenter"><?php echo $target->official_name; ?></td>
                            <td class="text-center vcenter" width="50px">
                                <?php if (!empty($target->photo) && File::exists('public/uploads/cm/'.$target->photo)) { ?>
                                    <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/uploads/cm/<?php echo e($target->photo); ?>" alt="<?php echo e(Common::getFurnishedCmName($target->full_name)); ?>"/>
                                <?php } else { ?>
                                    <img width="50" height="60" src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png" alt="<?php echo e(Common::getFurnishedCmName($target->full_name)); ?>"/>
                                <?php } ?>
                            </td>
                            <td class="vcenter"><?php echo $target->email; ?></td>
                            <td class="vcenter"><?php echo $target->number; ?></td>
                            <td class="vcenter"><?php echo $target->username; ?></td>
                            <td class="text-center vcenter">
                                <?php if($target->status == '1'): ?>
                                <span class="label label-sm label-success"><?php echo app('translator')->get('label.ACTIVE'); ?></span>
                                <?php else: ?>
                                <span class="label label-sm label-warning"><?php echo app('translator')->get('label.INACTIVE'); ?></span>
                                <?php endif; ?>
                            </td>

                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">
                                    <?php echo Form::open(array('url' => 'cm/' . $target->id.'/'.Helper::queryPageStr($qpArr))); ?>

                                    <?php echo Form::hidden('_method', 'DELETE'); ?>


                                    <a class="btn btn-xs green-seagreen tooltips vcenter" title="View CM Profile" href="<?php echo URL::to('cm/' . $target->id . '/profile'.Helper::queryPageStr($qpArr)); ?>">
                                        <i class="fa fa-user"></i>
                                    </a>

                                    <a class="btn btn-xs btn-primary tooltips vcenter" title="Edit" href="<?php echo URL::to('cm/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)); ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <button class="btn btn-xs btn-danger delete tooltips vcenter" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>

                                    <?php echo Form::close(); ?>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="13" class="vcenter"><?php echo app('translator')->get('label.NO_CM_FOUND'); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo $__env->make('layouts.paginator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>	
    </div>
</div>

<!--cm modal -->
<div id="cmInformation" class="modal container fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">
        <div id="showCmInformation">
            <!--ajax will be load here-->
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-outline dark">Close</button>
    </div>
</div>
<!--End cm modal -->
<script>
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
                    wing_id: wingId,
                    index_id: '1'
                },
                success: function (res) {
                    $('#rankId').html(res.html);
                },
            });
        
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/cm/index.blade.php ENDPATH**/ ?>