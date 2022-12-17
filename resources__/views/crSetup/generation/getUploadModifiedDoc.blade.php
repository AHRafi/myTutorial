<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.UPLOAD_MODIFIED_FILE')
        </h3>
    </div>

    <div class="modal-body">
        {!! Form::open(array('group' => 'form', 'url' => '','class' => 'form-horizontal','id' => 'saveModifiedDocForm')) !!}
        {!! Form::hidden('course_id', $request->course_id) !!}
        {!! Form::hidden('course_name', $course->name) !!}
        {!! Form::hidden('cm_id', $request->cm_id) !!}
        {!! Form::hidden('cm_pn', $cm->personal_no) !!}
        <div class="form-group">
            <label class="control-label col-md-3 text-right" for="reportFile">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
            <div class="col-md-9">
                <div class="control-label pull-left"> <strong> {{$course->name}} </strong></div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 text-right" for="reportFile">@lang('label.CM') :<span class="text-danger"> *</span></label>
            <div class="col-md-9">
                <div class="control-label pull-left"> <strong> {{Common::getFurnishedCmName($cm->cm_name)}} </strong></div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 text-right" for="reportFile">@lang('label.MODIFIED_FILE') :<span class="text-danger"> *</span></label>
            <div class="col-md-9">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn green btn-file">
                        <span class="fileinput-new">@lang('label.SELECT_FILE')</span>
                        <span class="fileinput-exists">@lang('label.CHANGE')</span>
                        {!! Form::file('report_file',null,['id'=> 'reportFile', 'disabled']) !!}
                    </span>
                    <span class="fileinput-filename"></span>&nbsp;
                    <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>
                </div>
                <div class="clearfix">
                    <span class="label label-danger">@lang('label.NOTE')</span> @lang('label.MODIFIED_DOC_FILE_FORMAT_SIZE')
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="modal-footer">

        <button type="button" class="btn green save-modified-doc">
            <i class="fa fa-check"></i> @lang('label.UPLOAD')
        </button>
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>

</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>


