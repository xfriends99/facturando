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
        </div>
    </div>
@endsection