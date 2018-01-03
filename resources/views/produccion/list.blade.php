@extends('app')


@section('content')
<div class="container-fluid">
	<div class="row">
	
		<div class="col-md-12 col-md-offset-0">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle de Ventas
				</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="post" action="/viewProdu">
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
								<th>Producto</th>
								<th>KG</th>
								<th>Maquina</th>
								<th>Fecha</th>
							</tr>
						</thead>
						<tbody>  <?php $total = 0; ?>
							@foreach($productos as $prod)
                                                         <?php    $prodKG = number_format($prod->kg,2,',','.');
                                                                  $total = $total + $prodKG; 
                                                                                              ?>
							<tr>
                                    
								<th>{{$prod->codigo}}</td>
                                                                
								<td>{{$prodKG}} </td>
								<td>{{$prod->users->name . ' ' . $prod->users->lastname}}</td>
								<td>{{date('d-m-Y H:i',strtotime($prod->created_at))}}</td>
							</tr>
                                                       @endforeach
                                                        <tr>
                                                                <th></td>
								<td>{{number_format($total,2,',','.')}} </td>
								<td></td>
								<td></td>
                                                        <tr>
						</tbody>
					</table>
					
					
							
				</div>
			</div>
		</div>
	</div>
</div>
@endsection