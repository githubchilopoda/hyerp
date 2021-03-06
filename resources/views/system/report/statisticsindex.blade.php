@extends('navbarerp')

@section('main')
    <div class="panel-heading">
        {{--
        <div class="pull-right" style="padding-top: 4px;">
            <a href="{{ URL::to('system/depts') }}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> {{'部门管理', [], 'layouts'}}</a>
        </div>
        --}}
    </div>

    <div class="panel-body">
        {!! Form::open(['url' => '/system/report/' . $report->id . '/export', 'class' => 'pull-right form-inline']) !!}
        <div class="form-group-sm">
            @foreach($input as $key=>$value)
                {!! Form::hidden($key, $value) !!}
            @endforeach
            {!! Form::submit('导出到Excel', ['class' => 'btn btn-default btn-sm']) !!}
        </div>
        {!! Form::close() !!}

        {!! Form::open(['url' => '/system/report/' . $report->id . '/statistics', 'class' => 'pull-right form-inline']) !!}
        <div class="form-group-sm">
            {{-- 根据不同报表设置不同搜索条件 --}}
            @if ($report->name == "po_warehouse_percent")
                {!! Form::label('arrivaldatelabel', '到货时间:', ['class' => 'control-label']) !!}
                {!! Form::date('datearravalfrom', null, ['class' => 'form-control']) !!}
                {!! Form::label('arrivaldatelabelto', '-', ['class' => 'control-label']) !!}
                {!! Form::date('datearravalto', null, ['class' => 'form-control']) !!}

                {!! Form::text('key', null, ['class' => 'form-control', 'placeholder' => '对应项目名称']) !!}
            @elseif ($report->name == "so_factory_analysis")
            @elseif ($report->name == "so_height_statistics_detail")
                {!! Form::select('orderid', $poheadList_hxold, null, ['class' => 'form-control', 'placeholder' => '--请选择--']) !!}
            @elseif ($report->name == "po_statistics")
                {!! Form::label('signdatelabel', '签订日期:', ['class' => 'control-label']) !!}
                {!! Form::date('signdatefrom', null, ['class' => 'form-control']) !!}
                {!! Form::label('signdatelabelto', '-', ['class' => 'control-label']) !!}
                {!! Form::date('signdateto', null, ['class' => 'form-control']) !!}
                {!! Form::select('arrivalstatus', array(0 => '未到货', 1 => '部分到货', 2 => '全部到货'), null, ['class' => 'form-control', 'placeholder' => '--到货状态--']) !!}
                {!! Form::select('paidstatus', array(0 => '未付款', 1 => '部分付款', 2 => '全部付款'), null, ['class' => 'form-control', 'placeholder' => '--付款状态--']) !!}
                {!! Form::select('ticketedstatus', array(0 => '未开票', 1 => '部分开票', 2 => '全部开票'), null, ['class' => 'form-control', 'placeholder' => '--开票状态--']) !!}
            @elseif ($report->name == "in_batch")
                {!! Form::text('batch', null, ['class' => 'form-control', 'placeholder' => '批号']) !!}
            @elseif ($report->name == "so_cost_statistics")
                {!! Form::select('orderid', $poheadList_hxold, null, ['class' => 'form-control', 'placeholder' => '--请选择--']) !!}
            @endif

            {!! Form::submit('查找', ['class' => 'btn btn-default btn-sm']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    
    @if ($items->count())
    <table class="table table-striped table-hover table-condensed">
        <thead>
            <tr>
                @foreach(array_first($items->items()) as $key=>$value)
                <th>{{$key}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                @foreach($item as $value)
                <td>
                    {{ $value }}
                </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>

    </table>
    {!! $items->setPath('/system/report/' . $report->id . '/statistics')->appends($input)->links() !!}
    @else
    <div class="alert alert-warning alert-block">
        <i class="fa fa-warning"></i>
        {{'无记录', [], 'layouts'}}
    </div>
    @endif    

@stop
