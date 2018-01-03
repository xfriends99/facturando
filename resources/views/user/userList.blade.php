@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Usuarios</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Nombre</th>
								<th>Apellido</th>
								<th>Email</th>
								<th>Rol</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>
							@foreach($usuarios as $usuario)
							@if($usuario->id != Auth::user()->id)
							<tr>
								<th>{{ $usuario->name  }}</th>
								<td>{{ $usuario->lastname }}</td>
								<td>{{ $usuario->email }}</td>
								<td>{{ $usuario->roles->rol }}</td>
								<td><a href= "/profile/{{$usuario->id}}" class="btn btn-info" >Editar</a>&nbsp;&nbsp;<a href= "/deleteUser/{{$usuario->id}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar</a></td>
							</tr>
							@endif
							@endforeach
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
