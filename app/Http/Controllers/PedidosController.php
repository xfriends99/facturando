<?php namespace app\Http\Controllers;

use Illuminate\Support\Facades\DB;
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

	public function getListarPedidos(\Illuminate\Http\Request $request){
	        $statuses = \DB::table('toallasd_tdp.ps_order_state_lang')
                ->where('id_lang',1)->orderBy('id_order_state','asc')->get();
	        if($request->get('status')){
                $pedidos = \app\Pedido::join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
                    ->join('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
                    ->distinct('ps_order_state_lang.id_order_state')
                    ->addSelect('ps_orders.*')
                    ->addSelect('ps_order_state_lang.name as name_state')
                    ->addSelect('ps_order_state.color as color')
                    ->where('ps_order_state_lang.id_lang',1)
                    ->where('ps_orders.current_state', $request->status)
                    ->orderBy('id_order','DESC')->paginate(10);
            } else {
                $pedidos = \app\Pedido::join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
                    ->join('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
                    ->distinct('ps_order_state_lang.id_order_state')
                    ->addSelect('ps_orders.*')
                    ->addSelect('ps_order_state_lang.name as name_state')
                    ->addSelect('ps_order_state.color as color')
                    ->where('ps_order_state_lang.id_lang',1)
                    ->orderBy('id_order','DESC')->paginate(10);
            }
	        return view('pedidos.list')->with('request', $request->all())
                ->with('pedidos',$pedidos)->with('statuses', $statuses);
		
		}
		
	
	
	
}