@extends('layouts.default.master')
@section('data_count')
<div class="col-md-12">
    @include('layouts.flash')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart"></i>@lang('label.LESSON_LIST')
            </div>
            <div class="actions">
                <a class="btn btn-default btn-sm create-new" href="{{ URL::to('lesson/create'.Helper::queryPageStr($qpArr)) }}"> @lang('label.CREATE_NEW_LESSON')
                    <i class="fa fa-plus create-new"></i>
                </a>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <!-- Begin Filter-->
                {!! Form::open(array('group' => 'form', 'url' => 'lesson/filter','class' => 'form-horizontal')) !!}
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="filSearch">@lang('label.SEARCH')</label>
                            <div class="col-md-8">
                                {!! Form::text('fil_search',  Request::get('fil_search'), ['class' => 'form-control tooltips', 'id' => 'filSearch', 'title' => __('label.TITLE') , 'placeholder' => __('label.TITLE'), 'list' => 'lessonName', 'autocomplete' => 'off']) !!}
                                <datalist id="lessonName">
                                    @if (!$nameArr->isEmpty())
                                    @foreach($nameArr as $item)
                                    <option value="{{$item->title}}" />
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
                            <th class="vcenter">@lang('label.LESSON_TITLE')</th>
                            {{-- <th class="vcenter">@lang('label.PROFILE_COMPLITION')</th> --}}
                            <th class="vcenter text-center">@lang('label.DATE_OF_EVAL')</th>
                            <th class="vcenter text-center">@lang('label.DEADLINE_OF_EVAL')</th>
                            <th class="vcenter text-center">@lang('label.CONSIDER_GS_FEEDBACK')</th>
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
                            <td class="vcenter">{{ $target->title }}</td>


                            {{-- <td class="vcenter">
                                @if($target->related_consideration && $target->related_grading && $target->related_comment)
                                <div class="cursor-pointer progress label-gray-mint ">
                                    <div class="progress-bar progress-bar-striped label-green-sharp complition" role="progressbar" aria-valuenow="100" data-status="1000"
                                         data-target="#showLessonProfileStatus" data-toggle="modal" data-id="{{$target->id}}" aria-valuemin="0" aria-valuemax="100" style="width: 100%">100%</div>
                                </div>
                                @elseif( !$target->related_consideration && $target->related_grading && $target->related_comment )
                                <div class="cursor-pointer progress label-gray-mint ">
                                    <div class="progress-bar progress-bar-striped label-green-sharp complition" role="progressbar" aria-valuenow="66" data-status="66"
                                         data-target="#showLessonProfileStatus" data-toggle="modal" data-id="{{$target->id}}" aria-valuemin="0" aria-valuemax="100" style="width: 66%">66%</div>
                                </div>
                                @elseif($target->related_consideration && !$target->related_grading && $target->related_comment )
                                <div class="cursor-pointer progress label-gray-mint ">
                                    <div class="progress-bar progress-bar-striped label-green-sharp complition" role="progressbar" aria-valuenow="66" data-status="66"
                                         data-target="#showLessonProfileStatus" data-toggle="modal" data-id="{{$target->id}}" aria-valuemin="0" aria-valuemax="100" style="width: 66%">66%</div>
                                </div>
                                @elseif($target->related_consideration && $target->related_grading && !$target->related_comment )
                                <div class="cursor-pointer progress label-gray-mint ">
                                    <div class="progress-bar progress-bar-striped label-green-sharp complition" role="progressbar" aria-valuenow="66" data-status="66"
                                         data-target="#showLessonProfileStatus" data-toggle="modal" data-id="{{$target->id}}" aria-valuemin="0" aria-valuemax="100" style="width: 66%">66%</div>
                                </div>
                                @elseif($target->related_consideration && !$target->related_grading && !$target->related_comment )
                                <div class="cursor-pointer progress label-gray-mint ">
                                    <div class="progress-bar progress-bar-striped label-green-sharp complition" role="progressbar" aria-valuenow="33" data-status="33"
                                         data-target="#showLessonProfileStatus" data-toggle="modal" data-id="{{$target->id}}" aria-valuemin="0" aria-valuemax="100" style="width: 33%">33%</div>
                                </div>
                                @elseif( !$target->related_consideration && $target->related_grading && !$target->related_comment )
                                <div class="cursor-pointer progress label-gray-mint ">
                                    <div class="progress-bar progress-bar-striped label-green-sharp complition" role="progressbar" aria-valuenow="33" data-status="33"
                                         data-target="#showLessonProfileStatus" data-toggle="modal" data-id="{{$target->id}}" aria-valuemin="0" aria-valuemax="100" style="width: 33%">33%</div>
                                </div>
                                @elseif( !$target->related_consideration && !$target->related_grading && $target->related_comment )
                                <div class="cursor-pointer progress label-gray-mint ">
                                    <div class="progress-bar progress-bar-striped label-green-sharp complition" role="progressbar" aria-valuenow="33" data-status="33"
                                         data-target="#showLessonProfileStatus" data-toggle="modal" data-id="{{$target->id}}" aria-valuemin="0" aria-valuemax="100" style="width: 33%">33%</div>
                                </div>
                                @else
                                <div class="cursor-pointer progress label-gray-mint ">
                                    <div class="progress-bar progress-bar-striped label-green-sharp complition" role="progressbar" aria-valuenow="0" data-status="0"
                                         data-target="#showLessonProfileStatus" data-toggle="modal" data-id="{{$target->id}}" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%</div>
                                </div>
                                @endif
                            </td> --}}



                            <td class="vcenter text-center">{{ Helper::formatDate($target->eval_date) }}</td>
                            <td class="vcenter text-center">{{ Helper::formatDate($target->eval_deadline) }}</td>
                            <td class="text-center vcenter">
                                @if($target->consider_gs_feedback == '1')
                                <span class="label label-sm label-success">@lang('label.YES')</span>
                                @else
                                <span class="label label-sm label-warning">@lang('label.NO')</span>
                                @endif
                            </td>
                            <td class="text-center vcenter">{{ $target->order }}</td>

                            <td class="text-center vcenter">
                                @if($target->status == '1')
                                <span class="label label-sm label-success">@lang('label.ACTIVE')</span>
                                @else
                                <span class="label label-sm label-warning">@lang('label.INACTIVE')</span>
                                @endif
                            </td>
                            <td class="td-actions text-center vcenter">
                                <div class="width-inherit">
                                    {{ Form::open(array('url' => 'lesson/' . $target->id.Helper::queryPageStr($qpArr))) }}
                                    {{ Form::hidden('_method', 'DELETE') }}

                                    <a class="btn btn-xs btn-primary tooltips " title="Edit" href="{{ URL::to('lesson/' . $target->id . '/edit'.Helper::queryPageStr($qpArr)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger delete tooltips" title="Delete" type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    {{ Form::close() }}

                                    <a class="btn btn-xs btn-warning tooltips vcenter " title="@lang('label.CLICK_HERE_TO_RELATE_OBJECTIVE_GRADING_CONSIDERATION_COMMENT')"
                                       href="{{ URL::to('lesson/' . $target->id . '/manageLesson') }}">
                                        <i class="fa fa-cog"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="vcenter">@lang('label.NO_LESSON_FOUND')</td>
                        </tr>
                        @endif
                    </tbody>

                </table>
            </div>
            @include('layouts.paginator')
        </div>
    </div>
</div>

<div class="modal fade" id="showLessonProfileStatus" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="placeLessonProfileStatus">
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).on("click", ".complition", function (e) {
        e.preventDefault();
        var lessonId = $(this).attr("data-id");

        var options = {
            closeButton: true,
            debug: false,
            positionClass: "toast-bottom-right",
            onclick: null,
        };
        $.ajax({
            type: 'post',
            url: "{{ URL::to('lesson/showProfileCompitionStatus') }}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                lesson_id: lessonId
            },

            success: function (res) {
                $("#placeLessonProfileStatus").html(res.html);
                App.unblockUI();
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
