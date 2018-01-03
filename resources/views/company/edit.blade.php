@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Editar Cliente</div>
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
					<form class="form-horizontal" role="form" method="POST" action="/editCustomer">
					<input type="hidden" name="company_type" value="{{ $company->companies_type_id }}">
					<input type="hidden" name="company_id" value="{{ $company->id}}">
						<div class="list-group">
							<div class="list-group-item">
								<legend>Información de la Empresa</legend>
								<div class="form-group">
									<label class="col-md-4 control-label">Razón Social</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="company_name" value="{{ $company->company_name }}" required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Tipo de Doc.</label>
									<div class="col-md-6">
										<select class="form-control" name="tax_type" id="tax_type" value="{{ old('tax_type') }}" required>
											<option value="">Seleccione Doc.</option>
											@foreach($taxes as $tax)
											<option value="{{$tax->id}}" @if($company->tax_type_id==$tax->id) selected @endif>{{$tax->type}}</option>
											@endforeach
										</select>	
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Nro. de Doc.</label>
									<div class="col-md-6">
										<input type="text" class="form-control" oninput='checkString(this.id)' name="tax_id" id="tax_id" value="{{ $company->tax_id }}" required> 
									</div> 
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Sit. frente al I.V.A.</label>
									<div class="col-md-6">
										<select class="form-control" name="fiscal_sit" id="fiscal_sit" value="{{ old('fiscal_sit') }}" required>
											<option value="">Seleccione Sit.</option>
											@foreach($fiscal_situations as $fisc_sit)
											<option value="{{$fisc_sit->id}}" @if($company->fiscal_situation_id==$fisc_sit->id) selected @endif>{{$fisc_sit->fisc_situation}}</option>
											@endforeach
										</select>	
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Teléfono</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="tel" value="{{ $company->tel }}" required> 
									</div> 
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Fax</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="fax" value="{{ $company->fax }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Sitio Web</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="web" value="{{ $company->website }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Email</label>
									<div class="col-md-6">
										<input type="email" class="form-control" name="email" value="{{ $company->email }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Dirección</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="address" value="{{ $company->addresses->address }}" required>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Piso</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="floor" value="{{ $company->addresses->floor }}"> 
									</div> <label class="col-md-2 control-label">Puerta</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="door" value="{{ $company->addresses->door }}"> 
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">País</label>
									<div class="col-md-6">
										<select class="form-control" name="country" id="country" required>
											<option value="">Seleccione País</option>
											@foreach($countries as $country)
											<option value="{{$country->id}}"@if($company->addresses->countries->id==$country->id) selected @endif>{{$country->country}}</option>
											@endforeach
										</select>	
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Estado</label>
									<div class="col-md-6">
										<select class="form-control" name="state" id="state" required>
											<option>Seleccione Estado</option>
											@foreach($states as $state)
											<option class="stateItems" value="{{$state->id}}"@if($company->addresses->states->id==$state->id) selected @endif>{{$state->state}}</option>
											@endforeach
										</select>	
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Ciudad</label>
									<div class="col-md-3">
										<input type="text" class="form-control" name="city" value="{{ $company->addresses->city }}" required> 
									</div> <label class="col-md-1 control-label">CP</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="post_code" value="{{ $company->addresses->post_code }}" required> 
									</div>
								</div>

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