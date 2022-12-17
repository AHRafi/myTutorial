@extends('layouts.default.master') 
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.RELATE_FACTOR_TO_TRAIT')
            </div>
        </div>

        <div class="portlet-body">
            {!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitForm')) !!}
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="trainingYearId">@lang('label.TRAINING_YEAR'):</label>
                                <div class="col-md-8">
                                    {!! Form::select('training_year_id', $trainingYearList, Request::get('training_year_id'), ['class' => 'form-control js-source-states', 'id' => 'trainingYearId']) !!}
                                    <span class="text-danger">{{ $errors->first('training_year_id') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="courseId">@lang('label.COURSE'):</label>
                                <div class="col-md-8">
                                    {!! Form::select('course_id', $courseList, Request::get('course_id'), ['class' => 'form-control js-source-states', 'id' => 'courseId']) !!}
                                    <span class="text-danger">{{ $errors->first('course_id') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-5" for="traitId">@lang('label.TRAIT') :<span class="text-danger"> *</span></label>
                                <div class="col-md-7">
                                    {!! Form::select('trait_id', $traitList, null, ['class' => 'form-control js-source-states', 'id' => 'traitId']) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--get module data-->
                </div>
                <div class="col-md-12">
                    <button class="btn green-steel bold tooltips clone-setup-btn pull-right margin-bottom-10 display-none" title="@lang('label.CLICK_HERE_TO_CLONE_THIS_SETUP')" type="button" data-placement="top"
                            data-rel="tooltip" data-original-title="@lang('label.CLICK_HERE_TO_CLONE_THIS_SETUP')" data-target="#cloneSentenceTraitModal" data-toggle="modal">
                        @lang('label.SET_TO_CLONE_THIS_SETUP')
                    </button> 
                </div>

            </div>


            <div class="margin-top-10 width-inherit" id="showMarkingSlab"></div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!-- START:: Clone Sentence to Trait Modal -->
<div class="modal fade test" id="cloneSentenceTraitModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="showCloneSentenceToTrait"></div>
    </div>
</div>
<!-- END:: Clone Sentence to Trait Modal -->


<script type="text/javascript">
    $(function () {
        //Start::Get Course
        $(document).on("change", "#trainingYearId", function () {
            var trainingYearId = $("#trainingYearId").val();
            $('#courseId').html("<option value='0'>@lang('label.SELECT_COURSE_OPT')</option>");
            if (trainingYearId == 0) {
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('crSentenceToTrait/getCourse')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#courseId').html(res.html);
                    $('.course-err').html(res.html1);
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax

        });
        //End::Get Course
        
        //Start::Get Course
        $(document).on("change", "#courseId", function () {
            var courseId = $("#courseId").val();
            if (courseId != 0) {
                $('.clone-setup-btn').removeClass('display-none');
                $('.clone-setup-btn').addClass('display-block');
            }else{
                $('.clone-setup-btn').removeClass('display-block');
                $('.clone-setup-btn').addClass('display-none');
                return false;
            }

        });
        //End::Get Course
        

        //Start::Clone Sentence to Trait Setup
        $(document).on("click", ".clone-setup-btn", function () {
            var courseId = $("#courseId").val();
            var trainingYearId = $("#trainingYearId").val();
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
            };
            $.ajax({
                url: "{{ URL::to('crSentenceToTrait/getCloneSentenceToTrait')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    training_year_id: trainingYearId,
                    course_id: courseId
                },
                beforeSend: function () {
                    $('#showCloneSentenceToTrait').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showCloneSentenceToTrait').html(res.html);
                    $('js-source-states').select2();
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax

        });
        //End::Clone Sentence to Trait Setup


        //Start::Show Related Trait List
        $(document).on("change", "#relatedCourseId", function () {
            var relatedCourseId = $("#relatedCourseId").val();
            var selectedCourseId = $("#selectedCourseId").val();

            if (relatedCourseId == 0) {
                $('#showRelatedTraits').html('');
                return false;
            }

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
            $.ajax({
                url: "{{ URL::to('crSentenceToTrait/getTraitList')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    related_course_id: relatedCourseId,
                    selected_course_id: selectedCourseId
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showRelatedTraits').html(res.html);
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    App.unblockUI();
                    var errorsHtml = '';
                    if (jqXhr.status == 400) {
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function (key, value) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, jqXhr.responseJSON.heading, {"closeButton": true});
                    } else {
                        toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    }

                }
            });//ajax

        });
        //End::Show Related Trait List
        
        
        //Start::Clone Sentences to Course
         $(document).on('click', '#cloneSubmit', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitCloneForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
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
                        url: "{{URL::to('crSentenceToTrait/cloneSentenceToTrait')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.SENTENCE_TO_TRAIT_CLONED_SUCCESSFULLY")', res, options);
                            setTimeout(location.reload(),5000);
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
                                toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
                            }
                            App.unblockUI();
                        }
                    });
                }

            });

        });
        //END::Clone Sentences to Course



        $(document).on("change", "#traitId", function () {

            var courseId = $("#courseId").val();
            var traitId = $("#traitId").val();
            if (traitId == '0') {
                $('#showMarkingSlab').html('');
                return false;
            }
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };

            $.ajax({
                url: "{{ URL::to('crSentenceToTrait/getMarkingSlab')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: courseId,
                    trait_id: traitId,
                },
                beforeSend: function () {
                    $('#showMarkingSlab').html('');
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showMarkingSlab').html(res.html);
                    $('.tooltips').tooltip();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    toastr.error('@lang("label.SOMETHING_WENT_WRONG")', 'Error', options);
                    App.unblockUI();
                }
            });//ajax
        });

        //add multiple phone number
        $(document).on("click", ".add-sentence", function () {
            var key = $(this).attr("data-marking-slab-id");

            $.ajax({
                url: "{{ URL::to('crSentenceToTrait/addSentence')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    key: key,
                },
                beforeSend: function () {
                    $('.tooltip').hide();
                },
                success: function (res) {
                    $("#newSentence_" + key).append(res.html);
//                    $(".tooltips").tooltip();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                }
            }); //ajax
        });


        //remove  row
        $(document).on('click', '.remove-sentence', function () {
            $(this).parent().parent().remove();
            $('.tooltip').hide();
            return false;
        });

        $(document).on('click', '.button-submit', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitForm')[0]);
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null
            };
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
                        url: "{{URL::to('crSentenceToTrait/saveSentenceToTrait')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success('@lang("label.FACTOR_TO_TRAIT_ASSIGNED_ASSIGNED_SUCCESSFULLY")', res, options);


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
                                toastr.error('Error', '@lang("label.SOMETHING_WENT_WRONG")', options);
                            }
                            App.unblockUI();
                        }
                    });
                }

            });

        });
    });

</script>
@stop