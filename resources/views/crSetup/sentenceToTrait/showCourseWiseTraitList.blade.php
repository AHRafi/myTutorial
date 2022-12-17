<div class = "row">
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-info alert-dismissable">
            <p><strong><i class="fa fa-info-circle fa-fw"></i> {!! __('label.SENTENCE_RELATED_TRAIT_LIST') !!}</strong></p>
        </div>
    </div>
</div><!--
<div class="row">-->
<div class="table-responsive  webkit-scrollbar max-height-500">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center vcenter">@lang('label.SL_NO')</th>
                <th class="vcenter">@lang('label.TRAIT')</th>
                <th class="vcenter text-center">@lang('label.HAS_SENTENCE') ({{$courseInfoArr[$request->related_course_id]}})</th>
                <th class="vcenter text-center">@lang('label.EVENT_MAPPED') ({{$courseInfoArr[$request->selected_course_id]}})</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($traitList))
            <?php
            $sl = 0;
            ?>
            @foreach($traitList as $traitId => $traitName)
            <?php
            $icon = $color =  $mapIcon = $mapcolor =  '';
            if ((!empty($prevSentenceToTraitArr) && array_key_exists($traitId, $prevSentenceToTraitArr))) {
                $icon = 'check';
                $color = 'green';
            } else {
                $icon = 'close';
                $color = 'red';
            }


            if (!empty($markingReflArr) && array_key_exists($traitId, $markingReflArr)) {
                $mapIcon = 'check';
                $mapcolor = 'green';
            } else {
                $mapIcon = 'close';
                $mapcolor = 'red';
            }
            ?>
            <tr>
                <td class="text-center vcenter">{{ ++$sl }}</td>
                <td class=" vcenter">{{$traitName}}</td>
                <td class="vcenter text-center"><i class="fa fa-{{$icon}} font-{{$color}}"></i></td>
                <td class="vcenter text-center"><i class="fa fa-{{$mapIcon}} font-{{$mapcolor}}"></i></td>
            </tr>

            @endforeach
            @else
            <tr>
                <td colspan="13" class="vcenter">@lang('label.NO_TRAIT_FOUND')</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!--</div>-->


<!-- if submit wt chack End -->
<script type="text/javascript">
    $(function () {

    });
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>