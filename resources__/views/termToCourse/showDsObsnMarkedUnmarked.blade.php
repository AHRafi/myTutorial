<div class="modal-content" >
    <div class="modal-header clone-modal-header" >
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.MARKED_SYN_SUMMARY', ['m' => ($request->mark_id == 1) ? 'M' : 'Unm'])
        </h3>
    </div>

    <div class="modal-body">

        <div class="row">
            <div class="col-md-4">
                @lang('label.TERM'):&nbsp;<strong>{{!empty($termToWeekInfo->term_name)?$termToWeekInfo->term_name:''}}</strong>
            </div>
            <div class="col-md-4">
                @lang('label.WEEK'):&nbsp;<strong>{{!empty($termToWeekInfo->week_name)?$termToWeekInfo->week_name:''}}</strong>
            </div>
        </div>

        <div class="row margin-top-10">
            <div class="col-md-12 table-responsive">
                <div class="webkit-scrollbar max-height-500">
                    <table class="table table-bordered table-head-fixer-color">
                        <thead>
                            <tr>
                                <th class="vcenter text-center">@lang('label.SERIAL')</th>
                                <th class="vcenter">@lang('label.SYN')</th>
                                <th class="vcenter">@lang('label.SYN_DS')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($courseSynArr))
                            <?php
                            $sl = 0;
                            ?>
                            @foreach($courseSynArr as $synId => $info)
                            <tr>
                                <td class="vcenter text-center">{{++$sl}}</td>
                                <td class="vcenter">{{!empty($info['syn_name'])?$info['syn_name']:''}}</td>
                                <td class="vcenter">{{!empty($info['syn_ds_name'])?$info['syn_ds_name']:''}}</td>

                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td class="vcenter" colspan="4"> @lang('label.NO_DATA_FOUND')
                            </tr>
                            @endif
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips" title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>
<script type="text/javascript">
    $(".table-head-fixer-color").tableHeadFixer();
</script>
<!-- END:: Contact Person Information-->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

