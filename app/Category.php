<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{


    protected $table = 'categories';

	protected $fillable = array('category');
	 public $timestamps = false;

	public function products()
	{
		return $this->hasMany('app\Product','categories_id');
	}

}
