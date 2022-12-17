<div class="modal-content">
    <div class="modal-header clone-modal-header">
        <div class="col-md-7 text-right">
            <h4 class="modal-title">{{ $target->name ?? '' }}</h4>
        </div>
        <div class="col-md-5">
            <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips"
                title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>

        </div>
    </div>
    <div class="modal-body">

        <div class="portlet-body">
            {!! Form::open(['group' => 'form', 'url' => '#', 'class' => 'form-horizontal', 'id' => 'courseCloneForm']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong>
                                            {{ $activeTrainingYear->name ?? '' }}</strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYear->id ?? null, ['id' => 'trainingYearId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="modalCourseId">@lang('label.COURSE'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{ $activeCourse->name ?? null }}
                                        </strong></div>
                                    {!! Form::hidden('modal_course_id', $activeCourse->id ?? null, ['id' => 'modalCourseId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="moduleId">@lang('label.CLONE_FROM'):</label>
                                <div class="col-md-7">
                                    {!! Form::select('previous_course_id', $courseArr, null, [
                                        'class' => 'form-control js-source-states',
                                        'id' => 'previousCourseId',
                                    ]) !!}
                                </div>
                            </div>

                        </div>

                    </div>
                    <div id="showDsList"></div>
                </div>
                <div id="courseWiseModuleTable">

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn green" type="button" id="cloneSubmitButton">
            <i class="fa fa-check"></i> @lang('label.CLONE')</button>
        <button type="button" data-dismiss="modal" data-placement="top" class="btn dark btn-outline tooltips"
            title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

<script src="{{ asset('public/js/custom.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".tooltips").tooltip();
        $("#cloneSubmitButton").hide();
        $('.js-source-states').select2();
        $("#hasBankAccountSwitch").bootstrapSwitch({
            offColor: 'danger'

        });
    });
</script>
