@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Facturas de Compra
					<a href= "/crearFacturaCompra"  style="margin-top:-7px; float:right;" class="btn btn-success" >Crear Cbte. de Compra</a>
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
								<th>Imp. Total</th>
								<th>Acción</th>
								</tr>
						</thead>
						<tbody>
							@foreach($invoices as $invoice)
							<tr>
								<th>{{ $invoice->fecha_factura  }}</td>
								<th>{{ $invoice->tipo_cbte->tipo  }}</td>
								<td>{{ $invoice->nro_factura  }}</td>
								<td>{{ $invoice->nombre_proveedor  }}</td>
								<td>{{ $invoice->tipo_doc  }}</td>
								<td>{{ $invoice->cuit  }}</td>

								<td>{{ '$ '.number_format($invoice->importe_total,2)  }}</td>
								<td><a href= "/editarFacturaCompra/{{$invoice->id}}" class="btn btn-info" >Editar</a>&nbsp;&nbsp;<a href= "/deleteFactura/{{$invoice->id}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar</a></td>
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
