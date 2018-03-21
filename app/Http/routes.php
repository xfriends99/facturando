<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index'); 

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('infoFact', 'FacturaController@info');

Route::get('stock', 'StockController@getStock');


Route::get('cargaProd', 'StockController@getStock'); /* Cargo listado de stock. Y por cada vez que te guarden un numero almaceno un registro timestamp. Armo pantalla para dejarte filtrar por fecha y te junto todos los productos. */


Route::get('costo', 'CostoController@getCosto');

Route::get('products', 'ProductsController@listProducts');
Route::get('products/create', 'ProductsController@getAddProduct');
Route::post('products/store', 'ProductsController@postAddProduct');
Route::get('products/{id}/edit', 'ProductsController@getEditProduct');
Route::post('products/{id}/update', 'ProductsController@postEditProduct');
Route::get('products/{id}/delete', 'ProductsController@deleteProduct');

Route::get('addProduccion', function(){

return view('produccion.add');

});


Route::get('search/autocompleteProducts', function(){

                $term = Input::get('term');
	
		$results = array();

		$products = app\ProductoTDP::where('codigo', 'LIKE', '%'.$term.'%')->orWhere('descripcion', 'LIKE', '%'.$term.'%')->take(5)->get();

		foreach ($products as $product)
		{
			

		$results[] = [ 'label' => $product->codigo, 'descripcion' => $product->descripcion, 'id' => $product->id, 'peso' => $product->pesoRef, 'diametro' => $product->diametroRef, 'metros' => $product->metrosRef ];

			
		}

		return Response::json($results);

});

Route::get('getProdAjax/{id}', function($id = null){

              
		$product = app\ProductoTDP::where('codigo', 'LIKE', '%'.$id.'%')->first();

		$results[0] = $product->descripcion;
$results[1] = $product->pesoRef;
$results[2] = $product->diametroRef;
$results[3] = $product->metrosRef;

		$results[4] = $product->id;

		return $results;

});

Route::get('listarPedidos', 'PedidosController@getListarPedidos');

Route::get('profile/{id}', 'UsersController@getEditProfile');
Route::post('editProfile', 'UsersController@postEditProfile');
Route::get('adduser', 'UsersController@getAddUser');
Route::post('newuser', 'UsersController@postAddUser');
Route::get('users', 'UsersController@listUser');
Route::get('deleteUser/{id}', 'UsersController@deleteUser');

Route::get('magia', 'FacturaController@procesarLAFactura');

Route::get('caja/{date?}', 'CajaController@listCajaMov');
Route::get('movimiento', 'CajaController@getCajaMov');
Route::post('newMovimiento', 'CajaController@postCajaMov');
Route::get('cerrarCaja', 'CajaController@getCerrarCaja');
Route::post('cerrarCaja', 'CajaController@postCerrarCaja');
Route::get('cierre', 'CajaController@cierreCaja');
Route::get('conceptosCaja', 'CajaController@getConceptosCaja');
Route::post('conceptosCaja', 'CajaController@postConceptosCaja');
Route::get('deleteConceptoCaja/{id}', 'CajaController@deleteConcepto');

Route::get('reporteCaja/{fechas?}', 'CajaController@reporteCaja');

Route::get('cajaEspecial/{date?}', 'CajaEspecialController@listCajaMov');
Route::get('movimientoEspecial', 'CajaEspecialController@getCajaMov');
Route::post('newMovimientoEspecial', 'CajaEspecialController@postCajaMov');
Route::get('cerrarCajaEspecial', 'CajaEspecialController@getCerrarCaja');
Route::post('cerrarCajaEspecial', 'CajaEspecialController@postCerrarCaja');
Route::get('cierreEspecial', 'CajaEspecialController@cierreCaja');
Route::get('conceptosCajaEspecial', 'CajaEspecialController@getConceptosCaja');
Route::post('conceptosCajaEspecial', 'CajaEspecialController@postConceptosCaja');
Route::get('deleteConceptoCajaEspecial/{id}', 'CajaEspecialController@deleteConcepto');

Route::get('reporteCajaEspecial/{fechas?}', 'CajaEspecialController@reporteCaja');

Route::get('exportCustomers', 'CompaniesController@exportCustomers');


Route::get('altaCliente', 'CompaniesController@addCustomer');
Route::post('createCustomer', 'CompaniesController@addCustomerPost');
Route::get('editarCliente/{id}', 'CompaniesController@editCustomer');
Route::get('clientes', 'CompaniesController@listCustomers');
Route::get('eliminarCliente/{id}', 'CompaniesController@deleteCompany');
Route::post('editCustomer', 'CompaniesController@editCustomerPost');

