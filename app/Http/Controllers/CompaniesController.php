<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Response;
use Session;
use Auth;
use Request;

class CompaniesController extends Controller {

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
	public function addCustomer()
	{
		$taxes = \app\TaxType::all();
		$countries = \app\Country::all();
		$fiscal_situations = \app\FiscalSituation::all();

		return view('company.add')
		->with('taxes',$taxes)
		->with('countries',$countries)
		->with('fiscal_situations',$fiscal_situations);

	}

        public function exportCustomers(){
        
                $clientes = \app\Cliente::all();
                return view('report.clientes')
		->with('clientes',$clientes);
        }

        public function listarEmpresas($id = null){

           if($id == null){
           $companies = \app\Cliente::all();
           
           return view('company.listEmp')->with('companies',$companies);
          }else{
            $company = \app\Cliente::find($id);
            $corredores = \app\Corredor::all();
            return view('company.corredorEmp')->with('company',$company)->with('corredores',$corredores);
          }

       }

       public function editAsig(){

                        $customer = \app\Cliente::find(Input::get('company_id'));
			$customer->corredores_id = Input::get('corr_id');
                        if($customer->save()){
                         Session::flash('message', 'Cliente modificado correctamente!!');
			 return Redirect::to('asignarCorredor/'.$customer->id_customer);
                        }
        }
        
        public function vendedoresTDP($id = null){
            if($id == null){
            $corredores = \app\Corredor::where('is_active','=',1)->get();
            $vendedor = null;
             return view('corredores.list')->with('corredores',$corredores)->with('vendedor',$vendedor);
            
            }else{
            
            $corredores = \app\Corredor::where('is_active','=',1)->get();
            $vendedor =  \app\Corredor::find($id);
             return view('corredores.list')->with('corredores',$corredores)->with('vendedor',$vendedor);    
                
            }
        }

	public function addProvider()
	{
		$taxes = \app\TaxType::all();
		$fiscal_situations = \app\FiscalSituation::all();
		return view('company.addP')->with('taxes',$taxes)->with('fiscal_situations',$fiscal_situations);

	}
	
	public function addCorredor(){
	        
	        if(empty(Input::get('id'))){
	    	$corredor = new \app\Corredor;
	        }else{
	        $corredor = \app\Corredor::find(Input::get('id')); 
	        }
	    	$corredor->nombre = Input::get('nombre');
			$corredor->mail = Input::get('mail');
			if(!empty(Input::get('clave'))){
			$corredor->clave = Input::get('clave');
			}
			$corredor->save();
			 if(empty(Input::get('id'))){
				Session::flash('message', 'Corredor creado correctamente!!');
			 }else{
			    Session::flash('message', 'Corredor editado correctamente!!');
			 }
					return Redirect::to('vendedoresTDP');
	    
	}
	
	public function deleteVendedor($id = null){
	    
	    	$corredor = \app\Corredor::find($id);
	    	$corredor->is_active = 0;
			
			$corredor->save();
				Session::flash('message', 'Corredor eliminado correctamente!!');
					return Redirect::to('vendedoresTDP');
	    
	}

