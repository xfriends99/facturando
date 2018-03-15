<?php
namespace app\Services;

use app\PedidoPrestashop;
use app\ProductoTDP;
use app\Product;
use app\Pedido;
use app\Parametros;

class UpdateProductService
{

    public function updateProduct()
    {
        $productsPs = Product::select(\DB::raw('ps_product.*, ps_product_lang.name as name, ps_product_lang.link_rewrite as link_rewrite, ps_stock.physical_quantity as physical_quantity, ps_stock.usable_quantity as usable_quantity'))
            ->join('ps_product_lang', 'ps_product_lang.id_product', '=', 'ps_product.id_product')
            ->leftJoin('ps_stock', 'ps_stock.id_product', '=', 'ps_product.id_product')
            ->groupBy('ps_product.id_product')->get();

        foreach ($productsPs as $p){
            $code = explode('-', $p->link_rewrite);
            $data = ['descripcion' => $p->name,
                'codigo' => $code[0],
                'reference' => $p->reference,
                'price' => $p->price,
                'pesoRef' => $p->weight,
                'diametroRef' => $p->height,
                'metrosRef' => $p->width,
                'rollosRef' => $p->depth,
                'stock_Fisico' => $p->physical_quantity,
                'stock_Pedido' => $p->physical_quantity-$p->usable_quantity,
                'id_product' => $p->id_product,
                'active' => $p->active];
            $productTDP = ProductoTDP::where('id_product', $p->id_product)->get()->first();
            if($productTDP){
                unset($data['stock_Fisico']);
                unset($data['stock_Pedido']);
                $productTDP->update($data);
            } else {
                ProductoTDP::create($data);
            }
        }
    }

    public function updateStock()
    {
        $param = Parametros::find(1);
        $pedidos = Pedido::select('ps_orders.*')->with(['lineas']);
        if($param->id_pedido!=null){
            $pedidos->where('id_order', '>=', $param->id_pedido);
        }
        $new_pedido = $param->id_pedido!=null ? $param->id_pedido: 1;
        $pedidos_prestashop = [];
        foreach(PedidoPrestashop::where('id_pedido', '>=', $new_pedido)->get() as $p){
            $pedidos_prestashop[$p->id_pedido] = $p->nivel_stock;
        }
        $valid = true;

        foreach ($pedidos->get() as $p){
            if(!isset($pedidos_prestashop[$p->id_order])){
                PedidoPrestashop::create(['id_pedido' => $p->id_order, 'nivel_stock' => 0]);
                $pedidos_prestashop[$p->id_order] = 0;
            }
            $nivel_stock = $pedidos_prestashop[$p->id_order];
            $new_stock = $nivel_stock;
            foreach ($p->lineas as $l){
                $product = ProductoTDP::where('id_product', $l->product_id)->first();
                if($nivel_stock==0 && ($p->current_state==3 || $p->current_state==7 || $p->current_state==8 || $p->current_state==9 || $p->current_state==12 || $p->current_state==13)){
                    $new_stock = 1;
                    $product->stock_Pedido += $l->product_quantity;
                } else if($nivel_stock==1 && $p->current_state==5){
                    $new_stock = 2;
                    $product->stock_Pedido = $product->stock_Pedido - $l->product_quantity;
                    $product->stock_Fisico = $product->stock_Fisico - $l->product_quantity;
                } else if(($nivel_stock==1 || $nivel_stock==2) && $p->current_state==6){
                    $new_stock = 3;
                    $product->stock_Fisico = $product->stock_Fisico + $l->product_quantity;
                }
                $product->save();
            }
            if($new_stock!=$nivel_stock){
                PedidoPrestashop::where('id_pedido', $p->id_order)->update([ 'nivel_stock' => $new_stock]);
            }
            if($valid && ($p->current_state==6 || $p->current_state==5)){
                $param->id_pedido = $p->id_order;
                $param->save();
            } else {
                $valid = false;
            }
        }

        return  $param->id_pedido;
    }
}