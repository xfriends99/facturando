p<!DOCTYPE html>
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
                                               @if(!Auth::guest() && (Auth::user()->email=='tpcontrolpro2@gmail.com' || Auth::user()->email=='tpcontroladm4@gmail.com'))
                                                        <li><a href="/viewProd">Producción</a></li>
                                               @endif
                                               @if(!Auth::guest()  && Auth::user()->roles_id==5)
                                                       <li><a href="/stock">Stock</a></li>
                                               @endif
						@if (!Auth::guest() && Auth::user()->roles_id!=4 && Auth::user()->roles_id!=5)
						<li><a href="{{ url('/') }}">Home</a></li>
						<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Ventas<span class="caret"></span></a>						
						<ul class="dropdown-menu" role="menu">
                                                        <li><a href="/listarPedidos">Pedidos</a></li>
                                                        @if(Auth::user()->roles_id!=3)
							<li><a href="/listarFacturas">Facturación</a></li>				
							<li><a href="/listarRemitos">Remito</a></li>
							<li><a href="/notaDebito">Nota de Debito</a></li>
							<li><a href="/notaCredito">Nota de Credito</a></li>
                                                        @endif
						</ul>
						</li>
<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Caja<span class="caret"></span></a>	
<ul class="dropdown-menu" role="menu">
                                                <li><a href="{{ url('caja') }}">Diaria</a></li>
                                                <li><a href="{{ url('cierre') }}">Cierres</a></li>
					        <li><a href="{{ url('conceptosCaja') }}">Conceptos</a></li>
</ul>
						</li>
@if(Auth::user()->roles_id!=3) <li><a href="/stock">Stock</a></li> @endif	
                                                        @if(Auth::user()->roles_id==1)
                                                        <li><a href="/viewProd">Producción</a></li>
							<li><a href="/costo">Costos</a></li>
							<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Caja Privada<span class="caret"></span></a>	
<ul class="dropdown-menu" role="menu">
							<li><a href="{{ url('cajaEspecial') }}">Diaria</a></li>
                            <li><a href="{{ url('cierreEspecial') }}">Cierres</a></li>
					        <li><a href="{{ url('conceptosCajaEspecial') }}">Conceptos</a></li>
					        </ul>
					        	</li>
							@endif
			                         
						<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Clientes<span class="caret"></span></a>						
						<ul class="dropdown-menu" role="menu">
							<li><a href="/ctacte">Cuentas Corrientes</a></li>
                                                         @if(Auth::user()->roles_id!=3)               
                                                        <li><a href="/asignarCorredor">Asignar Corredor</a></li> 
                                                        @endif
						</ul>
					</li>
@if(Auth::user()->roles_id!=3)  
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Proveedores<span class="caret"></span></a>						
						<ul class="dropdown-menu" role="menu">
							<li><a href="/proveedores">Alta y Modificación</a></li>
							<li><a href="/compras">Compras</a></li>
							<li><a href="#">Pagos</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Listados<span class="caret"></span></a>					
						<ul class="dropdown-menu" role="menu">
                                                        @if(Auth::user()->roles_id==1)
							<li><a href="/ventas">Detalle de Ventas</a></li>
							@endif
							<li><a href="#">Clientes</a></li>
							<li><a href="/cuentaCorriente">Cuentas Corrientes Clientes</a></li>
							<li><a href="/ivaVentas">I.V.A. Ventas</a></li>
							<li><a href="#">Proveedores</a></li>
							<li><a href="/listadoCtaCte">Listado Pago Cta. Cte.</a></li>
							<li><a href="/cuentaCorriente">Cuentas Corrientes Proveedores</a></li>
							<li><a href="/ivaCompras">I.V.A. Compras</a></li>
							<li><a href="/reporteCaja">Caja</a></li>
							<li><a href="/reporteCajaEspecial">Caja Especial</a></li>
							<li><a href="#">Banco</a></li>
							<li><a href="#">Cheques de Terceros</a></li>
							<li><a href="#">Tarjetas</a></li>							
						</ul>
					</li>
                                        
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Parámetros<span class="caret"></span></a>					
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Empresa</a></li>
							<li><a href="#">Tipo de Cbtes.</a></li>
							<li><a href="#">Formas de Pago</a></li>
							<li><a href="#">Bancos</a></li>
							<li><a href="#">Conceptos de Caja</a></li>
							<li><a href="/vendedoresTDP">Corredores</a></li>
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
