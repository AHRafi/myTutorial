
{!! Form::open(array('group' => 'form', 'url' => '', 'class' => 'form-horizontal','id'=>'preModuleForm')) !!}
{!! Form::hidden('active_course_id', $activeCourseId, ['id' => 'activeCourseId']) !!}
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="info">
                        <th class="vcenter">@lang('label.SL_NO')</th>
                        <th class="vcenter">
                            @if(sizeof($preCourseModuleList) == 0)
                            #
                            @elseif(sizeof($preCourseModuleList) >= 1)
                            <div class="md-checkbox padding-left-10" >
                                {!! Form::checkbox('pre_module_check_all',1,false, ['id' => 'preModuleCheckAll', 'class'=> 'md-check']) !!}
                                <label for="preModuleCheckAll">
                                    <span class=""></span>
                                    <span class="check"></span>
                                    <span class="box"></span>@lang('label.CHECK_ALL')
                                </label>

                            </div>
                            @endif
                        </th>
                        <th class="vcenter">@lang('label.NAME')</th>
                        <th class="vcenter text-center">@lang('label.STATUS')</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($preCourseModuleList))
                    <?php $sl = 0 ?>
                    @foreach($preCourseModuleList as $gsModule)
                    <?php
                    $checkedGsModule = '';
                    $disabled = '';
                    if (!empty($prevGsModuleArr)) {
                        if (in_array($gsModule->module_id, $prevGsModuleArr)) {
                            $checkedGsModule = 'checked';
                        }
                    }
                    $preClass= 'pre-module-check';
                    if ($gsModule->status == '2') {
                        $disabled = 'disabled';
                        $preClass ='';
                    }
                    ?>
                    <tr>
                        <td class="vcenter" width="5%">{!! ++$sl!!}</td>
                        <td class="text-center vcenter" width="20%">
                            <div class="md-checkbox has-success">
                                {!! Form::checkbox('pre_module['.$gsModule->module_id.']', $gsModule->module_id, false, ['id' => 'preModuleId_'.$gsModule->module_id, 'data-id'=> $gsModule->module_id, 'class'=> 'md-check '.$preClass, $checkedGsModule, $disabled]) !!}
                                <label for="{!! 'preModuleId_'.$gsModule->module_id !!}">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                </label>
                            </div>
                        </td>
                        <td class="vcenter">{!! $gsModule->name !!}</td>
                        <td class="text-center vcenter">
                            @if($gsModule->status == '1')
                            <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                            @else
                            <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5">
                            @lang('label.NO_MODULE_FOUND')
                        </td>
                    </tr>
                    @endif      
                </tbody>
            </table>
        </div>
        @if(!empty($preCourseModuleList))
        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-5 col-md-12">

                    <button class = "button-submit btn btn-circle green"  id="saveBtn"  type="button">
                        <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                    </button>

                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{!! Form::close() !!}

<script type="text/javascript">
    $(document).ready(function () {
        $("#preModuleCheckAll").change(function () {
            if (this.checked) {
                $(".pre-module-check").each(function () {
                    this.checked = true;
                });
            } else {
                $(".pre-module-check").each(function () {
                    this.checked = false;
                });
            }
        });
    

    $('.pre-module-check').change(function () {
        if (this.checked == false) { //if this item is unchecked
            $('#preModuleCheckAll')[0].checked = false; //change 'check all' checked status to false
        }

        //check 'check all' if all checkbox items are checked
        if ($('.pre-module-check:checked').length == $('.pre-module-check').length) {
            $('#preModuleCheckAll')[0].checked = true; //change 'check all' checked status to true
        }
    });

    $(document).on('click', '#saveBtn', function (e) {
        e.preventDefault();
        var form_data = new FormData($('#preModuleForm')[0]);
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null
        };
        swal({
            title: 'Are you sure?',
            text: "@lang('label.YOU_WANT_TO_ADD_MODULE')",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, Save',
            cancelButtonText: 'No, Cancel',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{route('courseToModule.savePreModule')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        toastr.success(res.message, res.heading, options);
                        
                        
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 400) {
                            var errorsHtml = '';
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value[0] + '</li>';
                            });
                            toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                        } else if (jqXhr.status == 401) {
                            toastr.error(jqXhr.responseJSON.message, 'Error', options);
                        } else {
                            toastr.error('Something went wrong', 'Error', options);
                        }
                        App.unblockUI();
                    }
                });
            }

        });

    });
    
    });
    
</script>