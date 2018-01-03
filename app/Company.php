<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
	protected $fillable = array('cbte_letra_id','website','addresses_id','company_name','tax_id','tel','fax','logo','is_active','fiscal_situation_id','tax_type_id','companies_type_id');
	public $timestamps = false;
	 
	public function addresses()
	{
		return $this->belongsTo('app\Address');
	}

	public function fiscal_situation()
	{
		return $this->belongsTo('app\FiscalSituation','fiscal_situation_id');
	}

	public function cbte_letra()
	{
		return $this->belongsTo('app\CbteLetra','cbte_letra_id');
	}

	public function tax_type()
	{
		return $this->belongsTo('app\TaxType','tax_type_id');
	}

	public function company_type()
	{
		return $this->belongsTo('app\CompanyType','companies_type_id');
	}

	public function user()
	{
		return $this->hasMany('app\User', 'companies_id');
	}

	public function factura_proveedor()
	{
		return $this->hasMany('app\FacturaProveedor', 'companies_id');
	}

	public function factura()
	{
		return $this->hasMany('app\InvoiceHead', 'companies_id');
	}


}