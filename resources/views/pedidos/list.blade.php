@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-md-offset-0">
			<div class="panel panel-default">
				<div class="panel-heading">Administrar Pedidos
				</div>

				<div class="panel-body">
					
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
					<div id="emailResul"></div>
					<table class="table table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Fecha de Pedido</th>
								<th>Pedido</th>
								<th>Cliente</th>
								<th>Importe Total</th>
 								<th>Estado</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>
							@foreach($pedidos as $pedido)
   <?php $cbtes = app\InvoiceHead::where('id_order','=',$pedido->id_order)->get();
$remito_a = 0;
$remito_b = 0;
$factura = 0;   
foreach($cbtes as $cbte){

if($cbte->cbte_tipo==99){
if($cbte->tipo_venta==0){
$remito_b = 1;
}
if($cbte->tipo_venta==1){
$remito_a = 1;
}
}
if($cbte->cbte_tipo==1 && $cbte->status=='A'){
$factura = 1;
}
if($cbte->cbte_tipo==6 && $cbte->status=='A'){
$factura = 1;
}

}


 ?>
							<tr>
								<td><div style="border-radius: 50%; behavior: url(PIE.htc); width: 15px; height: 15px; background: @if($pedido->current_state==16) #000000; border: 3px solid #000; @elseif($pedido->current_state==12) #FFFF00; border: 3px solid #FFFF00; @else #108510; border: 1px solid #108510; @endif"></div></td>
								<th>{{ date('d-m-Y',strtotime($pedido->date_add))  }}</th>
								<td>{{ $pedido->id_order}}</td>
                                                                @if($pedido->customer!=null)
								<td>{{ $pedido->customer->firstname . ' ' . $pedido->customer->lastname}}</td>
                                                                @endif
								<td>{{'$ '.number_format($pedido->total_paid,2) }}</td>
								<td>@if($pedido->current_state==16 && $remito_b==0) Sin Facturar @elseif($pedido->current_state==5 && $factura==0) Sin Facturar @elseif($pedido->current_state==12) En Proceso @else Facturado @endif</td>
								<td>@if($pedido->current_state==12) <a href= "expedicion/{{$pedido->id_order}}" target="_blank" class="btn btn-success" >Generar Expedición @else @if($pedido->current_state==16 && $remito_b==0) <a href= "generarRemito/{{$pedido->id_order}}" class="btn" style="background-color: black; color: white;" >Generar Remito</a> &nbsp;&nbsp; @endif @if($pedido->current_state==16 && $remito_b!=0) &nbsp;&nbsp; <a href= "reGenerarRemito/{{$pedido->id_order}}" class="btn" style="background-color: black; color: white;" >Re-Generar Remito</a> &nbsp;&nbsp; @endif @if($remito_a==0) <a href= "generarRemito/{{$pedido->id_order}}" class="btn btn-info" >Generar Remito</a> &nbsp;&nbsp; @endif @if($pedido->current_state==16) <a href= "generarPresupuesto/{{$pedido->id_order}}" target="_blank" class="btn" style="background-color: grey; color: black;">Generar Presupuesto</a> @endif @if($pedido->current_state==5 && $factura==0) <a href= "generarFactura/{{$pedido->id_order}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Generar Factura</a> @endif <a href= "expedicion/{{$pedido->id_order}}" target="_blank" class="btn btn-success" >Generar Expedición @endif</a> @if($pedido->orden_compra==null) <button data-toggle="modal" data-target="#myModal" id="modal_{{$pedido->id_order}}" onClick="changeID(this.id)" class="btn btn-info" >OC</button> @endif </td>
							</tr>
							@endforeach
						</tbody>
					</table>
				<center> <?php echo $pedidos->render(); ?> </center>
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
						<h4 class="modal-title" id="myModalLabel">Cargar Orden de Compra</h4>
					</div>
					<div class="modal-body">
						<div id="validation-errors">
						</div> 
						<form class="form-horizontal" id="formCliente" role="form">
							<input type="hidden"  name="oder_modal_id" id="oder_modal_id" required>

							<div class="list-group">
								<div class="list-group-item">
									<div class="form-group">
									<label class="col-md-4 control-label">Nro. de Orden de Compra</label>
										<div class="col-md-6">
											<input type="text" class="form-control" name="orden_compra" required>
										</div>
									</div>
								</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								<button type="button" class="btn btn-primary" id="submit_modal">Cargar</button>
							</div>

							</div>
						</form>
					</div>
				</div>
			</div>
</div>
<script>
       function changeID(id){
      
       var arr = id.split('_');
       $('#oder_modal_id').val(arr[1]);

       };
</script>
<script>
$(function() {
	//twitter bootstrap script
	$("#submit_modal").click(function(){
		$.ajax({
			type: "POST",
			url: "/changeOC",
			data: $('#formCliente').serialize(),
                        success: function(msg){ $("#myModal").modal('hide'); }
			
		});
	});
	});
</script>
@endsection