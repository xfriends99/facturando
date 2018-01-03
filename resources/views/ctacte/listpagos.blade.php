@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Detalles de Pagos del Cbte. N° {{$invoice}}
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Fecha</th>
								<th>Medio de Pago</th>
								<th>Monto</th>
								<th>Usuario</th>
								<th>Acción</th>
								</tr>
						</thead>
						<tbody>
							@foreach($pagos as $pago)
							<tr>
								<th>{{ date('d-m-Y',strtotime($pago->created_at))}}</td>
								<td>@if ($pago->medios_pagos_id!=null) {{ $pago->medio_pago->tipo }} @else {{ $pago->otro }} @endif</td>
								<td>{{ '$ '.number_format($pago->pago,2)  }}</td>
								<td>{{ $pago->users->name . ' ' . $pago->users->lastname  }}</td>
								<td><a href= "/eliminarPago/{{$pago->id}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar Pago</a></td>
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
