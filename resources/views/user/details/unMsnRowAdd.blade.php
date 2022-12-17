<?php
$arKey = uniqid();
?>
<tr id="punishmentRecordRemove">
    <td class="vcenter text-center new-punishment-record-sl width-50"> </td>
    <td class="vcenter width-210">
        {!! Form::text('un_msn['.$arKey.'][from]','', ['id'=> 'unMsnFrom'.$arKey
        , 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>
    <td class="vcenter width-210">
        {!! Form::text('un_msn['.$arKey.'][to]','', ['id'=> 'unMsnTo'.$arKey
        , 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>
    <td class="vcenter width-250">
        {!! Form::text('un_msn['.$arKey.'][msn]', null,  ['class' => 'form-control width-full', 'id' => 'unMsn'.$arKey]) !!} 
    </td>
    <td class="vcenter width-210">
        {!! Form::text('un_msn['.$arKey.'][appointment]', null,  ['class' => 'form-control width-full', 'id' => 'unAppt'.$arKey]) !!}
    </td>
    <td class="vcenter width-150">
        {!! Form::text('un_msn['.$arKey.'][remark]', null, ['id'=> 'remark'.$arKey, 'class' => 'form-control width-full']) !!}
    </td>
    <td class="vcenter text-center width-50">
        <a class="btn badge-red-intense punishment-record-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>