
@if(!empty($noOfChild) && $noOfChild != 0)
    <div class="col-md-12 margin-top-10">
        <div class="row">
            <div class="col-md-12 margin-bottom-10">
                <strong>@lang('label.CHILDREN_INFO')</strong>
            </div>
        </div>
        <div class="table-responsive" id="winterTrainingRowAdd">
            <table class="table table-bordered">
                <thead>
                    <tr class="info">
                        <th scope="col" class="vcenter text-center">@lang('label.SERIAL') </th>
                        <th scope="col" class="vcenter">@lang('label.NAME') <span class="text-danger"> *</span></th>
                        <th scope="col" class="vcenter text-center">@lang('label.DATE_OF_BIRTH') <span class="text-danger"> *</span></th>
                        <th scope="col" class="vcenter">@lang('label.SCHOOL_PROFESSION')</th>
                    </tr>
                </thead>
                @for($i = 1; $i <= $noOfChild; $i++)
                <?php
                $cKey = uniqid();
                ?>
                <tbody>
                <td class="vcenter text-center">{{ $i }}</td>
                <td class="vcenter">
                    {!! Form::text('child['.$cKey.'][name]', '',  ['class' => 'form-control', 'id' => 'childName'.$cKey]) !!}
                </td>
                <td class="vcenter text-right">
                    <div class="input-group date datepicker2">
                        {!! Form::text('child['.$cKey.'][dob]','', ['id'=> 'childDob'.$cKey, 'class' => 'form-control', 'placeholder' => 'DD/MM/YYYY']) !!} 
                        <span class="input-group-btn">
                            <button class="btn default reset-date" type="button" remove="childDob{{$cKey}}">
                                <i class="fa fa-times"></i>
                            </button>
                            <button class="btn default date-set" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                    </div>

                </td>
                <td class="vcenter">
                    {!! Form::text('child['.$cKey.'][school]', '',  ['class' => 'form-control', 'id' => 'childSchool'.$cKey]) !!}
                </td>

                </tbody>
                @endfor
            </table>
        </div>
    </div>
@endif
<!-- CUSTOM JS SCRIPTS -->
<script src="{{asset('public/js/custom.js')}}" type="text/javascript"></script>