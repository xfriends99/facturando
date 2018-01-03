@extends('app')
@if(Auth::user()->roles_id!=4)

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Home</div>

				<div class="panel-body">
					Facturando - Sistema de Factura Electrónica en Linea!
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@else

@section('content')
<style>
.ui-widget {
    font-size: 5.1em;
}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-md-offset-0">
			<div class="panel panel-default">
				<div class="panel-heading">Cargar Producción</div>
				<div class="panel-body">
					@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Whoops!</strong> Existen algunos errores en los campos del formulario.<br><br>
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
					<form class="form-horizontal" role="form" method="POST" action="/addProduction">

						<div class="list-group">
							<div class="list-group-item">
								<legend>Producción</legend>

								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								
                                                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                                                <input type="hidden" name="prod_id" id="prod_id" value="@if(isset($prod_id)){{ $prod_id }}@endif">

								<div class="form-group">
									<label class="col-md-3 control-label" style="font-weight: bold;font-size:50px;width: 220px;padding-left: 0px;" >Código</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="producto_id" id="producto_id" value="@if(isset($producto)){{ $producto}}@endif" style="height: 100px; font-size: 100px; font-weight: bold; text-transform: uppercase; width: 450px;" required @if(!isset($producto)) autofocus @endif>
									</div>
								</div>

<div id="desc" style="height: auto; font-size: 50px; font-weight: bold; text-transform: uppercase;"> @if(isset($prod_id)) {{ $prod->descripcion }} <br> <p style='font-weight: bold;'> Kg:  {{ $prod->pesoRef }}  Diam: {{ $prod->diametroRef }}  Mts: {{ $prod->metrosRef }} </p>  @endif </div>

                                                                <div class="form-group">
									<label class="col-md-3 control-label" style="font-weight: bold;font-size:50px;">N° Manga</label>
									<div class="col-md-3">
										<input type="text" style="height: 100px; font-size: 100px; font-weight: bold; text-transform: uppercase; width: 200px;" class="form-control" name="contador"  id="contador" value="@if(isset($contador)){{ $contador }} @else 1 @endif" required readonly>
									</div>

<label class="col-md-1 control-label" style="font-weight: bold;font-size:50px;">KG</label>
									<div class="col-md-5">
										<input type="number" style="height: 100px; font-size: 100px; font-weight: bold; text-transform: uppercase; width: 400px;" class="form-control" name="cantidad" id="cantidad" class="form-control" value="{{ old('cantidad') }}" max="99" step="0.01" required @if(isset($producto)) autofocus @endif>
									</div>
								</div>

                                                                <div class="form-group">
									
								</div>

								

							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-5"><br/>
								<button type="submit" onClick="return confirm('Controle antes de confirmar.')" class="btn btn-primary">
									Cargar Producción
								</button>
							</div>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	 $( "#producto_id" ).autocomplete({
	    	source: "/search/autocompleteProducts",
	    	select: function (event, ui) {
	    		$('#producto_id').val(ui.item.label);
	    		$('#contador').val('1');
                        $('#cantidad').val('');
	    		$('#prod_id').val(ui.item.id); 
                        $('#desc').html("");
                        var texto =  ui.item.descripcion + "<br>" + "<p style='font-weight: bold;'> Kg: " + ui.item.peso  + " Diam: " + ui.item.diametro + " Mts: " + ui.item.metros + "</p>" ;

                        $('#desc').html(texto);  
                        $("#cantidad").focus();
	    		return false;
	    	}
	    });

$( "#producto_id" ).on( "keydown", function(event) {
      if(event.which == 13) {
              $('#contador').val('1');
              $('#desc').html("");
              $.get( "getProdAjax/"+$( "#producto_id" ).val(), function( data ) {
              var texto =  data[0] + "<br>" + "<p style='font-weight: bold;'> Kg: " + data[1]  + " Diam: " + data[2] + " Mts: " + data[3] + "</p>" ;
              $( "#desc" ).html( texto );      
              $('#prod_id').val(data[4]);
              });
              $("#cantidad").focus();
	      return false;
              }
    });

$( "#producto_id" ).focus(function() {
  $('#producto_id').val("");
  $('#desc').html("");
});
</script>
@endsection

@endif
