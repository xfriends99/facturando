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
        foreach ($products as $p){
            $control = ['fecha' => $request->date,
                'id_producto' => $p];
            if($products_database[$p]->operacion=='I'){
                /*if($request['packs'.$p]=='' || $request['packs'.$p]==null){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' no puede estar vacio');
                }
                if($request['mangas'.$p]=='' || $request['mangas'.$p]==null){
                    return Redirect::back()->withErrors('La manga del producto '.$products_database[$p]->descripcion.' no puede estar vacio');
                }
                if($request['peso'.$p]=='' || $request['peso'.$p]==null){
                    return Redirect::back()->withErrors('El peso del producto '.$products_database[$p]->descripcion.' no puede estar vacio');
                }*/
                if($request['packs'.$p]!='' && !is_numeric($request['packs'.$p])){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if($request['mangas'.$p]!='' && !is_numeric($request['mangas'.$p])){
                    return Redirect::back()->withErrors('La manga del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if($request['peso'.$p]!='' && !is_numeric($request['peso'.$p])){
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
                $control['type_manga'] = $request['type_manga'.$p];
                $control['packs'] = ($request['packs'.$p]!='' && is_numeric($request['packs'.$p])) ? $request['packs'.$p] : 0;
                $control['mangas'] = ($request['mangas'.$p]!='' && is_numeric($request['mangas'.$p])) ? $request['mangas'.$p] : 0;
                $control['kg'] = ($request['peso'.$p]!='' && is_numeric($request['peso'.$p])) ? $request['peso'.$p] : 0;
            } else {
                /*if($request['packs'.$p]=='' || $request['packs'.$p]==null){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' no puede estar vacio');
                }*/
                if($request['packs'.$p]!='' && !is_numeric($request['packs'.$p])){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if(intval($request['packs'.$p]) < 0){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser mayor a 0');
                }
                $control['packs'] = ($request['packs'.$p]!='' && is_numeric($request['packs'.$p])) ? $request['packs'.$p] : 0;
            }
            $controll[] = $control;
        }
        $ii = 0;
        foreach ($controll as $c){
            if($cc = ControlDeProduccion::where('fecha', $request->date)->where('id_producto', $c['id_producto'])->get()->first()){
                ControlDeProduccion::where('fecha', $request->date)->where('id_producto', $c['id_producto'])->update($c);
            } else {
                ControlDeProduccion::create($c);
            }
            $ii++;
        }
        Session::flash('message', 'Producción cargada correctamente!!');
        return Redirect::to('viewProd');
    }

    public function controlProduccion(Request $request)
    {
        $control = ControlDeProduccion::where('controlado', 0)
            ->where('packs', '>', 0)
            ->groupBy('fecha')->groupBy('id_producto')->with(['producto'])
            ->orderBy('fecha', 'desc')
            ->get();
        $produccion_data = [];
        foreach ($control as $c){
            $pro = Produccion::select('produccion.*')
                ->addSelect(\DB::raw('SUM(mangas) as mangas_sum'))
                ->addSelect(\DB::raw('SUM(kg) as kg_suma'))
                ->addSelect(\DB::raw('COUNT(produccion.id_producto) as productos_count'))
                ->where('created_at', '>=', $c->fecha.' 00:00:00')->where('created_at', '<=', $c->fecha. ' 23:59:59')
                ->where('id_producto', $c->id_producto)
                ->groupBy('produccion.id_producto')->get()->first();
            $produccion_data[$c->id] = $pro;
        }

        $request = $request->all();
        return view('produccion.controlproduccion.list',
            compact('list', 'request', 'produccion_data', 'control'));
    }

    public function controlStore(Request $request)
    {
        if($request->ok){
            $index = 0;
            foreach ($request->ok as $p){
                if($p!='' && is_numeric($p)){
                    $control = ControlDeProduccion::find($p);
                    if($control){
                        $control->controlado = 1;
                        $control->a_stock = $request->stock[$index];
                        $control->save();
                        $producto = ProductoTDP::find($control->id_producto);
                        $producto->stock_Fisico = $producto->stock_Fisico+$request->stock[$index];
                        $producto->save();
                    }
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
        $pro = ControlDeProduccion::where('fecha', $request->fecha)
            ->where('controlado', 0)->with(['producto'])->get();
        $index = 0;
        foreach ($pro as $pp){
            if($pp->producto->operacion=='I'){
                $array[] = [];
                $array[$index]['packs'] = $pp->packs;
                $array[$index]['mangas'] = $pp->mangas ? $pp->mangas : 0;
                $array[$index]['peso'] = $pp->kg ? $pp->kg : 0;
                $array[$index]['type_manga'] = $pp->type_manga;
            } else {
                $array[] = [];
                $array[$index]['packs'] = $pp->packs;
            }
            $array[$index]['tipo'] = $pp->producto->operacion;
            $array[$index]['id'] = $pp->id_producto;
            $array[$index]['name'] = $pp->producto->descripcion;
            $index++;
        }
        return response()->json(['data' => $array]);
    }

}
