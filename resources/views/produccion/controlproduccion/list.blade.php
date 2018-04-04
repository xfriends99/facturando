@extends('app')


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                Control de producción
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group" style="float: right;">
                                    <button class="btn btn-success" id="send-form">Guardar producción</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!--<form class="form-horizontal" role="form" method="get" action="/controlProduccion">
                            <div class="list-group">
                                <div class="list-group-item">
                                    <legend>Período seleccionado: @if(isset($request['desde']) && isset($request['hasta'])) Desde: {{ date('d-m-Y',strtotime($request['desde'])) }} Hasta: {{ date('d-m-Y',strtotime($request['hasta'])) }} @endif</legend>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Desde</label>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control" name="desde" value="{{ old('desde') }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Hasta</label>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control" name="hasta" value="{{ old('hasta') }}"  required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4 col-md-offset-4"><br/>
                                            <button type="submit" class="btn btn-primary">
                                                Consultar!
                                            </button>
                                        </div>
                                    </div>

                        </form>-->
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
                                <th>OK</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!$list)
                                No se encontraron registros
                            @else
                                <form method="POST" action="/controlProduccion/store" id="form-send">
                                @foreach($list as $l)
                                    @if(isset($control_produccion[$l->id_producto]))
                                    <tr>
                                        <th>{{ date('d-m-Y',strtotime($l->created_at)) }}</th>
                                        <td>{{$l->producto->descripcion}}</td>
                                        @if($l->producto->operacion=='I')
                                            <?php
                                                $v3 = $control_produccion[$l->id_producto]['packs'];
                                                $v4 = $l->producto->cant_por_pack ? floor(($l->mangas_sum*$l->producto->cant_por_man)/$l->producto->cant_por_pack) : 0;
                                                $v5 = $l->kg_sum;
                                                $v6 = $control_produccion[$l->id_producto]['packs']*$l->producto->peso_manga;
                                            ?>
                                            <td>{{$l->mangas_sum}}</td>
                                            <td>{{$control_produccion[$l->id_producto]['packs']}}</td>
                                            @if($l->producto->cant_por_pack)
                                                <td>{{floor(($l->mangas_sum*$l->producto->cant_por_man)/$l->producto->cant_por_pack)}}</td>
                                            @else
                                                <td>0</td>
                                            @endif
                                            <td>{{$l->kg_sum}}</td>
                                        @else
                                            <?php
                                                $v3 = $control_produccion[$l->id_producto]['packs'];
                                                $v4 = $l->producto->cant_por_pack ? floor(($l->productos_count*$l->producto->cant_por_man)/$l->producto->cant_por_pack) : 0;
                                                $v5 = $l->kg_suma;
                                                $v6 = $control_produccion[$l->id_producto]['packs']*$l->producto->peso_manga;
                                            ?>
                                            <td>{{$l->productos_count}}</td>
                                            <td>{{$control_produccion[$l->id_producto]['packs']}}</td>
                                            @if($l->producto->cant_por_pack)
                                                <td>{{floor(($l->productos_count*$l->producto->cant_por_man)/$l->producto->cant_por_pack)}}</td>
                                            @else
                                                <td>0</td>
                                            @endif
                                            <td>{{$l->kg_suma}}</td>
                                        @endif
                                        <td>{{$control_produccion[$l->id_producto]['packs']*$l->producto->peso_manga}}</td>
                                        @if($v3-$v4==0 && $v5-$v6==0)
                                            <td width="7%"><input value="{{$v3}}" class="form-control input-sm stock-{{$l->id_producto}}" name="stock[]" id="stock-{{$l->id_producto}}" data-val="{{$l->id_producto}}"></td>
                                            <td width="7%"><input readonly value="S" class="form-control input-sm ok-{{$l->id_producto}}" id="ok-{{$l->id_producto}}" name="ok[]" data-val="{{$l->id_producto}}"></td>
                                        @else
                                            <td width="7%"><input class="form-control input-sm stock-{{$l->id_producto}}" id="stock-{{$l->id_producto}}" name="stock[]" data-val="{{$l->id_producto}}"></td>
                                            <td width="7%"><input class="form-control input-sm ok" id="ok-{{$l->id_producto}}" data-val="{{$l->id_producto}}" name="ok[]"></td>
                                        @endif
                                    </tr>
                                    @endif
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
    <script>
        $(document).ready(function(){
            $(".ok").keydown(function (e) {
                if(e.keyCode==8 || e.keyCode==46){
                    return true;
                }
                if(String.fromCharCode(e.keyCode)=='S' && $(this).val()==''){
                    $(this).val('S');
                    return false;
                } else {
                    return false;
                }
            });

            $("#send-form").click(function(){
                $(".ok").each(function(){
                    if($(this).val()=='S'){
                        $(this).val($(this).attr('data-val'));
                    }
                });
                $("#form-send").submit();
            });
        });
    </script>
@endsection