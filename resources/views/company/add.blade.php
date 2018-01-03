@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Alta Cliente</div>
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
					<form class="form-horizontal" role="form" method="POST" action="/createCustomer">
						<input type="hidden" name="company_type" value="1">
						<div class="list-group">
							<div class="list-group-item">
								<legend>Información de la Empresa</legend>
								<div class="form-group">
									<label class="col-md-4 control-label">Razón Social</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="company_name" value="{{ old('company_name') }}"  required>
									</div>
								</div>							
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
									<label class="col-md-4 control-label">Nro. de Doc.</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="tax_id" value="{{ old('tax_id') }}" id="tax_id" oninput='checkString(this.id)' required> 
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
								<div class="form-group">
									<label class="col-md-4 control-label">Teléfono</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="tel" value="{{ old('tel') }}"> 
									</div> 
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Fax</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="fax" value="{{ old('fax') }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Sitio Web</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="web" value="{{ old('web') }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Email</label>
									<div class="col-md-6">
										<input type="email" class="form-control" name="email" value="{{ old('email') }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Dirección</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="address" value="{{ old('address') }}" required>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Piso</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="floor" value="{{ old('floor') }}"> 
									</div> <label class="col-md-2 control-label">Puerta</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="door" value="{{ old('door') }}"> 
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">País</label>
									<div class="col-md-6">
									<select class="form-control" name="country" id="country" value="{{ old('country') }}" required>
											<option value="">Seleccione País</option>
											@foreach($countries as $country)
										<option value="{{$country->id}}">{{$country->country}}</option>
											@endforeach
										</select>	
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Estado</label>
									<div class="col-md-6">
										<select class="form-control" name="state" id="state" value="{{ old('state') }}" required>
										<option>Seleccione Estado</option>	
									</select>	
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Ciudad</label>
									<div class="col-md-3">
										<input type="text" class="form-control" name="city" value="{{ old('city') }}" required> 
									</div> <label class="col-md-1 control-label">CP</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="post_code" value="{{ old('post_code') }}" required> 
									</div>
								</div>

							<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
									Crear
								</button>
							</div>
						</div>
						
					</form>
							</div>
						</div>
						<script type="text/javascript">
						$('#country').change(function(){
							var countryId = $(this).val();
							$ciudaditems = $('.stateItems').remove();
							$.post("{{ URL::to('states') }}"+'/'+countryId, function(data){
								$.each(data, function(index, element){
	        	//console.log(element);
	        	$('#state').append('<option value="'+element.id+'" class="stateItems">'+element.state+'</option>')
	        });
							}, 'json');
						});
					</script>
					@endsection