@extends('app')


@section('content')
<div class="container-fluid">
	<div class="row">
	
		<div class="col-md-12 col-md-offset-0">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle de Ventas
				</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="get" action="/ventas">
						<div class="list-group">
							<div class="list-group-item">
								<legend>Período seleccionado: @if(isset($hoy)) {{ date('d-m-Y',strtotime($hoy)) }} @else Desde: {{ date('d-m-Y',strtotime($desde)) }} Hasta: {{ date('d-m-Y',strtotime($hasta)) }} @endif</legend>
								<div class="form-group">
									<label class="col-md-4 control-label">Desde</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="desde" value="{{ old('desde') }}" required>
									</div>
								</div>							
								<div class="form-group">
									<label class="col-md-4 control-label">Hasta</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="hasta" value="{{ old('hasta') }}"  required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Filtro por tipo</label>
									<div class="col-md-4">
										<select class="form-control" name="type" >
											<option value="" @if($request['type']=='') selected @endif>Todo</option>
											<option @if($request['type']=='f') selected @endif value="f">Factura</option>
											<option @if($request['type']=='r') selected @endif value="r">Remito</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label">Estados de pedido</label>
									<div class="col-md-4">
										<select class="form-control selectpicker" multiple name="status" id="status">@foreach($statuses as $s) <option value="{{$s->id_order_state}}" @if(isset($request['status']) && preg_match('/'.$s->id_order_state.'/', $request['status'])) selected @endif>{{$s->name}}</option>  @endforeach</select>
									</div>
								</div>
								<div class="form-group">
							<div class="col-md-4 col-md-offset-4"><br/>
								<button type="submit" class="btn btn-primary">
									Consultar!
								</button>
							</div>
						</div>

				</form>
					@if (Session::has('message'))
					<div class="alert alert-info">{{ Session::get('message') }}</div>
					@endif
				
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Pedido</th>
								<th>Provincia</th>
								<th>Tipo Cbte.</th>
								<th>Nro. Cbte.</th>
								<th>Razón Social</th>
                                                                <th>Código de Producto</th>	
                                                                <th>Nombre del Producto</th>
                                                                <th>Cantidad Vendida</th>
                                                                <th>Precio de Venta</th>
                                                                <th>Importe de Venta</th>
                                                                <th>Precio de Costo</th>
                                                                <th>Importe de Costo</th>
                                                                <th>Ganancia</th>
<th>Vendedor</th> 
						        </tr>
						</thead>
						<tbody>
					<?php $venta = 0; $costo = 0; $gananciaa = 0;


                                        ?>		
							@foreach($invoices as $invoice)

@foreach($invoice->invoice_lines as $linea)

@if($invoice->status=='A' && ($invoice->cbte_tipo==1 || $invoice->cbte_tipo==99) && $linea->code!=null)
	@if($request['type']=='' || ($request['type']=='f' && $invoice->cbte_tipo==1) || ($request['type']=='r' && $invoice->cbte_tipo==99))
	@if(!isset($request['status']) || (isset($request['status']) && isset($order_states[$invoice->id_order]) && preg_match('/'.$order_states[$invoice->id_order].'/', $request['status'])))
		<tr>
                                    

								<th>{{ date('d-m-Y',strtotime($invoice->fecha_facturacion)) }}</td>
								<td>{{ $invoice->id_order  }} </td>
								<?php $order = \app\Pedido::where('id_order','=',$invoice->id_order)->first();	?>
								<td> {{ $order->direccion_factura->state->name }} </td>
								<td> @if($invoice->cbte_tipo==1 ) Factura @else Remito B @endif </td>
								<td>{{ $invoice->nro_cbte  }}</td>
								<td>{{ $invoice->company_name  }}</td>                                                  
								<td>@if(app\Product::find($linea->product_id)!=null){{app\Product::find($linea->product_id)->reference}} @else {{$linea->code}} @endif</td>
								<td>{{$linea->name}}</td>
								<td>{{$linea->quantity}}</td>
                                                                <td>{{ number_format($linea->price, 2, ',', '.') }}</td>
                                                                <?php $imp_venta=$linea->quantity*$linea->price; $venta += $imp_venta;?>
								<td>{{ number_format($imp_venta, 2, ',', '.') }}</td>
								<td> @if($linea->costo>1) {{ number_format($linea->costo, 2, ',', '.') }} @else @if(app\Product::find($linea->product_id)!=null) {{ number_format(app\Product::find($linea->product_id)->wholesale_price, 2, ',', '.')}} @else N/A @endif @endif </td>
								<?php $imp_costo=$linea->quantity*$linea->costo;  $costo += $imp_costo; ?>
                                                                <td> @if($imp_costo>1) {{ number_format($imp_costo, 2, ',', '.') }} @else <?php  if(app\Product::find($linea->product_id)!=null){ $imp_costo= app\Product::find($linea->product_id)->wholesale_price*$linea->quantity; }else{ $imp_costo=0; }?> {{ number_format($imp_costo, 2, ',', '.') }} @endif </td>
                                                                <?php $ganancia=$imp_venta-$imp_costo;  $gananciaa += $ganancia; ?>
								<td>{{ number_format($ganancia, 2, ',', '.') }}</td>
                                                                <td>{{$invoice->corredor->corredor->nombre}}</td>
								
							</tr>
		@endif
@endif
                                                   @endif
                                                        @endforeach
				
							@endforeach
							<tr>
                                                         <td></td>
							 <td></td>
							 <td></td>
							 <td></td>
							 <td></td>
							 <td></td>
							 <td></td>
							 <td></td>
							 <td></td>
							 <td>{{  number_format($venta, 2, ',', '.') }}</td>
							 <td></td>
							 <td>{{  number_format($costo, 2, ',', '.') }}</td>  
							 <td>{{  number_format($gananciaa, 2, ',', '.') }}</td> 
							 <td></td>     
                                                        </tr>


						</tbody>
					</table>
					
					
							
				</div>
			</div>
		</div>
	</div>
</div>
@endsection