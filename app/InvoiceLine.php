<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
        
           protected $table = 'invoice_lines';
	
        protected $fillable = array('subtotal','quantity','invoice_head_id','code','name','description','price','categories_id');
	
        public $timestamps = false;
	
	public function invoice_head()
	{
		return $this->belongsTo('app\InvoiceHead','invoice_head_id');
	}

	public function tipo_iva_id()
	{
		return $this->belongsTo('app\TipoIVA','tipo_iva','code');
	}

       
}