@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
				<div class="panel-heading">Nro. de Remito</div>
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
					<form class="form-horizontal" role="form" method="POST" action="/crearRemito">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Nro. de Remito</legend>

								<div class="form-group">
									<label class="col-md-4 control-label">Número de Remito</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="nro_remito" value="{{ old('nro_remito') }}" required>
									</div>
								</div>
                                                      @if($customer==null)  
<legend>Datos Fiscales</legend>
                                                         <div class="form-group">
									<label class="col-md-4 control-label">Tipo de Doc.</label>
									<div class="col-md-6">
										<select class="form-control" name="tax_type" id="tax_type" value="{{ old('tax_type') }}" required>
											<option value="">Seleccione Doc.</option>
											@foreach($taxes as $tax)
											<option value="{{$tax->id}}">{{$tax->type}}</option>
											@endforeach
										</select>	
									</div>
								</div>


<div class="form-group">
									<label class="col-md-4 control-label">Número de Doc.</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="tax_number" value="{{  $order->direccion_factura->vat_number }}"  id="tax_number" oninput='checkString(this.id)' required>
									</div>
								</div>


                                                              <div class="form-group">
									<label class="col-md-4 control-label">Sit. frente al I.V.A.</label>
									<div class="col-md-6">
										<select class="form-control" name="fiscal_sit" id="fiscal_sit" value="{{ old('fiscal_sit') }}" required>
											<option value="">Seleccione Sit.</option>
											@foreach($fiscal_situations as $fisc_sit)
											<option value="{{$fisc_sit->id}}">{{$fisc_sit->fisc_situation}}</option>
											@endforeach
										</select>	
									</div>
								</div>
@endif
<legend>Datos del Corredor</legend>
                                                        <div class="form-group">
									<label class="col-md-4 control-label">Corredor</label>
									<div class="col-md-6">
										<select class="form-control" name="corredor" id="corredor" value="{{ old('corredor') }}" required>
											
											@foreach($corredores as $corredor)
											<option value="{{$corredor->id}}" @if($cliente!= null && $corredor->id==$cliente->transporte) selected @endif>{{$corredor->nombre}}</option>
											@endforeach
										</select>	
									</div>
								</div>
<legend>Datos del Transporte</legend>
<div class="form-group">
<label class="col-md-4 control-label">Transporte</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="transporte" value="@if($cliente!=null){{$cliente->transporte}}@endif"  id="transporte" >
									</div>
</div>
<div class="form-group">
<label class="col-md-4 control-label">Dirección</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="direccion" value="@if($cliente!=null){{$cliente->direccion}}@endif"  id="direccion"  >
									</div>
</div>
<div class="form-group">
<label class="col-md-4 control-label">Teléfono</label>
									<div class="col-md-6">
<input type="text" class="form-control" name="telefono" value="@if($cliente!=null){{$cliente->telefono}}@endif"  id="telefono"  >
									</div>
</div>
 @if($customer!=null) 
<input type="hidden" name="customer" value="{{ $customer }}" required>
@endif

							</div>
						</div>
<input type="hidden" name="nro_orden" value="{{ $nro_orden }}" required>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
									Generar Remito
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