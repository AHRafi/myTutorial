@if(Auth::user()->group_id == 2 && !empty($courseList) && sizeof($courseList) == 1)
<span class="text-danger course-err">{{ __('label.NO_COURSE_FOUND_WITH_ATLEAST_ONE_COMPLETED_TERM') }}</span>
@endif