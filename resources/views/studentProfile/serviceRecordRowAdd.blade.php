<?php
$srKey = uniqid();
?>
<tr id="serviceRecordRemove">
    <td class="vcenter text-center new-service-record-sl width-50"> </td>
    <td class="vcenter width-300"> 
        
        {!! Form::select('service_record['.$srKey.'][unit]', $unitList, null,  ['class' => 'form-control js-source-states width-inherit', 'id' => 'serviceRecord['.$srKey.'][unit]']) !!}
    </td>
    <td class="vcenter width-300">
       {!! Form::select('service_record['.$srKey.'][appointment]', $appointmentList, null,  ['class' => 'form-control js-source-states width-inherit', 'id' => 'serviceRecord['.$srKey.'][appointment]']) !!}
    </td>
    <td class="vcenter text-right width-100">
        {!! Form::text('service_record['.$srKey.'][year]', null, ['id'=> 'serviceRecord['.$srKey.'][year]', 'class' => 'form-control text-right width-inherit']) !!}
    </td>
    <td class="vcenter text-center">
        <a class="btn badge-red-intense service-record-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>