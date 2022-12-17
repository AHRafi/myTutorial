@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-calendar"></i>@lang('label.TRAINING_YEAR_LIST')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new"
                   href="{{ URL::to('trainingYear/create'.Helper::queryPageStr($qpArr)) }}">
                    @lang('label.CREATE_NEW_TRAINING_YEAR')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <!-- Begin Filter-->
                {!! Form::open(array('group' => 'form', 'url' => 'trainingYear/filter','class' => 'form-horizontal'))
                !!}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                            <div class="col-md-8">
                                {!! Form::text('fil_search', Request::get('fil_search'), ['class' => 'form-control
                                tooltips', 'id' => 'filSearch', 'title' => 'Name / Year', 'placeholder' => 'Name / Year'
                                , 'list' => 'tyName', 'autocomplete' => 'off']) !!}
                                <datalist id="tyName">
                                    @if (!$nameArr->isEmpty())
                                    @foreach($nameArr as $item)
                                    <option value="{{$item->year}}" />
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

            <!--            <div class="row">
                <div class="col-md-offset-8 col-md-4" id="manageEvDiv">
                    <a class="btn btn-icon-only btn-warning tooltips vcenter" title="Download PDF" 
                       href="{{action('TrainingYearController@index', ['download'=>'pdf','fil_search' => Request::get('fil_search')])}}">
                        <i class="fa fa-download"></i>
                    </a>
                </div>
            </div>-->

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center vcenter">@lang('label.SL_NO')</th>
                            <th class="vcenter">@lang('label.TRAINING_YEAR_NAME')</th>
                            <th class="vcenter">@lang('label.TRAINING_YEAR')</th>
                            <th class="text-center vcenter">@lang('label.TENURE')</th>
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
                            <td class="vcenter">{{ $target->name }}</td>
                            <td class="vcenter">{{ $target->year }}</td>
                            <td class="text-center vcenter">
                                <?php
                                $startDate = !empty($target->start_date) ? Helper::formatDate($target->start_date) : '';
                                $endDate = !empty($target->end_date) ? Helper::formatDate($target->end_date) : '';
                                ?>
                                {{ $startDate .' '. __('label.TO') .' '.  $endDate }}
                            </td>
                            <td class="text-center vcenter">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>

                                @elseif($target->status == '2')
                                <span class="label label-sm label-danger">@lang('label.CLOSED')</span>
                                @elseif($target->status == '0')
                                <span class="label label-sm label-warning">@lang('label.NOT_INITIATED')</span>
                                @endif
                            </td>
                            <td class="td-actions vcenter text-center">
                                <div class="width-inherit">
                                    {{ Form::open(array('url' => 'trainingYear/' . $target->id.'/'.Helper::queryPageStr($qpArr))) }}
                                    {{ Form::hidden('_method', 'DELETE') }}
                                    <?php
                                    if ($target->status == '0') {
                                        $icon = 'play';
                                        $title = __('label.ACTIVATE_THIS_TRAINING_YEAR');
                                        $actionTitle = __('label.YES_ACTIVATE');
                                        $status = 1;
                                    } elseif ($target->status == '1') {
                                        $icon = 'stop';
                                        $title = __('label.CLOSE_THIS_TRAINING_YEAR');
                                        $actionTitle = __('label.YES_CLOSE');
                                        $status = 2;
                                    }
//                                    elseif ($target->status == '2') {
//                                        $icon = 'fast-forward';
//                                        $title = __('label.REACTIVATE_THIS_COURSE');
//                                        $actionTitle = __('label.YES_REACTIVATE');
//                                        $status = 1;
//                                    }
                                    ?>
                                    @if ($target->status < '2' ) <button
                                        class="btn btn-xs btn-success change-status tooltips"
                                        title="{!! $title !!}" type="button" data-placement="top" data-rel="tooltip"
                                        data-id="{!! $target->id !!}" data-status="{!! $status !!}"
                                        data-original-title="{!! $title !!}" data-action="{!! $actionTitle !!}">
                                        <i class="fa fa-{!! $icon !!}"></i>
                                    </button>
                                    @endif
                                    <a class="btn btn-xs btn-primary tooltips" title="Edit"
                                       href="{{ URL::to('trainingYear/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips" title="Delete"
                                            type="submit" data-placement="top" data-rel="tooltip"
                                            data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    {{ Form::close() }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="vcenter">@lang('label.NO_TRAINING_YEAR_FOUND')</td>
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
        $(document).on('click', '.change-status', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var status = $(this).data('status');
            var actionTitle = $(this).data('action');
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null,
            };

            swal({
                title: 'Are you sure?',
                   
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: actionTitle,
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('trainingYear/changeStatus')}}",
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