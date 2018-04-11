<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{
    protected $table = 'produccion';

    protected $fillable = ['codigo', 'created_at', 'kg', 'mangas', 'controlado', 'id_producto', 'users_id'];

public function users()
	{
		return $this->belongsTo('app\User');
	}

	public function producto()
    {
        return $this->belongsTo('app\ProductoTDP', 'id_producto');
    }
}
