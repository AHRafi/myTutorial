@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-clipboard"></i>@lang('label.EDIT_NOTICE')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::model($target, ['route' => array('noticeBoard.update', $target->id), 'method' => 'PATCH', 'files'=> true, 'class' => 'form-horizontal'] ) !!}
            {!! Form::hidden('filter', Helper::queryPageStr($qpArr)) !!}
            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-7">
                        <div class = "form-group">
                            <label class = "control-label col-md-4" for="date">@lang('label.EXPIRE_DATE') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('end_date', !empty($target->end_date) ? Helper::formatDate($target->end_date): null, ['id'=> 'date', 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!} 
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="date">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('end_date') }}</span>
                            </div>         
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="headline">@lang('label.HEADLINE') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('headline',  null, ['id'=> 'headline', 'class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                <span class="text-danger">{{ $errors->first('headline') }}</span>
                            </div>
                        </div>


                        <div class = "form-group">
                            <label class = "control-label col-md-4" for="description">@lang('label.DESCRIPTION') :<span class="text-danger hide-mandatory-sign"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::textarea('description', null, ['id'=> 'description', 'class' => 'form-control','cols'=>'20','rows' => '3']) !!}
                                <span class="text-danger">{{ $errors->first('description') }}</span>
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
                        <a href="{{ URL::to('/noticeBoard'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>	
    </div>
    <!-- END BORDERED TABLE PORTLET-->
</div>
<script src="{{asset('public/assets/global/plugins/summer-note/summernote.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
        $('#description').summernote({
            height: 200,
        });
    });
</script>

@stop