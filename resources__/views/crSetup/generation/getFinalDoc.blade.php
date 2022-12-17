<?php $sentences = json_decode($request->sentences, true); ?>
@if(!empty($traitArr))
<div class="col-md-offset-2 col-md-8 final-doc-panel">
    <div class="col-md-12 text-center margin-bottom-20">
        <span class="bold uppercase">@lang('label.IN_CONFIDENCE')</span><br/>
    </div>
    <div class="col-md-12 text-center margin-top margin-bottom-20">
        <span class="bold underline uppercase font-size-14">@lang('label.NATIONAL_DEFENCE_COLLEGE')</span><br/>
        <span class="bold underline uppercase font-size-14">@lang('label.BANGLADESH')</span><br/>
        <span class="bold underline uppercase margin-top-10	font-size-14">@lang('label.MEMBERS_PERFORMANCE_REPORT')</span><br/>
    </div>
    <div class="col-md-12 margin-bottom-20 padding-0">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td width="15%">
                        <span class="bold">{{$cm->personal_no ?? ''}}</span>
                    </td>
                    <td width="25%">
                        <span class="bold">{{$cm->rank ?? ''}}</span>
                    </td>
                    <td width="60%">
                        <span class="bold">{!! !empty($cm->full_name) ? $cm->full_name : '' !!}</span>
                    </td>
                </tr>
                <tr>
                    <td width="40%" colspan="2">
                        <span class="bold">@lang('label.SERVICE'): {{$cm->wing ?? ''}}</span>
                    </td>
                    <td width="60%">
                        <span class="bold">@lang('label.COURSE'): {{$course->name ?? ''}}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @foreach($traitArr as $type => $info)
    @if($type != 3)
    <p class="margin-top-20 text-indent-20 text-justify">
        @foreach($info as $traitId => $trait)
        {{$request->sentence[$traitId] ?? ''}}
        @endforeach
    </p>
    @else
    <p class="margin-top-20 text-indent-20 text-justify">
        @foreach($info as $traitId => $trait)
        @if(!empty($specialTraitArr['for_recomnd_sentence']) && $specialTraitArr['for_recomnd_sentence'] != $traitId)
        {{$request->sentence[$traitId] ?? ''}}
        @endif
        @endforeach
    </p>
    @if(!empty($specialTraitArr['for_recomnd_sentence']))
    @if(!empty($info[$specialTraitArr['for_recomnd_sentence']]['slab']) && !empty($bPlusMarkingSlabList) && in_array($info[$specialTraitArr['for_recomnd_sentence']]['slab'], $bPlusMarkingSlabList))
    <div class="col-md-12 text-center margin-bottom-20 padding-0">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td width="65%">
                        <span class="bold">@lang('label.RECOMMENDATION_AS_DS_AFWC')</span>
                    </td>
                    <td width="35%">
                        <span class="bold">@lang('label.RECOMMENDED')</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
    @endif
    @endif
    @endforeach
    <div class="col-md-12 margin-bottom-20 padding-0  text-justify">
        <table class="table table-borderless">
            <tbody>
                <tr>
                    <td width="35%" class="padding-0">
                        <span class="bold text-justify uppercase">{!! $signAuthorityArr[3]['name'] ?? '' !!}</span><br/>
                        <span class="text-justify">{!! $signAuthorityArr[3]['rank'] ?? '' !!}</span><br/>
                        <span class="text-justify">{!! $signAuthorityArr[3]['appt'] ?? '' !!}</span><br/>
                    </td>
                    <td width="30%"></td>
                    <td width="35%" class="padding-0">
                        <span class="bold text-justify uppercase">{!! $signAuthorityArr[2]['name'] ?? '' !!}</span><br/>
                        <span class="text-justify">{!! $signAuthorityArr[2]['rank'] ?? '' !!}</span><br/>
                        <span class="text-justify">{!! $signAuthorityArr[2]['appt'] ?? '' !!}</span><br/>
                    </td>
                </tr>
                <tr class="margin-top-20">
                    <td width="35%" class="padding-left-0">
                        <span class="text-justify">{!!__('label.DATE__DEC_YEAR', ['y' => date("F Y")])!!}</span>
                    </td>
                    <td width="30%"></td>
                    <td width="35%" class="padding-left-0">
                        <span class="text-justify">{!!__('label.DATE__DEC_YEAR', ['y' => date("F Y")])!!}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-12 text-center margin-bottom-20">
        <span class="bold uppercase">@lang('label.IN_CONFIDENCE')</span><br/>
    </div>
</div>
<div class="col-md-12 margin-top-20 text-center">
    <button class="btn grey-mint" type="button" onclick="location.reload();">
        <i class="fa fa-arrow-left"></i> @lang('label.GO_BACK_TO_SENTENCE_SETUP')
    </button>
    <button class="btn green-seagreen generate-doc" type="button">
        <i class="fa fa-file-word-o"></i> @lang('label.GENERATE_REPORT_AS_DOC')
    </button>
    <?php
	$filePath = 'public/CourseReportFiles/' . $course->name;
    $uploadClass = !empty($prevCrGen->report_file) && file_exists($filePath . '/' . $prevCrGen->report_file) ? '' : 'display-none';
    ?>
    <button class="btn blue-steel tooltips upload-modified-doc {{$uploadClass}}" href="#modalUploadModifiedDoc"  data-toggle="modal">
        <i class="fa fa-upload"></i> @lang('label.UPLOAD_MODIFIED_FILE')
    </button>
</div>
@endif