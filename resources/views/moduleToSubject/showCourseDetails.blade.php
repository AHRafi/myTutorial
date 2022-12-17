{!! Form::hidden('data', json_encode($targetArr)) !!}
@if ($targetArr)
    <div class="col-md-12">
        <table class="table table-bordered table-hover" id="courseDetailsTable">
            <thead>
                <tr>
                    <th class="vcenter text-center">@lang('label.SL_NO')</th>
                    <th class="vcenter text-center">@lang('label.MODULE')</th>
                    <th class="text-center vcenter">@lang('label.SUBJECT')</th>

                </tr>
            </thead>
            <tbody>
                <?php $sl = 0; ?>
                @foreach ($targetArr as $item)
                    <tr>
                        <td class="vcenter text-center">{!! ++$sl !!}</td>
                        <td class="vcenter text-center">{{ $item->name }}</td>
                        <td class="vcenter text-center">{{ $item->title }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissable">
            <p><i class="fa fa-bell-o fa-fw"></i>@lang('label.NO_DATA_FOUND')</p>
        </div>
    </div>
@endif
<script type="text/javascript">
    $(document).ready(function() {
        // $('#courseDetailsTable').dataTable({
        //     "paging": true,
        //     "pageLength": 100,
        //     "info": false,
        //     "order": false
        // });
    });
</script>
