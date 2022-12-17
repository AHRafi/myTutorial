@if (!empty($target))
    <div class="form-group">
        <label class="control-label col-md-4" for="term">@lang('label.TERM') :</label>
        <div class="col-md-8">
            <div class="control-label pull-left"> <strong> {{$target->term_name}} </strong>
                {!! Form::hidden('term_id', $target->term_id,['id'=>'termIdMarkingTable']) !!}
                {!! Form::hidden('course_id', $target->course_id,['id'=>'courseIdMarkingTable']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-4" for="eventIdMarking">@lang('label.EVENT') :<span class="text-danger"> *</span></label>
        <div class="col-md-4">
            <div id="showBatch" >
                {!! Form::select('event_id', $eventList, null,  ['class' => 'form-control js-source-states', 'id' => 'eventIdMarking']) !!}
                <span class="text-danger">{{ $errors->first('event_id') }}</span>
            </div>
        </div>
    </div>
    <!--get Marking table-->
    <div id="showTermSynMarkingTable"></div>

@else
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissable">
        <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_MODULE_FOUND')</p>
    </div>
</div>
@endif

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>