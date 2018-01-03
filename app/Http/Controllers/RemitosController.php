<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Request;
use Session;
use Response;
use Auth;
use File;


class RemitosController extends Controller{


	public function __construct()
	{
		$this->middleware('auth');
	}



public function verRemito($id = null){

	if($id!=null) {

		$invoice = \app\InvoiceHead::find($id);

		$lines = \app\InvoiceLine::where('invoice_head_id','=',$invoice->id)->get();
               
                $html = view('remitos.remito')->with('lines',$lines)->with('invoice',$invoice);
                
                return $html;

	}
}
        public function getGenerarRemito($id = null)
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

                $corredores = \app\Corredor::all();
                
		return view('remitos.nroRemito')
		->with('taxes',$taxes)
		->with('customer',$customer)
		->with('order',$order)
		->with('cliente',$cliente)
		->with('nro_orden',$id)
                ->with('corredores',$corredores)
		->with('fiscal_situations',$fiscal_situations);

	}

	public function postGenerarRemito(){

		                $order = \app\Pedido::where('id_order','=',Input::get('nro_orden'))->first();
		          	
		          	if(Input::get('customer')!=null){
		          	$customer = \app\Cliente::where('id_customer','=',Input::get('customer'))->first();
                                $customer->transporte= Input::get('transporte');
                                $customer->telefono= Input::get('telefono');
                                $customer->direccion= Input::get('direccion');
                                $customer->corredores_id= Input::get('corredor');
                                $customer->save();
		          	}else{	      	
		          	$customer = new \app\Cliente;
		          	$customer->id_customer = $order->id_customer;
		          	$customer->fisc_situation = Input::get('tax_type');
		          	$customer->tax_number = Input::get('tax_number');
		          	$customer->tax_id_type = Input::get('fiscal_sit');
                                $customer->transporte= Input::get('transporte');
                                $customer->telefono= Input::get('telefono');
                                $customer->direccion= Input::get('direccion');
                                $customer->corredores_id= Input::get('corredor');
		          	$customer->save();
		          	}
		          	
				$head = new \app\InvoiceHead;

				$direccion = "";
				$direccion .= $order->direccion_factura->address1. ', ';
				$direccion .= $order->direccion_factura->city. ' ('. $order->direccion_factura->postcode. ')' .   ', ';
				$direccion .= $order->direccion_factura->state->name. ', ';
				$direccion .= 'Argentina.';

				$head->fecha_facturacion = date("Y-m-d");
				$head->id_order = $order->id_order;
				$head->company_name = $order->direccion_factura->company;
				if($order->current_state==16){
                                $remito = \app\InvoiceHead::where('id_order','=',$head->id_order)->first();
                                if($remito==null){
                              	
				$head->tipo_venta = 0;
                                }else{
                                
                                $head->tipo_venta = 1;
                                }
				}else{
				
				$head->tipo_venta = 1;
				}
				$head->tax_id = $customer->tax_number;
				$head->fisc_situation = $customer->fisc_situation; // Revisar si rompe o no, pero tengo que registrar si es o no Resp. Insc.
				$head->tax_id_type = $customer->tax_id_type; 
				$head->address = $direccion;
				$head->users_id = Auth::user()->id; 
				$head->companies_id = $order->id_customer;
				$head->status = 'G'; // Guardado.
                                $head->tipo_pago = $order->payment;
				$head->cbte_tipo = 99; // dato a obtener de acuerdo al cliente
				$head->imp_net = $order->total_paid_tax_excl;
				// Ver que onda con tema IVA 10.5% $head->imp_iva_10_5 = Input::get('iva_10_5');
				$head->imp_iva_21 = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
				$head->imp_total = $order->total_paid_tax_incl;
				
				$head->nro_cbte = Input::get('nro_remito');
                                $head->iva_imp_total = $head->imp_iva_21;
		
				$head->save();

			foreach($order->lineas as $linea){

				$line = new \app\InvoiceLine;
				$line->subtotal = $linea->total_price_tax_excl;
				$line->quantity = $linea->product_quantity;
				$line->invoice_head_id = $head->id;
                                $line->product_id = $linea->product_id;
				$line->code = $linea->product_reference;
				$line->name = $linea->product_name;
                                $line->costo = $linea->producto->wholesale_price;
                                $line->categories_id = $linea->producto->id_category_default;
				$line->price = $linea->unit_price_tax_excl;
				$line->tipo_iva = 5; // Ver de arreglarlo en un futuro
				$line->imp_iva = $linea->total_price_tax_incl - $linea->total_price_tax_excl;
				$line->save();
			}

			$lines = \app\InvoiceLine::where('invoice_head_id','=',$head->id)->get();
			  $pago = $order->payment;
			if($head->tipo_venta==0){
                        $html = view('remitos.download_b')->with('lines',$lines)->with('customer',$customer)->with('invoice',$head);
			$pdf = \App::make('dompdf');
			$pdf = $pdf->loadHTML($html)->save( 'comprobantes/remito_'.$head->nro_cbte.'.pdf' );
			$head->archivo_pdf = 'remito_'.$head->nro_cbte.'.pdf';
                        $ctacte = new \app\CtaCte;	
                        $head->status = 'A';  
			$ctacte->invoice_head_id = $head->id;
			$ctacte->saldo = $head->imp_net;
			$ctacte->save();
			}else{
                        $html = view('remitos.download_a')->with('lines',$lines)->with('customer',$customer)->with('invoice',$head);
			$pdf = \App::make('dompdf');
			$pdf = $pdf->loadHTML($html)->save( 'comprobantes/remito-'.$head->nro_cbte.'.pdf' );
			$head->archivo_pdf = 'remito-'.$head->nro_cbte.'.pdf';
			}			
                        $head->save();	
		        return Redirect::to('listarRemitos');						
			
		}


public function listarRemito()
{

	$invoices = \app\InvoiceHead::where('status','!=','D')
	->where('cbte_tipo','=','99')
	->orderBy('fecha_facturacion','DESC')
	->paginate(10);

	return view('remitos.list')->with('invoices',$invoices);

}

public function ReGenerarRemito($id = null)
{

                        $head = \app\InvoiceHead::find($id);
                        $customer = \app\Cliente::where('id_customer','=',$head->companies_id)->first();
                        $lines = \app\InvoiceLine::where('invoice_head_id','=',$head->id)->get();
			if($head->tipo_venta==0){
                        $html = view('remitos.download_b')->with('lines',$lines)->with('customer ',$customer)->with('invoice',$head);
			$pdf = \App::make('dompdf');		
			$pdf = $pdf->loadHTML($html)->save( 'comprobantes/remito_'.$head->nro_cbte.'.pdf' );
			$head->archivo_pdf = 'remito_'.$head->nro_cbte.'.pdf';
			}else{
                        $html = view('remitos.download_a')->with('lines',$lines)->with('customer',$customer)->with('invoice',$head);
			$pdf = \App::make('dompdf');
			$pdf = $pdf->loadHTML($html)->save( 'comprobantes/remito-'.$head->nro_cbte.'.pdf' );
			$head->archivo_pdf = 'remito-'.$head->nro_cbte.'.pdf';
			}			
                        $head->save();
                        	
		        return $html;

}

public function descargarRemito($id = null){


	if($id!=null) {

		$invoice = \app\InvoiceHead::find($id);
		
		$file= 'comprobantes/'.$invoice->archivo_pdf;

		$headers = array(
			'Content-Type: application/pdf',
			);

		return Response::download($file, $invoice->archivo_pdf , $headers);	

	}
}



}