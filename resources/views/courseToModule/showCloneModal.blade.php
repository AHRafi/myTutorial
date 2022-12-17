<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.CLONE_FROM_PREVIOUS_COURSE')
        </h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                            <div class="col-md-7">
                                <div class="control-label pull-left"> <strong> {{$activeCourse->name}} </strong></div>
                                {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="control-label col-md-5" for="preCourseId">@lang('label.PREVIOUS_COURSE'):</label>
                            <div class="col-md-7 show-term">
                                {!! Form::select('pre_course_id', $previousCourseList , null,  ['class' => 'form-control js-source-states', 'id' => 'preCourseId']) !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div id="showPreModuleTable" class="mt-15"></div>
    </div>


    <div class="modal-footer">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(document).on("change", "#preCourseId", function () {
            var preCourseId = $("#preCourseId").val();
            var activeCourseId = $("#courseId").val();

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('courseToModule/showPreModuleTable')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pre_course_id: preCourseId,
                    active_course_id: activeCourseId,
                },
                beforeSend: function () {
                    $('#showPreModuleTable').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showPreModuleTable').html(res.html);
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });


    });
</script>
