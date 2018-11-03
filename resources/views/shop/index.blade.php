@extends('layOut.default_model')

@section('contents')
<table class="table table-bordered table-hover">
    <tr class="info">
        <th>id</th>
        <th>店铺分类</th>
        <th>名称</th>
       <th>店铺图片</th>
        <th>评分</th>
        <th>是否是品牌</th>
        <th>是否准时送达</th>
        <th>是否蜂鸟配送</th>
        <th>是否保标记</th>
        <th>是否票标记</th>
        <th>是否准标记</th>
        <th>起送金额</th>
        <th>配送费</th>
        <th>店公告</th>
        <th>优惠信息</th>
        <th>店铺状态</th>
        <th>操作</th>
    </tr>
    @foreach($shops as $shop)
        <tr>
            <td>{{$shop->id}}</td>
            <td>{{$shop->shop_category_id}}</td>
            <td>{{$shop->shop_name}}</td>
            <td><img src="{{$shop->shop_img}}" width="70px"/></td>
            <td>{{$shop->shop_rating}}</td>
            <td>{{$shop->brand==1?'是':'不是'}}</td>
            <td>{{$shop->on_time==1?'准时':'不准时'}}</td>
            <td>{{$shop->fengniao==1?'是':'不是'}}</td>
            <td>{{$shop->bao==1?'是':'不是'}}</td>
            <td>{{$shop->piao==1?'是':'不是'}}</td>
            <td>{{$shop->zhun==1?'是':'不是'}}</td>
            <td>{{$shop->start_zend}}</td>
            <td>{{$shop->start_cost}}</td>
            <td>{{$shop->notice}}</td>
            <td>{{$shop->discount}}</td>
            <td>
                @if($shop->status==1)
                    正常
                @elseif($shop->status==0)
                    待审核
                @else
                    禁用
                @endif
            </td>
            <td>

                <a href="#" class="btn btn-success">编辑</a>

                <form action="#" method="post">
                    {{csrf_field()}}
                    {{method_field('DELETE')}}
                <button class="btn btn-warning">删除</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
{{$shops->links()}}

@endsection