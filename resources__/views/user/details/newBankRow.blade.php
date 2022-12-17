<?php
$aKey = uniqid();
?>
<tr id="punishmentRecordRemove">
    <td class="vcenter text-center new-bank-sl width-50"> </td>
    <td class="vcenter width-180">
        {!! Form::text('bank['.$aKey.'][name]', null,  ['class' => 'form-control width-full', 'id' => 'name'.$aKey]) !!}
    </td>
    <td class="vcenter width-180">
        {!! Form::text('bank['.$aKey.'][branch]', null,  ['class' => 'form-control width-full', 'id' => 'branch'.$aKey]) !!}
    </td>
    <td class="vcenter width-210">
        {!! Form::text('bank['.$aKey.'][account]', null, ['id'=> 'acct'.$aKey, 'class' => 'form-control width-full']) !!}
    </td>
    <td class="vcenter width-85">
        <div class="for-present-svc-block checkbox-center md-checkbox has-success">
            <input id="forOnline{{$aKey}}" class="md-check" name="bank[{{$aKey}}][is_online]" type="checkbox" value="1">
            <label for="forOnline{{$aKey}}" class="course-member">
                <span class="inc"></span>
                <span class="check mark-caheck"></span>
                <span class="box mark-caheck"></span>
            </label>&nbsp;&nbsp;
            <span class="text-green">@lang('label.YES')</span>
        </div>
    </td>
    <td class="vcenter text-center width-50">
        <a class="btn badge-red-intense bank-remove-Btn" id="" type="button"  >
            <i class="fa fa-close"></i>
        </a>
    </td>
</tr>
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>