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
        $customers = collect();
        $statuses = \DB::table('toallasd_tdp.ps_order_state_lang')
            ->where('id_lang',1)->whereIn('id_order_state',[3,5,6,7,8,9,12,13])
            ->orderBy('ps_order_state_lang.name','asc')->get();

        $pedidos = \app\Pedido::join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
            ->join('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
            ->leftJoin('ps_address', 'ps_address.id_address', '=', 'ps_orders.id_address_invoice')
            ->distinct('ps_order_state_lang.id_order_state')
            ->addSelect('ps_orders.*')
            ->addSelect('ps_order_state_lang.name as name_state')
            ->addSelect('ps_order_state.color as color')
            ->where('ps_order_state_lang.id_lang',1)
            ->orderBy('id_order','DESC');

        if($request->get('status')){
            $pedidos->where('ps_orders.current_state', $request->status);
        }
        if($request->get('cliente')){
            $pedidos->where(function($q) use($request){
                $q->where('ps_orders.id_customer', $request->cliente);
                $q->orWhere('ps_address.id_customer', $request->cliente);
            });
        }
        $peds = \app\Pedido::join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
            ->join('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
            ->distinct('ps_order_state_lang.id_order_state')
            ->addSelect('ps_orders.*')
            ->addSelect('ps_order_state_lang.name as name_state')
            ->addSelect('ps_order_state.color as color')
            ->where('ps_order_state_lang.id_lang',1)
            ->with(['direccion_factura', 'customer'])
            ->orderBy('id_order','DESC')->get();
            $cus = [];
            foreach ($peds as $p){
                if($p->direccion_factura && $p->direccion_factura->company!='') {
                    $customers->push(['key' => $p->direccion_factura->id_customer, 'v' => $p->direccion_factura->company]);
                } elseif($p->customer!=null && ($p->customer->firstname!='' || $p->customer->lastname!='')) {
                    $customers->push(['key' => $p->customer->id_customer, 'v' => $p->customer->firstname . ' ' . $p->customer->lastname]);
                }
            }

            foreach ($customers->sortBy('v')->toArray() as $r){
                $cus[$r['key']] = $r['v'];
            }
	        return view('pedidos.list')->with('request', $request->all())
                ->with('pedidos',$pedidos->paginate(10))->with('statuses', $statuses)->with('customers', $cus);
		
		}
		
	
	
	
}