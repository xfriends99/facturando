<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class ProductoTDP extends Model
{

        protected $fillable = ['codigo', 
            'descripcion','pesoRef','diametroRef', 'metrosRef','rollosRef', 'price', 'active',
            'operacion','peso_manga', 'diametro','cant_metros', 'cant_por_man','cant_por_pack',
            'peso_por_pack','tmpo_reb', 'emp_util_reb','tmpo_corte', 'reference', 'id_product',
            'emp_util_corte','tmpo_empq','emp_util_emp','stock_Fisico', 'stock_Pedido', 'updated'];

        protected $table = 'productosTDP';

        public $timestamps = false;

	
}