@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">  @if(isset($date)) <b> Caja {{ date('d-m-Y', strtotime($date)) }} </b> @else <b>Caja @if($ultimo_cierre > $today) Cerrada @else Abierta @endif </b>
<a href= @if($ultimo_cierre > $today) "#" @else "/cerrarCaja" @endif style="margin-top:-7px; float:right;" class="btn btn-info">Cerrar Caja</a>
&nbsp;&nbsp;&nbsp;<a href="/movimiento" style="margin-top:-7px; float:right;margin-right: 25px;" class="btn btn-success">Nuevo Movimiento</a> @endif

				</div>
				<div class="panel-body">
					
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
@if($cierreAnterior!=null)
<tr> 
<th> {{ date('d-m-Y', strtotime($cierreAnterior->created_at)) }} </th>
<td></td>
<td></td>
<th>{{ number_format($cierreAnterior->saldo,2,',','.')}}</th>
<td></td>
<th>{{$cierreAnterior->users->name}}</th>
<th>Saldo Anterior</th>
</tr>
@endif

@foreach($caja as $linea)
<tr> 
<th> {{ date('d-m-Y', strtotime($linea->created_at)) }} </th>
<td style="color: green;"> @if($linea->tipo_movimiento_caja == 1) {{'$ '.number_format($linea->importe,2,',','.')}} @endif </td>
<td style="color: red;"> @if($linea->tipo_movimiento_caja == 2) {{'$ '.number_format($linea->importe,2,',','.')}} @endif </td>
<th>{{number_format($linea->saldo,2,',','.')}}</th>
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
