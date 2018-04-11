@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Costo
					</div>

				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
						<div class="row">
							<div class="col-md-12">
								<label for="kwd_search">Producto: </label> <input autocomplete="off" class="form-control typeahead" id="name" @if(isset($request['name']) && isset($products_lists[$request['name']])) value="{{$products_lists[$request['name']]}}" @endif>
							</div>
						</div>
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Cod. Producto</th>
								<th>Producto</th>
								<th>Costo actual</th>
								<th>Nuevo Costo</th>
							</tr>
						</thead>
						<tbody>
							@foreach($products as $product)
							<tr>
								<th>{{ $product->reference}}</th>
								<th>{{ $products_lists[$product->id_product]}}</th>
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
	$(document).ready(function(){
        var $input = $(".typeahead");
        $input.typeahead({
            source: [
					@foreach($products_lists as $key => $v)
                {id: "{{$key}}", name: "{{$v}}"},
				@endforeach
            ],
            autoSelect: true
        });
        $input.change(function() {
            var current = $input.typeahead("getActive");
            if (current) {
                // Some item from your model is active!
                if (current.name == $input.val()) {
                    var urls = window.location.href.split('?');
                    url = urls[0];
                    if(current.id!=''){
                        url += '?name='+current.id;
                    }
                    if(urls[1]){
                        var params = urls[1].split('&');
                        for(var i =0; i<params.length;i++){
                            if(params[i].indexOf('name')==-1){
                                if(current.id!=''){
                                    url += '&'+params[i];
                                } else {
                                    url += '?'+params[i];
                                }
                            }
                        }
                    }
                    window.location.href = url;
                    // This means the exact match is found. Use toLowerCase() if you want case insensitive match.
                } else {

                    // This means it is only a partial match, you can either add a new item
                    // or take the active if you don't want new items
                }
            } else {
                var urls = window.location.href.split('?');
                url = urls[0];
                if(urls[1]){
                    var params = urls[1].split('&');
                    for(var i =0; i<params.length;i++){
                        if(params[i].indexOf('name')==-1){
                            url += '?'+params[i];
                        }
                    }
                }
                window.location.href = url;
                // Nothing is active so it is a new value (or maybe empty value)
            }
        });
	});

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


