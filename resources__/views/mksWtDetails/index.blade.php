@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.MKS_WT_DISTRIBUTION_DETAILS')
            </div>
            <div class="actions margin-left-4 margin-right-4">
                <a class="btn yellow btn-sm tooltips" title="@lang('label.CLICK_HERE_TO_GO_TO_WARNING_WT')" href="{{ URL::to('warningWt') }}"> @lang('label.GO_TO_WARNING_WT')
                </a>
            </div>
            <div class="actions margin-left-4 margin-right-4">
                <a class="btn green-steel btn-sm tooltips" title="@lang('label.CLICK_HERE_TO_GO_TO_MISC_WT')" href="{{ URL::to('miscWt') }}"> @lang('label.GO_TO_MISC_WT')
                </a>
            </div>
            <div class="actions margin-left-4 margin-right-4">
                <a class="btn blue-soft btn-sm tooltips" title="@lang('label.CLICK_HERE_TO_GO_TO_OBSN_WT')" href="{{ URL::to('obsnWt') }}"> @lang('label.GO_TO_OBSN_WT')
                </a>
            </div>
            <div class="actions margin-left-4 margin-right-4">
                <a class="btn purple-sharp btn-sm tooltips" title="@lang('label.CLICK_HERE_TO_GO_TO_EVENT_MKS_WT')" href="{{ URL::to('eventMksWt') }}"> @lang('label.GO_TO_EVENT_MKS_WT')
                </a>
            </div>
            <div class="actions margin-left-4 margin-right-4">
                <a class="btn green-jungle btn-sm tooltips" title="@lang('label.CLICK_HERE_TO_GO_TO_CRITERIA_WISE_WT')" href="{{ URL::to('criteriaWiseWt') }}"> @lang('label.GO_TO_CRITERIA_WISE_WT')
                </a>
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="form-group">
                        <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR') :</label>
                        <div class="col-md-8">
                            <div class="control-label pull-left"> <strong> {{$activeTrainingYearInfo->name}} </strong></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4" for="courseId">@lang('label.COURSE') :<span class="text-danger"> *</span></label>
                        <div class="col-md-4">
                            {!! Form::select('course_id', $courseList, null, ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                        </div>
                    </div>

                    <!--get module data-->
                    <div id="showMksWtDetails" class="col-md-offset-3 col-md-6"></div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId == '0') {
                $('#showMksWtDetails').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('mksWtDetails/getMksWtDetails')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showMksWtDetails').html(res.html);
                    $('.js-source-states').select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });
    });

</script>
@stop