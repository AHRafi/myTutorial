<div class="col-md-12">
    <div class="row">
        <div class="col-md-10">
            @php 
            $colorClass = [
            'label-green-seagreen', 'label-blue-steel', 'label-purple', 'label-red-soft'
            , 'label-yellow', 'label-purple-sharp', 'label-blue-soft', 'label-green-steel'
            , 'label-grey-mint', 'label-blue-hoki'
            ];

            $i = 0;
            @endphp
            @if(!empty($markingSheetInfo))
            <?php
            ?>
            @foreach($markingSheetInfo as $info)
            <?php
            $inf = !empty($info) ? explode('|', $info) : [];
            $inSl = 0;
            ?>
            @if(!empty($inf))
            <ul class="padding-left-0">
                <li class="list-style-item-none display-inline-block margin-top-10">
                    <span class="label label-sm margin-bottom-10 {{ $colorClass[0] }}">
                        @lang('label.TOTAL_NUMBER_OF_CM') : <strong>{{ !$cmListData->isEmpty() ? sizeof($cmListData) : 0 }}</strong>
                    </span> 
                </li>
                <span class="label label-sm label-green-seagreen">
                    @lang('label.TOTAL_NUMBER_OF_CM') : <strong>{{ !$cmListData->isEmpty() ? sizeof($cmListData) : 0 }}</strong>
                </span>
                @foreach($inf as $in)
                @if($i > 0)
                <li class="list-style-item-none display-inline-block margin-top-10">
                    <span class="label label-sm margin-bottom-10 {{ $colorClass[$i] }}">
                        {!! !empty($in) ? $in : '' !!} 
                    </span> 
                </li>
                @endif
                @php $i++ @endphp
                @endforeach
            </ul>
            @endif
            @endforeach
            @endif
        </div>
        <div class="col-md-2 text-right mb-10">
            <button class="btn purple-sharp btn-sm" type="button" id="import">
                <i class="fa fa-arrow-down" aria-hidden="true"></i> @lang('label.IMPORT')
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="vcenter text-center">@lang('label.SL')</th>
                        <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                        <th class="vcenter">@lang('label.RANK')</th>
                        <th class="vcenter">@lang('label.NAME')</th>
                        <th class="vcenter text-center">@lang('label.PHOTO')</th>
                        @if(!empty($factorList))
                        @foreach($factorList as $factorId => $factor)
                        <th class="vcenter text-center width-80">{{$factor}}</th>
                        @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $sl = 1; @endphp 
                    @forelse($cmListData as $cm) 
                    <tr>
                        <td class="vcenter text-center">{{ $sl++ }}</td>
                        <td class="vcenter">{{ $cm->personal_no }}</td>
                        <td class="vcenter">{{ $cm->rank }}</td>
                        <td class="vcenter">{!! Common::getFurnishedCmName($cm->full_name) !!}</td>
                        <td class="vcenter text-center" width="50px">
                            @if(!empty($cm->photo) && File::exists('public/uploads/cm/' . $cm->photo))
                            <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/cm/{{$cm->photo}}" alt="{!! Common::getFurnishedCmName($cm['full_name']) !!}"/>
                            @else
                            <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{!! Common::getFurnishedCmName($cm['full_name']) !!}"/>
                            @endif
                        </td>
                        @if(!empty($factorList))
                        @foreach($factorList as $factorId => $factor)
                        <td class="vcenter text-center width-80">
                            {!! Form::text('position['.$cm->cm_basic_id.']['.$factorId.']', !empty($cmIdAndPositonArr[$cm->cm_basic_id][$factorId])? $cmIdAndPositonArr[$cm->cm_basic_id][$factorId] : null, ['class' => 'form-control text-center width-inherit', 'readonly']) !!}
                        </td>
                        @endforeach
                        @endif
                    </tr>
                    @empty

                    @endforelse
                </tbody>
            </table>
            {{ Form::hidden('cm_id_and_position_arr', json_encode($cmIdAndPositonArr)) }}
            {{ Form::hidden('save_status', null, ['class' => 'save-status']) }}

        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12 text-center buttonHide">
                <button type="button" data-id='1' class="submit-form btn btn-circle blue-steel">
                    <i class="fa fa-file-text-o"></i> @lang('label.SUBMIT')
                </button>

                <!--            <button type="button" data-id='2' class="submit-form btn btn-circle green">
                                <i class="fa fa-lock"></i> @lang('label.SAVE_AND_LOCK')
                            </button>-->
                <a href="{{ URL::to('mutualAssessment') }}" class="btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
            </div>
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