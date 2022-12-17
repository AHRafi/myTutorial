<div class="row margin-top-20">
    <div class="col-md-5">
        <div class="filter-block">
            <div class="col-md-12 margin-top-10">
                <span class="col-md-12 font-size-16 border-bottom-1-green-seagreen">
                    <i class="fa fa-plus-square"></i> <strong>@lang('label.ASSESSMENT_EVENT_GROUPS')</strong>
                </span>
            </div>
            <div class="col-md-12 margin-top-10">
                @if(!empty($markingGroupArr))
                <div class="table-responsive max-height-300 webkit-scrollbar cm-list-filterable">
                    <table class="table borderless table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th class="vcenter" width="20px">
                                    <div class="md-checkbox has-success tooltips" title="@lang('label.CHECK_ALL')">
                                        {!! Form::checkbox('check_all',1,false,['id' => 'checkedAll','class'=> 'md-check']) !!} 
                                        <label for="checkedAll">
                                            <span></span>
                                            <span class="check"></span>
                                            <span class="box"></span>
                                        </label>

                                    </div>
                                </th>
                                <th class="vcenter">@lang('label.CHECK_ALL')</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($markingGroupArr as $gpId => $gpName)
                            <?php
                            $disabled = $spanDisabled = $title = '';
                            $checked = !empty($mkGroupArr) && in_array($gpId, $mkGroupArr) ? 'checked' : '';

                            if (!empty($mkOtherGroupArr) && in_array($gpId, $mkOtherGroupArr)) {
                                $disabled = 'disabled';
                                $spanDisabled = 'span-disabled';
                                $title = __('label.CM_OF_THIS_GROUP_HAS_ALREADY_BEEN_ASSIGNED_TO_MA_GROUP', ['gp' => $gpName]);
                            }
                            ?>
                            <tr>
                                <td class="vcenter" width="20px">
                                    <div class="md-checkbox">
                                        {!! Form::checkbox('gp['.$gpId.']',$gpId, $checked, ['id' => 'gp'.$gpId, 'data-id'=>$gpId,'class'=> 'md-check gp-check', $disabled]) !!}
                                        <label class="tooltips" for="gp{{$gpId}}" title="{{$title}}" data-placement="right">
                                            <span></span>
                                            <span class="check tooltips"></span>
                                            <span class="box tooltips "></span>
                                        </label>
                                    </div>
                                </td>
                                <td class=" vcenter">
                                    <span class="{{ $spanDisabled }}">{!!$gpName!!}</span>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>


                <div class="text-center margin-top-10">
                    <button type="button" class="btn btn-primary assign-selected-cm" id="assignSelectedCm">
                        @lang('label.SET')&nbsp;<i class="fa fa-arrow-circle-right"></i> 
                    </button>
                </div>

                @else
                <div class="alert alert-danger alert-dismissable">
                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_ASSESSMENT_EVENT_GROUP_FOUND') !!}</strong></p>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="filter-block">
            <div class="col-md-12 margin-top-10">
                <span class="col-md-12 font-size-16 border-bottom-1-green-seagreen">
                    <i class="fa fa-users"></i>  <strong>@lang('label.SELECTED_CM_LIST')</strong>
                </span>
            </div>
            <div class="col-md-12 margin-top-10" id="showCmList">
                @if(!$cmMaGroupInfo->isEmpty())
                <div class="table-responsive max-height-250 webkit-scrollbar">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th class="vcenter text-center">@lang('label.SL')</th>
                                <th class="vcenter text-center">@lang('label.PHOTO')</th>
                                <th class="vcenter">@lang('label.NAME')</th>
                            </tr>
                        </thead>
                        <tbody id="selected-cm-body">
                            <?php $cmSl = 0; ?>
                            @foreach($cmMaGroupInfo as $target)

                            <tr>
                                {{ Form::hidden('selected_cm['.$target->id.']', $target->id, array('id' =>  $target->id, 'class' => 'selected-cm')) }}
                                <td class="vcenter text-center initial-serial-cm">{!! ++$cmSl !!}</td>
                                <td class="text-center vcenter" width="22px">
                                    <?php if (!empty($target->photo && File::exists('public/uploads/cm/' . $target->photo))) { ?>
                                        <img width="22" height="23" src="{{URL::to('/')}}/public/uploads/cm/{{$target->photo}}" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                                    <?php } else { ?>
                                        <img width="22" height="23" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ Common::getFurnishedCmName($target->full_name)}}"/>
                                    <?php } ?>
                                </td>
                                <td class="vcenter">{!!Common::getFurnishedCmName($target->cm_name)!!}</td>

                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>

                <div class="alert alert-success alert-dismissable margin-top-10">
                    <p><strong> {!! __('label.TOTAL_NO_OF_SELECTED_CM') !!} : &nbsp;<span class="selected-cm-no">{!! sizeof($cmMaGroupInfo) !!}</span></strong></p>
                </div> 
                @else
                <div class="alert alert-danger alert-dismissable">
                    <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_IS_SELECTED_FOR_THIS_MA_GROUP_YET') !!}</strong></p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        // this code for  database 'check all' if all checkbox items are checked
        if ($('.gp-check:checked').length == $('.gp-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        }

        $("#checkedAll").click(function () {
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

        $('.gp-check').click(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.gp-check:checked').length == $('.gp-check').length) {
                $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
            }
        });

    });
</script>