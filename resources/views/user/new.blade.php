@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Agregar Usuario</div>
				<div class="panel-body">
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
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					<form class="form-horizontal" role="form" method="POST" action="/newuser">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Datos personales</legend>

								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

								<div class="form-group">
									<label class="col-md-4 control-label">Nombre</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Apellido</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}" required>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">E-Mail</label>
									<div class="col-md-6">
										<input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Contraseña</label>
									<div class="col-md-6">
										<input type="password" class="form-control" name="password" required>
										<span id="helpBlock" class="help-block">Mínimo 8 caracteres.</span>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Confirmar Contraseña</label>
									<div class="col-md-6">
										<input type="password" class="form-control" name="password_confirmation" required>
									</div>
								</div>


								<div class="form-group">
									<label class="col-md-4 control-label">Tipo de Usuario</label>
									<div class="col-md-6">
										<select class="form-control" name="rol" id="rol" value="{{ old('rol') }}" required>
										<option>Seleccione Rol</option>
										@foreach($roles as $rol)	
										<option value="{{$rol->id}}">{{$rol->rol}}</option>
										@endforeach
									</select>	
									</div>
								</div>

							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
									Crear Usuario
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
