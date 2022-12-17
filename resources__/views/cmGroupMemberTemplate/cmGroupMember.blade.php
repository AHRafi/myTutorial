<div class="row">
    @if (!$cmGroupMemberArr->isEmpty())



    <div class="col-md-12">
        <div class="row margin-bottom-10">
            <div class="col-md-12">
                <span class="label label-success" >@lang('label.TOTAL_NUM_OF_CM'): {!! !empty($cmGroupMemberArr)?count($cmGroupMemberArr):0 !!}</span>
                <span class="label label-purple" >@lang('label.TOTAL_ASSIGNED_CM'): &nbsp;{!! !empty($prevDataList) ? sizeof($prevDataList) : 0 !!}</span>

                <button class="label label-primary tooltips" href="#modalAssignedCm" id="assignedCm"  data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_CM')">
                    <!--@lang('label.CM_ASSIGNED_TO_THIS_GROUP'): {!! !empty($previousDataList)?count($previousDataList):0 !!}&nbsp; <i class="fa fa-search-plus"></i>-->
                    @lang('label.CM_ASSIGNED_TO_THIS_GROUP'): &nbsp;{!! !$chackPrevDataArr->isEmpty() ? sizeof($chackPrevDataArr) : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>
                </button>
            </div>
        </div>
    </div>


    <div class="col-md-12">
        <table class="table table-bordered table-hover" id="dataTable">
            <thead>
                <tr>
                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                    <th class="vcenter">
                        <div class="md-checkbox has-success tooltips" title="@lang('label.SELECT_ALL')">
                            {!! Form::checkbox('check_all',1,false,['id' => 'checkedAll','class'=> 'md-check']) !!} 
                            <label for="checkedAll">
                                <span></span>
                                <span class="check mark-caheck"></span>
                                <span class="box mark-caheck"></span>
                            </label>
                        </div>
                    </th>
                    <th class="text-center vcenter">@lang('label.PHOTO')</th>
                    <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                    <th class="vcenter">@lang('label.RANK')</th>
                    <th class="vcenter">@lang('label.FULL_NAME')</th>
                    <th class="vcenter">@lang('label.WING')</th>
                    <th class="vcenter">@lang('label.ASSIGNED_TO')</th>

                </tr>
            </thead>
            <tbody>
                <?php $sl = 0; ?>
                @foreach($cmGroupMemberArr as $item)
                <?php
                $checked = '';
                $disabled = '';
                $title = '';
                $class = 'cm-group-member-check';
                if (!empty($prevCourseWiseCmGroupMemberList)) {
                    $checked = array_key_exists($item->id, $prevCourseWiseCmGroupMemberList) ? 'checked' : '';
                }
                ?>

                <tr>
                    <td class="vcenter text-center">{!! ++$sl !!}</td>
                    <td class="vcenter text-center">
                        <div class="md-checkbox has-success tooltips" title="<?php echo $title ?>">
                            {!! Form::checkbox('cm_id['.$item->id.']', $item->id,$checked,['id' => $item->id, 'class'=> 'md-check '. $class, $disabled]) !!}

                            <label for="{{ $item->id }}">
                                <span class="inc"></span>
                                <span class="check mark-caheck"></span>
                                <span class="box mark-caheck"></span>
                            </label>
                        </div>
                    </td>
                    <td class="text-center vcenter" width="50px">
                        <?php if (!empty($item->photo && File::exists('public/uploads/cm/' . $item->photo))) { ?>
                            <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$item->photo}}" alt="{{ Common::getFurnishedCmName($item->full_name)}}"/>
                        <?php } else { ?>
                            <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($item->full_name)}}"/>
                        <?php } ?>
                    </td>
                    <td class="vcenter">{{ $item->personal_no }}</td>
                    <td class="vcenter">{{ $item->rank_code }}</td>
                    <td class="vcenter">{!! Common::getFurnishedCmName($item->full_name) !!}</td>
                    <td class="vcenter">{{ $item->wing_name }}</td>

                    <td class="vcenter">@if(!empty($prevDataList[$item->id]))
                        @foreach($prevDataList[$item->id] as $cmGroupId)
                        {!! isset($cmGroupDataList[$cmGroupId])?$cmGroupDataList[$cmGroupId].' ':''!!}
                        @endforeach
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-5 col-md-5">
                    <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit" >
                        <i class="fa fa-check"></i> @lang('label.SUBMIT')
                    </button>
                    <a href="{{ URL::to('cmGroupMemberTemplate') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_CM_FOUND')</p>
        </div>
    </div>
    @endif
</div>


<script src="{{ asset('public/js/custom.js') }}"></script>
<script type="text/javascript">


$(document).ready(function () {
<?php if (!$cmGroupMemberArr->isEmpty()) { ?>
        $('#dataTable').dataTable({
            "paging": true,
            "pageLength": 100,
            "info": false,
            "order": false
        });



        // this code for  database 'check all' if all checkbox items are checked
        if ($('.cm-group-member-check:checked').length == $('.cm-group-member-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        }

        $("#checkedAll").change(function () {
            if (this.checked) {
                $(".md-check").each(function () {
                    if (!this.hasAttribute("disabled")) {
                        this.checked = true;
                    }
                });
            } else {
                $(".md-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.cm-group-member-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            allCheck();
        });
        allCheck();
<?php } ?>
});
function allCheck() {
    if ($('.cm-group-member-check:checked').length == $('.cm-group-member-check').length) {
        $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
    } else {
        $('#checkedAll')[0].checked = false;
    }
}



</script>