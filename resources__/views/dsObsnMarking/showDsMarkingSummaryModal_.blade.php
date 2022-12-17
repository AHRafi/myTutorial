<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.DS_MARKING_SUMMARY')
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
                            <th class="vcenter text-center">@lang('label.DS')</th>
                            <th class="vcenter text-center">@lang('label.MARKING_STATUS')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($dsDataList))
                        <?php $sl = 0; ?>
                        @foreach($dsDataList as $dsId => $dsInfo)
                        <?php
                        $src = URL::to('/') . '/public/img/unknown.png';
                        $alt = $dsInfo['ds_name'] ?? '';
                        $personalNo = !empty($dsInfo['personal_no']) ? '(' . $dsInfo['personal_no'] . ')' : '';
                        if (!empty($dsInfo['photo']) && File::exists('public/uploads/user/' . $dsInfo['photo'])) {
                            $src = URL::to('/') . '/public/uploads/user/' . $dsInfo['photo'];
                        }
                        ?>
                        <tr>
                            <td class="text-center vcenter">{!! ++$sl !!}</td>
                            <th class="text-center vcenter">
                                <span class="tooltips" data-html="true" data-placement="bottom" title="
                                      <div class='text-center'>
                                      <img width='50' height='60' src='{!! $src !!}' alt='{!! $alt !!}'/><br/>
                                      <strong>{!! $alt !!}<br/>
                                      {!! $personalNo !!} </strong>
                                      </div>
                                      ">
                                    {{ $dsInfo['appt'] ?? '' }}
                                </span>
                            </th>
                            <td class="text-center vcenter">
                                @if((array_key_exists($dsInfo['ds_id'], $eventAssessmentMarkingLockInfo)) && (array_key_exists($dsInfo['ds_id'], $eventAssessmentMarkingInfo)))
                                <span class="label label-sm label-purple">@lang('label.FORWORDED')</span>
                                @elseif(array_key_exists($dsInfo['ds_id'], $eventAssessmentMarkingInfo))
                                <span class="label label-sm label-blue-steel">@lang('label.DRAFTED')</span>
                                @else
                                <span class="label label-sm label-grey-mint">@lang('label.NO_MARKING_PUT_YET')</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="3">@lang('label.NO_DS_IS_ASSIGNED_TO_THIS_MARKING_GROUP')</td>
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