Route::get('generarFactura/{id}', 'FacturaController@getGenerarFactura');
Route::get('listarFacturas', 'FacturaController@listarFacturas');
Route::post('crearFactura', 'FacturaController@postGenerarFactura');
Route::get('verFactura/{id}', 'FacturaController@verFactura');
Route::get('descargarFactura/{id}', 'FacturaController@descargarFactura');
Route::get('generarPDF/{id}', 'FacturaController@generarPDF');

Route::get('crearND/{customer_id?}', 'ComprobantesController@crearND');
Route::get('crearNC/{customer_id?}', 'ComprobantesController@crearNC');
Route::get('notaDebito', 'ComprobantesController@listarND');
Route::get('notaCredito', 'ComprobantesController@listarNC');
Route::post('guardarCbte', 'ComprobantesController@guardarCbte');
Route::get('verCbte/{id}', 'ComprobantesController@verCbte');
Route::get('descargarCbte/{id}', 'ComprobantesController@descargarCbte');
Route::get('emitirCbte/{id}', 'ComprobantesController@emitirCbte');
Route::get('eliminarCbte/{id}', 'ComprobantesController@eliminarCbte');

Route::get('generarCbte/{id}', 'ComprobantesController@generarCbte');


Route::get('altaProveedor', 'CompaniesController@addProvider');
Route::get('proveedores', 'CompaniesController@listProviders');

Route::get('crearFacturaCompra/{customer_id?}', 'ComprasController@crearFacturaCompra');
Route::get('compras', 'ComprasController@listarFacturaCompra');
Route::post('guardarFacturaCompra', 'ComprasController@guardarFacturaCompra');
Route::post('aditFacturaCompra', 'ComprasController@postEditFacturaCompra');
Route::get('editarFacturaCompra/{id}', 'ComprasController@getEditFacturaCompra');
Route::get('deleteFactura/{id}', 'ComprasController@deleteFactura');

Route::get('ctacteCompany/{id}', 'CuentaCorrienteController@listarCteCtaEmpresa');
Route::get('ctacte', 'CuentaCorrienteController@listarCteCta');
Route::get('agregarPago/{id}', 'CuentaCorrienteController@agregarPago');
Route::post('newPago', 'CuentaCorrienteController@agregarPagoPost');
Route::get('verPagos/{id}', 'CuentaCorrienteController@verPagos');
Route::get('eliminarPago/{id}', 'CuentaCorrienteController@eliminarPago');
Route::get('addSaldo/{id}', 'CuentaCorrienteController@getaddSaldo');
Route::post('addSaldo', 'CuentaCorrienteController@postaddSaldo');
Route::get('eliminarSaldo/{id}', 'CuentaCorrienteController@eliminarSaldo');

Route::get('ivaVentas', 'ReporteController@ivaVentas');
Route::post('listarIVAventas', 'ReporteController@listarIVAventas');
Route::get('ivaCompras', 'ReporteController@ivaCompras');
Route::post('listarIVAcompras', 'ReporteController@listarIVAcompras');
Route::get('cuentaCorriente', 'ReporteController@listarCtaCte');
Route::get('ventas/{fechas?}', 'ReporteController@ventas');
Route::get('listadoProductoPedidos/{fechas?}', 'ReporteController@listadoProductoPedidos');
Route::get('listadoStock', 'ReporteController@listadoStock');
Route::get('listadoStockTipo', 'ReporteController@listadoStockTipo');


Route::get('listadoCtaCte/{fechas?}', 'ReporteController@pagosCtaCte');
Route::get('listadoCtaCtes/{fechas?}', 'ReporteController@listadoCtaCte');

Route::get('listarRemitos', 'RemitosController@listarRemito');
Route::get('descargarRemito/{id}', 'RemitosController@descargarRemito');
Route::get('generarRemito/{id}', 'RemitosController@getGenerarRemito');
Route::post('crearRemito', 'RemitosController@postGenerarRemito');
Route::get('regenerarremito/{id}', 'RemitosController@ReGenerarRemito');
Route::get('verRemito/{id}', 'RemitosController@verRemito');
Route::get('asignarCorredor/{id?}', 'CompaniesController@listarEmpresas');
Route::post('editAsig', 'CompaniesController@editAsig');
Route::get('vendedoresTDP/{id?}', 'CompaniesController@vendedoresTDP');
Route::post('addCorredor', 'CompaniesController@addCorredor');
Route::get('deleteVendedor/{id}', 'CompaniesController@deleteVendedor');

