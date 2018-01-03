@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Remitos emitidos					
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
								<th>Fecha de Cbte.</th>
								<th>Nro. de Cbte</th>
								<th>Cliente</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>
							@foreach($invoices as $invoice)
							<tr>
<td><div style="border-radius: 50%; behavior: url(PIE.htc); width: 15px; height: 15px; background: @if($invoice->tipo_venta==0) #000000; border: 3px solid #000; @else #108510; border: 1px solid #108510; @endif"></div></td>
						<th>{{ $invoice->fecha_facturacion  }}</th>
						<td>{{ $invoice->nro_cbte }}</td>
						<td>{{ $invoice->company_name }}</td>
								<td>	
                                        <a href= "/verRemito/{{$invoice->id}}" class="btn btn-info" >Ver Remito</a>&nbsp;&nbsp;						
					<a href= "/descargarRemito/{{$invoice->id}}" class="btn btn-warning" >Descargar Remito</a>
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

<script>
var remitos = 0;
var customer = 0;

function getRemitos(id){

 var arr = id.split('_');
 
 if ( $('#'+id).is(':checked') ) {
	if(customer==0){
		customer=arr[2];
		remitos++;
	}else{
		if(customer!=arr[2]){
			$('input:checkbox').removeAttr('checked');
			remitos=0;
			customer=0;
			$('#generarFactura').attr('disabled','disabled');
		}else{
			remitos++;
			if(remitos>=2){
				$('#generarFactura').removeAttr('disabled'); 		
			}
		}
	}
}else{

		remitos--;
			if(remitos<2){
			$('#generarFactura').attr('disabled','disabled'); 		
			}
			if(remitos==0){
				customer=0;
			}
}

}

function generateInvoice(){
	var nro="";
	$("input:checked").each(function()
{	

    var arr = $(this).val().split('_');
    nro = nro.concat(arr[1]+'-');
});
	nro = nro.substr(0, nro.length - 1) + '';
	window.location.href='/generarFacturaRemitos/'+nro;
}
</script>

@endsection


