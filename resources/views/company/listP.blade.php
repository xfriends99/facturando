@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Proveedores
					<a href= "/altaProveedor"  style="margin-top:-7px; float:right;" class="btn btn-success" >Crear Proveedor</a>
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Razón Social</th>
								<th>Tipo Doc.</th>
								<th>Nr. Doc</th>
								<th>Acción</th>
								</tr>
						</thead>
						<tbody>
							@foreach($provs as $prov)
							<tr>
								<th>{{ $prov->company_name  }}</th>
								<td>{{ $prov->tax_type->type }}</td>
								<td>{{ $prov->tax_id }}</td>
								<td><a href= "/crearFacturaCompra/{{$prov->id}}" class="btn btn-success" >Crear Factura de Compra</a>&nbsp;&nbsp;<a href= "/editarCliente/{{$prov->id}}" class="btn btn-info" >Editar</a>&nbsp;&nbsp;<a href= "/eliminarCliente/{{$prov->id}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar</a></td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<center> <?php echo $provs->render(); ?> </center>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
