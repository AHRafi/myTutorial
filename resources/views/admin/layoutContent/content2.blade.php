<!--major events-->
<div class="border border-default margin-bottom-10" >
    <div class="font-white dashboard-title-back-style">
        <h4 class="text-center bold">@lang('label.MAJOR_EVENTS')</h4>
    </div>
    <div>
        <marquee direction="up" onmouseover="this.stop();" onmouseout="this.start();" class="dash-marquee">
            @if(!$majorEvent->isEmpty())
            @foreach($majorEvent as $item)
            <ul class="feeds margin-bottom-10 bold">
                <li style="border-radius:4% !important">
                    <div class="col1">
                        <div class="cont">
                            <div class="cont-col2">
                                <div class="desc font-green">@lang('label.EVENT_ITEM'): {{$item->name}}</div>
                                <div class="desc font-blue-dark">@lang('label.DATE'): {{Helper::formatDate2($item->from_date)}} {{isset($item->to_date)?__('label.TO').' '. Helper::formatDate2($item->to_date):'' }}</div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            @endforeach
            @endif
        </marquee>
    </div>
</div>