Route::get('generarPresupuesto/{id}', function($id)
{
    $head = \app\InvoiceHead::where('id_order','=',$id)->first();
    if($head!=null){
    $customer = \app\Cliente::where('id_customer','=',$head->companies_id)->first();
    $lines = \app\InvoiceLine::where('invoice_head_id','=',$head->id)->get();
    $html = view('presupuestos.download')->with('lines',$lines)->with('customer ',$customer)->with('invoice',$head);
    $pdf = \App::make('dompdf');
    $pdf = $pdf->loadHTML($html);
    return $pdf->stream();
}else{
    Session::flash('message', 'Generar remito primero!!');
    return Redirect::to('listarPedidos');
}
});

Route::post('addProduction', function(){

$producto = new \app\Produccion;
$producto->users_id = Auth::user()->id;
$producto->kg = Input::get('cantidad');
$producto->codigo = Input::get('producto_id');
$contador = Input::get('contador') + 1;

$producto->save();

$prod = app\ProductoTDP::find(Input::get('prod_id'));

return view('produccion.add')->with('contador',$contador)->with('prod',$prod)->with('prod_id',Input::get('prod_id'))->with('producto',Input::get('producto_id'));

});


Route::post('viewProdu', function(){


$rango[0] = Input::get('desde');
$rango[1] = Input::get('hasta');
$hasta = Input::get('hasta');
$rango[1] = strtotime ( '+1 day' , strtotime ( $rango[1] ) ) ;
$rango[1] = date ( 'Y-m-d' , $rango[1]);

$productos = \app\Produccion::whereBetween('created_at',$rango)->orderBy('created_at','DESC')->get();

return view('produccion.list')->with('productos',$productos)->with('desde',$rango[0])->with('hasta',$hasta);

});

Route::get('viewProd', function(){

$today = date("Y-m-d");
$rango[0] = date("Y-m-d");   
$rango[1] = strtotime ( '+1 day' , strtotime ( $rango[0] ) ) ;
$rango[1] = date ( 'Y-m-d' , $rango[1]);

$productos = \app\Produccion::whereBetween('created_at',$rango)->orderBy('created_at','DESC')->get();

return view('produccion.list')->with('productos',$productos)->with('hoy',$today);

});

Route::get('expedicion/{id}', function($id)
{
	                $order = \app\Pedido::where('id_order','=',$id)->first();
	                $invoice = \app\InvoiceHead::where('id_order',$order->id_order)->first();
                        $lines = $order->lineas;
                        $html = view('presupuestos.exp')->with('lines',$lines)->with('invoice',$order);
			$pdf = \App::make('dompdf');		
			$pdf = $pdf->loadHTML($html);
			return $pdf->stream();
			
			
					
                        
});
Route::get('reGenerarRemito/{id}', function($id)
{			             
    $order = \app\Pedido::where('id_order','=',$id)->first();
    $remitos = \app\InvoiceHead::where('id_order','=',$id)->get();
    foreach($remitos as $remito){
        $lines = \app\InvoiceLine::where('invoice_head_id','=',$remito->id)->delete();
        $remito->imp_net = $order->total_paid_tax_excl;
        // Ver que onda con tema IVA 10.5% $head->imp_iva_10_5 = Input::get('iva_10_5');
        $remito->imp_iva_21 = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
        $remito->imp_total = $order->total_paid_tax_incl;
        $remito->iva_imp_total = $remito->imp_iva_21;

        foreach($order->lineas as $linea){

            $line = new \app\InvoiceLine;
            $line->subtotal = $linea->total_price_tax_excl;
            $line->quantity = $linea->product_quantity;
            $line->invoice_head_id = $remito->id;
            $line->code = $linea->product_reference;
            $line->name = $linea->product_name;
            if(count($linea->producto->costo)){
                $line->costo = $linea->producto->costo->product_supplier_price_te;
            }
            $line->categories_id = $linea->producto->id_category_default;
            $line->price = $linea->unit_price_tax_excl;
            $line->tipo_iva = 5; // Ver de arreglarlo en un futuro
            $line->imp_iva = $linea->total_price_tax_incl - $linea->total_price_tax_excl;
            $line->save();

        }
        $lines = \app\InvoiceLine::where('invoice_head_id','=',$remito->id)->get();
        $customer = \app\Cliente::where('id_customer','=',$remito->companies_id)->first();
        $pago = $order->payment;
        if($remito->tipo_venta==0){
            $html = view('remitos.download_b')->with('lines',$lines)->with('customer',$customer)->with('invoice',$remito);
            $pdf = \App::make('dompdf');
            $pdf = $pdf->loadHTML($html)->save( 'comprobantes/remito_'.$remito->nro_cbte.'.pdf' );
            $remito->archivo_pdf = 'remito_'.$remito->nro_cbte.'.pdf';
            $ctacte = \app\CtaCte::where('invoice_head_id','=',$remito->id)->first();
            $remito->status = 'A';
            $ctacte->saldo = $remito->imp_net;
            $ctacte->save();
        }

        $remito->save();
    }

    Session::flash('message', 'Remito re-generado correctamente!!');
    return Redirect::to('listarPedidos');
});


