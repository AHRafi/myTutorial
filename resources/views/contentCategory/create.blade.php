@extends('layouts.default.master')
@section('data_count')	
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list"></i>@lang('label.CREATE_CONTENT_CATEGORY')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::open(array('group' => 'form', 'url' => 'contentCategory','class' => 'form-horizontal')) !!}

            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">

                        <div class="form-group">
                            <label class="control-label col-md-4" for="parentId">@lang('label.PARENT_CATEGORY') :</label>
                            <div class="col-md-8">
                                {!! Form::select('parent_id', array('0' => __('label.SELECT_CATEGORY_OPT')) + $parentArr, null, ['class' => 'form-control js-source-states', 'id' => 'parentId']) !!}
                                <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="name">@lang('label.NAME') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('name',null, ['id'=> 'name', 'class' => 'form-control','autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="shortDescription">@lang('label.SHORT_DESCRIPTION') :<span class="text-danger"> </span></label>
                            <div class="col-md-8">
                                {!! Form::text('short_description',null, ['id'=> 'shortDescription', 'class' => 'form-control','autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('short_description') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="relatedCompartment">@lang('label.RELATE_TO_THE_COMPARTMENTS') :</label>
                            <div class="col-md-8">
                                {!! Form::select('related_compartment[]', $compartmentList, null, ['id' => 'relatedCompartment', 'class' => 'form-control mt-multiselect btn btn-default', 'multiple']) !!}
                                <span class="text-danger">{{ $errors->first('related_compartment') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="order">@lang('label.ORDER') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('order', $orderList, null, ['class' => 'form-control js-source-states', 'id' => 'order']) !!} 
                                <span class="text-danger">{{ $errors->first('order') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="status">@lang('label.STATUS') :</label>
                            <div class="col-md-8">
                                {!! Form::select('status', ['1' => __('label.ACTIVE'), '2' => __('label.INACTIVE')], '1', ['class' => 'form-control', 'id' => 'status']) !!}
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
                        <a href="{{ URL::to('/contentCategory'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>	
    </div>
</div>

<script>

    $(function () {
        var authorityAllSelected = false;
        $('#relatedCompartment').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            nonSelectedText: "@lang('label.SELECT_COMPARTMENT')",
//        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                authorityAllSelected = true;
            },
            onChange: function () {
                authorityAllSelected = false;
            }
        });


    });


</script>
@stop