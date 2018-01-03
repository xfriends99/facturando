<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{


    protected $table = 'roles';

	protected $fillable = array('rol');
	 public $timestamps = false;

	public function products()
	{
		return $this->hasMany('app\Users','roles_id');
	}

}