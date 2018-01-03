@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Alta Proveedor</div>
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
						<input type="hidden" name="company_type" value="2">
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
										<input type="text" class="form-control" oninput='checkString(this.id)' name="tax_id" id="tax_id" value="{{ old('tax_id') }}" required> 
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
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
									Crear
								</button>
							</div>
						</div>
						
					</form>
							</div>
						</div>
					@endsection