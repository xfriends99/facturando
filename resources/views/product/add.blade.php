@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Nuevo Producto</div>
				<div class="panel-body">
					@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Ups!</strong> Existen los siguientes errores.<br><br>
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
					<form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{url('/products/store')}}">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Producto</legend>

								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-4 control-label">Código</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Descripción</label>
											<div class="col-md-6">
												<textarea class="form-control" name="descripcion"></textarea>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Peso referencia</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="pesoRef" value="{{ old('pesoRef') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Diámetro referencia</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="diametroRef" value="{{ old('diametroRef') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Metros referencia</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="metrosRef" value="{{ old('metrosRef') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Rollos referencia</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="rollosRef" value="{{ old('rollosRef') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Tipo de operación</label>
											<div class="col-md-6">
												<select class="form-control" name="operacion" >
													<option value="" selected>Seleccione</option>
													<option value="I" >Intercalado</option>
													<option value="R" >Rebobinado</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Peso de la Manga Fabricada</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="peso_manga" value="{{ old('peso_manga') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Diámetro</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="diametro" value="{{ old('diametro') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Metros que tiene la Manga Fabricada</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="cant_metros" value="{{ old('cant_metros') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Cantidad de Cortes que salen por manga</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="cant_por_man" value="{{ old('cant_por_man') }}" >
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-4 control-label">Cantidad de Cortes por Pack</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="cant_por_pack" value="{{ old('cant_por_pack') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Peso en Kg. Por Pack</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="peso_por_pack" value="{{ old('peso_por_pack') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Tiempo que tarda el rebobinado de la Manga</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="tmpo_reb" value="{{ old('tmpo_reb') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Cantidad de Empleados Utilizados para Rebobinar</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="emp_util_reb" value="{{ old('emp_util_reb') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Tiempo que tarda el Corte de la Manga</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="tmpo_corte" value="{{ old('tmpo_corte') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Cantidad de Empleados Utilizados para Cortar</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="emp_util_corte" value="{{ old('emp_util_corte') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Tiempo que tarda el Empaque del Pack</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="tmpo_empq" value="{{ old('tmpo_empq') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Cantidad de Empleados Necesarios para Empaque</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="emp_util_emp" value="{{ old('emp_util_emp') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Cantidad de Packs que hay en Deposito</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="stock_Fisico" value="{{ old('stock_Fisico') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Cantidad de Packs que estan comprometidos para Venta</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="stock_Pedido" value="{{ old('stock_Pedido') }}" >
											</div>
										</div>
									</div>
								</div>


								<div class="form-group">	
									<div class="col-md-6 col-md-offset-5"><br/>
										<button type="submit" class="btn btn-primary">
											Crear Producto
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
