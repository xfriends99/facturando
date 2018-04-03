<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class ControlDeProduccion extends Model
{

    protected $fillable = ['fecha', 'packs', 'a_stock', 'controlado', 'id_producto'];
	protected $table = 'ControlDeProduccion';
}
