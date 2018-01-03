@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Cerrar Caja Diaria</div>
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
					<form class="form-horizontal" role="form" method="POST" action="/cerrarCaja">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Detalle del Movimiento</legend>

								<input type="hidden" name="user_id" value="{{ Auth::user()->id }}">					

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">1 Ctvo.</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_01"                          value="{{ old('mon_01') }}" value="0">
									</div>
								</div>
								
                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">5 Ctvos.</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_05"                          value="{{ old('mon_05') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">10 Ctvos.</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_010"                          value="{{ old('mon_010') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">25 Ctvos.</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_025"                          value="{{ old('mon_025') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">50 Ctvos.</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_050"                          value="{{ old('mon_050') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">1 Peso</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_1"                          value="{{ old('mon_1') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">2 Pesos</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_2"                          value="{{ old('mon_2') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">5 Pesos</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_5"                          value="{{ old('mon_5') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">10 Pesos</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_10"                          value="{{ old('mon_10') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">20 Pesos</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_20"                          value="{{ old('mon_20') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">50 Pesos</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_50"                          value="{{ old('mon_50') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">100 Pesos</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_100"                          value="{{ old('mon_100') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">200 Pesos</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_200"                          value="{{ old('mon_200') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">500 Pesos</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="mon_500"                          value="{{ old('mon_500') }}" value="0">
									</div>
								</div>

                                                                <div class="form-group col-md-6">
									<label class="col-md-4 control-label">Importe Cheques</label>
									<div class="col-md-2">
					<input type="text" class="form-control" name="cheques_importe" value="{{ old('cheques_importe') }}" value="0">
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
