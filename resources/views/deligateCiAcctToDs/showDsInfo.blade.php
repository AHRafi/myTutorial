@if(!empty($prevDeligationInfo))
<div class="profile-userpic">
    @if(!empty($prevDeligationInfo->photo) && File::exists('public/uploads/user/' . $prevDeligationInfo->photo))
    <img src="{{URL::to('/')}}/public/uploads/user/{{$prevDeligationInfo->photo}}" class="text-center img-responsive pic-bordered border-default recruit-profile-photo-full"
         alt="{{ !empty($prevDeligationInfo->ds_name)? $prevDeligationInfo->ds_name:''}}" style="width: 150px;height: 180px;" />
    @else 
    <img src="{{URL::to('/')}}/public/img/unknown.png" class="text-center img-responsive pic-bordered border border-default recruit-profile-photo-full"
         alt="{{ !empty($prevDeligationInfo->ds_name)? $prevDeligationInfo->ds_name:'' }}"  style="width: 150px;height: 180px;" />
    @endif
</div>
<div class="profile-usertitle">
    @if(!empty($prevDeligationInfo->ds_name))
    <div class="text-center margin-bottom-10">
        <b>{{$prevDeligationInfo->ds_name}}</b>
    </div>
    @endif
    @if(!empty($prevDeligationInfo->appt))
    <div class="text-center margin-bottom-10">
        {{'('.$prevDeligationInfo->appt.')'}}
    </div>
    @endif
    <?php
    $labelColorPN = 'grey-mint';
    $fontColorPN = 'blue-hoki';

    if ($prevDeligationInfo->wing_id == 1) {
        $labelColorPN = 'green-seagreen';
    } elseif ($prevDeligationInfo->wing_id == 2) {
        $labelColorPN = 'white';
        $fontColorPN = 'white';
    } elseif ($prevDeligationInfo->wing_id == 3) {
        $labelColorPN = 'blue-madison';
    }
    ?>
    @if(!empty($prevDeligationInfo->personal_no))
    <div class="bold label label-square label-sm font-size-11 label-{{$labelColorPN}}">
        <span class="bg-font-{{$fontColorPN}}">{{$prevDeligationInfo->personal_no}}</span>
    </div>
    @endif
</div>

@endif
