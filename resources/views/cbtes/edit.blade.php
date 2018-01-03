@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Editar {{$tipo_comprobante}}</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" id="facturaForm" action="/guardarCbte" method="POST">
						@if($invoice->company_name!=null)
						<input type='hidden' value='{{$invoice->id}}' class='form-control' name='invoice_id' id='invoice_id'/>
						<section>
							
							<p>	Razón Social: {{$invoice->company_name}} </p>
							<p>	Dirección: {{$invoice->address}} </p>
							<p>	I.V.A.: {{$invoice->fiscal_situation->fisc_situation}} </p>
							<p>	{{$invoice->tax_type->type . ': ' . $invoice->tax_id}} </p>
						<input type='hidden' value='{{$invoice->companies_id}}' class='form-control' name='customer_id' id='customer_id'/>
						</section>	
						<div class="form-group">	
								<label class="col-md-1 control-label">Fecha</label>
								<div class="col-md-3">
									<input type='date' class='form-control' id='fecha' name='fecha' value='{{$invoice->fecha_facturacion}}' required/>	
								</div>					
						</div>
						@else
						<section>
							<div class="form-group">	
								<label class="col-md-1 control-label">Cliente</label>
								<div class="col-md-3">
									<input type='text' class='form-control' id='buscarCliente'/>	
								</div>					
								<button type="button" id="newCustomer" class="btn btn-info" data-toggle="modal" data-target="#myModal">Crear Cliente</button>
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
						<section>
							<div class="form-group">
								<label class="col-md-1 control-label">Concepto</label>
								<div class="col-md-3">
									<select class="form-control" name="concepto" id="concepto" value="{{ old('concepto') }}" required>
										<option value="">Seleccione Concepto</option>
										@foreach($conceptos as $concepto)	
										<option value="{{$concepto->code}}" @if($concepto->code==$invoice->concepto) selected @endif>{{$concepto->concepto}}</option>
										@endforeach
									</select>	
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
									<th>U. Medida</th>
									<th>Precio Unit.</th>
									<th>% Bonif.</th>
									<th>Imp. Bonif.</th>
									<th>I.V.A.</th>
									<th>Imp. I.V.A.</th>
									<th>Subtotal</th>
								</tr>
							</thead>
							<tbody>
								<?php $i=0; ?>
								@foreach($lines as $line)
								<tr>
									<td>
										<input type='text' class='form-control' name='code_{{$i}}' id='code_{{$i}}' value='{{$line->code}}' />
									</td>
									<td>
										<input type='text' oninput='buscar(this.id)' class='form-control' value='{{$line->name}}' name='product_{{$i}}' id='product_{{$i}}'/>
									</td>
									<td>
										<input type='text' class='form-control' name='cantidad_{{$i}}' id='cantidad_{{$i}}' value='{{$line->quantity}}' onchange='updateCantidades(this.id)' required/>
									</td>
									<td>
										<input type='text' class='form-control' value='{{$line->umedida}}' name='umedida_{{$i}}' id='umedida_{{$i}}'/>
									</td>
									<td>
										<input type='text' class='form-control' name='punitario_{{$i}}' id='punitario_{{$i}}' value='{{$line->price}}' onchange='updateTotales(this.id)' required/>
									</td>
									<td>
										<input type='text' class='form-control' value='{{$line->por_desc}}' name='porbon_{{$i}}' onchange='por_descuento(this.id)' id='porbon_{{$i}}'/></td>
										<td>
											<input type='text' class='form-control' value='{{$line->imp_desc}}' name='impbon_{{$i}}' onchange='descuento(this.id)' id='impbon_{{$i}}'/>
										</td>
										<td>
											<input type='hidden' class='form-control' name='tipo_iva_id_{{$i}}' id='tipo_iva_id_{{$i}}' value='{{$line->tipo_iva}}' required/>
											<input type='text' class='form-control' name='tipo_iva_{{$i}}' id='tipo_iva_{{$i}}' value='{{$line->tipo_iva_id->tipo_iva}}' readonly required/>
										</td>
										<td>
											<input type='text' class='form-control' name='imp_iva_{{$i}}' id='imp_iva_{{$i}}' value='{{$line->imp_iva}}' readonly required/>
										</td>
										<td>
											<input type='text' class='form-control' value='{{$line->subtotal}}' name='subtotal_{{$i}}' id='subtotal_{{$i}}' readonly required/></td>
										</tr>
										<?php $i++; ?>
										@endforeach
									</tbody>
								</table>
								<table class="table table-bordered">
									<thead>
										<tr>
										<th>Subtotal</th>
										<th>I.V.A. 10.5%</th>
										<th>I.V.A. 21%</th>
										<th>I.V.A. 27%</th>
										<th>Total</th>

										</thead>
										<tbody>
											<tr>
												<input type='hidden' class='form-control' name='cant_lineas' id='cant_lineas' value='{{$invoice->total_lineas}}'/>
											<td><input type='text' class='form-control' name='subtotal' id='subtotal' value='{{$invoice->imp_net}}'readonly required/></td>
											<td><input type='text' class='form-control' name='iva_10_5' id='iva_10_5' value='{{$invoice->imp_iva_10_5}}' readonly required/></td>
											<td><input type='text' class='form-control' name='iva_21' id='iva_21' value='{{$invoice->imp_iva_21}}' readonly required/></td>
											<td><input type='text' class='form-control' name='iva_27' id='iva_27' value='{{$invoice->imp_iva_27}}' readonly required/></td>
											<td><input type='text' class='form-control' name='total' id='total' value='{{$invoice->imp_total}}' readonly required/></td>
											</tr>
										</tbody>
									</table>
									<div class="form-group">
										<div class="col-md-6 col-md-offset-3"><br/>
											<a href="{{ URL::to('home')}}" class="btn btn-danger">
												Cancelar operación
											</a>
											<button type="submit" name="guardar" value="guardar" class="btn btn-info">
												Guardar copia
											</button>
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

		<script>
			$.widget( "custom.catcomplete", $.ui.autocomplete, {
				_create: function() {
					this._super();
					this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
				},
				_renderMenu: function( ul, items ) {
					var that = this,
					currentCategory = "";
					$.each( items, function( index, item ) {
						var li;
						if ( item.category != currentCategory ) {
							ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
							currentCategory = item.category;
						}
						li = that._renderItemData( ul, item );
						if ( item.category ) {
							li.attr( "aria-label", item.category + " : " + item.label );
						}
					});
				}
			});
		</script>
		<script type="text/javascript">

		var i = {{$i}};  

		$("#addFile").click(function () { 

			$('#tblData tbody').append("<tr><td><input type='text' class='form-control' name='code_"+i+"' id='code_"+i+"'/></td><td><input type='text' oninput='buscar(this.id)' class='form-control' name='product_"+i+"' id='product_"+i+"'/></td><td><input type='text' class='form-control' name='cantidad_"+i+"' id='cantidad_"+i+"' onchange='updateCantidades(this.id)' required/></td><td><input type='text' class='form-control' name='umedida_"+i+"' id='umedida_"+i+"'/></td><td><input type='text' class='form-control' name='punitario_"+i+"' id='punitario_"+i+"' onchange='updateTotales(this.id)' required/></td><td><input type='text' class='form-control' name='porbon_"+i+"' onchange='por_descuento(this.id)' id='porbon_"+i+"'/></td><td><input type='text' class='form-control' name='impbon_"+i+"' onchange='descuento(this.id)' id='impbon_"+i+"'/></td><td><input type='text' class='form-control' name='tipo_iva_"+i+"' id='tipo_iva_"+i+"' readonly required/><input type='hidden' class='form-control' name='tipo_iva_id_"+i+"' id='tipo_iva_id_"+i+"' required/></td><td><input type='text' class='form-control' name='imp_iva_"+i+"' id='imp_iva_"+i+"' readonly required/></td><td><input type='text' class='form-control' name='subtotal_"+i+"' id='subtotal_"+i+"' readonly required/></td></tr>"); 
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
				var iva10 = 0;
				var iva21 = 0;
				var iva27 = 0;
				for(j=0;i>j;j++){
					subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
					var tipo_iva = $('#tipo_iva_'+j).val();
					var arrr = tipo_iva.split('%');
					if(arrr[0]==10.5){
	    				iva10 = parseFloat(iva10 + parseFloat($('#imp_iva_'+j).val()));
	    				}	
	    			if(arrr[0]==21){
	    				iva21 = parseFloat(iva21 + parseFloat($('#imp_iva_'+j).val()));
	    				}
	    			if(arrr[0]==27){
	    				iva27 = parseFloat(iva27 + parseFloat($('#imp_iva_'+j).val()));
	    			}
					
				}
				
				if(iva10!=0){
	    				$('#iva_10_5').val(iva10.toFixed(2));
	    			}
	    		if(iva21!=0){
	    				$('#iva_21').val(iva21.toFixed(2));
	    			}
	    		if(iva27!=0){
	    			$('#iva_27').val(iva27.toFixed(2));
	    			}
	    			
		var total = parseFloat(subt) + parseFloat(iva10) + parseFloat(iva21) + parseFloat(iva27);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2)); 
			});      

	    function buscar(id){
	    	var element = '#'+id;
	    	var arr = id.split('_');
	    	var code = '#code_'+arr[1];
	    	var punitario = '#punitario_'+arr[1];
	    	var cantidad = '#cantidad_'+arr[1];
	    	var subtotal = '#subtotal_'+arr[1];
	    	var tipo_iva = '#tipo_iva_'+arr[1];
	    	var tipo_iva_id = '#tipo_iva_id_'+arr[1];
	    	var imp_iva = '#imp_iva_'+arr[1];
	    	var subt = 0;
	    	$( element ).catcomplete({
	    		source: "/search/autocompleteProducts",
	    		select: function (event, ui) {
	    			$(element).val(ui.item.label);
	    			$(code).val(ui.item.value); 
	    			$(tipo_iva).val(ui.item.tipo_iva); 
	    			$(tipo_iva_id).val(ui.item.tipo_iva_id); 
	    			$(punitario).val(ui.item.uprice);
	    			var iva = ui.item.tipo_iva;
	    			var arrr = iva.split('%');
	    			iva = parseFloat(arrr[0]) + 100;
	    			subIVA = (iva * ui.item.uprice)/100;
	    			subIVA = subIVA - ui.item.uprice;
	    			$(imp_iva).val(parseFloat(subIVA));
	    			$(cantidad).val(1); 
	    			$(subtotal).val(ui.item.uprice);
	    			if(arrr[0]==10.5){
	    				if($('#iva_10_5').val()!=''){
							var iva10 = parseFloat($('#iva_10_5').val());
	    				}else{
	    					var iva10 = 0;
	    				}
	    			iva10 = parseFloat(iva10) + parseFloat(subIVA);
	    			 $('#iva_10_5').val(parseFloat(iva10));
	    			}
	    			if(arrr[0]==21){
	    				if($('#iva_21').val()!=''){
	    					var iva21 = parseFloat($('#iva_21').val());
	    				}else{
	    					var iva21 = 0;
	    				}
	    			 iva21 = parseFloat(iva21) + parseFloat(subIVA);
	    			 $('#iva_21').val(parseFloat(iva21));
	    			}
	    			if(arrr[0]==27){
	    				if($('#iva_27').val()!=''){
	    					var iva27 = parseFloat($('#iva_27').val());
	    				}else{
	    					var iva27 = 0;
	    				}
	    			 iva27 = parseFloat(iva27) + parseFloat(subIVA);
	    			 $('#iva_27').val(parseFloat(iva27));
	    			}
	    			for(j=0;i>j;j++){
	    				subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
	    			}

	    			if($('#iva_10_5').val()!=''){
	    				var iva10 = $('#iva_10_5').val();
	    			}else{
	    				var iva10 = 0;
	    			}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			if($('#iva_27').val()!=''){
	    				var iva27 = $('#iva_27').val();
	    			}else{
	    				var iva27 = 0;
	    			}

	    	var total = parseFloat(subt) + parseFloat(iva10) + parseFloat(iva21) + parseFloat(iva27);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2));

	    			return false;
	    		}
	    	});

	    }

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

	    			if(arrr[0]==10.5){
	    				$('#iva_10_5').val(sumIVA.toFixed(2));
	    			}
	    			if(arrr[0]==21){
	    				$('#iva_21').val(sumIVA.toFixed(2));	
	    			}

	    			if(arrr[0]==27){
	    				$('#iva_27').val(sumIVA.toFixed(2));	
	    			}

	    		subt = 0;
	    		for(j=0;i>j;j++){
	    			subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
	    		}
	    		
	    		if($('#iva_10_5').val()!=''){
	    				var iva10 = $('#iva_10_5').val();
	    			}else{
	    				var iva10 = 0;
	    			}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			if($('#iva_27').val()!=''){
	    				var iva27 = $('#iva_27').val();
	    			}else{
	    				var iva27 = 0;
	    			}

	    		var total = parseFloat(subt) + parseFloat(iva10) + parseFloat(iva21) + parseFloat(iva27);
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

	    			if(arrr[0]==10.5){
	    				$('#iva_10_5').val(sumIVA.toFixed(2));
	    			}
	    			if(arrr[0]==21){
	    				$('#iva_21').val(sumIVA.toFixed(2));	
	    			}

	    			if(arrr[0]==27){
	    				$('#iva_27').val(sumIVA.toFixed(2));	
	    			}
	    		subt = 0;
	    		for(j=0;i>j;j++){
	    			subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
	    		}
	    		if($('#iva_10_5').val()!=''){
	    				var iva10 = $('#iva_10_5').val();
	    			}else{
	    				var iva10 = 0;
	    			}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			if($('#iva_27').val()!=''){
	    				var iva27 = $('#iva_27').val();
	    			}else{
	    				var iva27 = 0;
	    			}

	    		var total = parseFloat(subt) + parseFloat(iva10) + parseFloat(iva21) + parseFloat(iva27);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2));
	    	}
	    }

	    $( "#concepto" ).change(function() {
	     	if($( "#concepto" ).val()==1 || $( "#concepto" ).val()==""){
	    		$('#fechas').html("");	
	    	}else{
	    		$('#fechas').html("<hr> <section> <div class='form-group'> <label class='col-md-2 control-label'>Periodo facturado desde</label> <div class='col-md-2'> <input type='date' class='form-control' name='fecha_desde' onchange='checkDays()' id='fecha_desde' required> </div> <label class='col-md-1 control-label'>Hasta</label> <div class='col-md-2'> <input type='date' class='form-control' name='fecha_hasta' onchange='checkDays()' id='fecha_hasta' required> </div> <label class='col-md-3 control-label'>Fecha de Vto. para el pago</label><div class='col-md-2'> <input type='date' class='form-control' name='fecha_vto_pago' id='fecha_vto_pago' onchange='checkDays()' required></div> </div>");

	    	}

	    });

	    function checkDays(){
	    	var desde = "#fecha_desde";
	    	var hasta = "#fecha_hasta";
	    	var vto_pago = "#fecha_vto_pago";

	    	if($(desde).val()!=""){
	    		if($(hasta).val()!=""){
	    			if(new Date($(desde).val()) > new Date($(hasta).val())){
	    				alert("Fecha 'desde' debe ser menor/igual a fecha 'hasta'.");
	    				$(desde).val($(hasta).val());
	    			}
	    			if($(vto_pago).val()!=""){
	    			if(new Date($(hasta).val()) > new Date($(vto_pago).val())){
	    				alert("Fecha 'hasta' debe ser menor/igual a fecha 'Vto. de Pago'.");
	    				$(hasta).val($(vto_pago).val());
	    				}
	    			}		
	    		}
	    		if($(vto_pago).val()!=""){
	    			if(new Date($(desde).val()) > new Date($(vto_pago).val())){
	    				alert("Fecha 'desde' debe ser menor/igual a fecha 'Vto. de Pago'.");
	    				if($(hasta).val()!=""){
	    					$(desde).val($(hasta).val());	
	    				}else{
	    					$(desde).val($(vto_pago).val());
	    				}
	    				
	    			}		
	    		}
	    	}
	    
	    }

	    $( "#buscarCliente" ).autocomplete({
	    	source: "/search/autocompleteCustomer",
	    	select: function (event, ui) {
	    		$( "#buscarCliente" ).val(ui.item.label);
	    		$('#resulCliente').html("");
	    		$('#resulCliente').html("<p>Razón Social: "+ ui.item.label +"</p> <p> Dirección: "+ ui.item.dire +" </p> <p> I.V.A.: "+ ui.item.iva +" </p> <p> "+ui.item.tax_type+" : "+ ui.item.tax_id +" </p>");
	    		$('#customer_id').val(ui.item.id);   
	    		return false;
	    	}
	    });

	</script>
	<script type="text/javascript">
	$('#country').change(function(){
		var countryId = $(this).val();
		$ciudaditems = $('.stateItems').remove();
		$.post("{{ URL::to('states') }}"+'/'+countryId, function(data){
			$.each(data, function(index, element){
		        	//console.log(element);
		        	$('#state').append('<option value="'+element.id+'" class="stateItems">'+element.state+'</option>')
		        });
		}, 'json');
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
					$('#resulCliente').html("<p>Razón Social: "+ msg.company_name +"</p> <p> Dirección: " + msg.dire + "  </p> <p> I.V.A.: "+ msg.fs +" </p> <p> "+msg.tax_type +" : "+ msg.tax_id +" </p>"); 
					$('#customer_id').val(msg.id);
					$("#myModal").modal('hide');
				}

			}
		});
	});
	});


	function descuento(id){

		var arr = id.split('_');
		var porbon = '#porbon_'+arr[1];
		var subtotal = '#subtotal_'+arr[1];
		var punitario = '#punitario_'+arr[1];
		var cantidad = '#cantidad_'+arr[1];	
		var tipo_iva = '#tipo_iva_'+arr[1];
		var imp_iva = '#imp_iva_'+arr[1];

		if( $('#'+id).val() != "" && $('#'+id).val() > 0  ){

			if(parseFloat($('#'+id).val()) >= parseFloat($(punitario).val())){
				alert("Ingrese un descuento menor al precio unitario.");
				$('#'+id).val("");
			}else{
				desc = parseFloat($('#'+id).val() * $(cantidad).val());
				subt =  parseFloat(($(cantidad).val() * $(punitario).val()) - desc);
				$(subtotal).val(subt.toFixed(2));
				var iva = $(tipo_iva).val();
	    			var arrr = iva.split('%');
	    			iva = parseFloat(arrr[0]) + 100;
	    			subIVA = (iva * subt)/100;
	    			subIVA = subIVA - subt;
	    			$(imp_iva).val(parseFloat(subIVA.toFixed(2)));
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

	    			if(arrr[0]==10.5){
	    				$('#iva_10_5').val(sumIVA.toFixed(2));
	    			}
	    			if(arrr[0]==21){
	    				$('#iva_21').val(sumIVA.toFixed(2));	
	    			}

	    			if(arrr[0]==27){
	    				$('#iva_27').val(sumIVA.toFixed(2));	
	    			}
				subt = 0;
				for(j=0;i>j;j++){
					subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
				}
				if($('#iva_10_5').val()!=''){
	    				var iva10 = $('#iva_10_5').val();
	    			}else{
	    				var iva10 = 0;
	    			}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			if($('#iva_27').val()!=''){
	    				var iva27 = $('#iva_27').val();
	    			}else{
	    				var iva27 = 0;
	    			}

	    		var total = parseFloat(subt) + parseFloat(iva10) + parseFloat(iva21) + parseFloat(iva27);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2));
				var porcentaje = parseFloat(($('#'+id).val() * 100) / $(punitario).val());
				$(porbon).val(porcentaje);
				$(porbon).attr('readonly', true);
			}

		}else{
			$(subtotal).val(parseFloat($(cantidad).val() * $(punitario).val()));
			$(porbon).val("");
			$(porbon).attr('readonly', false);
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

	    			if(arrr[0]==10.5){
	    				$('#iva_10_5').val(sumIVA.toFixed(2));
	    			}
	    			if(arrr[0]==21){
	    				$('#iva_21').val(sumIVA.toFixed(2));	
	    			}

	    			if(arrr[0]==27){
	    				$('#iva_27').val(sumIVA.toFixed(2));	
	    			}
			subt = 0;
			for(j=0;i>j;j++){
				subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
			}
			if($('#iva_10_5').val()!=''){
	    				var iva10 = $('#iva_10_5').val();
	    			}else{
	    				var iva10 = 0;
	    			}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			if($('#iva_27').val()!=''){
	    				var iva27 = $('#iva_27').val();
	    			}else{
	    				var iva27 = 0;
	    			}

	    		var total = parseFloat(subt) + parseFloat(iva10) + parseFloat(iva21) + parseFloat(iva27);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2));		 			
		}

	}

	function por_descuento(id){

		var arr = id.split('_');
		var impbon = '#impbon_'+arr[1];
		var subtotal = '#subtotal_'+arr[1];
		var punitario = '#punitario_'+arr[1];
		var cantidad = '#cantidad_'+arr[1];	
		var tipo_iva = '#tipo_iva_'+arr[1];
		var imp_iva = '#imp_iva_'+arr[1];

		if( $('#'+id).val() != "" && $('#'+id).val() > 0  ){

			if(parseFloat($('#'+id).val()) >= 100){
				alert("Ingrese un porcentaje menor a 100.");
				$('#'+id).val("");
			}else{
				desc = parseFloat(($('#'+id).val() * $(punitario).val())/100);
				subt =  parseFloat(($(punitario).val() - desc)  * $(cantidad).val());
				$(subtotal).val(subt.toFixed(2));
				var iva = $(tipo_iva).val();
	    			var arrr = iva.split('%');
	    			iva = parseFloat(arrr[0]) + 100;
	    			subIVA = (iva * subt)/100;
	    			subIVA = subIVA - subt;
	    			$(imp_iva).val(parseFloat(subIVA.toFixed(2)));
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

	    			if(arrr[0]==10.5){
	    				$('#iva_10_5').val(sumIVA.toFixed(2));
	    			}
	    			if(arrr[0]==21){
	    				$('#iva_21').val(sumIVA.toFixed(2));	
	    			}

	    			if(arrr[0]==27){
	    				$('#iva_27').val(sumIVA.toFixed(2));	
	    			}
				subt = 0;
				for(j=0;i>j;j++){
					subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
				}
				if($('#iva_10_5').val()!=''){
	    				var iva10 = $('#iva_10_5').val();
	    			}else{
	    				var iva10 = 0;
	    			}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			if($('#iva_27').val()!=''){
	    				var iva27 = $('#iva_27').val();
	    			}else{
	    				var iva27 = 0;
	    			}

	    		var total = parseFloat(subt) + parseFloat(iva10) + parseFloat(iva21) + parseFloat(iva27);
	    		$('#subtotal').val(subt.toFixed(2)); 
	    		$('#total').val(total.toFixed(2));
				$(impbon).val(desc);
				$(impbon).attr('readonly', true);
			}

		}else{
			$(subtotal).val(parseFloat($(cantidad).val() * $(punitario).val()));
			$(impbon).val("");
			$(impbon).attr('readonly', false);
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

	    			if(arrr[0]==10.5){
	    				$('#iva_10_5').val(sumIVA.toFixed(2));
	    			}
	    			if(arrr[0]==21){
	    				$('#iva_21').val(sumIVA.toFixed(2));	
	    			}

	    			if(arrr[0]==27){
	    				$('#iva_27').val(sumIVA.toFixed(2));	
	    			}
			subt = 0;
			for(j=0;i>j;j++){
				subt = parseFloat(subt + parseFloat($('#subtotal_'+j).val()));
			}	

					if($('#iva_10_5').val()!=''){
	    				var iva10 = $('#iva_10_5').val();
	    			}else{
	    				var iva10 = 0;
	    			}
	    			if($('#iva_21').val()!=''){
	    				var iva21 = $('#iva_21').val();
	    			}else{
	    				var iva21 = 0;
	    			}
	    			if($('#iva_27').val()!=''){
	    				var iva27 = $('#iva_27').val();
	    			}else{
	    				var iva27 = 0;
	    			}

	    		var total = parseFloat(subt) + parseFloat(iva10) + parseFloat(iva21) + parseFloat(iva27);
	    			$('#subtotal').val(subt.toFixed(2)); 
	    			$('#total').val(total.toFixed(2));		 			
		}

	}
	</script>


@endsection