Route::post('states/{id}', function($id)
{
	if(Request::ajax()){
	$country_id = $id;
	$states = app\Country::find($country_id)->state()->get();
        return Response::json($states);
	}	
});

Route::get('updateStock/{id}/{quantity}', function($id,$quantity)
{/*
	if(Request::ajax()){
        $producto = app\Stock::where('id_product','=',$id)->first();
        $producto->quantity = $producto->quantity + $quantity;
        $producto->save();
        return $producto->quantity;
	}	*/
    if(Request::ajax()){
        $producto = app\ProductoTDP::where('id_product','=',$id)->first();
        $producto->stock_Fisico = $producto->stock_Fisico + $quantity;
        $producto->save();
        return $producto->stock_Fisico;
    }
});

Route::get('updateCosto/{id}/{quantity}', function($id,$quantity)
{
	if(Request::ajax()){
        $producto = app\Product::where('id_product','=',$id)->first();
        $producto->wholesale_price = $quantity;
        $producto->save();
        return $quantity;
	}	
});



Route::get('changePass', function()
{
	$user = app\User::find(2);
        $user->password = bcrypt("facturando1243");
        $user->save();
    		
});

Route::get('altaventas', function()
{
	$user = new \app\User;

			$user->name = 'Ventas';
			$user->lastname = 'Ventas';
			$user->email = 'ventas2tdp@gmail.com';
			$user->password = bcrypt('ToallasDePapel1243');
			$user->is_active = 1;
			$user->roles_id = 3;
			$user->companies_id = 1;

         $user->save();
    		
});


Route::get('search/autocompleteProvider', 'ComprasController@autocompleteProvider');


Route::post('email/{id}', function($id)
{
	if(Request::ajax()){
	$company = app\Company::find($id);
	$email['mail'] = $company->email;
    return Response::json($email);
	}	
});

Route::post('changeOC', function()
{
	if(Request::ajax()){
	$pedido = app\Pedido::Where('id_order','=',Input::get('oder_modal_id'))->first();
	$pedido->orden_compra = Input::get('orden_compra');
        $pedido->save();
        return "true";
	}	
});


Route::post('sendInvoice', function()
{

if(Request::ajax()){

$data = array('to' => Input::get('to'), 'msj' => Input::get('msj'), 'id' => Input::get('invoice_id'));
	Mail::send('emails.invoice', $data, function($message)
{
    $message->from('factura@toalladepapel.com.ar', 'Administración');
    $message->subject('Comprobante Electrónico');
	$message->to(Input::get('to'));
    $invoice = \app\InvoiceHead::find(Input::get('invoice_id'));
    $file = 'comprobantes/'.$invoice->archivo_pdf;
    $message->attach($file);
});

if(count(Mail::failures()) > 0){
    return Response::json(array(
					'fail' => true
					));
}else{
	return Response::json(array(
					'fail' => false
					));
}


}
});





/* Carga de Base 

Route::get('csv', function(){


    $handle = fopen('magia.csv', "r");
    fgetcsv($handle);
    $i = 0;
    while (($data = fgetcsv($handle,4096,';')) !== FALSE) {

	$producto = new app\ProductoTDP;
        $producto->codigo = utf8_encode($data[0]);
        $producto->descripcion = utf8_encode($data[1]);
        $producto->pesoRef = utf8_encode($data[2]);
        $producto->diametroRef = utf8_encode($data[3]);
        $producto->metrosRef = utf8_encode($data[4]);
        $producto->rollosRef = utf8_encode($data[5]);
        $producto->save();
        $i++;
    }

echo $i;

});


*/














