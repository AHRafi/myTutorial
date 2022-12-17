@if(in_array(Auth::user()->group_id, [1, 2, 3]))
@if(!empty($batchList))
@if(!empty($activeYearRecruitPlain))
<div class="font-white dashboard-title-back-style">
    <h4 class="text-center bold">@lang('label.RECRUIT_STATISTICS') (@lang('label.ACTIVE_TRAINING_YEAR'))</h4>
</div>
<div class="tabbable-line">
    <ul class="nav nav-tabs ">
        @if(!empty($batchList))
        <?php
        $sl = 0;
        ?>
        @foreach($batchList as $batchId=>$item)
        <?php
        ++$sl;
        if ($sl == 1) {
            $active = 'active';
        } else {
            $active = '';
        }
        ?>
        <li class="{{$active}}">
            <a class="batch" data-id="{{$batchId}}" data-toggle="tab">{{$item}}</a>
        </li>
        @endforeach
        @endif
    </ul>
    <?php
    if (empty($batchList)) {
        $border = 'border: 1px solid #F8F9F9;text-align: center;';
    } else {
        $border = '';
    }
    ?>
    <div class="tab-content" style="{{$border}}" >
        <div class="tab-pane active">
            @if(!empty($batchList))
            <div id="getChart">
            </div>
            @else
            <span>@lang('label.BATCH_NOT_ASSIGN')</span>
            @endif
        </div>
    </div>
</div>
@endif
@endif
@elseif(in_array(Auth::user()->group_id, [4, 5, 6, 7]))
<div class="font-white dashboard-title-back-style margin-bottom-10">
    <h4 class="text-center bold">@lang('label.RECRUIT_STATISTICS') (@lang('label.ACTIVE_TRAINING_YEAR'))</h4>
</div>
<div class="tabbable-line">
    <ul class="nav nav-tabs ">
        @if(!empty($batchList))
        <?php
        $sl = 0;
        ?>
        @foreach($batchList as $batchId=>$item)
        <?php
        ++$sl;
        if ($sl == 1) {
            $active = 'active';
        } else {
            $active = '';
        }
        ?>
        <li class="{{$active}}">
            <a class="batch" data-id="{{$batchId}}" data-toggle="tab">{{$item}}</a>
        </li>
        @endforeach
        @endif
    </ul>
    <?php
    if (empty($batchList)) {
        $border = 'border: 1px solid #F8F9F9;text-align: center;';
    } else {
        $border = '';
    }
    ?>
    <div class="tab-content" style="{{$border}}" >
        <div class="tab-pane active">
            @if(!empty($batchList))
            <div id="getChart">
            </div>
            @else
            <span>@lang('label.BATCH_NOT_ASSIGN')</span>
            @endif
        </div>
    </div>
</div>
@elseif(in_array(Auth::user()->group_id, [8]))
<div class="font-white dashboard-title-back-style">
    <h4 class="text-center bold">@lang('label.RECRUIT_STATISTICS') (@lang('label.ALL_BATCHES'))</h4>
</div>
<div id="allBatchsBarChart" style="min-width: 310px; height: 350px; margin: 0 auto;"></div>
@endif