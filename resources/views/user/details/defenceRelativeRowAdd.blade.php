<?php
$drKey = uniqid();
?>
<tr id="remove">
    <td class="vcenter text-center new-defence-relative-sl"> </td>
    <td class="vcenter width-170">
        {!! Form::text('mil_qual['.$drKey.'][institute_name]', '', ['id'=> 'inst'.$drKey, 'class' => 'form-control width-full']) !!}
    </td>
    <td class="vcenter width-210">
        <div class="width-full">
            {!! Form::select('mil_qual['.$drKey.'][course]', $milCourseList,null, ['id'=> 'course'.$drKey, 'data-key'=>$drKey, 'class' => 'form-control js-source-states mil-course text-width-100-per']) !!}
            {!! Form::text('mil_qual['.$drKey.'][course_name]', !empty($defenceRelativeInfo['course_name']) ? $defenceRelativeInfo['course_name'] : '', 
            ['id'=> 'milQualCourseName'.$drKey, 'placeholder' => 'Enter Course Name', 'class' => 'form-control width-full display-none ']) !!} 
        </div>
    </td>
    <td class="vcenter width-110">
        {!! Form::text('mil_qual['.$drKey.'][from]', '', ['id'=> 'milQualFrom'.$drKey, 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>
    <td class="vcenter width-110">
        {!! Form::text('mil_qual['.$drKey.'][to]', '', ['id'=> 'milQualTo'.$drKey, 'class' => 'form-control width-full', 'placeholder' => 'DD/MM/YYYY']) !!} 
    </td>
    <td class="vcenter width-85">
        {!! Form::text('mil_qual['.$drKey.'][result]', null, ['id'=> 'qualErode'.$drKey, 'placeholder' => 'Enter Grade' , 'class' => 'form-control grade-letter-only width-full ']) !!}
        {!! Form::text('mil_qual['.$drKey.'][other_result]', null, ['id'=> 'milQualOtherResult'.$drKey, 'placeholder' => 'Enter Grade' 
        ,'class' => 'form-control grade-letter-only width-full display-none']) !!} 
    </td>
    <td class="vcenter text-center width-50">
        <a class="btn badge-red-intense defence-relative-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
