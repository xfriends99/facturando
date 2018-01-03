<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{


    protected $table = 'companies_type';

	protected $fillable = array('type');

	public $timestamps = false;

	public function company()
	{
		return $this->hasMany('app\Company', 'companies_type_id');
	}


}