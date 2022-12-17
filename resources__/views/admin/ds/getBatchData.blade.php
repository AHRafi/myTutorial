@if(!empty($targetArr))
<div class="row">
    <div class="col-md-12">
        <div id="centerBarchart" style="min-width: 310px; height: 300px; margin: 0 auto;"></div>
    </div>
</div>
@else
@endif
<script type="text/javascript">
$(function () {
//BAR CHART START
var options = {

chart: {
height: 350,
        type: 'bar',
        toolbar: {
        show: false
        },
},
        plotOptions: {
        bar: {
        horizontal: false,
                columnWidth: '40%',
                endingShape: 'rounded',
                dataLabels: {
                position: 'top', // top, center, bottom
                },
        },
        },
        colors: ['#369EAD', '#C24642', '#7F6084'],
        dataLabels: {
        enabled: true,
                formatter: function (val) {
                return val;
                },
                offsetY: -90,
                style: {
                fontSize: '10px',
                        colors: ["#000"]
                }
        },
        stroke: {
        show: true,
                width: 2,
                colors: ['transparent']
        },
        series: [{
        name: "@lang('label.FORCASTED_BY_PA_DTE')",
                data: [
<?php
if (!$centerArr->isEmpty()) {
    foreach ($centerArr as $item) {
        ?>
                        "{{$targetArr[$item->center_id]['planned']}}",
        <?php
    }
}
?>
                ]
        }, {
        name:  "@lang('label.JOINED_STR')",
                data: [

<?php
if (!$centerArr->isEmpty()) {
    foreach ($centerArr as $item) {
        ?>
                        "{{$targetArr[$item->center_id]['joined']}}",
        <?php
    }
}
?>
                ]
        },{
        name:  "@lang('label.CURRENT_STR')",
                data: [

<?php
if (!$centerArr->isEmpty()) {
    foreach ($centerArr as $item) {
        ?>
                        "{{$targetArr[$item->center_id]['current']}}",
        <?php
    }
}
?>
                ]
        }
        ],
        xaxis: {
        labels: {
        rotate: - 45
        },
                categories: [
<?php
if (!$centerArr->isEmpty()) {
    foreach ($centerArr as $item) {
        ?>
                        "{!!$targetArr[$item->center_id]['name']!!}",
        <?php
    }
}
?>
                ],
        },
        yaxis: {
        title: {
        text: ''
        }
        },
        fill: {
//            opacity: 1
        type: 'gradient',
                gradient: {
                shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.25,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.95,
                        opacityTo: 0.95,
                        stops: [50, 0, 100]
                },
        },
        tooltip: {
        y: {
        formatter: function (val) {
        return  val
        }
        }
        }
}

var chart = new ApexCharts(
        document.querySelector("#centerBarchart"),
        options
        );
chart.render();
//BAR CHART END

});

</script>