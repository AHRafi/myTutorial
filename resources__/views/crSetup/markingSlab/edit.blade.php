@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EDIT_MARKING_SLAB')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::model($target, ['route' => array('crMarkingSlab.update', $target->id), 'method' => 'PATCH', 'files'=> true, 'class' => 'form-horizontal'] ) !!}
            {!! Form::hidden('filter', Helper::queryPageStr($qpArr)) !!}
            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">

                        <div class="form-group">
                            <label class="control-label col-md-4" for="title">@lang('label.TITLE') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('title',  null, ['id'=> 'title', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('title') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="type">@lang('label.TYPE') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('type', $slabTypeList, null, ['class' => 'form-control js-source-states', 'id' => 'type']) !!}
                                <span class="text-danger">{{ $errors->first('type') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="startRange">@lang('label.START_RANGE') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('start_range',  $target->start_range ?? null, ['id'=> 'startRange', 'class' => 'form-control start-range range', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('start_range') }}</span>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="control-label col-md-4" for="endRange">@lang('label.END_RANGE') :<span class="text-danger"> * </span><span class="end-less-than"></span></label>
                            <div class="col-md-8">
                                {!! Form::text('end_range',  $target->end_range ?? null, ['id'=> 'endRange', 'class' => 'form-control end-range range', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('end_range') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class = "control-label col-md-4" for="bPlusNAbove">@lang('label.B_PLUS_N_ABOVE')&nbsp;:</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('b_plus_n_above',1,null,['id' => 'bPlusNAbove', 'class'=> 'md-check']) !!}

                                    <label for="bPlusNAbove">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_TO_MARK_THE_SLAB_AS_B_PLUS_N_ABOVE')</span>
                                </div>
                            </div>
                        </div>

                        <div  id="order">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="order">@lang('label.ORDER') :<span class="text-danger"> *</span></label>
                                <div class="col-md-8">
                                    {!! Form::select('order', $orderList, null, ['class' => 'form-control js-source-states', 'id' => 'order']) !!} 
                                    <span class="text-danger">{{ $errors->first('order') }}</span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-4" for="status">@lang('label.STATUS') :</label>
                            <div class="col-md-8">
                                {!! Form::select('status', ['1' => __('label.ACTIVE'), '2' => __('label.INACTIVE')], null, ['class' => 'form-control', 'id' => 'status']) !!}
                                <span class="text-danger">{{ $errors->first('status') }}</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn btn-circle green" type="submit">
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href="{{ URL::to('/crMarkingSlab'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>	
    </div>
    <!-- END BORDERED TABLE PORTLET-->
</div>

<script type="text/javascript">
    $(function () {
        
        if ($("#type").val() == '1') {
            $("span.end-less-than").html('(<i class="fa fa-angle-left bold"></i>)');
        } else if ($("#type").val() == '2') {
            $("span.end-less-than").html('');
        }
        $("#type").on("change", function () {
            var type = $(this).val();
            if (type == '1') {
                $("span.end-less-than").html('(<i class="fa fa-angle-left bold"></i>)');
            } else if (type == '2') {
                $("span.end-less-than").html('');
            }
        });

    });
</script>

@stop