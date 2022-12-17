<?php
$arKey = uniqid();
?>
<tr id="punishmentRecordRemove">
    <td class="vcenter text-center new-punishment-record-sl width-50"> </td>
    <td class="vcenter width-250"> 

        {!! Form::text('punishment_record['.$arKey.'][punishment]', null,  ['class' => 'form-control width-full', 'id' => 'punishment_record['.$arKey.'][punishment]']) !!}    
    </td>
    <td class="vcenter width-250">
        {!! Form::text('punishment_record['.$arKey.'][reason]', null,  ['class' => 'form-control width-full', 'id' => 'punishment_record['.$arKey.'][reason]']) !!}
    </td>
    <td class="vcenter text-right width-100">
        {!! Form::text('punishment_record['.$arKey.'][year]', null, ['id'=> 'punishment_record['.$arKey.'][year]', 'class' => 'form-control width-full text-right']) !!}
    </td>
    <td class="vcenter text-center width-50">
        <a class="btn badge-red-intense punishment-record-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>