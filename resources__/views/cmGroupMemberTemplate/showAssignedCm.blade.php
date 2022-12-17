<div class="modal-content">
    <div class="modal-header clone-modal-header">
        <button type="button" data-dismiss="modal" data-placement="bottom" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">
            @lang('label.CLOSE')
        </button>
        <h3 class="modal-title text-center">
            @lang('label.ASSIGNED_CM_LIST')
        </h3>
    </div>
    <div class="modal-body">
        <div class="row margin-bottom-10">
            <div class="col-md-4">
                @lang('label.COURSE'): <strong>{!! $courseName->name ?? '' !!}</strong>
            </div>
            <div class="col-md-4">
                @lang('label.TERM'): <strong>{!! $termName->name ?? '' !!}</strong>
            </div>
            <div class="col-md-4">
                @lang('label.CM_GROUP'): <strong>{!! $cmGroupName->name ?? '' !!}</strong>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive max-height-500 webkit-scrollbar">
                    <table class="table table-bordered table-hover relation-view-2">
                        <thead>
                            <tr class="active">
                                <th class="text-center vcenter" rowspan="2">@lang('label.SL_NO')</th>
                                <th class="text-center vcenter" rowspan="2">@lang('label.PHOTO')</th>
                                <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                <th class="vcenter">@lang('label.RANK')</th>
                                <th class="vcenter">@lang('label.FULL_NAME')</th>
                                <th class="vcenter">@lang('label.WING')</th>
                            </tr>
                        </thead>
                        <tbody id="exerciseData">
                            @if(!$assignedCmArr->isEmpty())
                            @php $sl = 0 @endphp
                            @foreach($assignedCmArr as $assignedCm)
                            <tr>
                                <td class="text-center vcenter">{!! ++$sl !!}</td>
                                <td class="text-center vcenter" width="50px">
                                    <?php if (!empty($assignedCm->photo && File::exists('public/uploads/cm/' . $assignedCm->photo))) { ?>
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$assignedCm->photo}}" alt="{{ Common::getFurnishedCmName($assignedCm->full_name)}}"/>
                                    <?php } else { ?>
                                        <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($assignedCm->full_name)}}"/>
                                    <?php } ?>
                                </td>
                                <td class="vcenter">{{ $assignedCm->personal_no }}</td>
                                <td class="vcenter">{{ $assignedCm->rank_code }}</td>
                                <td class="vcenter">{!! Common::getFurnishedCmName($assignedCm->full_name) !!}</td>
                                <td class="vcenter">{{ $assignedCm->wing_name }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="6" class="text-danger">
                                    @lang('label.NO_CM_FOUND_FOR_THIS_CM_GROUP')
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
