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
                                <th>Peso referencia</th>
                                <th>Diámetro referencia</th>
                                <th>Metros referencia</th>
                                <th>Rollos referencia</th>
                                <th>Peso Manga</th>
                                <th>Diámetro Manga</th>
                                <th>Metros Manga</th>
                                <th>Cortes por Manga</th>
                                <th>Peso por Pack</th>
                                <th>Cortes por Pack</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0; ?>
                            @foreach($product_list as $product)
                                @if(preg_match('/^'.$request['reference'].'-.+/', $product->reference) || $request['reference']=='')                                     <?php $i++; ?>
                                    <tr>
                                        <th>{{ $i }}</th>
                                        <td>{{ $product->descripcion }}</td>
                                        <td>{{ $product->pesoRef }}</td>
                                        <td>{{ $product->diametroRef }}</td>
                                        <td>{{ $product->metrosRef }}</td>
                                        <td>{{ $product->rollosRef }}</td>
                                        <td>{{ $product->peso_manga }}</td>
                                        <td>{{ $product->diametro }}</td>
                                        <td>{{ $product->cant_metros }}</td>
                                        <td>{{ $product->cant_por_man }}</td>
                                        <td>{{ $product->peso_por_pack }}</td>
                                        <td>{{ $product->cant_por_pack }}</td>
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