@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Crear Cbte. de Compra</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" id="facturaForm" action="/guardarFacturaCompra" onsubmit="return confirm('¿El importe total coincide con el de su factura?')" method="POST">
						@if($customer!=null)
						<input type='hidden' value='{{$customer->id}}' class='form-control' name='customer_id' id='customer_id'/>
						<section>

							<p>	Razón Social: {{$customer->company_name}} </p>
							<p>	I.V.A.: {{$customer->fiscal_situation->fisc_situation}} </p>
							<p>	{{$customer->tax_type->type . ': ' . $customer->tax_id}} </p>

						</section>	
						@else
						<section>
							<div class="form-group">	
								<label class="col-md-1 control-label">Proveedor</label>
								<div class="col-md-3">
									<input type='text' class='form-control' id='buscarCliente'/>	
								</div>					
								<button type="button" id="newCustomer" class="btn btn-info" data-toggle="modal" data-target="#myModal">Crear Proveedor</button>
							</div>
							<input type='hidden' value='' class='form-control' name='customer_id' id='customer_id'/>
						</section>
						<div id="resulCliente">	
						</div>	
						@endif
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
											<option value="{{$cbte->id}}">{{$cbte->tipo}}</option>
											@endforeach
										</select>	
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Nro. Cbte.</label>
									<div class="col-md-4">
										<input type="text" class="form-control" name="nro_factura" value="{{ old('nro_factura') }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Fecha Cbte.</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="fecha_factura" value="{{ old('fecha_factura') }}" required>
									</div>
								</div>								
								<div class="form-group">
									<label class="col-md-4 control-label">Neto Gravado</label>
									<div class="col-md-4">
										<input type="text"  onBlur="total()" class="form-control" name="importe_neto" id="importe_neto" value="{{ old('importe_neto') }}" >
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Importe I.V.A. (21%)</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="importe_iva" id="importe_iva" value="{{ old('importe_iva') }}" >
									</div>
								</div>
									<div class="form-group">
									<label class="col-md-4 control-label">Importe I.V.A. (27%)</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="importe_iva_27" id="importe_iva_27" value="{{ old('importe_iva_27') }}" >
									</div>
								</div>
								<div class="form-group">
								<label class="col-md-4 control-label">Importe I.V.A. (10.5%)</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="importe_iva_10" id="importe_iva_10" value="{{ old('importe_iva_10') }}" >
									</div>
								</div>
								<div class="form-group">
								<label class="col-md-4 control-label">Perc. I.V.A.</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="perc_iva" id="perc_iva" value="{{ old('perc_iva') }}" >
									</div>
								</div>
								<div class="form-group">
								<label class="col-md-4 control-label">Importe no Gravado</label>
									<div class="col-md-4">
										<input type="text" class="form-control"  onBlur="total()" name="imp_no_grabado" id="imp_no_grabado" value="{{ old('imp_no_grabado') }}" >
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Importe Total</label>
									<div class="col-md-4">
										<input type="text" class="form-control" name="importe_total" id="importe_total" value="{{ old('importe_total') }}" readonly required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label"></label>
								<div class="col-md-6"><br/>
									<a href="{{ URL::to('home')}}" class="btn btn-danger">
										Cancelar operación
									</a>
									<button type="submit" class="btn btn-success">
										Guardar factura
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

	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Nuevo Proveedor</h4>
				</div>
				<div class="modal-body">
					<div id="validation-errors">
					</div> 
					<form class="form-horizontal" id="formCliente" role="form">
						<input type="hidden" name="company_type" value="2">
						<div class="list-group">
							<div class="list-group-item">
								<div class="form-group">
									<label class="col-md-4 control-label">Razón Social</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="company_name" value="{{ old('company_name') }}"  required>
									</div>
								</div>							
								<div class="form-group">
									<label class="col-md-4 control-label">Tipo de Doc.</label>
									<div class="col-md-6">
										<select class="form-control" name="tax_type" id="tax_type" value="{{ old('tax_type') }}" required>
											<option value="">Seleccione Doc.</option>
											@foreach($taxes as $tax)
											<option value="{{$tax->id}}">{{$tax->type}}</option>
											@endforeach
										</select>	
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Nro. de Doc.</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="tax_id" value="{{ old('tax_id') }}" required> 
									</div> 
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Sit. frente al I.V.A.</label>
									<div class="col-md-6">
										<select class="form-control" name="fiscal_sit" id="fiscal_sit" value="{{ old('fiscal_sit') }}" required>
											<option value="">Seleccione Sit.</option>
											@foreach($fiscal_situations as $fisc_sit)
											<option value="{{$fisc_sit->id}}">{{$fisc_sit->fisc_situation}}</option>
											@endforeach
										</select>	
									</div>
								</div>
								</div>
						</div>
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button type="button" class="btn btn-primary" id="submit_modal">Crear</button>
						</div>
					</div>
				</div>
			</div>





<script type="text/javascript">

function total(){
var imp_net = 0;
var imp_iva = 0;
var imp_iva_10_5 = 0;
var imp_iva_27 = 0;
var imp_perc_iva = 0;
var imp_no_grabado = 0;

	if($( "#importe_neto" ).val()!=""){
	imp_net = $( "#importe_neto" ).val();	
	}
	if($( "#importe_iva" ).val()!=""){
	imp_iva = $( "#importe_iva" ).val();	
	}
	if($( "#importe_iva_10" ).val()!=""){
	imp_iva_10_5 = $( "#importe_iva_10" ).val();	
	}
	if($( "#importe_iva_27" ).val()!=""){
	imp_iva_27 = $( "#importe_iva_27" ).val();	
	}
	if($( "#perc_iva" ).val()!=""){
	imp_perc_iva = $( "#perc_iva" ).val();	
	}
	if($( "#imp_no_grabado" ).val()!=""){
	imp_no_grabado = $( "#imp_no_grabado" ).val();	
	}

	var imp_tot = parseFloat(imp_net) + parseFloat(imp_iva) + parseFloat(imp_iva_10_5) + parseFloat(imp_iva_27) + parseFloat(imp_perc_iva) + parseFloat(imp_no_grabado) ;
	
	$( "#importe_total" ).val(parseFloat(imp_tot).toFixed(2));
	
};


$( "#buscarCliente" ).autocomplete({
    	source: "/search/autocompleteProvider",
    	select: function (event, ui) {
    		$( "#buscarCliente" ).val(ui.item.label);
    		$('#resulCliente').html("");
    		$('#resulCliente').html("<p>Razón Social: "+ ui.item.label +"</p> <p> I.V.A.: "+ ui.item.iva +" </p> <p> "+ui.item.tax_type+" : "+ ui.item.tax_id +" </p>");
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
				$('#resulCliente').html("<p>Razón Social: "+ msg.company_name +"</p> <p> I.V.A.: "+ msg.fs +" </p> <p> "+msg.tax_type +" : "+ msg.tax_id +" </p>"); 
				$('#customer_id').val(msg.id);
				$("#myModal").modal('hide');
			}

		}
	});
});
});

</script>


@endsection
