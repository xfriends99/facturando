@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
                    <div class="row">
                        <div class="col-md-6">
                            Producción
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group" style="float: right;">
                                <a class="btn btn-success" href="{{url('/produccion/')}}/create">Crear producción</a>
                            </div>
                        </div>
                    </div>
				</div>
				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<label for="kwd_search">Referencia: </label> <select class="form-control" name="reference" id="reference"><option value="">Seleccione</option>@foreach($reference as $s) <option value="{{$s['id']}}" @if(isset($request['reference']) && $request['reference']==$s['id']) selected @endif>{{$s['name']}}</option>  @endforeach</select>
						</div>
						<div class="col-md-6 col-sm-12">
							<label for="kwd_search">Producto: </label> <input autocomplete="off" class="form-control typeahead" id="name" @if(isset($request['name']) && isset($products_lists[$request['name']])) value="{{$products_lists[$request['name']]}}" @endif>
						</div>
                        <div class="col-md-6 col-sm-12">
                            <label for="kwd_search">Fecha: </label> <input type="date" class="form-control" name="date" id="date" @if(isset($request['name']) && isset($products_lists[$request['name']])) value="{{$products_lists[$request['name']]}}" @endif>
                        </div>
					</div>
					<table class="table table-hover">
						<thead>
							<tr>

								<th>#</th>
                                <th>Producto</th>
                                <th>KG</th>
                                <th>Maquina</th>
                                <th>Fecha</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; ?>
							@foreach($produccion as $prod)
                            <?php $i++;
                            $prodKG = number_format($prod->kg,2,',','.');
                            ?>
							<tr>
								<th>{{ $i }}</th>
                                <th>{{$prod->codigo}}</td>
                                <td>{{$prodKG}} </td>
                                <td>{{$prod->users->name . ' ' . $prod->users->lastname}}</td>
                                <td>{{date('d-m-Y H:i',strtotime($prod->created_at))}}</td>
								<td>
                                    @if(!isset($control[$prod->id]))
									<a href= "{{url('/produccion/'.$prod->id)}}/edit" class="btn btn-info" >Editar</a>&nbsp;&nbsp;
									<a href= "{{url('produccion/'.$prod->id)}}/delete" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar</a>
                                    @endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
						<center> <?php echo $produccion->appends(Input::except('page'))->render(); ?> </center>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
        $('#date').change(function () {
            var urls = window.location.href.split('?');
            url = urls[0];
            if ($(this).val() != '') {
                url += '?date=' + $(this).val();
            }
            if (urls[1]) {
                var params = urls[1].split('&');
                for (var i = 0; i < params.length; i++) {
                    if (params[i].indexOf('date') == -1) {
                        if ($(this).val() != '') {
                            url += '&' + params[i];
                        } else {
                            url += '?' + params[i];
                        }
                    }
                }
            }
            window.location.href = url;
        });
        $('#reference').change(function () {
            var urls = window.location.href.split('?');
            url = urls[0];
            if ($(this).val() != '') {
                url += '?reference=' + $(this).val();
            }
            if (urls[1]) {
                var params = urls[1].split('&');
                for (var i = 0; i < params.length; i++) {
                    if (params[i].indexOf('reference') == -1) {
                        if ($(this).val() != '') {
                            url += '&' + params[i];
                        } else {
                            url += '?' + params[i];
                        }
                    }
                }
            }
            window.location.href = url;
        });

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
</script>
@endsection
