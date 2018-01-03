@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Editar Factura de Compra</div>
				<div class="panel-body">
<form class="form-horizontal" role="form" id="facturaForm" action="/aditFacturaCompra" method="POST">
						
						<input type='hidden' value='{{$invoice->customer_id}}' class='form-control' name='customer_id' id='customer_id'/>
						<input type='hidden' value='{{$invoice->id}}' class='form-control' name='fc' id='fc'/>
						<section>

							<p>	Razón Social: {{$invoice->nombre_proveedor}} </p>
							<p>	I.V.A.: {{$invoice->companies->fiscal_situation->fisc_situation}} </p>
							<p>	{{$invoice->tipo_doc . ': ' . $invoice->cuit}} </p>

						</section>	
						
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
						<div class="list-group">
							<div class="list-group-item">
								<div class="form-group">
									<label class="col-md-4 control-label">Tipo de Cbte.</label>
									<div class="col-md-4">
										<select class="form-control" name="cbte_tipo" id="cbte_tipo" value="{{ old('cbte_tipo') }}" required>
											<option value="">Seleccione Cbte.</option>
											@foreach($cbtes as $cbte)
											<option value="{{$cbte->id}}" @if($cbte->id==$invoice->tipo_cbte_prov_id) selected @endif>{{$cbte->tipo}}</option>
											@endforeach
										</select>	
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Nro. Cbte.</label>
									<div class="col-md-4">
										<input type="text" class="form-control" name="nro_factura" value="{{ $invoice->nro_factura }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Fecha Cbte.</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="fecha_factura" value="{{ $invoice->fecha_factura }}"  required>
									</div>
								</div>								
								<div class="form-group">
									<label class="col-md-4 control-label">Neto Gravado</label>
									<div class="col-md-4">
										<input type="text" class="form-control" name="importe_neto" id="importe_neto" value="{{ $invoice->importe_neto }}"  required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Importe I.V.A. (21%)</label>
									<div class="col-md-4">
										<input type="text" class="form-control" name="importe_iva" id="importe_iva" value="{{ $invoice->importe_iva }}" >
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Importe I.V.A. (27%)</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="importe_iva_27" id="importe_iva_27" value="{{ $invoice->importe_iva_27 }}" >
									</div>
								</div>
								<div class="form-group">
								<label class="col-md-4 control-label">Importe I.V.A. (10.5%)</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="importe_iva_10" id="importe_iva_10" value="{{ $invoice->importe_iva_10_5 }}" >
									</div>
								</div>
								<div class="form-group">
								<label class="col-md-4 control-label">Perc. I.V.A.</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="perc_iva" id="perc_iva" value="{{$invoice->perc_iva }}" >
									</div>
								</div>
								<div class="form-group">
								<label class="col-md-4 control-label">Importe no Gravado</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="imp_no_grabado" id="imp_no_grabado" value="{{ $invoice->importe_neto_no_gravado }}" >
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Importe Total</label>
									<div class="col-md-4">
										<input type="text" class="form-control" name="importe_total" id="importe_total" value="{{ $invoice->importe_total }}" readonly required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label"></label>
								<div class="col-md-6"><br/>
									<a href="{{ URL::to('compras')}}" class="btn btn-danger">
										Cancelar operación
									</a>
									<button type="submit" class="btn btn-success">
										Editar factura
									</button>
								</div>
							</div>	 
								</div>
								</div>
							
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">

$( "#importe_neto" ).blur(function() {

var imp_net = 0;
var imp_iva = 0;
	if($( "#importe_neto" ).val()!=""){
	imp_net = $( "#importe_neto" ).val();	
	}

	if($( "#importe_iva" ).val()!=""){
	imp_iva = $( "#importe_iva" ).val();	
	}
	
	var imp_tot = parseFloat(imp_net)+parseFloat(imp_iva);
	$( "#importe_total" ).val(parseFloat(imp_tot).toFixed(2));
	
});
$( "#importe_iva" ).blur(function() {
var imp_net = 0;
var imp_iva = 0;
	if($( "#importe_neto" ).val()!=""){
	imp_net = $( "#importe_neto" ).val();	
	}

	if($( "#importe_iva" ).val()!=""){
	imp_iva = $( "#importe_iva" ).val();	
	}
	var imp_tot = parseFloat(imp_net)+parseFloat(imp_iva);
	$( "#importe_total" ).val(parseFloat(imp_tot).toFixed(2));

});

$( "#buscarCliente" ).autocomplete({
    	source: "/search/autocompleteProvider",
    	select: function (event, ui) {
    		$( "#buscarCliente" ).val(ui.item.label);
    		$('#resulCliente').html("");
    		$('#resulCliente').html("<p>Razón Social: "+ ui.item.label +"</p> <p> "+ui.item.tax_type+" : "+ ui.item.tax_id +" </p>");
    		$('#customer_id').val(ui.item.id);   
    		return false;
    	}
    });
</script>

<script>
$(function() {
//twitter bootstrap script
$("#submit_modal").click(function(){
	$.ajax({
		type: "POST",
		url: "/createCustomer",
		data: $('#formCliente').serialize(),
		success: function(msg){
			if(msg.fail) {
				$("#validation-errors").html("<div class='alert alert-danger'><strong>Whoops!</strong> Por favor complete todos los datos del formulario.</div>");
			}else{
				$('#resulCliente').html("<p>Razón Social: "+ msg.company_name +"</p> <p> "+msg.tax_type +" : "+ msg.tax_id +" </p>"); 
				$('#customer_id').val(msg.id);
				$("#myModal").modal('hide');
			}

		}
	});
});
});

</script>


@endsection
