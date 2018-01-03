<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class InvoiceHead extends Model
{
    protected $table = 'invoice_head';

	protected $fillable = array('concepto','cae','fecha_vto_cae','nro_cbte','pto_vta','fecha_facturacion','fecha_vto_factura','company_name','tax_id','fisc_situation','tax_id_type','address','cbte_tipo','cbte_desde','cbte_hasta','imp_total','imp_total_conc','imp_net','imp_op_ex','imp_trib','imp_iva','mon_id','mon_cotiz','fecha_serv_desde','fecha_serv_hasta','iva_id','fecha_vto_pago','iva_base_imp','iva_importe','status','companies_id','users_id');

	 public $timestamps = false;
	
	public function campanies()
	{
		return $this->belongsTo('app\Company');
	}

	public function fiscal_situation()
	{
		return $this->belongsTo('app\FiscalSituation','fisc_situation');
	}

	public function tax_type()
	{
		return $this->belongsTo('app\TaxType','tax_id_type');
	}

	public function users()
	{
		return $this->belongsTo('app\User');
	}

        public function corredor()
	{
		return $this->belongsTo('app\Cliente','companies_id','id_customer');
	}

	public function invoice_lines()
	{
		return $this->hasMany('app\InvoiceLine','invoice_head_id');
	}

	public function cta_cte()
	{
		return $this->hasMany('app\CtaCte','invoice_head_id');
	}
}