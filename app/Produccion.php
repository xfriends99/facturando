<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{
    protected $table = 'produccion';

    protected $fillable = ['control_id', 'created_at', 'kg', 'mangas', 'controlado', 'id_producto', 'users_id'];

public function users()
	{
		return $this->belongsTo('app\User');
	}

	public function producto()
    {
        return $this->belongsTo('app\ProductoTDP', 'id_producto');
    }

    public function control()
    {
        return$this->hasOne(ControlDeProduccion::class, 'id', 'control_id');
    }

}
