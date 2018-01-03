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

	body {

		width: 15cm;  
		margin: 0 auto; 
		color: #001028;
		background: #FFFFFF; 
		font-family: Arial, sans-serif; 
		font-size: 18px; 
		font-family: Arial;
	}

</style>
</head>
<body>
	<b style="font-size:30px;"> Cliente: {{$invoice->direccion_factura->company}} </b>
		
	<main style="margin-top: 180px;">
		<table e border="1">
			<thead>
				<tr>
					<th>Código</th>
					<th>Producto/Servicio</th>
					<th>Cantidad</th>
				</tr>
			</thead>
			<tbody>
				
				@foreach($lines as $line)
				<tr>
					<th>{{ $line->product_reference }}</th>
					<td>{{ $line->product_name }}</td>
					<td>{{ $line->product_quantity }}</td>
				</tr>
				
				@endforeach
				
			</tbody>
		</table>
     </main>

   <p> Nro. de Pedido: {{$invoice->id_order}} </p>
   <p> Fecha de Pedido: {{date('d-m-Y',strtotime($invoice->date_add))}} </p> 
   <p> Fecha y Hora de Entrega:</p> 
   <p> Pedido entragado por: .......................................................... </p> 
</body>
</html>