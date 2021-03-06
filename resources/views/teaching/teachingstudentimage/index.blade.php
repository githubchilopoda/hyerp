@extends('navbarerp')

@section('title', '教学点')

@section('main')
    <div class="panel-heading">
        <div class="panel-title">审批 -- 供应商付款
{{--            <div class="pull-right">
                <a href="{{ URL::to('product/itemclasses') }}" target="_blank" class="btn btn-sm btn-success">{{'物料类型管理'}}</a>
                <a href="{{ URL::to('product/characteristics') }}" target="_blank" class="btn btn-sm btn-success">{{'物料属性管理'}}</a>
            </div> --}}
        </div>
    </div>
    
    <div class="panel-body">
        <a href="{{ URL::to('teaching/teachingstudentimage/create') }}" class="btn btn-sm btn-success">新建</a>
{{--
        <form class="pull-right" action="/approval/paymentrequests/export" method="post">
            {!! csrf_field() !!}
            <div class="pull-right">
                <button type="submit" class="btn btn-default btn-sm">导出</button>
            </div>
        </form>

        <div class="pull-right">
            <button class="btn btn-default btn-sm" id="btnExport">导出</button>
        </div>
--}}
{{--
        <form class="pull-right" action="/approval/paymentrequests/search" method="post">
            {!! csrf_field() !!}
            <div class="pull-right">
                <button type="submit" class="btn btn-default btn-sm">查找</button>
            </div>
            <div class="pull-right input-group-sm">
                <input type="text" class="form-control" name="key" placeholder="支付对象、对应项目名称、申请人">    
            </div>
        </form>
    </div> 
--}}

    
    @if ($teachingstudentimages->count())
    <table class="table table-striped table-hover table-condensed">
        <thead>
            <tr>
                <th>姓名</th>
                <th>教学点</th>
                <th>描述</th>
                <th style="width: 120px">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachingstudentimages as $teachingstudentimage)
                <tr>
                    <td>
                        <a href="{{ url('/teaching/teachingstudentimage', $teachingstudentimage->id) }}" target="_blank">{{ $teachingstudentimage->name }}</a>
                    </td>
                    <td>
                        {{ $teachingstudentimage->teachingpoint->name }}
                    </td>
                    <td>
                        {{ $teachingstudentimage->descrip }}
                    </td>

                    <td>
                        <a href="{{ URL::to('/teaching/teachingstudentimage/'.$teachingstudentimage->id.'/edit') }}" class="btn btn-success btn-sm pull-left">编辑</a>
                        {!! Form::open(array('route' => array('teaching.teachingstudentimage.destroy', $teachingstudentimage->id), 'method' => 'delete', 'onsubmit' => 'return confirm("确定删除此记录?");')) !!}
                            {!! Form::submit('删除', ['class' => 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    @if (isset($key))
        {!! $teachingstudentimages->setPath('/teaching/teachingstudentimage')->appends(['key' => $key])->links() !!}
    @else
        {!! $teachingstudentimages->setPath('/approval/teachingstudentimage')->links() !!}
    @endif
    @else
    <div class="alert alert-warning alert-block">
        <i class="fa fa-warning"></i>
        {{'无记录', [], 'layouts'}}
    </div>
    @endif    

@endsection

@section('script')
    <script type="text/javascript">
        jQuery(document).ready(function(e) {
            $("#btnExport").click(function() {
                $.ajax({
                    type: "POST",
                    url: "{{ url('approval/paymentrequests/export') }}",
                    // data: $("form#formAddVendbank").serialize(),
                    // dataType: "json",
                    error:function(xhr, ajaxOptions, thrownError){
                        alert('error');
                    },
                    success:function(result){
                        alert("导出成功:" + result);
                    },
                }); 
            });
        });
    </script>
@endsection