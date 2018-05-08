@extends('app')


@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Control de Producción
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="get" action="/listadoControlProduccion">
                            <div class="list-group">
                                <div class="list-group-item">
                                    <legend>Período seleccionado: @if(isset($hoy)) {{ date('d-m-Y',strtotime($hoy)) }} @else Desde: {{ date('d-m-Y',strtotime($desde)) }} Hasta: {{ date('d-m-Y',strtotime($hasta)) }} @endif</legend>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Desde</label>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control" name="desde" @if(!isset($hoy) && isset($desde)) value="{{ $desde }}" @endif required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Hasta</label>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control" name="hasta" @if(!isset($hoy) && isset($hasta)) value="{{ $hasta }}" @endif  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4 col-md-offset-4"><br/>
                                            <button type="submit" class="btn btn-primary">
                                                Consultar!
                                            </button>
                                        </div>
                                    </div>

                        </form>
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>DESCRIPCION </th>
                                <th>CANT. INFO</th>
                                <th>CANT. CALC </th>
                                <th>MANGAS PROD </th>
                                <th>PESO PROD</th>
                                <th>PESO TEOR</th>
                                <th>A STOCK</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!$control)
                                No se encontraron registros
                            @else
                                <form method="POST" action="/controlProduccion/store" id="form-send">
                                    @foreach($control as $l)
                                        <tr>
                                            <th>{{ date('d/m/Y',strtotime($l->fecha)) }}</th>
                                            <td>{{$l->producto->descripcion}}</td>
                                            @if($l->producto->operacion=='I')
                                                <?php
                                                    if($l->type_manga=='c'){
                                                        $division = 4;
                                                    } else {
                                                        $division = 5;
                                                    }
                                                ?>
                                                <td>{{$l->packs}}</td>
                                                @if($l->producto->cant_por_man)
                                                    <td>{{floor(($l->mangas*$division)/$l->producto->cant_por_pack)}}</td>
                                                @else
                                                    <td>0</td>
                                                @endif
                                                <td>{{$l->mangas}}</td>
                                                <td>{{number_format($l->kg*$l->mangas, 2)}}</td>
                                                @if($l->type_manga=='c')
                                                    @if($l->producto->cant_por_man)
                                                        <td>{{number_format((($l->producto->peso_manga / $l->producto->cant_por_man) * $division)*$l->mangas, 2)}}</td>
                                                    @else
                                                        <td>0</td>
                                                    @endif
                                                @else
                                                    <td>{{number_format($l->producto->peso_manga*$l->mangas, 2)}}</td>
                                                @endif
                                            @else
                                                <td>{{$l->packs}}</td>
                                                @if($l->producto->cant_por_pack && isset($produccion_data[$l->id]))
                                                    <td>{{floor(($produccion_data[$l->id]->productos_count*$l->producto->cant_por_man)/$l->producto->cant_por_pack)}}</td>
                                                @else
                                                    <td>0</td>
                                                @endif
                                                @if(isset($produccion_data[$l->id]))
                                                    <td>{{$produccion_data[$l->id]->productos_count}}</td>
                                                    <td>{{number_format($produccion_data[$l->id]->kg_suma, 2)}}</td>
                                                @else
                                                    <td>0</td>
                                                    <td>0</td>
                                                @endif
                                                @if(isset($produccion_data[$l->id]))
                                                        <td>{{number_format($l->producto->peso_manga * $produccion_data[$l->id]->productos_count, 2)}}</td>
                                                @else
                                                    <td>0</td>
                                                @endif
                                            @endif
                                            <td>{{$l->a_stock}}</td>
                                        </tr>
                                    @endforeach
                                </form>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
