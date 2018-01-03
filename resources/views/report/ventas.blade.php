@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		@if($invoices!=null)
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Consultar I.V.A. Ventas
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
								<th>Razón Social</th>
								<th>Subtotal</th>
								<th>I.V.A. 21%</th>
								<th>Total</th>
								</tr>
						</thead>
						<tbody>
							<?php 


							$imp_neto = 0;
							$imp_iva_10_5 = 0;
							$imp_iva_21 = 0;
							$imp_iva_27 = 0;
							$imp_total = 0;

							?>
							@foreach($invoices as $invoice)
							<tr>
								<th>{{ $invoice->fecha_facturacion  }}</td>
								<td> @if($invoice->cbte_tipo==1 || $invoice->cbte_tipo==6) Factura @elseif ($invoice->cbte_tipo==2 || $invoice->cbte_tipo==7) Nota de Debito @else Nota de Credito @endif </td>
								<td>{{ $invoice->nro_cbte  }}</td>
								<td>{{ $invoice->company_name  }}</td>
								<td>@if($invoice->cbte_tipo==3 || $invoice->cbte_tipo==8){{ '$ -'.number_format($invoice->imp_net,2)  }}@else{{ '$ '.number_format($invoice->imp_net,2)  }}@endif</td>
								<td>@if($invoice->cbte_tipo==3 || $invoice->cbte_tipo==8){{ '$ -'.number_format($invoice->imp_iva_21,2)  }}@else{{ '$ '.number_format($invoice->imp_iva_21,2)  }}@endif</td>    <td>@if($invoice->cbte_tipo==3 || $invoice->cbte_tipo==8){{ '$ -'.number_format($invoice->imp_total,2)  }}@else{{ '$ '.number_format($invoice->imp_total,2)  }}@endif</td>
							</tr>
							<?php 

							if($invoice->cbte_tipo!=3 && $invoice->cbte_tipo!=8){
							$imp_neto = $imp_neto + $invoice->imp_net;
							$imp_iva_21 = $imp_iva_21 + $invoice->imp_iva_21;
							$imp_total = $imp_total + $invoice->imp_total;	
							}else{
							$imp_neto = $imp_neto - $invoice->imp_net;
							$imp_iva_21 = $imp_iva_21 - $invoice->imp_iva_21;
							$imp_total = $imp_total - $invoice->imp_total;
							}
							
							?>
							@endforeach
							<tr>
								
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<th>{{ '$ '.number_format($imp_neto,2)  }}</th>
																<th>{{ '$ '.number_format($imp_iva_21,2)  }}</th>
								
								<th>{{ '$ '.number_format($imp_total,2)  }}</th>
							</tr>
						</tbody>
					</table>
					@else
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
				<div class="panel-heading">Consultar I.V.A. Ventas
				</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="/listarIVAventas">
						<div class="list-group">
							<div class="list-group-item">
								<legend>Seleccione Período</legend>
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
							@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
