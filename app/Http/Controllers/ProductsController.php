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

		$product = \app\Product::find($id);
		$categories = \app\Category::where('is_active','=','1')->get();
		$ivas = \app\TipoIVA::all();

		if($product!=null){

			return view('product.edit')
			->with('product',$product)
			->with('ivas',$ivas)
			->with('categories',$categories);
		}
		else{
			return view('home');
		}

	}

	public function postEditProduct()
	{	
		
		$rules = array(
			'category' => 'required',
			'name' => 'required',
			'iva' => 'required',
			);
		
		$validator = Validator::make(Input::all(), $rules);
		$id = Input::get('product_id');

		if ($validator->fails()) {
			return Redirect::to('editCategory/'.$id)
			->withErrors($validator);
		} else {

			$product = \app\Product::find($id);

			$product->name = Input::get('name');
			
			$product->tipo_iva_id = Input::get('iva');
			
			$product->categories_id = Input::get('category');

			$punitario = Input::get('punitario');
			if(!empty($punitario)){
				$product->price = Input::get('punitario');
			}

			$descripcion = Input::get('descripcion');
			if(!empty($descripcion)){
				$product->description = Input::get('descripcion');
			}

			$code = Input::get('code');
			if(!empty($code)) {
				$product->code = Input::get('code'); 
			}
			else{
				$product->code = $product->id;			
			}

			if($product->save()){
				Session::flash('message', 'Producto actualizado correctamente!!');
				return Redirect::to('editProduct/'.$id);
			}


		}
	}


	public function getAddProduct()
	{
		$categories = \app\Category::where('is_active','=','1')
		->get();
		$ivas = \app\TipoIVA::all();

		return view('product.add')->with('categories',$categories)
		->with('ivas',$ivas);

	}

	public function postAddProduct()
	{

		$rules = array(
			'category' => 'required',
			'name' => 'required',
			'code' => 'unique:products',
			'iva' => 'required'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('addProduct')
			->withErrors($validator);
		} else {
			$product = new \app\Product;

			$product->tipo_iva_id = Input::get('iva');

			$product->name = Input::get('name');
			
			$product->categories_id = Input::get('category');

			$punitario = Input::get('punitario');
			if(!empty($punitario)){
				$product->price = Input::get('punitario');
			}

			$descripcion = Input::get('descripcion');
			if(!empty($descripcion)){
				$product->description = Input::get('descripcion');
			}
			
			if($product->save()){

				$code = Input::get('code');
				if(!empty($code)) {
					$product->code = Input::get('code'); 
				}
				else{
					$product->code = $product->id;			
				}
				$product->save();
				Session::flash('message', 'Producto creado correctamente!!');
				return Redirect::to('addProduct');
			}

		}
	}

	public function listProducts()
	{

		$products = \app\Product::where('is_active','=','1')
		->paginate(10);

		return view('product.list')->with('products',$products);

	}

	public function deleteProduct( $id = null )
	{

		$product = \app\Product::find($id);

		if($product!=null){

			$product->is_active = 0;
			if($product->save()) {
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
