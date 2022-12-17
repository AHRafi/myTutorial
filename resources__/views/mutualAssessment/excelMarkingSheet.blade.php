   
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <tbody>
            <tr>
                <td colspan="5" style="text-align:center; font-size: 24px"><strong>@lang('label.MUTUAL_ASSESSSMENT')</strong></td>
            </tr>
            <tr style="background-color:#000000">
                <td colspan="5">
                    <h5>
                        {{__('label.TOTAL_NUMBER_OF_CM')}} : <strong>{{!$cmList->isEmpty() ? sizeof($cmList) : 0}} |</strong>
                        {{__('label.COURSE')}} : <strong>{{$courseName->name}} |</strong>
                        {{__('label.TERM')}} : <strong>{{ $term->name }} |</strong>

                        @if($maProcess == '1')
                        {{__('label.SYNDICATE')}} : <strong>{{ $syndicate->name }} </strong>
                        @elseif($maProcess == '2')
                        {{__('label.SUB_SYNDICATE')}} : <strong>{{ $subSyndicate->name }} </strong>
                        @elseif($maProcess == '3')
                        {{__('label.EVENT')}} : <strong>{{ $eventName->name }} |</strong>
                        @if(!empty($subEventId))
                        {{__('label.SUB_EVENT')}} : <strong>{{ $subEventName->name }} |</strong>
                        @endif
                        @if(!empty($subSubEventId))
                        {{__('label.SUB_SUB_EVENT')}} : <strong>{{ $subSubSubEventName->name }} |</strong>
                        @endif
                        @if(!empty($subSubSubEventId))
                        {{__('label.SUB_SUB_SUB_EVENT')}} : <strong>{{ $subSubSubEventName->name }} |</strong>
                        @endif
                        {{__('label.EVENT_GROUP')}} : <strong>{{ $eventGroup->name }} </strong>
                        @endif

                    </h5>
                </td>
            </tr>

            <tr></tr>
            <tr>
                <td ><strong>@lang('label.SL')</strong></td>
                <td ><strong>@lang('label.PERSONAL_NO')</strong></td>
                <td ><strong>@lang('label.RANK')</strong></td>
                <td ><strong>@lang('label.NAME')</strong></td>
                @if(!empty($factorList))
                @foreach($factorList as $factorId => $factor)
                <td><strong>{{$factor}}</strong></td>
                @endforeach
                @endif
            </tr>
            @php $sl = 1; @endphp 
            @foreach($cmList as $cm) 
            <tr>
                <td ><strong>{{ $sl++ }}</strong></td>
                <td >{{ $cm->personal_no }}</td>
                <td >{{ $cm->rank }}</td>
                <td >{!! Common::getFurnishedCmName($cm->full_name) !!}</td>
                @if(!empty($factorList))
                @foreach($factorList as $factorId => $factor)
                <td ></td>
                @endforeach
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
    .borderless td, .borderless th {
        border: none;
    }    
</style>