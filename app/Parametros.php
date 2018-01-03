<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Parametros extends Model
{


    protected $table = 'parametros';

	protected $fillable = array('nro_remito_inicial');
	public $timestamps = false;


}
