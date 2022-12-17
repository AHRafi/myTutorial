<div class="modal-content">
    <div class="modal-header clone-modal-header">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.DS_RMKS_PREVIEW')
        </h3>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label col-md-3">@lang('label.TRAINING_YEAR') :</label>
                    <div class="col-md-9">
                        <div class="pull-left"> 
                            <strong> {{!empty($previewData['trainingYear']) ? $previewData['trainingYear'] : __('label.N_A')}} </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label col-md-3">@lang('label.COURSE') :</label>
                    <div class="col-md-9">
                        <div class="control-label pull-left"> 
                            <strong> {{!empty($previewData['course']) ? $previewData['course'] : __('label.N_A')}} </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label col-md-3">@lang('label.TERM') :</label>
                    <div class="col-md-9">
                        <div class="control-label pull-left"> 
                            <strong> {{!empty($previewData['term']) ? $previewData['term'] : __('label.N_A')}} </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label col-md-3">@lang('label.CM') :</label>
                    <div class="col-md-9">
                        <div class="control-label pull-left"> 
                            <strong> {{!empty($previewData['cm']) ? $previewData['cm'] : __('label.N_A')}} </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label col-md-3">@lang('label.EVENT') :</label>
                    <div class="col-md-9">
                        <div class="control-label pull-left"> 
                            <strong> {{!empty($previewData['event']) ? $previewData['event'] : __('label.N_A')}} </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label col-md-3">@lang('label.DATE') :</label>
                    <div class="col-md-9">
                        <div class="control-label pull-left"> 
                            <strong> {{!empty($previewData['date']) ? $previewData['date'] : __('label.N_A')}} </strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label col-md-3">@lang('label.REMARKS') :</label>
                    <div class="col-md-9">
                        <div class="control-label pull-left"> 
                            <strong> {{!empty($previewData['remarks']) ? $previewData['remarks'] : __('label.N_A')}} </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-inline green" type="button" id="submitRmksButton">
                    <i class="fa fa-check"></i> @lang('label.CONFIRM_SAVE')
                </button> 
                <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script>
$(function () {

});
</script>