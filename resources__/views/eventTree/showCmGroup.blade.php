<div class = "form-group">
    <label class = "control-label col-md-6" for = "moduleId">@lang('label.HAS_SUB_EVENT')</label>
    <div class = "col-md-6 margin-top-8">
        <div class="md-checkbox">
            <input type="checkbox" id="check" class="md-check">
            <label for="check">
                <span></span>
                <span class="check"></span>
                <span class="box"></span>
            </label>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function () {
        //get module
        $(document).on("change", "#eventId", function () {
            var eventId = $("#eventId").val();

            if (eventId === '0') {
                $('#showEventTree').html('');
                return false;
            }
            $.ajax({
                url: "{{ URL::to('eventTree/getEventTree')}}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    course_id: eventId
                },
                beforeSend: function () {
                    App.blockUI({boxed: true});
                },
                success: function (res) {
                    $('#showEventTree').html(res.html);
                    $('.tooltips').tooltip();
                    $(".js-source-states").select2();
                    App.unblockUI();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
//                    App.unblockUI();
                }
            });//ajax
            App.unblockUI();
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
                        url: "{{URL::to('cmGroupToCourse/saveCmGroup')}}",
                        type: "POST",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (res) {
                            toastr.success(res, 'Cm Group has been related with this course', options);
                            $(document).trigger("change", "#courseId");
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
</script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>