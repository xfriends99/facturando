<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Request;
use Session;
use Auth;

class ReporteController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}


public function ivaVentas(){

	$invoices = null;
	return view('report.ventas')->with('invoices',$invoices);
}

public function listarIVAventas(){

		$rules = array(
			'desde' => 'required',
			'hasta' => 'required'
			);

	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()) {
			return Redirect::to('ivaVentas')
			->withErrors($validator);
		} else {

			$rango[0] = Input::get('desde');
			$rango[1] = Input::get('hasta');

			$invoices = \app\InvoiceHead::where('status','=','A')
                        ->where('cbte_tipo','!=','99')
			->whereBetween('fecha_facturacion',$rango)->get();

			return view('report.ventas')->with('invoices',$invoices);

		}

}

public function ivaCompras(){

	$invoices = null;
	return view('report.compras')->with('invoices',$invoices);
}

public function listarIVAcompras(){

		$rules = array(
			'desde' => 'required',
			'hasta' => 'required'
			);

	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()) {
			return Redirect::to('ivaCompras')
			->withErrors($validator);
		} else {

			$rango[0] = Input::get('desde');
			$rango[1] = Input::get('hasta');

			$invoices = \app\FacturaProveedor::where('is_active','=',1)
			->whereBetween('fecha_factura',$rango)->get();

			return view('report.compras')->with('invoices',$invoices);

		}

}

public function cuentaCorriente(){

	$invoices = null;
	return view('report.ctacte')->with('invoices',$invoices);
}

public function listarCtaCte(){


			$invoices = \app\InvoiceHead::where('status','=','A')
			//->where('cbte_tipo','!=',3)
			->select(\DB::raw('SUM(cta_ctes.saldo) as sumaSaldo, invoice_head.company_name, invoice_head.companies_id'))			
			->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
			->groupBy('invoice_head.companies_id')
			->get();
			return view('report.ctacte')->with('invoices',$invoices);

	}

public function ventas(){

        if(Input::has('desde') && Input::has('hasta')){
        $rango[0] = Input::get('desde');
	$rango[1] = Input::get('hasta');

        $invoices = \app\InvoiceHead::whereBetween('fecha_facturacion',$rango)
        ->orderBy('fecha_facturacion','DESC')
        ->get();
         return view('report.reporte_ventas')->with('invoices',$invoices)->with('desde',Input::get('desde'))->with('hasta',Input::get('hasta'));
       }else{
        $hoy = date("Y-m-d");   
	$invoices = \app\InvoiceHead::where('fecha_facturacion','=',$hoy)->orderBy('fecha_facturacion','DESC')->get();
        return view('report.reporte_ventas')->with('invoices',$invoices)->with('hoy',$hoy);
     
     }	
   }

public function pagosCtaCte(){

	 if(Input::has('desde') && Input::has('hasta')){
	      $rango[0] = Input::get('desde');
	      $rango[1] = Input::get('hasta');
	      
	   $movimientos = \app\Pago::whereBetween('created_at',$rango)->where('is_active','=',1)->get();
	   
	   return view('report.reporte_pago_ctacte')->with('movimientos',$movimientos)->with('desde',Input::get('desde'))->with('hasta',Input::get('hasta'));
	 }else{
	     $hoy = date("Y-m-d");
	     $movimientos = null;
	return view('report.reporte_pago_ctacte')->with('hoy',$hoy)->with('movimientos',$movimientos);
	 }
}


}
