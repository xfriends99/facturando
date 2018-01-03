@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Perfil de Usuario</div>
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
					<form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="/editProfile">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Datos personales</legend>

								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="user_id" value="{{ $user->id }}">
								<div class="form-group">
									<label class="col-md-4 control-label">Nombre</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Apellido</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="lastname" value="{{ $user->lastname }}" required>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">E-Mail</label>
									<div class="col-md-6">
										<input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Contraseña</label>
									<div class="col-md-6">
										<input type="password" class="form-control" name="password">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Confirmar Contraseña</label>
									<div class="col-md-6">
										<input type="password" class="form-control" name="password_confirmation">
									</div>
								</div>
								@if(Auth::user()->roles_id==1 && Auth::user()->id!=$user->id)
								<div class="form-group">
									<label class="col-md-4 control-label">Tipo de Usuario</label>
									<div class="col-md-6">
										<select class="form-control" name="rol" id="rol" value="{{ old('rol') }}" required>
										<option>Seleccione Rol</option>
										@foreach($roles as $rol)	
										<option value="{{$rol->id}}" @if($user->roles->id==$rol->id) selected @endif>{{$rol->rol}}</option>
										@endforeach
									</select>	
									</div>
								</div>
								@endif
								

							</div>
						</div>
						@if(Auth::user()->roles_id==1 && Auth::user()->id==$user->id)
						<div class="list-group-item">
							<legend>Empresa</legend>
							<div class="form-group">
								<label class="col-md-4 control-label">Razón Social</label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="company_name" value="{{ $user->companies->company_name }}" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label">C.U.I.T.</label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="tax_id" value="{{ $user->companies->tax_id }}" required>
								</div>
							</div>
							<div class="form-group">
									<label class="col-md-4 control-label">Teléfono</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="tel" value="{{ $user->companies->tel }}" required> 
									</div> 
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Fax</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="fax" value="{{ $user->companies->fax }}">
									</div>
								</div>
							<div class="form-group">
								<label class="col-md-4 control-label">Sitio Web</label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="web" value="{{ $user->companies->website }}">
								</div>
							</div>

							
						  <div class="form-group">
								<label class="col-md-4 control-label">Logo</label>
								<div class="col-md-6">
									<input type="file" accept="image/*" class="form-control" name="logo" >
								</div>
							</div>

						</div>
						@endif
						

						<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
									Editar
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
