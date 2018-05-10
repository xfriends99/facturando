<?php namespace app\Http\Controllers;

use app\ControlDeProduccion;
use app\Linea;
use app\Pago;
use app\Pedido;
use app\Produccion;
use app\ProductoTDP;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Request;
use Session;
use Auth;

class ReporteController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}


public function ivaVentas(){

	$invoices = null;
	return view('report.ventas')->with('invoices',$invoices);
}

public function listarIVAventas(){

		$rules = array(
			'desde' => 'required',
			'hasta' => 'required'
			);

	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()) {
			return Redirect::to('ivaVentas')
			->withErrors($validator);
		} else {

			$rango[0] = Input::get('desde');
			$rango[1] = Input::get('hasta');

			$invoices = \app\InvoiceHead::where('status','=','A')
                        ->where('cbte_tipo','!=','99')
			->whereBetween('fecha_facturacion',$rango)->get();

			return view('report.ventas')->with('invoices',$invoices);

		}

}

public function ivaCompras(){

	$invoices = null;
	return view('report.compras')->with('invoices',$invoices);
}

public function listarIVAcompras(){

		$rules = array(
			'desde' => 'required',
			'hasta' => 'required'
			);

	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()) {
			return Redirect::to('ivaCompras')
			->withErrors($validator);
		} else {

			$rango[0] = Input::get('desde');
			$rango[1] = Input::get('hasta');

			$invoices = \app\FacturaProveedor::where('is_active','=',1)
			->whereBetween('fecha_factura',$rango)->get();

			return view('report.compras')->with('invoices',$invoices);

		}

}

public function cuentaCorriente(){

	$invoices = null;
	return view('report.ctacte')->with('invoices',$invoices);
}

public function listarCtaCte(\Illuminate\Http\Request $request){


			$invoices = \app\InvoiceHead::where('status','=','A')
			//->where('cbte_tipo','!=',3)
			->select(\DB::raw('SUM(cta_ctes.saldo) as sumaSaldo, invoice_head.company_name, invoice_head.companies_id'))			
			->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
			->groupBy('invoice_head.companies_id')
			->get();

			$request['saldo'] = $request->saldo ? $request->saldo : '';
			return view('report.ctacte')->with('invoices',$invoices)->with('request', $request->all());

	}

    public function listarCtaCteProv(\Illuminate\Http\Request $request){


        $invoices = \app\FacturaProveedor::where('is_active', 1)
            ->select(\DB::raw('SUM(importe_total) as sumaSaldo, nombre_proveedor'))
            ->groupBy('nombre_proveedor')
            ->get();

        $request['saldo'] = $request->saldo ? $request->saldo : '';
        return view('report.ctacte_provider')->with('invoices',$invoices)
            ->with('request', $request->all());

    }

    public function listadoControlProduccion(\Illuminate\Http\Request $request)
    {
        if (Input::has('desde') && Input::has('hasta')) {
            $desde = Input::get('desde');
            $hasta = Input::get('hasta');

            $control = ControlDeProduccion::where('controlado', 1)
                ->where(function($q){
                    $q->where('packs', '>', 0);
                    $q->orWhere('type_case', 'B');
                })
                ->whereBetween('fecha', [$desde, $hasta])
                ->groupBy('fecha')->groupBy('id_producto')->with(['producto'])
                ->orderBy('fecha', 'desc')
                ->get();
            $produccion_data = [];
            foreach ($control as $c) {
                $id = $c->id_producto;
                if($c->type_case=='A'){
                    $pp = ProductoTDP::where('codigo', $c->original_code)->first();
                    if($pp){
                        $c->old_name = $c->producto->descripcion;
                        $c->producto = $pp;
                        $id = $pp->id;
                    } else {
                        continue;
                    }
                }
                $pro = Produccion::select('produccion.*')
                    ->addSelect(\DB::raw('SUM(mangas) as mangas_sum'))
                    ->addSelect(\DB::raw('SUM(kg) as kg_suma'))
                    ->addSelect(\DB::raw('COUNT(produccion.id_producto) as productos_count'))
                    ->where('created_at', '>=', $c->fecha . ' 00:00:00')->where('created_at', '<=', $c->fecha . ' 23:59:59')
                    ->where('id_producto', $id)
                    ->groupBy('produccion.id_producto')->get()->first();
                $produccion_data[$c->id] = $pro;
            }
            return view('report.control_produccion',
                compact('request', 'produccion_data', 'control', 'desde', 'hasta'));
        } else {
            $hoy = date("Y-m-d");
            $control = ControlDeProduccion::where('controlado', 1)
                ->where(function($q){
                    $q->where('packs', '>', 0);
                    $q->orWhere('type_case', 'B');
                })
                ->where('fecha', '>=', $hoy . ' 00:00:00')
                ->groupBy('fecha')->groupBy('id_producto')->with(['producto'])
                ->orderBy('fecha', 'desc')
                ->get();
            $produccion_data = [];
            foreach ($control as $c) {
                $id = $c->id_producto;
                if($c->type_case=='A'){
                    $pp = ProductoTDP::where('codigo', $c->original_code)->first();
                    if($pp){
                        $c->old_name = $c->producto->descripcion;
                        $c->producto = $pp;
                        $id = $pp->id;
                    } else {
                        continue;
                    }
                }
                $pro = Produccion::select('produccion.*')
                    ->addSelect(\DB::raw('SUM(mangas) as mangas_sum'))
                    ->addSelect(\DB::raw('SUM(kg) as kg_suma'))
                    ->addSelect(\DB::raw('COUNT(produccion.id_producto) as productos_count'))
                    ->where('created_at', '>=', $c->fecha . ' 00:00:00')->where('created_at', '<=', $c->fecha . ' 23:59:59')
                    ->where('id_producto', $id)
                    ->groupBy('produccion.id_producto')->get()->first();
                $produccion_data[$c->id] = $pro;
            }
            return view('report.control_produccion', compact('request', 'produccion_data', 'control', 'hoy'));
        }
    }
