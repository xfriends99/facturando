<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Request;
use Session;
use Response;
use Auth;
use File;


class PedidosController extends Controller{


	public function __construct()
	{
		$this->middleware('auth');
	}

	public function getListarPedidos(){
	
	        $pedidos = \app\Pedido::orderBy('id_order','DESC')->paginate(10);

	        return view('pedidos.list')->with('pedidos',$pedidos);
		
		}
		
	
	
	
}