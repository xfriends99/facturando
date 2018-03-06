@extends('app')

@section('content')
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">Editar Producto</div>
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
							<div class="alert alert-info">{{ Session::get('message')}}</div>
						@endif
						<form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{url('/products/'.$product->id.'/update')}}">

							<div class="list-group">
								<div class="list-group-item">
									<legend>Producto: {{$product->descripcion}}</legend>

									<input type="hidden" name="_token" value="{{ csrf_token() }}">

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="col-md-4 control-label">Referencia</label>
												<div class="col-md-6">
													<input type="text" readonly class="form-control" name="reference-read" value="{{$product->reference}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Descripción</label>
												<div class="col-md-6">
													<textarea readonly class="form-control" name="descripcion">{{$product->descripcion}}</textarea>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Peso referencia</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="pesoRef" value="{{$product->pesoRef}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Diámetro referencia</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="diametroRef" value="{{$product->diametroRef}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Metros referencia</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="metrosRef" value="{{$product->metrosRef}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Rollos referencia</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="rollosRef" value="{{$product->rollosRef}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Tipo de operación</label>
												<div class="col-md-6">
													<select class="form-control" name="operacion" >
														<option value="" >Seleccione</option>
														<option value="I" @if($product->operacion=='I') selected @endif>Intercalado</option>
														<option value="R" @if($product->operacion=='R') selected @endif>Rebobinado</option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Peso de la Manga Fabricada</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="peso_manga" value="{{$product->peso_manga}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Diámetro</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="diametro" value="{{$product->diametro}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Metros que tiene la Manga Fabricada</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="cant_metros" value="{{$product->cant_metros}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Cantidad de Cortes que salen por manga</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="cant_por_man" value="{{$product->cant_por_man}}" >
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="col-md-4 control-label">Cantidad de Cortes por Pack</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="cant_por_pack" value="{{$product->cant_por_pack}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Peso en Kg. Por Pack</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="peso_por_pack" value="{{$product->peso_por_pack}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Tiempo que tarda el rebobinado de la Manga</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="tmpo_reb" value="{{$product->tmpo_reb}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Cantidad de Empleados Utilizados para Rebobinar</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="emp_util_reb" value="{{$product->emp_util_reb}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Tiempo que tarda el Corte de la Manga</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="tmpo_corte" value="{{$product->tmpo_corte}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Cantidad de Empleados Utilizados para Cortar</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="emp_util_corte" value="{{$product->emp_util_corte}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Tiempo que tarda el Empaque del Pack</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="tmpo_empq" value="{{$product->tmpo_empq}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Cantidad de Empleados Necesarios para Empaque</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="emp_util_emp" value="{{$product->emp_util_emp}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Cantidad de Packs que hay en Deposito</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="stock_Fisico" value="{{$product->stock_Fisico}}" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label">Cantidad de Packs que estan comprometidos para Venta</label>
												<div class="col-md-6">
													<input type="text" class="form-control" name="stock_Pedido" value="{{$product->stock_Pedido}}" >
												</div>
											</div>
										</div>
									</div>


									<div class="form-group">
										<div class="col-md-6 col-md-offset-5"><br/>
											<button type="submit" class="btn btn-primary">
												Editar Producto
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
