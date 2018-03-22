@extends('app')


@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Listado de Productos Pedidos
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Fecha de pedido</th>
                                <th>Nro. de Pedido </th>
                                <th>Status</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Cliente</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pedidos as $p)
                                <tr>
                                    <th>{{ date('d-m-Y',strtotime($p->date_add)) }}</td>
                                    <td>{{ $p->id_order  }} </td>
                                    <td style="width:50px;">
                                        <span class="label label-default" style="background: {{$p->color}} !important;">
                                            {{$p->name_state}}
                                        </span>
                                    </td>
                                    <td>{{$p->product_name}}</td>
                                    <td>{{$p->product_quantity}}</td>
                                    <td>
                                        @if($p->pedido->direccion_factura)
                                            {{$p->pedido->direccion_factura->company}}
                                        @elseif($p->edido->customer!=null)
                                            {{ $p->pedido->customer->firstname . ' ' . $p->pedido->customer->lastname}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>



                    </div>
                </div>
            </div>

            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Corte de Control por Producto
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Cliente</th>
                                <th>Fecha de pedido</th>
                                <th>Nro. de Pedido </th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 0;
                            $id_product = $pedidos_productos[0]->product_id;
                            ?>
                            @foreach($pedidos_productos as $p)
                                @if($id_product!=$p->product_id)
                                    <tr>
                                        <td>Total</td>
                                        <td>{{$count}}</td>
                                        <td colspan="2">En stock Fisico = @if(isset($product_list[$id_product]->stock_Fisico)) {{$product_list[$id_product]->stock_Fisico}} @else 0 @endif</td>
                                        <td colspan="2">A reponer = @if(($count-$product_list[$id_product]->stock_Fisico)<=0) OK @else {{$count-$product_list[$id_product]->stock_Fisico}} @endif</td>
                                    </tr>
                                    <?php
                                    $count = 0;
                                    $id_product = $p->product_id;
                                    ?>
                                @endif
                                <?php $count += $p->product_quantity ?>
                                <tr>
                                    <td>{{$p->product_name}}</td>
                                    <td>{{$p->product_quantity}}</td>
                                    <td>
                                        @if($p->pedido->direccion_factura)
                                            {{$p->pedido->direccion_factura->company}}
                                        @elseif($p->edido->customer!=null)
                                            {{ $p->pedido->customer->firstname . ' ' . $p->pedido->customer->lastname}}
                                        @endif
                                    </td>
                                    <td>{{ date('d-m-Y',strtotime($p->date_add)) }}</td>
                                    <td>{{ $p->id_order  }} </td>
                                    <td style="width:50px;">
                                        <span class="label label-default" style="background: {{$p->color}} !important;">
                                            {{$p->name_state}}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>Total</td>
                                <td>{{$count}}</td>
                                <td colspan="2">En stock Fisico = @if($product_list[$p->product_id]->stock_Fisico!=null) {{$product_list[$p->product_id]->stock_Fisico}} @else 0 @endif</td>
                                <td colspan="2">A reponer = @if($count-$product_list[$p->product_id]->stock_Fisico<=0) OK @else {{$count-$product_list[$p->product_id]->stock_Fisico}} @endif</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection