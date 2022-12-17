<?php $__env->startSection('data_count'); ?>
    <div class="col-md-12">
        <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.GS_LIST'); ?>
                </div>
                <div class="actions">
                    <a class="btn btn-default btn-sm create-new"
                        href="<?php echo e(URL::to('gs/create' . Helper::queryPageStr($qpArr))); ?>"> <?php echo app('translator')->get('label.CREATE_NEW_GS'); ?>
                        <i class="fa fa-plus create-new"></i>
                    </a>
                </div>
            </div>
            <div class="portlet-body">

                <div class="row">
                    <!-- Begin Filter-->
                    <?php echo Form::open(['group' => 'form', 'url' => 'gs/filter', 'class' => 'form-horizontal']); ?>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="filSearch"><?php echo app('translator')->get('label.SEARCH'); ?></label>
                                <div class="col-md-8">
                                    <?php echo Form::text('fil_search', Request::get('fil_search'), [
                                        'class' => 'form-control tooltips',
                                        'id' => 'filSearch',
                                        'title' => __('label.NAME'),
                                        'placeholder' => __('label.NAME'),
                                        'list' => 'gsName',
                                        'autocomplete' => 'off',
                                    ]); ?>

                                    <datalist id="gsName">
                                        <?php if(!$nameArr->isEmpty()): ?>
                                            <?php $__currentLoopData = $nameArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($item->name); ?>" />
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </datalist>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form">
                                <button type="submit"
                                    class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                    <i class="fa fa-search"></i> <?php echo app('translator')->get('label.FILTER'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>

                    <!-- End Filter -->
                </div>

                <!--            <div class="row">
                                    <div class="col-md-offset-8 col-md-4" id="manageEvDiv">
                                        <a class="btn btn-icon-only btn-warning tooltips vcenter" title="Download PDF"
                                           href="<?php echo e(action('RankController@index', ['download' => 'pdf', 'fil_search' => Request::get('fil_search'), 'fil_service_id' => Request::get('fil_service_id')])); ?>">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </div>
                                </div>-->

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center vcenter"><?php echo app('translator')->get('label.SL_NO'); ?></th>
                                <th class="vcenter"><?php echo app('translator')->get('label.NAME'); ?></th>
                                <th class="text-center vcenter"><?php echo app('translator')->get('label.UNIT_ORGANIZATION'); ?></th>
                                
                                <th class="text-center vcenter"><?php echo app('translator')->get('label.IMAGE'); ?></th>
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
                                        <td class="vcenter"><?php echo e($target->name); ?></td>
                                        <td class="text-center vcenter"><?php echo e($target->unit); ?></td>
                                        
                                        <td class="text-center vcenter">
                                            <?php if (!empty($target->photo) && File::exists('public/uploads/gs/'.$target->photo)) { ?>
                                            <img width="50" height="60"
                                                src="<?php echo e(URL::to('/')); ?>/public/uploads/gs/<?php echo e($target->photo); ?>"
                                                alt="<?php echo e($target->full_name); ?>" />
                                            <?php } else { ?>
                                            <img width="50" height="60"
                                                src="<?php echo e(URL::to('/')); ?>/public/img/unknown.png"
                                                alt="<?php echo e($target->full_name); ?>" />
                                            <?php } ?>
                                        </td>
                                        <td class="text-center vcenter">
                                            <?php if($target->status == '1'): ?>
                                                <span class="label label-sm label-success"><?php echo app('translator')->get('label.ACTIVE'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-sm label-warning"><?php echo app('translator')->get('label.INACTIVE'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="td-actions text-center vcenter">
                                            <div class="width-inherit">
                                                <?php echo e(Form::open(['url' => 'gs/' . $target->id . Helper::queryPageStr($qpArr)])); ?>

                                                <?php echo e(Form::hidden('_method', 'DELETE')); ?>


                                                <a class="btn btn-xs btn-primary tooltips " title="Edit"
                                                    href="<?php echo e(URL::to('gs/' . $target->id . '/edit' . Helper::queryPageStr($qpArr))); ?>">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button class="btn btn-xs btn-danger delete tooltips" title="Delete"
                                                    type="submit" data-placement="top" data-rel="tooltip"
                                                    data-original-title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>

                                                <?php echo e(Form::close()); ?>

                                                <button class="btn btn-xs btn-warning tooltips" title="GS Info" id="gsInfo" data-target="#showGsInfo" data-toggle="modal" data-id="<?php echo e($target->id); ?>" data-original-title="GS Info">
                                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="vcenter"><?php echo app('translator')->get('label.NO_GS_FOUND'); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php echo $__env->make('layouts.paginator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="showGsInfo" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div id="placeGsInfo">
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).on("click", "#gsInfo", function(e) {
            e.preventDefault();
            var gsId = $(this).attr("data-id");

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null,
            };
            $.ajax({
                type: 'post',
                url: "<?php echo e(URL::to('gs/showGsInfo')); ?>",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    gs_id: gsId
                },

                success: function(res) {
                    $("#placeGsInfo").html(res.html);
                    App.unblockUI();
                },
                error: function(jqXhr, ajaxOptions, thrownError) {

                    if (jqXhr.status == 400) {
                        var errorsHtml = '';
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function(key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, '', options);
                    } else {
                        toastr.error('Error', 'Something went wrong', options);
                    }
                    App.unblockUI();
                }
            });

        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/gs/index.blade.php ENDPATH**/ ?>