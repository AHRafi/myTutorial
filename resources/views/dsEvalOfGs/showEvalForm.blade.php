<div class="row mt-15">
    <div class="col-md-12">

        <div class="portlet-body" style="padding: 8px !important">
            <h4 class="text-center" ><u>@lang('label.GS_EVAL_FORM'){{ $courseInfo->name }}</u></h4>
            <h4 class="text-center" >@lang('label.FACULTY')</h4>
        <div class="row">
            <div class="col-md-10">
                @lang('label.MODULE'): {{ $moduleInfo->name }}
            </div>
            <div class="col-md-2">
                @lang('label.DATE'): {{ $date }}
            </div>
        </div>
        <div class="row mt-15">
            <div class="col-md-12">
                @lang('label.LESSON'): <b>{{ $lessonInfo->title }}</b>
            </div>
        </div>
        <div class="row mt-15">
            <div class="col-md-12">
                @lang('label.LESSON_OBJ'): @lang('label.THE_GS_IS_EXPECTED_TO_FOCUS_ON_THE_FOL')
                <div class="col-md-12 col-md-offset-1 mt-10">
                    @if (!$objectiveArr->isEmpty())
                    <?php
                    $count= 'A';
                    ?>
                    @foreach($objectiveArr as $objective)
                    <tr>
                        <td class="text-center vcenter">{{ $count++ }}.{{ $objective->name }}</td></br>
                    </tr>
                    @endforeach
                    @endif

                </div>
            </div>
        </div>
        <div class="row mt-15">
            <div class="col-md-12">
                @lang('label.GS'):
            </div>
        </div>
        <div class="row mt-10">
            <div class="col-md-12">
                @lang('label.GRADING_SCALE_GS'):
                <div class="col-md-12 col-md-offset-1 mt-10">
                    @if (!$gradingArr->isEmpty())

                    @foreach($gradingArr as $grading)
                    <tr>
                        <td class="text-center vcenter">{{ $grading->wt }} - {{ $grading->title }} : {{ $grading->description }}</td></br>
                    </tr>
                    @endforeach
                    @endif

                </div>
            </div>
        </div>

        </div>
    </div>

</div>
