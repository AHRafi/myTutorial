@if (!$targetArr->isEmpty())
    <div class="row">
        <div class="col-md-12">
            <span class="label label-sm label-blue-steel">
                @lang('label.TOTAL_NO_OF_LESSON'):&nbsp;{!! !empty($targetArr) ? sizeOf($targetArr) : 0 !!}
            </span>&nbsp;
            <span class="label label-purple">@lang('label.TOTAL_NO_OF_LESSON_ASSIGNED'):
                &nbsp;{!! !empty($count) ? $count : 0 !!}
            </span>&nbsp;

            <button class="label label-sm label-green-seagreen btn-label-groove tooltips" href="#modalAssignedLessen"
                id="assignedLesson" data-toggle="modal" title="@lang('label.SHOW_LESSON_ASSIGNED_TO_THIS_GS')">
                @lang('label.TOTAL_NO_OF_LESSON_ASSIGNED_TO_THIS_GS'):&nbsp;{!! !empty($count) ? $count : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>
            </button>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th class="text-center vcenter " width="5%">@lang('label.SL_NO')</th>
                        <th class="vcenter" width="10%">
                            <?php
                            //disable
                            $disabledCAll = '';
                            if (!empty($disableDataArr)) {
                                $disabledCAll = 'disabled';
                            }
                            ?>

                            <div class="md-checkbox has-success">
                                {!! Form::checkbox('check_all', 1, false, ['id' => 'checkAll', 'class' => 'md-check', $disabledCAll]) !!}
                                <label for="checkAll">
                                    <span class="inc"></span>
                                    <span class="check mark-caheck"></span>
                                    <span class="box mark-caheck"></span>
                                </label>&nbsp;&nbsp;
                                <span class="bold">@lang('label.CHECK_ALL')</span>
                            </div>
                        </th>
                        <th class="vcenter">@lang('label.LESSON')</th>
                        <th class="vcenter">@lang('label.SUBJECT')</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sl = 0; @endphp

                    @foreach ($targetArr as $target)
                        <?php
                        $disabled = '';
                        $checked = '';
                        $title = __('label.CHECK');
                        if (!empty($disableDataArr[$target->subject_id][$target->id])) {
                            $disabled = 'disabled';
                            $title = __('label.THIS_LESSON_IS_ALREADY_ASSIGNED_TO_GS', ['gs' => $disableDataArr[$target->subject_id][$target->id]]);
                        }


                        if (!empty($assignedLesson[$target->subject_id][$target->id])) {
                            $checked = 'checked';
                        }
                        ?>
                        <tr>
                            <td class="text-center vcenter">{!! ++$sl !!}</td>
                            <td class="vcenter">
                                <div class="md-checkbox has-success tooltips">
                                    {!! Form::checkbox('lesson[' . $target->subject_id . '][' . $target->id . ']', $target->id, $checked, [
                                        'id' => $target->id . '_' . $target->subject_id,
                                        'data-id' => $target->id,
                                        'class' => 'md-check gs-to-lesson',
                                        $disabled,
                                    ]) !!}

                                    <label for="{!! $target->id . '_' . $target->subject_id !!}">
                                        <span class="inc"></span>
                                        <span class="check mark-caheck tooltips" title="{{ $title }}"></span>
                                        <span class="box mark-caheck tooltips" title="{{ $title }}"></span>
                                    </label>
                                </div>
                            </td>
                            <td class="vcenter">{!! $target->lesson ?? '' !!}</td>
                            <td class="vcenter">{!! $target->subject ?? '' !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- if submit wt chack Start -->
    <div class="form-actions">
        <div class="col-md-offset-4 col-md-8">
            <button class="button-submit btn btn-circle green" type="button">
                <i class="fa fa-check"></i> @lang('label.SUBMIT')
            </button>
            <a href="{{ URL::to('gsToLesson') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
        </div>
    </div>
@else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_LESSON_FOUND')</p>
        </div>
    </div>
@endif
<!-- if submit wt chack End -->

<script type="text/javascript">
    //   Start: CHECK ALL
    $(document).ready(function() {

        <?php if (!$targetArr->isEmpty()) { ?>
        allCheck();
        $('#dataTable').dataTable({
            "paging": true,
            "pageLength": 100,
            "info": false,
            "order": false
        });
        <?php } ?>

        //'check all' change
        $(document).on('click', '#checkAll', function() {
            if ($('#checkAll').is(':checked')) {
                $('.gs-to-lesson').each(function() {
                    if (this.checked == false) {
                        var key = $(this).attr('data-id');
                        $(this).prop('checked', true);
                    }
                });
            } else {
                $(".gs-to-lesson").removeAttr('checked');
                $(".has-checked").attr('disabled', true);
                $(".has-checked").removeAttr('checked');
            }
        });

        $(document).on('click', '.gs-to-lesson', function() {
            allCheck();
        });

    });

    function allCheck() {

        if ($('.gs-to-lesson:checked').length == $('.gs-to-lesson').length) {
            $('#checkAll')[0].checked = true;
        } else {
            $('#checkAll')[0].checked = false;
        }
    }
    // End:  CHECK ALL
</script>
<script src="{{ asset('public/js/custom.js') }}" type="text/javascript"></script>
