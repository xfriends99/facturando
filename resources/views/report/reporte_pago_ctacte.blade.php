@extends('app')


@section('content')
<div class="container-fluid">
	<div class="row">
	
		<div class="col-md-12 col-md-offset-0">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle de Pagos de Cta. Cte.
				</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="get" action="/listadoCtaCte">
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
								<th>Medio de Pago</th>
								<th>Monto</th>
								<th>Razón Social</th>
                                <th>Usuario</th> 
						        </tr>
						</thead>
						<tbody>
					        
					        @if($movimientos!=null)
					        @foreach($movimientos as $movimiento)

							<tr>
                                    
                                 <td>{{ date('d-m-Y',strtotime($movimiento->created_at))  }} </td>
							     <td>@if ($movimiento->medios_pagos_id!=null) {{ $movimiento->medio_pago->tipo }} @else {{ $movimiento->otro }} @endif</td>
    							 <td>{{ number_format($movimiento->pago,2,',','.')  }}</td>
    							 <td>{{ $movimiento->cta_cte->facturas->company_name  }}</td>
    							 <td>{{ $movimiento->users->name . ' ' . $movimiento->users->lastname  }}</td>
    							    
                             </tr>
                            
                            @endforeach
                            @endif

						</tbody>
					</table>
					
					
							
				</div>
			</div>
		</div>
	</div>
</div>
@endsection