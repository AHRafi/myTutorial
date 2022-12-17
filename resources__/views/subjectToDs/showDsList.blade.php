<div class="row">
    @if ($dsArr)
        <div class="col-md-12">
            <div class="row margin-bottom-10">
                <div class="col-md-12">
                    <span class="label label-success">@lang('label.TOTAL_NUM_OF_DS'): {!! !empty($dsArr) ? count($dsArr) : 0 !!}</span>
                    <span class="label label-purple">@lang('label.TOTAL_ASSIGNED_DS'): &nbsp;{!! !empty($prevDataList) ? sizeof($prevDataList) : 0 !!}</span>

                    <button class="label label-primary tooltips" href="#modalAssignedDs" id="assignedDs" data-toggle="modal"
                        data-subjectId="{{ $request->subject_id ?? 0 }}" title="@lang('label.SHOW_ASSIGNED_DS')">
                        @lang('label.DS_ASSIGNED_TO_THIS_SUBJECT'): {!! !empty($previousCheck) ? count($previousCheck) : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>
                    </button>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <table class="table table-bordered table-hover hover display" id="dataTable">
                <thead>
                    <tr>
                        <th class="vcenter text-center">@lang('label.SL_NO')</th>
                        <th class="vcenter">
                            <div class="md-checkbox has-success tooltips" title="@lang('label.SELECT_ALL')">
                                {!! Form::checkbox('check_all', 1, false, ['id' => 'checkedAll', 'class' => 'md-check']) !!}
                                <label for="checkedAll">
                                    <span></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label> <span class="bold">&nbsp;&nbsp; @lang('label.CHECK_ALL')</span>
                            </div>
                        </th>
                        <th class="text-center vcenter">@lang('label.PHOTO')</th>
                        <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                        <th class="vcenter">@lang('label.RANK')</th>
                        <th class="vcenter">@lang('label.FULL_NAME')</th>
                        <th class="vcenter">@lang('label.WING')</th>

                    </tr>
                </thead>
                <tbody>
                    <?php $sl = 0; ?>
                    @foreach ($dsArr as $item)
                        @php
                            $checked = '';
                            $disabled = '';
                            $subject = '';
                            $class = 'cm-group-member-check';
                            if (!empty($previousCheck)) {
                                $checked = array_key_exists($item->id, $previousCheck) ? 'checked' : '';
                            }
                            foreach ($subjectArr as $key => $value) {
                                if($value->id == $item->id && $value->subject_id != $request->subject_id){
                                    $subject .= "{$value->title}, ";
                                }
                            }
                        @endphp

                        <tr>
                            <td class="vcenter text-center">{!! ++$sl !!}</td>
                            <td class="vcenter text-center tooltips" title="{{ !empty($subject) ? 'Assigned to '.trim($subject,', ') : ''  }}">
                                <div class="md-checkbox has-success">
                                    {!! Form::checkbox('ds_id[' . $item->id . ']', $item->id, $checked, ['id' => $item->id, 'class' => 'md-check ' . $class, $disabled ]) !!}
                                    <label for="{{ $item->id }}">
                                        <span class="inc"></span>
                                        <span class="check mark-caheck"></span>
                                        <span class="box mark-caheck"></span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center vcenter" width="50px">
                                @if (!empty($item->photo && File::exists('public/uploads/user/' . $item->photo)))
                                    <img width="50" height="60"
                                        src="{{ URL::to('/') }}/public/uploads/user/{{ $item->photo }}"
                                        alt="{{ Common::getFurnishedCmName($item->full_name) }}" />
                                @else
                                    <img width="50" height="60" src="{{ URL::to('/') }}/public/img/unknown.png"
                                        alt="{{ Common::getFurnishedCmName($item->full_name) }}" />
                                @endif
                            </td>
                            <td class="vcenter">{{ $item->personal_no }}</td>
                            <td class="vcenter">{{ $item->rank_code }}</td>
                            <td class="vcenter">{!! Common::getFurnishedCmName($item->full_name) !!}</td>
                            <td class="vcenter">{{ $item->wing_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-5 col-md-5">
                        <button class="btn btn-circle green button-submit" type="button" id="buttonSubmit">
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>
                        <a href=""
                            class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissable">
                <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_DS_FOUND')</p>
            </div>
        </div>
    @endif
</div>


<script src="{{ asset('public/js/custom.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        var isDsEmpty = {{ !$dsArr->isEmpty() }};

        if (isDsEmpty) {
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

            $("#checkedAll").change(function() {
                if (this.checked) {
                    $(".md-check").each(function() {
                        if (!this.hasAttribute("disabled")) {
                            this.checked = true;
                        }
                    });
                } else {
                    $(".md-check").each(function() {
                        this.checked = false;
                    });
                }
            });

            $('.cm-group-member-check').change(function() {
                if (this.checked == false) { //if this item is unchecked
                    $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
                }

                //check 'check all' if all checkbox items are checked
                allCheck();
            });
            allCheck();
        }

    });

    function allCheck() {
        if ($('.cm-group-member-check:checked').length == $('.cm-group-member-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        } else {
            $('#checkedAll')[0].checked = false;
        }
    }
</script>
