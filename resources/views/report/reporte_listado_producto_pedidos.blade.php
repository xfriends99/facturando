@extends('app')


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Listado de Productos Pedidos
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="get" action="/listadoProductoPedidos">
                            <div class="list-group">
                                <div class="list-group-item">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Filtro por stock teorico</label>
                                        <div class="col-md-4">
                                            <select class="form-control" name="teorico" >
                                                <option value="" @if($request['teorico']=='') selected @endif>Todos</option>
                                                <option @if($request['teorico']=='<0') selected @endif value="<0"><0</option>
                                                <option @if($request['teorico']=='=0') selected @endif value="=0">=0</option>
                                                <option @if($request['teorico']=='>0') selected @endif value=">0">>0</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-4"><br/>
                                                <button type="submit" class="btn btn-primary">
                                                    Consultar!
                                                </button>
                                            </div>
                                        </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Listado de Productos
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
                                <?php
                                $stockTeorico = $product_list_tdp[$p->product_id]->stock_Fisico-$product_list_order[$p->product_id]->tot_product;
                                ?>
                                @if($request['teorico']=='' || ($stockTeorico<0 && $request['teorico']=='<0') || ($stockTeorico==0 && $request['teorico']=='=0') || ($stockTeorico>0 && $request['teorico']=='>0'))
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
                            @endif
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
                                <th>Fecha de pedido</th>
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
                            $i = 0;
                            ?>
                            @foreach($product_list_pedidos as $pp)
                                @foreach($pp as $p)
                                <?php
                                if($i==0){
                                    $id_product = $p->product_id;
                                }
                                $i++;
                                $stockTeorico = $product_list_tdp[$p->product_id]->stock_Fisico-$product_list_order[$p->product_id]->tot_product;
                                $stockTeorico_2 = $product_list_tdp[$id_product]->stock_Fisico-$product_list_order[$id_product]->tot_product;
                                ?>
                                @if($id_product!=$p->product_id)
                                    <?php
                                    $stockTeorico_2 = $product_list_tdp[$id_product]->stock_Fisico-$product_list_order[$id_product]->tot_product;
                                    ?>
                                    @if($request['teorico']=='' || ($stockTeorico_2<0 && $request['teorico']=='<0') || ($stockTeorico_2==0 && $request['teorico']=='=0') || ($stockTeorico_2>0 && $request['teorico']=='>0'))
                                        <tr>
                                            <td colspan="2">Total</td>
                                            <td>{{$count}}</td>
                                            <td colspan="2">En stock Fisico = @if(isset($product_list[$id_product]->stock_Fisico)) {{$product_list[$id_product]->stock_Fisico}} @else 0 @endif</td>
                                            <td colspan="2">A reponer = @if(($count-$product_list[$id_product]->stock_Fisico)<=0) OK @else {{$count-$product_list[$id_product]->stock_Fisico}} @endif</td>
                                        </tr>
                                    @endif
                                    <?php
                                    $count = 0;
                                    $id_product = $p->product_id;
                                    ?>
                                @endif
                                <?php $count += $p->product_quantity ?>
                                @if($request['teorico']=='' || ($stockTeorico<0 && $request['teorico']=='<0') || ($stockTeorico==0 && $request['teorico']=='=0') || ($stockTeorico>0 && $request['teorico']=='>0'))
                                <tr>
                                    <th>{{ date('d-m-Y',strtotime($p->date_add)) }}</td>
                                    <td>{{$p->product_name}}</td>
                                    <td>{{$p->product_quantity}}</td>
                                    <td width="17%">
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
                                @endif
                                @endforeach
                            @endforeach
                            <?php
                            $stockTeorico_2 = $product_list_tdp[$p->product_id]->stock_Fisico-$product_list_order[$p->product_id]->tot_product;
                            ?>
                            @if($request['teorico']=='' || ($stockTeorico_2<0 && $request['teorico']=='<0') || ($stockTeorico_2==0 && $request['teorico']=='=0') || ($stockTeorico_2>0 && $request['teorico']=='>0'))
                                <tr>
                                    <td>Total</td>
                                    <td>{{$count}}</td>
                                    <td colspan="2">En stock Fisico = @if($product_list[$p->product_id]->stock_Fisico!=null) {{$product_list[$p->product_id]->stock_Fisico}} @else 0 @endif</td>
                                    <td colspan="2">A reponer = @if($count-$product_list[$p->product_id]->stock_Fisico<=0) OK @else {{$count-$product_list[$p->product_id]->stock_Fisico}} @endif</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection