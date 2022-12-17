<!--<div class = "row">
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.COURSE_REPORT_GENERATION_HAS_ALREADY_BEEN_STARTED') !!}</strong></p>
        </div>
    </div>
</div>-->
<div class = "row">
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-info alert-dismissable">
            <p><strong><i class="fa fa-info-circle fa-fw"></i> {!! __('label.COURSE_REPORT_GENERATION_REFERENCE') !!}</strong></p>
        </div>
    </div>
</div>

<div class="row">
    @if(!empty($markingSlabList))
    @foreach($markingSlabList as $markingSlabId => $markingSlab)
    <div class="col-md-11  marking-slab-bolck" id="newSentence_{{$markingSlabId}}">
        <span class="bold font-size-14 margin-bottom-10">{{$markingSlab}}</span>

        @if(!empty($prevSentenceArr[$markingSlabId]))
        <?php
        $i = 1;
        ?>
        @foreach($prevSentenceArr[$markingSlabId] as $key => $sentence)

        <div class="sentence">
            <div class="col-md-11 margin-top-10 padding-0">
                {!! Form::text('sentence['.$markingSlabId.']['.$key.']', $sentence, ['id'=> 'sentence_'.$markingSlabId.'_'.$key
                ,'class' => 'form-control sentence sentence-'.$markingSlabId]) !!} 
            </div>
            <div class="col-md-1 margin-top-10 padding-left-right-5 width-50">
                @if($i == 1)
                <button class="btn btn-inline green-haze add-sentence tooltips" data-marking-slab-id="{{$markingSlabId}}" data-placement="right" title="@lang('label.ADD_NEW_FACTOR')" type="button">
                    <i class="fa fa-plus"></i>
                </button>
                @else
                <button class="btn btn-inline btn-danger remove-sentence  tooltips"  title="@lang('label.REMOVE')" type="button">
                    <i class="fa fa-remove"></i>
                </button>
                @endif
            </div>
        </div>
        <?php
        $i++;
        ?>

        @endforeach
        @else
        <?php
        $v4 = 'f' . uniqid();
        ?>
        <div class="sentence ">
            <div class="col-md-11 margin-top-10 padding-0">
                {!! Form::text('sentence['.$markingSlabId.']['.$v4.']', null, ['id'=> 'sentence_'.$markingSlabId.'_'.$v4
                ,'class' => 'form-control sentence sentence-'.$markingSlabId]) !!} 
            </div>
            <div class="col-md-1 margin-top-10 padding-left-right-5 width-50">
                <button class="btn btn-inline green-haze add-sentence tooltips" data-marking-slab-id="{{$markingSlabId}}" data-placement="right" title="@lang('label.ADD_NEW_FACTOR')" type="button">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        @endif
    </div>
    @endforeach
    <div class="col-md-12 margin-top-20 text-center">
        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
            <i class="fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href="{{ URL::to('crSentenceToTrait') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_MARKING_SLAB_FOUND')</p>
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