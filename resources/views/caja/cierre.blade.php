@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-md-offset-0">
			<div class="panel panel-default">
				<div class="panel-heading">Cierres de Caja</b>
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Fecha</th>
								<th>Usuario</th>
								<th>0,01</th>
								<th>0,05</th>
								<th>0,10</th>
								<th>0,25</th>
								<th>0,50</th>
								<th>1</th>
								<th>2</th>
								<th>5</th>
								<th>10</th>
								<th>20</th>
								<th>50</th>
								<th>100</th>
								<th>200</th>
								<th>500</th>
								<th>Imp. Cheque</th>
								<th>Saldo</th>
								</tr>
						</thead>
						<tbody>

@foreach($cierres as $cierre)
<tr> 
<th> <a href='{{url("caja/".date("d-m-Y", strtotime($cierre->created_at)))}}'> {{ date('d-m-Y', strtotime($cierre->created_at)) }} </a></th> 
<th> {{ $cierre->users->name}} </th>
<td> {{ $cierre->mon_01}} </td>
<td> {{ $cierre->mon_05}} </td>
<td> {{ $cierre->mon_010}} </td>
<td> {{ $cierre->mon_025}} </td>
<td> {{ $cierre->mon_050}} </td>
<td> {{ $cierre->mon_1}} </td>
<td> {{ $cierre->mon_2}} </td>
<td> {{ $cierre->mon_5}} </td>
<td> {{ $cierre->mon_10}} </td>
<td> {{ $cierre->mon_20}} </td>
<td> {{ $cierre->mon_50}} </td>
<td> {{ $cierre->mon_100}} </td>
<td> {{ $cierre->mon_200}} </td>
<td> {{ $cierre->mon_500}} </td>
<td> {{ number_format($cierre->cheques_importe,2,',','.')}} </td>
<th> {{ number_format($cierre->saldo,2,',','.')}} </th> 

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
