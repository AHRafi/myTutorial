<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.REQUEST_FOR_UNLOCK')
        </h3>
    </div>
    
    <div class="modal-body">
        <div class="form-group">
            <label class="control-label col-md-3 text-right" for="unlockMsgId">@lang('label.UNLOCK_MESSAGE') :<span class="text-danger"> *</span></label>
            <div class="col-md-9">
                {!! Form::textarea('unlock_message', null, ['id'=> 'unlockMsgId', 'class' => 'form-control','cols'=>'20','rows' => '3']) !!} 
            </div>
        </div>

    </div>
    <div class="modal-footer">
        
        
        <button type="button" class="btn green save-request-for-unlock">
            <i class="fa fa-check"></i> @lang('label.SUBMIT')
        </button>
       
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
    
</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>


