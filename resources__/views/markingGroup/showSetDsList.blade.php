 
<div class="row margin-bottom-10 selected-ds-list">

    @if(!$targetArr->isEmpty())
    <div class="col-md-12 margin-top-10">
        <div class="table-responsive max-height-250 webkit-scrollbar">
            <table class="table table-bordered table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th class="vcenter text-center">@lang('label.SL')</th>
                        <th class="vcenter text-center">@lang('label.PHOTO')</th>
                        <th class="vcenter">@lang('label.NAME')</th>
                        @if($eventAssessmentMarkingDataArr->isEmpty())
                        <th class="vcenter text-center">@lang('label.REMOVE')</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="selected-ds-body">
                    <?php $dsSl = 0; ?>
                    @foreach($targetArr as $target)

                    <tr>
                        {{ Form::hidden('selected_ds['.$target->id.']', $target->id, array('id' => $target->id, 'class' => 'selected-ds')) }}
                        <td class="vcenter text-center initial-serial-ds">{!! ++$dsSl !!}</td>
                        <td class="text-center vcenter" width="50px">
                            <?php if (!empty($target->photo && File::exists('public/uploads/user/' . $target->photo))) { ?>
                                <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$target->photo}}" alt="{{ $target->full_name}}"/>
                            <?php } else { ?>
                                <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $target->full_name}}"/>
                            <?php } ?>
                        </td>
                        <td class="vcenter">{{$target->ds_name}}</td>
                        @if($eventAssessmentMarkingDataArr->isEmpty())
                        <td class="text-center"> 
                            <button class="btn btn-danger remove-selected-ds tooltips" type="button" data-id="{!! $target->id !!}" title="@lang('label.REMOVE')">×</button>
                        </td>
                        @endif
                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-success alert-dismissable">
            <p><strong> {!! __('label.TOTAL_NO_OF_SELECTED_DS') !!} : &nbsp;<span class="selected-ds-no">{!! sizeof($targetArr) !!}</span></strong></p>
        </div>
    </div>
    @else
    <div class="col-md-12 margin-top-10">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> {!! __('label.NO_DS_IS_SELECTED_FOR_THIS_MARKING_GROUP_YET') !!}</strong></p>
        </div>
    </div>
    @endif
</div>

<!-- Modal end-->
<script type='text/javascript'></script>
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>

