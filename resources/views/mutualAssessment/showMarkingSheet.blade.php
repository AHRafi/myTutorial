<div class="col-md-12">
    <div class="row">
        <div class="col-md-10">
            <span class="label label-sm label-green-seagreen">
                @lang('label.TOTAL_NUMBER_OF_CM') : <strong>{{ !$cmList->isEmpty() ? $cmList->count() : 0 }}</strong>
            </span>
        </div>
        <div class="col-md-2 text-right mb-10">
            <button class="btn purple-sharp btn-sm" type="button" id="import">
                <i class="fa fa-arrow-down"></i> @lang('label.IMPORT')
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-header-fixed">
                <thead>
                    <tr>
                        <th class="vcenter text-center">@lang('label.SL')</th>
                        <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                        <th class="vcenter">@lang('label.RANK')</th>
                        <th class="vcenter">@lang('label.NAME')</th>
                        <th class="vcenter">@lang('label.PHOTO')</th>
                        @if(!empty($factorList))
                        @foreach($factorList as $factorId => $factor)
                        <th class="vcenter text-center width-80">{{$factor}}</th>
                        @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $sl = 1; @endphp 
                    @if(!$cmList->isEmpty())
                    @foreach($cmList as $cm) 
                    <tr>
                        <td class="vcenter text-center width-80"><strong>{{ $sl++ }}</strong></td>
                        <td class="vcenter width-80">{{ $cm->personal_no }}</td>
                        <td class="vcenter width-80">{{ $cm->rank }}</td>
                        <td class="vcenter">{!! Common::getFurnishedCmName($cm->full_name) !!}</td>
                        <td class="vcenter" width="50px">
                            @if(!empty($cm->photo) && File::exists('public/uploads/cm/' . $cm->photo))
                            <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cm->photo}}" alt="{!! Common::getFurnishedCmName($cm['full_name']) !!}"/>
                            @else
                            <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cm['full_name']) !!}"/>
                            @endif
                        </td>
                        @if(!empty($factorList))
                        @foreach($factorList as $factorId => $factor)
                        <td class="vcenter text-center width-80">
                            {!! Form::text('position['.$cm->cm_id.']['.$factorId.']', !empty($prevMarkingArr[$cm->cm_id][$factorId])? $prevMarkingArr[$cm->cm_id][$factorId] : null, ['class' => 'form-control text-center width-inherit', 'readonly']) !!}
                        </td>
                        @endforeach
                        @endif
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="vcenter"><strong>@lang('label.CM_NOT_AVAILABLE')</strong></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .borderless td, .borderless th {
        border: none;
    } 
    .custom-padding-3-10 td{
        padding:3px 10px !important;
    }
</style>

<script>
$(function(){
    $('.table-header-fixed').tableHeadFixer({left: 5});
});

</script>