public function ventas(\Illuminate\Http\Request $request){
    $reference = [['id' => 1, 'name'=> '1 - Fabricación Propia de Papelera'],
        ['id' => 2, 'name'=> '2 - Fabricación de Terceros de Papelera'],
        ['id' => 3, 'name'=> '3 - Reventa de Productos no Propio de Papelera'],
        ['id' => 4, 'name'=> '4 - Reventa de Productos no Propio de Plastico'],
        ['id' => 5, 'name'=> '5 - Reventa de Productos no Propio de Servilleta'],
        ['id' => 6, 'name'=> '6 - Materia Prima'],
        ['id' => 7, 'name'=> '7 - Packaging'],
        ['id' => 8, 'name'=> '8 - Insumos']];
    $request['reference'] = $request->reference ? $request->reference : '';
    $statuses = \DB::table('toallasd_tdp.ps_order_state_lang')
        ->where('id_lang',1)->whereIn('id_order_state',[3,5,6,7,8,9,12,13])
        ->orderBy('ps_order_state_lang.name','asc')->get();

        if(Input::has('desde') && Input::has('hasta')){
        $rango[0] = Input::get('desde');
    	$rango[1] = Input::get('hasta');
        $invoices = \app\InvoiceHead::whereBetween('fecha_facturacion',$rango)
        ->orderBy('fecha_facturacion','DESC')
        ->get();
        $id_orders  = [];
        foreach ($invoices as $i){
            $id_orders[] = $i->id_order;
        }
        $order_states = [];
        $pedds = [];
        $peds = Pedido::select('ps_orders.id_order')
            ->addSelect('ps_orders.current_state as current_state')
            ->addSelect('ps_order_state_lang.name as name_state')
            ->addSelect('ps_order_state.color as color')
            ->leftJoin('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
            ->join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
            ->whereIn('ps_orders.id_order', $id_orders)->get();

        foreach ($peds as $p){
            $pedds[$p->id_order] = $p;
        }
        foreach (Pedido::whereIn('id_order', $id_orders)->get() as $p){
            $order_states[$p->id_order] = $p->current_state;
        }
        $request['type'] = $request->type ? $request->type : '';
        $request['reference'] = $request->reference ? implode(',', $request->reference) : '';
        $request['status'] = $request->status ? implode(',', $request->status) : '';
         return view('report.reporte_ventas')->with('invoices',$invoices)
             ->with('request', $request->all())->with('statuses', $statuses)->with('order_states', $order_states)
             ->with('desde',Input::get('desde'))->with('reference', $reference)
             ->with('hasta',Input::get('hasta'))->with('pedds', $pedds);
       }else{
        $hoy = date("Y-m-d");   
	$invoices = \app\InvoiceHead::where('fecha_facturacion','=',$hoy)->orderBy('fecha_facturacion','DESC')->get();
        $id_orders  = [];
        foreach ($invoices as $i){
            $id_orders[] = $i->id_order;
        }
            $request['type'] = $request->type ? $request->type : '';
        $pedds = [];
        $peds = Pedido::select('ps_orders.id_order')
            ->addSelect('ps_orders.current_state as current_state')
            ->addSelect('ps_order_state_lang.name as name_state')
            ->addSelect('ps_order_state.color as color')
            ->join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
            ->leftJoin('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
            ->whereIn('ps_orders.id_order', $id_orders)->get();
        foreach ($peds as $p){
            $pedds[$p->id_order] = $p;
        }
        return view('report.reporte_ventas')->with('invoices',$invoices)
        ->with('request', $request->all())->with('statuses', $statuses)
        ->with('hoy',$hoy)->with('reference', $reference)->with('pedds', $pedds);
     
     }	
   }

    public function listadoProducto(\Illuminate\Http\Request $request){
        $reference = [['id' => 1, 'name'=> '1 - Fabricación Propia de Papelera'],
            ['id' => 2, 'name'=> '2 - Fabricación de Terceros de Papelera'],
            ['id' => 3, 'name'=> '3 - Reventa de Productos no Propio de Papelera'],
            ['id' => 4, 'name'=> '4 - Reventa de Productos no Propio de Plastico'],
            ['id' => 5, 'name'=> '5 - Reventa de Productos no Propio de Servilleta'],
            ['id' => 6, 'name'=> '6 - Materia Prima'],
            ['id' => 7, 'name'=> '7 - Packaging'],
            ['id' => 8, 'name'=> '8 - Insumos']];
        $product_list = ProductoTDP::all();
        $request['reference'] = $request->reference ? $request->reference : '';
        return view('report.products')
            ->with('product_list', $product_list)->with('request', $request->all())
            ->with('reference', $reference);
    }

    public function listadoProductoPedidos(\Illuminate\Http\Request $request){
        $reference = [['id' => 1, 'name'=> '1 - Fabricación Propia de Papelera'],
            ['id' => 2, 'name'=> '2 - Fabricación de Terceros de Papelera'],
            ['id' => 3, 'name'=> '3 - Reventa de Productos no Propio de Papelera'],
            ['id' => 4, 'name'=> '4 - Reventa de Productos no Propio de Plastico'],
            ['id' => 5, 'name'=> '5 - Reventa de Productos no Propio de Servilleta'],
            ['id' => 6, 'name'=> '6 - Materia Prima'],
            ['id' => 7, 'name'=> '7 - Packaging'],
            ['id' => 8, 'name'=> '8 - Insumos']];

        /*if(Input::has('desde') && Input::has('hasta')){
            $rango[0] = Input::get('desde');
            $rango[1] = Input::get('hasta');

            $invoices = \app\InvoiceHead::whereBetween('fecha_facturacion',$rango)
            ->orderBy('fecha_facturacion','DESC')
            ->get();
            return view('report.reporte_listado_producto_pedidos')->with('invoices',$invoices)->with('desde',Input::get('desde'))->with('hasta',Input::get('hasta'));
        }else{
            $hoy = date("Y-m-d");
            $invoices = \app\InvoiceHead::where('fecha_facturacion','=',$hoy)->orderBy('fecha_facturacion','DESC')->get();
            return view('report.reporte_listado_producto_pedidos')->with('invoices',$invoices)->with('hoy',$hoy);
        }*/
        $productos = Linea::select('ps_order_detail.*')
            ->addSelect('ps_orders.date_add as date_add')
            ->addSelect('ps_orders.current_state as current_state')
            ->addSelect(\DB::raw('sum(ps_order_detail.product_quantity) as tot_product'))
            ->join('ps_orders', 'ps_orders.id_order', '=', 'ps_order_detail.id_order')
            ->join('ps_product', 'ps_product.id_product', '=', 'ps_order_detail.product_id')
            ->whereIn('ps_orders.current_state', [3, 13, 12, 7, 8, 9])
            ->groupBy('ps_order_detail.product_id')
            ->orderBy('ps_product.reference')
            ->orderBy('ps_orders.date_add', 'desc')->get();
        $product_list_order = [];
        foreach ($productos as $p){
            $product_list_order[$p->product_id] = $p;
        }
        $products_id = ProductoTDP::all();
        $product_list_tdp = [];
        foreach ($products_id as $p){
            $product_list_tdp[$p->id_product] = $p;
        }
        $pedidos = Linea::select('ps_order_detail.*')
            ->addSelect('ps_orders.date_add as date_add')
            ->addSelect('ps_orders.current_state as current_state')
            ->addSelect('ps_order_state_lang.name as name_state')
            ->addSelect('ps_order_state.color as color')
            ->join('ps_orders', 'ps_orders.id_order', '=', 'ps_order_detail.id_order')
            ->join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
            ->join('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
            ->where('ps_order_state_lang.id_lang',1)
            ->whereIn('ps_orders.current_state', [3, 13, 12])
            ->orderBy('ps_orders.date_add', 'asc')
            ->orderBy('ps_order_detail.product_id')
            ->orderBy('ps_orders.id_customer')->get();

        $pedidos_productos = Linea::select('ps_order_detail.*')
            ->addSelect('ps_orders.date_add as date_add')
            ->addSelect('ps_orders.current_state as current_state')
            ->addSelect('ps_order_state_lang.name as name_state')
            ->addSelect('ps_order_state.color as color')
            ->join('ps_orders', 'ps_orders.id_order', '=', 'ps_order_detail.id_order')
            ->join('ps_order_state_lang', 'ps_orders.current_state','=','ps_order_state_lang.id_order_state')
            ->join('ps_order_state', 'ps_orders.current_state','=','ps_order_state.id_order_state')
            ->where('ps_order_state_lang.id_lang',1)
            ->whereIn('ps_orders.current_state', [3, 13, 12])
            ->orderBy('ps_orders.date_add', 'asc')
            ->get();
        $product_list_pedidos = [];
        foreach ($pedidos_productos as $p){
            $pro = $p->product_id."";
            if(!isset($product_list_pedidos[$pro])) $product_list_pedidos[$pro] = [];
            $product_list_pedidos[$pro][] = $p;
        }
        $products_id = ProductoTDP::all();
        $product_list = [];
        foreach ($products_id as $p){
            $product_list[$p->id_product] = $p;
        }
        $request['reference'] = $request->reference ? $request->reference : '';
        $request['teorico'] = $request->teorico ? $request->teorico : '';
        return view('report.reporte_listado_producto_pedidos')->with('pedidos',$pedidos)
            ->with('product_list', $product_list)->with('pedidos_productos', $pedidos_productos)
            ->with('request', $request->all())->with('product_list_order', $product_list_order)
            ->with('product_list_tdp', $product_list_tdp)->with('reference', $reference)
            ->with('product_list_pedidos', $product_list_pedidos);
    }

    public function listadoStockTipo(\Illuminate\Http\Request $request){
        $reference = [['id' => 1, 'name'=> '1 - Fabricación Propia de Papelera'],
            ['id' => 2, 'name'=> '2 - Fabricación de Terceros de Papelera'],
            ['id' => 3, 'name'=> '3 - Reventa de Productos no Propio de Papelera'],
            ['id' => 4, 'name'=> '4 - Reventa de Productos no Propio de Plastico'],
            ['id' => 5, 'name'=> '5 - Reventa de Productos no Propio de Servilleta'],
            ['id' => 6, 'name'=> '6 - Materia Prima'],
            ['id' => 7, 'name'=> '7 - Packaging'],
            ['id' => 8, 'name'=> '8 - Insumos']];

        $productos = Linea::select('ps_order_detail.*')
            ->addSelect('ps_orders.date_add as date_add')
            ->addSelect('ps_orders.current_state as current_state')
            ->addSelect(\DB::raw('sum(ps_order_detail.product_quantity) as tot_product'))
            ->join('ps_orders', 'ps_orders.id_order', '=', 'ps_order_detail.id_order')
            ->join('ps_product', 'ps_product.id_product', '=', 'ps_order_detail.product_id')
            ->whereIn('ps_orders.current_state', [3, 13, 12, 7, 8, 9])
            ->groupBy('ps_order_detail.product_id')
            ->orderBy('ps_product.reference')
            ->orderBy('ps_orders.date_add', 'desc')->get();
        $product_list = collect();
        $product_pedidos_list = [];
        foreach ($productos as $p){
            $product_list->push($p->product_id);
            $product_pedidos_list[$p->product_id] = $p;
        }
        $products_id = ProductoTDP::orderBy('reference')->get();
        $product_list = [];
        foreach ($products_id as $p){
            $product_list[$p->id_product] = $p;
        }
        $request['reference'] = $request->reference ? $request->reference : '';
        $request['teorico'] = $request->teorico ? $request->teorico : '';
        $request['fisico'] = $request->fisico ? $request->fisico : '';
        return view('report.reporte_listado_stock_tipo')->with('request', $request)
            ->with('product_list', $product_list)->with('reference', $reference)
            ->with('productos', $productos)->with('product_pedidos_list', $product_pedidos_list);
    }

    public function listadoStock(\Illuminate\Http\Request $request){
        $reference = [['id' => 1, 'name'=> '1 - Fabricación Propia de Papelera'],
            ['id' => 2, 'name'=> '2 - Fabricación de Terceros de Papelera'],
            ['id' => 3, 'name'=> '3 - Reventa de Productos no Propio de Papelera'],
            ['id' => 4, 'name'=> '4 - Reventa de Productos no Propio de Plastico'],
            ['id' => 5, 'name'=> '5 - Reventa de Productos no Propio de Servilleta'],
            ['id' => 6, 'name'=> '6 - Materia Prima'],
            ['id' => 7, 'name'=> '7 - Packaging'],
            ['id' => 8, 'name'=> '8 - Insumos']];

        $productos = Linea::select('ps_order_detail.*')
            ->addSelect('ps_orders.date_add as date_add')
            ->addSelect('ps_orders.current_state as current_state')
            ->addSelect(\DB::raw('sum(ps_order_detail.product_quantity) as tot_product'))
            ->join('ps_orders', 'ps_orders.id_order', '=', 'ps_order_detail.id_order')
            ->join('ps_product', 'ps_product.id_product', '=', 'ps_order_detail.product_id')
            ->whereIn('ps_orders.current_state', [3, 13, 12, 7, 8, 9])
            ->groupBy('ps_order_detail.product_id')
            ->orderBy('ps_product.reference')
            ->orderBy('ps_orders.date_add', 'desc')->get();
        $product_list = collect();
        foreach ($productos as $p){
            $product_list->push($p->product_id);
        }
        $products_id = ProductoTDP::whereIn('id_product', $product_list->toArray())->get();
        $product_list = [];
        foreach ($products_id as $p){
            $product_list[$p->id_product] = $p;
        }
        $request['reference'] = $request->reference ? $request->reference : '';
        $request['teorico'] = $request->teorico ? $request->teorico : '';
        $request['fisico'] = $request->fisico ? $request->fisico : '';
        return view('report.reporte_listado_stock')->with('request', $request)
            ->with('product_list', $product_list)->with('reference', $reference)
            ->with('productos', $productos);
    }

public function pagosCtaCte(){

	 if(Input::has('desde') && Input::has('hasta')){
	      $rango[0] = Input::get('desde');
	      $rango[1] = Input::get('hasta');
	      
	   $movimientos = \app\Pago::whereBetween('created_at',$rango)->where('is_active','=',1)->get();
	   
	   return view('report.reporte_pago_ctacte')->with('movimientos',$movimientos)->with('desde',Input::get('desde'))->with('hasta',Input::get('hasta'));
	 }else{
	     $hoy = date("Y-m-d");
	     $movimientos = null;
	return view('report.reporte_pago_ctacte')->with('hoy',$hoy)->with('movimientos',$movimientos);
	 }
}

public function listadoCtaCte(){

    if(Input::has('desde') && Input::has('hasta')){
        $rango[0] = Input::get('desde');
        $rango[1] = Input::get('hasta');

        $invoices = $invoices = \app\InvoiceHead::where('status','=','A')
            ->select(\DB::raw('invoice_head.id_order, invoice_head.id as idfact,cta_ctes.saldo,invoice_head.company_name,invoice_head.imp_total,invoice_head.imp_net, cta_ctes.id, invoice_head.nro_cbte, invoice_head.cbte_tipo, invoice_head.fecha_facturacion,invoice_head.users_id'))
            ->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
            ->orderBy('fecha_facturacion','DESC')
            ->whereBetween('fecha_facturacion',$rango)->get();
        $rango[0] = $rango[0]. ' 00:00:00';
        $rango[1] = $rango[1]. ' 23:59:59';
        $movimientos = \app\Pago::select(\DB::raw('pagos.*, invoice_head.company_name'))
            ->join('cta_ctes', 'pagos.cta_ctes_id', '=', 'cta_ctes.id')
            ->join('invoice_head', 'invoice_head.id', '=', 'cta_ctes.invoice_head_id')
            ->whereBetween('created_at',$rango)
            ->where('is_active','=',1)->orderBy('pagos.created_at','desc')->get();
        $row = collect();
        foreach ($invoices as $inv){
            $data = ['type'=>'invoice', 'date' => $inv->fecha_facturacion,
                'cbte_tipo' => $inv->cbte_tipo, 'nro_cbte'=> $inv->nro_cbte,
                'imp_net' => $inv->imp_net,
                'saldo' => Pago::where('cta_ctes_id', $inv->id)
                    ->where('is_active',1)->sum('pago'),
                'idfact' => $inv->idfact, 'id_order' => $inv->id_order,
                'id' => $inv->id, 'object' => $inv, 'companyName' => $inv->company_name];

            if($inv->cbte_tipo!=3){
                $data['imp_total'] = $inv->imp_total;
            } else {
                $data['imp_total'] = 0;
                $data['saldo'] += $inv->imp_total;
            }

            $row->push($data);
        }

        foreach ($movimientos as $inv){
            $data = ['type'=>'pago', 'date' => $inv->created_at,
                'cbte_tipo' => '', 'nro_cbte'=> '',
                'imp_total' => $inv->pago,
                'idfact' => '', 'id_order' => '',
                'id' => $inv->id, 'object' => $inv, 'companyName' => $inv->company_name];

            $row->push($data);
        }

        return view('report.reporte_listado_ctacte')
            ->with('invoices',$row->sortByDesc('date')->toArray())->with('desde',Input::get('desde'))
            ->with('hasta',Input::get('hasta'))->with('invos', $invoices);
    }else{
        $hoy = date("Y-m-d");
        $invoices = $invoices = \app\InvoiceHead::where('status','=','A')
            ->select(\DB::raw('invoice_head.id_order, invoice_head.id as idfact,cta_ctes.saldo,invoice_head.company_name,invoice_head.imp_total,invoice_head.imp_net, cta_ctes.id, invoice_head.nro_cbte, invoice_head.cbte_tipo, invoice_head.fecha_facturacion,invoice_head.users_id'))
            ->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
            ->orderBy('fecha_facturacion','DESC')
            ->where('fecha_facturacion',$hoy)->get();
        $movimientos = \app\Pago::select(\DB::raw('pagos.*, invoice_head.company_name'))
            ->join('cta_ctes', 'pagos.cta_ctes_id', '=', 'cta_ctes.id')
            ->join('invoice_head', 'invoice_head.id', '=', 'cta_ctes.invoice_head_id')
            ->where('created_at',$hoy)
            ->where('is_active','=',1)->orderBy('pagos.created_at','desc')->get();
        $row = collect();
        foreach ($invoices as $inv){
            $data = ['type'=>'invoice', 'date' => $inv->fecha_facturacion,
                'cbte_tipo' => $inv->cbte_tipo, 'nro_cbte'=> $inv->nro_cbte,
                'imp_net' => $inv->imp_net,
                'saldo' => Pago::where('cta_ctes_id', $inv->id)
                    ->where('is_active',1)->sum('pago'),
                'idfact' => $inv->idfact, 'id_order' => $inv->id_order,
                'id' => $inv->id, 'object' => $inv, 'companyName' => $inv->company_name];

            if($inv->cbte_tipo!=3){
                $data['imp_total'] = $inv->imp_total;
            } else {
                $data['imp_total'] = 0;
                $data['saldo'] += $inv->imp_total;
            }

            $row->push($data);
        }

        foreach ($movimientos as $inv){
            $data = ['type'=>'pago', 'date' => $inv->created_at,
                'cbte_tipo' => '', 'nro_cbte'=> '',
                'imp_total' => $inv->pago,
                'idfact' => '', 'id_order' => '',
                'id' => $inv->id, 'object' => $inv, 'companyName' => $inv->company_name];

            $row->push($data);
        }
        return view('report.reporte_listado_ctacte')
            ->with('hoy',$hoy)->with('invoices',$row->sortByDesc('date')->toArray());
    }
}


}
