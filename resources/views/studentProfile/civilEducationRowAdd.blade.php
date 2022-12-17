<?php
$uniqkey = uniqid();
?>
<tr id="civilEducationRemove">
    <td class="vcenter text-center new-civil-education-sl"> </td>
    <td class="vcenter"> 
        
        {!! Form::text('civil_education['.$uniqkey.'][institute_name]', null, ['id'=> 'civilEducation['.$uniqkey.'][institute_name]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('civil_education['.$uniqkey.'][examination]', null, ['id'=> 'civilEducation['.$uniqkey.'][examination]', 'class' => 'form-control']) !!}
    </td>
    <td class="vcenter text-right">
        {!! Form::text('civil_education['.$uniqkey.'][result]', null, ['id'=> 'civilEducation['.$uniqkey.'][result]', 'class' => 'form-control integer-decimal-only text-right']) !!}
    </td>
    <td class="vcenter text-right">
        {!! Form::text('civil_education['.$uniqkey.'][year]', null, ['id'=> 'civilEducation['.$uniqkey.'][year]', 'class' => 'form-control text-right']) !!}
    </td>
    <td class="vcenter text-center">
        <a class="btn badge-red-intense civil-education-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>