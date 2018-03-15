<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Parametros extends Model
{
    protected $table = 'parametros';

	protected $fillable = ['nro_remito_inicial', 'id_pedido'];
	public $timestamps = false;

}
