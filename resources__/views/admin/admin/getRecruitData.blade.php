<div id="activeYrBarChart" style="min-width: 300px; height: 350px; margin: 0 auto;"></div>
<script type="text/javascript">
    $(function () {
        //BAR CHART START
        var colors = ['#008FFB', '#26a69a', '#775DD0', '#FF4560', '#00E396', '#FEB019', '#546E7A', '#D10CE8'];
        var options = {
            chart: {
                height: 300,
                type: 'bar',
                toolbar: {
                    show: false
                },
            },
            colors: colors,
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    distributed: true,
//                                    endingShape: 'rounded',
                    dataLabels: {
                        position: 'top', // top, center, bottom
                    },
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val;
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#000"]
                }
            },
            stroke: {
                show: true,
                width: 2,
//                                colors: ['transparent']
            },
            series: [{
                    name: "@lang('label.RECRUIT')",
                    data: [
                        "{{$rectPlanned}}",
                        "{{$rectJoint}}",
                        "{{$rectCurrent}}",
                        "{{$rectOnParadeCount}}",
                        "{{$rectAbsentcount}}",
                    ]
                },
            ],
            xaxis: {
                labels: {
                    rotate: -25,
                    style: {
                        colors: colors,
                        fontSize: '12px'
                    }
                },
                categories: [
                    "@lang('label.FORCASTED_BY_PA_DTE')",
                    "@lang('label.JOINED_STR')",
                    "@lang('label.CURRENT_STR')",
                    "@lang('label.ON_PARADE')",
                    "@lang('label.ABSENT')",
                ],
            },
            yaxis: {
                title: {
                    text: 'Recruit Statistics (All Batches)'
                }
            },
            fill: {
//                                opacity: 1
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "horizontal",
                    shadeIntensity: 0.50,
                    gradientToColors: undefined,
                    inverseColors: true,
                    opacityFrom: 0.95,
                    opacityTo: 0.95,
                    stops: [80, 50, 80]
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return  val;
                    }
                }
            }
        }

        var chart = new ApexCharts(
                document.querySelector("#activeYrBarChart"),
                options
                );
        chart.render();

// endof bar chart

    });
</script>