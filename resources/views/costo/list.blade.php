@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Costo
					</div>

				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif

					<table class="table table-hover">
						<thead>
							<tr>

								<th>Cod. Producto</th>
								<th>Costo actual</th>
								<th>Nuevo Costo</th>
							</tr>
						</thead>
						<tbody>
							@foreach($products as $product)
							<tr>
								<th>{{ $product->reference}}</th>
								<td><input class="form-control" type="text" id="{{$product->id_product}}" value="{{number_format($product->wholesale_price,2)}}" readonly> </td>
								<td><input class="form-control" type="text" id="add_{{$product->id_product}}" onblur="update_value(this.id,this.value)"> <td>
							
							</tr>
							@endforeach
						</tbody>
					</table>
								</div>
			</div>
		</div>
	</div>
</div>

<script>
function update_value(id, value){

	var arr = id.split('_');
        var quantity = value;
        
        var id = arr[1];
       
	$.get("{{ URL::to('updateCosto') }}"+'/'+id+'/'+quantity, function(data){
	$('#'+id).val(data);
	}, 'json');   

}

setInterval(function() {
                  window.location.reload();
                }, 300000); 

</script>


@endsection


