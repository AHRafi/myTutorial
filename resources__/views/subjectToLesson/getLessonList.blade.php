{!! Form::open(['group' => 'form', 'url' => '#', 'class' => 'form-horizontal', 'id' => 'submitLessonForm']) !!}
{!! Form::hidden('subject_id', $subjectId, ['id' => 'subjectId']) !!}
{!! Form::hidden('course_id', $courseId, ['id' => 'courseId']) !!}

<div class="row margin-top-10">
    <div class="col-md-12">
        <span class="label label-success">@lang('label.TOTAL_NUM_OF_LESSONS'): {!! !empty($lessonList) ? count($lessonList) : 0 !!}</span>
        <span class="label label-purple total-related-lessons">@lang('label.TOTAL_RELATED_LESSONS'): &nbsp;{!! !empty($prevAllLessonList) ? sizeof($prevAllLessonList) : 0 !!}</span>

        <button class="label label-primary tooltips" href="#modalAssignedLesson" id="assignedLesson" data-toggle="modal"
                title="@lang('label.CLICK_HERE_TO_VIEW_LESSONS_RELATED_TO_THIS_SUBJECT')">
                <!--@lang('label.DS_ASSIGNED_TO_THIS_GROUP'): {!! !empty($previousDataList) ? count($previousDataList) : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>-->
            @lang('label.LESSONS_RELATED_TO_THIS_SUBJECT'): &nbsp;{!! !empty($prevlessonList) ? sizeof($prevlessonList) : 0 !!}&nbsp; <i class="fa fa-search-plus"></i>
        </button>
    </div>
</div>

<div class="row margin-top-10">
    <div class="col-md-12">
        <div class="table-responsive webkit-scrollbar">
            <table class="table table-bordered table-hover" id="dataTable">
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
                        <th class="vcenter">@lang('label.LESSON')</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($lessonList))
                    <?php $sl = 0; ?>
                    @foreach ($lessonList as $lessonId => $lessonName)
                    <?php
                    $checkedLesson = '';
                    if (!empty($prevlessonList[$lessonId])) {
                        $checkedLesson = 'checked';
                    }
                    
                    $disabledLesson = '';
                    $disabledTitle = '';
                    if (!empty($prevAllLessonList[$lessonId]['subject_id']) && $prevAllLessonList[$lessonId]['subject_id'] != $subjectId) {
                        $disabledLesson = 'disabled';
                        $disabledTitle = __('label.THIS_LESSON_IS_ALREADY_RELATED_TO_SUBJECT', ['subject' => $prevAllLessonList[$lessonId]['subject'] ?? '']);
                    }
                    ?>
                    <tr>
                        <td class="vcenter" width="5%">{!! ++$sl !!}</td>
                        <td class="text-center vcenter" width="10%">
                            <div class="md-checkbox has-success tooltips" title="{!! $disabledTitle !!}">
                                {!! Form::checkbox('lesson_id[' . $lessonId . ']', $lessonId, $checkedLesson, [
                                'id' => 'lessonId_' . $lessonId,
                                'data-id' => $lessonId,
                                'class' => 'md-check lesson-check',
                                $disabledLesson
                                ]) !!}
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
    </div>
</div>

@if (!empty($lessonList))
<div class="form-actions margin-top-10">
    <div class="row">
        <div class="col-md-offset-5 col-md-12">

            <button class="button-submit btn btn-circle green" id="lessonBtn" type="button">
                <i class="fa fa-check"></i> @lang('label.SUBMIT')
            </button>

            <a href="{{ URL::to('subjectToLesson') }}"
               class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>

        </div>
    </div>
</div>
@endif
{!! Form::close() !!}
<div class="modal fade" id="modalAssignedLesson" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="placeAssignedLesson">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#dataTable').dataTable({
            "paging": true,
            "pageLength": 100,
            "info": false,
            "order": false
        });

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


    });
</script>
