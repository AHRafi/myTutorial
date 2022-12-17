<div class="form-group" id="showCommissioningCourse">
    <label class="control-label col-md-5" for="commissioningCourseId">@lang('label.COMMISSIONING_COURSE') :<span class="text-danger hide-mandatory-sign"> *</span></label>
    <div class="col-md-7">
        {!! Form::select('commissioning_course_id', $commissioningCourseList, null, ['class' => 'form-control js-source-states', 'id' => 'commissioningCourseId', 'autocomplete' => 'off']) !!}
        <span class="text-danger">{{ $errors->first('commissioning_course_id') }}</span>
    </div>
</div>