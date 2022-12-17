{!! Form::select('arms_service_id[]', $armsServiceList, [], ['multiple'=>'multiple', 'class' => 'form-control mt-multiselect', 'id' => 'armsServiceId']) !!}
<span class="text-danger">{{ $errors->first('arms_service_id') }}</span>

<script src="{{asset('public/js/custom.js')}}"></script>   
<script>

    $(function () {

        //Start:: Multiselect decorations
        var armsServiceAllSelected = false;
        $('#armsServiceId').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: '100%',
            maxHeight: 250,
            nonSelectedText: "@lang('label.ARMS_SERVICE_OPT')",
            //        enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                armsServiceAllSelected = true;
            },
            onChange: function () {
                armsServiceAllSelected = false;
            }
        });
    });
    //End:: Multiselect decorations
</script>