@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-calendar"></i>@lang('label.EDIT_COMMISSIONING_COURSE')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::model($target, ['route' => array('commissioningCourse.update', $target->id), 'method' => 'PATCH', 'class'
            => 'form-horizontal'] ) !!}
            {!! Form::hidden('filter', Helper::queryPageStr($qpArr)) !!}
            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="name">@lang('label.NAME') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('name', null, ['id'=> 'name', 'class' =>
                                'form-control','autocomplete'=>'off']) !!}
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="shortInfo">@lang('label.SHORT_INFO') :</label>
                            <div class="col-md-8">
                                {!! Form::text('short_info', null, ['id'=> 'shortInfo', 'class' =>
                                'form-control','autocomplete'=>'off']) !!}
                                <div>
                                    <span class="text-danger">{{ $errors->first('short_info') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="wingId">@lang('label.WING') :<span
                                    class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('wing_id', $wingList, null, ['class' => 'form-control js-source-states', 'wingId']) !!}
                                <span class="text-danger">{{ $errors->first('wing_id') }}</span>
                            </div>
                        </div>
                        <div class = "form-group">
                            <label class = "control-label col-md-4" for="commisioningDate">@lang('label.COMMISSIONING_DATE') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('commissioning_date', !empty($target->commissioning_date) ? Helper::formatDate($target->commissioning_date) : null, ['id'=> 'commisioningDate', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="commisioningDate">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('commisioning_date') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" for="status">@lang('label.STATUS') :</label>
                            <div class="col-md-8">
                                {!! Form::select('status', ['1' => __('label.ACTIVE'), '0' => __('label.INACTIVE')],
                                null, ['class' => 'form-control', 'id' => 'status']) !!}
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
                        <a href="{{ URL::to('/commissioningCourse'.Helper::queryPageStr($qpArr)) }}"
                            class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        //function for no of weeks
        $(document).on('change', '.batch-date', function () {
            var initialDate = new Date($('#initialDate').val());
            var terminationDate = new Date($('#terminationDate').val());
            if (terminationDate < initialDate) {
                swal('Termination Date must be greater than InitialDate ');
                $('#terminationDate').val('');
                $('noOfWeeks').val('');
                return false;
            }

            var weeks = Math.ceil((terminationDate - initialDate) / (24 * 3600 * 1000 * 7));

            if (isNaN(weeks)) {
                var weeks = '';
            }
            $("#noOfWeeks").val(weeks);
        });



    });
</script>













@stop