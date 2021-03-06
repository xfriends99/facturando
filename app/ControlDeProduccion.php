<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class ControlDeProduccion extends Model
{

    protected $fillable = ['type_manga', 'fecha', 'packs', 'a_stock', 'controlado',
        'id_producto', 'kg', 'mangas', 'type_case', 'original_code'];
	protected $table = 'ControlDeProduccion';

    public function producto()
    {
        return $this->belongsTo('app\ProductoTDP', 'id_producto');
    }
}
