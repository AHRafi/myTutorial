@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EDIT_GS')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::model($target, ['route' => array('gs.update', $target->id), 'method' => 'PATCH', 'files'=> true, 'class' => 'form-horizontal'] ) !!}
            {!! Form::hidden('filter', Helper::queryPageStr($qpArr)) !!}
            {{csrf_field()}}
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">

                        <div class="form-group">
                            <label class="control-label col-md-4" for="name">@lang('label.NAME') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::text('name', Request::old('name'), ['id'=> 'name', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="unit">@lang('label.UNIT_ORGANIZATION') :</label>
                            <div class="col-md-8">
                                {!! Form::text('unit', Request::old('unit'), ['id'=> 'unit', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
                                <span class="text-danger">{{ $errors->first('unit') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">@lang('label.DATE_OF_FIRST_SESSION_CONDUCT_TO_AFWC') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                <div class="input-group date datepicker2">
                                    {!! Form::text('conduct_date', !empty($target->conduct_date) ? Helper::formatDate($target->conduct_date) : null, ['class' => 'form-control', 'id' => 'conductDate', 'placeholder' => 'DD/MM/YYYY', 'readonly' => '']) !!}
                                    <span class="input-group-btn">
                                        <button class="btn default reset-date" type="button" remove="conductDate">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="text-danger">{{ $errors->first('conduct_date') }}</span>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="description">@lang('label.SUMMARY_OF_EXPERTISE'):</label>
                            <div class="col-md-8">
                                {!! Form::textarea('summary_expertise', Request::old('summary_expertise'), ['id' => 'summaryExpertise', 'class' => 'form-control full-name-text-area', 'cols' => '20', 'rows' => '8']) !!}
                                <span class="text-danger">{{ $errors->first('summary_expertise') }}</span>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="number">@lang('label.CONTACT_NUMBER') :</label>
                            <div class="col-md-8">
                                {!! Form::text('number', Request::old('number'), ['id'=> 'number', 'class' => 'form-control integer-only', 'autocomplete' => 'off']) !!}
                                <span class="text-danger">{{ $errors->first('number') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="altNumber">@lang('label.ALTERNATIVE_CONTACT_NUMBER') :</label>
                            <div class="col-md-8">
                                {!! Form::text('alt_number', Request::old('alt_number'), ['id'=> 'altNumber', 'class' => 'form-control integer-only', 'autocomplete' => 'off']) !!}
                                <span class="text-danger">{{ $errors->first('alt_number') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="email">@lang('label.EMAIL') :</label>
                            <div class="col-md-8">
                                {!! Form::text('email', Request::old('email'), ['id'=> 'email', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="shortDescription">@lang('label.PRESENT_ADDRESS') :<span class="text-danger"></span></label>
                            <div class="col-md-8">
                                {!! Form::textarea('present_address',  Request::old('present_address'), ['id'=> 'presentAddress', 'class' => 'form-control', 'autocomplete' => 'off', 'size' => '10x1']) !!}
                                <span class="text-danger">{{ $errors->first('present_address') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4" for="shortDescription">@lang('label.PERMANENT_ADDRESS') :<span class="text-danger"></span></label>
                            <div class="col-md-8">
                                {!! Form::textarea('permanent_address',  Request::old('permanent_address'), ['id'=> 'permanentAddress', 'class' => 'form-control', 'autocomplete' => 'off', 'size' => '10x1']) !!}
                                <span class="text-danger">{{ $errors->first('permanent_address') }}</span>
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
                    <div class="col-md-4">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                @if(!empty($target->photo))
                                <img src="{{URL::to('/')}}/public/uploads/gs/{{$target->photo}}" alt="{{ Common::getFurnishedCmName($target->name)}}"/>
                                @endif
                            </div>
                            <div>
                                <span class="btn green-seagreen btn-outline btn-file">
                                    <span class="fileinput-new"> Select image </span>
                                    <span class="fileinput-exists"> Change </span>
                                    {!! Form::file('photo', null, ['id'=> 'photo']) !!}
                                </span>
                                @if(!empty($target->photo))
                                <a href="javascript:;" class="btn green-seagreen" data-dismiss="fileinput"> Remove </a>
                                @else
                                <a href="javascript:;" class="btn green-seagreen fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                @endif
                            </div>
                        </div>
                        <div class="clearfix margin-top-10">
                            <span class="label label-danger">@lang('label.NOTE')</span> @lang('label.USER_IMAGE_FOR_IMAGE_DESCRIPTION')
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
                        <a href="{{ URL::to('/gs'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
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
    $(document).ready(function () {

        $('#summaryExpertise').summernote({
            placeholder: 'Summary Of Expertise',
            tabsize: 2,
            height: 100,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
            ]
        });

    });
</script>
@stop
