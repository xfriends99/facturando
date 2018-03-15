<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class PedidoPrestashop extends Model
{

    protected $table = 'pedidos_prestashop';

    protected $fillable = ['id_pedido', 'nivel_stock'];
    public $timestamps = false;

}
