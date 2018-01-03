@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Clientes
					<a href= "/altaCliente"  style="margin-top:-7px; float:right;" class="btn btn-success" >Nuevo Cliente</a></div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Razón Social</th>
								<th>ID Fiscal</th>
								<th>Teléfono</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>
							@foreach($customers as $customer)
							<tr>
								<th>{{ $customer->company_name  }}</th>
								<td>{{ $customer->tax_id }}</td>
								<td>{{ $customer->tel }}</td>
								<td><a href= "/crearFactura/{{$customer->id}}" class="btn btn-success" >Crear Factura</a>&nbsp;&nbsp;<a href= "/editarCliente/{{$customer->id}}" class="btn btn-info" >Editar</a>&nbsp;&nbsp;<a href= "/eliminarCliente/{{$customer->id}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar</a></td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<center> <?php echo $customers->render(); ?> </center>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
