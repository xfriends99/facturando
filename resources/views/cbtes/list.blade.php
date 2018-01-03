@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar {{$tipo_cbtes}}
					@if($url!='remito')
					<a href= "/{{$url}}"  style="margin-top:-7px; float:right;" class="btn btn-success" >Nueva {{$tipo_cbte}}</a>
					@endif
				</div>

				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					<div id="emailResul"></div>
					<table class="table table-hover">
						<thead>
							<tr>

								<th>Fecha de Cbte.</th>
								@if($url==='remito')
								<th>Nro. de Cbte</th>
								@endif
								<th>Cliente</th>
								<th>Importe Total</th>
								@if($url!='remito')
								<th>Estado</th>
								@endif
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>
							@foreach($invoices as $invoice)
							<tr>
								<th>{{ $invoice->fecha_facturacion  }}</th>
								@if($url==='remito')
								<td>{{ $invoice->nro_cbte }}</td>
								@endif
								<td>{{ $invoice->company_name }}</td>
								<td>{{ '$ '.number_format($invoice->imp_total,2) }}</td>
								@if($url!='remito')
								<td>{{ \myFunctions::FacturaStatus($invoice->status) }}</td>
								@endif
								<td>@if($invoice->status=='G')
									<a href= "/editarCbte/{{$invoice->id}}" class="btn btn-info" >Editar</a>&nbsp;&nbsp;<a href= "/emitirCbte/{{$invoice->id}}" class="btn btn-success" >Emitir Cbte.</a>&nbsp;&nbsp;<a href= "/eliminarCbte/{{$invoice->id}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Eliminar</a>
									@endif
									@if($invoice->status=='A')
									@if($url!='remito')
									<a href= "/verCbte/{{$invoice->id}}" class="btn btn-info" >Ver Cbte.</a>								
									<a href= "/descargarCbte/{{$invoice->id}}" class="btn btn-warning" >Descargar Cbte.</a>
									<button type="button" id="email_{{$invoice->companies_id}}_{{$invoice->id}}" class="btn btn-success" data-toggle="modal" onClick="get_invoice_id(this.id)" data-target="#myModal">Enviar por E-mail</button>
									@else
									<a href= "/descargarRemito/{{$invoice->id}}" class="btn btn-warning" >Descargar Remito</a>
									@endif									
									@endif

								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				<center> <?php echo $invoices->render(); ?> </center>
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
					<h4 class="modal-title" id="myModalLabel">Enviar Cbte. por E-mail</h4>
				</div>
				<div class="modal-body">
					<div id="validation-errors">
					</div> 
					<form class="form-horizontal" id="formEmail" role="form">
						<input type="hidden" id="invoice_id" name="invoice_id" value="">
						<input type="hidden" id="company_id" name="company_id" value="">
						<div class="list-group">
							<div class="list-group-item">
								<div class="form-group">
									<label class="col-md-4 control-label">Para</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="to" id="to" value="{{ old('to') }}"  required>
									</div>
								</div>							
								
								<div class="form-group">
									<label class="col-md-4 control-label">Mensaje</label>
									<div class="col-md-6">
										<textarea name="msj" style="margin: 0px; width: 252px; height: 92px;">{{ old('msj') }}</textarea>
									</div>
								</div>	
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button type="button" class="btn btn-primary" id="submit_modal">Enviar</button>
						</div>
					</div>
				</div>
			</div>
<script>
function get_invoice_id(id){

	var arr = id.split('_');
	$('#company_id').val(arr[1]);
	$('#invoice_id').val(arr[2]);
	$.post("{{ URL::to('email') }}"+'/'+arr[1], function(data){
	$('#to').val(data.mail);
	}, 'json');   
}
</script>

<script>
$(function() {
//twitter bootstrap script
$("#submit_modal").click(function(){
	$.ajax({
		type: "POST",
		url: "/sendInvoice",
		data: $('#formEmail').serialize(),
		success: function(msg){
			if(msg.fail) {
				$("#emailResul").html("<div class='alert alert-danger'><strong>Whoops!</strong> Ocurrió un error al enviar el correo. Por favor, vuelva a intentarlo.</div>");
				$("#myModal").modal('hide');
			}else{
				$('#emailResul').html("<div class='alert alert-success>Correo enviado correctamente.</div>");
				$("#myModal").modal('hide');
			}

		}
	});
});
});
</script>
@endsection


