@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.SUB_SUB_EVENT_LIST')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="{{ URL::to('subSubEvent/create'.Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_SUB_SUB_EVENT')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <div class="col-md-12">
                    <!-- Begin Filter-->
                    {!! Form::open(array('group' => 'form', 'url' => 'subSubEvent/filter','class' => 'form-horizontal')) !!}
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                                <div class="col-md-8">
                                    {!! Form::text('fil_search',  Request::get('fil_search'), ['class' => 'form-control tooltips', 'id' => 'filSearch', 'title' => 'Code', 'placeholder' => 'Code', 'list' => 'subSubEventCode', 'autocomplete' => 'off']) !!} 
                                    <datalist id="subSubEventCode">
                                        @if (!$nameArr->isEmpty())
                                        @foreach($nameArr as $item)
                                        <option value="{{$item->event_code}}" />
                                        @endforeach
                                        @endif
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="hidden">@lang('label.HIDDEN')</label>
                                <div class="col-md-8">
                                    {!! Form::select('hidden', $hiddenOptionList,  Request::get('hidden'), ['class' => 'form-control js-source-states', 'id' => 'hideShow']) !!}
                                    <span class="text-danger">{{ $errors->first('hidden') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <div class="form">
                                <button type="submit" class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
                                    <i class="fa fa-search"></i> @lang('label.FILTER')
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                    </div>
                    {!! Form::close() !!}
                    <!-- End Filter -->
                </div>
            </div>

            <!--            <div class="row">
                            <div class="col-md-offset-8 col-md-4" id="manageEvDiv">
                                <a class="btn btn-icon-only btn-warning tooltips vcenter" title="Download PDF" 
                                   href="{{action('RankController@index', ['download'=>'pdf', 'fil_search' => Request::get('fil_search'), 'fil_service_id' => Request::get('fil_service_id') ])}}">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>-->

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="vcenter">@lang('label.SUB_SUB_EVENT_CODE')</th>
                            <th class="vcenter">@lang('label.SUB_SUB_EVENT_DETAIL')</th>
                            <th class="text-center vcenter">@lang('label.ORDER')</th>
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
                        <?php
                        $color = 'yellow';
                        $title = __('label.CLICK_HERE_TO_HIDE_THIS_RECORD');
                        $icon = 'eye-slash';
                        $iconFront = '';
                        $status = '1';
                        if (!empty($target->hidden) && $target->hidden == '1') {
                            $color = 'purple-wisteria';
                            $title = __('label.CLICK_HERE_TO_SHOW_THIS_RECORD');
                            $icon = 'eye';
                            $status = '0';
                            $iconFront = '<span class="badge badge-yellow tooltips bold" title="' . __('label.HIDDEN') . '">'
                                    . '<i class="fa fa-eye-slash"></i>'
                                    . '</span>';
                        }
                        ?>
                        <tr>
                            <td class="text-center vcenter">{{ ++$sl }}</td>
                            <td class="vcenter">{{ $target->event_code }}</td>
                            <td class="vcenter">{{ $target->event_detail }}</td>
                            <td class="text-center vcenter">{{ $target->order }}</td>
                            <td class="text-center vcenter">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                @else
                                <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                @endif
                            </td>
                            <td class="td-actions vcenter text-center">
                                <div class="width-inherit">
                                    {{ Form::open(array('url' => 'subSubEvent/' . $target->id.Helper::queryPageStr($qpArr))) }}
                                    {{ Form::hidden('_method', 'DELETE') }}

                                    <a class="btn btn-xs btn-primary tooltips " title="Edit" href="{{ URL::to('subSubEvent/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    
                                    <button class="btn btn-xs {{$color}} bold tooltips hide-show-btn"
                                            title="{{$title}}" type="button" data-id="{!! $target->id !!}" data-status="{{$status}}">
                                        <i class="fa fa-{{$icon}}"></i>
                                    </button>
                                    {{ Form::close() }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="vcenter">@lang('label.NO_SUB_SUB_EVENT_FOUND')</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @include('layouts.paginator')
        </div>	
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(document).on('click', '.hide-show-btn', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null,
            };
            var hide = (status == '1') ? 'Hide' : 'Show';
            swal({
                title: 'Are you sure, You want to ' + hide + '?',

                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, ' + hide,
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('subSubEvent/hideShow')}}",
                        type: "POST",
                        datatype: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                            status: status,
                        },

                        success: function (res) {
                            toastr.success(res.message, 'Success', options);
                            setTimeout(location.reload.bind(location), 1000);
                        },

                        error: function (jqXhr, ajaxOptions, thrownError) {
                            if (jqXhr.status == 400) {
                                var errorsHtml = '';
                                var errors = jqXhr.responseJSON.message;
                                $.each(errors, function (key, value) {
                                    errorsHtml += '<li>' + value + '</li>';
                                });
                                toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                            } else if (jqXhr.status == 401) {
                                //toastr.error(jqXhr.responseJSON.message, '', options);
                                var errors = jqXhr.responseJSON.message;
                                var errorsHtml = 'SI Impr Mks have not been Locked for following Wing :';
                                if (typeof (errors) === 'object') {
                                    $.each(errors, function (key, value) {
                                        errorsHtml += '<li>' + value + '</li>';
                                    });
                                    toastr.error(errorsHtml, '', options);
                                } else {
                                    toastr.error(jqXhr.responseJSON.message, '', options);
                                }
                            } else {
                                toastr.error('Error', 'Something went wrong', options);
                            }
                            App.unblockUI();
                        }
                    });
                }
            });
        });

    });

</script>
@stop