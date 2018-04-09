<?php namespace app\Http\Controllers;

use app\Http\Requests;
use app\Http\Controllers\Controller;
use Carbon\Carbon;
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
        $controll = [];
        $produccionl = [];
        foreach ($products as $p){
            $control = ['fecha' => $request->date,
                'id_producto' => $p];
            $produccion = ['codigo' => $products_database[$p]->codigo,
                'id_producto' => $p, 'users_id' => \Auth::user()->id];
            if($products_database[$p]->operacion=='I'){
                if($request['packs'.$p]=='' || $request['packs'.$p]==null){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' no puede estar vacio');
                }
                if($request['mangas'.$p]=='' || $request['mangas'.$p]==null){
                    return Redirect::back()->withErrors('La manga del producto '.$products_database[$p]->descripcion.' no puede estar vacio');
                }
                if($request['peso'.$p]=='' || $request['peso'.$p]==null){
                    return Redirect::back()->withErrors('El peso del producto '.$products_database[$p]->descripcion.' no puede estar vacio');
                }
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
                if($request['packs'.$p]=='' || $request['packs'.$p]==null){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' no puede estar vacio');
                }
                if(!is_numeric($request['packs'.$p])){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if(intval($request['packs'.$p]) < 0){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser mayor a 0');
                }
                $control['packs'] = $request['packs'.$p];
            }
            $controll[] = $control;
            $produccionl[] = $produccion;
        }
        $ii = 0;
        foreach ($controll as $c){
            if($cc = ControlDeProduccion::where('fecha', $request->date)->where('id_producto', $c['id_producto'])->get()->first()){
                ControlDeProduccion::where('fecha', $request->date)->where('id_producto', $c['id_producto'])->update($c);
                Produccion::where('control_id', $cc->id)->update($produccionl[$ii]);
            } else {
                $cc = ControlDeProduccion::create($c);
                Produccion::create(array_merge($produccionl[$ii], ['control_id' => $cc->id]));
            }
            $ii++;
        }
        Session::flash('message', 'Producción cargada correctamente!!');
        return Redirect::to('viewProd');
    }

    public function controlProduccion(Request $request)
    {
        $list = Produccion::select('produccion.*')
            ->addSelect(\DB::raw('SUM(mangas) as mangas_sum'))
            ->addSelect(\DB::raw('SUM(mangas*kg) as kg_sum'))
            ->addSelect(\DB::raw('SUM(kg) as kg_suma'))
            ->addSelect(\DB::raw('COUNT(produccion.id_producto) as productos_count'))
            ->where('produccion.controlado', 0)
            ->join('productosTDP', 'productosTDP.id', '=', 'produccion.id_producto')
            ->join('ControlDeProduccion', 'produccion.control_id', '=', 'ControlDeProduccion.id')
            ->with(['producto', 'control'])
            ->groupBy('ControlDeProduccion.id_producto')->groupBy('ControlDeProduccion.fecha')->get();
        $ll = [];
        foreach ($list as $l){
            $ll[] = $l->id_producto;
        }
        $control_produccion = [];
        $cr = ControlDeProduccion::whereIn('id_producto', $ll)
            ->where('controlado', 0)->get();
        foreach ($cr as $c){
            if(!isset($control_produccion[$c->id_producto])) $control_produccion[$c->id_producto] = [];
            if(!isset($control_produccion[$c->id_producto]['packs'])) $control_produccion[$c->id_producto]['packs'] = $c->packs;
            if(!isset($control_produccion[$c->id_producto]['created_at'])) $control_produccion[$c->id_producto]['created_at'] = $c->fecha;
            $control_produccion[$c->id_producto]['packs'] += $c->packs;
            $control_produccion[$c->id_producto]['created_at'] = $c->fecha;
        }
        $request = $request->all();
        return view('produccion.controlproduccion.list',
            compact('list', 'request', 'control_produccion'));
    }

    public function controlStore(Request $request)
    {
        if($request->ok){
            $index = 0;
            foreach ($request->ok as $p){
                if($p!=''){
                    Produccion::where('id_producto', $p)->update(['controlado' => 1]);
                    ControlDeProduccion::where('id_producto', $p)->update(['controlado' => 1]);
                    /*$producto = ProductoTDP::find($p);
                    $producto->stock_Fisico = $producto->stock_Fisico+$request->stock[$index];
                    $producto->save();*/
                }
            }
            Session::flash('message', 'Producción actualizada correctamente!!');
            return Redirect::back();
        }
        return Redirect::back()->withErrors('No marco algun producto a ser cargado');
    }

    public function getListProduct(Request $request)
    {
        if(!$request->fecha){
            return response()->json(['data' => false]);
        }
        $array = [];
        $pro = Produccion::join('ControlDeProduccion', 'produccion.control_id', '=', 'ControlDeProduccion.id')
            ->where('ControlDeProduccion.fecha', $request->fecha)
            ->groupBy('ControlDeProduccion.id_producto')
            ->with(['control', 'producto'])->get();
        $index = 0;
        foreach ($pro as $pp){
            $array[] = [];
            $array[$index]['packs'] = $pp->control->packs;
            $array[$index]['mangas'] = $pp->mangas;
            $array[$index]['peso'] = $pp->kg;
            $array[$index]['tipo'] = $pp->mangas==null ? 'R' : 'I';
            $array[$index]['id'] = $pp->id_producto;
            $array[$index]['name'] = $pp->producto->descripcion;
            $index++;
        }
        return response()->json(['data' => $array]);
    }

}
