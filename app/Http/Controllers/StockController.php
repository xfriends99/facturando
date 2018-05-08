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

class StockController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}


        public function getStock()
	{
        //$products = \app\Stock::all();
        $user = \Auth::user();
        $products = \app\ProductoTDP::where('active', 1)->orderBy('reference');
        if($user->roles_id!=1){
            $products->where('reference', 'not like', '1-%');
            $products->where('reference', 'not like', '6-%');
        }
		return view('stock.list')->with('products',$products->get());

	}

	

}
