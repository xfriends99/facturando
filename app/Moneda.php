<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{


    protected $table = 'monedas';

	protected $fillable = array('code','moneda');

	public $timestamps = false;


}
