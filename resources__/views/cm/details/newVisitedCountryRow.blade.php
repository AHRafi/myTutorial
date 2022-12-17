<?php
$crKey = uniqid();
?>
<tr id="punishmentRecordRemove">
    <td class="vcenter text-center new-country-visit-sl width-50"> </td>
    <td class="vcenter width-100">
        {!! Form::text('country_visit['.$crKey.'][country_name]', '',  ['class' => 'form-control width-full', 'id' => 'country'.$crKey]) !!} 
    </td>
    <td class="vcenter width-110">
        {!! Form::text('country_visit['.$crKey.'][from]','', ['id'=> 'visitFrom'.$crKey
        , 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>
    <td class="vcenter width-110">
        {!! Form::text('country_visit['.$crKey.'][to]','', ['id'=> 'visitTo'.$crKey
        , 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>

    <td class="vcenter width-250">
        {!! Form::text('country_visit['.$crKey.'][reason]','', ['id'=> 'reason'.$crKey, 'class' => 'form-control width-full']) !!}
    </td>
    <td class="vcenter text-center width-50">
        <a class="btn badge-red-intense country-visit-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>