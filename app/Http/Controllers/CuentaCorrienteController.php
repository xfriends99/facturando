<?php namespace app\Http\Controllers;

use app\Company;
use app\CtaCte;
use app\Pago;
use app\Saldo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Response;
use Session;
use Auth;
use Request;

class CuentaCorrienteController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */

public function listarCteCta(){
	
     $invoices = \app\InvoiceHead::where('status','=','A')
     //->where('cbte_tipo','!=',3)
        ->select(\DB::raw('SUM(cta_ctes.saldo) as sumaSaldo,invoice_head.company_name, invoice_head.companies_id, invoice_head.id as idinv'))
        ->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
        ->groupBy('invoice_head.companies_id')
        ->get();

     $saldos = Saldo::select(\DB::raw('SUM(importe) as saldo, customer_id'))
         ->where('is_active','=',1)->groupBy('customer_id')->get();

    return view('ctacte.listgral')->with('invoices',$invoices)->with('saldos',$saldos);

}

public function listarCteCtaEmpresa(\Illuminate\Http\Request $request, $id = null){

if($id!=null){

$invoices = \app\InvoiceHead::where('status','=','A')
			->select(\DB::raw('invoice_head.id as idfact,cta_ctes.saldo,invoice_head.company_name,invoice_head.imp_total,invoice_head.imp_net, cta_ctes.id, invoice_head.nro_cbte, invoice_head.cbte_tipo, invoice_head.fecha_facturacion'))
			->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
			->where('companies_id','=',$id)
            ->orderBy('fecha_facturacion','DESC')
            ->orderBy('invoice_head.id','desc')->paginate(10);

$total = \app\InvoiceHead::where('status','=','A')
	     //->where('cbte_tipo','!=',3)
			->select(\DB::raw('SUM(cta_ctes.saldo) as sumaSaldo, invoice_head.company_name, invoice_head.companies_id'))			
			->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
			->groupBy('invoice_head.companies_id')	
			->where('invoice_head.companies_id','=',$id)
			->get();

$lastInvoice = \app\InvoiceHead::where('status','=','A')
    ->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
    ->where('invoice_head.companies_id','=',$id)
    ->orderBy('fecha_facturacion', 'desc')
    ->get()->first();

$salddos = \app\Saldo::where('customer_id','=',$id)->where('is_active','=',1)->get();
$page = Input::get('page', null);
if($page!=null && $page!=1){
    $saldos = \app\Saldo::where('customer_id','=',$id)->whereBetween('created_at', [$invoices[count($invoices)-1]->fecha_facturacion,$invoices[0]->fecha_facturacion])
        ->where('is_active','=',1)->orderBy('created_at','DESC')
        ->orderBy('id','DESC')->get();
} else {
    $saldos = \app\Saldo::where('customer_id','=',$id)->where('created_at','>',$invoices[count($invoices)-1]->fecha_facturacion)
        ->where('is_active','=',1)->orderBy('created_at','DESC')
        ->orderBy('id','DESC')->get();
}
$row = collect();
$segundos = 9;
foreach ($invoices as $inv){
    $date = Carbon::createFromFormat('Y-m-d H:i:s', $inv->fecha_facturacion.' 00:00:0'.$segundos);
    $data = ['type'=>'invoice', 'date' => $inv->fecha_facturacion,
        'date_ord' => $date,
        'cbte_tipo' => $inv->cbte_tipo, 'nro_cbte'=> $inv->nro_cbte,
        'imp_net' => $inv->imp_net,
        'saldo' => Pago::where('cta_ctes_id', $inv->id)
            ->where('is_active',1)->sum('pago'),
        'idfact' => $inv->idfact,
        'id' => $inv->id, 'object' => $inv];

    if($inv->cbte_tipo!=3){
        $data['imp_total'] = $inv->imp_total;
    } else {
        $data['imp_total'] = 0;
        $data['saldo'] += $inv->imp_total;
    }

    $row->push($data);
    $companyName = $inv->company_name;
    $segundos--;
}

foreach ($saldos as $inv){
    $row->push(['type'=>'saldo', 'date' => $inv->created_at,
        'date_ord' => $inv->created_at,
        'medios_pagos_id' => $inv->medios_pagos_id, 'importe'=> $inv->importe,
        'otro' => $inv->otro, 'id' => $inv->id,
        'medioPago_tipo' => ($inv->medios_pagos_id!=0) ? $inv->medioPago->tipo : '',
        'object' => $inv]);
}
$imp = 0;
$sald = 0;
/*$rowFinal = collect();
foreach ($row->sortBy('date')->toArray() as $d) {
    if ($d['type'] == "saldo") {
        $sald += $d['importe'];
    } else {
        if ($d['cbte_tipo'] == 99) {
            $imp += $d['imp_net'];
        } else {
            $imp += $d['imp_total'];
        }
        $sald += $d['saldo'];
    }
    $rowFinal->push(array_merge($d, ['saldo_acumulado' => $imp - $sald]));
}*/

return view('ctacte.listctacte')->with('total',$total)->with('saldos', $saldos)
    ->with('invoices',$row->sortByDesc('date_ord')->toArray())
    ->with('invos', $invoices)
    ->with('companyName',$companyName)
    ->with('companyID',$id)
    ->with('last_invoice',$lastInvoice)
    ->with('salddos',$salddos);
}else{
	return Redirect::to('ctacte');
}

}

