@if(!empty($assessmentActDeact))
<div class="col-md-12 text-center">
    <div class="form-group">
        <button type="submit" class="btn btn-md green-seagreen btn-outline filter-btn" id="eventCombineReportId" value="Show Filter Info" data-id="1">
            <i class="fa fa-pencil"></i> @lang('label.PROCEED')
        </button>
    </div>
</div>
@else
<div class="col-md-12 margin-top-10">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.COURSE_REPORT_GENERATION_HAS_NOT_BEEN_ACTIVATED_YET') !!}</strong></p>
    </div>
</div>
@endif