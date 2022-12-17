@if(!$cmDataArr->isEmpty())
<div class="table-responsive max-height-250 webkit-scrollbar">
    <table class="table table-bordered table-hover" id="dataTable">
        <thead>
            <tr>
                <th class="vcenter text-center">@lang('label.SL')</th>
                <th class="vcenter text-center">@lang('label.PHOTO')</th>
                <th class="vcenter">@lang('label.NAME')</th>
            </tr>
        </thead>
        <tbody id="selected-cm-body">
            <?php $cmSl = 0; ?>
            @foreach($cmDataArr as $target)

            <tr>
                {{ Form::hidden('selected_cm['.$target->id.']', $target->id, array('id' =>  $target->id, 'class' => 'selected-cm')) }}
                <td class="vcenter text-center initial-serial-cm">{!! ++$cmSl !!}</td>
                <td class="text-center vcenter" width="22px">
                    <?php if (!empty($target->photo && File::exists('public/uploads/cm/' . $target->photo))) { ?>
                        <img width="22" height="23" src="{{URL::to('/')}}/public/uploads/cm/{{$target->photo}}" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                    <?php } else { ?>
                        <img width="22" height="23" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                    <?php } ?>
                </td>
                <td class="vcenter">{!!Common::getFurnishedCmName($target->cm_name)!!}</td>
                
            </tr>
            @endforeach

        </tbody>
    </table>

</div>

<div class="alert alert-success alert-dismissable margin-top-10">
    <p><strong> {!! __('label.TOTAL_NO_OF_SELECTED_CM') !!} : &nbsp;<span class="selected-cm-no">{!! sizeof($cmDataArr) !!}</span></strong></p>
</div> 
@else
<div class="alert alert-danger alert-dismissable">
    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_SELECTED_FOR_THIS_MA_GROUP_YET') !!}</strong></p>
</div>
@endif
