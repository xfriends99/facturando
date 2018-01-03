@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Conceptos de Caja</b>
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Concepto</th>
								<th>Acción</th>								
								</tr>
						</thead>
						<tbody>

@foreach($conceptos as $concepto)
<tr> 
<th> {{ $concepto->concepto }} </th>
<td><a href="/deleteConceptoCaja/{{$concepto->id}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger">Eliminar</a></td>
</tr>
@endforeach
							
						</tbody>
					</table>

					<form class="form-horizontal" role="form" method="POST" action="/conceptosCaja">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Nuevo concepto</legend>


                                                                <div class="form-group">
									<label class="col-md-4 control-label">Concepto</label>
									<div class="col-md-3">
										<input type="text" class="form-control" name="concepto"                          value="{{ old('concepto') }}" required>
									</div>
								</div>


								

						<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" class="btn btn-primary">
									Agregar Concepto
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