	public function addCustomerPost()
	{

		$company_type = Input::get('company_type');
	if($company_type==1){ 
		$rules = array(
			'company_name' => 'required',
			'tax_type' => 'required',
			'tax_id' => 'required',
			'fiscal_sit' => 'required',
			'address' => 'required|max:255',
			'country' => 'required',
			'state' => 'required',  
			'city'=> 'required',     
			'post_code'=> 'required',   
			);

		
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			if(Request::ajax()){
				return Response::json(array(
					'fail' => true,
					'errors' => $validator->getMessageBag()->toArray()
					));
			}else{
				return Redirect::to('altaCliente')
				->withErrors($validator);
			}
		} else {

			$address = \app\Address::create([
				'address' => Input::get('address'),
				'floor' => Input::get('floor'),
				'door' => Input::get('door'),
				'post_code' => Input::get('post_code'),
				'countries_id' => Input::get('country'),
				'states_id' => Input::get('state'),
				'city' => Input::get('city'),
				]);

			$customer = new \app\Company;
			
			$web = Input::get('web');
			if(!empty($web)) {
				$customer->website = Input::get('web');
			}
			
			$customer->addresses_id = $address->id;
			$customer->company_name = Input::get('company_name');
			$customer->email = Input::get('email');
			$customer->tax_id = Input::get('tax_id');
			$tel = Input::get('tel');
			if(!empty($tel)) {
				$customer->tel = Input::get('tel');
			}
			$fax = Input::get('fax');
			if(!empty($fax)) {
				$customer->fax = Input::get('fax');
			}
			$customer->is_active = 1;
			$customer->fiscal_situation_id = Input::get('fiscal_sit');
			$customer->tax_type_id = Input::get('tax_type');
			$customer->companies_type_id = $company_type;

			if($customer->save()){
				if(Request::ajax()){
					$direccion = "";
					$direccion .= $customer->addresses->address . ', ';
					if($customer->addresses->floor!=null){
						$direccion .= 'Piso: ' . $customer->addresses->floor . ', ';	
					}
					if($customer->addresses->door!=null){
						$direccion .= 'Dpto.: ' . $customer->addresses->door . ', ';	
					}
					$direccion .= $customer->addresses->city. ' ('. $customer->addresses->post_code . ')' .   ', ';
					$direccion .= $customer->addresses->states->state. ', ';
					$direccion .= $customer->addresses->countries->country . '.';
					return Response::json(array(
					'id' => $customer->id,	
					'company_name' => $customer->company_name,
					'dire' => $direccion,
					'tax_id' => $customer->tax_id,
					'fs' => $customer->fiscal_situation->fisc_situation,
					'tax_type' => $customer->tax_type->type,
					));
				}else{
					Session::flash('message', 'Cliente creado correctamente!!');
					return Redirect::to('altaCliente');
				}
			}

		}
	}else{

			$rules = array(
			'company_name' => 'required',
			'tax_type' => 'required',
			'tax_id' => 'required',
			'fiscal_sit' => 'required',
			);

		
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			if(Request::ajax()){
				return Response::json(array(
					'fail' => true,
					'errors' => $validator->getMessageBag()->toArray()
					));
			}else{
				return Redirect::to('altaProveedor')
				->withErrors($validator);
			}
		} else {

			$prov = new \app\Company;
			$prov->company_name = Input::get('company_name');
			$prov->tax_id = Input::get('tax_id');
			$prov->is_active = 1;
			$prov->tax_type_id = Input::get('tax_type');
			$prov->companies_type_id = $company_type;
			$prov->fiscal_situation_id = Input::get('fiscal_sit');
			
			if($prov->save()){
				if(Request::ajax()){
					return Response::json(array(
					'id' => $prov->id,	
					'company_name' => $prov->company_name,
					'tax_id' => $prov->tax_id,
					'tax_type' => $prov->tax_type->type,
					'fs' => $prov->fiscal_situation->fisc_situation,
					));
				}else{
					Session::flash('message', 'Proveedor creado correctamente!!');
					return Redirect::to('altaProveedor');
				}
			}						
	}
	

	}
}

	public function editCustomer($id = null)
	{
		$company = \app\Company::find($id);
		$taxes = \app\TaxType::all();
		$fiscal_situations = \app\FiscalSituation::all();
		if($company->companies_type_id!=2){
		$taxes = \app\TaxType::all();
		$countries = \app\Country::all();
		$states = \app\State::where('countries_id', '=',$company->addresses->countries->id)->get();

		return view('company.edit')
		->with('company',$company)
		->with('states',$states)
		->with('taxes',$taxes)
		->with('countries',$countries)
		->with('fiscal_situations',$fiscal_situations);
	}else{
		return view('company.editP')
		->with('company',$company)
		->with('fiscal_situations',$fiscal_situations)
		->with('taxes',$taxes);
	}

	}



	public function editCustomerPost(){

	$company_type = Input::get('company_type');
	$id = Input::get('company_id');	
	if($company_type!=2){
		
		$rules = array(
			'company_name' => 'required',
			'tax_type' => 'required',
			'tax_id' => 'required',
			'fiscal_sit' => 'required',
			'address' => 'required|max:255',
			'country' => 'required',
			'state' => 'required',  
			'city'=> 'required',     
			'post_code'=> 'required',   
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('editarCliente/'.$id)
			->withErrors($validator);
		} else {
			$customer = \app\Company::find($id);
			$floor = Input::get('floor');
			if(!empty($floor)){
				$customer->addresses->floor = Input::get('floor');
			}
			$door = Input::get('door');
			if(!empty($door)){
				$customer->addresses->door = Input::get('door');
			}
			$customer->addresses->address =Input::get('post_code');
			$customer->addresses->post_code = Input::get('post_code');
			$customer->addresses->countries_id = Input::get('country');
			$customer->addresses->states_id = Input::get('state');
			$customer->addresses->city = Input::get('city');
			$customer->email = Input::get('email');
			$web = Input::get('web');
			if(!empty($web)) {
				$customer->website = Input::get('web');
			}
			
			$customer->company_name = Input::get('company_name');

			$customer->tax_id = Input::get('tax_id');
			$tel = Input::get('tel');
			if(!empty($tel)) {
				$customer->tel = Input::get('tel');
			}
			$fax = Input::get('fax');
			if(!empty($fax)) {
				$customer->fax = Input::get('fax');
			}
			$customer->is_active = 1;
			$customer->fiscal_situation_id = Input::get('fiscal_sit');
			$customer->tax_type_id = Input::get('tax_type');
			$customer->companies_type_id = $company_type;

			if($customer->push()){
				Session::flash('message', 'Cliente modificado correctamente!!');
				return Redirect::to('editarCliente/'.$id);
			}

		}

	}else{
			$rules = array(
			'company_name' => 'required',
			'tax_type' => 'required',
			'tax_id' => 'required',
			'fiscal_sit' => 'required',
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('editarCliente/'.$id)
			->withErrors($validator);
		} else {
			$customer = \app\Company::find($id);
			$customer->company_name = Input::get('company_name');
			$customer->tax_id = Input::get('tax_id');
			$customer->tax_type_id = Input::get('tax_type');
			$customer->fiscal_situation_id = Input::get('fiscal_sit');

			if($customer->push()){
				Session::flash('message', 'Proveedor modificado correctamente!!');
				return Redirect::to('editarCliente/'.$id);
			}

		}

	}

}

	public function listCustomers()
	{

		$customers = \app\Company::where('id','!=',Auth::user()->companies_id)
		->where('companies_type_id','=',1)
		->where('is_active','=','1')
		->paginate(10);

		return view('company.list')->with('customers',$customers);

	}

	public function listProviders()
	{

		$provs = \app\Company::where('id','!=',Auth::user()->companies_id)
		->where('companies_type_id','=',2)
		->where('is_active','=','1')
		->paginate(10);

		return view('company.listP')->with('provs',$provs);

	}

	public function deleteCompany( $id = null )
	{

		$company = \app\Company::find($id);
		
		if($company!=null){

				$company->is_active = 0;
				if($company->save()) {
				if($company->companies_type_id==1){
					Session::flash('message', 'Cliente eliminado correctamente!!');
					return Redirect::to('clientes');
				
				}else{
					Session::flash('message', 'Proveedor eliminado correctamente!!');
					return Redirect::to('proveedores');
					
				}
				
				}
			} else{
			return view('clientes');
			}

	}

}