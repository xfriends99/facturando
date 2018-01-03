<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Response;
use Session;
use Auth;
use Request;

class ComprasController extends Controller {

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

	public function crearFacturaCompra($customer_id = null){

		if($customer_id!=null){

			$customer = \app\Company::find($customer_id);

		}else{
			$customer = null;
		}

		$taxes = \app\TaxType::all();
		$cbtes = \app\TipoCbteProv::all();
		$fiscal_situations = \app\FiscalSituation::all();
		
		return view('compra.new')
		->with('taxes',$taxes)
		->with('cbtes',$cbtes)
		->with('fiscal_situations',$fiscal_situations)
		->with('customer',$customer);
	}

	

	public function autocompleteProvider(){
	$term = Input::get('term');	
	$results = array();
	$companies = \app\Company::where('id','!=',Auth::user()->companies_id)
	->where('companies_type_id', '!=',1)
	->where('company_name', 'LIKE', '%'.$term.'%')
	->take(5)->get();
	
	foreach ($companies as $company)
	{
		
		$results[] = [ 'iva' => $company->fiscal_situation->fisc_situation, 'id' => $company->id, 'tax_id' => $company->tax_id, 'tax_type' => $company->tax_type->type, 'value' => $company->id, 'label' => $company->company_name ];
	}
	
	return Response::json($results);
}


public function guardarFacturaCompra(){

		$id = Input::get('customer_id');

		$rules = array(
			'customer_id' => 'required',
			'fecha_factura' => 'required',
			'cbte_tipo' => 'required',
			'importe_total' => 'required'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('crearFacturaCompra/'.$id)
			->withErrors($validator);
		} else {

			$prov = \app\Company::find($id);

			$fc = new \app\FacturaProveedor;

			$fc->companies_id = $id;
			$fc->fecha_factura = Input::get('fecha_factura');; 			
			$fc->nro_factura = Input::get('nro_factura');
			$fc->importe_neto = Input::get('importe_neto');
			$fc->importe_total = Input::get('importe_total');
			$fc->importe_iva = Input::get('importe_iva');

			$fc->importe_iva_27 = Input::get('importe_iva_27');
			$fc->importe_iva_10_5 = Input::get('importe_iva_10');
			$fc->perc_iva = Input::get('perc_iva');
			$fc->importe_neto_no_gravado = Input::get('imp_no_grabado');
			$fc->tipo_cbte_prov_id = Input::get('cbte_tipo');	

			$fc->nombre_proveedor = $prov->company_name;
			$fc->cuit = $prov->tax_id;
			$fc->tipo_doc = $prov->tax_type->type;

			if($fc->save()){
				
					Session::flash('message', 'Factura de Compra creada correctamente!!');
					return Redirect::to('compras');
				}
			}
}



public function listarFacturaCompra(){
	
		$invoices = \app\FacturaProveedor::where('is_active','=',1)
		->orderBy('fecha_factura','DESC')
		->paginate(10);

		return view('compra.list')->with('invoices',$invoices);

	
}

public function getEditFacturaCompra($id = null){

	$invoice = \app\FacturaProveedor::find($id);
	$cbtes = \app\TipoCbteProv::all();

	if($invoice!=null){

		return view('compra.edit')->with('invoice',$invoice)->with('cbtes',$cbtes);

	}else{
		return view('home');
	}



}


public function postEditFacturaCompra(){

		$id = Input::get('fc');

		$rules = array(
			'fecha_factura' => 'required',
			'cbte_tipo' => 'required',
			'importe_total' => 'required'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('editarFacturaCompra/'.$id)
			->withErrors($validator);
		} else {

			$fc =  \app\FacturaProveedor::find($id);

			$fc->fecha_factura = Input::get('fecha_factura');; 			
			$fc->nro_factura = Input::get('nro_factura');
			$fc->importe_neto = Input::get('importe_neto');
			$fc->importe_total = Input::get('importe_total');
			$fc->importe_iva = Input::get('importe_iva');
			$fc->importe_iva_27 = Input::get('importe_iva_27');
			$fc->importe_iva_10_5 = Input::get('importe_iva_10');
			$fc->perc_iva = Input::get('perc_iva');
			$fc->importe_neto_no_gravado = Input::get('imp_no_grabado');
			$fc->tipo_cbte_prov_id = Input::get('cbte_tipo');	
			

			if($fc->save()){
				
					Session::flash('message', 'Factura de Compra editada correctamente!!');
					return Redirect::to('editarFacturaCompra/'.$id);
				}
			}

}

public function deleteFactura($id = null){

		$fc = \app\FacturaProveedor::find($id);
				
		if($fc!=null){

			if(Auth::user()->roles_id==1){ 
				$fc->is_active = 0;
				if($fc->save()) {
					Session::flash('message', 'Factura eliminada correctamente!!');
					return Redirect::to('compras');
				}
			}else{
				return view('home');
			}

		}else{
			return view('home');
		}

}


}
