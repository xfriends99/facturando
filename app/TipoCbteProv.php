<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class TipoCbteProv extends Model
{

    protected $table = 'tipo_cbte_prov';

	protected $fillable = array('tipo');

	public $timestamps = false;

	
}