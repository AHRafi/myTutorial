
{!! Form::select('cm_id[]', $cmArr, Request::get('cm_id'),  ['multiple'=>'multiple', 'class' => 'form-control', 'id' => 'cmId', 'data-width' => '100%']) !!}
<script>

    $(function () {
        //START:: Multiselect CM
        var cmAllSelected = false;
        $('#cmId').multiselect({
            numberDisplayed: 0,
            includeSelectAllOption: true,
            buttonWidth: 'inherit',
            maxHeight: 250,
            nonSelectedText: "@lang('label.SELECT_CM_OPT')",
            enableCaseInsensitiveFiltering: true,
            onSelectAll: function () {
                cmAllSelected = true;
            },
            onChange: function () {
                cmAllSelected = false;
            }
        });
//END:: Multiselect CM
    });

</script>