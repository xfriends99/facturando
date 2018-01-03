<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class TipoCbte extends Model
{

    protected $table = 'tipo_cbtes';

	protected $fillable = array('code','tipo_cbte');

	public $timestamps = false;

	
}
