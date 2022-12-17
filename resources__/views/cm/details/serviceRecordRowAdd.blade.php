<?php
$srKey = uniqid();
?>
<tr id="serviceRecordRemove">
    <td class="vcenter text-center new-service-record-sl width-50"> </td>
    <td class="vcenter width-110">
        {!! Form::text('service_record['.$srKey.'][from]','', ['id'=> 'serviceRecordFrom'.$srKey
        , 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>
    <td class="vcenter width-110">
        {!! Form::text('service_record['.$srKey.'][to]','', ['id'=> 'serviceRecordTo'.$srKey
        , 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>
    <td class="vcenter width-130">
        {!! Form::text('service_record['.$srKey.'][unit_fmn_inst]', null, ['class' => 'form-control width-full', 'id' => 'unitFmnInst'.$srKey]) !!}
    </td>
    <td class="vcenter width-140">
        <div class="width-full">
            {!! Form::select('service_record['.$srKey.'][resp]', $respList,null, ['id'=> 'serviceResp'.$srKey, 'class' => 'form-control js-source-states width-full']) !!}
        </div>
    </td>
    <td class="vcenter width-210">
        {!! Form::text('service_record['.$srKey.'][appointment]', null, ['class' => 'form-control width-full', 'id' => 'svcAppointment'.$srKey]) !!}
    </td>
    <td class="vcenter text-center width-50">
        <a class="btn badge-red-intense service-record-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>