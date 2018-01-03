@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
			    <?php	$final = 0;  ?>
			     @if(!empty($saldos) && $saldos!=null)
						    
						    @foreach($saldos as $saldo)
						 
						    <?php	$final = $final + $saldo->importe; ?>
						  
							@endforeach
							@endif
				<div class="panel-heading">Cuenta Corriente de <b>{{$companyName}}</b> -------------  Saldo actual: @foreach($total as $to)<b> {{'$'. number_format($final + $to->sumaSaldo,2)}} </b> @endforeach
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
								<th>Saldo</th>
								<th>Acción</th>
								</tr>
						</thead>
						<tbody>
						     @if(!empty($saldos) && $saldos!=null)
						    
						    @foreach($saldos as $saldo)
							<tr>
								<th>{{ date("Y-m-d", strtotime($saldo->created_at))  }}</td>
								<td>@if ($saldo->medios_pagos_id!=0) {{ $saldo->medioPago->tipo }} @else {{ $saldo->otro }} @endif  </td>
								<td>  </td>
								<td>  </td>
								<td> {{ '$'. number_format($saldo->importe , 2) }} </td>
								<td><a href= "/eliminarSaldo/{{$saldo->id}}" class="btn btn-danger" onClick="return confirm('¿Esta seguro?');" >Eliminar</a></td>
							</tr>
							@endforeach
							@endif
							@foreach($invoices as $invoice)
							<tr>
								<th>{{ $invoice->fecha_facturacion  }}</td>
								<td> @if($invoice->cbte_tipo==1) Factura @elseif ($invoice->cbte_tipo==2) Nota de Débito @elseif($invoice->cbte_tipo==3) Nota de Crédito @elseif($invoice->cbte_tipo==99) Remito @endif </td>
								<td>{{ $invoice->nro_cbte  }}</td>
								<td>@if($invoice->cbte_tipo==99){{ '$ '. number_format($invoice->imp_net, 2) }} @else @if($invoice->cbte_tipo==3){{ '$ -'. number_format($invoice->imp_total , 2) }}@else{{ '$ '. number_format($invoice->imp_total , 2) }}@endif @endif</td>
								<td>{{ '$ '. number_format($invoice->saldo , 2)  }}</td>
								<td>@if(number_format($invoice->saldo , 2)>0)<a href= "/agregarPago/{{$invoice->id}}" class="btn btn-success" >Agregar Pago</a>&nbsp;&nbsp;@endif
								 @if($invoice->cbte_tipo!=3)
								<a href= "/verPagos/{{$invoice->id}}" class="btn btn-info" >Ver Pagos</a>@endif</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<center> <?php echo $invoices->render(); ?> </center>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
