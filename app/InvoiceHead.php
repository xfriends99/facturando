<?php namespace app;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InvoiceHead extends Model
{
    protected $table = 'invoice_head';

	protected $fillable = array('concepto','cae','fecha_vto_cae','nro_cbte','pto_vta','fecha_facturacion','fecha_vto_factura','company_name','tax_id','fisc_situation','tax_id_type','address','cbte_tipo','cbte_desde','cbte_hasta','imp_total','imp_total_conc','imp_net','imp_op_ex','imp_trib','imp_iva','mon_id','mon_cotiz','fecha_serv_desde','fecha_serv_hasta','iva_id','fecha_vto_pago','iva_base_imp','iva_importe','status','companies_id','users_id');

	public $timestamps = false;

	public function getSaldo($customer_id, $date_closed = null, $id = null)
    {
        if($date_closed!=null){
            $saldos = \app\Saldo::where('customer_id','=',$customer_id)
                ->orderBy('created_at','ASC')->where('is_active','=',1)
                ->where('created_at','<',$date_closed)
                ->orderBy('id','DESC')->get();

            $invoices = \app\InvoiceHead::where(function($q){
                $q->where('status','=','A');
                $q->orWhere('cbte_tipo',99);
            })
                ->select(\DB::raw('cta_ctes.saldo,invoice_head.company_name,invoice_head.imp_total,invoice_head.imp_net, cta_ctes.id, invoice_head.nro_cbte, invoice_head.cbte_tipo, invoice_head.fecha_facturacion'))
                ->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
                ->where('companies_id','=',$customer_id)
                ->where(function($query) use($id, $date_closed){
                    $query->where('fecha_facturacion','<',$date_closed);
                    $query->orWhere('invoice_head.id',$id);
                    $query->orWhere(function ($q) use($id, $date_closed){
                       $q->where('fecha_facturacion', '=', $date_closed);
                       $q->where('invoice_head.id','<',$id);
                    });
                })->orderBy('fecha_facturacion','ASC')
                ->orderBy('invoice_head.id','desc')->get();
        } else {
            $saldos = \app\Saldo::where('customer_id','=',$customer_id)
                ->orderBy('created_at','ASC')->where('is_active','=',1)->get();

            $invoices = \app\InvoiceHead::where(function($q){
                $q->where('status','=','A');
                $q->orWhere('cbte_tipo',99);
            })
                ->select(\DB::raw('cta_ctes.saldo,invoice_head.company_name,invoice_head.imp_total,invoice_head.imp_net, cta_ctes.id, invoice_head.nro_cbte, invoice_head.cbte_tipo, invoice_head.fecha_facturacion'))
                ->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
                ->where('companies_id','=',$customer_id)->orderBy('fecha_facturacion','ASC')->get();
        }

        $row = collect();

        foreach ($invoices as $inv){
            $row->push(['type'=>'invoice', 'date' => $inv->fecha_facturacion,
                'cbte_tipo' => $inv->cbte_tipo, 'nro_cbte'=> $inv->nro_cbte,
                'imp_net' => $inv->imp_net, 'imp_total'=> $inv->imp_total,
                'saldo' => Pago::where('cta_ctes_id', $inv->id)
                    ->where('is_active',1)->sum('pago'),
                'id' => $inv->id]);
        }

        foreach ($saldos as $inv){
            $row->push(['type'=>'saldo', 'date' => $inv->created_at,
                'medios_pagos_id' => $inv->medios_pagos_id, 'importe'=> $inv->importe,
                'otro' => $inv->otro, 'id' => $inv->id,
                'medioPago_tipo' => ($inv->medios_pagos_id!=0) ? $inv->medioPago->tipo : '']);
        }
        $imp = 0;
        $sald = 0;
        foreach ($row->sortBy('date')->toArray() as $d) {
            if ($d['type'] == "saldo") {
                $imp += $d['importe'];
            } else {
                if ($d['cbte_tipo'] != 3) {
                    if ($d['cbte_tipo'] == 99) {
                        $imp += $d['imp_net'];
                    } else {
                        $imp += $d['imp_total'];
                    }
                } else {
                    $sald += $d['imp_total'];
                }
                $sald += $d['saldo'];
            }
        }
        return $imp - $sald;
    }

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