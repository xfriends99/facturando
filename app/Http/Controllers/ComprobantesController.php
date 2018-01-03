<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Request;
use Session;
use Response;
use Auth;


class ComprobantesController extends Controller{


	public function __construct()
	{
		$this->middleware('auth');
	}


	    public function crearND(){

		$taxes = \app\TaxType::all();
		$fiscal_situations = \app\FiscalSituation::all();
		$nro_tipo_cbte = 2;
		$tipo_cbte = "Nota de Debito";

		return view('cbtes.new')
		->with('nro_tipo_cbte',$nro_tipo_cbte)
		->with('tipo_cbte',$tipo_cbte)
		->with('taxes',$taxes)
		->with('fiscal_situations',$fiscal_situations);
	}

	public function crearNC(){

	
		$taxes = \app\TaxType::all();
		$fiscal_situations = \app\FiscalSituation::all();
		$nro_tipo_cbte = 3;
		$tipo_cbte = "Nota de Credito";
		
		return view('cbtes.new')
		->with('nro_tipo_cbte',$nro_tipo_cbte)
		->with('tipo_cbte',$tipo_cbte)
		->with('taxes',$taxes)
		->with('fiscal_situations',$fiscal_situations);
	}

	


public function guardarCbte(){

		$rules = array(
				
				'raz_social' => 'required',
				'cuit' => 'required',
                                'direccion' => 'required',
                                'fecha' => 'required',
				'subtotal' => 'required',
				'total' => 'required',
			);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			if(Input::get('tipo_cbte')==2){
			return Redirect::to('crearND/')
			->withErrors($validator);	
			}else{
			return Redirect::to('crearNC/')
			->withErrors($validator);	
			}			
		} else {

		        $head = new \app\InvoiceHead;

			   $direccion = Input::get('direccion');
				$head->fecha_facturacion = Input::get('fecha');
				$head->company_name = Input::get('raz_social');
				$head->tax_id =  preg_replace('/[^0-9.]/', '', Input::get('cuit'));
				$head->fisc_situation = 1;
				$head->tax_id_type = 1;
				$head->address = $direccion;
				$head->users_id = Auth::user()->id;
				
				$customer = \app\Cliente::where('tax_number','=',$head->tax_id)->first();
				$head->companies_id = $customer->id_customer;
				$head->concepto = 1;
				$head->imp_net = Input::get('subtotal');
				$head->imp_total = Input::get('total');
				$head->imp_tot_conc = 0;
				$head->imp_op_ex = 0;
				$head->imp_trib = 0;
				$head->imp_iva_21 = Input::get('iva_21');
				$head->mon_cotiz = 'PES';
				$head->mon_id = 1;
				$head->status = 'G'; // Guardado.
                             
		
		                $head->iva_imp_total =  $head->imp_iva_21;

		                $head->pto_vta = 5; // dato a obtener de acuerdo al cliente
		
		$tc = Input::get('tipo_cbte');
		$tipo_cbte  = \app\RelFSCbtes::where('fiscal_situation_id','=',1)
		->where('cbte','=',$tc)->first()->tipo_cbtes_id;
		$head->cbte_tipo = $tipo_cbte; 

		$head->save();

		for($j=0;Input::get('cant_lineas')>$j;$j++){

		
			$line = new \app\InvoiceLine;
			$line->subtotal = Input::get('subtotal_'.$j);
			$line->quantity = Input::get('cantidad_'.$j);
			$line->invoice_head_id = $head->id;
			$line->code = Input::get('code_'.$j);
			$line->name = Input::get('product_'.$j);
			$line->price = Input::get('punitario_'.$j);
			$line->tipo_iva = 5;
			$line->imp_iva = Input::get('imp_iva_'.$j);

			$line->save();
		}

		$generar = Input::get('generar');

		if(!empty($generar)){
			return $this->emitirCbte($head->id);
		}

	}


}

	public function listarND()
	{

		$invoices = \app\InvoiceHead::where('status','!=','D')
		->where('cbte_tipo','=','2')
		->orWhere('cbte_tipo','=','7')
		->orderBy('fecha_facturacion','DESC')
		->paginate(10);

		$tipo_cbtes = "Notas de Debito";
		$tipo_cbte = "Nota de Debito";
		$url = "crearND";

		return view('cbtes.list')->with('invoices',$invoices)
		->with('tipo_cbtes',$tipo_cbtes)
		->with('tipo_cbte',$tipo_cbte)
		->with('url',$url);

	}

	public function listarNC()
	{

		$invoices = \app\InvoiceHead::where('status','!=','D')
		->where('cbte_tipo','=','3')		
		->orWhere('cbte_tipo','=','8')
		->orderBy('fecha_facturacion','DESC')
		->paginate(10);


		$tipo_cbtes = "Notas de Credito";
		$tipo_cbte = "Nota de Credito";
		$url = "crearNC";

		return view('cbtes.list')->with('invoices',$invoices)
		->with('tipo_cbtes',$tipo_cbtes)
		->with('tipo_cbte',$tipo_cbte)
		->with('url',$url);
	}

	
	public function emitirCbte($id = null){

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
			if($invoice->cbte_tipo==2 || $invoice->cbte_tipo==7){
				$ctacte->saldo = $invoice->imp_total;		
			}else{
				$ctacte->saldo = -($invoice->imp_total);
			}
			$ctacte->save();
			if($invoice->cbte_tipo==2 || $invoice->cbte_tipo==7){
				$tipo_comprobante = "Nota de Debito";
			}else{
				$tipo_comprobante = "Nota de Credito";
			}
			$html = view('cbtes.download')->with('tipo_comprobante',$tipo_comprobante)->with('lines',$lines)->with('invoice',$invoice);
			$pdf = \App::make('dompdf');
			if($invoice->cbte_tipo==2 || $invoice->cbte_tipo==7){
			$pdf = $pdf->loadHTML($html)->save( 'comprobantes/notaDebito-'.$invoice->nro_cbte.'.pdf' );
	    	$invoice->archivo_pdf = 'notaDebito-'.$invoice->nro_cbte.'.pdf';
	    	}else{
			$pdf = $pdf->loadHTML($html)->save( 'comprobantes/notaCredito-'.$invoice->nro_cbte.'.pdf' );
			$invoice->archivo_pdf = 'notaCredito-'.$invoice->nro_cbte.'.pdf';	
				}
			
	    	$invoice->save();
	    }
	    
		return $this->verCbte($invoice->id);


	}	

}



	public function verCbte($id = null){

		if($id!=null) {

		$invoice = \app\InvoiceHead::find($id);

		$lines = \app\InvoiceLine::where('invoice_head_id','=',$invoice->id)->get();

		return view('cbtes.invoice')->with('lines',$lines)
		->with('invoice',$invoice);

		}
	}

	public function descargarCbte($id = null){
		
		if($id!=null) {

		$invoice = \app\InvoiceHead::find($id);
		
		$file= 'comprobantes/'.$invoice->archivo_pdf;

        $headers = array(
              'Content-Type: application/pdf',
            );

      return Response::download($file, $invoice->archivo_pdf , $headers);	

			}
	}


  public function eliminarCbte($id = null){
		
		if($id!=null) {

		$invoice = \app\InvoiceHead::find($id);
		$invoice->status = 'D'; 

		if($invoice->save()) {
					Session::flash('message', 'Factura eliminada correctamente!!');
					if($invoice->cbte_tipo==2){
					return Redirect::to('notaDebito');	
					}else{					
					return Redirect::to('notaCredito');	
					}
					
			}else{
				
				if($invoice->cbte_tipo==2 || $invoice->cbte_tipo==7){
					return Redirect::to('notaDebito');	
					}else{					
					return Redirect::to('notaCredito');	
					}
			}
			}else{

				if($invoice->cbte_tipo==2 || $invoice->cbte_tipo==7){
					return Redirect::to('notaDebito');	
					}else{					
					return Redirect::to('notaCredito');	
					}

			}
	

	}  	
  
 public function generarCbte($id = null){

                $invoice = \app\InvoiceHead::find($id);

		$lines = \app\InvoiceLine::where('invoice_head_id','=',$invoice->id)->get();

		if($invoice->status=='A'){

                if($invoice->cbte_tipo==2 || $invoice->cbte_tipo==7){
				$tipo_comprobante = "Nota de Debito";
			}else{
				$tipo_comprobante = "Nota de Credito";
			}
	        $html = view('cbtes.download')->with('tipo_comprobante',$tipo_comprobante)->with('lines',$lines)->with('invoice',$invoice);
	        $pdf = \App::make('dompdf');
	        if($invoice->cbte_tipo==2 || $invoice->cbte_tipo==7){
		$pdf = $pdf->loadHTML($html)->save( 'comprobantes/notaDebito-'.$invoice->nro_cbte.'.pdf' );
	    	$invoice->archivo_pdf = 'notaDebito-'.$invoice->nro_cbte.'.pdf';
	    	}else{
		$pdf = $pdf->loadHTML($html)->save( 'comprobantes/notaCredito-'.$invoice->nro_cbte.'.pdf' );
		$invoice->archivo_pdf = 'notaCredito-'.$invoice->nro_cbte.'.pdf';	
		}
	        $invoice->save();		
		}
}
}