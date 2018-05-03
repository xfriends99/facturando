@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		@if($invoices!=null)
		<div class="col-md-12 col-md-offset-0">
			<div class="panel panel-default">
				<div class="panel-heading">Consultar I.V.A. Compras
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
				
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Fecha</th>
								<th>Tipo de Cbte.</th>
								<th>Nro. Cbte.</th>
								<th>Razón Social</th>
								<th>Tipo Doc.</th>
								<th>Nr. Doc</th>
								<th>Neto Gravado</th>
								<th>I.V.A. (21%)</th>
								<th>I.V.A. (10.5%)</th>
								<th>I.V.A. (27%)</th>
								<th>Imp. no Gravado</th>
								<th>Total</th>
								</tr>
						</thead>
						<tbody>
							<?php 


							$imp_neto = 0;
							$imp_iva = 0;
							$imp_iva_27 = 0;
							$imp_iva_10 = 0;
							$imp_no_grav = 0;
							$imp_total = 0;

							?>
							@foreach($invoices as $invoice)
							<tr>
								<th>{{ $invoice->fecha_factura }}</td>
							    <th>{{ $invoice->tipo_cbte->tipo  }}</td>
								<td>{{ $invoice->nro_factura  }}</td>
								<td>{{ $invoice->nombre_proveedor  }}</td>
								<td>{{ $invoice->tipo_doc  }}</td>
								<td>{{ $invoice->cuit  }}</td>
								@if($invoice->tipo_cbte_prov_id!=3)
								<td>{{ '$ '.number_format($invoice->importe_neto,2) }}</td>
								<td>{{ '$ '.number_format($invoice->importe_iva,2) }}</td>
								<td>{{ '$ '.number_format($invoice->importe_iva_10_5,2) }}</td>
								<td>{{ '$ '.number_format($invoice->importe_iva_27,2) }}</td>
								<td>{{ '$ '.number_format($invoice->importe_neto_no_gravado,2) }}</td>
								<td>{{ '$ '.number_format($invoice->importe_total,2) }}</td>
								<?php 
							$imp_neto = $imp_neto + $invoice->importe_neto;
							$imp_iva = $imp_iva + $invoice->importe_iva;
							$imp_total = $imp_total + $invoice->importe_total;
							$imp_iva_27 = $imp_iva_27 + $invoice->importe_iva_27;
							$imp_iva_10 = $imp_iva_10 + $invoice->importe_iva_10_5;
							$imp_no_grav = $imp_no_grav + $invoice->importe_neto_no_gravado;
								?>
								@else
								<td>{{ '$ -'.number_format($invoice->importe_neto,2) }}</td>
								<td>{{ '$ -'.number_format($invoice->importe_iva,2) }}</td>
								<td>{{ '$ -'.number_format($invoice->importe_iva_10_5,2) }}</td>
								<td>{{ '$ -'.number_format($invoice->importe_iva_27,2) }}</td>
								<td>{{ '$ -'.number_format($invoice->importe_neto_no_gravado,2) }}</td>
								<td>{{ '$ -'.number_format($invoice->importe_total,2) }}</td>
								<?php 
							$imp_neto = $imp_neto - $invoice->importe_neto;
							$imp_iva = $imp_iva - $invoice->importe_iva;
							$imp_total = $imp_total - $invoice->importe_total;
							$imp_iva_27 = $imp_iva_27 - $invoice->importe_iva_27;
							$imp_iva_10 = $imp_iva_10 - $invoice->importe_iva_10_5;
							$imp_no_grav = $imp_no_grav - $invoice->importe_neto_no_gravado;
								?>
								@endif
							</tr>							
							@endforeach
							<tr>
								<td></td>
								<td></td>
								<td></td>	
								<td></td>
								<td></td>
								<td></td>
								<th>{{ '$ '.number_format($imp_neto,2) }}</th>
								<th>{{ '$ '.number_format($imp_iva,2) }}</th>
								<th>{{ '$ '.number_format($imp_iva_10,2) }}</th>
								<th>{{ '$ '.number_format($imp_iva_27,2) }}</th>
								<th>{{ '$ '.number_format($imp_no_grav,2) }}</th>
								<th>{{ '$ '.number_format($imp_total,2) }}</th>
							</tr>
						</tbody>
					</table>
					@else
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
				<div class="panel-heading">Consultar I.V.A. Compras
				</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="/listarIVAcompras">
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
