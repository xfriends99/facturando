<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Request;
use Session;
use Response;
use Auth;


class FacturaController extends Controller{


	public function __construct()
	{
		$this->middleware('auth');
	}


         public function info(){
           
          ini_set("soap.wsdl_cache_enabled", "0");
		if (!file_exists(env('CERT'))) {exit("Failed to open ".env('CERT')."\n");}
		if (!file_exists(env('PRIVATEKEY'))) {exit("Failed to open ".env('PRIVATEKEY')."\n");}
		if (!file_exists(env('WSDL_WSAA'))) {exit("Failed to open ".env('WSDL_WSAA')."\n");}
		if (!file_exists(env('WSDL_WSFEX'))) {exit("Failed to open ".env('WSDL_WSFEX')."\n");}


		$SERVICE=1;

		\myFunctions::CreateTRA($SERVICE);
		$CMS= \myFunctions::SignTRA();
		$TA= \myFunctions::CallWSAA($CMS);

		if (!file_put_contents("TA.xml", $TA)) {exit();}

		if (!file_exists(env('TA'))) {exit("Failed to open ".env('TA')."\n");}

		$client= \myFunctions::get_soap();

		$TA=simplexml_load_file(env('TA'));

		$token=$TA->credentials->token;

		$sign=$TA->credentials->sign;

                $i = 1810;
                while($i<1816){
                $params['Auth']['Token'] = $token;
                $params['Auth']['Sign'] = $sign;
                $params['Auth']['Cuit'] = env('CUIT');
                $params['FeCompConsReq']['CbteTipo']=1;
                $params['FeCompConsReq']['CbteNro']=$i;
                $params['FeCompConsReq']['PtoVta']= env('PV');
                $results= $client->FECompConsultar($params);
                echo date('Y-m-d',strtotime($results->FECompConsultarResult->ResultGet->CbteFch)). ' ' . $results->FECompConsultarResult->ResultGet->CbteHasta . ' ' . $results->FECompConsultarResult->ResultGet->CodAutorizacion . ' ' . date('Y-m-d',strtotime($results->FECompConsultarResult->ResultGet->FchVto)). ' ' .  $results->FECompConsultarResult->ResultGet->DocNro . ' ' . $results->FECompConsultarResult->ResultGet->ImpTotal . ' ' . $results->FECompConsultarResult->ResultGet->ImpNeto . ' ' . $results->FECompConsultarResult->ResultGet->ImpIVA . '</br>';
                 $i++;
                }


       }
 	public function getGenerarFactura($id = null)
	{
		$taxes = \app\TaxType::all();
		$fiscal_situations = \app\FiscalSituation::all();
                $order = \app\Pedido::where('id_order','=',$id)->first();
                
                $cliente= \app\Cliente::where('id_customer','=',$order->id_customer)->first();
                
                if($cliente!=null){
                $customer = $cliente->id_customer;
                }else{
                $customer = null;
                }
                if($customer == null){
		return view('invoice.generarFactura')
		->with('taxes',$taxes)
		->with('order',$order)
		->with('nro_orden',$id)
		->with('fiscal_situations',$fiscal_situations);

                }else{
                return $this->postGenerarFactura($order->id_order);
                }

	}

