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
	<header>

		<div><img src="logo.png"/>

 <div id="cbte_cabecera" style="margin-left:420px; margin-top:-100px;">			
	<div>N°: {{ str_pad(env('PV'), 4, 0, STR_PAD_LEFT) . '-' . str_pad($invoice->nro_cbte, 8, 0, STR_PAD_LEFT)}}</div>
				<div>Fecha: {{date('d-m-Y',strtotime($invoice->fecha_facturacion))}}</div>
				<div>{{env('EMPRESA')}}</div>
				<div>{{env('NOMBREFANTASIA')}}</div>
				<div>C.U.I.T.: {{env('CUIT_MOSTRAR')}}</div>
				<div>Nro. II.BB.: {{env('IIBB')}}</div>
				<div>Fecha Inicio de Act.: {{env('FIA')}}</div>
				<div>{{env('DIRECCION')}}</div>
				<div>TEL/FAX: {{env('TELEFONO')}}</div>
				<div><a href="mailto:{{env('EMAIL')}}">{{env('EMAIL')}}</a></div>

			</div>

			@if($invoice->fisc_situation==1)
			
                        <div id="titulo">FACTURA A</div>   
			@else 
			<div id="titulo">FACTURA B</div>
			@endif
			
		</div>				
               
              <div id="project" style="margin-top: 50px;">
			<hr/>
			<div><span>Razón Social </span> {{$invoice->company_name}} </div>
			<div><span>Dirección </span> {{$invoice->address}} </div>
			<div><span>I.V.A. </span> {{$invoice->fiscal_situation->fisc_situation}} </div>
			<div><span>C.U.I.T. </span> {{ $invoice->tax_id }}</div>
                        @if($invoice->tax_id!=null) <div><span>Orden de compra N°: </span> {{ $invoice->orden_compra }}</div> @endif     
		
		<hr/>
      </div>

	</header>
		
	<main style="margin-top: 180px;">
		@if($invoice->fisc_situation==1)
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

				
				?>
				@endforeach
				<tr>
					<td colspan="5">SUBTOTAL</td>
					<td class="total">{{'$ '.number_format($invoice->imp_net,2)}}</td>
				</tr>
				<tr>
					<td colspan="5">I.V.A. 21%</td>
					<td class="total">{{'$ '.number_format($invoice->imp_iva_21,2)}}</td>
				</tr>
				<tr>
					<td colspan="5" class="grand total">TOTAL</td>
					<td class="grand total">{{'$ '.number_format($invoice->imp_total,2)}}</td>
				</tr>
			</tbody>
		</table>
		@else
		<table>
			<thead>
				<tr>
					<th>Código</th>
					<th>Producto/Servicio</th>
					<th>Cantidad</th>
					<th>Precio Unit.</th>
					<th>Subtotal</th>
				</tr>
			</thead>
			<tbody>
				
				@foreach($lines as $line)
				<tr>
					<th>{{ $line->code  }}</th>
					<td>{{ $line->name }}</td>
					<td>{{ $line->quantity }}</td>
					<td>{{ '$ '.number_format($line->price,2) }} </td>
					<td> {{ '$ '.number_format($line->subtotal,2) }} </td>
				</tr>
				
				@endforeach
				<tr>
					<td colspan="4" class="grand total">TOTAL</td>
					<td class="grand total">{{'$ '.number_format($invoice->imp_total,2)}}</td>
				</tr>
			</tbody>
		</table>
		@endif
		<div id="notices">
			<p>C.A.E.: {{$invoice->cae}} </p>
			<p> Fecha de Vto. de C.A.E.: {{ date('d-m-Y',strtotime($invoice->fecha_vto_cae)) }} </p>
			<?php echo \myFunctions::codigoBarraCAE( env('CUIT'), 1, env('PV'), $invoice->cae, date('Ymd',strtotime($invoice->fecha_vto_cae)) );
			?>
		</div>
	</main>
</body>
</html>