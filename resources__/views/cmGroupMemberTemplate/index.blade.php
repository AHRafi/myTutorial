@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CM_GROUP_MEMBER_TEMPLATE')
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
                                <label class="control-label col-md-5" for="cmGroupId">@lang('label.CM_GROUP') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('cm_group_id', $cmGroupList, Request::get('cm_group_id'), ['class' => 'form-control js-source-states', 'id' => 'cmGroupId']) !!}
                                </div>
                            </div>
                        </div>

                    </div>



                    <div id="cmGroupMember">
                        @if(!empty(Request::get('course_id')) && !empty(Request::get('term_id')) && !empty(Request::get('cm_group_id')))
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
//                                            if (!empty($previousCmGroupMemberList[$item->id]) && ($request->cm_group_id != $previousCmGroupMemberList[$item->id])) {
//                                                $disabled = 'disabled';
//                                                $class = '';
//                                                $cmGroup = !empty($previousCmGroupMemberList[$item->id]) && !empty($cmGroupDataList[$previousCmGroupMemberList[$item->id]]) ? $cmGroupDataList[$previousCmGroupMemberList[$item->id]] : '';
//                                                $title = __('label.ALREADY_ASSIGNED_TO_CM_GROUP', ['cm_group' => $cmGroup]);
//                                            }
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
                                                {!! isset($cmGroupDataList[$cmGroupId])?$cmGroupDataList[$cmGroupId] . ' ':''!!}
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
                        @endif
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

<!--Assigned Cm list-->
<div class="modal fade" id="modalAssignedCm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showAssignedCm">

        </div>
    </div>
</div>


<script type="text/javascript">
    $(function () {
<?php
if (!empty(Request::get('course_id')) && !empty(Request::get('term_id')) && !empty(Request::get('cm_group_id'))) {
    if (!$cmGroupMemberArr->isEmpty()) {
        ?>
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
            $('#cmGroupId').html("<option value='0'>@lang('label.SELECT_CM_GROUP_OPT')</option>");
            $('#cmGroupMember').html('');

            if (termId == '0') {
                $('#cmGroupMember').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('cmGroupMemberTemplate/getCmGroup')}}",
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
                    $('#cmGroupId').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        $(document).on('change', '#cmGroupId', function () {
            var cmGroupId = $("#cmGroupId").val();
            var courseId = $("#courseId").val();
            var termId = $("#termId").val();
            if (cmGroupId == '0') {
                $('#cmGroupMember').html('');
                return false;
            }
            $.ajax({
                url: "{{URL::to('cmGroupMemberTemplate/cmGroupMember')}}",
                type: "POST",
                datatype: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    term_id: termId,
                    cm_group_id: cmGroupId,

                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
//                    alert('sdfghjk');
                    $("#cmGroupMember").html(res.html);
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
                        url: "{{URL::to('cmGroupMemberTemplate/saveCmGroupMember')}}",
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
                            toastr.success(res, "@lang('label.CM_HAS_BEEN_ASSIGNED_TO_THIS_GROUP')", options);
                            $('#buttonSubmit').prop('disabled', false);
                            App.unblockUI();
                            var courseId = $("#courseId").val();
                            var termId = $("#termId").val();
                            var cmGroupId = $("#cmGroupId").val();
                            location = "cmGroupMemberTemplate?course_id=" + courseId + "&term_id=" + termId + "&cm_group_id=" + cmGroupId;
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

    });

    function allCheck() {
        if ($('.cm-group-member-check:checked').length == $('.cm-group-member-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        } else {
            $('#checkedAll')[0].checked = false;
        }
    }

    // Start Show Assigned CM Modal
    $(document).on("click", "#assignedCm", function (e) {
        e.preventDefault();
        var courseId = $("#courseId").val();
        var termId = $("#termId").val();
        var cmGroupId = $("#cmGroupId").val();
        $.ajax({
            url: "{{ URL::to('cmGroupMemberTemplate/getAssignedCm')}}",
            type: "POST",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                term_id: termId,
                cm_group_id: cmGroupId,
            },
            success: function (res) {
                $("#showAssignedCm").html(res.html);
            },
            error: function (jqXhr, ajaxOptions, thrownError) {
            }
        }); //ajax
    });
    // End Show Assigned CM Modal
</script>
@stop