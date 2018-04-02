@extends('app')


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Listado de Stock
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="get" action="/listadoStockTipo">
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
                                        <label class="col-md-4 control-label">Filtro por stock fisico</label>
                                        <div class="col-md-4">
                                            <select class="form-control" name="fisico" >
                                                <option value="" @if($request['fisico']=='') selected @endif>Todos</option>
                                                <!--<option @if($request['fisico']=='<0') selected @endif value="<0"><0</option>-->
                                                <option @if($request['fisico']=='=0') selected @endif value="=0">=0</option>
                                                <option @if($request['fisico']=='>0') selected @endif value=">0">>0</option>
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
                            @foreach($product_list as $k => $p)
                                @if(isset($product_pedidos_list[$p->id_product]))
                                    <?php
                                        $stockTeorico = $p->stock_Fisico-$product_pedidos_list[$p->id_product]->tot_product;
                                    ?>
                                @else
                                    <?php
                                        $stockTeorico = $p->stock_Fisico;
                                    ?>
                                @endif
                                @if((preg_match('/^'.$request['reference'].'-.+/', $p->reference) || $request['reference']=='') && ($request['teorico']=='' || ($stockTeorico<0 && $request['teorico']=='<0') || ($stockTeorico==0 && $request['teorico']=='=0') || ($stockTeorico>0 && $request['teorico']=='>0')) && ($request['fisico']=='' || ($p->stock_Fisico<0 && $request['fisico']=='<0') || ($p->stock_Fisico==0 && $request['fisico']=='=0') || ($p->stock_Fisico>0 && $request['fisico']=='>0')))
                                    <tr>
                                        <td>{{$p->descripcion}}</td>
                                        <td>{{$p->stock_Fisico}}</td>
                                        @if(isset($product_pedidos_list[$p->id_product]))
                                            <td>{{$product_pedidos_list[$p->id_product]->tot_product}}</td>
                                            <td>{{$p->stock_Fisico-$product_pedidos_list[$p->id_product]->tot_product}}</td>
                                        @else
                                            <td>0</td>
                                            <td>{{$p->stock_Fisico}}</td>
                                        @endif
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
                            @foreach($product_list as $k => $p)
                                @if(isset($product_pedidos_list[$p->id_product]))
                                    <?php
                                    $stockTeorico = $p->stock_Fisico-$product_pedidos_list[$p->id_product]->tot_product;
                                    ?>
                                @else
                                    <?php
                                    $stockTeorico = $p->stock_Fisico;
                                    ?>
                                @endif
                                @if((preg_match('/^'.$request['reference'].'-.+/', $p->reference) || $request['reference']=='') && ($request['teorico']=='' || ($stockTeorico<0 && $request['teorico']=='<0') || ($stockTeorico==0 && $request['teorico']=='=0') || ($stockTeorico>0 && $request['teorico']=='>0')) && ($request['fisico']=='' || ($p->stock_Fisico<0 && $request['fisico']=='<0') || ($p->stock_Fisico==0 && $request['fisico']=='=0') || ($p->stock_Fisico>0 && $request['fisico']=='>0')))
                                    <tr>
                                        <td>{{$p->descripcion}}</td>
                                        <td>{{number_format($p->price,2) }}</td>
                                        <td>{{$p->stock_Fisico}}</td>
                                        <td>{{number_format($p->stock_Fisico*$p->price,2) }}</td>
                                        @if(isset($product_pedidos_list[$p->id_product]))
                                            <td>{{$product_pedidos_list[$p->id_product]->tot_product}}</td>
                                            <td>{{number_format($product_pedidos_list[$p->id_product]->tot_product*$p->price,2) }}</td>
                                            <td>{{$p->stock_Fisico-$product_pedidos_list[$p->id_product]->tot_product}}</td>
                                            <td>{{number_format(($p->stock_Fisico-$product_pedidos_list[$p->id_product]->tot_product)*$p->price,2) }}</td>
                                            <?php
                                                $fisico+= $p->stock_Fisico*$p->price;
                                                $pedido+=$product_pedidos_list[$p->id_product]->tot_product*$p->price;
                                                $teorico+=($p->stock_Fisico-$product_pedidos_list[$p->id_product]->tot_product)*$p->price;
                                                $fisico_cant+= $p->stock_Fisico;
                                                $pedido_cant+=$product_pedidos_list[$p->id_product]->tot_product;
                                                $teorico_cant+=$p->stock_Fisico-$product_pedidos_list[$p->id_product]->tot_product;
                                            ?>
                                        @else
                                            <?php
                                            $fisico+= $p->stock_Fisico*$p->price;
                                            $teorico+=$p->stock_Fisico*$p->price;
                                            $fisico_cant+= $p->stock_Fisico;
                                            $teorico_cant+=$p->stock_Fisico;
                                            ?>
                                            <td>0</td>
                                            <td>{{number_format(0,2) }}</td>
                                            <td>{{$p->stock_Fisico}}</td>
                                            <td>{{number_format($p->stock_Fisico*$p->price,2) }}</td>
                                        @endif
                                    </tr>
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