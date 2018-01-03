@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Ver Comprobante</div>
				<div class="panel-body">
					
					<section>

						<p>	Razón Social: {{$invoice->company_name}} </p>
						<p>	Dirección: {{$invoice->address}} </p>
						<p>	I.V.A.: Responsable Inscripto </p>
						<p>	{{'C.U.I.T.' . ': ' . $invoice->tax_id}} </p>

					</section>	
					<section>
						<table id="tblData" class="table table-bordered">
							<thead>
								<tr>
									<th>Código</th>
									<th>Producto/Servicio</th>
									<th>Cantidad</th>
									<th>U. Medida</th>
									<th>Precio Unit.</th>
									<th>% Bonif.</th>
									<th>Imp. Bonif.</th>
									<th>I.V.A.</th>
									<th>Imp. I.V.A.</th>
									<th>Subtotal</th>
								</tr>
							</thead>
							<tbody>
	<?php							
	  $iva_10_5 = 4;
      $iva_21 = 5;
      $iva_27 = 6;
      $iva_imp_10_5 = 0;
      $iva_imp_21 = 0;
      $iva_imp_27 = 0;
      ?>
								@foreach($lines as $line)
							<tr>
								<th>{{ $line->code  }}</th>
								<td>{{ $line->name }}</td>
								<td>{{ $line->quantity }}</td>
								<td> </td>
								<td>{{ '$ '.number_format($line->price,2) }} </td>
								<td>{{ $line->por_desc }} </td>
								<td>{{ '$ '.number_format($line->imp_desc,2) }} </td>
								<td>{{ $line->tipo_iva_id->tipo_iva,2 }} </td>
								<td>{{ '$ '.number_format($line->imp_iva,2) }} </td>
								<td> {{ '$ '.number_format($line->subtotal,2) }} </td>
							</tr>
<?php
   
      	if($line->tipo_iva == $iva_10_5){
      		$iva_imp_10_5 = $iva_imp_10_5 + $line->imp_iva;
      	}
      	if($line->tipo_iva == $iva_21){
      		$iva_imp_21 = $iva_imp_21 + $line->imp_iva;
      	}
      	if($line->tipo_iva == $iva_27){
      		$iva_imp_27 = $iva_imp_27 + $line->imp_iva;
      	}
?>
							@endforeach
							</tbody>
						</table>
					</section>
					<section>
					<p>Subtotal: {{'$ '.number_format($invoice->imp_net,2)}} </p>
					<p>I.V.A. 10.5%: {{'$ '.number_format($iva_imp_10_5,2)}} </p>
					<p>I.V.A. 21%: {{'$ '.number_format($iva_imp_21,2)}} </p>
					<p>I.V.A. 27%: {{'$ '.number_format($iva_imp_27,2)}} </p>
					<p>Total: {{'$ '.number_format($invoice->imp_total,2)}} </p>
					</section>
					<section>
				<p>C.A.E.: {{$invoice->cae}} </p>
				<p> Fecha de Vto. de C.A.E.: {{ date('d-m-Y',strtotime($invoice->fecha_vto_cae)) }} </p>
				 <?php echo \myFunctions::codigoBarraCAE( env('CUIT'), 1, env('PV'), $invoice->cae, date('Ymd',strtotime($invoice->fecha_vto_cae)));
				 ?>
					</section>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
