<?php
$uniqkey = uniqid();
?>
<tr id="remove">
    <td class="vcenter text-center new-brother-sister-sl"> </td>
    <td class="vcenter"> 

        {!! Form::text('brother_sister['.$uniqkey.'][name]', null, ['id'=> 'brotherSisterName['.$uniqkey.'][name]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('brother_sister['.$uniqkey.'][relation]', null, ['id'=> 'brotherSisterRelation['.$uniqkey.'][relation]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter text-right">
        {!! Form::text('brother_sister['.$uniqkey.'][age]', null, ['id'=> 'brotherSisterAge['.$uniqkey.'][age]', 'class' => 'form-control integer-only text-right']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('brother_sister['.$uniqkey.'][occupation]', null, ['id'=> 'brotherSisterOccupation['.$uniqkey.'][occupation]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('brother_sister['.$uniqkey.'][address]', null, ['id'=> 'brotherSisterAddress['.$uniqkey.'][address]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter text-center">
        <a class="btn badge-red-intense remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
