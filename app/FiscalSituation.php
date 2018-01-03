<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class FiscalSituation extends Model
{


    protected $table = 'fiscal_situation';

	protected $fillable = array('fisc_situation','tipo_iva_id');

	public $timestamps = false;

	public function company()
	{
		return $this->hasMany('app\Company', 'fiscal_situation_id');
	}
	
}