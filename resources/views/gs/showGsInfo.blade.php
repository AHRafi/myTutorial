<div class="modal-content">
    <div class="modal-header clone-modal-header">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn red pull-right tooltips"
            title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
        <h3 class="modal-title text-center">
            @lang('label.GS_INFO')
        </h3>
    </div>

    <div class="modal-body">


        <!--BASIC ORDER INFORMATION-->
            <div class="row div-box-default">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 border-bottom-1-green-seagreen">
                            <h4><strong>@lang('label.BASIC_INFORMATION')</strong></h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6 col-sm-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="bold"  width="50%">@lang('label.NAME')</td>
                                    <td width="50%">{!! !empty($target->name)? $target->name:'' !!}</td>
                                </tr>
                                <tr>
                                    <td class="bold"  width="50%">@lang('label.UNIT')</td>
                                    <td width="50%">{!! !empty($target->unit)?$target->unit:'' !!}</td>
                                </tr>
                                <tr>
                                    <td class="bold"  width="50%">@lang('label.NUMBER')</td>
                                    <td width="50%">{!! !empty($target->number)?$target->number:'' !!}</td>
                                </tr>

                            </table>
                        </div>

                        <div class="col-md-6 col-lg-6 col-sm-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="bold"  width="50%">@lang('label.EMAIL')</td>
                                    <td width="50%">{!! !empty($target->email)?$target->email:'' !!}</td>
                                </tr>
                                <tr>
                                    <td class="bold"  width="50%">@lang('label.ALT_NUMBER')</td>
                                    <td width="50%">{!! !empty($target->alt_number)?$target->alt_number:'' !!}</td>
                                </tr>
                                <tr>
                                    <td class="bold"  width="50%">@lang('label.DATE_OF_CONDUCT')</td>
                                    <td width="50%">{!! !empty($target->conduct_date)? Helper::formatDate($target->conduct_date):'' !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <!--END OF BASIC ORDER INFORMATION-->

            <div class="row div-box-default">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 border-bottom-1-green-seagreen">
                            <h4><strong>@lang('label.SUMMARY_OF_EXPERTISE')</strong></h4>
                        </div>
                    </div>
                    <div class="row">


                        <div class="col-md-6 col-lg-6 col-sm-6">
                            <table class="table table-borderless">
                                <tr>

                                    <td width="50%">{!! !empty($target->summary_expertise)?$target->summary_expertise:'' !!}</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row div-box-default">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 border-bottom-1-green-seagreen">
                            <h4><strong>@lang('label.ADDRESS')</strong></h4>
                        </div>
                    </div>
                    <div class="row">


                        <div class="col-md-6 col-lg-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6><strong>@lang('label.PRESENT_ADDRESS')</strong></h6>
                                </div>
                            </div>
                            <table class="table table-borderless">
                                <tr>

                                    <td width="50%">{!! !empty($target->present_address)?$target->present_address:'' !!}</td>
                                </tr>

                            </table>
                        </div>
                        <div class="col-md-6 col-lg-6 col-sm-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6><strong>@lang('label.PERMANENT_ADDRESS')</strong></h6>
                                </div>
                            </div>
                            <table class="table table-borderless">
                                <tr>

                                    <td width="50%">{!! !empty($target->permanent_address)?$target->permanent_address:'' !!}</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>

            </div>

    </div>

    <div class="modal-footer">
        <button type="button" data-dismiss="modal" data-placement="left" class="btn dark btn-inline tooltips"
            title="@lang('label.CLOSE_THIS_POPUP')">@lang('label.CLOSE')</button>
    </div>
</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $(".tooltips").tooltip();
    });
</script>
