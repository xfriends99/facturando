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
use app\ProductoTDP;

class ProductsController extends Controller {

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
	public function getEditProduct($id = null)
	{	

		$product = ProductoTDP::find($id);
		if($product!=null){
			return view('product.edit')->with('product',$product);
		}
		else{
			return view('products');
		}

	}

	public function postEditProduct(\Illuminate\Http\Request $request, $id)
	{

        $rules = array(
            'codigo' => 'required',
            'descripcion' => 'required',
            'pesoRef' => 'required|numeric',
            'diametroRef' => 'required|numeric',
            'metrosRef' => 'required|numeric',
            'rollosRef' => 'required|integer',
            'operacion' => 'required',
            'peso_manga' => 'numeric',
            'diametro' => 'numeric',
            'cant_metros' => 'numeric',
            'cant_por_man' => 'numeric',
            'cant_por_pack' => 'numeric',
            'peso_por_pack' => 'numeric',
            //'tmpo_reb' => '',
            'emp_util_reb' => 'integer',
            //'tmpo_corte' => '',
            'emp_util_corte' => 'integer',
            //'tmpo_empq' => '',
            'emp_util_emp' => 'integer',
            'stock_Fisico' => 'required|integer',
            'stock_Pedido' => 'required|integer'
        );
		
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Redirect::to('products/'.$id.'/edit')
			->withErrors($validator);
		} else {
			$product = ProductoTDP::find($id);
            $data = $request->except(['_token']);
			if($product = $product->update($data)){
				Session::flash('message', 'Producto actualizado correctamente!!');
				return Redirect::to('products');
			} else {
                return Redirect::to('products/'.$id.'/edit')->withErrors('Error al editar el producto');
            }
		}
	}


	public function getAddProduct()
	{
		return view('product.add');
	}

	public function postAddProduct(\Illuminate\Http\Request $request)
	{

        $rules = array(
            'codigo' => 'required',
            'descripcion' => 'required',
            'pesoRef' => 'required|numeric',
            'diametroRef' => 'required|numeric',
            'metrosRef' => 'required|numeric',
            'rollosRef' => 'required|integer',
            'operacion' => 'required',
            'peso_manga' => 'numeric',
            'diametro' => 'numeric',
            'cant_metros' => 'numeric',
            'cant_por_man' => 'numeric',
            'cant_por_pack' => 'numeric',
            'peso_por_pack' => 'numeric',
            //'tmpo_reb' => '',
            'emp_util_reb' => 'integer',
            //'tmpo_corte' => '',
            'emp_util_corte' => 'integer',
            //'tmpo_empq' => '',
            'emp_util_emp' => 'integer',
            'stock_Fisico' => 'required|integer',
            'stock_Pedido' => 'required|integer'
        );
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('products/create')
			->withErrors($validator);
		} else {
		    $data = array_merge($request->except(['_token']), ['reference' => '']);
            $product = ProductoTDP::create($data);
			if($product){
				Session::flash('message', 'Producto creado correctamente!!');
				return Redirect::to('products');
			} else {
                return Redirect::to('products/create')->withErrors('Error al almacenar el producto');
            }

		}
	}

	public function listProducts()
	{

		$products = ProductoTDP::orderBy('id', 'desc')->paginate(10);

		return view('product.list')->with('products',$products);

	}

	public function deleteProduct( $id = null )
	{

		$product = ProductoTDP::find($id);

		if($product!=null){
			if($product->delete()) {
				Session::flash('message', 'Producto eliminado correctamente!!');
				return Redirect::to('products');
			} else{
				return view('home');
			}
		}
		else{
			return view('home');
		}

	}

}
