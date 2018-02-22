@extends('app')


@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Detalle de Cta. Cte.
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="get" action="/listadoCtaCtes">
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
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Pedido</th>
                                <th>Tipo Cbte.</th>
                                <th>Nro. Cbte.</th>
                                <th>Importe</th>
                                <th>Pago</th>
                                <th>Usuario</th>
                            </tr>
                            </thead>
                            <tbody>

                            @if($invoices!=null)
                                @foreach($invoices as $invoice)
                                    <?php
                                    if($invoice['cbte_tipo']==99){
                                        $im = $invoice['imp_net'];
                                    } else {
                                        $im = $invoice['imp_total'];
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ $invoice['date'] }}</td>
                                        <td>{{$invoice['companyName']}}</td>
                                        <td>@if($invoice['id_order']){{$invoice['id_order']}}@endif</td>
                                        <td> @if($invoice['cbte_tipo']==1) Factura @elseif ($invoice['cbte_tipo']==2) Nota de Débito @elseif($invoice['cbte_tipo']==3) Nota de Crédito @elseif($invoice['cbte_tipo']==99) Remito @endif </td>
                                        <td>{{ $invoice['nro_cbte']  }}</td>
                                        <td>@if($invoice['cbte_tipo']==99){{ '$ '. number_format($invoice['imp_net'], 2) }} @else @if($invoice['cbte_tipo']==3){{ '$ '. number_format($invoice['imp_total'] , 2) }}@else{{ '$ '. number_format($invoice['imp_total'] , 2) }}@endif @endif</td>
                                        <td>{{ '$ '. number_format($invoice['saldo'] , 2)  }}</td>
                                        <td>
                                            @if(!$invoice['id_order'] && $invoice['object']->users)
                                                {{ $invoice['object']->users->name . ' ' . $invoice['object']->users->lastname  }}
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                            @endif

                            </tbody>
                        </table>
                        @if($invoices!=null)
                            <center> <?php echo $invos->appends(Input::except('page'))->render(); ?> </center>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection