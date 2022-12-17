<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.TERM_MARKING_STATUS_SUMMARY')
        </h3>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <span class="bold label label-sm label-blue-steel">
                    <?php $totalAssignedSyn = !empty($totalSyn->total_syn) ? $totalSyn->total_syn : 0;
                    ?>
                    @lang('label.TOTAL_SYN_IS_ASSIGNED_TO_THIS_COURSE'): {{$totalAssignedSyn}}
                </span>          
            </div>
        </div>
        <!--Start::Event assessment summary -->
        <div class="row margin-top-10">
            <div class="col-md-12 table-responsive">
                <div class="webkit-scrollbar max-height-300">
                    <table class="table table-bordered table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter" colspan="4">@lang('label.EVENT_ASSESSMENT_SUMMARY')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center" rowspan="2">@lang('label.SERIAL')</th>
                                <th class="vcenter" rowspan="2">@lang('label.EVENT')</th>
                                <th class="vcenter text-center" colspan="2">@lang('label.TOTAL_NO_OF_SYN')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center">@lang('label.MARKED')</th>
                                <th class="vcenter text-center">@lang('label.UNMARKED')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($termToEventArr))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($termToEventArr as $eventId => $eventName)
                            <tr>
                                <td class="vcenter text-center">{{++$sl}}</td>
                                <td class="vcenter">
                                    {{!empty($eventName)?$eventName:''}}
                                </td>
                                
                                <?php
                                $markedSyn = isset($markedEventSynArr[$eventId]['mark'])?$markedEventSynArr[$eventId]['mark']:0;
                                $unmarkedSyn = isset($markedEventSynArr[$eventId]['unmark'])?$markedEventSynArr[$eventId]['unmark']:$totalAssignedSyn;
                                $markedHref = !empty($markedSyn) ? '#showEventSynSummary':'';
                                $unmarkedHref = !empty($unmarkedSyn) ? '#showEventSynSummary':'';
                                $markedClass = !empty($markedSyn) ? 'event-syn-summary tooltips':'';
                                $unmarkedClass = !empty($unmarkedSyn) ? 'event-syn-summary tooltips':'';
                                $markedTitle = !empty($markedSyn) ? __('label.CLICK_HERE_TO_VIEW_MARKED_SYN'):'';
                                $unmarkedTitle = !empty($unmarkedSyn) ? __('label.CLICK_HERE_TO_VIEW_UNMARKED_SYN'):'';
                                
                                ?>

                                <td class="vcenter text-center">
                                    <a class = "btn btn-xs bold  green-steel {{$markedClass}}" data-mark-id="1" data-term-id="{{$request->term_id}}" course-id="{{$request->course_id}}" data-event-id="{{$eventId}}" title="{{$markedTitle}}" type="button" href="{{$markedHref}}" data-toggle="modal">
                                        {{$markedSyn}}
                                    </a>
                                </td>
                                <td class="vcenter text-center">
                                    <a class = "btn btn-xs bold  red-intense {{$unmarkedClass}}" data-mark-id="2" data-term-id="{{$request->term_id}}" course-id="{{$request->course_id}}" data-event-id="{{$eventId}}" title="{{$unmarkedTitle}}" type="button" href="{{$unmarkedHref}}" data-toggle="modal">
                                        {{$unmarkedSyn}}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">@lang('label.NO_DATA_FOUND')</td>
                            </tr>
                            @endif
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>

        <!--End::Event assessment summary -->

        <!--Start::DS Obsn summary -->
        <div class="row margin-top-10">
            <div class="col-md-12 table-responsive">
                <div class="webkit-scrollbar max-height-300">
                    <table class="table table-bordered table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter" colspan="4">@lang('label.DS_OBSN_MARKING_SUMMARY')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center" rowspan="2">@lang('label.SERIAL')</th>
                                <th class="vcenter" rowspan="2">@lang('label.WEEK')</th>
                                <th class="vcenter text-center" colspan="2">@lang('label.TOTAL_NO_OF_SYN')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center">@lang('label.MARKED')</th>
                                <th class="vcenter text-center">@lang('label.UNMARKED')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($weekInfoArr))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($weekInfoArr as $weekId => $weekName)
                            <tr>
                                <td class="vcenter text-center">{{++$sl}}</td>
                                <td class="vcenter">
                                    {{!empty($weekName)?$weekName:''}}
                                </td>
                                <?php
                                $markedSyn = isset($markedWeekArr[$weekId]['mark'])?$markedWeekArr[$weekId]['mark']:0;
                                $unmarkedSyn = isset($markedWeekArr[$weekId]['unmark'])?$markedWeekArr[$weekId]['unmark']:$totalAssignedSyn;
                                $markedHref = !empty($markedSyn) ? '#showDsObsnSummary':'';
                                $unmarkedHref = !empty($unmarkedSyn) ? '#showDsObsnSummary':'';
                                $markedClass = !empty($markedSyn) ? 'ds-obsn-summary tooltips':'';
                                $unmarkedClass = !empty($unmarkedSyn) ? 'ds-obsn-summary tooltips':'';
                                $markedTitle = !empty($markedSyn) ? __('label.CLICK_HERE_TO_VIEW_MARKED_SYN'):'';
                                $unmarkedTitle = !empty($unmarkedSyn) ? __('label.CLICK_HERE_TO_VIEW_UNMARKED_SYN'):'';
                                
                                ?>

                                <td class="vcenter text-center">
                                    <a class = "btn btn-xs bold  green-steel {{$markedClass}}" data-mark-id="1" data-term-id="{{$request->term_id}}" course-id="{{$request->course_id}}" data-week-id="{{$weekId}}" title="{{$markedTitle}}" type="button" href="{{$markedHref}}" data-toggle="modal">
                                        {{$markedSyn}}
                                    </a>
                                </td>
                                <td class="vcenter text-center">
                                    <a class = "btn btn-xs bold  red-intense {{$unmarkedClass}}" data-mark-id="2" data-term-id="{{$request->term_id}}" course-id="{{$request->course_id}}" data-week-id="{{$weekId}}" title="{{$unmarkedTitle}}" type="button" href="{{$unmarkedHref}}" data-toggle="modal">
                                        {{$unmarkedSyn}}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">@lang('label.NO_DATA_FOUND')</td>
                            </tr>
                            @endif
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>
        <!--End::Ds obsn summary -->

        <!--Start::Mutual Assessment summary -->
        <div class="row margin-top-10">
            <div class="col-md-12 table-responsive">
                <div class="webkit-scrollbar max-height-300">
                    <table class="table table-bordered table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter" colspan="4">@lang('label.MUTUAL_ASSESSMENT_MARKING_SUMMARY')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center" rowspan="2">@lang('label.SERIAL')</th>
                                <th class="vcenter" rowspan="2">@lang('label.WEEK')</th>
                                <th class="vcenter text-center" colspan="2">@lang('label.TOTAL_NO_OF_SYN')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center">@lang('label.MARKED')</th>
                                <th class="vcenter text-center">@lang('label.UNMARKED')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($mutualAsmntWeekArr))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($mutualAsmntWeekArr as $weekId => $weekName)
                            <tr>
                                <td class="vcenter text-center">{{++$sl}}</td>
                                <td class="vcenter">
                                    {{!empty($weekName)?$weekName:''}}
                                </td>
                                <?php
                                $markedSyn = isset($mutualAsmntMarkedWeekArr[$weekId]['mark'])?$mutualAsmntMarkedWeekArr[$weekId]['mark']:0;
                                $unmarkedSyn = isset($mutualAsmntMarkedWeekArr[$weekId]['unmark'])?$mutualAsmntMarkedWeekArr[$weekId]['unmark']:$totalAssignedSyn;
                                $markedHref = !empty($markedSyn) ? '#showMutualAssessmentSummary':'';
                                $unmarkedHref = !empty($unmarkedSyn) ? '#showMutualAssessmentSummary':'';
                                $markedClass = !empty($markedSyn) ? 'mutual-assessment-summary tooltips':'';
                                $unmarkedClass = !empty($unmarkedSyn) ? 'mutual-assessment-summary tooltips':'';
                                $markedTitle = !empty($markedSyn) ? __('label.CLICK_HERE_TO_VIEW_MARKED_SYN'):'';
                                $unmarkedTitle = !empty($unmarkedSyn) ? __('label.CLICK_HERE_TO_VIEW_UNMARKED_SYN'):'';
                                
                                ?>

                                <td class="vcenter text-center">
                                    <a class = "btn btn-xs bold  green-steel {{$markedClass}}" data-mark-id="1" data-term-id="{{$request->term_id}}" course-id="{{$request->course_id}}" data-week-id="{{$weekId}}" title="{{$markedTitle}}" type="button" href="{{$markedHref}}" data-toggle="modal">
                                        {{$markedSyn}}
                                    </a>
                                </td>
                                <td class="vcenter text-center">
                                    <a class = "btn btn-xs bold  red-intense {{$unmarkedClass}}" data-mark-id="2" data-term-id="{{$request->term_id}}" course-id="{{$request->course_id}}" data-week-id="{{$weekId}}" title="{{$unmarkedTitle}}" type="button" href="{{$unmarkedHref}}" data-toggle="modal">
                                        {{$unmarkedSyn}}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">@lang('label.NO_DATA_FOUND')</td>
                            </tr>
                            @endif
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>
        <!--End::Mutual Assessment summary -->

        <!--Start::IPFT summary -->
        <div class="row margin-top-10">
            <div class="col-md-12 table-responsive">
                <div class="webkit-scrollbar max-height-300">
                    <table class="table table-bordered table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter" colspan="4">@lang('label.IPFT_SUMMARY')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center" rowspan="2">@lang('label.SERIAL')</th>
                                <th class="vcenter" rowspan="2">@lang('label.IPFT')</th>
                                <th class="vcenter text-center" colspan="2">@lang('label.TOTAL_NO_OF_SYN')</th>
                            </tr>
                            <tr>
                                <th class="vcenter text-center">@lang('label.MARKED')</th>
                                <th class="vcenter text-center">@lang('label.UNMARKED')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($ipftInfoArr))
                            <?php
                            $sl = 0;
                            ?>
                            <tr>
                                <td class="vcenter text-center">{{++$sl}}</td>
                                <td class="vcenter">
                                    {{!empty($ipftInfoArr)?$ipftInfoArr:''}}
                                </td>
                                <?php
                                $markedSyn = isset($ipftArr['mark'])?$ipftArr['mark']:0;
                                $unmarkedSyn = isset($ipftArr['unmark'])?$ipftArr['unmark']:$totalAssignedSyn;
                                $markedHref = !empty($markedSyn) ? '#showIpftSummary':'';
                                $unmarkedHref = !empty($unmarkedSyn) ? '#showIpftSummary':'';
                                $markedClass = !empty($markedSyn) ? 'ipft-summary tooltips':'';
                                $unmarkedClass = !empty($unmarkedSyn) ? 'ipft-summary tooltips':'';
                                $markedTitle = !empty($markedSyn) ? __('label.CLICK_HERE_TO_VIEW_MARKED_SYN'):'';
                                $unmarkedTitle = !empty($unmarkedSyn) ? __('label.CLICK_HERE_TO_VIEW_UNMARKED_SYN'):'';
                                
                                ?>

                                <td class="vcenter text-center">
                                    <a class = "btn btn-xs bold  green-steel {{$markedClass}}" data-mark-id="1" data-term-id="{{$request->term_id}}" course-id="{{$request->course_id}}" title="{{$markedTitle}}" type="button" href="{{$markedHref}}" data-toggle="modal">
                                        {{$markedSyn}}
                                    </a>
                                </td>
                                <td class="vcenter text-center">
                                    <a class = "btn btn-xs bold  red-intense {{$unmarkedClass}}" data-mark-id="2" data-term-id="{{$request->term_id}}" course-id="{{$request->course_id}}" title="{{$unmarkedTitle}}" type="button" href="{{$unmarkedHref}}" data-toggle="modal">
                                        {{$unmarkedSyn}}
                                    </a>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="4">@lang('label.NO_DATA_FOUND')</td>
                            </tr>
                            @endif
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>
        <!--End::IPFT summary -->
    </div>

    <div class="modal-footer">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

<script type="text/javascript">
    $(".table-head-fixer-color").tableHeadFixer();
</script>
<!-- END:: Contact Person Information-->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

