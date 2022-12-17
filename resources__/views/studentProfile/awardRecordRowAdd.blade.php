<?php
$arKey = uniqid();
?>
<tr id="awardRecordRemove">
    <td class="vcenter text-center new-award-record-sl width-50"> </td>
    <td class="vcenter width-250"> 

        {!! Form::text('award_record['.$arKey.'][award]', null,  ['class' => 'form-control width-inherit', 'id' => 'award_record['.$arKey.'][award]']) !!}    
    </td>
    <td class="vcenter width-250">
        {!! Form::text('award_record['.$arKey.'][reason]', null,  ['class' => 'form-control width-inherit', 'id' => 'award_record['.$arKey.'][reason]']) !!}
    </td>
    <td class="vcenter text-right width-100">
        {!! Form::text('award_record['.$arKey.'][year]', null, ['id'=> 'award_record['.$arKey.'][year]', 'class' => 'form-control text-right width-inherit']) !!}
    </td>
    <td class="vcenter text-center width-50">
        <a class="btn badge-red-intense award-record-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>