<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>FACTURANDO!</title>
        <style>
         body {font-size:12px;}   
        </style>

	</head>

	<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<section>

						<p>	Razón Social: {{$invoice->company_name}} </p>
						<p>	Presupuesto </p>

					</section>	
					<section>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Código</th>
									<th>Producto/Servicio</th>
									<th>Cantidad</th>
									<th>Precio Unit.</th>
									<th>I.V.A.</th>
									<th>Imp. I.V.A.</th>
									<th>Subtotal</th>
								</tr>
							</thead>
							<tbody>
	<?php							
	 
      $iva_21 = 5;
      $iva_imp_21 = 0;
      ?>
								@foreach($lines as $line)
							<tr>
								<th>{{ $line->code  }}</th>
								<td>{{ $line->name }}</td>
								<td>{{ $line->quantity }}</td>
								<td>{{ '$ '.number_format($line->price,2) }} </td>
								<td>{{ $line->tipo_iva_id->tipo_iva }} </td>
								<td>{{ '$ '.number_format($line->imp_iva,2) }} </td>
								<td> {{ '$ '.number_format($line->subtotal,2) }} </td>
							</tr>
<?php
   
 
      	if($line->tipo_iva == $iva_21){
      		$iva_imp_21 = $iva_imp_21 + $line->imp_iva;
      	}
      	
?>
							@endforeach
							</tbody>
						</table>
					</section>
					<section>
					<p><b>Subtotal: {{'$ '.number_format($invoice->imp_net,2)}} </p>
					<p>I.V.A. 21%: {{'$ '.number_format($iva_imp_21,2)}} </p>
					<p>Total: {{'$ '.number_format($invoice->imp_total,2)}} </p></b>
					</section>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
