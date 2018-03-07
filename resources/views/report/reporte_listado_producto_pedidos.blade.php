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
                                    <legend>Período seleccionado: @if(isset($hoy)) {{ date('d-m-Y',strtotime($hoy)) }} @else Desde: {{ date('d-m-Y',strtotime($desde)) }} Hasta: {{ date('d-m-Y',strtotime($hasta)) }} @endif</legend>
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

                        </form>
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
                            @foreach($invoices as $invoice)

                                @foreach($invoice->invoice_lines as $linea)

                                    <?php $pedido = \app\Pedido::join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
                                        ->join('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
                                        ->addSelect('ps_orders.current_state as current_state')
                                        ->addSelect('ps_order_state_lang.name as name_state')
                                        ->addSelect('ps_order_state.color as color')
                                        ->where('ps_order_state_lang.id_lang',1)
                                        ->where('id_order','=',$invoice->id_order)->first();	?>

                                    @if($pedido && $pedido->current_state!=5 && $pedido->current_state!=6 && $pedido->current_state!=16 && $invoice->status=='A' && ($invoice->cbte_tipo==1 || $invoice->cbte_tipo==99) && $linea->code!=null)
                                        <tr>
                                            <th>{{ date('d-m-Y',strtotime($invoice->fecha_facturacion)) }}</td>
                                            <td>{{ $invoice->id_order  }} </td>
                                            <td style="width:50px;">
                                                <span class="label label-default" style="background: {{$pedido->color}} !important;">
                                                    {{$pedido->name_state}}
                                                </span>
                                            </td>
                                            <td>{{$linea->name}}</td>
                                            <td>{{$linea->quantity}}</td>
                                            <td>{{ $invoice->company_name  }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                            @endforeach
                            </tbody>
                        </table>



                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection