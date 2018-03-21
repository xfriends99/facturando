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
                                                @foreach($reference as $r)
                                                    <option @if($request['reference']==$r['id']) selected @endif value="{{$r['id']}}">{{$r['name']}}</option>
                                                @endforeach
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
                                @if(preg_match('/'.$request['reference'].'-.+/', $product_list[$p->product_id]->reference))
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
                            @foreach($productos as $p)
                                @if(preg_match('/'.$request['reference'].'-.+/', $product_list[$p->product_id]->reference))
                                    <tr>
                                        <td>{{$p->product_name}}</td>
                                        <td>{{'$ '.number_format($p->product_price,2) }}</td>
                                        <td>{{$product_list[$p->product_id]->stock_Fisico}}</td>
                                        <td>{{'$ '.number_format($product_list[$p->product_id]->stock_Fisico*$p->product_price,2) }}</td>
                                        <td>{{$p->tot_product}}</td>
                                        <td>{{'$ '.number_format($p->tot_product*$p->product_price,2) }}</td>
                                        <td>{{$product_list[$p->product_id]->stock_Fisico-$p->tot_product}}</td>
                                        <td>{{'$ '.number_format(($product_list[$p->product_id]->stock_Fisico-$p->tot_product)*$p->product_price,2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection