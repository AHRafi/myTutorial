@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.RELATE_COURSE_TO_MODULE')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitGsModuleForm')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

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
                        <div class="col-md-3">
                            <button class = "btn btn-success" id="clone" data-target="#showCloneModal" data-toggle="modal" >
                                <i class=" fa fa-clone"></i>
                                @lang('label.CLONE_FROM_PREVIOUS_COURSE')
                            </button>
                        </div>
                    </div>

                    <div>
                        @if (!empty($gsModuleArr))
                        <div class = "form-group">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="info">
                                                <th class="vcenter">@lang('label.SL_NO')</th>
                                                <th class="vcenter">
                                                    @if(sizeof($gsModuleArr) == 0)
                                                    #
                                                    @elseif(sizeof($gsModuleArr) >= 1)
                                                    <div class="md-checkbox padding-left-10" >
                                                        {!! Form::checkbox('gs_module_check_all',1,false, ['id' => 'gsModuleCheckAll', 'class'=> 'md-check']) !!}
                                                        <label for="gsModuleCheckAll">
                                                            <span class=""></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span>@lang('label.CHECK_ALL')
                                                        </label>

                                                    </div>
                                                    @endif
                                                </th>
                                                <th class="vcenter">@lang('label.NAME')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($gsModuleArr))
                                            <?php $sl = 0 ?>
                                            @foreach($gsModuleArr as $gsModuleId => $gsModuleName)
                                            <?php
                                            $checkedGsModule = '';
                                            if (!empty($prevGsModuleArr)) {
                                                if (in_array($gsModuleId, $prevGsModuleArr)) {
                                                    $checkedGsModule = 'checked';
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td class="vcenter" width="5%">{!! ++$sl!!}</td>
                                                <td class="text-center vcenter" width="20%">
                                                    <div class="md-checkbox has-success">
                                                        {!! Form::checkbox('gs_module['.$gsModuleId.']', $gsModuleId, false, ['id' => 'gsModuleId_'.$gsModuleId, 'data-id'=> $gsModuleId, 'class'=> 'md-check gsmodule-check',$checkedGsModule]) !!}
                                                        <label for="{!! 'gsModuleId_'.$gsModuleId !!}">
                                                            <span class="inc"></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="vcenter">{!! $gsModuleName !!}</td>
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
                                @if(!empty($gsModuleArr))
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-5 col-md-12">

                                            <button class = "button-submit btn btn-circle green"  id="gsModuleBtn"  type="button">
                                                <i class = "fa fa-check"></i> @lang('label.SUBMIT')
                                            </button>

                                            <a href = "{{ URL::to('courseToModule') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>

                                        </div>
                                    </div>
                                </div>
                                @endif
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

<div class="modal fade" id="showCloneModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="placeCloneModal">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {



        $("#gsModuleCheckAll").change(function () {
            if (this.checked) {
                $(".gsmodule-check").each(function () {
                    this.checked = true;
                });
            } else {
                $(".gsmodule-check").each(function () {
                    this.checked = false;
                });
            }
        });

        $('.gsmodule-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#gsModuleCheckAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.gsmodule-check:checked').length == $('.gsmodule-check').length) {
                $('#gsModuleCheckAll')[0].checked = true; //change 'check all' checked status to true
            }
        });


        $(document).on('click', '#gsModuleBtn', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitGsModuleForm')[0]);
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
                        url: "{{route('courseToModule.saveModule')}}",
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




        $(document).on("click", "#clone", function (e) {
            e.preventDefault();


            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null,
            };
            $.ajax({
                type: 'post',
                url: "{{ URL::to('courseToModule/cloneModal') }}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function (res) {
                    $("#placeCloneModal").html(res.html);
                    $('.js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
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

        });
        
         
  
    });
</script>
@stop