<div class="row "> 
    <div class="col-md-10 col-md-offset-1">
        <span class="bold label label-sm label-blue-steel">
            @lang('label.NUMBER_OF_SYN_RELATED_TO_COURSE'): {{ !empty($courseSynInfo) ? count($courseSynInfo): '0'}}
        </span> &nbsp;
        <span class="bold label label-sm label-green-seagreen">
            @lang('label.NUMBER_OF_SYN_ASSIGN_TO_DS'): {{ !empty($syndicateInfo) ? count($syndicateInfo): '0'}}
        </span>
    </div>
</div>
@if(!empty($courseSynInfo) && !empty($syndicateInfo) && count($courseSynInfo) == count($syndicateInfo))
<div class="row margin-top-10">
    <div class="col-md-10 col-md-offset-1 text-center">


        <div class="portlet-body" style="padding: 8px !important">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="vcenter text-center">@lang('label.SL')</th>
                            <th class="vcenter">@lang('label.TERM_SYN')</th>
                            <th class="vcenter text-center">@lang('label.MARKING_DS_SYN') :<span class="text-danger"> *</span></th>
                            <th class="vcenter">@lang('label.MARKING_DS')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($syndicateInfo))
                        <?php
                        $sl = 0;
                        ?>
                        @foreach($syndicateInfo as $syndicateInfoMarking)
                        <?php //echo '<pre>';        print_r($syndicateInfoMarking); exit;?>
                        <tr>
                            <td class="vcenter text-center width-50">{{++$sl}}</td>
                            <td class="vcenter width-200 text-left">
                                {{!empty($syndicateInfoMarking['syn_code'])?$syndicateInfoMarking['syn_code']:''}}
                                <?php
                                $dsId = !empty($syndicateInfoMarking['ds_to_syn_ds_id']) ? $syndicateInfoMarking['ds_to_syn_ds_id'] : 0;
                                $dsName = !empty($syndicateInfoMarking['user_full_name']) ? $syndicateInfoMarking['user_full_name'] : '';
                                ?>
                                {!! Form::hidden('ds_name['.$dsId.']', $dsName,['id'=>'dsName_'.$dsId]) !!}
                            </td>
                            <td class="vcenter width-300">
                                <?php
                                $syndicateList2 = $syndicateList;
                                if (!empty($syndicateInfoMarking['ds_to_syn_ds_id']) && array_key_exists($syndicateInfoMarking['ds_to_syn_ds_id'], $syndicateList2)) {
                                    unset($syndicateList2[$syndicateInfoMarking['ds_to_syn_ds_id']]);
                                }
                                $selectedMarkingDs = !empty($markingDsToSynInfo) && array_key_exists($syndicateInfoMarking['syn_id'], $markingDsToSynInfo) ? $markingDsToSynInfo[$syndicateInfoMarking['syn_id']] : null;
//                                echo '<pre>';                                print_r($selectedMarkingDs); exit;

//                                $disabled = 'disabled';
//                                if (!empty($syndicateList2)) {
////                                echo '<pre>';                                print_r($syndicateInfoMarking['ds_to_syn_ds_id']); exit;
//                                    if (array_key_exists($syndicateInfoMarking['ds_to_syn_ds_id'], $syndicateList2)) {
//                                        $disabled = '';
//                                    }
//                                }
                                ?>
                                <select class="form-control width-inherit js-source-states ds-list marking-ds-to-syn" name="marking_ds_id[{!! $syndicateInfoMarking['syn_id']!!}]" id="ds-{!! $syndicateInfoMarking['syn_id'] !!}">
                                    <?php $i = 0; ?>
                                    @foreach($syndicateList2 as $dsKey => $dsValue)
                                    <?php
                                    //$selectData = !empty($selectedMarkingDs[$syndicateInfoMarking['ds_to_syn_ds_id']]) ? $selectedMarkingDs[$syndicateInfoMarking['ds_to_syn_ds_id']] : '0';
                                    $optionId = ($i == 0) ? '0' : $dsKey;
                                    $i++;
                                    ?>
                                    <option value="{!! $dsKey !!}" id="{!! $syndicateInfoMarking['syn_id'].'-'.$optionId !!}" 
                                            data-syndicate-id="{!! $syndicateInfoMarking['syn_id'] !!}" data-option-id="{!! $optionId !!}"
                                            <?php
                                            if ($selectedMarkingDs == $dsKey) {
                                                echo 'selected="selected"';
                                            } else {
                                                echo '';
                                            }
                                            ?>>{!! $dsValue !!} 
                                    </option>
                                    @endforeach
                                </select>
                                <!--{!! Form::select('marking_ds_id['.$syndicateInfoMarking['syn_id'].']', $syndicateList2, $selectedMarkingDs,  ['class' => 'form-control js-source-states width-inherit marking-ds-to-syn', 'id' => 'markingDsToSyn_' . $syndicateInfoMarking['syn_id']]) !!}-->
                            </td>
                            <td class="vcenter text-left ds-name">
                                {{!empty($markingDsToSynName) && array_key_exists($syndicateInfoMarking['syn_id'], $markingDsToSynName) ? $markingDsToSynName[$syndicateInfoMarking['syn_id']] : ''}}
                            </td>
                        </tr>
                        @endforeach
                        @else
                    <td>
                        @lang('label.NO_DATA_FOUND')
                    </td>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@else
<div class="row margin-top-10">
    <div class="col-md-10 col-md-offset-1">
        <div class="alert alert-danger alert-dismissable">
            <p><strong><i class="fa fa-bell-o fa-fw"></i> @lang('label.ALL_SYNDICATES_OF_THE_COURSE_ARE_NOT_ASSIGN_TO_DS_YET')</strong></p>
        </div>
    </div>
</div>
@endif

<div class = "form-actions">
    <div class = "col-md-offset-4 col-md-8">
        <button class = "button-submit btn btn-circle green" type="button">
            <i class = "fa fa-check"></i> @lang('label.SUBMIT')
        </button>
        <a href = "{{ URL::to('markingDsToSyn') }}" class = "btn btn-circle btn-outline grey-salsa">@lang('label.CANCEL')</a>
    </div>
</div>

<script type="text/javascript">
    $(function () {
//        // Start::Ds name show on change
//        $(document).on('change', ".marking-ds-to-syn", function (e) {
//                e.preventDefault();
//                var dsId = $(this).val();
//                var dsName = $('#dsName_'+dsId).val();
//                $(this).parent().siblings('.ds-name').text(dsName);
//            });
//        // End::Ds name show on change

        var selections = [];
        $('select.ds-list option:selected').each(function () {
            if ($(this).val() != '0') {
                var optionId = $(this).attr('data-option-id');
                selections.push(optionId);
            }
        });

        $('select.ds-list option').each(function () {
            $(this).attr('disabled', $.inArray($(this).val(), selections) > -1 && !$(this).is(":selected"));
        });

        $(document).on('change', 'select.ds-list', function () {
            var selections = [];
            $('select.ds-list option:selected').each(function () {
                if ($(this).val() != '0') {
                    var optionId = $(this).attr('data-option-id');
                    selections.push(optionId);
                }
            });

            $('select.ds-list option').each(function () {
                $(this).attr('disabled', $.inArray($(this).val(), selections) > -1 && !$(this).is(":selected"));
            });
            $("select.js-source-states").select2();
        });
    });
</script>