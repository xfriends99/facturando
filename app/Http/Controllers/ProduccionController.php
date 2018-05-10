<?php namespace app\Http\Controllers;

use app\Http\Requests;
use app\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use app\ProductoTDP;
use app\Produccion;
use app\ControlDeProduccion;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
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

    public function getProductType($id, Request $request)
    {
        $prod = ProductoTDP::find($id);
        if($prod->operacion!='I'){
            $pup = Produccion::select('produccion.*')
                ->addSelect(\DB::raw('SUM(mangas) as mangas_sum'))
                ->addSelect(\DB::raw('SUM(kg) as kg_suma'))
                ->addSelect(\DB::raw('COUNT(produccion.id_producto) as productos_count'))
                ->where('created_at', '>=', $request->fecha.' 00:00:00')->where('created_at', '<=', $request->fecha. ' 23:59:59')
                ->where('id_producto', $id)
                ->groupBy('produccion.id_producto')->get()->first();
        } else {
            $pup = true;
        }
        return response()->json(['data' => $prod->operacion, 'pro' => $pup ? true : false]);
    }

    public function store(Request $request)
    {
        $products = explode(',', $request->products);
        $products_database = [];
        if(!$request->products){
            return Redirect::back()->withErrors('Debe seleccionar algun producto para cargar');
        }
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
                if(isset($request['type_case'.$p]) && ($request['type_case'.$p]=='' || $request['type_case'.$p]==null)){
                    return Redirect::back()->withErrors('Debe seleccionar el caso del producto '.$products_database[$p]->descripcion.' sin producción del dia seleccionado');
                }
                if(isset($request['code'.$p]) && ($request['code'.$p]=='' || $request['code'.$p]==null)){
                    return Redirect::back()->withErrors('Debe introducir el código original para el producto '.$products_database[$p]->descripcion);
                }
                if($request['packs'.$p]!='' && !is_numeric($request['packs'.$p])){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser numerico');
                }
                if(intval($request['packs'.$p]) < 0){
                    return Redirect::back()->withErrors('EL packs del producto '.$products_database[$p]->descripcion.' debe ser mayor a 0');
                }
                $control['packs'] = ($request['packs'.$p]!='' && is_numeric($request['packs'.$p])) ? $request['packs'.$p] : 0;
                if(isset($request['peso'.$p])) {
                    $control['kg'] = ($request['peso'.$p] != '' && is_numeric($request['peso'.$p])) ? $request['peso'.$p] : 0;
                }
                if(isset($request['type_case'.$p])){
                    $control['type_case'] = $request['type_case'.$p];
                }
                if(isset($request['code'.$p])){
                    $control['original_code'] = $request['code'.$p];
                }
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
            ->where(function($q){
                $q->where('packs', '>', 0);
                $q->orWhere('type_case', 'B');
            })
            ->groupBy('fecha')->groupBy('id_producto')->with(['producto'])
            ->orderBy('fecha', 'desc')
            ->get();
        $produccion_data = [];
        foreach ($control as $c){
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
                ->where('created_at', '>=', $c->fecha.' 00:00:00')->where('created_at', '<=', $c->fecha. ' 23:59:59')
                ->where('id_producto', $id)
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
                        if($control->type_case=='A'){
                            $producto = ProductoTDP::where('codigo', $control->original_code)->first();
                        } else {
                            $producto = ProductoTDP::find($control->id_producto);
                        }
                        $producto->stock_Fisico = $producto->stock_Fisico ? $producto->stock_Fisico+$request->stock[$index] : $request->stock[$index];
                        $producto->save();
                    }
                }
                $index++;
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
                $pup = Produccion::select('produccion.*')
                    ->addSelect(\DB::raw('SUM(mangas) as mangas_sum'))
                    ->addSelect(\DB::raw('SUM(kg) as kg_suma'))
                    ->addSelect(\DB::raw('COUNT(produccion.id_producto) as productos_count'))
                    ->where('created_at', '>=', $pp->fecha.' 00:00:00')->where('created_at', '<=', $pp->fecha. ' 23:59:59')
                    ->where('id_producto', $pp->id_producto)
                    ->groupBy('produccion.id_producto')->get()->first();
                $array[] = [];
                $array[$index]['packs'] = $pp->packs;
                $array[$index]['pro'] = $pup ? true : false;
                $array[$index]['peso'] = $pp->kg ? $pp->kg : 0;
                $array[$index]['type_case'] = $pp->type_case;
                $array[$index]['original_code'] = $pp->original_code;
            }
            $array[$index]['tipo'] = $pp->producto->operacion;
            $array[$index]['id'] = $pp->id_producto;
            $array[$index]['name'] = $pp->producto->descripcion;
            $index++;
        }
        return response()->json(['data' => $array]);
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function getEditProduccion($id = null)
    {

        $product = Produccion::find($id);
        if($product!=null){
            $products_lists = [];
            foreach (ProductoTDP::where('active', 1)->get() as $p){
                $products_lists[$p->id] = $p->descripcion;
            }
            return view('produccion.abm.edit')->with('product',$product)
                ->with('products_lists', $products_lists);
        }
        else{
            return Redirect::to('produccion');
        }

    }

    public function postEditProduccion(\Illuminate\Http\Request $request, $id)
    {

        $rules = array(
            'id_producto' => 'required',
            'created_at' => 'required',
            'kg' => 'numeric',
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('produccion/'.$id.'/edit')
                ->withErrors($validator);
        } else {
            $product = Produccion::find($id);
            $producto = ProductoTDP::find($request->id_producto);
            $data = array_merge($request->except(['_token']), ['codigo' => $producto->codigo,
                'users_id' => \Auth::user()->id]);
            if($product = $product->update($data)){
                Session::flash('message', 'Producción actualizada correctamente!!');
                return Redirect::to('produccion');
            } else {
                return Redirect::back()->withErrors('Error al editar la Producción');
            }
        }
    }


    public function getAddProduccion()
    {
        $products_lists = [];
        foreach (ProductoTDP::where('active', 1)->get() as $p){
            $products_lists[$p->id] = $p->descripcion;
        }
        return view('produccion.abm.add', compact('products_lists'));
    }

    public function postAddProduccion(\Illuminate\Http\Request $request)
    {
        $rules = array(
            'id_producto' => 'required',
            'created_at' => 'required',
            'kg' => 'numeric'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator);
        } else {
            $producto = ProductoTDP::find($request->id_producto);
            $data = array_merge($request->except(['_token']), ['codigo' => $producto->codigo,
                'users_id' => \Auth::user()->id]);
            $product = Produccion::create($data);
            if($product){
                Session::flash('message', 'Producción creada correctamente!!');
                return Redirect::to('produccion');
            } else {
                return Redirect::back()->withErrors('Error al almacenar la Producción');
            }

        }
    }

    public function listProduccion(\Illuminate\Http\Request $request)
    {
        $reference = [['id' => 1, 'name'=> '1 - Fabricación Propia de Papelera'],
            ['id' => 2, 'name'=> '2 - Fabricación de Terceros de Papelera'],
            ['id' => 3, 'name'=> '3 - Reventa de Productos no Propio de Papelera'],
            ['id' => 4, 'name'=> '4 - Reventa de Productos no Propio de Plastico'],
            ['id' => 5, 'name'=> '5 - Reventa de Productos no Propio de Servilleta'],
            ['id' => 6, 'name'=> '6 - Materia Prima'],
            ['id' => 7, 'name'=> '7 - Packaging'],
            ['id' => 8, 'name'=> '8 - Insumos']];


        $products = \app\Produccion::select('produccion.*')
            ->with(['productoOld'])->orderBy('created_at','DESC');

        if($request->get('reference') && $request->reference){
            $products->join('productosTDP', 'productosTDP.codigo', '=', 'produccion.codigo')
            ->where('productosTDP.reference', 'like', $request->reference.'-%');
        }
        if($request->get('date') && $request->date){
            $products->where('produccion.created_at', '>=', $request->date.' 00:00:00');
            $products->where('produccion.created_at', '<=', $request->date.' 23:59:59');
        }
        $query2 = clone $products;
        $products_lists = [];
        $control = [];
        $iteration_product = $query2->get();
        foreach ($iteration_product as $p){
            if(!$p->productoOld){
                $pro = ProductoTDP::where('codigo', $p->codigo)->first();
                if($pro){
                    $products_lists[$pro->id] = $pro->descripcion;
                }
            } else {
                $products_lists[$p->productoOld->id] = $p->productoOld->descripcion;
            }
        }
        if($request->get('name') && $request->name){
            $pro = null;
            if($request->get('reference')){
                $pro = ProductoTDP::find($request->name);
            }
            if($pro==null || explode('-', $pro->reference)[0]==$request->reference){
                $products->join('productosTDP', 'productosTDP.codigo', '=', 'produccion.codigo')
                    ->where('productosTDP.id', $request->name);
            }
        }
        $products = $products->paginate(15);
        foreach ($products as $p){
            $control[$p->id] = ControlDeProduccion::where('fecha', $p->created_at->format('Y-m-d'))
                ->where('controlado', 1)->where('id_producto', $p->id_producto)->first();
        }
        return view('produccion.abm.list')->with('produccion', $products)
            ->with('reference', $reference)->with('request', $request->all())
            ->with('products_lists', $products_lists)->with('control', $control);

    }

    public function deleteProduccion( $id = null )
    {

        $product = Produccion::find($id);

        if($product!=null){
            if($product->delete()) {
                Session::flash('message', 'Producción eliminada correctamente!!');
                return Redirect::to('produccion');
            } else{
                return Redirect::to('produccion');
            }
        }
        else{
            return Redirect::to('produccion');
        }

    }

}
