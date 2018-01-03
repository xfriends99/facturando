@extends('app')


@section('content')
<div class="container-fluid">
	<div class="row">
	
		<div class="col-md-12 col-md-offset-0">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle de Caja
				</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="get" action="/reporteCajaEspecial">
						<div class="list-group">
							<div class="list-group-item">
								<legend>Período seleccionado: @if(isset($hoy)) {{ date('d-m-Y',strtotime($hoy)) }} @else Desde: {{ date('d-m-Y',strtotime($desde)) }} Hasta: {{ date('d-m-Y',strtotime($hasta)) }} @endif</legend>
								<div class="form-group">
									<label class="col-md-4 control-label">Desde</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="desde" value="{{ old('desde') }}" required>
									</div>
								</div>							
								<div class="form-group">
									<label class="col-md-4 control-label">Hasta</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="hasta" value="{{ old('hasta') }}"  required>
									</div>
								</div>	

								<div class="form-group">
							<div class="col-md-4 col-md-offset-4"><br/>
								<button type="submit" class="btn btn-primary">
									Consultar!
								</button>
							</div>
						</div>

				</form>
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
				
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Fecha</th>
								<th>Ingreso</th>
								<th>Egreso</th>
								<th>Saldo</th>
								<th>Concepto</th>
								<th>Usuario</th>
                                                                <th>Detalle</th>
								</tr>
						</thead>
						<tbody>
						
							@foreach($movimientos as $linea)
                                                            
                                                        <tr> 
                                                        <th> {{ date('d-m-Y', strtotime($linea->created_at)) }} </th>
                                                        <td style="color: green;"> @if($linea->tipo_movimiento_caja == 1) {{number_format($linea->importe,2,',','.')}} @endif </td>
                                                        <td style="color: red;"> @if($linea->tipo_movimiento_caja == 2) {{number_format($linea->importe,2,',','.')}} @endif </td>
                                                        <th @if($linea->saldo < 0) style="color: red;" @endif> @if($linea->saldo < 0) -{{number_format($linea->saldo,2,',','.')}} @else {{number_format($linea->saldo,2,',','.')}} @endif</th>
                                                        <td>{{$linea->concepto->concepto}}</td>
                                                        <td>{{$linea->users->name}}</td>
                                                        <td>{{$linea->detalle}}</td>
                                                        </tr>


                                                        @endforeach
						</tbody>
					</table>
					
					
							
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
