@if(!empty($cmList))
<div class="cm-list col-md-12">
    {!! Form::open(array('group' => 'form', 'url' => 'mutualAssessment/generate','class' => 'form-horizontal','id' => 'submitForm')) !!}   

    <div class="row">
        <div class="col-md-10 mb-10">
            <span class="label label-sm label-green-seagreen">
                @lang('label.TOTAL_NUMBER_OF_CM') : <strong>{{ !empty($cmList) ? sizeof($cmList) : 0 }}</strong> 
            </span> &nbsp;
            <span class="label label-sm label-blue-steel">
                @lang('label.TOTAL_EXPORTED_MARK_SHEET') : <strong>{{ !empty($exportCmIdArr) ? sizeof($exportCmIdArr) : 0 }}</strong> 
            </span> 
        </div>
        <div class="col-md-2 text-right">
            <button  type="button"  class=" btn green-steel btn-sm previewMarkingSheet">
                <i class="fa fa-download" aria-hidden="true"></i>&nbsp;@lang('label.PREVIEW_MARKING_SHEET')
            </button>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="col-md-12 table-responsive">
            <div class="webkit-scrollbar my-datatable">
                <table class="table table-bordered table-hover relation-view-2" id="cmListTable">
                    <thead>
                        <tr>
                            <th class="vcenter text-center">@lang('label.SL')</th>
                            <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                            <th class="vcenter">@lang('label.RANK')</th>
                            <th class="vcenter">@lang('label.NAME')</th>
                            <th class="vcenter">@lang('label.PHOTO')</th>
                            <th class="vcenter text-center" width="50">@lang('label.EXPORT_STATUS')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sl = 1; @endphp 
                        @foreach($cmList as $cm) 
                        <tr>
                            <td class="vcenter text-center"><strong>{{ $sl++ }}</strong></td>
                            <td class="vcenter">{{ $cm['personal_no'] }}</td>
                            <td class="vcenter">{{ $cm['rank'] }}</td>
                            <td class="vcenter">{!! Common::getFurnishedCmName($cm['full_name']) !!}</td>
                            <td class="vcenter" width="50px">
                                @if(!empty($cm['photo']) && File::exists('public/uploads/cm/' . $cm['photo']))
                                <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cm['photo']}}" alt="{!! Common::getFurnishedCmName($cm['full_name']) !!}"/>
                                @else
                                <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cm['full_name']) !!}"/>
                                @endif
                            </td>
                            <td class="vcenter text-center" width="50">
                                @if (in_array($cm['cm_id'],$exportCmIdArr))
                                <i class="fa fa-check font-green-jungle" aria-hidden="true"></i>
                                @else
                                <i class="fa fa-times font-red-thunderbird" aria-hidden="true"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {!! Form::hidden('course_id', $courseId) !!} 
    {!! Form::hidden('term_id', $termId) !!} 
    {!! Form::hidden('syn_id', $synId) !!} 
    {!! Form::hidden('sub_syn_id', $subSynId) !!} 
    {!! Form::hidden('event_id', $eventId) !!} 
    {!! Form::hidden('cm_id', null, ['class' => 'cm-id']) !!}   

    {!! Form::close() !!}  
</div>
@else
<div class="col-md-12  margin-top-10">
    <div class="alert alert-danger alert-dismissable">
        <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_CM_FOUND') !!}</strong></p>
    </div>
</div>
@endif

<style>
    .mb-10{
        margin-bottom: 10px;
    }
    .p-5{padding:5px;}
    .infos span{
        margin-right: 10px;
    }
</style>
<script>
    $(document).ready(function () {
        $('.relation-view-2').tableHeadFixer();
        $('#cmListTable').DataTable();
    });
</script>



