@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Crear {{$tipo_cbte}}</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" id="facturaForm" action="/guardarCbte" method="POST">					
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
						<section>
							<div class="form-group">
							 <label class="col-md-1 control-label">Razón Social</label>
								<div class="col-md-3">
	  <input type='input' class='form-control' id='raz_social' name='raz_social' required/>
	  </div>					
							</div>
							<div class="form-group">
							 <label class="col-md-1 control-label">C.U.I.T.</label>
								<div class="col-md-3">
	  <input type='input' class='form-control' id='cuit' name='cuit' oninput='checkString(this.id)' required/>
	  </div>					
							</div>
	  <div class="form-group">
	    <label class="col-md-1 control-label">Dirección</label>
								<div class="col-md-3">
	  <input type='input' class='form-control' id='direccion' name='direccion' required/>
	  </div>					
							</div>
							<div class="form-group">
							   <label class="col-md-1 control-label">Fecha</label>
								<div class="col-md-3">
	  <input type='date' class='form-control' id='fecha' name='fecha' value='<?php echo date('Y-m-d'); ?>' required/>	
								</div>					
							</div>
							</section>
						<section id="fechas">
						</section>				
					</section>
					<hr>
					<p><button type="button" id="addFile" class="btn btn-success">Agregar Fila</button>&nbsp;&nbsp;&nbsp;
						<button type="button" id="removeFile" class="btn btn-danger">Eliminar Fila</button></p>
						<table id="tblData" class="table table-bordered">
							<thead>
								<tr>
									<th>Código</th>
									<th>Producto/Servicio</th>
									<th>Cantidad</th>
									<th>Precio Unit.</th>
									<th>I.V.A.</th>
									<th>Imp. I.V.A.</th>
									<th>Subtotal</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Subtotal</th>
									<th>I.V.A. 21%</th>
									<th>Total</th>

								</thead>
								<tbody>
									<tr>
