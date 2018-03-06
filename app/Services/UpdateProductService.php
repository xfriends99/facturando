<?php
namespace app\Services;

use app\ProductoTDP;
use app\Product;

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
                'stock_Pedido' => $p->usable_quantity,
                'id_product' => $p->id_product];
            $productTDP = ProductoTDP::where('id_product', $p->id_product)->get()->first();
            if($productTDP){
                $productTDP->update($data);
            } else {
                ProductoTDP::create($data);
            }
        }
    }
}