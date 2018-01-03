@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Stock
					</div>

				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
                                        <label for="kwd_search">Producto: </label> <input type="text" class="form-control" id="kwd_search" value=""/>
					<table class="search-table table table-hover" id="my-table">
						<thead>
							<tr>
                                                                <th>Referencia</th>
								<th>Cod. Producto</th>
								<th>Cantidad Actual</th>
								 @if(!Auth::guest()  && Auth::user()->roles_id!=5)
								<th>Nueva cantidad</th>
								@endif
							</tr>
						</thead>
						<tbody>
							@foreach($products as $product)
							<tr>
                                                                <th>{{ $product->producto->reference  }}</th>
								<th>{{ $product->producto->nombre->name  }}</th>
								<td id="{{$product->id_product}}">{{$product->quantity}}</td>
								@if(!Auth::guest()  && Auth::user()->roles_id!=5)
								<td><input class="form-control" type="text" id="add_{{$product->id_product}}" onblur="update_quantity(this.id,this.value)"> <td>
							    @endif
							
							</tr>
							@endforeach
						</tbody>
					</table>

								</div>
			</div>
		</div>
	</div>
</div>

<script src="{{ asset('/js/buscador.js') }}"></script>

<script>

function update_quantity(id, value){

	var arr = id.split('_');
        var quantity = value;
        
        var id = arr[1];
       
	$.get("{{ URL::to('updateStock') }}"+'/'+id+'/'+quantity, function(data){
        $('#'+id).html('');
	$('#'+id).html(data);
	}, 'json');   

}

setInterval(function() {
                  window.location.reload();
                }, 300000); 


$(document).ready(function(){
  $('table.search-table').tableSearch();
});

</script>





@endsection


