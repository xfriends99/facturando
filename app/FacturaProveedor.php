<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class FacturaProveedor extends Model
{
    protected $table = 'facturas_proveedores';
	protected $fillable = array('nro','importe_total','importe_neto','importe_iva','fecha_factura','companies_id','nombre_proveedor','cuit');
	 public $timestamps = false;
	
	public function companies()
	{
		return $this->belongsTo('app\Company','companies_id');
	}

	public function tipo_cbte()
	{
		return $this->belongsTo('app\TipoCbteProv','tipo_cbte_prov_id');
	}

}
