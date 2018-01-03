<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class RelFSCbtes extends Model
{
    protected $table = 'rel_fiscal_situation_tipo_cbtes';
	protected $fillable = array('fiscal_situation_id','tipo_cbtes_id');
	 public $timestamps = false;
	
	public function fs()
	{
		return $this->belongsTo('app\FiscalSituation','fiscal_situation_id');
	}

	public function cbte()
	{
		return $this->belongsTo('app\TipoCbte','tipo_cbtes_id');
	}
	
}