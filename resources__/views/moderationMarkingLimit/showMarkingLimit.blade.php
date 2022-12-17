<div class="row margin-top-10">
    <div class="col-md-7 table-responsive webkit-scrollbar col-md-offset-2">
        <table class="table table-bordered table-hover" id="dataTable">
            <thead>
                <tr>
                    <th class="text-center vcenter">@lang('label.SL_NO')</th>
                    <th class="vcenter">@lang('label.TERM')</th>
<!--                    <th class="text-center vcenter">@lang('label.MODERATION_MARKING_LIMIT')</th>
                </tr>
                <tr>-->
                    <th class="text-center vcenter">@lang('label.CI_MODERAION')</th>
                    <!--<th class="text-center vcenter">@lang('label.COMDT_MODERAION')</th>-->
                </tr>
            </thead>
            <tbody>
                @if(!$termArr->isEmpty())
                <?php $sl = 0; ?>
                @foreach($termArr as $term)
                <?php
                $ciMod = !empty($prevDataArr[$term->id]['ci_mod']) ? $prevDataArr[$term->id]['ci_mod'] : null;
                $comdtMod = !empty($prevDataArr[$term->id]['comdt_mod']) ? $prevDataArr[$term->id]['comdt_mod'] : null;
                ?>
                <tr>
                    <td class="text-center vcenter width-80">{!! ++$sl !!}</td>
                    <td class="vcenter width-250">
                        <div class="width-inherit">{!! $term->name ?? '' !!}</div>
                    </td>
                    <td class="text-center vcenter width-150">
                        <div class="input-group bootstrap-touchspin width-inherit">
                            <span class="input-group-addon bootstrap-touchspin-prefix bold">&plusmn;</span>
                            {!! Form::text('mod['.$term->id.'][ci_mod]', $ciMod, ['id'=> 'ciMod_' . $term->id, 'class' => 'form-control text-right integer-decimal-only text-input-width-100-per ci-mod ci-mod-' . $term->id, 'data-term-id' => $term->id]) !!}
                            <span class="input-group-addon bootstrap-touchspin-postfix bold">%</span>
                        </div>
                    </td>
<!--                    <td class="text-center vcenter width-150">
                        <div class="input-group bootstrap-touchspin width-inherit">
                            <span class="input-group-addon bootstrap-touchspin-prefix bold">&plusmn;</span>
                            {!! Form::text('mod['.$term->id.'][comdt_mod]', $comdtMod, ['id'=> 'comdtMod_' . $term->id, 'class' => 'form-control text-right integer-decimal-only text-input-width-100-per comdt-mod comdt-mod-' . $term->id, 'data-term-id' => $term->id]) !!}
                            <span class="input-group-addon bootstrap-touchspin-postfix bold">%</span>
                        </div>
                    </td>-->
                </tr>
                {!! Form::hidden('mod['.$term->id.'][term_name]', $term->name ?? '', ['id' => 'termName_' . $term->id]) !!}
                @endforeach
                @else
                <tr>
                    <td colspan="4">@lang('label.NO_TERM_IS_ASSIGNED_TO_THIS_COURSE')</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<div class = "form-actions margin-top-10">
    <div class = "col-md-offset-4 col-md-8">
        <button class = "button-submit btn btn-circle green" type="button">
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href = "{{ URL::to('moderationMarkingLimit') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>

<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {

    //start: for CI mod
    $(document).on('keyup', '.ci-mod', function () {
        modRange(this);
    });
    //start: for CI mod
    
    //start: for Comdt mod
    $(document).on('keyup', '.comdt-mod', function () {
        modRange(this);
    });
    //start: for Comdt mod
});

function modRange(modClass){
    var mod = $(modClass).val();
        if (mod > 100) {
            swal({
                title: "@lang('label.PLEASE_PUT_A_VALUE_UP_TO_100') ",
                text: "",
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('label.OK')",
                closeOnConfirm: true,
                closeOnCancel: true,
            }, function (isConfirm) {
                if (isConfirm) {
                    $(modClass).val('');
                }
            });
            return false;
        }
}
</script>

