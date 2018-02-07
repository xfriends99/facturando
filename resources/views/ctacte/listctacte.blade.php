@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
			    <?php	$final = 0;  ?>
			     @if(!empty($salddos) && $salddos!=null)
						    
						    @foreach($salddos as $saldo)
						 
						    <?php $final = $final + $saldo->importe; ?>
						  
							@endforeach
							@endif
				<div class="panel-heading">Cuenta Corriente de <b>{{$companyName}}</b> -------------  Saldo actual: @foreach($total as $to)<b> {{'$'. number_format($to->getSaldo($to->companies_id),2)}} </b> @endforeach
				<a href="/addSaldo/{{$companyID}}" style="margin-top:-7px; float:right;margin-right: 25px;" class="btn btn-success">Agregar Saldo</a>
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Fecha</th>
								<th>Tipo Cbte.</th>
								<th>Nro. Cbte.</th>
								<th>Imp. Original</th>
								<th>Pago</th>
								<th>Saldo</th>
								<th>Acción</th>
								</tr>
						</thead>
						<tbody>

							@foreach($invoices as $invoice)
								@if($invoice['type']=='saldo')
									<tr>
										<th>{{ date("Y-m-d", strtotime($invoice['date']))  }}</th>
										<td>@if ($invoice['medios_pagos_id']!=0) {{ $invoice['medioPago_tipo'] }} @else {{ $invoice['otro'] }} @endif</td>
										<td></td>
										<td>{{ '$ '. number_format($invoice['importe'] , 2) }}</td>
										<td></td>
										<td>{{ '$ '. number_format($invoice['object']->getSaldo($companyID, $invoice['date'], $invoice['id']) , 2)  }}</td>
										<td><a href= "/eliminarSaldo/{{$invoice['id']}}" class="btn btn-danger" onClick="return confirm('¿Esta seguro?');" >Eliminar</a></td>
									</tr>
								@else
									<?php
									if($invoice['cbte_tipo']==99){
									    $im = $invoice['imp_net'];
									} else {
									    $im = $invoice['imp_total'];
									}
									?>
									<tr>
										<th>{{}}{{ $invoice['date'] }}</td>
										<td> @if($invoice['cbte_tipo']==1) Factura @elseif ($invoice['cbte_tipo']==2) Nota de Débito @elseif($invoice['cbte_tipo']==3) Nota de Crédito @elseif($invoice['cbte_tipo']==99) Remito @endif </td>
										<td>{{ $invoice['nro_cbte']  }}</td>
										<td>@if($invoice['cbte_tipo']==99){{ '$ '. number_format($invoice['imp_net'], 2) }} @else @if($invoice['cbte_tipo']==3){{ '$ '. number_format($invoice['imp_total'] , 2) }}@else{{ '$ '. number_format($invoice['imp_total'] , 2) }}@endif @endif</td>
										<td>{{ '$ '. number_format($invoice['saldo'] , 2)  }}</td>
										<td>
											{{ '$ '. number_format($invoice['object']->getSaldo($companyID, $invoice['date'], $invoice['idfact']) , 2)  }}
										</td>
										<td>@if(number_format($invoice['object']->getSaldo($companyID, $invoice['date'], $invoice['idfact']), 2)>0)<a href= "/agregarPago/{{$invoice['id']}}" class="btn btn-success" >Agregar Pago</a>&nbsp;&nbsp;@endif
											@if($invoice['cbte_tipo']!=3)
												<a href= "/verPagos/{{$invoice['id']}}" class="btn btn-info" >Ver Pagos</a>@endif</td>
									</tr>
								@endif
							@endforeach
						</tbody>
					</table>
					<center> {!! $invos->render() !!} </center>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