public function getaddSaldo($id = null){

if($id!=null){
	$mpagos = \app\MedioPago::all();
return view('saldo.addSaldo')->with('id',$id)->with('mpagos',$mpagos);	
}else{
	return Redirect::to('ctacte');
}

}

public function verPagos($id = null){
		
		$invoice =\app\CtaCte::find($id)->facturas->nro_cbte;
		$pagos = \app\Pago::where('cta_ctes_id','=',$id)->where('is_active','=',1)->get();

		return view('ctacte.listpagos')->with('pagos',$pagos)->with('invoice',$invoice);

	
}

public function eliminarPago($id = null){
		
		$pago = \app\Pago::find($id);
		$ctacte = \app\CtaCte::find($pago->cta_ctes_id);
		$ctacte->saldo = $ctacte->saldo + $pago->pago;
		$pago->is_active = 0;

		if($pago->save()){
			$ctacte->save();

		Session::flash('message', 'Pago eliminado correctamente!!');
					return Redirect::to('verPagos/'.$pago->cta_ctes_id);
			
			}else{
				
				return Redirect::to('verPagos/'.$pago->cta_ctes_id);
			}

	
}

public function eliminarSaldo($id  = null){
        $pago = \app\Saldo::find($id);
		$pago->is_active = 0;

		if($pago->save()){
		Session::flash('message', 'Saldo eliminado correctamente!!');
					return Redirect::to('ctacteCompany/'.$pago->customer_id);
			
			}else{
				
				return Redirect::to('ctacteCompany/'.$pago->customer_id);
			}
}

public function agregarPago($id = null){
    $invoice = CtaCte::find($id)->load('facturas');
	if($id!=null){
		$mpagos = \app\MedioPago::all();
		return view('ctacte.addPago')->with('id',$id)->with('mpagos',$mpagos);		
	}else{
		return Redirect::to('ctacte');
	}

}

public function agregarPagoPost(){

	$id = Input::get('ctacte_id');

	if(Input::get('mpago')=='otro'){
		$rules = array(
			'mpago' => 'required',
			'otro' => 'required',
			'monto' => 'required'
			);

	}else{
		$rules = array(
			'mpago' => 'required',
			'monto' => 'required'
			);

	}
	
	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()) {
			return Redirect::to('agregarPago/'.$id)
			->withErrors($validator);
		} else {

			$ctacte = \app\CtaCte::find($id);

			$pago = new \app\Pago;

			$pago->cta_ctes_id = $id;

			if(Input::get('mpago')=='otro'){
				$pago->otro = Input::get('otro');
			 }else{
				$pago->medios_pagos_id = Input::get('mpago');; 					 	
			 }	
			$pago->pago	 = Input::get('monto');
			$pago->users_id = Auth::user()->id;

			if($pago->save()){
					$ctacte->saldo = $ctacte->saldo - $pago->pago;
					 $ctacte->save();
					Session::flash('message', 'Pago ingresado correctamente!!');
					return Redirect::to('ctacteCompany/'.$ctacte->facturas->companies_id);
				}
			}

}

public function postaddSaldo(){

	$id = Input::get('ctacte_id');

	if(Input::get('mpago')=='otro'){
		$rules = array(
			'mpago' => 'required',
			'otro' => 'required',
			'monto' => 'required'
			);

	}else{
		$rules = array(
			'mpago' => 'required',
			'monto' => 'required'
			);

	}
	
	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()) {
			return Redirect::to('addSaldo/'.$id)
			->withErrors($validator);
		} else {

			
			$saldo = new \app\Saldo;

			if(Input::get('mpago')=='otro'){
				$saldo->otro = Input::get('otro');
			 }else{
				$saldo->medios_pagos_id = Input::get('mpago');; 					 	
			 }	
			$saldo->importe	 = Input::get('monto');
			
			$saldo->users_id = Auth::user()->id;
            
            $saldo->customer_id = $id;

            if(Input::get('date_pay',null)!=null){
                try{
                    $dt = Carbon::createFromFormat('d/m/Y',Input::get('date_pay'));
                    $saldo->created_at = $dt;
                } catch(\Exception $e){

                }
            }

			if($saldo->save()){
					Session::flash('message', 'Pago ingresado correctamente!!');
					return Redirect::to('ctacteCompany/'.$id);
				}
			}

}


}
