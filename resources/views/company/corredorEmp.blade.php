@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Editar Asignación</div>
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
					<form class="form-horizontal" role="form" method="POST" action="/editAsig">
					<input type="hidden" name="company_id" value="{{ $company->id_customer}}">
						<div class="list-group">
							<div class="list-group-item">
<?php  $customer = app\Customer::find($company->id_customer); ?>
								<legend>Corredor Asignado de {{ $customer->firstname . ' ' . $customer->lastname}}</legend>
								
								<div class="form-group">
									<label class="col-md-4 control-label">Corredor</label>
									<div class="col-md-6">
										<select class="form-control" name="corr_id" id="corr_id" value="{{ old('corr_id') }}" required>
											@foreach($corredores as $corr)
											<option value="{{$corr->id}}" @if($company->corredores_id==$corr->id) selected @endif>{{$corr->nombre}}</option>
											@endforeach
										</select>	
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
						
					@endsection