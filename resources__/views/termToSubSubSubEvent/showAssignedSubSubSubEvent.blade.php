<div class="modal-content">
    <div class="modal-header clone-modal-header">
        <button type="button" data-dismiss="modal" data-placement="bottom" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">
            @lang('label.CLOSE')
        </button>
        <h3 class="modal-title text-center">
            @lang('label.ASSIGNED_SUB_SUB_EVENT_LIST')
        </h3>
    </div>
    <div class="modal-body">
        <div class="row margin-bottom-10">
            <div class="col-md-3">
                @lang('label.COURSE'): <strong>{!! $courseName->name ?? '' !!}</strong>
            </div>
            <div class="col-md-3">
                @lang('label.TERM'): <strong>{!! $termName->name ?? '' !!}</strong>
            </div>
            <div class="col-md-3">
                @lang('label.EVENT'): <strong>{!! $eventName->event_code ?? '' !!}</strong>
            </div>
            <div class="col-md-3">
                @lang('label.SUB_EVENT'): <strong>{!! $subEventName->event_code ?? '' !!}</strong>
            </div>
            <div class="col-md-3">
                @lang('label.SUB_SUB_EVENT'): <strong>{!! $subEventName->event_code ?? '' !!}</strong>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive max-height-500 webkit-scrollbar">
                    <table class="table table-bordered table-hover relation-view-2">
                        <thead>
                            <tr class="active">
                                <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
                                <th class="vcenter">@lang('label.SUB_SUB_SUB_EVENT')</th>
                            </tr>
                        </thead>

                        <tbody id="exerciseData">
                            @if(!$assignedSubSubSubEventArr->isEmpty())
                            @php $sl = 0 @endphp
                            @foreach($assignedSubSubSubEventArr as $assignedSubSubSubEvent)
                            <tr>
                                <td class="text-center vcenter">{!! ++$sl !!}</td>
                                <td class="vcenter">{{ $assignedSubSubSubEvent->event_code }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4" class="text-danger">
                                    @lang('label.NO_SUB_SUB_EVENT_FOUND_FOR_THIS_TERM')
                                </td>
                            </tr>
                            @endif      
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-outline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
    $(".tooltips").tooltip();
    $('.relation-view-2').tableHeadFixer();
});
</script>
