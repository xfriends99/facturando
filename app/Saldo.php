<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
        protected $table = 'saldos';


	public function medioPago()
	{
		return $this->belongsTo('app\MedioPago','medios_pagos_id');
	}

    public function user()
	{
		return $this->belongsTo('app\User');
	}

    public function getSaldo($customer_id, $date_closed = null, $id = null)
    {
        if($date_closed!=null){
            $saldos = \app\Saldo::where('customer_id','=',$customer_id)
                ->orderBy('created_at','ASC')->where('is_active','=',1)
                ->where(function($query) use($id, $date_closed){
                    $query->where('created_at','<',$date_closed);
                    $query->orWhere('saldos.id',$id);
                    $query->orWhere(function ($q) use($id, $date_closed){
                        $q->where('created_at', '=', $date_closed);
                        $q->where('saldos.id','<',$id);
                    });
                })->orderBy('id','DESC')->get();

            $invoices = \app\InvoiceHead::where(function($q){
                $q->where('status','=','A');
                $q->orWhere('cbte_tipo',99);
            })
                ->select(\DB::raw('cta_ctes.saldo,invoice_head.company_name,invoice_head.imp_total,invoice_head.imp_net, cta_ctes.id, invoice_head.nro_cbte, invoice_head.cbte_tipo, invoice_head.fecha_facturacion'))
                ->leftJoin("cta_ctes", "invoice_head_id", "=", "invoice_head.id")
                ->where('companies_id','=',$customer_id)
                ->where('fecha_facturacion','<=',$date_closed)
                ->orderBy('fecha_facturacion','ASC')
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
}