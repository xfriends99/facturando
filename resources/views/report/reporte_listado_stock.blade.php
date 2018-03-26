@extends('app')


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Listado de Stock Productos Pedidos
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="get" action="/listadoStock">
                            <div class="list-group">
                                <div class="list-group-item">
                                    <legend>Seleccionado:
                                        @if(isset($request['reference']))
                                            @foreach($reference as $r)
                                                @if($request['reference']==$r['id']) {{$r['name']}} @endif
                                            @endforeach
                                        @endif
                                    </legend>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Tipo</label>
                                        <div class="col-md-4">
                                            <select class="form-control" name="reference" >
                                                <option value="" @if($request['reference']=='') selected @endif>Seleccione</option>
                                                @foreach($reference as $r)
                                                    <option @if($request['reference']==$r['id']) selected @endif value="{{$r['id']}}">{{$r['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
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
                    <div class="panel-heading">Listado de Stock de productos
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock Fisico</th>
                                <th>Cant. Pedida</th>
                                <th>Stock Teorico</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($productos as $p)
                                <?php
                                $stockTeorico = $product_list[$p->product_id]->stock_Fisico-$p->tot_product;
                                ?>
                                @if((preg_match('/^'.$request['reference'].'-.+/', $product_list[$p->product_id]->reference) || $request['reference']=='') && ($request['teorico']=='' || ($stockTeorico<0 && $request['teorico']=='<0') || ($stockTeorico==0 && $request['teorico']=='=0') || ($stockTeorico>0 && $request['teorico']=='>0')))
                                    <tr>
                                        <td>{{$p->product_name}}</td>
                                        <td>{{$product_list[$p->product_id]->stock_Fisico}}</td>
                                        <td>{{$p->tot_product}}</td>
                                        <td>{{$product_list[$p->product_id]->stock_Fisico-$p->tot_product}}</td>
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
                    <div class="panel-heading">Listado de Stock de productos Valorizado
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock Fisico</th>
                                <th>Valor</th>
                                <th>Cant. Pedida</th>
                                <th>Valor</th>
                                <th>Stock Teorico</th>
                                <th>Valor</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $fisico = 0;
                            $pedido = 0;
                            $teorico = 0;
                            $fisico_cant = 0;
                            $pedido_cant = 0;
                            $teorico_cant = 0;
                            ?>
                            @foreach($productos as $p)
                                <?php
                                $stockTeorico = $product_list[$p->product_id]->stock_Fisico-$p->tot_product;
                                ?>
                                @if((preg_match('/^'.$request['reference'].'-.+/', $product_list[$p->product_id]->reference) || $request['reference']=='') && ($request['teorico']=='' || ($stockTeorico<0 && $request['teorico']=='<0') || ($stockTeorico==0 && $request['teorico']=='=0') || ($stockTeorico>0 && $request['teorico']=='>0')))
                                    <tr>
                                        <td>{{$p->product_name}}</td>
                                        <td>{{number_format($p->product_price,2) }}</td>
                                        <td>{{$product_list[$p->product_id]->stock_Fisico}}</td>
                                        <td>{{number_format($product_list[$p->product_id]->stock_Fisico*$p->product_price,2) }}</td>
                                        <td>{{$p->tot_product}}</td>
                                        <td>{{number_format($p->tot_product*$p->product_price,2) }}</td>
                                        <td>{{$product_list[$p->product_id]->stock_Fisico-$p->tot_product}}</td>
                                        <td>{{number_format(($product_list[$p->product_id]->stock_Fisico-$p->tot_product)*$p->product_price,2) }}</td>
                                    </tr>
                                    <?php
                                    $fisico+= $product_list[$p->product_id]->stock_Fisico*$p->product_price;
                                    $pedido+=$p->tot_product*$p->product_price;
                                    $teorico+=($product_list[$p->product_id]->stock_Fisico-$p->tot_product)*$p->product_price;
                                    $fisico_cant+= $product_list[$p->product_id]->stock_Fisico;
                                    $pedido_cant+=$p->tot_product;
                                    $teorico_cant+=$product_list[$p->product_id]->stock_Fisico-$p->tot_product;
                                    ?>
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="2">Totales:</td>
                                <td>{{$fisico_cant}}</td>
                                <td>{{number_format($fisico,2) }}</td>
                                <td>{{$pedido_cant}}</td>
                                <td>{{number_format($pedido,2) }}</td>
                                <td>{{$teorico_cant}}</td>
                                <td>{{number_format($teorico,2) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection