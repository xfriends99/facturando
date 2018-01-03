@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Corredores
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Nombre</th>
								<th>Mail</th>
								<th>Acción</th>
								</tr>
						</thead>
						<tbody>
							@foreach($corredores as $corredor)
							<tr>
								<th>{{ $corredor->nombre  }}</td>
								<th>{{ $corredor->mail  }}</td>

								<td><a href= "/vendedoresTDP/{{$corredor->id}}" class="btn btn-info" >Editar</a>&nbsp;&nbsp;<a href= "/deleteVendedor/{{$corredor->id}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar</a></td>
							</tr>
							@endforeach
						</tbody>
					</table>
					
					@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Whoops!</strong> Existen algunos errores en los campos del formulario.<br><br>
						<ul>
							@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
					@endif
				
					<form class="form-horizontal" role="form" method="POST" action="/addCorredor" >

						<div class="list-group">
							<div class="list-group-item">
								<legend>@if($vendedor==null) 	Agregar Corredor @else Editar Corredor @endif</legend>

								<input type="hidden" class="form-control" name="id" @if($vendedor!=null) value="{{ $vendedor->id }}"  @endif>
								
							   <div class="form-group">
									<label class="col-md-4 control-label">Nombre</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="nombre" @if($vendedor!=null) value="{{ $vendedor->nombre }}"  @endif required>
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-md-4 control-label">Email</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="mail" @if($vendedor!=null) value="{{ $vendedor->mail }}"  @endif required>
									</div>
								</div>
								
							    <div class="form-group">
									<label class="col-md-4 control-label">Contraseña</label>
									<div class="col-md-6">
										<input type="password" class="form-control" name="clave" @if($vendedor==null) required @endif>
									</div>
								</div>
								

							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
								 @if($vendedor==null) 	Agregar Corredor @else Editar Corredor @endif
								</button>
							</div>
						</div>
						
					</form>
					
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
