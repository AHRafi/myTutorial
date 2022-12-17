@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.RELATE_EVENT_GROUP_TO_COURSE')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeTrainingYear->name}} </strong></div>
                                    {!! Form::hidden('training_year_id', $activeTrainingYear->id, ['id' => 'trainingYearId']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="courseId">@lang('label.COURSE'):</label>
                                <div class="col-md-7">
                                    <div class="control-label pull-left"> <strong> {{$activeCourse->name}} </strong></div>
                                    {!! Form::hidden('course_id', $activeCourse->id, ['id' => 'courseId']) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--get Cm Group data-->
                    <div id="showEventGroup">
                        @if (!$targetArr->isEmpty())

                        <div class = "form-group table-responsive max-height-300 webkit-scrollbar">
                            <label class = "control-label col-md-4" for = "moduleId">@lang('label.CHOOSE_EVENT_GROUP') :<span class = "text-danger"> *</span></label>
                            <div class = "col-md-8 margin-top-8">
                                <?php
                                //disable
                                $disabledCAll = '';
                                if (!empty($markingGroupDataArr)) {
                                    $disabledCAll = 'disabled';
                                }
                                ?>
                                <div class="md-checkbox">
                                    <input type="checkbox" id="checkedAll" class="md-check" {{ $disabledCAll }}>
                                    <label for="checkedAll">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                    <span class="bold">@lang('label.CHECK_ALL')</span>
                                </div>
                                <div class="form-group form-md-line-input">
                                    <div class="col-md-10">
                                        <div class="md-checkbox-list">
                                            @foreach($targetArr as $key=>$item)
                                            <?php
                                            //disable
                                            $disabled = '';
                                            if (!empty($markingGroupDataArr)) {
                                                $disabled = in_array($item->id, $markingGroupDataArr) ? 'disabled' : '';
                                            }
                                            $checked = '';
                                            $title = __('label.CHECK');
                                            if (!empty($previousDataList[$item->id])) {
                                                if (in_array($previousDataList[$item->id], $previousDataList)) {
                                                    $checked = 'checked';
                                                    $groupNmae = $item->name;
                                                    if (in_array($item->id, $markingGroupDataArr)) {
                                                        $title = __('label.GROUP_IS_ALREADY_ASSIGNED_TO_MARKING_GROUP', ['groupName' => $groupNmae]);
                                                    }  else {
                                                        $title = __('label.UNCHECK');
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="md-checkbox">
                                                {!! Form::checkbox('event_group_id['.$item->id.']',$item->id, false, ['id' => $item->id, 'data-id'=>$item->id,'class'=> 'md-check event-group-to-course-check', $checked,$disabled]) !!}
                                                @if(!empty($disableEventGroup))
                                                @foreach($disableEventGroup as $key=>$value )
                                                {!! Form::hidden('event_group_id['.$key.']', $key) !!}
                                                @endforeach
                                                @endif
                                                @if(!empty($disabled))
                                                {!! Form::hidden('event_group_id['.$item->id.']', $item->id) !!}
                                                @endif
                                                <span class = "text-danger">{{ $errors->first('event_group_id') }}</span>
                                                <label for="{{$item->id}}">
                                                    <span></span>
                                                    <span class="check tooltips" title="{{$title}}"></span>
                                                    <span class="box tooltips" title="{{$title}}"></span>{{$item->name}}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class = "form-actions">
                            <div class = "col-md-offset-4 col-md-8">
                                <button class = "button-submit btn btn-circle green" type="button">
                                    <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                                </button>
                                <a href = "{{ URL::to('eventGroupToCourse') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
                            </div>
                        </div>
                        @else
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissable">
                                <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_EVENT_GROUP_FOUND')</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {

    var options = {
        closeButton: true,
        debug: false,
        positionClass: "toast-bottom-right",
        onclick: null
    };
    //get event group
//    $(document).on("change", "#courseId", function () {
//        var trainingYearId = $("#trainingYearId").val();
//        var courseId = $("#courseId").val();
//
//        if (courseId === '0') {
//            $('#showEventGroup').html('');
//            return false;
//        }
//        $.ajax({
//            url: "{{ URL::to('eventGroupToCourse/getEventGroup')}}",
//            type: "POST",
//            dataType: "json",
//            headers: {
//                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//            },
//            data: {
//                course_id: courseId,
//                training_year_id: trainingYearId
//            },
//            beforeSend: function () {
//                App.blockUI({boxed: true});
//            },
//            success: function (res) {
//                $('#showEventGroup').html(res.html);
//                $('.tooltips').tooltip();
//                $(".js-source-states").select2();
//                App.unblockUI();
//            },
//            error: function (jqXhr, ajaxOptions, thrownError) {
//                toastr.error('Error', 'Something went wrong', options);
//                App.unblockUI();
//            }
//        });//ajax
//    });

    $(document).on('click', '.button-submit', function (e) {
        e.preventDefault();
        var form_data = new FormData($('#submitForm')[0]);

        swal({
            title: 'Are you sure?',
               
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, Save',
            cancelButtonText: 'No, Cancel',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{URL::to('eventGroupToCourse/saveEventGroup')}}",
                    type: "POST",
                    datatype: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (res) {
                        toastr.success(res, "@lang('label.EVENT_GROUP_HAS_BEEN_RELATED_TO_THIS_COURSE')", options);
//                        $(document).trigger("change", "#courseId");
                    },
                    error: function (jqXhr, ajaxOptions, thrownError) {
                        if (jqXhr.status == 400) {
                            var errorsHtml = '';
                            var errors = jqXhr.responseJSON.message;
                            $.each(errors, function (key, value) {
                                errorsHtml += '<li>' + value + '</li>';
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
            }

        });

    });
});
//    CHECK ALL
$(document).ready(function () {
    // this code for  database 'check all' if all checkbox items are checked
    if ($('.event-group-to-course-check:checked').length == $('.event-group-to-course-check').length) {
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

    $('.event-group-to-course-check').change(function () {
        if (this.checked == false) { //if this item is unchecked
            $('#checkedAll')[0].checked = false; //change 'check all' checked status to false
        }

        //check 'check all' if all checkbox items are checked
        if ($('.event-group-to-course-check:checked').length == $('.event-group-to-course-check').length) {
            $('#checkedAll')[0].checked = true; //change 'check all' checked status to true
        }
    });

});
//    CHECK ALL
</script>
@stop