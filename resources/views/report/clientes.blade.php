@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Clientes
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
				
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Razón Social</th>
                                                                <th>C.U.I.T.</th>
                                                                <th>Transporte</th>
                                                                <th>Dir. Transporte</th>
                                                                <th>Tel. Transporte</th>
								<th>Corredor Asignado</th>								
								</tr>
						</thead>
						<tbody>
							@foreach($clientes as $company)
							
<?php  $customer = app\Customer::find($company->id_customer); ?>
<tr>
                                                                @if($customer!=null)
								<td>{{ $customer->firstname . ' ' . $customer->lastname}}</td>
                                                                <td>{{ $company->tax_number}}</td>
                                                                <td>{{ $company->transporte}}</td>
                                                                <td>{{ $company->direccion}}</td>
                                                                <td>{{ $company->telefono}}</td>
								<td>{{ $company->corredor->nombre }}</td>
								
                                                    </tr>
@endif
							@endforeach
                                                        
						</tbody>
					</table>
					
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

