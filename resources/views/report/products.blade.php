@extends('app')


@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Listado de Productos
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif
                            <form class="form-horizontal" role="form" method="get" action="/listadoProducto">
                                <div class="list-group">
                                    <div class="list-group-item">
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
                                            <div class="form-group">
                                                <div class="col-md-4 col-md-offset-4"><br/>
                                                    <button type="submit" class="btn btn-primary">
                                                        Consultar!
                                                    </button>
                                                </div>
                                            </div>

                            </form>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Descripción</th>
                                <th>Stock Fisico</th>
                                <th>Stock Pedido</th>
                                <th>Stock Teorico</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0; ?>
                            @foreach($product_list as $product)
                                @if(preg_match('/^'.$request['reference'].'-.+/', $product->reference) || $request['reference']=='')                                     <?php $i++; ?>
                                    <tr>
                                        <th>{{ $i }}</th>
                                        <td>{{ $product->descripcion }}</td>
                                        <td>{{ $product->stock_Fisico }}</td>
                                        <td>{{ $product->stock_Pedido }}</td>
                                        <td>{{ $product->stock_Fisico-$product->stock_Pedido }}</td>
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