<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center"> @lang('label.SET_TO_CLONE_THIS_SETUP')</h3>
    </div>

    <div class="modal-body">
        {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitCloneForm')) !!}
        <div class="row margin-bottom-75">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="selectedCourseId">@lang('label.COURSE_TO_CLONE'):</label>
                        <div class="col-md-8">
                            <div class="control-label pull-left"> <strong> {{$toClonecouseInfo->name}} </strong></div>
                            {!! Form::hidden('selected_course_id',$request->course_id,['id' => 'selectedCourseId']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="relatedCourseId">@lang('label.COURSE'):</label>
                        <div class="col-md-8">
                            {!! Form::select('related_course_id',$courseList,null, ['class' => 'form-control js-source-states', 'id' => 'relatedCourseId']) !!}
                            <span class="text-danger">{{ $errors->first('related_course_id') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 margin-top-10" id="showRelatedTraits"></div>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="modal-footer">
        <button class="btn green" type="button" id="cloneSubmit">
            <i class="fa fa-check"></i> @lang('label.CLONE_SENTENCES')
        </button>
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>