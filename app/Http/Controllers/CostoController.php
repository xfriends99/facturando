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

class CostoController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}


        public function getCosto(\Illuminate\Http\Request $request)
	{
        $productss = ProductoTDP::orderBy('reference', 'asc')->get();
        $products_lists = [];
        foreach ($productss as $p){
            $products_lists[$p->id_product] = $p->descripcion;
        }
        if($request->name){
            $products = \app\Product::where('id_product', $request->name)->get();
        } else {
            $products = \app\Product::all();
        }
		return view('costo.list')->with('products',$products)
            ->with('products_lists', $products_lists);

	}

	

}
