
<?php $__env->startSection('data_count'); ?>
<div class="col-md-12">
    <?php echo $__env->make('layouts.flash', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i><?php echo app('translator')->get('label.COURSE_WISE_DOC_REPORT'); ?>
            </div>
        </div>

        <div class="portlet-body">
            <?php echo Form::open(array('group' => 'form', 'url' => 'courseWiseDocSummary/filter','class' => 'form-horizontal')); ?>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-md-4"><?php echo app('translator')->get('label.COURSE'); ?>  <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <?php if(Auth::user()->group_id != 4): ?>
                                <?php echo Form::select('course[]', $courseList, $courseIds , ['multiple' => 'multiple', 'class' => 'form-control ', 'id' => 'course', 'data-width' => '100px']); ?>

                                <span class="text-danger"><?php echo e($errors->first('course')); ?></span>
                            <?php else: ?>
                                <div class="control-label pull-left"> <strong> <?php echo e($activeCourseInfo->name); ?> </strong></div>
                                <?php echo Form::hidden('course_id', $activeCourseInfo->id, ['id' => 'courseId']); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-center pull-left">
                    <div class="form-group">
                        <!--                        <label class="control-label col-md-4" for="">&nbsp;</label>-->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="modeController" value="Show Filter Info" data-mode="1">
                                <i class="fa fa-search"></i> <?php echo app('translator')->get('label.GENERATE'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(Request::get('generate') == 'true'): ?>
            <div class = "row">
                <div class = "col-md-12">
                    <div id = "contentDocSummary" style = "width: 100%; height: 400px; margin: 0 auto;"></div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<script src="<?php echo e(asset('public/js/apexcharts.min.js')); ?>" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
    //START:: Multiselect Category
    var catAllSelected = false;
    $('#course').multiselect({
    numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: 'inherit',
            maxHeight: 250,
            nonSelectedText: "<?php echo app('translator')->get('label.SELECT_COURSE_OPT'); ?>",
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
            catAllSelected = true;
            },
            onChange: function () {
            catAllSelected = false;
            }
    });
//END:: Multiselect Category


//START :: Content Document Summary Chart
    var contentSummaryOptions = {
    chart: {
    height: 400,
            type: "<?php echo!empty($selectedCourses) && sizeof($selectedCourses) > 1 ? 'line' : 'bar'; ?>",
            shadow: {
            enabled: true,
                    color: '#fff',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 1
            },
            toolbar: {
            show: false
            }
    },
<?php
if (!empty($selectedCourses) && sizeof($selectedCourses) == 1) {
    ?>
        plotOptions: {
        bar: {
        horizontal: false,
                columnWidth: '15%',
                endingShape: 'rounded',
                distributed: true,
                dataLabels: {
                position: 'top', // top, center, bottom
                },
        },
        },
    <?php
}
?>
    colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233'],
            dataLabels: {
            enabled: false,
                    enabledOnSeries: undefined,
                    formatter: function (val) {
                    return parseFloat(val).toFixed(2)
                    },
                    textAnchor: 'middle',
                    distributed: false,
                    offsetX: 0,
                    offsetY: - 10,
                    style: {
                    fontSize: '12px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 'bold',
                            colors: ['#1f441e', '#ff0000', '#440a67', '#C62700', '#ABC400', '#26001b', '#ff005c', '#21209c', '#04BC06', '#013C38', '#8f4f4f', '#435560', '#025955', '#8c0000', '#763857', '#28527a', '#413c69', '#484018', '#1687a7', '#41584b', '#dd9866', '#16a596', '#649d66', '#7a4d1d', '#630B0B', '#FF5600', '#AF00A0', '#000000', '#290262', '#9D0233'],
                    },
                    background: {
                    enabled: true,
                            foreColor: '#fff',
                            padding: 4,
                            borderRadius: 2,
                            borderWidth: 1,
                            borderColor: '#fff',
                            opacity: 0.9,
                            dropShadow: {
                            enabled: false,
                                    top: 1,
                                    left: 1,
                                    blur: 1,
                                    color: '#000',
                                    opacity: 0.45
                            }
                    },
                    dropShadow: {
                    enabled: false,
                            top: 1,
                            left: 1,
                            blur: 1,
                            color: '#000',
                            opacity: 0.45
                    }
            },
            stroke: {
            curve: 'smooth'
            },
            series: [
<?php
$showLg = false;
if (!empty($selectedCourses)) {
    if (sizeof($selectedCourses) > 1) {
        $showLg = true;
        if (!empty($contentTypeList)) {
            foreach ($contentTypeList as $contentTypeId => $typeName) {
                ?>
                            {
                            name: "<?php echo e($typeName); ?>",
                                    data: [
                <?php
                foreach ($selectedCourses as $catId => $catName) {
                    $totalContent = !empty($targetArr[$catId][$contentTypeId]) ? $targetArr[$catId][$contentTypeId] : 0;
                    echo $totalContent . ',';
                }
                ?>
                                    ]
                            },
                <?php
            }
        }
    } else {
        ?>
                    {
                    name: "<?php echo e(__('label.TOTAL')); ?>",
                            data: [
        <?php
        if (!empty($contentTypeList)) {
            foreach ($contentTypeList as $contentTypeId => $typeName) {
                foreach ($selectedCourses as $catId => $catName) {
                    $totalContent = !empty($targetArr[$catId][$contentTypeId]) ? $targetArr[$catId][$contentTypeId] : 0;
                    echo $totalContent . ',';
                }
            }
        }
        ?>
                            ]
                    },
        <?php
    }
}
?>

            ],
            grid: {
            borderColor: '#e7e7e7',
                    row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            opacity: 0.5
                    },
            },
            markers: {

            size: 6
            },
            xaxis: {
            categories: [
<?php
if (!empty($selectedCourses)) {
    if (sizeof($selectedCourses) > 1) {
        foreach ($selectedCourses as $catId => $catName) {
            echo "'$catName',";
        }
    } else {
        if (!empty($contentTypeList)) {
            foreach ($contentTypeList as $contentTypeId => $typeName) {
                echo "'$typeName',";
            }
        }
    }
}
?>
            ],
                    title: {
                        <?php
                        if (sizeof($selectedCourses) > 1) {
                            ?>
                    text: "<?php echo app('translator')->get('label.COURSES'); ?>",
                            <?php
                        }else{
                            ?>
                    text: "<?php echo app('translator')->get('label.MEDIA_TYPE'); ?>",    
                            <?php
                        }
                            ?>
                            offsetY: - 15,
                            style: {
                            color: undefined,
                                    fontSize: '11px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    fontWeight: 700,
                                    cssClass: 'apexcharts-xaxis-title',
                            },
                    },
                    labels: {
                    show: true,
                            rotate: - 45,
                            rotateAlways: true,
                            hideOverlappingLabels: false,
                            showDuplicates: true,
                            trim: true,
                            minHeight: 100,
                            maxHeight: 180,
                            style: {
                            color: undefined,
                                    fontSize: '10px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    fontWeight: 600,
                                    cssClass: 'apexcharts-xaxis-title',
                            },
                    },
            },
            yaxis: {
            title: {
            text: "<?php echo app('translator')->get('label.TOTAL_NO_OF_CONTENT'); ?>",
                    style: {
                    color: undefined,
                            fontSize: '11px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 700,
                            cssClass: 'apexcharts-xaxis-title',
                    },
            },
//            forceNiceScale: true,
                    labels: {
                    show: true,
                            align: 'right',
                            minWidth: 0,
                            maxWidth: 160,
                            style: {
                            color: undefined,
                                    fontSize: '11px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    fontWeight: 400,
                                    cssClass: 'apexcharts-xaxis-title',
                            },
                            offsetX: 0,
                            offsetY: 0,
                            rotate: 0,
                            formatter: (val) => {
                    return parseFloat(val).toFixed(2)
                    },
                    },
            },
            tooltip: {
            y: {
            formatter: function (val) {

            return val
            }
            }
            },
            legend: {
<?php
if (!empty($selectedCourses)) {
    if (sizeof($selectedCourses) > 1) {
        ?>
                    show: true,
        <?php
    } else {
        ?>
                    show: false,
        <?php
    }
}
?>

            position: 'bottom',
                    horizontalAlign: 'center',
                    floating: false,
                    offsetY: 0,
                    offsetX: - 5
            }

    }

    var contentSummary = new ApexCharts(document.querySelector("#contentDocSummary"), contentSummaryOptions);
    contentSummary.render();
//END :: Content Document Summary Chart
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\afwc\resources\views/referenceArchive/courseWiseDocSummary/index.blade.php ENDPATH**/ ?>