@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Agregar Movimiento de Caja</div>
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
					<form class="form-horizontal" role="form" method="POST" action="/newMovimientoEspecial">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Detalle del Movimiento</legend>

								<input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

								<div class="form-group">
									<label class="col-md-4 control-label">Tipo de Movimiento</label>
									<div class="col-md-6">
										<select class="form-control" name="tipo_movimiento_caja" id="tipo_movimiento_caja" value="{{ old('tipo_movimiento_caja') }}" required>
										<option value="">Seleccione Tipo</option>											
										<option value="1">Ingreso</option>										
										<option value="2">Egreso</option>
									</select>	
									</div>
								</div>

                                                                <div class="form-group">
									<label class="col-md-4 control-label">Importe</label>
									<div class="col-md-3">
										<input type="text" class="form-control" name="importe"                          value="{{ old('importe') }}" required>
									</div>
								</div>


								<div class="form-group">
									<label class="col-md-4 control-label">Concepto</label>
									<div class="col-md-6">
										<select class="form-control" name="conceptos_caja_id" id="conceptos_caja_id" value="{{ old('conceptos_caja_id') }}" required>
										<option value="">Seleccione Concepto</option>
										@foreach($conceptos as $concepto)	
										<option value="{{$concepto->id}}">{{$concepto->concepto}}</option>
										@endforeach
										
									</select>	
									</div>
								</div>
								

                                                                <div class="form-group">
									<label class="col-md-4 control-label">Detalle</label>
									<div class="col-md-6">
								  <input type="text" class="form-control" name="detalle"                          value="{{ old('detalle') }}">
									</div>
								</div>
								
								

							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
									Agregar Movimiento
								</button>
							</div>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#mpago').change(function(){
		var mpagoID = $(this).val();
		if(mpagoID!='otro'){
    		$('#otro').html("");	
    	}else{
    		$('#otro').html("<div class='form-group'> <label class='col-md-4 control-label'>Otro</label> <div class='col-md-6'> <input type='text' class='form-control' name='otro' required> </div> </div>");

    	}

		
	});
</script>
@endsection
