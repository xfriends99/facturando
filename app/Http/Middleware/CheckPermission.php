<?php namespace app\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        $response = $next($request);
	    $url = $request->path();
	    if($url=='listarPedidos' && (Auth::guest() || (!Auth::user()->getPermission('Ventas', 'pedidos') && Auth::user()->roles_id!=1))){
            return redirect('home');
        } elseif($url=='listarFacturas' && (Auth::guest() || (!Auth::user()->getPermission('Ventas', 'facturacion') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='listarRemitos' && (Auth::guest() || (!Auth::user()->getPermission('Ventas', 'remito') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='notaDebito' && (Auth::guest() || (!Auth::user()->getPermission('Ventas', 'nota_debito') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='notaCredito' && (Auth::guest() || (!Auth::user()->getPermission('Ventas', 'nota_credito') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='caja' && (Auth::guest() || (!Auth::user()->getPermission('Caja', 'diaria') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='cierre' && (Auth::guest() || (!Auth::user()->getPermission('Caja', 'cierres') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='conceptosCaja' && (Auth::guest() || (!Auth::user()->getPermission('Caja', 'conceptos') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='stock' && (Auth::guest() || (!Auth::user()->getPermission('Stock', 'stock') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='products' && (Auth::guest() || (!Auth::user()->getPermission('Productos', 'productos') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='viewProd' && (Auth::guest() || (!Auth::user()->getPermission('Produccion', 'detalle') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='cargaManualProduccion' && (Auth::guest() || (!Auth::user()->getPermission('Produccion', 'control') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='controlProduccion' && (Auth::guest() || (!Auth::user()->getPermission('Produccion', 'carga_manual') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='costo' && (Auth::guest() || (!Auth::user()->getPermission('Costos', 'costos') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='cajaEspecial' && (Auth::guest() || (!Auth::user()->getPermission('Caja Privada', 'diaria') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='cierreEspecial' && (Auth::guest() || (!Auth::user()->getPermission('Caja Privada', 'cierres') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='conceptosCajaEspecial' && (Auth::guest() || (!Auth::user()->getPermission('Caja Privada', 'conceptos') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='ctacte' && (Auth::guest() || (!Auth::user()->getPermission('Clientes', 'cuentas_corrientes') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='asignarCorredor' && (Auth::guest() || (!Auth::user()->getPermission('Clientes', 'asignar_corredor') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='proveedores' && (Auth::guest() || (!Auth::user()->getPermission('Proveedores', 'alta_modificacion') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='compras' && (Auth::guest() || (!Auth::user()->getPermission('Proveedores', 'compras') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='ventas' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'detalle_ventas') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='cuentaCorriente' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'cuentas_corrientes_clientes') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='ivaVentas' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'iva') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='listadoProducto' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'listado_productos') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='listadoProductoPedidos' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'listado_productos_pedidos') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='listadoStock' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'listado_stock_productos_pedidos') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='listadoStockTipo' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'listado_stock') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='listadoCtaCtes' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'listado_cta_cte') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='cuentaCorrienteProviders' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'cuentas_corrientes_proveedores') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='ivaCompras' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'iva_compras') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='reporteCaja' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'caja') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='reporteCajaEspecial' && (Auth::guest() || (!Auth::user()->getPermission('Listados', 'caja_especial') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        } elseif($url=='vendedoresTDP' && (Auth::guest() || (!Auth::user()->getPermission('Parametros', 'corredores') && Auth::user()->roles_id!=1))) {
            return redirect('home');
        }

        return $response;
	}

}
