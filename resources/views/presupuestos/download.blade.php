<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Factura</title>
	<style>
	.clearfix:after {
		content: "";
		display: table;
		clear: both;
	}

	a {
		color: #5D6975;
		text-decoration: underline;
	}

	body {

		width: 22cm;  
		margin: 0 auto; 
		color: #001028;
		background: #FFFFFF; 
		font-family: Arial, sans-serif; 
		font-size: 12px; 
		font-family: Arial;
	}

	#logo {
		text-align: left;
		margin-bottom: 1px;
	}

	
	hr { 
		background-color: black;
		height: 1px; 
	}

	#project {
		float: left;
	}

	#project span {
		color: #5D6975;
		text-align: right;
		width: 52px;
		margin-right: 10px;
		display: inline-block;
		font-size: 0.8em;
	}

	#cbte_cabecera {
		float: right;
		text-align: left;
	}
	
	#project div,
	#company div {
		white-space: nowrap;        
	}

	table {
                width: 90%;
		border-spacing: 0;
		margin-bottom: 20px;
	}

	table tr:nth-child(2n-1) td {
		background: #F5F5F5;
	}

	table th,
	table td {
		text-align: center;
	}

	table th {
		padding: 5px 20px;
		color: #5D6975;
		border-bottom: 1px solid #C1CED9;
		white-space: nowrap;        
		font-weight: normal;
	}

	table .service,
	table .desc {
		text-align: left;
	}

	table td {
		padding: 20px;
		text-align: right;
	}

	table td.service,
	table td.desc {
		vertical-align: top;
	}

	table td.unit,
	table td.qty,
	table td.total {
		font-size: 1.2em;
	}

	table td.grand {
		border-top: 1px solid #5D6975;;
	}

	#notices .notice {
		color: #5D6975;
		font-size: 1.2em;
	}

	footer {
		color: #5D6975;
		width: 100%;
		height: 30px;
		position: absolute;
		bottom: 0;
		border-top: 1px solid #C1CED9;
		padding: 8px 0;
		text-align: center;
	}

	#titulo {
		font-size: 30px;
		text-align: center;
		width: 500px;
		height: 0px;
		margin-top: -85px;
		margin-left: 30px;
	}
</style>
</head>
<body>
	
		
	<main style="margin-top: 180px;">
		<table>
			<thead>
				<tr>
					<th>Código</th>
					<th>Producto/Servicio</th>
					<th>Cantidad</th>
					<th>Precio Unit.</th>
                                        <th>I.V.A.</th>
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
					<td>{{ '$ '.number_format($line->imp_iva,2) }} </td>
					<td> {{ '$ '.number_format($line->subtotal,2) }} </td>
				</tr>
				<?php

				if($line->tipo_iva == $iva_21){
					$iva_imp_21 = $iva_imp_21 + $line->imp_iva;
				}
				?>
				@endforeach
				<tr>
					<td colspan="5">SUBTOTAL</td>
					<td class="total">{{'$ '.number_format($invoice->imp_net,2)}}</td>
				</tr>
				<tr>
					<td colspan="5">I.V.A. 21%</td>
					<td class="total">{{'$ '.number_format($iva_imp_21,2)}}</td>
				</tr>
				<tr>
					<td colspan="5" class="grand total">TOTAL</td>
					<td class="grand total">{{'$ '.number_format($invoice->imp_total,2)}}</td>
				</tr>
			</tbody>
		</table>
		
	</main>
</body>
</html>