        public function postGenerarFactura($id = null){

                                if($id==null){
		                $order = \app\Pedido::where('id_order','=',Input::get('nro_orden'))->first();		          	
		          	$customer = new \app\Cliente;
		          	$customer->id_customer = $order->id_customer;
		          	$customer->fisc_situation = Input::get('tax_type');
		          	$customer->tax_number = Input::get('tax_number');
		          	$customer->tax_id_type = Input::get('fiscal_sit');
                                $customer->save();
		          	}else{
                                $order = \app\Pedido::where('id_order','=',$id)->first();
                                $customer = \app\Cliente::where('id_customer','=',$order->id_customer)->first();
                                }

			      $head = new \app\InvoiceHead;

			        $direccion = "";
			        $direccion .= $order->direccion_factura->address1. ', ';
	                        $direccion .= $order->direccion_factura->city. ' ('. $order->direccion_factura->postcode. ')' .   ', ';
			        $direccion .= $order->direccion_factura->state->name. ', ';
			        $direccion .= 'Argentina.';

				$head->fecha_facturacion = date("Y-m-d");
				$head->id_order = $order->id_order;
                                $head->concepto = 1;
                                $head->orden_compra = $order->orden_compra;
				$head->company_name = $order->direccion_factura->company;
				$head->tipo_venta = 1;
				$head->tax_id = preg_replace('/[^0-9.]/', '', $customer->tax_number);
				$head->fisc_situation = $customer->fisc_situation; 
				$head->tax_id_type = $customer->tax_id_type; 
				$head->address = $direccion;
				$head->users_id = Auth::user()->id; 
				$head->companies_id = $order->id_customer;
				$head->status = 'G'; // Guardado.
                                $head->tipo_pago = $order->payment;
                                $head->mon_cotiz = 'PES';
				$head->mon_id = 1;
							
			        $tipo_cbte  = \app\RelFSCbtes::where('fiscal_situation_id','=',$customer->fisc_situation)
->where('cbte','=',1)->first()->tipo_cbtes_id;
		                $head->cbte_tipo = $tipo_cbte;
				
                                $head->imp_tot_conc = 0;
				$head->imp_op_ex = 0;
				$head->imp_trib = 0;
		
				$head->save();
                                $subtotal = 0;
			foreach($order->lineas as $linea){
                                
                                
				$line = new \app\InvoiceLine;
				$line->subtotal = $linea->product_quantity * number_format($linea->unit_price_tax_excl, 2, '.', '');
				$line->quantity = $linea->product_quantity;
				$line->invoice_head_id = $head->id;
                                $line->product_id = $linea->product_id;
				$line->code = $linea->product_reference;
				$line->name = $linea->product_name;
                                $line->costo = $linea->producto->wholesale_price;
                                $line->categories_id = $linea->producto->id_category_default;
				$line->price = number_format($linea->unit_price_tax_excl, 2, '.', '');
				$line->tipo_iva = 5; // Ver de arreglarlo en un futuro
				$line->imp_iva = ($line->subtotal * 1.21) - $line->subtotal;
				$line->save();
                                $subtotal += $line->subtotal;
			}

                                $head->imp_net = $subtotal;
				// Ver que onda con tema IVA 10.5% $head->imp_iva_10_5 = Input::get('iva_10_5');
				$head->imp_total = $subtotal * 1.21;                    
                                $head->imp_iva_21 = $head->imp_total - $head->imp_net;	
                                $head->iva_imp_total = $head->imp_iva_21;

                                $head->save();

			return $this->emitirFactura($head->id);		
		}

public function listarFacturas()
{

	$invoices = \app\InvoiceHead::where('status','!=','D')
	->where('cbte_tipo','=','1')
	->orWhere('cbte_tipo','=','6')
	->orderBy('nro_cbte','DESC')
	->paginate(10);

	return view('invoice.list')->with('invoices',$invoices);

}

public function emitirFactura($id = null){

	if($id!=null) {

		ini_set("soap.wsdl_cache_enabled", "0");
		if (!file_exists(env('CERT'))) {exit("Failed to open ".env('CERT')."\n");}
		if (!file_exists(env('PRIVATEKEY'))) {exit("Failed to open ".env('PRIVATEKEY')."\n");}
		if (!file_exists(env('WSDL_WSAA'))) {exit("Failed to open ".env('WSDL_WSAA')."\n");}
		if (!file_exists(env('WSDL_WSFEX'))) {exit("Failed to open ".env('WSDL_WSFEX')."\n");}


		$SERVICE=1;

		\myFunctions::CreateTRA($SERVICE);
		$CMS= \myFunctions::SignTRA();
		$TA= \myFunctions::CallWSAA($CMS);

		if (!file_put_contents("TA.xml", $TA)) {exit();}

		if (!file_exists(env('TA'))) {exit("Failed to open ".env('TA')."\n");}

		$client= \myFunctions::get_soap();

		$TA=simplexml_load_file(env('TA'));

		$token=$TA->credentials->token;

		$sign=$TA->credentials->sign;

		file_put_contents("functions.txt",print_r($client->__getFunctions(),TRUE));
		file_put_contents("types.txt",print_r($client->__getTypes(),TRUE));

		$MAX_CBTE= \myFunctions::FECompTotXRequest($client, $token, $sign, env('CUIT'));
		$PV=env('PV');
		$CANT=1;
		
		$invoice = \myFunctions::EmitirFC($client, $token, $sign, env('CUIT'), $PV, 1, $id);

		$lines = \app\InvoiceLine::where('invoice_head_id','=',$invoice->id)->get();

		if($invoice->status=='A'){
                $ctacte = new \app\CtaCte;	  
		$ctacte->invoice_head_id = $invoice->id;
		$ctacte->saldo = $invoice->imp_total;
	        $ctacte->save();
	        $html = view('invoice.download')->with('lines',$lines)->with('invoice',$invoice);
	        $pdf = \App::make('dompdf');
		    $pdf = $pdf->loadHTML($html)->save( 'comprobantes/factura-'.$invoice->nro_cbte.'-'.$invoice->id.'.pdf' );
      	 $invoice->archivo_pdf = 'factura-'.$invoice->nro_cbte.'-'.$invoice->id.'.pdf';
		$invoice->save();
           		}
                return Redirect::to('listarFacturas');						
}

}


public function verFactura($id = null){

	if($id!=null) {

		$invoice = \app\InvoiceHead::find($id);

		$lines = \app\InvoiceLine::where('invoice_head_id','=',$invoice->id)->get();
               
                $html = view('invoice.invoice')->with('lines',$lines)->with('invoice',$invoice);
                
                return $html;

	}
}

public function descargarFactura($id = null){


	if($id!=null) {

		$invoice = \app\InvoiceHead::find($id);

		$file= 'comprobantes/'.$invoice->archivo_pdf;

		$headers = array(
			'Content-Type: application/pdf',
			);

		return Response::download($file, $invoice->archivo_pdf , $headers);	
	}
}

public function generarPDF($id = null){

                $invoice = \app\InvoiceHead::find($id);

		$lines = \app\InvoiceLine::where('invoice_head_id','=',$invoice->id)->get();

		if($invoice->status=='A'){
	        $html = view('invoice.download')->with('lines',$lines)->with('invoice',$invoice);
	        $pdf = \App::make('dompdf');
	        $pdf = $pdf->loadHTML($html)->save( 'comprobantes/factura-'.$invoice->nro_cbte.'-'.$invoice->id.'.pdf' );
      		$invoice->archivo_pdf = 'factura-'.$invoice->nro_cbte.'-'.$invoice->id.'.pdf';
	        $invoice->save();		
		}
}


}