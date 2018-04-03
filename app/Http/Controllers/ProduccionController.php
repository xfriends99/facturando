<?php namespace app\Http\Controllers;

use app\Http\Requests;
use app\Http\Controllers\Controller;
use Illuminate\Http\Request;
use app\ProductoTDP;
use app\Produccion;
use app\ControlDeProduccion;
use Illuminate\Support\Facades\Redirect;
use Session;

class ProduccionController extends Controller
{

    public function cargaManualProduccion()
    {
        $products_lists = [];
        $iteration_product = ProductoTDP::where('active', 1)->where('reference', 'like', '1-%')
            ->orderBy('reference', 'asc')->get();
        foreach ($iteration_product as $p){
            $products_lists[$p->id] = $p->descripcion;
        }
        return view('produccion.cargaproduccion.add', compact('products_lists'));
    }

    public function getProductType($id)
    {
        $prod = ProductoTDP::find($id);
        return response()->json(['data' => $prod->operacion]);
    }

    public function store(Request $request)
    {
        $products = explode(',', $request->products);
        $products_database = [];
        foreach (ProductoTDP::whereIn('id', $products)->get() as $p){
            $products_database[$p->id] = $p;
        }
        foreach ($products as $p){
            $control = ['fecha' => $request->fecha,
                'id_producto' => $p];
            $produccion = ['created_at' => $request->fecha,
                'id_producto' => $p, 'users_id' => \Auth::user()->id];
            if($products_database[$p]->operacion=='I'){
                if(!is_numeric($request['packs'.$p])){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if(!is_numeric($request['mangas'.$p])){
                    return Redirect::back()->withErrors('La manga del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if(!is_numeric($request['peso'.$p])){
                    return Redirect::back()->withErrors('El peso del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if(intval($request['packs'.$p]) < 0){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser mayor a 0');
                }
                if(intval($request['mangas'.$p]) < 0){
                    return Redirect::back()->withErrors('La manga del producto '.$products_database[$p]->descripcion.' debe ser mayor a 0');
                }
                if(intval($request['peso'.$p]) < 0){
                    return Redirect::back()->withErrors('EL peso del producto '.$products_database[$p]->descripcion.' debe ser mayor a 0');
                }
                $control['packs'] = $request['packs'.$p];
                $produccion['mangas'] = $request['mangas'.$p];
                $produccion['kg'] = $request['peso'.$p];
            } else {
                if(!is_numeric($request['packs'.$p])){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if(intval($request['packs'.$p]) < 0){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser mayor a 0');
                }
                $control['packs'] = $request['packs'.$p];
            }
            Produccion::create($produccion);
            ControlDeProduccion::create($control);
        }
        Session::flash('message', 'Producción cargada correctamente!!');
        return Redirect::to('viewProd');
    }

    public function controlProduccion(Request $request)
    {
        if($request->desde && $request->hasta){
            $list = Produccion::select('produccion.*')
                ->addSelect(\DB::raw('SUM(mangas) as mangas_sum'))
                ->addSelect(\DB::raw('SUM(mangas*kg) as kg_sum'))
                ->addSelect(\DB::raw('SUM(kg) as kg_suma'))
                ->addSelect(\DB::raw('COUNT(id_producto) as productos_count'))
                ->whereBetween('created_at', [$request->desde, $request->hasta])
                ->where('controlado', 0)->with('producto')->groupBy('id_producto')->get();
            $search = true;
            $ll = [];
            foreach ($list as $l){
                $ll[] = $l->id_producto;
            }
            $control_produccion = [];
            $cr = ControlDeProduccion::whereIn('id_producto', $ll)
                ->where('controlado', 0)->whereBetween('fecha', [$request->desde, $request->hasta])->get();
            foreach ($cr as $c){
                if(!isset($control_produccion[$c->id_producto])) $control_produccion[$c->id_producto] = [];
                if(!isset($control_produccion[$c->id_producto]['packs'])) $control_produccion[$c->id_producto]['packs'] = $c->packs;
                $control_produccion[$c->id_producto]['packs'] += $c->packs;
            }
        } else {
            $list = null;
            $search = false;
            $control_produccion = null;
        }
        $request = $request->all();
        return view('produccion.controlproduccion.list',
            compact('list', 'search', 'request', 'control_produccion'));
    }

}
