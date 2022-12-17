@if(!$prevCrGen->isEmpty())
<div class = "row">
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.COURSE_REPORT_GENERATION_HAS_ALREADY_BEEN_STARTED') !!}</strong></p>
        </div>
    </div>
</div>
@endif

<div class="row">
    @if(!empty($markingRefl))
    @if(!empty($markingSlabList))
    <?php $t = 0; ?>
    @foreach($markingSlabList as $markingSlabId => $markingSlab)
    <?php
    if ($t % 3 == 0) {
        echo '</div><div class="row">';
    }
    ?>
    <div class="col-md-3  marking-slab-bolck" id="newFactor_{{$markingSlabId}}">
        <span class="bold font-size-14 margin-bottom-10">{{$markingSlab}}</span>

        @if(!empty($prevFactorArr[$markingSlabId]))
        <?php
        $i = 1;
        ?>
        @foreach($prevFactorArr[$markingSlabId] as $key => $factor)

        <div class="factor">
            <div class="col-md-10 margin-top-10 padding-0">
                {!! Form::text('factor['.$markingSlabId.']['.$key.']', $factor, ['id'=> 'factor_'.$markingSlabId.'_'.$key
                ,'class' => 'form-control factor factor-'.$markingSlabId]) !!} 
            </div>
            @if($prevCrGen->isEmpty())
            <div class="col-md-2 margin-top-10 padding-left-right-5">
                @if($i == 1)
                <button class="btn btn-inline green-haze add-factor tooltips" data-marking-slab-id="{{$markingSlabId}}" data-placement="right" title="@lang('label.ADD_NEW_FACTOR')" type="button">
                    <i class="fa fa-plus"></i>
                </button>
                @else
                <button class="btn btn-inline btn-danger remove-factor  tooltips"  title="@lang('label.REMOVE')" type="button">
                    <i class="fa fa-remove"></i>
                </button>
                @endif
            </div>
            @endif
        </div>
        <?php
        $i++;
        ?>

        @endforeach
        @else
        <?php
        $v4 = 'f' . uniqid();
        ?>
        <div class="factor ">
            <div class="col-md-10 margin-top-10 padding-0">
                {!! Form::text('factor['.$markingSlabId.']['.$v4.']', null, ['id'=> 'factor_'.$markingSlabId.'_'.$v4
                ,'class' => 'form-control factor factor-'.$markingSlabId]) !!} 
            </div>
            @if($prevCrGen->isEmpty())
            <div class="col-md-2 margin-top-10 padding-left-right-5">
                <button class="btn btn-inline green-haze add-factor tooltips" data-marking-slab-id="{{$markingSlabId}}" data-placement="right" title="@lang('label.ADD_NEW_FACTOR')" type="button">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
            @endif
        </div>
        @endif
    </div>
    <?php $t++; ?>
    @endforeach
    @if($prevCrGen->isEmpty())
    <div class="col-md-12 margin-top-20 text-center">
        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
            <i class="fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href="{{ URL::to('crFactorToTrait') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
    @endif
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_MARKING_SLAB_FOUND')</p>
        </div>
    </div>
    @endif
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.MARKING_REFLECTION_IS_NOT_SET_YET')</p>
        </div>
    </div>
    @endif
</div>


<!-- if submit wt chack End -->
<script type="text/javascript">
    $(function () {
        
    });
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>