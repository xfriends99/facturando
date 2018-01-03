@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Agregar Pago</div>
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
					<form class="form-horizontal" role="form" method="POST" action="/newPago">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Detalle del Pago</legend>

								<input type="hidden" name="ctacte_id" value="{{ $id }}">

								<div class="form-group">
									<label class="col-md-4 control-label">Medio de Pago</label>
									<div class="col-md-6">
										<select class="form-control" name="mpago" id="mpago" value="{{ old('mpago') }}" required>
										<option value="">Seleccione Medio</option>
										@foreach($mpagos as $mpago)	
										<option value="{{$mpago->id}}">{{$mpago->tipo}}</option>
										@endforeach
										<option value="otro">Otro</option>
									</select>	
									</div>
								</div>
								

								<div id="otro" >
									</div>
								

								<div class="form-group">
									<label class="col-md-4 control-label">Monto</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="monto" value="{{ old('monto') }}" required>
									</div>
								</div>
								

							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
									Agregar Pago
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