<input type='hidden' class='form-control' name='cant_lineas' id='cant_lineas'/>
<td><input type='text' class='form-control' name='subtotal' id='subtotal'readonly required/></td>							<td><input type='text' class='form-control' name='iva_21' id='iva_21' readonly required/></td>
<td><input type='text' class='form-control' name='total' id='total' readonly required/></td>
										</tr>
								</tbody>
							</table>
							<input type='hidden' value='{{$nro_tipo_cbte}}' class='form-control' name='tipo_cbte' id='tipo_cbte'/>
							<div class="form-group">
								<div class="col-md-6 col-md-offset-3"><br/>
									<a href="{{ URL::to('home')}}" class="btn btn-danger">
										Cancelar operación
									</a>
									
									<button type="submit" name="generar" value="generar" class="btn btn-warning">
										Generar Comprobante
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

			var i = 0;  

			$("#addFile").click(function () { 

				$('#tblData tbody').append("<tr><td><input type='text' class='form-control' name='code_"+i+"' id='code_"+i+"'/></td><td><input type='text' class='form-control' name='product_"+i+"' id='product_"+i+"'/></td><td><input type='text' class='form-control' name='cantidad_"+i+"' id='cantidad_"+i+"' onchange='updateCantidades(this.id)' required/></td><td><input type='text' class='form-control' name='punitario_"+i+"' id='punitario_"+i+"' onchange='updateTotales(this.id)' required/><td><input type='text' class='form-control' name='tipo_iva_"+i+"' id='tipo_iva_"+i+"' value='21%' readonly required/></td><td><input type='text' class='form-control' name='imp_iva_"+i+"' id='imp_iva_"+i+"' onchange='updateIVA(this.id)' required/></td><td><input type='text' class='form-control' name='subtotal_"+i+"' id='subtotal_"+i+"' readonly required/></td></tr>"); 
				i++;
				$("#cant_lineas").val(i);

			});

			$("#removeFile").click(function(event) { 
				$("#tblData tr:last").has('td').remove();
				i--;
				if(i==0){
				$("#cant_lineas").val('');				
				}else{
				$("#cant_lineas").val(i);				
				}
				subt = 0;
				var iva21 = 0;
				for(j=0;i>j;j++){
					subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
					var tipo_iva = $('#tipo_iva_'+j).val();
					var arrr = tipo_iva.split('%');
			    	if(arrr[0]==21){
	    				iva21 = parseFloat(iva21 + parseFloat($('#imp_iva_'+j).val()));
	    				}					
				}
				
	    		if(iva21!=0){
	    				$('#iva_21').val(iva21.toFixed(2));
	    			}
	    			
		       var total = parseFloat(subt) + parseFloat(iva21);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2)); 
			});      

	    function updateCantidades(id){

	    	var cant = $('#'+id).val();
	    	var arr = id.split('_');
	    	var subtotal = '#subtotal_'+arr[1];
	    	var punitario = parseFloat($('#punitario_'+arr[1]).val());
	    	var tipo_iva = '#tipo_iva_'+arr[1];
			var impbon = '#impbon_'+arr[1];
			var imp_iva = '#imp_iva_'+arr[1];
	    	var subt = 0;

	    	if($(impbon).val()!=''){
	    		impbon = $(impbon).val();
	    	}else{
	    		impbon = 0;
	    	}	

	    	if(cant<1){
	    		alert('Ingrese un valor mayor/igual a 1');
	    		$('#'+id).val(1);   		
	    	}else{
	    		var subt = cant * (punitario-impbon);
	    		$(subtotal).val(subt.toFixed(2));
	    			var iva = $(tipo_iva).val();
	    			var arrr = iva.split('%');
	    			iva = parseFloat(arrr[0]) + 100;
	    			subIVA = (iva * subt)/100;
	    			subIVA = subIVA - subt;
	    			$(imp_iva).val(parseFloat(subIVA).toFixed(2));
	    			var iva = $(tipo_iva).val();
	    			var sumIVA = 0;
	    			for(j=0;i>j;j++){
	    			var tipo_iva = '#tipo_iva_'+j;
	    			var imp_iva = '#imp_iva_'+j;
	    			var ti = $(tipo_iva).val();	
	    			if(iva == $(tipo_iva).val() ){
	    				sumIVA = sumIVA + parseFloat($(imp_iva).val()); 
	    			}

	    		}

	    			if(arrr[0]==21){
	    				$('#iva_21').val(sumIVA.toFixed(2));	
	    			}

	    		subt = 0;
	    		for(j=0;i>j;j++){
	    			subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
	    		}
	    		
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			
	    		var total = parseFloat(subt) + parseFloat(iva21);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2));
		}
	    }
	    
	    function updateTotales(id){

	    	var punitario = $('#'+id).val();
	    	var arr = id.split('_');
	    	var subtotal = '#subtotal_'+arr[1];
	    	var cant = $('#cantidad_'+arr[1]).val();
	    	var tipo_iva = '#tipo_iva_'+arr[1];
			var imp_iva = '#imp_iva_'+arr[1];
			var impbon = '#impbon_'+arr[1];
			var porbon = '#porbon_'+arr[1];

			if($(impbon).val()!=''){
	    		$(impbon).val('');
	    		$(porbon).val('');  		
	    		impbon = 0;
	    	}else{
	    		impbon = 0;
	    	}	

	    	if(punitario<1){
	    		alert('Ingrese un valor mayor/igual a 1');
	    		$('#'+id).val(1);   		
	    	}else{
	    		var subt = cant * (punitario-impbon);
	    		$(subtotal).val(subt.toFixed(2));
	    		var iva = $(tipo_iva).val();
	    			var arrr = iva.split('%');
	    			iva = parseFloat(arrr[0]) + 100;
	    			subIVA = (iva * subt)/100;
	    			subIVA = subIVA - subt;
	    			$(imp_iva).val(parseFloat(subIVA).toFixed(2));
	    			var iva = $(tipo_iva).val();
	    			var sumIVA = 0;
	    			for(j=0;i>j;j++){
	    			var tipo_iva = '#tipo_iva_'+j;
	    			var imp_iva = '#imp_iva_'+j;
	    			var ti = $(tipo_iva).val();	
	    			if(iva == $(tipo_iva).val() ){
	    				sumIVA = sumIVA + parseFloat($(imp_iva).val()); 
	    			}

	    		}

	    			if(arrr[0]==21){
	    				$('#iva_21').val(sumIVA.toFixed(2));	
	    			}

	    			
	    		subt = 0;
	    		for(j=0;i>j;j++){
	    			subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
	    		}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			
	    		var total = parseFloat(subt) + parseFloat(iva21);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2));
	    	}
	    }


            function updateIVA(id){

	    	var imp_iva = $('#'+id).val();
	    	var arr = id.split('_');
                var punitario = $('#punitario_'+arr[1]).val();
	    	var cant = $('#cantidad_'+arr[1]).val();
	    		
                        var impbon = '#impbon_'+arr[1];
			var porbon = '#porbon_'+arr[1];

			if($(impbon).val()!=''){
	    		$(impbon).val('');
	    		$(porbon).val('');  		
	    		impbon = 0;
	    	}else{
	    		impbon = 0;
	    	}	
	    		var subt = cant * (punitario-impbon);
	    		$(subtotal).val(subt.toFixed(2));
	    			
                                var sumIVA = 0;
	    			for(j=0;i>j;j++){
	    			var tipo_iva = '#tipo_iva_'+j;
	    			var imp_iva = '#imp_iva_'+j;
	    			var ti = $(tipo_iva).val();	
	    			sumIVA = sumIVA + parseFloat($(imp_iva).val()); 
	    		        

	    		        }
                       
	    		$('#iva_21').val(sumIVA.toFixed(2));	
	    	

	    			
	    		subt = 0;
	    		for(j=0;i>j;j++){
	    			subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
	    		}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			
	    		var total = parseFloat(subt) + parseFloat(iva21);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2));
	    	
	    }

	   </script>


@endsection
