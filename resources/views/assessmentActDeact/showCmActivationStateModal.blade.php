<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.CM_ACTIVATION_STATE')
        </h3>
    </div>

    <div class="modal-body">
        <div class="row margin-bottom-75">
            <div class="col-md-12">
                <div class="col-md-4">
                    <span><strong>@lang('label.TRAINING_YEAR') :</strong> {{$activeTrainingYearInfo->name}}</span>&nbsp;&nbsp;&nbsp;
                </div>
                <div class="col-md-4">
                    <span><strong>@lang('label.COURSE') :</strong> {{$course->name}}</span>&nbsp;&nbsp;&nbsp;
                </div>
                <div class="col-md-4">
                    <span><strong>@lang('label.TERM') :</strong> {{$term->name}}</span>&nbsp;&nbsp;&nbsp;
                </div>
                <div class="col-md-4">
                    <span><strong>@lang('label.EVENT') :</strong> {{$event->event_code}}</span>&nbsp;&nbsp;&nbsp;
                </div>
                @if(!empty($subEvent))
                <div class="col-md-4">
                    <span><strong>@lang('label.SUB_EVENT') :</strong> {!! $subEvent->event_code!!}</span>&nbsp;&nbsp;&nbsp;
                </div>
                @endif
                @if(!empty($subSubEvent))
                <div class="col-md-4">
                    <span><strong>@lang('label.SUB_SUB_EVENT') :</strong> {!! $subSubEvent->event_code !!}</span>&nbsp;&nbsp;&nbsp;
                </div>
                @endif
                @if(!empty($subSubSubEvent))
                <div class="col-md-4">
                    <span><strong>@lang('label.SUB_SUB_SUB_EVENT') :</strong> {!! $subSubSubEvent->event_code !!}</span>&nbsp;&nbsp;&nbsp;
                </div>
                @endif
            </div>
            <div class="col-md-12">
                <table class="table table-bordered table-hover table-head-fixer-color">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="vcenter text-center" colspan="2">@lang('label.CM')</th>
                            <th class="vcenter text-center">@lang('label.ACTIVATION_STATUS')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($cmDataList))
                        <?php $sl = 0; ?>
                        @foreach($cmDataList as $cmId => $cm)
                        <tr>
                            <td class="text-center vcenter">{!! ++$sl !!}</td>
                            <td class="vcenter text-center" width="36px">
                                @if(!empty($cm['photo'] && File::exists('public/uploads/cm/' . $cm['photo'])))
                                <img width="36" height="40" src="{{URL::to('/')}}/public/uploads/cm/{{$cm['photo']}}" alt="{{ Common::getFurnishedCmName($cm['cm_name']) }}"/>
                                @else
                                <img width="36" height="40" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($cm['cm_name']) }}"/>
                                @endif
                            </td>
                            <td class="vcenter">{!!Common::getFurnishedCmName($cm['cm_name'])!!}</td>
                            <td class="vcenter text-center">
                                @if(!empty($eventAssessmentMarkingArr[$cmId]['mks']) || (empty($eventAssessmentMarkingArr[$cmId]['mks']) && !empty($eventAssessmentMarkingArr[$cmId]['locked_by'])))
                                <span class="label label-sm label-purple">@lang('label.THIS_CM_HAS_ALREADY_BEEN_ASSESSED_IN_THIS_EVENT')</span>
                                @else
                                {!! Form::checkbox('on_pause_['.$cm['cm_marking_group_id'].']'
                                , 1, !empty($cm['active']) ? 1:0
                                , ['id'=> 'onPause_['.$cm['cm_marking_group_id'].']'
                                , 'class' => 'make-switch on-pause-switch tooltips','data-on-text'=> __('label.ACTIVATE')
                                , 'data-off-text'=> __('label.PAUSE'),'cm-marking-group-id' => $cm['cm_marking_group_id']]) !!} 
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4">@lang('label.NO_CM_IS_ASSIGNED_TO_THIS_EVENT')</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>

</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>


