<div class="modal-content">
    <div class="modal-header clone-modal-header">
        <button type="button" data-dismiss="modal" data-placement="bottom" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">
            @lang('label.CLOSE')
        </button>
        <h3 class="modal-title text-center">
            @lang('label.MUTUAL_ASSESSSMENT') @lang('label.MARKING_SHEET')
        </h3>
    </div>
    {!! Form::open(array('group' => 'form', 'url' => 'mutualAssessment/generate','class' => 'form-horizontal','id' => 'submitForm')) !!}   
    <div class="modal-body">

        <div class="row">
            <div class="col-md-4">
                <span>@lang('label.COURSE') : <strong>{{ $courseName->name}}</strong></span>
            </div>
            <div class="col-md-4">
                <span>@lang('label.TERM') : <strong>{{ $term->name}}</strong></span>
            </div>
            @if($maProcess == '1')
            <div class="col-md-4">
                <span>@lang('label.SYNDICATE') : <strong>{{ $syndicate->name }}</strong></span>
            </div>
            @elseif($maProcess == '2')
            <div class="col-md-4">
                <span>@lang('label.SUB_SYNDICATE') : <strong>{{ $subSyndicate->name }}</strong></span>
            </div>
            @elseif($maProcess == '3')
            <div class="col-md-4">
                <span>@lang('label.EVENT') : <strong>{{ $eventName->name }}</strong></span>
            </div>
            @if(!empty($subEventId))
            <div class="col-md-4">
                <span>@lang('label.SUB_EVENT') : <strong>{{ $subEventName->name }}</strong></span>
            </div>
            @endif
            @if(!empty($subSubEventId))
            <div class="col-md-4">
                <span>@lang('label.SUB_SUB_EVENT') : <strong>{{ $subSubSubEventName->name }}</strong></span>
            </div>
            @endif
            @if(!empty($subSubSubEventId))
            <div class="col-md-4">
                <span>@lang('label.SUB_SUB_SUB_EVENT') : <strong>{{ $subSubSubEventName->name }}</strong></span>
            </div>
            @endif
            <div class="col-md-4">
                <span>@lang('label.EVENT_GROUP') : <strong>{{ $eventGroup->name }}</strong></span>
            </div>
            @endif
        </div>
        
        @if(!$cmList->isEmpty())
        <div class="row margin-top-10">
            <div class="col-md-12 table-responsive">
                <div class="webkit-scrollbar my-datatable">
                    <table class="table table-bordered table-hover relation-view-2" id="cmListTable2">
                        <thead>
                            <tr>
                                <th class="vcenter text-center">@lang('label.SL')</th>
                                <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter">@lang('label.RANK')</th>
                                <th class="vcenter">@lang('label.NAME')</th>
                                <th class="vcenter">@lang('label.PHOTO')</th>
                                @if(!empty($factorList))
                                @foreach($factorList as $factorId => $factor)
                                <th class="vcenter text-center">{{$factor}}</th>
                                @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $sl = 1; @endphp 
                            @foreach($cmList as $cm) 
                            <tr>
                                <td class="vcenter text-center"><strong>{{ $sl++ }}</strong></td>
                                <td class="vcenter">{{ $cm->personal_no }}</td>
                                <td class="vcenter">{{ $cm->rank }}</td>
                                <td class="vcenter">{!! Common::getFurnishedCmName($cm->full_name) !!}</td>
                                <td class="vcenter" width="50px">
                                    @if(!empty($cm->photo) && File::exists('public/uploads/cm/' . $cm->photo))
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cm->photo}}" alt="{!! Common::getFurnishedCmName($cm->full_name) !!}"/>
                                    @else
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cm->full_name) !!}"/>
                                    @endif
                                </td>
                                @if(!empty($factorList))
                                @foreach($factorList as $factorId => $factor)
                                <td class="vcenter text-center"></td>
                                @endforeach
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    {!! Form::hidden('course_id', $courseId) !!} 
    {!! Form::hidden('term_id', $termId) !!} 
    {!! Form::hidden('syn_id', $synId) !!} 
    {!! Form::hidden('cm_group_id', $request->cm_group_id) !!} 
    {!! Form::hidden('event_group_id', $eventGroupId) !!} 
    {!! Form::hidden('ma_process', $maProcess) !!} 
    {!! Form::hidden('sub_syn_id', $subSynId) !!} 
    {!! Form::hidden('event_id', $eventId) !!} 
    {!! Form::hidden('sub_event_id', $subEventId) !!} 
    {!! Form::hidden('sub_sub_event_id', $subSubEventId) !!} 
    {!! Form::hidden('sub_sub_sub_event_id', $subSubSubEventId) !!} 
    <div class="modal-footer">
        <button type="submit" class="btn green" id="generate">@lang('label.GENERATE')</button>
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-outline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>

    {!! Form::close() !!}  
</div>

<style>
    .borderless td, .borderless th {
        border: none;
    }    
</style>
@if(!$cmList->isEmpty())
<script>
    $(document).ready(function () {
        $('#cmListTable2').DataTable();
    });
</script>
@endif