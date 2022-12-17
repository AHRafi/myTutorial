<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.PROFILE_COMPLITION')
        </h3>
    </div>

    <div class="modal-body">
        @if(!empty($target))

        <div class="portlet-body" style="padding-bottom: 0px;  padding-left: 8px; padding-right: 8px">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td class="vcenter fit bold info" width="15%">@lang('label.LESSON')</td>
                            <td>{{ $targetInfo->title }}</td>
                            <td class="vcenter fit bold info" width="15%">@lang('label.DATE_OF_EVAL')</td>
                            <td> {{ Helper::formatDate($targetInfo->eval_date) }}</td>
                        </tr>
                        <tr>
                            <td class="vcenter fit bold info" width="15%">@lang('label.DEADLINE_OF_EVAL')</td>
                            <td>{{ Helper::formatDate($targetInfo->eval_deadline) }}</td>
                        </tr>

                    </tbody></table>
            </div>
            <div class="col-md-offset-2">

                {{-- <div class="col-md-2">

                    @if($target->related_objective )
                    <a class="label label-sm label-success" href="{{ URL::to('lesson/' . $target->id . '/manageLesson#tab_5_1') }}">
                        @lang('label.OBJECTIVE')
                    </a>
                    <span class="label label-sm label-success">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </span>
                    @else
                    <a class="label label-sm label-danger" href="{{ URL::to('lesson/' . $target->id . '/manageLesson#tab_5_1') }}">
                        @lang('label.OBJECTIVE')
                    </a>
                    <span class="label label-sm label-danger">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </span>
                    @endif
                </div>
                <div class="col-md-3">

                    @if($target->related_consideration )
                    <a class="label label-sm label-success" href="{{ URL::to('lesson/' . $target->id . '/manageLesson#tab_5_2') }}">
                        @lang('label.CONSIDERATIONS')
                    </a>
                    <span class="label label-sm label-success">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </span>
                    @else
                    <a class="label label-sm label-danger" href="{{ URL::to('lesson/' . $target->id . '/manageLesson#tab_5_2') }}">
                        @lang('label.CONSIDERATIONS')
                    </a>
                    <span class="label label-sm label-danger">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </span>
                    @endif
                </div>
                <div class="col-md-2">

                    @if($target->related_grading )
                    <a class="label label-sm label-success" href="{{ URL::to('lesson/' . $target->id . '/manageLesson#tab_5_3') }}">
                        @lang('label.GRADING')
                    </a>
                    <span class="label label-sm label-success">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </span>
                    @else
                    <a class="label label-sm label-danger" href="{{ URL::to('lesson/' . $target->id . '/manageLesson#tab_5_3') }}">
                        @lang('label.GRADING')
                    </a>
                    <span class="label label-sm label-danger">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </span>
                    @endif
                </div>
                <div class="col-md-2">

                    @if($target->related_comment )
                    <a class="label label-sm label-success" href="{{ URL::to('lesson/' . $target->id . '/manageLesson#tab_5_4') }}">
                        @lang('label.COMMENT')
                    </a>
                    <span class="label label-sm label-success">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </span>
                    @else
                    <a class="label label-sm label-danger" href="{{ URL::to('lesson/' . $target->id . '/manageLesson#tab_5_4') }}">
                        @lang('label.COMMENT')
                    </a>
                    <span class="label label-sm label-danger">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </span>
                    @endif
                </div> --}}


            </div>

        </div>


        @endif
    </div>

    <div class="modal-footer">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

