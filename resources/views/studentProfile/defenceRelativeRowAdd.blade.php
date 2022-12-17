<?php
$drKey = uniqid();
?>
<tr id="remove">
    <td class="vcenter text-center new-defence-relative-sl"> </td>
    <td class="vcenter width-200"> 

        {!! Form::select('defence_relative['.$drKey.'][course]', $courseList, null, ['class' => 'form-control js-source-states width-inherit', 'id' => 'defenceRelative['.$drKey.'][course]']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('defence_relative['.$drKey.'][institute]', null,  ['class' => 'form-control', 'id' => 'defenceRelative['.$drKey.'][institute]']) !!}
    </td>
    <td class="vcenter">
        {!! Form::text('defence_relative['.$drKey.'][grading]', null,  ['class' => 'form-control', 'id' => 'defenceRelative['.$drKey.'][grading]']) !!}
    </td>
    <td class="vcenter text-right">
        {!! Form::text('defence_relative['.$drKey.'][year]', null, ['id'=> 'punishment_record['.$drKey.'][year]', 'class' => 'form-control text-right']) !!}
    </td>
    <td class="vcenter text-center">
        <a class="btn badge-red-intense defence-relative-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
