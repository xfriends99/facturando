<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{


    protected $table = 'tax_type';

	protected $fillable = array('code','type');

	public $timestamps = false;

	public function company()
	{
		return $this->hasMany('app\Company', 'tax_type_id');
	}


}
