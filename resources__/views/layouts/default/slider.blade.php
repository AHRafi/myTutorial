<div id="jssor_1" style="margin:0 auto;top:0px;left:0px;width:1046px;height:380px;overflow:hidden;visibility:hidden;">
    <!-- Loading Screen -->
    <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:1046px;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
        <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="{{URL::to('/public/uploads/slider/spin.svg')}}" />
    </div>

    <!-- Slider ----->

    <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:1046px;height:380px;overflow:hidden;">
        @if(!$sliderArr->isEmpty())
        @foreach($sliderArr as $item)
        <div>
            <img data-u="image" src="{{URL::to('/public/uploads/slider',$item->banner_image)}}" />
            <div u="thumb">{{ $item->caption }}</div>
        </div>
        @endforeach
        @endif
    </div>

    <!-- Thumbnail Navigator -->
    <div u="thumbnavigator" style="position:absolute;bottom:0px;left:0px;width:1046px;height:50px;color:#FFF;overflow:hidden;cursor:default;background-color:rgba(0,0,0,.5);">
        <div u="slides">
            <div u="prototype" style="position:absolute;top:0;left:0;width:1046px;height:50px;">
                <div u="thumbnailtemplate" style="position:absolute;top:0;left:0;width:100%;height:100%;font-family:arial,helvetica,verdana;font-weight:normal;line-height:50px;font-size:16px;padding-left:10px;box-sizing:border-box;"></div>
            </div>
        </div>
    </div>

    <!-- Arrow Navigator -->
    <div data-u="arrowleft" class="jssora061" style="width:55px;height:55px;top:0px;left:25px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
        <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
        <path class="a" d="M11949,1919L5964.9,7771.7c-127.9,125.5-127.9,329.1,0,454.9L11949,14079"></path>
        </svg>
    </div>
    <div data-u="arrowright" class="jssora061" style="width:55px;height:55px;top:0px;right:25px;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
        <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
        <path class="a" d="M5869,1919l5984.1,5852.7c127.9,125.5,127.9,329.1,0,454.9L5869,14079"></path>
        </svg>
    </div>



</div>
<script type="text/javascript">jssor_1_slider_init();</script>
<script type="text/javascript">
    $('#jssor_1').css("padding-left", '30px');
</script>