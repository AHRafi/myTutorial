<div class="col-md-4">
    @if($maProcess == '1')
    <div class="form-group">
        <label class="control-label col-md-4" for="synId">@lang('label.SYN') :<span class="text-danger"> *</span></label>
        <div class="col-md-8">
            {!! Form::select('syn_id', $synList, Request::get('syn_id'), ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
            <span class="text-danger">{{ $errors->first('syn_id') }}</span>
        </div>
    </div>
    @elseif($maProcess == '2')
    <div class="form-group">
        <label class="control-label col-md-4" for="subSynId">@lang('label.SUB_SYN') :<span class="text-danger"> *</span></label>
        <div class="col-md-8">
            {!! Form::select('sub_syn_id', $subSynList, Request::get('sub_syn_id'), ['class' => 'form-control js-source-states', 'id' => 'subSynId']) !!}
            <span class="text-danger">{{ $errors->first('sub_syn_id') }}</span>
        </div>
    </div>
    @elseif($maProcess == '3')
    <div class="form-group">
        <label class="control-label col-md-4" for="eventId">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
        <div class="col-md-8">
            {!! Form::select('event_id', $eventList, Request::get('event_id'), ['class' => 'form-control js-source-states', 'id' => 'eventId']) !!}
            <span class="text-danger">{{ $errors->first('event_id') }}</span>
        </div>
    </div>
    @else
    <div class="form-group">
        <label class="control-label col-md-4" for="synId">@lang('label.SYN') :<span class="text-danger"> *</span></label>
        <div class="col-md-8">
            {!! Form::select('syn_id', $synList, Request::get('syn_id'), ['class' => 'form-control js-source-states', 'id' => 'synId']) !!}
            <span class="text-danger">{{ $errors->first('syn_id') }}</span>
        </div>
    </div>
    @endif
    {!! Form::hidden('ma_process', !empty($maProcess) ? $maProcess : 0, ['id' => 'maProcess']) !!}
</div>