@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.EDIT_TRAIT')
            </div>
        </div>
        <div class="portlet-body form">
            {!! Form::model($target, ['route' => array('crTrait.update', $target->id), 'method' => 'PATCH', 'files'=> true, 'class' => 'form-horizontal'] ) !!}
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
                            <label class="control-label col-md-4" for="paraId">@lang('label.PARA') :<span class="text-danger"> *</span></label>
                            <div class="col-md-8">
                                {!! Form::select('para_id', $paraList, null, ['class' => 'form-control js-source-states', 'id' => 'paraId']) !!}
                                <span class="text-danger">{{ $errors->first('para_id') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class = "control-label col-md-4" for="forGradingSentence">@lang('label.GRADING_SENTENCE')&nbsp;:</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('for_grading_sentence',1,null,['id' => 'forGradingSentence', 'class'=> 'md-check']) !!}

                                    <label for="forGradingSentence">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_TO_MARK_AS_GRADING_SENTENCE')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class = "control-label col-md-4" for="forRecomndSentence">@lang('label.RECOMMENDATION_SENTENCE')&nbsp;:</label>
                            <div class = "col-md-8 margin-top-8">
                                <div class="md-checkbox">
                                    {!! Form::checkbox('for_recomnd_sentence',1,null,['id' => 'forRecomndSentence', 'class'=> 'md-check']) !!}

                                    <label for="forRecomndSentence">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="">@lang('label.PUT_TICK_TO_MARK_AS_RECOMMENDATION_SENTENCE')</span>
                                </div>
                            </div>
                        </div>

                        <div id="order">
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
                        <a href="{{ URL::to('/crTrait'.Helper::queryPageStr($qpArr)) }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
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
        changeWithPara("#paraId");
        $(document).on("change", "#paraId", function () {
            changeWithPara(this);
        });
        $(document).on("click", "#forGradingSentence", function () {
            if (this.checked) {
                $("#forRecomndSentence").prop('checked', false);
            }
        });
        $(document).on("click", "#forRecomndSentence", function () {
            if (this.checked) {
                $("#forGradingSentence").prop('checked', false);
            }
        });

        function changeWithPara(paraId) {
            var paraId = $(paraId).val();
            if (paraId != '3') {
                $("#forRecomndSentence").prop('checked', false);
                $("#forRecomndSentence").prop('disabled', true);
            } else {
                $("#forRecomndSentence").prop('disabled', false);
            }
        }
    });
</script>

@stop