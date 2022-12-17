<label class="control-label col-md-6" for="actDeactStat_4_0_0_0_0">@lang('label.COURSE_REPORT_GENERATION') :</label>
<div class="col-md-6">
    {!! Form::checkbox('act_deact_stat[0][0][0][0]'
    , 1, !empty($assessmentActDeactArr[4][0][0][0][0]) ? 1:0
    , ['id'=> 'actDeactStat_4_0_0_0_0'
    , 'class' => 'make-switch act-deact-switch','data-on-text'=> __('label.ACTIVATE')
    ,'data-off-text'=>__('label.DEACTIVATE'), 'criteria' => '4']) !!}
</div>