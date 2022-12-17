<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.CLONE_EVENT')
        </h3>
    </div>

    <div class="modal-body">
        {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitCloneEventForm')) !!}
        {!! Form::hidden('course_id', $request->course_id) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-offset-2 col-md-7">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="prevCourseId">@lang('label.PREVIOUS_COURSE') :</label>
                                <div class="col-md-8">
                                    {!! Form::select('prev_course_id', $courseList, null, ['class' => 'form-control js-source-states ','id'=>'prevCourseId']) !!}
                                    <span class="text-danger">{{ $errors->first('prev_course_id') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="showPrevCourseEvent">
            
        </div>
        {!! Form::close() !!}

    </div>

    <div class="modal-footer">
        
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

<script type="text/javascript">
    $(".table-head-fixer-color").tableHeadFixer();
</script>


