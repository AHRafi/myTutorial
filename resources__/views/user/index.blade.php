@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user"></i>@lang('label.USER_LIST')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="{{ URL::to('user/create'.Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_USER')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">
            <div id="filterOpt">
                <!-- Begin Filter-->
                {!! Form::open(array('group' => 'form', 'url' => 'user/filter','class' => 'form-horizontal')) !!}

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                            <div class="col-md-8">
                                {!! Form::text('fil_search',  Request::get('fil_search'), ['class' => 'form-control tooltips', 'id' => 'filSearch', 'title' => 'Name', 'placeholder' => 'Short Name/Personal No.', 'list' => 'userName', 'autocomplete' => 'off']) !!} 
                                <datalist id="userName">
                                    @if (!$nameArr->isEmpty())
                                    @foreach($nameArr as $item)
                                    <option value="{{$item->official_name}}"/>
                                    @endforeach
                                    @endif
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="groupId">@lang('label.USER_GROUP')</label>
                            <div class="col-md-8">
                                {!! Form::select('fil_group_id', $groupList,  Request::get('fil_group_id'), ['class' => 'form-control js-source-states', 'id' => 'groupId']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="wingId">@lang('label.WING')</label>
                            <div class="col-md-8">
                                {!! Form::select('fil_wing_id', $wingList, Request::get('fil_wing_id'), ['class' => 'form-control js-source-states', 'id' => 'wingId']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="rankId">@lang('label.RANK')</label>
                            <div class="col-md-8">
                                {!! Form::select('fil_rank_id', $rankList, Request::get('fil_rank_id'), ['class' => 'form-control js-source-states', 'id' => 'rankId']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="appointmentId">@lang('label.APPOINTMENT')</label>
                            <div class="col-md-8">
                                {!! Form::select('fil_appointment_id', $appointmentList, Request::get('fil_appointment_id'), ['class' => 'form-control js-source-states', 'id' => 'appointmentId']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="form">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20 filter-btn">
                                <i class="fa fa-search"></i> @lang('label.FILTER')
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- End Filter -->
            </div>
            <!-- <div id="filterShow">
                <button type="button" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20" id="viewIcon">
                    <i class="fa fa-search"></i> @lang('label.FILTER')
                </button>
            </div>

                       <div class="row">
                            <div class="col-md-offset-8 col-md-4" id="manageEvDiv">
                                <a class="btn btn-icon-only btn-warning tooltips vcenter" title="Download PDF" 
                                   href="{{action('UserController@index', ['download'=>'pdf','fil_search' => Request::get('fil_search'), 'fil_group_id' => Request::get('fil_group_id'), 
                                          'fil_service_id' => Request::get('fil_service_id'),'fil_rank_id' => Request::get('fil_rank_id'),'fil_appointment_id' => Request::get('fil_appointment_id'),
                                      'fil_institute_id' => Request::get('fil_institute_id')])}}">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>-->


            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="vcenter">@lang('label.USER_GROUP')</th>
                            <th class="vcenter">@lang('label.RANK')</th>
                            <th class="vcenter">@lang('label.APPT')</th>
                            <th class="vcenter">@lang('label.PERSONAL_NO')</th>
                            <th class="vcenter">@lang('label.FULL_NAME')</th>
                            <th class="vcenter">@lang('label.OFFICIAL_NAME')</th>
                            <th class="vcenter">@lang('label.USERNAME')</th>
                            <th class="text-center vcenter">@lang('label.PHOTO')</th>
                            <th class="vcenter">@lang('label.EMAIL')</th>
                            <th class="vcenter">@lang('label.PHONE')</th>
                            <th class="text-center vcenter">@lang('label.STATUS')</th>
                            <th class="td-actions text-center vcenter">@lang('label.ACTION')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$targetArr->isEmpty())
                        <?php
                        $page = Request::get('page');
                        $page = empty($page) ? 1 : $page;
                        $sl = ($page - 1) * Session::get('paginatorCount');
                        ?>
                        @foreach($targetArr as $target)
                        <tr>
                            <td class="text-center vcenter">{{ ++$sl }}</td>
                            <td class="vcenter">{!! $target->group_name !!}</td>
                            <td class=" vcenter">{!! !empty($target->rank['code']) ? $target->rank['code'] : ''  !!}</td>
                            <td class="vcenter">{!! !empty($target->appointment['code']) ? $target->appointment['code'] : '' !!}</td>
                            <td class="vcenter">{!! $target->personal_no !!}</td>
                            <td class="vcenter">{!! $target->full_name !!}</td>
                            <td class="vcenter">{!! $target->official_name !!}</td>
                            <td class="vcenter">{!! $target->username !!}</td>
                            <td class="text-center vcenter" width="50px">
                                <?php if (!empty($target->photo) && File::exists('public/uploads/user/'.$target->photo)) { ?>
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/uploads/user/{{$target->photo}}" alt="{{ $target->full_name}}"/>
                                <?php } else { ?>
                                    <img width="50" height="60" src="{{URL::to('/')}}/public/img/unknown.png" alt="{{ $target->full_name}}"/>
                                <?php } ?>
                            </td>
                            <td class="vcenter">{!! $target->email !!}</td>
                            <td class="vcenter">{!! $target->phone !!}</td>
                            <td class="text-center vcenter">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                @else
                                <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                @endif
                            </td>
                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">
                                    {!! Form::open(array('url' => 'user/' . $target->id.'/'.Helper::queryPageStr($qpArr))) !!}
                                    {!!Form::hidden('_method', 'DELETE') !!}
                                    
                                    @if($target->group_id=='3' || $target->group_id=='4')
                                        <a class="btn btn-xs green-seagreen tooltips vcenter" title="View Profile" href="{!! URL::to('user/' . $target->id . '/profile'.Helper::queryPageStr($qpArr)) !!}">
                                            <i class="fa fa-user"></i>
                                        </a>
                                    @endif
                                    <a class="btn btn-xs btn-primary tooltips vcenter" title="Edit" href="{!! URL::to('user/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) !!}">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <button class="btn btn-xs btn-danger delete tooltips vcenter" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>

                                    {!! Form::close() !!}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="13" class="vcenter">@lang('label.NO_USER_FOUND')</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @include('layouts.paginator')
        </div>	
    </div>
</div>

<!--User modal -->
<div id="userInformation" class="modal container fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">
        <div id="showUserInformation">
            <!--ajax will be load here-->
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-outline dark">Close</button>
    </div>
</div>
<!--End user modal -->
<script type="text/javascript">
    $(function () {

//filter show hide work just pause


//        $('.filter-btn').on('click', function () {
//            $('.filter-info').show('slow');
//            $('#filterOpt').hide('slow');
//            $('#filterShow').show('slow');
//            return false;
//        })
//        $('#viewIcon').on('click', function () {
//            $('.filter-info').hide('slow');
//            $('#filterOpt').show('slow');
//            $('#filterShow').hide('slow');
//            return false;
//
//        })

    });


</script>
@stop