{!! Form::open(array('group' => 'form', 'url' => '#','class' => 'form-horizontal','id' => 'submitLessonForm')) !!}
{!! Form::hidden('gs_id', $gsId, ['id' => 'gsId']) !!}
{!! Form::hidden('course_id', $courseId, ['id' => 'courseId']) !!}
{!! Form::hidden('module_id', $moduleId, ['id' => 'moduleId']) !!}
<div class="form-group">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="info">
                        <th class="vcenter">@lang('label.SL_NO')</th>
                        <th class="vcenter">
                            @if (sizeof($lessonList) == 0)
                                #
                            @elseif(sizeof($lessonList) >= 1)
                                <div class="md-checkbox padding-left-10">
                                    {!! Form::checkbox('lesson_check_all', 1, false, ['id' => 'lessonCheckAll', 'class' => 'md-check']) !!}
                                    <label for="lessonCheckAll">
                                        <span class=""></span>
                                        <span class="check"></span>
                                        <span class="box"></span>@lang('label.CHECK_ALL')
                                    </label>

                                </div>
                            @endif
                        </th>
                        <th class="vcenter">@lang('label.TITLE')</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($lessonList))
                        <?php $sl = 0; ?>
                        @foreach ($lessonList as $lessonId => $lessonName)
                            <?php
                            $checkedLesson = '';
                            if (!empty($prevlessonList)) {
                                if (in_array($lessonId, $prevlessonList)) {
                                    $checkedLesson = 'checked';
                                }
                            }
                            ?>
                            <tr>
                                <td class="vcenter" width="5%">{!! ++$sl !!}</td>
                                <td class="text-center vcenter" width="20%">
                                    <div class="md-checkbox has-success">
                                        {!! Form::checkbox('lesson_id[' . $lessonId . ']', $lessonId, false, ['id' => 'lessonId_' . $lessonId, 'data-id' => $lessonId, 'class' => 'md-check lesson-check', $checkedLesson]) !!}
                                        <label for="{!! 'lessonId_' . $lessonId !!}">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span>
                                        </label>
                                    </div>
                                </td>
                                <td class="vcenter">{!! $lessonName !!}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">
                                @lang('label.NO_LESSON_FOUND')
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if (!empty($lessonList))
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-5 col-md-12">

                        <button class="button-submit btn btn-circle green" id="lessonBtn" type="button">
                            <i class="fa fa-check"></i> @lang('label.SUBMIT')
                        </button>

                        <a href="{{ URL::to('gsToLesson') }}"
                            class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>

                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
{!! Form::close() !!}

<script type="text/javascript">

    $("#lessonCheckAll").change(function () {
        if (this.checked) {
            $(".lesson-check").each(function () {
                this.checked = true;
            });
        } else {
            $(".lesson-check").each(function () {
                this.checked = false;
            });
        }
    });

    $('.lesson-check').change(function () {
            if (this.checked == false) { //if this item is unchecked
                $('#lessonCheckAll')[0].checked = false; //change 'check all' checked status to false
            }

            //check 'check all' if all checkbox items are checked
            if ($('.lesson-check:checked').length == $('.lesson-check').length) {
                $('#lessonCheckAll')[0].checked = true; //change 'check all' checked status to true
            }
        });

        $(document).on('click', '#lessonBtn', function (e) {
            e.preventDefault();
            var form_data = new FormData($('#submitLessonForm')[0]);
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
                        url: "{{route('gsToLesson.saveLesson')}}",
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



</script>
