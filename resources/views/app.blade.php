<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>FACTURANDO!</title>

	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="/js/typeahead.js"></script>
	<script>
        function checkString(id){
            var element = '#'+id;
            var doc = $(element).val();
            if(/^[0-9a-zA-Z]+$/.test(doc)===false)
            {
                alert("Solo se admiten números y/o letras.");
            }
        }
	</script>

	<style>
		.ui-autocomplete-category {
			font-weight: bold;
			padding: .2em .4em;
			margin: .8em 0 .2em;
			line-height: 1.5;
		}
	</style>
</head>

<body>
<nav class="navbar navbar-default">
	<small style="color:#60D75A">Versión: 2.8</small>
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle Navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Demo Facturando</a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				@if (!Auth::guest())
					<li><a href="{{ url('/') }}">Home</a></li>
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Ventas'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Ventas<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Ventas', 'pedidos'))
									<li><a href="/listarPedidos">Pedidos</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Ventas', 'facturacion'))
									<li><a href="/listarFacturas">Facturación</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Ventas', 'remito'))
									<li><a href="/listarRemitos">Remito</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Ventas', 'nota_debito'))
									<li><a href="/notaDebito">Nota de Debito</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Ventas', 'nota_credito'))
									<li><a href="/notaCredito">Nota de Credito</a></li>
								@endif
							</ul>
						</li>
					@endif
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Caja'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Caja<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Caja', 'diaria'))
									<li><a href="{{ url('caja') }}">Diaria</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Caja', 'cierres'))
										<li><a href="{{ url('cierre') }}">Cierres</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Caja', 'conceptos'))
										<li><a href="{{ url('conceptosCaja') }}">Conceptos</a></li>
								@endif
							</ul>
						</li>
					@endif
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Stock', 'stock'))
						<li><a href="/stock">Stock</a></li>
					@endif
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Productos', 'productos'))
						<li><a href="/products">Productos</a></li>
					@endif
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Produccion'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Producción<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Produccion', 'produccion'))
									<li><a href="/produccion">Producción</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Produccion', 'detalle'))
									<li><a href="/viewProd">Detalle</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Produccion', 'carga_manual'))
									<li><a href="/cargaManualProduccion">Carga manual</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Produccion', 'control'))
									<li><a href="/controlProduccion">Control</a></li>
								@endif
							</ul>
						</li>
					@endif
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Costos', 'costos'))
						<li><a href="/costo">Costos</a></li>
					@endif

					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Caja Privada'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Caja Privada<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Caja Privada', 'diaria'))
									<li><a href="{{ url('cajaEspecial') }}">Diaria</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Caja Privada', 'cierres'))
									<li><a href="{{ url('cierreEspecial') }}">Cierres</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Caja Privada', 'conceptos'))
									<li><a href="{{ url('conceptosCajaEspecial') }}">Conceptos</a></li>
								@endif
							</ul>
						</li>
					@endif

					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Clientes'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Clientes<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Clientes', 'cuentas_corrientes'))
									<li><a href="/ctacte">Cuentas Corrientes</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Clientes', 'asignar_corredor'))
										<li><a href="/asignarCorredor">Asignar Corredor</a></li>
								@endif
							</ul>
						</li>
					@endif
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Proveedores'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Proveedores<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Proveedores', 'alta_modificacion'))
									<li><a href="/proveedores">Alta y Modificación</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Proveedores', 'compras'))
										<li><a href="/compras">Compras</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Proveedores', 'pagos'))
										<li><a href="#">Pagos</a></li>
								@endif
							</ul>
						</li>
					@endif
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Listados'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Listados<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'detalle_ventas'))
									<li><a href="/ventas">Detalle de Ventas</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'clientes'))
									<li><a href="#">Clientes</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'cuentas_corrientes_clientes'))
									<li><a href="/cuentaCorriente">Cuentas Corrientes Clientes</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'iva'))
									<li><a href="/ivaVentas">I.V.A. Ventas</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'proveedores'))
									<li><a href="#">Proveedores</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'listado_productos'))
										<li><a href="/listadoProducto">Listado Productos</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'listado_productos_pedidos'))
									<li><a href="/listadoProductoPedidos">Listado Productos Pedidos</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'listado_stock_productos_pedidos'))
									<li><a href="/listadoStock">Listado Stock Productos Pedidos</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'listado_stock'))
									<li><a href="/listadoStockTipo">Listado Stock</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'listado_control_produccion'))
									<li><a href="/listadoControlProduccion">Listado Control de Producción</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'listado_cta_cte'))
									<li><a href="/listadoCtaCtes">Listado Cta. Cte.</a></li>
								@endif
								<!--<li><a href="/listadoCtaCte">Listado Pago Cta. Cte.</a></li>-->
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'cuentas_corrientes_proveedores'))
									<li><a href="/cuentaCorrienteProviders">Cuentas Corrientes Proveedores</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'iva_compras'))
									<li><a href="/ivaCompras">I.V.A. Compras</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'caja'))
									<li><a href="/reporteCaja">Caja</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'caja_especial'))
									<li><a href="/reporteCajaEspecial">Caja Especial</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'banco'))
									<li><a href="#">Banco</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'cheques_terceros'))
									<li><a href="#">Cheques de Terceros</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Listados', 'tarjetas'))
									<li><a href="#">Tarjetas</a></li>
								@endif

							</ul>
						</li>
					@endif
					@if(Auth::user()->roles_id==1 || Auth::user()->getPermissionType('Parametros'))
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Parámetros<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Parametros', 'empresa'))
									<li><a href="#">Empresa</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Parametros', 'tipo_cbtes'))
									<li><a href="#">Tipo de Cbtes.</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Parametros', 'formas_pago'))
									<li><a href="#">Formas de Pago</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Parametros', 'bancos'))
									<li><a href="#">Bancos</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Parametros', 'conceptos_caja'))
									<li><a href="#">Conceptos de Caja</a></li>
								@endif
								@if(Auth::user()->roles_id==1 || Auth::user()->getPermission('Parametros', 'corredores'))
									<li><a href="/vendedoresTDP">Corredores</a></li>
								@endif
							</ul>
						</li>
					@endif
				@endif
			</ul>

			<ul class="nav navbar-nav navbar-right">
				@if (Auth::guest())
					<li><a href="{{ url('/auth/login') }}">Ingresar</a></li>
				@else
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name .' '. Auth::user()->lastname }} <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							@if(Auth::user()->roles_id!=4)
								<li><a href="/profile/{{Auth::user()->id}}">Perfil</a></li>
								@if(Auth::user()->roles_id==1)
									<li><a href="/adduser">Agregar + Usuarios</a></li>
									<li><a href="/users">Listar Usuarios</a></li>
								@endif
							@endif
							<li><a href="/auth/logout">Logout</a></li>
						</ul>
					</li>
				@endif
			</ul>
		</div>
	</div>
</nav>

@yield('content')

</body>
</html>