<?php
$uniqkey = uniqid();
?>
<tr id="civilEducationRemove">
    <td class="vcenter text-center new-civil-education-sl"> </td>
    <td class="vcenter width-250">
        {!! Form::text('academic_qual['.$uniqkey.'][institute_name]', null, ['id'=> 'inst'.$uniqkey, 'class' => 'form-control width-full']) !!}
    </td>
    <td class="vcenter width-100">
        {!! Form::text('academic_qual['.$uniqkey.'][examination]', null, ['id'=> 'exm'.$uniqkey, 'class' => 'form-control width-full']) !!}
    </td>
<!--    <td class="vcenter width-100">
        {!! Form::text('academic_qual['.$uniqkey.'][from]', '', ['id'=> 'academicQualFrom'.$uniqkey, 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>-->
    <td class="vcenter width-100">
        {!! Form::text('academic_qual['.$uniqkey.'][year]', '', ['id'=> 'academicQualTo'.$uniqkey, 'class' => 'form-control width-full', 'placeholder' => 'YYYY']) !!} 
    </td>
    <td class="vcenter width-170">
        {!! Form::text('academic_qual['.$uniqkey.'][qual_erode]', null, ['id'=> 'qualErode'.$uniqkey, 'class' => 'form-control width-full']) !!}
    </td>
    <td class="vcenter text-center width-50">
        <a class="btn label-red-intense civil-education-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>