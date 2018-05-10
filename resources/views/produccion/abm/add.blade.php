@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Nueva Producción</div>
				<div class="panel-body">
					@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Ups!</strong> Existen los siguientes errores.<br><br>
						<ul>
							@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
					@endif
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					<form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{url('/produccion/store')}}">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Producto</legend>

								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" id="product_id" name="id_producto">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-4 control-label">Kilos</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="kg" value="{{ old('kg') }}" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-4 control-label">Producto</label>
											<div class="col-md-8">
												<input autocomplete="off" class="form-control typeahead" id="name">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-4 control-label">Fecha</label>
											<div class="col-md-6">
												<input id="input-date" type="date" class="form-control" name="created_at" value="{{ old('created_at') }}" required >
											</div>
										</div>
									</div>
								</div>


								<div class="form-group">	
									<div class="col-md-6 col-md-offset-5"><br/>
										<button type="submit" class="btn btn-primary">
											Crear Producción
										</button>
									</div>
								</div>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<script>
    $(document).ready(function () {
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
					$('#product_id').val(current.id);
                    // This means the exact match is found. Use toLowerCase() if you want case insensitive match.
                } else {
                    console.log('d2');
                    // This means it is only a partial match, you can either add a new item
                    // or take the active if you don't want new items
                }
            } else {
                console.log('d3');
                $('#product_id').val('');
                // Nothing is active so it is a new value (or maybe empty value)
            }
        });

    });
</script>
	@endsection