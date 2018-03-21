<?php namespace app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Request;
use app\Services\UpdateProductService;
use Session;
use Auth;
use app\ProductoTDP;

class ProductsController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

	private $updateProductService;

	public function __construct(UpdateProductService $updateProductService)
	{
		$this->middleware('auth');

        $this->updateProductService = $updateProductService;
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
            $data['updated'] = Carbon::now();
			if($product = $product->update($data)){
				Session::flash('message', 'Producto actualizado correctamente!!');
                return Redirect::to('products');
			} else {
                return Redirect::back()->withErrors('Error al editar el producto');
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

	public function listProducts(\Illuminate\Http\Request $request)
	{
	    $this->updateProductService->updateProduct();
        $reference = [['id' => 1, 'name'=> '1 - Fabricación Propia de Papelera'],
            ['id' => 2, 'name'=> '2 - Fabricación de Terceros de Papelera'],
            ['id' => 3, 'name'=> '3 - Reventa de Productos no Propio de Papelera'],
            ['id' => 4, 'name'=> '4 - Reventa de Productos no Propio de Plastico'],
            ['id' => 5, 'name'=> '5 - Reventa de Productos no Propio de Servilleta'],
            ['id' => 6, 'name'=> '6 - Materia Prima'],
            ['id' => 7, 'name'=> '7 - Packaging'],
            ['id' => 8, 'name'=> '8 - Insumos']];

		$products = ProductoTDP::where('active', 1)
            ->orderBy('reference', 'asc');

		if($request->get('reference') && $request->reference){
		    $products->where('reference', 'like', $request->reference.'-%');
        }
        $query2 = clone $products;
		$products_lists = [];
		$iteration_product = $query2->get();
		foreach ($iteration_product as $p){
		    $products_lists[$p->id] = $p->descripcion;
        }

        if($request->get('name') && $request->name){
            $pro = null;
		    if($request->get('reference')){
                $pro = ProductoTDP::find($request->name);
            }
            if($pro==null || explode('-', $pro->reference)[0]==$request->reference){
                $products->where('id', $request->name);
            }
        }

        return view('product.list')->with('products',$products->paginate(15))
            ->with('reference', $reference)->with('request', $request->all())
            ->with('products_lists', $products_lists);

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
