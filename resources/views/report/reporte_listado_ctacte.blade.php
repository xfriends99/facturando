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
                                <th style="width: 10%;">Fecha</th>
                                <th style="width: 20%;">Cliente</th>
                                <th style="width: 5%;">Pedido</th>
                                <th style="width: 25%;">Tipo Cbte.</th>
                                <th style="width: 10%;">Nro. Cbte.</th>
                                <th style="width: 10%;">Importe</th>
                                <th style="width: 10%;">Pago</th>
                                <th style="width: 20%;">Usuario</th>
                            </tr>
                            </thead>
                            <tbody>

                            @if($invoices!=null)
                                @foreach($invoices as $invoice)
                                    @if($invoice['type']=='invoice')
                                    <?php
                                    if($invoice['cbte_tipo']==99){
                                        $im = $invoice['imp_net'];
                                    } else {
                                        $im = $invoice['imp_total'];
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ date('d-m-Y',strtotime($invoice['date'])) }}</td>
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
                                    @else
                                        <tr>
                                            <td>{{ $invoice['date']->format('d-m-Y') }}</td>
                                            <td>{{$invoice['companyName']}}</td>
                                            <td></td>
                                            <td>@if ($invoice['object']->medios_pagos_id!=null) {{ $invoice['object']->medio_pago->tipo }} @else {{ $invoice['object']->otro }} @endif</td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ '$ '. number_format($invoice['imp_total'] , 2)  }}</td>
                                            <td>
                                                @if($invoice['object']->users)
                                                    {{ $invoice['object']->users->name . ' ' . $invoice['object']->users->lastname  }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection