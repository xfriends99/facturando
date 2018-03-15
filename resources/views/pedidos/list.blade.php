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
					<span>Id de pedido para Stock: {{$last_order}}</span>
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<label for="kwd_search">Pedidos: </label> <select class="form-control" name="status" id="status"><option value="">Seleccione</option>@foreach($statuses as $s) <option value="{{$s->id_order_state}}" @if(isset($request['status']) && $request['status']==$s->id_order_state) selected @endif>{{$s->name}}</option>  @endforeach</select>
						</div>
						<div class="col-md-6 col-sm-12">
							<label for="kwd_search">Cliente: </label> <input autocomplete="off" class="form-control typeahead" id="cliente" @if(isset($request['cliente'])) value="{{$customers[$request['cliente']]}}" @endif>
						</div>
					</div>
					<table class="table table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Fecha de Pedido</th>
								<th>Pedido</th>
								<th>Cliente</th>
								<th>Importe Total</th>
								<th>Estatus</th>
								<th></th>
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
								<td style="width:20px;"><div style="border-radius: 50%; behavior: url(PIE.htc); width: 15px; height: 15px; background: @if($factura==1) #108510; border: 1px solid #108510; @elseif($remito_a!=0 || $remito_b!=0) #000000; border: 3px solid #000; @else #FFFF00; border: 3px solid #FFFF00; @endif"></div></td>
								<th style="width:20px;">{{ date('d-m-Y',strtotime($pedido->date_add))  }}</th>
								<td style="width:20px;">{{ $pedido->id_order}}</td>

								<td style="width:150px;">
									@if($pedido->direccion_factura)
										{{$pedido->direccion_factura->company}}
									@elseif($pedido->customer!=null)
										{{ $pedido->customer->firstname . ' ' . $pedido->customer->lastname}}
									@endif</td>
								<td style="width:30px;">{{'$ '.number_format($pedido->total_paid,2) }}</td>
								<td style="width:50px;">
									<span class="label label-default" style="background: {{$pedido->color}} !important;">
										{{$pedido->name_state}}
									</span>
								</td>
								<td style="width:10px;" >@if($factura==1) F @endif </td>
								<td style="width:500px;">
									@if($pedido->current_state==3 || $pedido->current_state==7 || $pedido->current_state==8 || $pedido->current_state==9 || $pedido->current_state==12 || $pedido->current_state==13)
										<a href= "/expedicion/{{$pedido->id_order}}" target="_blank" class="btn btn-success" >Generar Expedición</a>
										@if($factura==0 && $remito_a==0 && $remito_b==0)
											&nbsp;&nbsp;<a href= "/generarFactura/{{$pedido->id_order}}" onClick="return confirm('¿Esta seguro?');" class="btn btn-danger" >Generar Factura</a>
											&nbsp;&nbsp;<a href= "/generarRemito/{{$pedido->id_order}}" class="btn" style="background-color: black; color: white;" >Generar Remito</a>
										@endif
										@if($factura==1)
											@if($remito_a==0)
											&nbsp;&nbsp;<a href= "/generarRemito/{{$pedido->id_order}}" class="btn btn-info" >Generar Remito</a>
											@endif
											&nbsp;&nbsp;<button data-toggle="modal" data-target="#myModal" id="modal_{{$pedido->id_order}}" onClick="changeID(this.id)" class="btn btn-info" >OC</button>
										@endif
										@if(($remito_a!=0 || $remito_b!=0) && $factura==0)
											&nbsp;&nbsp;<a href= "/generarPresupuesto/{{$pedido->id_order}}" target="_blank" class="btn" style="background-color: grey; color: black;">Generar Presupuesto</a>
											@if($remito_a==0)
												&nbsp;&nbsp;<a href= "/generarRemito/{{$pedido->id_order}}" class="btn btn-info" >Generar Remito</a>
											@endif
											&nbsp;&nbsp;<a href= "/reGenerarRemito/{{$pedido->id_order}}" class="btn" style="background-color: black; color: white;" >Re-Generar Remito</a>
										@endif

									@endif
									@if($pedido->current_state==5)
										@if($factura==1)
                                                <?php $fact = app\InvoiceHead::where('id_order','=',$pedido->id_order)
                                                	->where('status','!=','D')
                                                    ->where(function($q){
                                                        $q->where('cbte_tipo','=','1');
														$q->orWhere('cbte_tipo','=','6');
                                                    })
                                                    ->orderBy('nro_cbte','DESC')->first();?>
											<a href= "/verFactura/{{$fact->id}}" class="btn btn-danger" >Ver Factura</a>
										@endif
										@if(($remito_a!=0 || $remito_b!=0) && $factura==0)
											<?php $rem = app\InvoiceHead::where('id_order','=',$pedido->id_order)
                                                    ->where('cbte_tipo','=','99')
                                                    ->orderBy('fecha_facturacion','DESC')->first();?>
											<a href= "/verRemito/{{$rem->id}}" class="btn" style="background-color: black; color: white;" >Ver Remito</a>
										@endif
									@endif

									@if($pedido->current_state==6)
										<a href= "/expedicion/{{$pedido->id_order}}" target="_blank" class="btn btn-success" >Ver</a>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				<center> <?php echo $pedidos->appends(Input::except('page'))->render(); ?> </center>
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
	$(document).ready(function(){
	   $('#status').change(function(){
	       var urls = window.location.href.split('?');
	       url = urls[0];
	       if($(this).val()!=''){
	           url += '?status='+$(this).val();
		   }
           if(urls[1]){
               var params = urls[1].split('&');
               for(var i =0; i<params.length;i++){
                   if(params[i].indexOf('status')==-1){
                       if($(this).val()!=''){
                           url += '&'+params[i];
					   } else {
                           url += '?'+params[i];
					   }
				   }
			   }
           }
           window.location.href = url;
	   });

        var $input = $(".typeahead");
        $input.typeahead({
            source: [
                @foreach($customers as $key => $v)
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
                        url += '?cliente='+current.id;
                    }
                    if(urls[1]){
                        var params = urls[1].split('&');
                        for(var i =0; i<params.length;i++){
                            if(params[i].indexOf('cliente')==-1){
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
                    var urls = window.location.href.split('?');
                    url = urls[0];
                    if(urls[1]){
                        var params = urls[1].split('&');
                        for(var i =0; i<params.length;i++){
                            if(params[i].indexOf('cliente')==-1){
								url += '?'+params[i];
                            }
                        }
                    }
                    window.location.href = url;
                    // This means it is only a partial match, you can either add a new item
                    // or take the active if you don't want new items
                }
            } else {
                // Nothing is active so it is a new value (or maybe empty value)
            }
        });
        /*$('#cliente').change(function(){
            var urls = window.location.href.split('?');
            url = urls[0];
            if($(this).val()!=''){
                url += '?cliente='+$(this).val();
            }
            if(urls[1]){
                var params = urls[1].split('&');
                for(var i =0; i<params.length;i++){
                    if(params[i].indexOf('cliente')==-1){
                        if($(this).val()!=''){
                            url += '&'+params[i];
                        } else {
                            url += '?'+params[i];
                        }
                    }
                }
            }
            window.location.href = url;
        });*/
	});
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