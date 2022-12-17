@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.CONTENT_CATEGORY_MANAGEMENT')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="{{ URL::to('contentCategory/create'.Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_CONTENT_CATEGORY')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <!-- Begin Filter-->
                {!! Form::open(array('group' => 'form', 'url' => 'contentCategory/filter','class' => 'form-horizontal')) !!}
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="search">@lang('label.SEARCH')</label>
                            <div class="col-md-8">
                                {!! Form::text('search',  Request::get('search'), ['class' => 'form-control tooltips', 'id' => 'search', 'title' => 'Name', 'placeholder' => 'Name', 'list' => 'contentCategoryName', 'autocomplete' => 'off']) !!} 
                                <datalist id="contentCategoryName">
                                    @if (!$nameArr->isEmpty())
                                    @foreach($nameArr as $item)
                                    <option value="{{$item->name}}" />
                                    @endforeach
                                    @endif
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form">
                            <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                <i class="fa fa-search"></i> @lang('label.FILTER')
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- End Filter -->
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="vcenter">@lang('label.NAME')</th>
                            <th class="vcenter">@lang('label.SHORT_DESCRIPTION')</th>
                            <th class="vcenter">@lang('label.PARENT_CATEGORY')</th>
                            <th class="text-center vcenter">@lang('label.RELATE_TO_THE_COMPARTMENTS')</th>
                            <th class="text-center vcenter">@lang('label.ORDER')</th>
                            <th class="text-center vcenter">@lang('label.STATUS')</th>
                            <th class="vcenter text-center">@lang('label.ACTION')</th>
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
                            <td class="vcenter">{{ $target->name }}</td>
                            <td class="vcenter">{{ $target->short_description }}</td>
                            <td class="vcenter">
                                <?php
                                if (isset($parentArr[$target->id])) {
                                    echo $parentArr[$target->id];
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                            <td class="text-center vcenter">
                                <?php $comptArr = !empty($target->related_compartment) ? explode(',', $target->related_compartment) : []; ?>
                                @if(!empty($comptArr))
                                @foreach($comptArr as $comptId)
                                <?php
                                $compt = !empty($comptId) && !empty($compartmentList[$comptId]) ? $compartmentList[$comptId] : __('label.N_A');
                                $comptColor = empty($comptId) ? 'grey-mint' : ($comptId == '1' ? 'purple-sharp' : ($comptId == '2' ? 'blue-steel' : ($comptId == '3' ? 'yellow' : 'grey-mint')));
                                ?>
                                <span class="label label-sm label-{{$comptColor}}">{!! $compt !!}</span>&nbsp; 
                                @endforeach
                                @endif
                            </td>
                            <td class="text-center vcenter">{{ $target->order }}</td>
                            <td class="vcenter text-center">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                @else
                                <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                @endif
                            </td>
                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">  
                                    {{ Form::open(array('url' => 'contentCategory/' . $target->id.'/'.Helper::queryPageStr($qpArr), 'class' => 'delete-form-inline')) }}
                                    <a class="btn btn-xs btn-primary tooltips vcenter" title="Edit" href="{{ URL::to('contentCategory/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    {{ Form::hidden('_method', 'DELETE') }}
                                    <button class="btn btn-xs btn-danger delete tooltips vcenter" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    {{ Form::close() }}

                                </div>
                            </td>

                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="3" class="vcenter">@lang('label.CONTENT_CATEGORY_NOT_FOUND')</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @include('layouts.paginator')
        </div>	
    </div>
</div>
@stop