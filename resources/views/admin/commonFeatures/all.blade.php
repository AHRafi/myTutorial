<div class="page-bar">
    <ul class="page-breadcrumb margin-top-10">
        <li>
            <a href="{{url('dashboard')}}">@lang('label.HOME')</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>@lang('label.DASHBOARD')</span>
        </li>
    </ul>
    <div class="page-toolbar margin-top-15">
        <h5 class="dashboard-date font-green-sharp"><span class="icon-calendar"></span> @lang('label.TODAY_IS') <span class="font-green-sharp">{!! date('l, d F Y ') !!} <span id="timeCountUp">{!! date('H:i:s') !!}</span></span> </h5>   
    </div>
</div>
@if(!empty($noticeList))
<div class="row margin-top-10">
    <div class="col-md-11 col-sm-11 col-xs-11 col-lg-11 scroll-block">
        <div class="marquee marquee2">
            <!--//notice-->
            <?php
            $str = '';
            ?>
            @foreach($noticeList as $id => $notice)
            <?php $str .= '<i class="fa fa-clipboard">&nbsp;</i>' . Helper::trimString100($notice) . '&nbsp;&nbsp;'; ?>
            @endforeach
            <?php
            echo trim($str, " | ");
            ?>

        </div>	
    </div>
    <a class="text-decoration-none bold" href="{{ URL::to('/noticeBoard') }}">
        <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 scroll-btn-block text-center">
            @lang('label.SEE_ALL')
        </div>	
    </a>	
</div>
@endif

<script src="{{asset('public/js/jquery.marquee.min.js')}}" type="text/javascript"></script>
<script>
    $(function () {
        $('.marquee').marquee({
            //speed in milliseconds of the marquee
            duration: 2000,
            //gap in pixels between the tickers
            gap: 10,
            //time in milliseconds before the marquee will start animating
            delayBeforeStart: 0,
            //'left' or 'right'
            direction: 'left',
            //true or false - should the marquee be duplicated to show an effect of continues flow
            duplicated: false,
            pauseOnHover: true
        });



        var timerVar = setInterval(countTimer, 1000);


        function countTimer() {
            var time = new Date();
            var hours = time.getHours();
            var minutes = time.getMinutes();
            var seconds = time.getSeconds();

            if (hours < 10) {
                hours = '0' + hours;
            }
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            if (seconds < 10) {
                seconds = '0' + seconds;
            }
            document.getElementById("timeCountUp").innerHTML = hours + ":" + minutes + ":" + seconds;
        }
    });
</script>