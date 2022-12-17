@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-calendar"></i>@lang('label.MIL_COURSE_LIST')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new"
                    href="{{ URL::to('milCourse/create'.Helper::queryPageStr($qpArr)) }}">
                    @lang('label.CREATE_NEW_MIL_COURSE')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <!-- Begin Filter-->
                {!! Form::open(array('group' => 'form', 'url' => 'milCourse/filter','class' => 'form-horizontal'))
                !!}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                            <div class="col-md-8">
                                {!! Form::text('fil_search', Request::get('fil_search'), ['class' => 'form-control
                                tooltips', 'id' => 'filSearch', 'title' => 'Name', 'placeholder' => 'Name',
                                'list' => 'milCourseName', 'autocomplete' => 'off']) !!}
                                <datalist id="milCourseName">
                                    @if (!$nameArr->isEmpty())
                                    @foreach($nameArr as $item)
                                    <option value="{{$item->name}}" />
                                    @endforeach
                                    @endif
                                </datalist>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="categoryId">@lang('label.CATEGORY')</label>
                            <div class="col-md-8">
                                {!! Form::select('fill_category_id', $categoryList,  Request::get('fill_category_id'), ['class' => 'form-control js-source-states', 'id' => 'categoryId']) !!}
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
                            <th class="vcenter">@lang('label.SHORT_INFO')</th>
                            <th class="vcenter text-center">@lang('label.CATEGORY')</th>
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
                        <tr>
                            <td class="text-center vcenter">{{ ++$sl }}</td>
                            <td class="vcenter">{{ $target->name }}</td>
                            <td class="vcenter">{{ $target->short_info }}</td>
                            <td class="vcenter text-center">
                                <?php
                                $cat = !empty($target->category_id) && !empty($categoryList[$target->category_id]) ? $categoryList[$target->category_id] : __('label.N_A');
                                $catColor = empty($target->category_id) ? 'grey-mint' : ($target->category_id == '1' ? 'green-mil' : ($target->category_id == '2' ? 'purple-wisteria' : ($target->category_id == '3' ? 'blue-steel' : 'grey-mint')));
                                ?>
                                <span class="label label-sm label-{{$catColor}}">{{$cat}}</span>
                            </td>
                            <td class="text-center vcenter">{{ $target->order }}</td>
                            <td class="text-center vcenter">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                @elseif($target->status == '0')
                                <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                @else
                                <span class="label label-sm label-purple-sharp">@lang('label.CLOSED')</span>
                                @endif
                            </td>
                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">
                                    @if ($target->status !='2' )
                                    {{ Form::open(array('url' => 'milCourse/' . $target->id.'/'.Helper::queryPageStr($qpArr))) }}
                                    {{ Form::hidden('_method', 'DELETE') }}
                                    <a class="btn btn-xs btn-primary tooltips" title="Edit"
                                        href="{{ URL::to('milCourse/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips" title="Delete"
                                        type="submit" data-placement="top" data-rel="tooltip"
                                        data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    {{ Form::close() }}
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="vcenter">@lang('label.NO_MIL_COURSE_FOUND')</td>
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
        $(document).on('click', '.close-btn', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null,
            };

            swal({
                title: 'Are you sure,You want to Close?',
                   
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Close',
                cancelButtonText: 'No, Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{URL::to('milCourse/close')}}",
                        type: "POST",
                        datatype: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                        },

                        success: function (res) {
                            toastr.success(res.message, 'Success', options);
                            //setTimeout(location.reload.bind(location), 1000);
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