@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Productos
					<a href= "{{ url('products/create')}}"  style="margin-top:-7px; float:right;" class="btn btn-success" >Nuevo Producto</a></div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					
					<table class="table table-hover">
						<thead>
							<tr>

								<th>#</th>
								<th>Código</th>
								<th>Descripción</th>
								<th>Referencia</th>
								<th>Fecha</th>
								<th>Stock Fisico</th>
								<th>Stock Pedido</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							@foreach($products as $product)
                            <?php $i++; ?>
							<tr>
								<th>{{ $i }}</th>
								<td>{{ $product->codigo}}</td>
								<td>{{ $product->descripcion }}</td>
								<td>{{ $product->reference }}</td>
								<td>{{ $product->fecha_Hora }}</td>
								<td>{{ $product->stock_Fisico }}</td>
								<td>{{ $product->stock_Pedido }}</td>
								<td><a href= "{{url('/products/'.$product->id)}}/edit" class="btn btn-info" >Editar</a>&nbsp;&nbsp;<a href= "{{url('products/'.$product->id)}}/delete" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar</a> </td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<center>{!! $products->render() !!}</center>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
