@extends('layouts.default.master')
@section('data_count')
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i>@lang('label.GS_LIST')
                </div>
                <div class="actions">
                    <a class="btn btn-default btn-sm create-new"
                        href="{{ URL::to('gs/create' . Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_GS')
                        <i class="fa fa-plus create-new"></i>
                    </a>
                </div>
            </div>
            <div class="portlet-body">

                <div class="row">
                    <!-- Begin Filter-->
                    {!! Form::open(['group' => 'form', 'url' => 'gs/filter', 'class' => 'form-horizontal']) !!}
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                                <div class="col-md-8">
                                    {!! Form::text('fil_search', Request::get('fil_search'), [
                                        'class' => 'form-control tooltips',
                                        'id' => 'filSearch',
                                        'title' => __('label.NAME'),
                                        'placeholder' => __('label.NAME'),
                                        'list' => 'gsName',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                    <datalist id="gsName">
                                        @if (!$nameArr->isEmpty())
                                            @foreach ($nameArr as $item)
                                                <option value="{{ $item->name }}" />
                                            @endforeach
                                        @endif
                                    </datalist>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form">
                                <button type="submit"
                                    class="btn btn-md green-seagreen btn-outline filter-submit margin-bottom-20">
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
                                           href="{{ action('RankController@index', ['download' => 'pdf', 'fil_search' => Request::get('fil_search'), 'fil_service_id' => Request::get('fil_service_id')]) }}">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </div>
                                </div>-->

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center vcenter">@lang('label.SL_NO')</th>
                                <th class="vcenter">@lang('label.NAME')</th>
                                <th class="text-center vcenter">@lang('label.UNIT_ORGANIZATION')</th>
                                {{-- <th class="text-center vcenter">@lang('label.DATE_OF_FIRST_SESSION_CONDUCT_TO_AFWC')</th>
                            <th class="text-center vcenter">@lang('label.NUMBER')</th>
                            <th class="text-center vcenter">@lang('label.ALT_NUMBER')</th>
                            <th class="text-center vcenter">@lang('label.EMAIL')</th>
                            <th class="text-center vcenter">@lang('label.PRESENT_ADDRESS')</th>
                            <th class="text-center vcenter">@lang('label.PERMANENT_ADDRESS')</th> --}}
                                <th class="text-center vcenter">@lang('label.IMAGE')</th>
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
                                @foreach ($targetArr as $target)
                                    <tr>
                                        <td class="text-center vcenter">{{ ++$sl }}</td>
                                        <td class="vcenter">{{ $target->name }}</td>
                                        <td class="text-center vcenter">{{ $target->unit }}</td>
                                        {{-- <td class="text-center vcenter">{{ Helper::formatDate($target->conduct_date) }}</td>
                            <td class="text-center vcenter">{{ $target->number }}</td>
                            <td class="text-center vcenter">{{ $target->alt_number }}</td>
                            <td class="text-center vcenter">{{ $target->email }}</td>
                            <td class="text-center vcenter">{{ $target->present_address }}</td>
                            <td class="text-center vcenter">{{ $target->permanent_address }}</td> --}}
                                        <td class="text-center vcenter">
                                            <?php if (!empty($target->photo) && File::exists('public/uploads/gs/'.$target->photo)) { ?>
                                            <img width="50" height="60"
                                                src="{{ URL::to('/') }}/public/uploads/gs/{{ $target->photo }}"
                                                alt="{{ $target->full_name }}" />
                                            <?php } else { ?>
                                            <img width="50" height="60"
                                                src="{{ URL::to('/') }}/public/img/unknown.png"
                                                alt="{{ $target->full_name }}" />
                                            <?php } ?>
                                        </td>
                                        <td class="text-center vcenter">
                                            @if ($target->status == '1')
                                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                            @else
                                                <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                            @endif
                                        </td>
                                        <td class="td-actions text-center vcenter">
                                            <div class="width-inherit">
                                                {{ Form::open(['url' => 'gs/' . $target->id . Helper::queryPageStr($qpArr)]) }}
                                                {{ Form::hidden('_method', 'DELETE') }}

                                                <a class="btn btn-xs btn-primary tooltips " title="Edit"
                                                    href="{{ URL::to('gs/' . $target->id . '/edit' . Helper::queryPageStr($qpArr)) }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button class="btn btn-xs btn-danger delete tooltips" title="Delete"
                                                    type="submit" data-placement="top" data-rel="tooltip"
                                                    data-original-title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>

                                                {{ Form::close() }}
                                                <button class="btn btn-xs btn-warning tooltips" title="GS Info" id="gsInfo" data-target="#showGsInfo" data-toggle="modal" data-id="{{$target->id}}" data-original-title="GS Info">
                                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="vcenter">@lang('label.NO_GS_FOUND')</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @include('layouts.paginator')
            </div>
        </div>
    </div>
    <div class="modal fade" id="showGsInfo" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div id="placeGsInfo">
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).on("click", "#gsInfo", function(e) {
            e.preventDefault();
            var gsId = $(this).attr("data-id");

            var options = {
                closeButton: true,
                debug: false,
                positionClass: "toast-bottom-right",
                onclick: null,
            };
            $.ajax({
                type: 'post',
                url: "{{ URL::to('gs/showGsInfo') }}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    gs_id: gsId
                },

                success: function(res) {
                    $("#placeGsInfo").html(res.html);
                    App.unblockUI();
                },
                error: function(jqXhr, ajaxOptions, thrownError) {

                    if (jqXhr.status == 400) {
                        var errorsHtml = '';
                        var errors = jqXhr.responseJSON.message;
                        $.each(errors, function(key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        toastr.error(errorsHtml, jqXhr.responseJSON.heading, options);
                    } else if (jqXhr.status == 401) {
                        toastr.error(jqXhr.responseJSON.message, '', options);
                    } else {
                        toastr.error('Error', 'Something went wrong', options);
                    }
                    App.unblockUI();
                }
            });

        });
    </script>

@stop
