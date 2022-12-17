@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.DS_GROUP_MEMBER_TEMPLATE')
            </div>
        </div>
        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            {{csrf_field()}}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYearInfo->id, ['id' => 'trainingYearId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeCourse->name}} </strong></div>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="termId">@lang('label.TERM') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7 show-term">
                                    {!! Form::select('term_id', $termList, Request::get('term_id'),  ['class' => 'form-control js-source-states', 'id' => 'termId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="dsGroupId">@lang('label.DS_GROUP') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('ds_group_id', $dsGroupList, Request::get('ds_group_id'), ['class' => 'form-control js-source-states', 'id' => 'dsGroupId']) !!}
                                </div>
                            </div>
                        </div>
                    </div>



                    <div id="dsGroupMember">
                        @if(!empty(Request::get('course_id')) && !empty(Request::get('term_id')) && !empty(Request::get('ds_group_id')))
                        <div class="row">
                            @if (!$dsGroupMemberArr->isEmpty())

                            <div class="col-md-12">
                                <div class="row margin-bottom-10">
                                    <div class="col-md-12">
                                        <span class="label label-success" >@lang('label.TOTAL_NUM_OF_DS'): {!! !empty($dsGroupMemberArr)?count($dsGroupMemberArr):0 !!}</span>
                                        <span class="label label-purple" >@lang('label.TOTAL_ASSIGNED_DS'): &nbsp;{!! !empty($prevDataList) ? sizeof($prevDataList) : 0 !!}</span>

                                        <button class="label label-primary tooltips" href="#modalAssignedDs" id="assignedDs"  data-toggle="modal" title="@lang('label.SHOW_ASSIGNED_DS')">
                                            <!--@lang('label.DS_ASSIGNED_TO_THIS_GROUP'): {!! !empty($previousDataList)?count($previousDataList):0 !!}&nbsp; <i class="fa fa-search-plus"></i>-->
                                            @lang('label.DS_ASSIGNED_TO_THIS_GROUP'): &nbsp;{!! !$chackPrevDataArr->isEmpty() ? sizeof($chackPrevDataArr) : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>
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
                                            <th class="vcenter">@lang('label.PHOTO')</th>
                                            <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                                            <th class="vcenter">@lang('label.RANK')</th>
                                            <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                                            <th class="vcenter">@lang('label.WING')</th>
                                            <th class="vcenter">@lang('label.ASSIGNED_TO')</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sl = 0; ?>
                                        @foreach($dsGroupMemberArr as $item)

                                        <?php
                                        $checked = '';
                                        $disabled = '';
                                        $title = '';
                                        $class = 'ds-group-member-check';
                                        if (!empty($prevCourseWiseDsGroupMemberList)) {
                                            $checked = array_key_exists($item->id, $prevCourseWiseDsGroupMemberList) ? 'checked' : '';
//                                            if (!empty($previousDsGroupMemberList[$item->id]) && ($request->ds_group_id != $previousDsGroupMemberList[$item->id])) {
//                                                $disabled = 'disabled';
//                                                $class = '';
//                                                $dsGroup = !empty($previousDsGroupMemberList[$item->id]) && !empty($dsGroupDataList[$previousDsGroupMemberList[$item->id]]) ? $dsGroupDataList[$previousDsGroupMemberList[$item->id]] : '';
//                                                $title = __('label.ALREADY_ASSIGNED_TO_DS_GROUP', ['ds_group' => $dsGroup]);
//                                            }
                                        }
                                        ?>

                                        <tr>
                                            <td class="vcenter text-center">{!! ++$sl !!}</td>
                                            <td class="vcenter text-center">
                                                <div class="md-checkbox has-success tooltips" title="<?php echo $title ?>">
                                                    {!! Form::checkbox('ds_id['.$item->id.']', $item->id,$checked,['id' => $item->id, 'class'=> 'md-check ' . $class, $disabled]) !!}

                                                    <label for="{{ $item->id }}">
                                                        <span class="inc"></span>
                                                        <span class="check mark-caheck"></span>
                                                        <span class="box mark-caheck"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center vcenter" width="50px">
                                                <?php if (!empty($item->photo && File::exists('public/uploads/user/' . $item->photo))) { ?>
                                                    <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$item->photo}}" alt="{{ $item->official_name}}"/>
                                                <?php } else { ?>
                                                    <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $item->official_name}}"/>
                                                <?php } ?>
                                            </td>
                                            <td class="vcenter">{{ $item->personal_no }}</td>
                                            <td class="vcenter">{{ $item->rank_code }}</td>
                                            <td class="vcenter">{!! $item->official_name !!}</td>
                                            <td class="vcenter">{{ $item->wing_name }}</td>

                                            <td class="vcenter">@if(!empty($prevDataList[$item->id]))
                                                @foreach($prevDataList[$item->id] as $dsGroupId)
                                                {!! isset($dsGroupDataList[$dsGroupId])?$dsGroupDataList[$dsGroupId].' ':''!!}
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
                                            <a href="{{ URL::to('dsGroupMemberTemplate') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
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
                        @endif
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

<!--Assigned ds list-->
<div class="modal fade" id="modalAssignedDs" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showAssignedDs">

        </div>
    </div>
</div>


<script type="text/javascript">
    $(function () {
<?php
if (!empty(Request::get('course_id')) && !empty(Request::get('term_id')) && !empty(Request::get('ds_group_id'))) {
    if (!$dsGroupMemberArr->isEmpty()) {
        ?>
                $('#dataTable').dataTable({
                    "paging": true,
                    "pageLength": 100,
                    "info": false,
                    "order": false
                });

                $('#checkedAll').change(function () {  //'check all' change
                    $('.ds-group-member-check').prop('checked', $(this).prop('checked')); //change all 'checkbox' checked status
                });
                $('.ds-group-member-check').change(function () {
                    if (this.checked == false) { //if this item is unchecked
                        $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
                    }
                    //check 'check all' if all checkbox items are checked
                    if ($('.ds-to-syn:checked').length == $('.ds-group-member-check').length) {
                        $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
                    }
                });




                // this code for  database 'check all' if all checkbox items are checked
                if ($('.ds-group-member-check:checked').length == $('.ds-group-member-check').length) {
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

                $('.ds-group-member-check').change(function () {
                    if (this.checked == false) { //if this item is unchecked
                        $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
                    }

                    //check 'check all' if all checkbox items are checked
                    allCheck();
                });
                allCheck();
        <?php
    }
}
?>
        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null,
        };

        $(document).on("change", "#termId", function () {
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            $('#dsGroupId').html("<option value='0'>@lang('label.SELECT_DS_GROUP_OPT')</option>");
            $('#dsGroupMember').html('');

            if (termId == '0') {
                $('#dsGroupMember').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('dsGroupMemberTemplate/getDsGroup')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#dsGroupId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on('click', '#buttonSubmit', function (e) {
            e.preventDefault();
            var oTable = $('#dataTable').dataTable();
            var x = oTable.$('input,select,textarea').serializeArray();
            $.each(x, function (i, field) {

                $("#submitForm").append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', field.name)
                        .val(field.value));
            });
            var form_data = new FormData($('#submitForm')[0]);
            swal({
                title: 'Are you sure?',
                   
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'No, cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('dsGroupMemberTemplate/saveDsGroupMember')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        beforeSend: function () {
                            $('#buttonSubmit').prop('disabled', true);
                            App.blockUI({boxed: true});
                        },
                        success: function (res) {
                            toastr.success(res, "@lang('label.DS_HAS_BEEN_ASSIGNED_TO_THIS_GROUP')", options);
                            $('#buttonSubmit').prop('disabled', false);
                            App.unblockUI();
                            var courseId = $("#courseId").val();
                            var termId = $("#termId").val();
                            var dsGroupId = $("#dsGroupId").val();
                            location = "dsGroupMemberTemplate?course_id=" + courseId + "&term_id=" + termId + "&ds_group_id=" + dsGroupId;

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
                                //toastr.error(jqXhr.responseJSON.message, '', options);
                                var errors = jqXhr.responseJSON.message;
                                var errorsHtml = '';
                                if (typeof (errors) === 'object') {
                                    $.each(errors, function (key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, '', options);
                                } else {
                                    toastr.error(jqXhr.responseJSON.message, '', options);
                                }
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            $('#buttonSubmit').prop('disabled', false);
                            App.unblockUI();
                        }
                    });
                }
            });
        });

        $(document).on('change', '#dsGroupId', function () {
            var dsGroupId = $("#dsGroupId").val();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            if (dsGroupId == '0') {
                $('#dsGroupMember').html('');
                return false;
            }
            $.ajax({
                url: "{{URL::to('dsGroupMemberTemplate/dsGroupMember')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    ds_group_id: dsGroupId,

                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
//                    alert('sdfghjk');
                    $("#dsGroupMember").html(res.html);
                    $(".previnfo").html('');
                    $('.tooltips').tooltip();
                    App.unblockUI();
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
                        toastr.error(jqXhr.responseJSON.message, '', options);
                    } else {
                        toastr.error('Error', 'Something went wrong', options);
                    }
                    App.unblockUI();
                }
            });
        });
    });
    function allCheck() {
        if ($('.ds-group-member-check:checked').length == $('.ds-group-member-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        } else {
            $('#checkedAll')[0].checked = false;
        }
    }

    // Start Show Assigned DS Modal
    $(document).on("click", "#assignedDs", function (e) {
        e.preventDefault();
        var dsGroupId = $("#dsGroupId").val();
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        $.ajax({
            url: "{{ URL::to('dsGroupMemberTemplate/getAssignedDs')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
                ds_group_id: dsGroupId,
            },
            success: function (res) {
                $("#showAssignedDs").html(res.html);
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        }); //ajax
    });
    // End Show Assigned DS Modal
</script>
@stop