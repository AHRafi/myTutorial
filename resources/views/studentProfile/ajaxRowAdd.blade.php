<?php
$uniqkey = uniqid();
?>
<tr id="remove">
    <td class="vcenter new-brother-sister-sl text"> </td>
    <td class="vcenter"> 
        {!! Form::text('brother_sister['.$uniqkey.'][name]', null, ['id'=> 'brotherSisterName_['.$uniqkey.'][name]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('brother_sister['.$uniqkey.'][relation]', null, ['id'=> 'brotherSisterRelation_['.$uniqkey.'][relation]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('brother_sister['.$uniqkey.'][age]', null, ['id'=> 'brotherSisterAge_['.$uniqkey.'][age]', 'class' => 'form-control integer-only text-right']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('brother_sister['.$uniqkey.'][occupation]', null, ['id'=> 'brotherSisterOccupation_['.$uniqkey.'][occupation]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('brother_sister['.$uniqkey.'][address]', null, ['id'=> 'brotherSisterAddress_['.$uniqkey.'][address]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter">
        <a class="btn badge-red-intense remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
