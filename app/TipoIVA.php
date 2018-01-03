<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class TipoIVA extends Model
{


    protected $table = 'tipo_iva';

	protected $fillable = array('code','tipo_iva');

	public $timestamps = false;

	public function fiscal_situation()
	{
		return $this->hasMany('app\FiscalSituation','tipo_iva_id');
	}

}
